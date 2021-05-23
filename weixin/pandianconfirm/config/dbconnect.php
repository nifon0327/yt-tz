<?php
/**
 * Created by PhpStorm.
 * User: IceFire
 * Date: 2018/11/24
 * Time: 22:17
 */

class DbConnect
{
    public $conn;

    function __construct()
    {
        $config = parse_ini_file("config.ini");
        $this->conn = new mysqli(
            $config["resources.database.dev.hostname"],
            $config["resources.database.dev.username"],
            $config["resources.database.dev.password"],
            $config["resources.database.dev.database"],
            $config["resources.database.dev.port"]);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

#region 权限校验
    public function apiAuth($openId,$tag){
        $sql = "SELECT
                A.uName,
                A.Number,
                A.openid,
                B.NAME,
                D.NAME AS Branch,
                E.NAME AS RoleName
            FROM
                UserTable A
                LEFT JOIN staffmain B ON B.Number = A.Number
                LEFT JOIN companys_group C ON C.cSign = B.cSign
                LEFT JOIN branchdata D ON D.Id = B.BranchId
                LEFT JOIN ac_roles E ON E.id = A.roleId 
            WHERE
                1 
                AND A.uType = '1'
            AND A.openid='$openId' LIMIT 1";

        $result = $this->format($sql);

        switch ($tag) {
            //盘点
            case 1:return is_null($result)?false:true;
            break;
            //出货
            case 2:

                if(!is_null($result)&& ($result[0]["Branch"]=="IT中心" || $result[0]["Branch"]=="资材部")){
                    return true;
                }else{
                    return false;
                }
        }
    }

#endregion

//region 盘点功能

    /**
     * 获取扫码用户昵称
     */
    public function get_operator_name($openId)
    {
        $sql = "SELECT SM.name uName FROM usertable U INNER JOIN staffmain SM ON SM.Number = U.Number WHERE U.openid='$openId';";
        return $this->format($sql);
    }

    //根据库位号查询垛号
    public function get_stack_by_seat($seatId){
        $sql = "SELECT ID, StackNo FROM inventory_stackinfo WHERE SeatId='$seatId'";
        return $this->format($sql);
    }

    //移垛
    public function move_stack($originStackId,$stackId,$productIds)
    {
        $sql = "UPDATE inventory_data SET StackID=$stackId WHERE StackID=$originStackId AND FIND_IN_SET(ProductID,'";
        foreach ($productIds as $item)
        {
            $pId = $item["productId"];
            $sql .=",$pId";
        }
        $sql .="')";
        return $this->conn->query($sql);
    }

    //获取上一次数据
    public function get_last_search($stackId, $openid="op_TywzYDwG4walmycIBLQWKdEn8")
    {
        $lastSql = "SELECT TradeId,BuildingNo,FloorNo,CmptTypeId,ProductName FROM stack_search_params where StackId =$stackId and CreateBy='$openid' LIMIT 1;";
        $lastSearch = $this->format($lastSql);
        if (is_null($lastSearch)) {
            return "";
        } else {
            return array(
                'lastSearch' => $lastSearch[0],
                'trade' => $this->get_company_forshort(),
                'building' => $this->get_company_building($lastSearch[0]["TradeId"]),
                'floor' => $this->get_building_floor($lastSearch[0]["TradeId"], $lastSearch[0]["BuildingNo"]),
                'type' => $this->get_cmpttype($lastSearch[0]["TradeId"], $lastSearch[0]["BuildingNo"], $lastSearch[0]["FloorNo"])
            );
        }

    }

    /**
     * 查询扫描垛号是否存在
     * @param $stackNo      垛号
     * @return mixed|null
     */
    public function search_stack($stackNo)
    {
        $sql = "SELECT * FROM inventory_stackinfo WHERE StackNo = '$stackNo'";
        $result = $this->conn->query($sql) or die($this->conn->error());
        if ($result->num_rows > 0) {
            return $result->fetch_array();
        } else {
            return null;
        }
    }

    /**
     * 创建垛号
     * @param $stackNo          垛号
     * @param $creator          盘点人OpenID
     */
    public function create_stack($stackNo, $creator)
    {
        $sql = "INSERT INTO inventory_stackinfo(StackNo,Creator,CreateDT) 
              VALUES('$stackNo','$creator',CURRENT_TIMESTAMP)";
        if ($this->conn->query($sql)) {
            return $this->conn->insert_id;
        } else {
            return 0;
        }
    }

    /**
     * 查询当前垛号下构件的状态
     * @param $stackID
     * @return mixed|null
     */
    public function get_stack_product($stackID)
    {
        $sql = "SELECT ID.ProductID ProductId, TOB.Forshort,PD.cName,Status,Result,ID.Creator Creator,DoubleCheckUser,PS.tstockqty,TEMP.inventoryNum
                FROM inventory_data ID
                JOIN inventory_stackinfo IST ON IST.ID=ID.StackID
                JOIN productdata PD ON ID.ProductID = PD.ProductId
                JOIN trade_object TOB ON TOB.CompanyId = PD.CompanyId
                LEFT JOIN productstock PS ON PD.ProductId =PS.ProductId
                LEFT JOIN (SELECT ProductID,COUNT(ProductID) inventoryNum FROM inventory_data GROUP BY ProductId) TEMP ON PD.ProductId=TEMP.ProductId
                WHERE ID.StackID=$stackID";
        return $this->format($sql);
    }
    /**
     * 获取盘点人和复盘人
     */
    public function get_user_name($stackId,$openId)
    {
        $creatorSql = "SELECT creator FROM inventory_stackinfo WHERE ID=$stackId";
        $creator = $this->format($creatorSql);
        $sql = "SELECT SM.name uName FROM usertable U INNER JOIN staffmain SM ON SM.Number = U.Number WHERE U.openid='$openId';";
        $creatorOpenId = $creator[0]["creator"];
        if($openId == $creatorOpenId)
        {
            return array(
                "creator"=>$this->format($sql)[0]["uName"],
                "doubleCheckUser"=>""
            );
        }else{
            $creatorNameSql = "SELECT SM.name uName FROM usertable U INNER JOIN staffmain SM ON SM.Number = U.Number WHERE U.openid='$creatorOpenId';";
            return array(
                "creator"=>$this->format($creatorNameSql)[0]["uName"],
                "doubleCheckUser"=>$this->format($sql)[0]["uName"]
            );
        }

    }

    /**
     * 获取所有构件的项目信息
     */
    public function get_company_forshort()
    {
        $sql = "SELECT I.TradeId TradeId, I.TradeNo TradeNo,O.Forshort Forshort FROM trade_info I INNER JOIN trade_object O ON O.Id = I.TradeId";
        return $this->format($sql);
    }

    /**
     * 获取项目楼栋数
     * @param $tradeId      公司ID
     * @return mixed|null   楼栋号
     */
    public function get_company_building($tradeId)
    {
        $sql = "SELECT BuildingNo FROM trade_drawing WHERE TradeId = $tradeId  GROUP BY BuildingNo";
        return $this->format($sql);
    }

    /**
     * 获取楼栋层数
     * @param $tradeId      项目ID
     * @param $buildingNo   楼栋ID
     * @return mixed|null   层数信息
     */
    public function get_building_floor($tradeId, $buildingNo)
    {
        $sql = "SELECT FloorNo FROM trade_drawing WHERE TradeId = $tradeId AND buildingNo = $buildingNo GROUP BY FloorNo";
        return $this->format($sql);
    }

    /**
     * 获取构件类型
     * @param $tradeId
     * @param $buildingNo
     * @param $floorNo
     * @return array|null
     */
    public function get_cmpttype($tradeId, $buildingNo, $floorNo)
    {
        $sql = "SELECT
                    P.TypeId,
                    D.CmptType 
                FROM
                    trade_drawing D
                    INNER JOIN producttype P ON P.TypeName = D.CmptType 
                WHERE
                    D.TradeId = $tradeId 
                    AND D.buildingNo = $buildingNo 
                    AND D.FloorNo = $floorNo 
                GROUP BY
                    D.CmptType";
        return $this->format($sql);
    }

    /**
     * 根据入参查询构件信息
     * @param $tradeId          公司ID
     * @param $buildingNo         楼栋编号
     * @param $floorNo            层数
     * @param $type
     * @param $productCode      构件编号
     * @param $stackId
     * @param $openId
     * @return array|null
     */
    public function search_product($tradeId, $buildingNo, $floorNo, $type, $productCode, $stackId, $openId)
    {
        $sql = "SELECT PD.ProductId, TOB.Forshort, PD.cName FROM productdata PD LEFT JOIN trade_object TOB ON PD.CompanyId = TOB.CompanyId LEFT JOIN producttype PT ON PT.TypeId = PD.TypeId WHERE 1=1";

        if ($tradeId != 0) {
            $sql .= " AND TOB.Id = $tradeId";
        } else {
            $tradeId = '';
        }
        $cname = "";
        if ($buildingNo != 0) {
            $cname .= "$buildingNo";
            if ($floorNo != 0) {
                $cname .= "-$floorNo";
            } else {
                $floorNo = "";
            }
            $cname .= "-%";
            $sql .= " AND PD.cName LIKE '$cname'";
        } else {
            $buildingNo = "";
            $floorNo = "";
        }
        if ($type != "") {
            $sql .= " AND PT.TypeId = $type";
//            $typeIds = $this->format("SELECT TypeId from producttype WHERE TypeName='PCB'");
//            $type = $typeIds[0]["TypeId"];
        } else {
            $type = "";
        }
        if ($productCode != "") {
            $sql .= " AND PD.cName like '%$productCode%'";
        }
        $sql .= " LIMIT 1000";
        $lastSql = "select TradeId,BuildingNo,FloorNo,CmptTypeId,ProductName FROM stack_search_params where StackId =$stackId and CreateBy='$openId' LIMIT 1;";
        $lastSearch = $this->format($lastSql);
        if (is_null($lastSearch)) {
            $insertSql = "INSERT INTO stack_search_params (StackId,TradeId,BuildingNo,FloorNo,CmptTypeId,ProductName,CreateBy) VALUES($stackId,'$tradeId','$buildingNo','$floorNo','$type','$productCode','$openId');";
            $this->conn->query($insertSql);
        } else {
            $updateSql = "UPDATE stack_search_params SET TradeId = '$tradeId',BuildingNo='$buildingNo',FloorNo='$floorNo',CmptTypeId='$type',ProductName='$productCode' WHERE StackID =$stackId AND CreateBy='$openId';";
            $this->conn->query($updateSql);
        }
        return $this->format($sql);
    }
    /**
     * 将构件存入指定垛内并进行盘点
     * @param $stackId          垛ID
     * @param $productId        构件ID
     * @param $status           盘点状态（0：未盘；1：已盘；2：复盘）
     * @param $result           盘点结果（0：未知；1：正常；2：异常）
     * @param $creator          盘点人OpenID
     * @param $doubleCheckUser  复盘人OpenID
     */
    //$stackId, $productId, $status, $result, $creator, $doubleCheckUser = null
    public function add_product_to_stack($stackId, $array, $creator = "")
    {
        $sql = "";
        $updateSeatSql = "";
        //获取seatId
        $getSeatSql = "SELECT SeatId FROM inventory_stackinfo WHERE ID = $stackId";
        $seatArray = $this->format($getSeatSql);
        $seatId = $seatArray[0]["SeatId"];
        foreach ($array as $item) {
            $productId = $item['productId'];
            $sql .= "INSERT INTO inventory_data(StackID,ProductID,`Status`,Result,Creator,CreateDT)
                VALUES($stackId,$productId,0,0,'$creator',CURRENT_TIMESTAMP);";
            $updateSeatSql .= "UPDATE yw1_ordersheet SET SeatId = '$seatId' WHERE ProductId = $productId ;";
        }

        if ($this->conn->multi_query($sql) === TRUE && $this->conn->multi_query($updateSeatSql) === TRUE) {
            return 1;
        } else {
            return 0;
        }
    }

    //扫码添加
    public function scan_product_stack($stackId,$pName,$creator='')
    {
        $getProductIdSql = "SELECT ProductId from productdata where cName='$pName' limit 1";
        $pResult = $this->format($getProductIdSql);
        if(is_null($pResult)) return 0;
        $productId = $pResult[0]["ProductId"];
        //获取seatId
        $getSeatSql = "SELECT SeatId FROM inventory_stackinfo WHERE ID = $stackId";
        $seatArray = $this->format($getSeatSql);
        $seatId = $seatArray[0]["SeatId"];
        $sql = "INSERT INTO inventory_data(StackID,ProductID,`Status`,Result,Creator,CreateDT)
                VALUES($stackId,$productId,0,0,'$creator',CURRENT_TIMESTAMP);";
        $updateSeatSql = "UPDATE yw1_ordersheet SET SeatId = '$seatId' WHERE ProductId = $productId ;";
        $this->format($sql);
        $this->format($updateSeatSql);
        return 1;

    }

    /**
     * 获取所有垛号
     * @return array|null
     */
    public function get_stackno()
    {
        $sql = "SELECT DISTINCT StackNo FROM inventory_stackinfo IST
                JOIN inventory_data ID ON IST.ID=ID.StackID";
        return $this->format($sql);
    }

    /**
     * 获取异常构件
     * @param $status
     * @param $stackNo
     * @return array|null
     */
    public function get_error_product($status, $stackNo)
    {
//        $sql = "SELECT IST.StackNo StackNo,TOB.Forshort Forshort, PD.cName CName,ID.Status Status  FROM inventory_stackinfo IST JOIN inventory_data ID ON IST.ID=ID.StackID JOIN productdata PD ON ID.ProductID =PD.ProductId LEFT JOIN trade_object TOB ON PD.CompanyId = TOB.CompanyId WHERE Result=2";
        $sql = "SELECT
                    IST.StackNo StackNo,
                    TOB.Forshort Forshort,
                    PD.cName CName,
                    ID. STATUS STATUS,
                    COUNT(ID.ProductID) inventory_num,
                    tstockqty
                FROM
                    inventory_stackinfo IST
                JOIN inventory_data ID ON IST.ID = ID.StackID
                JOIN productdata PD ON ID.ProductID = PD.ProductId
                LEFT JOIN trade_object TOB ON PD.CompanyId = TOB.CompanyId
                JOIN productstock PS ON ID.ProductID = PS.ProductId
                WHERE
                    Result = 2";
        if (!($status < 0)) {
            $sql .= " AND ID.Status= $status";
        }
        if (!($stackNo < 0)) {
            $sql .= " AND IST.StackNo='$stackNo'";
        }
        $sql .= " GROUP BY ID.ProductID;";
        return $this->format($sql);
    }

    /**
     * 更新构件状态
     * @param $isRepeat 判断是否复盘
     */
    public function update_result($stackId, $productId, $status, $currentStatus, $isRepeat, $openId="op_TywzYDwG4walmycIBLQWKdEn8")
    {
        $inventoryNum = 0;
        $tstockqty = 0;
        // $sql = "SELECT COUNT(*) inventory_num FROM inventory_data WHERE StackID=$stackId AND ProductID =$productId;";
        $sql = "SELECT COUNT(*) inventory_num FROM inventory_data WHERE ProductID =$productId;";
        $result = $this->conn->query($sql);
        // $stkSql = "SELECT Count(*) productstock_num FROM productstock WHERE ProductId = $productId";
        $stkSql = "SELECT tstockqty FROM productstock WHERE ProductId = $productId";
        $resultStk = $this->conn->query($stkSql);
        if ($result->num_rows > 0 && $resultStk->num_rows > 0) {
            $inventory_num = 0;
            while ($row = $result->fetch_assoc()) {
                $inventory_num = $row["inventory_num"];
            }
            $productStock_num = 0;
            while ($row = $resultStk->fetch_assoc()) {
                $productStock_num = $row["tstockqty"];
            }
            if ($inventory_num == $productStock_num && $inventory_num > 0 && $productStock_num > 0) {
                $updateSql = "UPDATE inventory_data Set Status =$status,Result=1";
                if (!$isRepeat) {
                    $updateSql .= " ,Creator = '$openId'";
                } else {
                    $updateSql .= " ,DoubleCheckUser = '$openId'";
                }
//                $updateSql .= " WHERE ProductID = $productId AND Status =$currentStatus";
                $updateSql .= " WHERE ProductID = $productId ";

                $this->conn->query($updateSql);
            } else {
                $updateSql = "UPDATE inventory_data Set Status =$status,Result=2";
                if (!$isRepeat) {
                    $updateSql .= " ,Creator = '$openId'";
                } else {
                    $updateSql .= " ,DoubleCheckUser = '$openId'";
                }
//                $updateSql .= " WHERE ProductID = $productId AND Status =$currentStatus";
                $updateSql .= " WHERE ProductID = $productId";
                $this->conn->query($updateSql);
            }
        } else {
            $updateSql = "UPDATE inventory_data Set Status =$status,Result=2";
            if (!$isRepeat) {
                $updateSql .= " ,Creator = '$openId'";
            } else {
                $updateSql .= " ,DoubleCheckUser = '$openId'";
            }
//            $updateSql .= " WHERE ProductID = $productId AND Status =$currentStatus";
            $updateSql .= " WHERE ProductID = $productId ";
            $this->conn->query($updateSql);
        }
        return array(
            "productId"=>$productId,
            "inventoryNum"=>is_null($this->format($sql))?0:$this->format($sql)[0]["inventory_num"],
            "tstockqty"=>is_null($this->format($stkSql))?0:$this->format($sql)[0]["tstockqty"]
        );


    }

    public function remove_product($stackId, $productId)
    {
        $sql = "DELETE FROM inventory_data WHERE StackID =$stackId AND ProductId=$productId AND Status=0";
        return $this->conn->query($sql);
    }

    public function get_seat()
    {
        $sql = "SELECT SeatId seatId FROM wms_seat WHERE WareHouse='成品仓库' order by SeatId";
        return $this->format($sql);
    }

    public function get_stack_seat($stackId)
    {
        $sql = "SELECT SeatId FROM inventory_stackinfo WHERE Id=$stackId ";
        $result = $this->format($sql);
        if (is_null($result)) {
            return "";
        } else {
            return $result[0]["SeatId"];
        }
    }

    public function update_stack_seatid($stackId, $seatId)
    {
        $sql = "UPDATE inventory_stackinfo SET SeatId='$seatId' WHERE id =$stackId";
        return $this->conn->query($sql);
    }

    //更新垛备注
    public function update_stack_desc($stackId,$desc){
        $sql = "UPDATE inventory_stackinfo SET Desc='$desc' WHERE id=$stackId";
        return $this->conn->query($sql);
    }
    //获取垛备注
    public function get_stack_desc($stackId)
    {
        $sql = "SELECT Desc FROM inventory_stackinfo WHERE Id=$stackId ";
        $result = $this->format($sql);
        if (is_null($result)) {
            return "";
        } else {
            return $result[0]["Desc"];
        }
    }
//endregion

//region 通用方法
    public function format($sql)
    {
        $result_query = $this->conn->query($sql);
        $result = array();
        if ($result_query->num_rows > 0) {
            while ($row = $result_query->fetch_assoc()) {
                $result[] = $row;
            }
            return $result;
        } else {
            return null;
        }

    }
//endregion
}

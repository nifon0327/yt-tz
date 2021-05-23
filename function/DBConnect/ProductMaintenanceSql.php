<?php
/**
 * Created by PhpStorm.
 * User: IceFire
 * Date: 2018/11/24
 * Time: 22:17
 */
include "./Config/DbConnect.php";

class ProductMaintenanceSql
{
    public $db;
    //estate 0 养护阶段构件 1 养护完成
    public $productsSql = "SELECT
            DISTINCT P.ProductId,date_format(SC.scdate, '%Y-%c-%d' ) as scdate, P.TypeId ,P.FloorNo,P.cName,SC.WorkShopId,
            SC.POrderId,S.RealLining as liningNo,SC.Id scsheetId,O.Forshort,S.OrderPO,D.StuffCname,1 Qty
        FROM
            yw1_scsheet SC
            INNER JOIN yw1_ordersheet S ON S.POrderId = SC.POrderId
            INNER JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
            LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
            INNER JOIN productdata P ON P.ProductId = S.ProductId
            INNER JOIN productunit U ON U.Id = P.Unit
            INNER JOIN cg1_stocksheet G ON G.POrderId = SC.POrderID
            INNER JOIN stuffdata D ON D.StuffId = G.StuffId
        WHERE
            1 
            AND SC.ActionId = '110' 
            and SC.WorkShopId in(101,104)
            AND D.TypeId = 9017
            AND SC.Estate = 1
            ";

    function __construct()
    {
        $this->db = new DbConnect();
    }


    /**
     * 获取所有构件的项目信息
     */
    public function get_company_forshort()
    {
//        $sql = "SELECT I.TradeId TradeId,O.CompanyId, I.TradeNo TradeNo,O.Forshort Forshort FROM trade_info I INNER JOIN trade_object O ON O.Id = I.TradeId";

        $sql = "SELECT DISTINCT O.Forshort,O.Id as TradeId
        FROM
            yw1_scsheet SC
            INNER JOIN yw1_ordersheet S ON S.POrderId = SC.POrderId
            INNER JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
            LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
            INNER JOIN productdata P ON P.ProductId = S.ProductId
            INNER JOIN productunit U ON U.Id = P.Unit
            INNER JOIN cg1_stocksheet G ON G.StockId = SC.StockId
            INNER JOIN stuffdata D ON D.StuffId = G.StuffId
        WHERE
            1  
            AND SC.ActionId = '110' 
            and SC.WorkShopId in(101,104)
						GROUP BY O.Id";
        return $this->db->format($sql);
    }

    /**
     * @return array
     *  0待养护 1 养护待确认  2养护中  3 出窑待确认 4 已出窑
     */
    public function getMaintenanceStatus()
    {
        $statusArr = array(
            array(
                "key" => 0,
                "value" => "待养护",
            ),
            array(
                "key" => 1,
                "value" => "养护待确认",
            ),
            array(
                "key" => 2,
                "value" => "养护中",
            ),
            array(
                "key" => 3,
                "value" => "出窑待确认",
            ),
            array(
                "key" => 4,
                "value" => "已出窑",
            ),
        );
        return $statusArr;
    }

    /**
     * 获取位号  列
     * @param $workshopId
     * @param $type
     * @return array|null
     */
    public function getLineNo($workshopId, $type)
    {
        $sql = "SELECT DISTINCT LineNo from maintan_kiln_bits WHERE Ktype = $type AND WorkshopdataId = $workshopId 
              AND MaintanOrderId = 0 AND `Status` = 1;";
        return $this->db->format($sql);
    }

    /**
     * 获取位号  row
     * @param $workshopId
     * @param $type
     * @return array|null
     */
    public function getRowNo($workshopId, $type, $lineNo)
    {
        $sql = "SELECT DISTINCT Id kilnId,KRowNo from maintan_kiln_bits 
                WHERE Ktype = $type AND WorkshopdataId = $workshopId AND LineNo = '$lineNo' AND MaintanOrderId = 0 AND `Status` = 1;";
        return $this->db->format($sql);
    }


    /**
     * 窑位选择
     * @param $kilnId
     * @param $trolleyNo
     * @param $openId
     * @return bool
     * @throws Exception
     */
    public function selectKilnBit($kilnId, $trolleyNo, $openId)
    {
        //判断该窑位是否已占用
        $querySql = "SELECT * from maintan_kiln_bits WHERE ID= $kilnId AND MaintanOrderId = 0";
        $querySqlRes = $this->db->format($querySql);
        if (count($querySqlRes) == 0) {
            throw new Exception("该窑位已占用，请选择其他窑位");
        }

        //判断该台车是否已入窑
        $querySql = "SELECT id,KilnId,TrolleyNo,`Status` from maintan_order WHERE TrolleyNo ='$trolleyNo'  AND `Status` <> 4";
        $querySqlRes = $this->db->format($querySql);
        if (count($querySqlRes) != 0) {
            $orderDetail = $querySqlRes[0];
            $tmpStatus = $orderDetail['Status'];
            $tmpOrderId = $orderDetail['id'];
            $tmpTrolleyNo = $orderDetail['TrolleyNo'];
            $tmpKilnId = $orderDetail['KilnId'];
            if($tmpStatus!=0){
                throw new Exception("该台车已入窑确认，请勿重复选择");
            }
            //更新窑位绑定订单信息
            $updateSql = "UPDATE maintan_kiln_bits set MaintanOrderId = 0 WHERE ID = $tmpKilnId";
            $updateSqlRes = $this->db->conn->query($updateSql);
            if (!$updateSqlRes && mysqli_affected_rows($this->db->conn) == 0) {
                throw new Exception("更新maintan_kiln_bits数据失败");
            }
            //删除订单
            $deleteSql = "DELETE from maintan_order WHERE ID = $tmpOrderId";
            $deleteSqlRes = $this->db->conn->query($deleteSql);
            if (!$deleteSqlRes && mysqli_affected_rows($this->db->conn) == 0) {
                throw new Exception("删除maintan_kiln_order数据失败");
            }
        }


        //查询操作人
        $checkSql = "select B.Number from  usertable UT 
                LEFT JOIN staffmain B ON B.Number = UT.Number where UT.openid = '$openId' limit 1";
        $res = $this->db->format($checkSql);
        if ($res == null)
            throw new Exception("未查找到操作人员！");
        $number = $res[0]['Number'];

        //插入养护订单表
        $sql = "INSERT INTO maintan_order(KilnId,TrolleyNo,`Status`,Operator,Created) VALUES($kilnId,'$trolleyNo',0,$number,NOW());";
        $sqlRes = $this->db->conn->query($sql);
        if (!$sqlRes) {
            throw new Exception("插入maintan_order失败");
        }
        $idd = mysqli_insert_id($this->db->conn);

        //更新窑位绑定订单信息
        $updateSql = "UPDATE maintan_kiln_bits set MaintanOrderId = $idd WHERE ID = $kilnId";
        $updateSqlRes = $this->db->conn->query($updateSql);
        if (!$updateSqlRes) {
            throw new Exception("更新maintan_kiln_bits失败");
        }
        return true;
    }

    /**
     * 获取养护窑详情
     * @param $workshopId
     * @return array|null
     */
    public function getKilnBits($workshopId)
    {
        $sql = "SELECT K.ID kilnId,K.KRowNo,K.LineNo,K.TemperatureValue,K.HumidityValue,K.KType,K.MaintanOrderId,
                K.WorkshopdataId, O.MaintanTime,O.`Status`,O.TrolleyNo,O.Remark FROM maintan_kiln_bits K 
                LEFT JOIN maintan_order	O ON O.ID = K.MaintanOrderId WHERE K.WorkshopdataId = $workshopId";
        return $this->db->format($sql);
    }

    /**
     * 时间获取
     * @param $workshopId
     * @param $tradeId
     * @return array|null
     */
    public function getDate($workshopId, $tradeId="")
    {
        $searchRow = "";
        if($tradeId!="")
        {
            $searchRow= " AND O.Id = $tradeId ";
        }
        $querySql = $this->productsSql . "AND SC.WorkShopId = $workshopId ".$searchRow." group by SC.scdate";
        $sql = "select A.scdate  from (  " . $querySql . " ) A group by A.scdate";
        return $this->db->format($sql);
    }

    /**
     * 窑位养护页面列表接口
     * @param $workshopId
     * @param $tradeId
     * @param $trolleyNo
     * @param $scdate
     * @param $status
     * @return array
     */
    public function searchProducts($workshopId, $tradeId, $trolleyNo, $scdate, $status)
    {
        $searchRow = "";
        if($tradeId!="")
        {
            $searchRow= " AND O.Id = $tradeId ";
        }
        $sql = $this->productsSql . "AND SC.WorkShopId = $workshopId ".$searchRow;
        if ($trolleyNo != '') {
            if ($trolleyNo == 'null') {
                $sql .= " AND S.RealLining is null";
            }else {
                $sql .= " AND S.RealLining = '$trolleyNo'";
            }
        }

        if ($scdate != '') {
            $sql .= " AND date_format(SC.scdate, '%Y-%c-%d' ) = '$scdate'";
        }

        $sqlRes = $this->db->format($sql);

        //查询窑位信息
        $mainSql = "SELECT MO.ID maintanOrderId,KB.ID,MO.TrolleyNo,MO.`Status`,MO.MaintanTime,KB.KRowNo,KB.ID kilnId,KB.LineNo,KB.KType,CONCAT(KB.LineNo,KB.KRowNo) klinName,M.`Name` FROM maintan_kiln_bits KB
LEFT JOIN maintan_order MO ON KB.MaintanOrderId = MO.ID
LEFT JOIN staffmain M ON MO.Operator = M.Number
WHERE KB.MaintanOrderId != 0 ";
        if ($status != '') {
            $mainSql .= " AND MO.`Status` = $status";
        }

        if ($trolleyNo != '') {
            $mainSql .= " AND MO.TrolleyNo = '$trolleyNo'";
        }

        $mainSqlRes = $this->db->format($mainSql);


        //将这些构件按照台车号分类
        $returnArr = array();

        foreach ($sqlRes as $item) {
            $tmpLiningNo = $item['liningNo'];
            if (isset($returnArr[$tmpLiningNo])) {
                array_push($returnArr[$tmpLiningNo], $item);
            } else {
                $returnArr[$tmpLiningNo] = array($item);
            }

        }

        $returnInfo = array();

        if ($status != '') {
            if($mainSqlRes == null){
                return null;
            }
        }
        foreach ($returnArr as $key => $item) {
            $flag = 0;
            foreach ($mainSqlRes as $main) {
                if ($key == $main['TrolleyNo']) {
                    $addItem = array_merge($main, array("list" => $item));
                    array_push($returnInfo, $addItem);
                    $flag = 1;
                    break;
                }
            }
            if ($flag == 0) {
                $addItem = array("TrolleyNo" => $key, "list" => $item);
                array_push($returnInfo, $addItem);
            }
        }

        return $returnInfo;
    }

    /**
     * 入窑确认
     * @param $orders
     * @return bool
     * @throws Exception
     */
    public function intoKilnBit($orders,$openId)
    {
        //查询操作人
        $checkSql = "select B.Number from  usertable UT 
                LEFT JOIN staffmain B ON B.Number = UT.Number where UT.openid = '$openId' limit 1";
        $res = $this->db->format($checkSql);
        if ($res == null)
            throw new Exception("未查找到操作人员！");
        $number = $res[0]['Number'];


        foreach ($orders as $item) {
            $maintanOrderId = $item["maintanOrderId"];
            $sql = "SELECT Id FROM maintan_kiln_bits WHERE MaintanOrderId = $maintanOrderId";
            $sqlRes = $this->db->format($sql);
            if ($sqlRes == null) {
                throw new Exception("未找到MaintanOrderId为" . $maintanOrderId . "所配置的窑位");
            }
            $checkSql = "SELECT ID,`Status`,TrolleyNo from maintan_order WHERE ID = $maintanOrderId";
            $checkSqlRes = $this->db->format($checkSql);
            if ($checkSqlRes == null) {
                throw new Exception("订单编号不存在");
            }
            $status = $checkSqlRes[0]['Status'];
            $TrolleyNo = $checkSqlRes[0]['TrolleyNo'];
            $MaintanId = $checkSqlRes[0]['ID'];
            if ($status == 3 || $status == 4) {
                throw new Exception("台车编号为" . $TrolleyNo . "已在出窑阶段或已出窑！");
            } else if ($status == 1) {
                throw new Exception("该台车已在养护待确认中");
            } else if ($status == 2) {
                throw new Exception("该台车已在养护中");
            } else if ($status == 0) {
                $updateSql = "UPDATE maintan_order set `Status` = 1 WHERE ID=$maintanOrderId";
                $updateSqlRes = $this->db->conn->query($updateSql);
                if (!$updateSqlRes) {
                    throw new Exception("数据更新失败");
                }

                //调用wcs接口
            }

            $updateProSql = $this->productsSql." AND S.RealLining = '$TrolleyNo' ";

            $updateSqlProRes = $this->db->format($updateProSql);
            if (!$updateSqlProRes) {
                throw new Exception("数据查询失败");
            }

            foreach ($updateSqlProRes as $item){
                $POrderId = $item['POrderId'];
                //将构建插入maintan_order_product_mapping
                $insertSql = "insert into maintan_order_product_mapping (POrderID,MainOrderId,Operator,Created)
                              VALUES ('$POrderId',$MaintanId,$number,NOW()) ; ";
                $this->db->conn->query($insertSql);
            }
        }
        return true;
    }


    /**
     * 手动入窑确认
     * @param $orders
     * @return bool
     * @throws Exception
     */
    public function intoKilnBitForce($orders,$openId)
    {
        //查询操作人
        $checkSql = "select B.Number from  usertable UT 
                LEFT JOIN staffmain B ON B.Number = UT.Number where UT.openid = '$openId' limit 1";
        $res = $this->db->format($checkSql);
        if ($res == null)
            throw new Exception("未查找到操作人员！");
        $number = $res[0]['Number'];
        foreach ($orders as $item) {
            $maintanOrderId = $item["maintanOrderId"];
            $sql = "SELECT Id FROM maintan_kiln_bits WHERE MaintanOrderId = $maintanOrderId";
            $sqlRes = $this->db->format($sql);
            if ($sqlRes == null) {
                throw new Exception("未找到MaintanOrderId为" . $maintanOrderId . "所配置的窑位");
            }
            $checkSql = "SELECT ID,`Status`,TrolleyNo from maintan_order WHERE ID = $maintanOrderId";
            $checkSqlRes = $this->db->format($checkSql);
            if ($checkSqlRes == null) {
                throw new Exception("订单编号不存在");
            }
            $status = $checkSqlRes[0]['Status'];
            $TrolleyNo = $checkSqlRes[0]['TrolleyNo'];
            $MaintanId = $checkSqlRes[0]['ID'];
            if ($status == 3 || $status == 4) {
                throw new Exception("台车编号为" . $TrolleyNo . "已在出窑阶段或已出窑！");
            } else if ($status == 1) {
                throw new Exception("该台车已在养护待确认中");
            } else if ($status == 2) {
                throw new Exception("该台车已在养护中");
            } else if ($status == 0) {
                $updateSql = "UPDATE maintan_order set `Status` = 2,ForceFlag = 1 WHERE ID=$maintanOrderId";
                $updateSqlRes = $this->db->conn->query($updateSql);
                if (!$updateSqlRes) {
                    throw new Exception("数据更新失败");
                }
            }
            $updateProSql = $this->productsSql." AND S.RealLining = '$TrolleyNo' ";
            $updateSqlProRes = $this->db->format($updateProSql);
            if (!$updateSqlProRes) {
                throw new Exception("数据查询失败");
            }
            foreach ($updateSqlProRes as $item){
                $POrderId = $item['POrderId'];
                //将构建插入maintan_order_product_mapping
                $insertSql = "insert into maintan_order_product_mapping (POrderID,MainOrderId,Operator,Created)
                              VALUES ('$POrderId',$MaintanId,$number,NOW()) ; ";
                $this->db->conn->query($insertSql);
            }
        }
        return true;
    }

    /**
     * 根据订单id获取窑位详情
     * @param $maintanOrderId
     * @return mixed
     * @throws Exception
     */
    public function getProductsByMaintanOrderId($maintanOrderId)
    {
        $sql = "SELECT K.ID kilnId,K.KRowNo,K.LineNo,K.TemperatureValue,K.HumidityValue,K.KType,K.MaintanOrderId,
                K.WorkshopdataId, O.MaintanTime,O.`Status`,O.TrolleyNo,O.Remark,CONCAT(K.LineNo,K.KRowNo) klinName FROM maintan_kiln_bits K 
                LEFT JOIN maintan_order	O ON O.ID = K.MaintanOrderId WHERE O.ID = $maintanOrderId";
        $sqlRes = $this->db->format($sql);
        if ($sqlRes == null) {
            throw new Exception("未查询到数据");
        }

        $TrolleyNo = $sqlRes[0]['TrolleyNo'];
        $querySql = $this->productsSql . " AND S.RealLining = '$TrolleyNo'";
        $querySqlRes = $this->db->format($querySql);

        $kilnInfo = $sqlRes[0];
        $kilnInfo["listInfo"] = $querySqlRes;
        return $kilnInfo;
    }

    /**
     * 出窑
     * @param $maintanOrderId
     * @return bool
     * @throws Exception
     */
    public function outKilnBit($maintanOrderId)
    {
        $sql = "SELECT Id FROM maintan_kiln_bits WHERE MaintanOrderId = $maintanOrderId";
        $sqlRes = $this->db->format($sql);
        if ($sqlRes == null) {
            throw new Exception("未找到MaintanOrderId为" . $maintanOrderId . "所配置的窑位");
        }
        $checkSql = "SELECT ID,`Status`,TrolleyNo ,ForceFlag from maintan_order WHERE ID = $maintanOrderId";
        $checkSqlRes = $this->db->format($checkSql);
        if ($checkSqlRes == null) {
            throw new Exception("订单编号不存在");
        }

        $status = $checkSqlRes[0]['Status'];
        $TrolleyNo = $checkSqlRes[0]['TrolleyNo'];
        $ForceFlag = $checkSqlRes[0]['ForceFlag'];

        //判断是否是手动操作
        if($ForceFlag == 1){
            throw new Exception("该台车为手动入窑，无法出窑");
        }

        if ($status == 2) {
            $updateSql = "UPDATE maintan_order set `Status` = 3 WHERE ID=$maintanOrderId";
            $updateSqlRes = $this->db->conn->query($updateSql);
            if (!$updateSqlRes) {
                throw new Exception("数据更新失败");
            }
        } else if ($status == 4) {
            throw new Exception("该台车已出窑成功！无需重复点击");
        } else if ($status == 0 || $status == 1) {
            throw new Exception("只有在状态为养护中的台车才可出窑");
        }
        return true;
    }


    /**
     * 出窑(强制)
     * @param $maintanOrderId
     * @return bool
     * @throws Exception
     */
    public function outKilnBitForce($maintanOrderId)
    {
        $sql = "SELECT Id FROM maintan_kiln_bits WHERE MaintanOrderId = $maintanOrderId";
        $sqlRes = $this->db->format($sql);
        if ($sqlRes == null) {
            throw new Exception("未找到MaintanOrderId为" . $maintanOrderId . "所配置的窑位");
        }
        $checkSql = "SELECT ID,`Status`,TrolleyNo,KilnId,ForceFlag from maintan_order WHERE ID = $maintanOrderId";
        $checkSqlRes = $this->db->format($checkSql);
        if ($checkSqlRes == null) {
            throw new Exception("订单编号不存在");
        }
        $status = $checkSqlRes[0]['Status'];
        $TrolleyNo = $checkSqlRes[0]['TrolleyNo'];
        $KilnId = $checkSqlRes[0]['KilnId'];
        $ForceFlag = $checkSqlRes[0]['ForceFlag'];
        //判断是否是手动操作
        if($ForceFlag != 1){
            throw new Exception("该台车非手动入窑，无法手动出窑");
        }
        if ($status == 2) {
            $updateSql = "UPDATE maintan_order set `Status` = 4,OutMaintanTime= NOW() WHERE ID= $maintanOrderId";
            $updateSqlRes = $this->db->conn->query($updateSql);
            if(!$updateSqlRes){
                throw new Exception("订单id为".$maintanOrderId."状态更新失败,");
            }
            //将窑位绑定订单解绑
            $updateSql = "UPDATE maintan_kiln_bits set MaintanOrderId = 0 WHERE ID = $KilnId";
            $updateSqlRes = $this->db->conn->query($updateSql);
            if(!$updateSqlRes || mysqli_affected_rows($this->db->conn) == 0){
                throw new Exception("订单id为".$maintanOrderId."出窑状态更新失败,");
            }

            //更新yw1_scsheet  estate状态置位1
            $updateSCsheetSql  = "UPDATE yw1_scsheet set Estate = 1 WHERE 1 AND ActionId = 101 AND POrderId in (
              SELECT POrderID FROM maintan_order_product_mapping WHERE MainOrderId = $maintanOrderId 
            )";
            $updateSCsheetSqlRes = $this->db->conn->query($updateSCsheetSql);
            if(!$updateSCsheetSqlRes || mysqli_affected_rows($this->db->conn) == 0){
                throw new Exception("数据更新失败");
            }

            //更新yw1_scsheet  estate状态置位1
            $updateSCsheetSql  = "UPDATE yw1_scsheet set Estate = 0 WHERE 1 AND ActionId = 110 AND POrderId in (
              SELECT POrderID FROM maintan_order_product_mapping WHERE MainOrderId = $maintanOrderId 
            )";
            $updateSCsheetSqlRes = $this->db->conn->query($updateSCsheetSql);
            if(!$updateSCsheetSqlRes || mysqli_affected_rows($this->db->conn) == 0){
                throw new Exception("数据更新失败");
            }


        } else if ($status == 4) {
            throw new Exception("该台车已出窑成功！无需重复点击");
        } else if ($status == 0 || $status == 1) {
            throw new Exception("只有在状态为养护中的台车才可出窑");
        }
        return true;
    }
}
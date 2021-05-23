<?php
 include_once('dbproduct.php');
include "../../processName.php";

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

    public function get_name_rule()
    {
        $sql ="select DISTINCT NameRule from producttype";
        return $this->format($sql);
    }

    public function INSERTLOG($item,$function,$log,$result,$openId="")
    {
        $user_id=0;
        $openId = $_SESSION["openid"];
        $checkSql = "select B.Number from  usertable UT LEFT JOIN staffmain B ON B.Number = UT.Number where UT.openid = '$openId' limit 1";
        $res = $this->format($checkSql);
        if($res!=null)
        {
            $user_id = $res[0]['Number'];
        }

        $sql = "INSERT INTO oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES (now(),'$item','$function','$log','$result',$user_id)";
        $this->db->conn->query($sql);
    }


    /**
     * 获取所有构件的项目信息
     */
    public function get_company_forshort()
    {
        $sql = "SELECT I.TradeId TradeId,O.CompanyId, I.TradeNo TradeNo,O.Forshort Forshort FROM trade_info I INNER JOIN trade_object O ON O.Id = I.TradeId";
        return $this->format($sql);
    }

    /**
     * 获取生产线
     * @return array|null
     */
    public function get_work_shop(){

        $sql = "SELECT Id,`Name` FROM workshopdata ;";
        return $this->format($sql);
    }

    /**
     * 新增记录
     * @param $recordNo
     * @param $recordName
     * @param $workShopId
     * @param $status
     * @param $openId
     * @return array|null
     */
    public function create_inspection_record($recordNo, $recordName, $workShopId,$status,$openId){
        $sql = "SELECT Id FROM sc1_inspection_record WHERE RecordNO='$recordNo' AND Status = $status";
        if($this->conn->query($sql)->num_rows > 0){
            $result = $this->format($sql);
            array_push($result,array('exist' => 1));
            return $result;
        }
        else
        {
            if($recordName=="" || $workShopId==""|| $status == "" || $recordNo==""){
                return array('status' => 1);
            }
            $csql="INSERT INTO sc1_inspection_record (RecordNo,RecordName,WorkShopId,Status,Creator) VALUES('$recordNo','$recordName',$workShopId,$status,'$openId') ";
            $this->conn->query($csql);
            $result = $this->format($sql);
            array_push($result,array('exist' => 0));
            return $result;
        }
    }

    public function get_product_by_workshop($workshopId,$tradeId=0)
    {
        $sql = "select P.ProductId from 
	yw1_scsheet SC 
	INNER JOIN workshopdata W  ON W.Id = SC.WorkShopId 
	INNER JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
	INNER JOIN yw1_ordermain M ON M.OrderNumber=Y.OrderNumber 
	LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
	INNER JOIN productdata P ON P.ProductId=Y.ProductId
	Where W.Id =$workshopId ";
        if($tradeId!=0){
            $sql .=" AND O.Id=$tradeId";
        }
        return $this->format($sql);
    }

    /**
     * 插入构件
     * @param $inspectionRecordId
     * @param $productId
     * @return bool
     */
    public function InsertInspectionRecord($inspectionRecordId, $product,$openId="",$status=1)
    {
        $sql = "";
        foreach ($product as $item) {
            $pId = $item['productId'];
            $cjtjId = $item["cjtjId"];
            if($status == 1){
                $sql = "INSERT INTO sc1_inspection_product(InspectionRecordId,ProductId,CjtjId,Creator,Modifier) VALUES($inspectionRecordId,$pId,$cjtjId,'$openId','$openId');";
            }else{
                $sql = "INSERT INTO sc1_inspection_product(InspectionRecordId,ProductId,GysShsheetId,Creator,Modifier) VALUES($inspectionRecordId,$pId,$cjtjId,'$openId','$openId');";
            }

            if(!$this->conn->query($sql)){
                return false;
            }
        }
        return true;
    }


    /**
     * 扫码添加
     * @param $inspectionRecordId
     * @param $productName
     * @param string $openId
     * @return bool
     */
    public function InsertInspectionProductByQrCode($inspectionRecordId , $productName,$workshopId, $openId="")
    {
        $ssql = "SELECT O.Forshort,M.CompanyId,M.OrderDate,SC.Id,
    S.POrderId,S.OrderPO,S.Price,S.sgRemark,S.DeliveryDate,S.ShipType,
    SC.Id,SC.sPOrderId,SC.Qty,SC.Estate,SC.ActionId,SC.StockId,SC.Remark,
    P.ProductId,P.cName,P.eCode,P.TestStandard,P.pRemark,
    U.Name AS Unit,PI.Leadtime,PI.Leadweek,D.TypeId
	FROM  yw1_scsheet SC 
	INNER JOIN yw1_ordersheet S ON S.POrderId = SC.POrderId
	INNER JOIN yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
	INNER JOIN productdata P ON P.ProductId=S.ProductId
	INNER JOIN productunit U ON U.Id=P.Unit
	INNER JOIN cg1_stocksheet G ON G.StockId = SC.StockId
	INNER JOIN stuffdata D ON D.StuffId = G.StuffId
	LEFT  JOIN yw3_pileadtime PI ON PI.POrderId=S.POrderId
	LEFT  JOIN yw2_orderexpress E ON E.POrderId=S.POrderId
	WHERE 1 AND SC.scFrom>0 AND SC.Estate>0 AND SC.ActionId=101 AND P.cName='$productName' AND getCanStock(SC.sPOrderId,3)=3  GROUP BY SC.Id ORDER BY  PI.Leadweek DESC";

        if($this->format($ssql)!=null)
        {
            //将脱模入库的构件完成扫码添加操作
            $this->warehouseConfirm($productName,101,$openId);
        }
        $sql = "SELECT  P.ProductId, S.Id
                    FROM
                        sc1_cjtj S
                        INNER JOIN cg1_stocksheet G ON G.StockId = S.StockId
                        INNER JOIN yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
                        INNER JOIN workshopdata W ON W.Id = SC.WorkShopId
                        INNER JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
                        INNER JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
                        LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
                        INNER JOIN productdata P ON P.ProductId = Y.ProductId
                        INNER JOIN producttype PT ON PT.TypeId = P.TypeId 
                        INNER JOIN productunit U ON U.Id = P.Unit
                        LEFT JOIN yw3_pileadtime PI ON PI.POrderId = Y.POrderId 
                    WHERE
                        S.Estate = '1' 
                        AND G.LEVEL = 1
                        AND W.Id=$workshopId 
AND P.cName='$productName'";
        $res = $this->format($sql);
        if($res==null)
        {
            $ssql = "SELECT  P.ProductId, S.Id
                    FROM
                        sc1_cjtj S
                        INNER JOIN cg1_stocksheet G ON G.StockId = S.StockId
                        INNER JOIN yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
                        INNER JOIN workshopdata W ON W.Id = SC.WorkShopId
                        INNER JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
                        INNER JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
                        LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
                        INNER JOIN productdata P ON P.ProductId = Y.ProductId
                        INNER JOIN producttype PT ON PT.TypeId = P.TypeId 
                        INNER JOIN productunit U ON U.Id = P.Unit
                        LEFT JOIN yw3_pileadtime PI ON PI.POrderId = Y.POrderId 
                    WHERE
                        S.Estate = '1' 
                        AND G.LEVEL = 1
                        AND P.cName='$productName'";


            if($this->format($ssql)!=null)
            {
                $ssql = "SELECT  w.`Name` WName
                    FROM workshopdata W
                        LEFT JOIN yw1_scsheet SC ON W.Id = SC.WorkShopId
                        LEFT JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
                        LEFT JOIN productdata P ON P.ProductId = Y.ProductId
                    WHERE
                   P.cName='$productName' LIMIT 1";
                $result = $this->format($ssql)[0]["WName"];
                $Err = "添加失败，该构件的当前产线是".$result;
                throw new Exception($Err);
            }else{
                $msg= $this->GetMsg($productName);
                throw new Exception($msg);
            }
        }


        $pId = $res[0]['ProductId'];
        $cjtjId = $res[0]["Id"];
        $checkSql = "select * from sc1_inspection_product WHERE InspectionRecordId = $inspectionRecordId AND ProductId = $pId ";
        $res = $this->format($checkSql);
        if($res!=null)
            throw new Exception("添加失败，该构件已被添加！");
        $sql = "INSERT INTO sc1_inspection_product(InspectionRecordId,ProductId,CjtjId,Creator,Modifier) VALUES('$inspectionRecordId','$pId',$cjtjId,'$openId','$openId');";
        if(!$this->conn->query($sql)){
            return false;
        }
        return true;

    }

    /**
     * 生产过程中扫码添加
     * @param $inspectionRecordId
     * @param $productName
     * @param string $openId
     * @return bool
     * @throws Exception
     */
    public function InsertInspectionProductByQrCodeProducting($inspectionRecordId , $productName,$workshopId, $openId="")
    {


           /*         $ssql = "SELECT O.Forshort,SC.Id,SC.POrderId,SC.sPOrderId,SC.Qty,SC.Estate,SC.ActionId,
                    SC.StockId AS scStockId,SC.Remark,SC.mStockId,
            D.StuffId,D.StuffCname,D.Picture,SD.TypeId,
            G.DeliveryDate,G.DeliveryWeek,SM.PurchaseID,G.Mid,SC.mStockId,Y.OrderPO,P.cName
            FROM  yw1_scsheet SC 
            LEFT JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
            LEFT JOIN yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
            LEFT JOIN productdata P ON P.ProductId=Y.ProductId
            LEFT JOIN trade_object O ON O.CompanyId = OM.CompanyId
            LEFT JOIN cg1_semifinished M ON M.StockId = SC.StockId
            LEFT JOIN cg1_stocksheet G ON G.StockId = M.mStockId
            LEFT JOIN cg1_stockmain SM ON SM.Id = G.Mid
            LEFT JOIN stuffdata D ON D.StuffId = M.mStuffId
            LEFT JOIN stuffdata SD  ON SD.StuffId = M.StuffId
            WHERE 1 AND SC.scFrom>0 AND SC.Estate>0 AND SC.ActionId=104
             AND P.cName='$productName' AND getCanStock(SC.sPOrderId,3)=3  GROUP BY SC.Id  ORDER BY G.DeliveryWeek ASC";
        if($this->format($ssql)!=null)
        {
            // 将骨架搭建的构件完成扫码添加操作
            $this->warehouseConfirm($productName,104,$openId);
        }*/
    
        $dbproduct=new DbProduct();
        $dbproduct->structuresByProuctName($productName,$openId);
        $sql = "SELECT DISTINCT
                        O.Forshort,
                        P.ProductId,
                        P.cName,
                        S.Id 
            FROM
                gys_shsheet S
                LEFT JOIN gys_shmain M ON S.Mid = M.Id
                LEFT JOIN cg1_stocksheet G ON G.StockId = S.StockId
                LEFT JOIN yw1_ordersheet Y ON Y.POrderId = G.POrderId
                LEFT JOIN yw1_scsheet SC ON Y.POrderId = SC.POrderId
                LEFT JOIN yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
                LEFT JOIN productdata P ON P.ProductId = Y.ProductId
                LEFT JOIN trade_object O ON O.CompanyId = OM.CompanyId
                LEFT JOIN stuffdata D ON D.StuffId = S.StuffId
                LEFT JOIN stuffunit U ON U.Id = D.Unit
                LEFT JOIN stufftype T ON T.TypeId = D.TypeId 
                
            WHERE
                1  
                AND S.Estate = 2 
                AND D.TypeId = 9017
                AND P.cName='$productName'  AND SC.WorkShopId = $workshopId";
        $res = $this->format($sql);
        if ($res == null)
        {
            //判断是否待质检的半成品构件
            $ssql = "SELECT DISTINCT
                        O.Forshort,
                        P.ProductId,
                        P.cName,
                        S.Id 
            FROM
                gys_shsheet S
                LEFT JOIN gys_shmain M ON S.Mid = M.Id
                LEFT JOIN cg1_stocksheet G ON G.StockId = S.StockId
                LEFT JOIN yw1_ordersheet Y ON Y.POrderId = G.POrderId
                LEFT JOIN yw1_scsheet SC ON Y.POrderId = SC.POrderId
                LEFT JOIN yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
                LEFT JOIN productdata P ON P.ProductId = Y.ProductId
                LEFT JOIN trade_object O ON O.CompanyId = OM.CompanyId
                LEFT JOIN stuffdata D ON D.StuffId = S.StuffId
                LEFT JOIN stuffunit U ON U.Id = D.Unit
                LEFT JOIN stufftype T ON T.TypeId = D.TypeId 
                
            WHERE
                1  
                AND S.Estate = 2 
                AND D.TypeId = 9017
                AND P.cName='$productName' ";
            if($this->format($ssql)!=null)
            {
                //根据构件名查询产线
                $ssql = "SELECT  w.`Name` WName
                    FROM workshopdata W
                        LEFT JOIN yw1_scsheet SC ON W.Id = SC.WorkShopId
                        LEFT JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
                        LEFT JOIN productdata P ON P.ProductId = Y.ProductId
                    WHERE
                   P.cName='$productName' LIMIT 1";
                $result = $this->format($ssql)[0]["WName"];
                $Err = "添加失败，该构件的当前产线是".$result;
                throw new Exception($Err);
            }
            else{

               $msg= $this->GetMsg($productName);
               throw  new Exception($msg);
            }
        }
        $pId = $res[0]['ProductId'];
        $cjtjId = $res[0]["Id"];

        $checkSql = "select * from sc1_inspection_product WHERE InspectionRecordId =$inspectionRecordId AND ProductId =$pId ";
        $res = $this->format($checkSql);
        if($res!=null)
            throw new Exception("添加失败，该构件已被添加！");
        $sql = "INSERT INTO sc1_inspection_product(InspectionRecordId,ProductId,GysShsheetId,Creator,Modifier) VALUES($inspectionRecordId,$pId,$cjtjId,'$openId','$openId');";
        if(!$this->conn->query($sql)){
            return false;
        }
        return true;

    }

    /**
     * @param $inspectionRecordId
     * @param $inspectionProductId  sc1_inspection_product Id
     * @return bool
     */
    public function DeleteInspectionRecord( $inspectionProductId,$status=1){
        foreach ($inspectionProductId as $item){
            $Id = $item["inspectionProductId"];

            if($status == 0){
                $filterSql = "SELECT Id from sc1_inspection_product WHERE Id=$Id AND ProductId IN
                (
                SELECT DISTINCT
                    P.ProductId
                FROM
                    gys_shsheet S
                    LEFT JOIN gys_shmain M ON S.Mid = M.Id
                    LEFT JOIN cg1_stocksheet G ON G.StockId = S.StockId
                    LEFT JOIN yw1_ordersheet Y ON Y.POrderId = G.POrderId
                    LEFT JOIN yw1_scsheet SC ON Y.POrderId = SC.POrderId
                    LEFT JOIN yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
                    LEFT JOIN productdata P ON P.ProductId = Y.ProductId
                    LEFT JOIN trade_object O ON O.CompanyId = OM.CompanyId
                    LEFT JOIN stuffdata D ON D.StuffId = S.StuffId
                    LEFT JOIN stuffunit U ON U.Id = D.Unit
                    LEFT JOIN stufftype T ON T.TypeId = D.TypeId 
                WHERE
                    1 
                    AND S.Estate = 2 
                    AND D.TypeId = 9017
                ORDER BY
                    S.Id)";
                $res = $this->format($filterSql);
                if($res == null){
                    throw new Exception("该构件已审核，无法删除！");
                }
            }

            $sql = "DELETE FROM sc1_inspection_product WHERE  Id=$Id AND Operator is null ";
            if(!$this->conn->query($sql)){
                return false;}
        }
        return true;
    }

    /**
     * 根据$inspectionRecordId获取构件详情
     * @param $inspectionRecordId
     * @return array|null
     */
    public function getProductByInspectionRecord($inspectionRecordId,$status=1)
    {
        $sql = "SELECT RecordNo,RecordName,WorkShopId,W.Name WorkShopName,B.NAME Creator,S.Created,S.ImageUrl from sc1_inspection_record S
JOIN workshopdata W ON S.WorkShopId = W.Id
LEFT JOIN usertable UT ON UT.openid = S.Creator
LEFT JOIN staffmain B ON B.Number = UT.Number 
WHERE S.Id = $inspectionRecordId LIMIT 1";
        $result = $this->format($sql);
        if($status == 1){
            $sql = "select T.Forshort,P.cName,SC.Estate,P.ProductId,B.NAME uName,S.Id InspectionProductId,S.CjtjId from sc1_inspection_product S
                    Join productdata P on S.ProductId = P.ProductId
                    JOIN sc1_cjtj SC ON SC.Id = S.CjtjId
                    JOIN trade_object  T ON P.CompanyId = T.CompanyId
                    LEFT JOIN usertable UT ON UT.openid = S.Operator
                    LEFT JOIN staffmain B ON B.Number = UT.Number
                where inspectionRecordId=$inspectionRecordId";
        }else{
            // estate 2 待质检  其他 合格
            $sql = "select   T.Forshort,P.cName,P.ProductId,P.Id productdataId
            ,B.NAME uName
            ,S.Id InspectionProductId ,S.InspectionRecordId 
            ,GS.Estate,S.GysShsheetId AS CjtjId from sc1_inspection_product S
            Join productdata P on S.ProductId = P.ProductId
            JOIN trade_object  T ON P.CompanyId = T.CompanyId
            LEFT JOIN usertable UT ON UT.openid = S.Operator
            LEFT JOIN staffmain B ON B.Number = UT.Number
            LEFT JOIN yw1_ordersheet Y ON P.ProductId=Y.ProductId
            LEFT JOIN gys_shsheet GS ON GS.ID = S.GysShsheetId  WHERE S.InspectionRecordId = $inspectionRecordId";
        }

        array_push($result,$this->format($sql));
        return $result;
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

    //获取上一次数据
    public function get_last_search($inspectionRecordId, $openid="op_TywzYDwG4walmycIBLQWKdEn8")
    {
        $lastSql = "SELECT WorkshopId,TradeId,BuildingNo,FloorNo,CmptTypeId,ProductName FROM inspection_record_search_params where InspectionRecordId =$inspectionRecordId and CreateBy='$openid' LIMIT 1;";
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
     * 根据入参查询构件信息
     * @param $workshopId       产线ID
     * @param $tradeId          项目ID
     * @param $buildingNo       楼栋编号
     * @param $floorNo          层数
     * @param $type             类型
     * @param $productCode      构件编号
     * @return array|null
     */
    public function search_product($workshopId,$tradeId, $buildingNo, $floorNo, $type, $productCode,$status,$inspectionRecordId, $openId)
    {
        $sql = "";
        if($status==1)
        {
            $sql ="SELECT
                        O.Forshort,
                        P.ProductId,
                        P.cName,
                        S.Id AS CjtjId
                    FROM
                        sc1_cjtj S
                        INNER JOIN cg1_stocksheet G ON G.StockId = S.StockId
                        INNER JOIN yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
                        INNER JOIN workshopdata W ON W.Id = SC.WorkShopId
                        INNER JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
                        INNER JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
                        LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
                        INNER JOIN productdata P ON P.ProductId = Y.ProductId
                        INNER JOIN producttype PT ON PT.TypeId = P.TypeId 
                        INNER JOIN productunit U ON U.Id = P.Unit
                        LEFT JOIN yw3_pileadtime PI ON PI.POrderId = Y.POrderId 
                    WHERE
                        W.Id =$workshopId
                        AND S.Estate = '1' 
                        AND G.LEVEL = 1 ";
            if ($tradeId != 0) {
                $sql .= " AND O.Id = $tradeId";
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
                $sql .= " AND P.cName LIKE '$cname'";
            } else {
                $buildingNo = "";
                $floorNo = "";
            }
            if ($type != "") {
                $sql .= " AND PT.TypeId = $type";
            } else {
                $type = "";
            }
            if ($productCode != "") {
                $sql .= " AND P.cName like '%$productCode%'";
            }

            //已添加的构件
            $InsertedSql = "SELECT productId from sc1_inspection_product IP LEFT JOIN sc1_inspection_record IR  ON IP.InspectionRecordId=IR.Id
WHERE IR.Status =$status";

            $sql .=" AND P.ProductId NOT IN (".$InsertedSql.")";


            $sql .= " LIMIT 1000";
            $lastSql = "SELECT WorkshopId,TradeId,BuildingNo,FloorNo,CmptTypeId,ProductName FROM inspection_record_search_params where InspectionRecordId =$inspectionRecordId and CreateBy='$openId' LIMIT 1;";
            $lastSearch = $this->format($lastSql);
            if (is_null($lastSearch)) {
                $insertSql = "INSERT INTO inspection_record_search_params (inspectionRecordId,WorkshopId,TradeId,BuildingNo,FloorNo,CmptTypeId,ProductName,Status,CreateBy) VALUES($inspectionRecordId,$workshopId,'$tradeId','$buildingNo','$floorNo','$type','$productCode',$status,'$openId');";
                $this->conn->query($insertSql);
            } else {
                $updateSql = "UPDATE inspection_record_search_params SET TradeId = '$tradeId',BuildingNo='$buildingNo',FloorNo='$floorNo',CmptTypeId='$type',ProductName='$productCode' WHERE InspectionRecordId =$inspectionRecordId AND CreateBy='$openId';";
                $this->conn->query($updateSql);
            }

            return $this->format($sql);
        }else{
            $sql = "SELECT DISTINCT
                        O.Forshort,
                        P.ProductId,
                        P.cName,
                        S.Id AS CjtjId
            FROM
                gys_shsheet S
                LEFT JOIN gys_shmain M ON S.Mid = M.Id
                LEFT JOIN cg1_stocksheet G ON G.StockId = S.StockId
                LEFT JOIN yw1_ordersheet Y ON Y.POrderId = G.POrderId
                LEFT JOIN yw1_scsheet SC ON Y.POrderId = SC.POrderId
                LEFT JOIN yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
                LEFT JOIN productdata P ON P.ProductId = Y.ProductId
                LEFT JOIN trade_object O ON O.CompanyId = OM.CompanyId
                LEFT JOIN stuffdata D ON D.StuffId = S.StuffId
                LEFT JOIN stuffunit U ON U.Id = D.Unit
                LEFT JOIN stufftype T ON T.TypeId = D.TypeId 
                
            WHERE
                1 
                AND S.Estate = 2 
                AND D.TypeId = 9017
                ";

            $sql .= " AND SC.WorkShopId = $workshopId";

            //已添加的构件
            $InsertedSql = "SELECT productId from sc1_inspection_product IP LEFT JOIN sc1_inspection_record IR  ON IP.InspectionRecordId=IR.Id
WHERE IR.Status =$status";


            $sql .=" AND P.ProductId NOT IN (".$InsertedSql.")";

            if ($tradeId != 0) {
                $sql .= " AND O.Id = $tradeId";
            }
            if ($buildingNo != 0) {
                $sql .= " AND P.BuildingNo = $buildingNo";
            }

            if($floorNo != 0){
                $sql .= " AND P.FloorNo = $floorNo";
            }

            if ($type != "") {
                $sql .= " AND P.TypeId = $type";
            }

            if ($productCode != "") {
                $sql .= " AND P.cName like '%$productCode%'";
            }
            $sql .= " LIMIT 1000";

            $lastSql = "SELECT WorkshopId,TradeId,BuildingNo,FloorNo,CmptTypeId,ProductName FROM inspection_record_search_params where InspectionRecordId =$inspectionRecordId and CreateBy='$openId' LIMIT 1;";
            $lastSearch = $this->format($lastSql);
            if (is_null($lastSearch)) {
                $insertSql = "INSERT INTO inspection_record_search_params (inspectionRecordId,WorkshopId,TradeId,BuildingNo,FloorNo,CmptTypeId,ProductName,Status,CreateBy) VALUES($inspectionRecordId,$workshopId,'$tradeId','$buildingNo','$floorNo','$type','$productCode',$status,'$openId');";
                $this->conn->query($insertSql);
            } else {
                $updateSql = "UPDATE inspection_record_search_params SET TradeId = '$tradeId',BuildingNo='$buildingNo',FloorNo='$floorNo',CmptTypeId='$type',ProductName='$productCode' WHERE InspectionRecordId =$inspectionRecordId AND CreateBy='$openId';";
                $this->conn->query($updateSql);
            }

            return $this->format($sql);
        }

    }

    /**
     * 构建质检合格
     * @param $cjtjId
     * @param $openId
     * @return bool
     */
    public function inspectProduct($cjtjId,$openId,$status=1)
    {
        foreach ($cjtjId as $item)
        {
            $Id = $item['cjtjId'];
            if($status == 1){
                $sql = "UPDATE sc1_cjtj SET Estate =2 WHERE Id= $Id;";
                if(!$this->conn->query($sql)){
                    throw new Exception("质检失败");
                }
                $sql = "UPDATE sc1_inspection_product SET Operator='$openId' WHERE CjtjId = $Id";
                if(!$this->conn->query($sql)){
                    throw new Exception("操作人更新失败");
                }
            }else{
                //查询操作人
                $checkSql = "select B.Number from  usertable UT
                LEFT JOIN staffmain B ON B.Number = UT.Number where UT.openid = '$openId' limit 1";
                $res = $this->format($checkSql);
                if($res==null)
                    throw new Exception("未查找到操作人员！");
                $number = $res[0]['Number'];
//                $number = 10045;
                //判断qc_badrecord 是否存在数据
                $selectSql = "SELECT Sid from qc_badrecord WHERE Sid = $Id";
                $selectSqlRes = $this->format($selectSql);
                if($selectSqlRes == null){
                    $insertBadrecordSql = "INSERT INTO qc_badrecord
                        (Id,shMid,Sid,StockId,StuffId,shQty,checkQty,Qty,AQL,Remark,Estate,Locks,Date,Operator,creator,created)
                                SELECT
                        NULL,Mid,'$Id',StockId,StuffId,Qty,'1','0','1','','0','0',NOW(),'$number','$number',NOW()
                        FROM  gys_shsheet WHERE Id = '$Id' LIMIT 1";
                    if(!$this->conn->query($insertBadrecordSql)){
                        throw new Exception("插入qc_badrecord失败");
                    }
                }
                $sqlCall = "CALL proc_ck1_rksheet_save('$Id','0',$number);";
                if(!$this->conn->query($sqlCall)){
                    throw new Exception("质检失败");
                }else{
                    $this->conn->close();
                    $this->__construct();
                }

                $sql = "SELECT G.stockId, G.POrderId, G.stuffId, SC.sPOrderId, SC.ActionId, SC.WorkshopId, 
                        round(G.OrderQty*(SC.Qty/O.Qty)) AS Qty1 
                        FROM gys_shsheet GS LEFT JOIN cg1_stocksheet G ON GS.stockId = G.StockId 
                        LEFT JOIN yw1_scsheet SC ON SC.POrderId = G.POrderId 
                        LEFT JOIN yw1_ordersheet O ON SC.POrderId = O.POrderId 
                        WHERE GS.id = $Id and ActionId = 101";
                $myResult = $this->format($sql);
                if(!$myResult){
                    throw new Exception("数据获取失败");
                }
                $myRow = $myResult[0];
                $StockId = $myRow['stockId'];
                $POrderId = $myRow['POrderId'];
                $StuffId = $myRow['stuffId'];
                $sPOrderId = $myRow['sPOrderId'];
                $ActionId = $myRow['ActionId'];
                $WorkShopId = $myRow['WorkshopId'];
                $llQty = $myRow['Qty1'];
                $fromPage = "";

                $myResult = $this->conn->query("CALL proc_ck5_llsheet_save('$POrderId','$sPOrderId','$StockId','$StuffId','$llQty',$number,'$fromPage');");

                if(!$myResult){
                    throw new Exception("质检失败[proc_ck5_llsheet_save 执行失败]");
                }else{
                    $this->conn->close();
                    $this->__construct();
                }
                $DateTime = date("Y-m-d H:i:s");
                $upSql = "UPDATE ck5_llsheet SET Estate=0,Receiver='$number',Received='$DateTime'
    WHERE sPOrderId='$sPOrderId' AND StockId='$StockId' ";
                $upResult = $this->conn->query($upSql);

                if ($upResult) {
                    //echo "Y";
                    $UpdateComboxSql = "UPDATE ck5_llsheet  L
                        LEFT JOIN cg1_stuffcombox C ON C.StockId = L.StockId
                        SET L.Estate = 0,L.Receiver='$number',L.Received='$DateTime'
                        WHERE C.mStockId = '$StockId' AND L.sPOrderId = '$sPOrderId'";
                    $UpdateComboxResult = $this->conn->query($UpdateComboxSql);

                    $IN_recode = "INSERT INTO oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','工单领料确认','数据更新','领料单确认成功','Y','$number')";
                    $IN_res = $this->conn->query($IN_recode);
                    if(!$IN_res){
                        throw new Exception("插入操作日志失败");
                    }
                }


                $sql = "UPDATE sc1_inspection_product SET Operator='$openId' WHERE GysShsheetId = $Id";
                if(!$this->conn->query($sql)){
                    throw new Exception("操作人更新失败");
                }

                $workshopidSql = "SELECT WorkShopId FROM yw1_scsheet WHERE POrderId = $POrderId  AND ActionId = 104";
                $workshopidSqlRes =  $this->format($workshopidSql);
                $WorkShopIdTmp = $workshopidSqlRes[0]['WorkShopId'];
                //pc1 和pc4进入浇捣养护流程
//                 if($WorkShopIdTmp == 101 || $WorkShopIdTmp == 104){
//                     //更新yw1_scsheet  estate状态置位10
//                     $updateSCsheetSql  = "UPDATE yw1_scsheet set Estate = 0 WHERE 1 AND ActionId = 101 AND POrderId = $POrderId";
//                     $updateSCsheetSqlRes = $this->conn->query($updateSCsheetSql);
//                     if(!$updateSCsheetSqlRes){
//                         throw new Exception("数据更新失败");
//                     }

//                     //在scsheet新增actionid为110的数据

//                     $sql = "	SELECT * from yw1_scsheet WHERE POrderID = '$POrderId' AND ActionId = 104";
//                     $sqlRes = $this->format($sql);
//                     $scsheetEn = $sqlRes[0];
//                     $newSPOrderId = $scsheetEn['sPOrderId']+5;
//                     $ScFrom = $scsheetEn['ScFrom'];
//                     $Level = $scsheetEn['Level'];
//                     $Qty = $scsheetEn['Qty'];
//                     $ScQty = $scsheetEn['ScQty'];
//                     $scLineId = $scsheetEn['scLineId'];
//                     $Type = $scsheetEn['Type'];
//                     $KscQty= $scsheetEn['KscQty'];
//                     $mStockId= $scsheetEn['mStockId'];
// //                    $Estate= $scsheetEn['Estate'];
//                     $Estate= 1;
//                     $Locks= $scsheetEn['Locks'];
//                     $PLocks= $scsheetEn['PLocks'];
//                     $StockId= $scsheetEn['StockId'];
//                     $WorkShopId= $scsheetEn['WorkShopId'];
//                     $insertSql = "INSERT INTO yw1_scsheet (sPOrderId,POrderID,ActionId,ScFrom,WorkShopId,`Level`,Qty,ScQty,
//                                   Date,scDate,Operator,scLineId,Type,KscQty,Estate,Locks,PLocks,StockId,mStockId) values
//                                   ('$newSPOrderId','$POrderId',110,$ScFrom,$WorkShopId,$Level,$Qty,$ScQty,
//                                   NOW(),NOW(),$number,$scLineId,$Type,$KscQty,$Estate,$Locks,$PLocks,$StockId,$mStockId)";
//                     $insertSqlRes = $this->conn->query($insertSql);
//                     if(!$insertSqlRes){
//                         $this->INSERTLOG("半成品质检","PC1和PC4进入浇捣养护状态",$insertSql,"N",$openId);
//                         throw new Exception("浇捣养护数据插入失败");
//                     }
//                 }
            }

        }
        return true;
    }

    public function InsertImageUrl($inspectionRecordId,$imageUrl){

        $sql = "SELECT ImageUrl FROM sc1_inspection_record WHERE Id=$inspectionRecordId";
        $result = $this->format($sql);
        if($result[0]["ImageUrl"]==null)
        {
            $sql = "UPDATE sc1_inspection_record SET ImageUrl = '$imageUrl' WHERE Id=$inspectionRecordId ";
        }else{
            $param = ";". $imageUrl;
            $sql = "UPDATE sc1_inspection_record SET ImageUrl = concat(ImageUrl ,'$param') WHERE Id=$inspectionRecordId ";
        }

        if(!$this->conn->query($sql)){
            throw new Exception("图片上传失败");
        }
        return true;
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

    /**
     * 根据入参查询构件质检记录
     * @param $date       date
     * @param $workshopId       产线ID
     * @param $tradeId          公司ID
     * @param $buildingNo       楼栋编号
     * @param $floorNo          层数
     * @param $type             类型
     * @param $productCode      构件编号
     * @param $status           0生产过程中   1成品
     * @return array|null
     */
    public function search_inspection_record($date,$workshopId,$tradeId, $buildingNo, $floorNo, $type, $productCode,$status)
    {

        //todo     缺少构件状态  合格，失败
        if($status == 1){
            $sql = "
            select DISTINCT P.ProductId,TOB.Forshort,S.EState,IPT.modified, P.cName,IR.RecordNo,IR.ImageUrl,W.`Name` AS threadName,B.NAME uName,UINFO.Id AS userId from 
						sc1_inspection_product IPT
						LEFT JOIN sc1_inspection_record IR ON IR.Id = IPT.InspectionRecordId
						LEFT JOIN productdata P ON IPT.ProductId = P.ProductId 
						LEFT JOIN producttype PT ON PT.TypeId = P.TypeId 
						LEFT JOIN yw1_ordersheet Y ON P.ProductId=Y.ProductId
						LEFT JOIN yw1_ordermain M ON M.OrderNumber=Y.OrderNumber 
            LEFT JOIN trade_object TOB ON TOB.CompanyId = M.CompanyId	
            LEFT JOIN yw1_scsheet SC  ON Y.POrderId = SC.POrderId
            LEFT JOIN workshopdata W  ON W.Id = IR.WorkShopId 
						LEFT JOIN usertable UINFO ON UINFO.openid = IPT.Operator
						LEFT JOIN staffmain B ON B.Number = UINFO.Number";
        }
        else{
            $sql = 'select DISTINCT P.ProductId,TOB.Forshort,GS.EState,IPT.modified, 
P.cName,IR.RecordNo,IR.ImageUrl,W.`Name` AS threadName,B.NAME uName,UINFO.Id AS userId 
                        from sc1_inspection_product IPT
						LEFT JOIN sc1_inspection_record IR ON IR.Id = IPT.InspectionRecordId
						LEFT JOIN productdata P ON IPT.ProductId = P.ProductId 
						LEFT JOIN producttype PT ON PT.TypeId = P.TypeId 
						LEFT JOIN yw1_ordersheet Y ON P.ProductId=Y.ProductId
						LEFT JOIN yw1_ordermain M ON M.OrderNumber=Y.OrderNumber 
            LEFT JOIN trade_object TOB ON TOB.CompanyId = M.CompanyId	
            LEFT JOIN yw1_scsheet SC  ON Y.POrderId = SC.POrderId
            LEFT JOIN workshopdata W  ON W.Id = IR.WorkShopId 
						LEFT JOIN usertable UINFO ON UINFO.openid = IPT.Operator
						LEFT JOIN staffmain B ON B.Number = UINFO.Number
						LEFT JOIN gys_shsheet GS ON GS.ID = IPT.GysShsheetId ';
        }

        $sql .=" LEFT JOIN sc1_cjtj S ON IPT.CjtjId = S.Id";
        $sql .=" Where IR.status = $status";
        if($workshopId !=0){
            $sql .=" AND W.Id =$workshopId ";
        }
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
            $sql .= " AND P.cName LIKE '$cname'";
        } else {
            $buildingNo = "";
            $floorNo = "";
        }
        if ($type != "") {
            $sql .= " AND PT.TypeId = $type";
        } else {
            $type = "";
        }
        if ($productCode != "") {
            $sql .= " AND IR.RecordNo like '%$productCode%'";
        }

        if($date !=""){
            $sql .= " AND DATE_FORMAT(IR.Created,'%Y-%m-%d') like '%$date%'";
        }
        $sql .= " LIMIT 1000";
        return $this->format($sql);
    }


    /**
     * 成品入库登记
     * @param $cName
     * @param $user_id
     */
    public function warehouseConfirm($cName,$actionId,$openId){
        //查询操作人
        $checkSql = "select B.Number from  usertable UT
                LEFT JOIN staffmain B ON B.Number = UT.Number where UT.openid = '$openId' limit 1";
        $res = $this->format($checkSql);
        if($res==null)
            throw new Exception("未查找到操作人员！");
        $user_id = $res[0]['Number'];

        //判断此p_name 所处的阶段 并返回POrderId
        $p_name = $cName;

        $p_name_filter = " AND P.cName='$p_name' ";

        // $test_filter = ' AND SC.ActionId=101';//101 脱模入库 103 钢筋下料 104 骨架搭建 106 浇捣养护

        $checkScSign = 3;//可生产标识

        //获取工单信息
        $query = "SELECT O.Forshort,SC.Id,SC.POrderId,SC.sPOrderId,SC.Qty,SC.Estate,SC.ActionId,SC.StockId AS scStockId,SC.Remark,SC.mStockId,
        D.StuffId,D.StuffCname,D.Picture,SD.TypeId,P.eCode,OM.OrderPO,W.Name AS scLine,SC.WorkShopId,Y.ProductId,
        G.DeliveryDate,G.DeliveryWeek,SM.PurchaseID,G.Mid,SC.mStockId
        FROM  yw1_scsheet SC 
        LEFT JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
        LEFT JOIN yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
        LEFT JOIN productdata P ON P.ProductId = Y.ProductId 
        LEFT JOIN trade_object O ON O.CompanyId = OM.CompanyId
        LEFT JOIN workscline W ON W.Id = SC.scLineId
        LEFT JOIN cg1_semifinished M ON M.StockId = SC.StockId
        LEFT JOIN cg1_stocksheet G ON G.StockId = M.mStockId
        LEFT JOIN cg1_stockmain SM ON SM.Id = G.Mid
        LEFT JOIN stuffdata D ON D.StuffId = M.mStuffId
        LEFT JOIN stuffdata SD  ON SD.StuffId = M.StuffId
        WHERE 1 $p_name_filter AND SC.ActionId=$actionId AND SC.Estate>0 AND getCanStock(SC.sPOrderId,$checkScSign)=$checkScSign limit 1";
        $res = $this->format($query);
        if($res == null){
            throw new Exception("获取信息失败");
        }
        $myRow = $res[0];
        $Id = $myRow["Id"];

        $Forshort = $myRow['Forshort'];

        $POrderId = $myRow["POrderId"];

        $sPOrderId = $myRow["sPOrderId"];

        $StuffId = $myRow["StuffId"];

        $mStockId = $myRow["mStockId"];

        $Picture = $myRow["Picture"];

        $StuffCname = $myRow["StuffCname"];//有保留

        $PurchaseID = $myRow["PurchaseID"];

        $Qty = $myRow["Qty"];

        $Remark = $myRow["Remark"] == "" ? "&nbsp;" : $myRow["Remark"];

        $DeliveryDate = $myRow["DeliveryDate"];

        $DeliveryWeek = $myRow["DeliveryWeek"];//本配件的交期

        $scStockId = $myRow["scStockId"];

        $mStockId = $myRow["mStockId"];

        $TypeId = $myRow["TypeId"];

        $Estate = $myRow["Estate"];

        $OrderPO = $myRow["OrderPO"];

        $eCode = $myRow["eCode"];

        $scLine = $myRow["scLine"];

        $WorkShopId = $myRow["WorkShopId"];

        $ActionId = $myRow["ActionId"];

        $ProductId = $myRow["ProductId"];
        $Date = date("Y-m-d");

        $cmptNo = $eCode;
        $Operator = $user_id;
        $StockId = $scStockId;

        //领完料未下采购单的，自动下采购单
        $cgMid = $myRow["Mid"];
        switch ($ActionId) {//101 脱模入库 103 钢筋下料 104 骨架搭建 106 浇捣养护
            case 101:
                $reg = 1;
                $DateTime = date("Y-m-d H:i:s");
                $Date = date("Y-m-d");

                $OperationResult = "Y";


                $fromAction = 1 ;// ($ActionId == 101 ? 1 : 2);

                $DateTemp = date("Ymd");

                $Tempyear = date("Y");

                if ($fromAction == 1) {//成品登记页面
                    $Relation = $_POST['Relation'];
                    if ($Relation > 0) {
                        $inRelation = "REPLACE INTO sc1_newrelation (Id,POrderId,Relation,Date,Operator) VALUES 
        (NULL,'$POrderId','1','$DateTime','$Operator')";
                        $res = $this->conn->query($inRelation);
                        if(!$res){
                            throw new Exception("sc1_newrelation replace 失败。脱模入库登记失败");
                        }
                    }
                }

                //没传入则获取数量
                $query = "select Qty from yw1_scsheet where POrderId='$POrderId' AND sPOrderId = '$sPOrderId'";
                $row = $this->format($query);
                $Qty = $row[0]['Qty'];

                if ($fromAction == 1) {//成品登记页面
                    $checkSql = "SELECT YOS.ProductId,SC.* FROM sc1_cjtj SC LEFT JOIN yw1_scsheet YSS ON YSS.sPOrderId = SC.sPOrderId LEFT JOIN yw1_ordersheet YOS ON YOS.POrderId = YSS.POrderId WHERE YSS.ActionId = 101 AND YOS.ProductId =$ProductId";
                    if($this->format($checkSql)==null)
                    {
                        $inRecode = "INSERT INTO sc1_cjtj (Id,GroupId,POrderId,sPOrderId,StockId,Qty,Remark,Date,Estate,Locks,Leader) VALUES 
                        (NULL,'$Operator','$POrderId','$sPOrderId','$StockId','$Qty','','$Date','1','0','$Operator')";
                        $res = $this->conn->query($inRecode);
                                        if(!$res){
                                            $this->INSERTLOG("成品质检","成品扫码添加","脱模入库登记失败".$inRecode,"N");
                                           // throw new Exception("脱模入库登记失败");
                                        }
                    }else{
                        $this->INSERTLOG("成品质检","成品扫码添加","脱模入库已存在".$checkSql,"N");
                    }
                 
                }
                

                break;
            case 104:
                $this->gujiaConfirm($user_id,$sPOrderId,$mStockId,$POrderId);
                $inRecode="INSERT INTO sc1_cjtj (Id,GroupId,POrderId,sPOrderId,StockId,Qty,Remark,Date,Estate,Locks,Leader) VALUES
(NULL,'$Operator','$POrderId','$sPOrderId','$StockId','$Qty','','$Date','3','0','$Operator')";
                $res = $this->conn->query($inRecode);
                if(!$res){
                    throw new Exception("骨架搭建登记失败");
                }

                break;

            default:
                break;

        }
    }

    public function gujiaConfirm($userId,$sPOrderId,$mStockId,$POrderId)
    {
        $Operator = $userId;
        $query = "select Qty from yw1_scsheet where POrderId='$POrderId' AND sPOrderId = '$sPOrderId'";
        $row = $this->format($query);
        $shQty = $row[0]['Qty'];
        $Mid=0;
        $DateTime=date("Y-m-d H:i:s");
        $checkResult = $this->format("
	                     SELECT D.SendFloor,D.StuffId,S.CompanyId 
	                     FROM cg1_stocksheet S
           	             LEFT JOIN stuffdata D ON D.StuffId=S.StuffId
           	             WHERE S.StockId = $mStockId");
        $myCompanyId = $checkResult[0]["CompanyId"];
        $floor = $checkResult[0]["SendFloor"];
        $StuffId = $checkResult[0]["StuffId"];

        $DateTemp = date("Ymd");
        $Tempyear= date("Y");
        $maxBillResult =$this->format("SELECT MAX(BillNumber) AS BillNumber FROM gys_shmain WHERE BillNumber  LIKE '$DateTemp%'");
        $TempBillNumber=$maxBillResult[0]["BillNumber"];
        if($TempBillNumber){

            $TempBillNumber=$TempBillNumber+1;
        }
        else{
            $TempBillNumber=$DateTemp."0001";//默认
        }

        $maxGysResult = $this->format("SELECT MAX(GysNumber) AS GysNumber FROM gys_shmain WHERE GysNumber  LIKE '$Tempyear%' AND CompanyId = '$myCompanyId'");
        $tempGysNumber=$maxGysResult[0]["GysNumber"];
        if($tempGysNumber){

            $tempGysNumber=$tempGysNumber+1;
        }
        else{
            $tempGysNumber=$Tempyear."00001";//默认
        }


        if($Mid==0){//如果没生成主送货单就先生成主送货单
            $inRecode="INSERT INTO gys_shmain 
		      (Id,BillNumber,GysNumber,CompanyId,Locks,Date,Remark,Floor,Operator,creator,created) 
		      VALUES (NULL,'$TempBillNumber','$tempGysNumber','$myCompanyId','1','$DateTime','半成品入库','$floor','$Operator','$Operator',NOW())";
            $this->conn->query($inRecode);
            $Mid=$this->conn->insert_id;
        }

        if($Mid>0){
            $addRecodes="INSERT INTO gys_shsheet (Id,Mid,sPOrderId,StockId,StuffId,Qty,SendSign,Estate,Locks,Operator,creator,created) 
			    VALUES (NULL,'$Mid','$sPOrderId','$mStockId','$StuffId','$shQty','0','2','1','$Operator','$Operator',NOW())";
            $addAction=$this->conn->query($addRecodes);
            if($addAction){
                $saveSign = 1;
                $updatesql="update yw1_scsheet set Estate = '0' where sPOrderId='$sPOrderId' ";
                $this->conn->query($updatesql);
            }
        }
    }


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

    public function ClearDBResult()
    {
        $this->conn->close();
        $this->__construct();
    }

    public function GetMsg($cName)
    {
        $ret="";
        $inform="";

        if ($cName) {
            //排产派单
            $pSql = "SELECT
	P.cName,
	WD.`Name` AS '产线',
	YS.scDate AS '计划日期',
	getCanStock ( YS.sPOrderId, 1 ) AS '排产状态',
	(
	SELECT
		SUM( CL.Estate )
	FROM
		ck5_llsheet CL
	WHERE
		CL.sPOrderId = YS.sPOrderId
	) AS '领料状态',
	YS.FinishDate AS '浇捣日期',
IF
	(
	IF
		( G.Estate = 2, 2, G.Estate ) = 0,
		'已质检',
	IF
		( G.Estate = 2, '未质检', G.Estate ) 
	) AS '半成品质检',
	(
	SELECT
		YSS.FinishDate 
	FROM
		yw1_ordersheet YT
		LEFT JOIN yw1_scsheet YSS ON YSS.POrderId = YT.POrderId 
	WHERE
		YSS.ActionId = 101 
		AND YT.ProductId = P.ProductId 
	) AS '脱模日期',
	(
	SELECT
		SC.Estate
	FROM
		sc1_cjtj SC
		LEFT JOIN yw1_scsheet YSS ON YSS.sPOrderId = SC.sPOrderId 
		LEFT JOIN yw1_ordersheet YOS ON YOS.POrderId = YSS.POrderId
	WHERE
		YSS.ActionId = 101 
		AND YOS.ProductId = P.ProductId 
	) AS '成品质检',
	YO.PutawayDate AS '入库日期',
	YO.StorageNO AS '入库单号',
	CSM.Date AS '出库日期',
	( SELECT `Name` FROM staffmain WHERE Number = CSM.Operator ) AS '出库操作人',
	CSM.InvoiceNO AS '出库单号',
	CSM.Estate  AS '出库' 
FROM
	yw1_ordersheet YO
	LEFT JOIN yw1_scsheet YS ON YS.POrderId = YO.POrderId
	LEFT JOIN workshopdata WD ON WD.Id = YS.WorkShopId
	LEFT JOIN productdata P ON P.ProductId = YO.ProductId
	LEFT JOIN producttype PT ON PT.TypeId = P.TypeId
	LEFT JOIN trade_object T ON T.CompanyId = P.CompanyId
	LEFT JOIN ch1_shipsplit CSL ON CSL.POrderId = YO.POrderId
	LEFT JOIN ch1_shipsheet CSS ON CSS.Id = CSL.ShipId
	LEFT JOIN ch1_shipmain CSM ON CSM.Id = CSS.Mid
	LEFT JOIN companyinfo C ON C.CompanyId = P.CompanyId
	LEFT JOIN trade_drawing TD ON TD.Id = P.drawingId
	LEFT JOIN gys_shsheet G ON G.sPOrderId = YS.sPOrderId
	LEFT JOIN sc1_cjtj SC ON SC.sPOrderId = YS.sPOrderId 
WHERE
	YS.ActionId = 104 
	AND P.cName = '$cName' ";
            $pRes = $this->format($pSql);

            if ($pRes!=null) {
                $pRow = $pRes[0];
                if ($pRow['计划日期'] && $pRow['排产状态']!=1) {
                    if ($pRow['领料状态'] == 0 ) {
                        if ($pRow['浇捣日期']) {
                            if ($pRow['半成品质检'] == '已质检') {
                                if ($pRow['脱模日期']) {
                                    if ($pRow['成品质检'] == 0 || $pRow['成品质检'] == 2) {
                                        if ($pRow['入库日期'] || $pRow['入库单号']) {
                                            if ($pRow['出库日期'] || $pRow['出库单号'] ) {
                                                $ret = "构件已出库";
                                                $inform = "如有疑问，请联系资材部门（物流）";
                                            } else {
                                                $ret = "构件已入库";
                                                $inform = "如有疑问，请联系资材部门（物流）";
                                            }
                                        } else {
                                            $ret = "成品入库未完成";
                                            $inform = "请联系资材部门（仓库），成品入库未完成";
                                        }
                                    } else {
                                        if ($pRow['成品质检'] == 3) {
                                            $ret = "成品质检不合格";
                                            $inform = "请联系品质部门（质检），成品质检扫码";
                                        } elseif ($pRow['成品质检'] == 1) {
                                            $ret = "成品质检未完成";
                                            $inform = "请联系品质部门（质检），成品质检扫码";
                                        }
                                    }
                                } else {
                                    $ret = "脱模生产未完成";
                                    $inform = "请联系相关生产线，生产登记扫码";
                                }
                            } else {
                                $ret = "半成品质检未完成";
                                $inform = "请联系品质部门（质检），半成品质检扫码";
                            }
                        } else {
                            $ret = "浇捣生产未完成";
                            $inform = "请联系相关生产线，生产登记扫码";
                        }
                    } else {
                        $ret = "领料未完成";
                        $inform = "请联系资材部门（仓库），领料未完成";
                    }
                } else {
                    $ret = "排产派单未完成";
                    $inform = "请联系资材部门（计划），生产派单未保存";
                }
            } else {
                $ret = "构件不存在";
                $inform = "如有疑问，请联系信息部人员<br/>电话：15919701518";
            }
        }
        else
        {
            $ret=  "获取信息失败";
           $inform= "如有疑问，请联系信息部人员</br>电话：15919701518";
        }
        $msg = $ret.$inform;
        return $msg;
    }
//endregion
}
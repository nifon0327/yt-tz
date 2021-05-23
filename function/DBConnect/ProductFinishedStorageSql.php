<?php
/**
 * Created by PhpStorm.
 * User: IceFire
 * Date: 2018/11/24
 * Time: 22:17
 */
include "./Config/DbConnect.php";

class ProductFinishedStorageSql
{
    public $db;

    public $toOutSql = "SELECT
                    P.ProductId
                    FROM
                     ch1_shipsplit SP
                     INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
                     LEFT JOIN productdata P ON P.ProductId = S.ProductId
                     LEFT JOIN productstock K ON K.ProductId = P.ProductId
                     LEFT JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
                     LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
                    WHERE
                     S.Estate > 0 
                     AND SP.Estate = '1' 
                     AND K.tStockQty >= SP.Qty
										 UNION
                    SELECT
                        P.ProductId
                    FROM
                        ch1_shipsheet S
                        LEFT JOIN ch1_shipmain M ON M.Id = S.Mid
                        LEFT JOIN yw1_ordersheet YS ON YS.POrderId = S.POrderId
                        LEFT JOIN productdata P ON P.ProductId = S.ProductId
                    WHERE
                        1 
                        AND S.Type = '1' 
                    AND M.Estate = '0' ";

    function __construct()
    {
        $this->db = new DbConnect();
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
     * 获取成品构件列表
     * @param $tradeId
     * @param $workshopId
     * @return array|null
     */
    public function searchFinishedProducts($tradeId, $workshopId)
    {
            $sql = "SELECT
        O.Forshort,
        Y.POrderId,
        P.ProductId,
        P.cName,
        S.Id cjtjId
    FROM
        sc1_cjtj S
        INNER JOIN cg1_stocksheet G ON G.StockId = S.StockId
        INNER JOIN yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
        INNER JOIN workshopdata W ON W.Id = SC.WorkShopId
        INNER JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
        INNER JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
        LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
        INNER JOIN productdata P ON P.ProductId = Y.ProductId
        INNER JOIN productunit U ON U.Id = P.Unit
        LEFT JOIN yw3_pileadtime PI ON PI.POrderId = Y.POrderId 
    WHERE
        S.Estate <> 0 
        AND S.Estate = '2' 
        AND G.LEVEL = 1 
        AND O.id = $tradeId
        AND W.Id = $workshopId
        AND P.ProductId
        AND (Y.StackId IS  NULL OR Y.StackId = '')
        AND P.ProductId not in ( ".$this->toOutSql." )
    GROUP BY
        S.StockId";
        return $this->db->format($sql);
    }

    /**
     * 扫码添加
     * @param $cname
     * @param $stackId
     * @param $openId
     * @return bool
     * @throws Exception
     */
    public function addFinishedProductByProductName($cname,$stackId,$openId){
        //查询操作人
        $checkSql = "select B.Number from  usertable UT 
                LEFT JOIN staffmain B ON B.Number = UT.Number where UT.openid = '$openId' limit 1";
        $res = $this->db->format($checkSql);
        if ($res == null)
            throw new Exception("未查找到操作人员！");
        $number = $res[0]['Number'];

        //判断该构件是否是待入库
        $querySql = "SELECT
                P.ProductId,
                P.cName,
                y.StackId,y.SeatId
            FROM
                sc1_cjtj S
                INNER JOIN cg1_stocksheet G ON G.StockId = S.StockId
                INNER JOIN yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
                INNER JOIN workshopdata W ON W.Id = SC.WorkShopId
                INNER JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
                INNER JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
                LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
                INNER JOIN productdata P ON P.ProductId = Y.ProductId
                INNER JOIN productunit U ON U.Id = P.Unit
                LEFT JOIN yw3_pileadtime PI ON PI.POrderId = Y.POrderId 
            WHERE
                S.Estate <> 0 
                AND (Y.StackId IS  NULL OR Y.StackId = '')
                AND S.Estate = '2' 
                AND G.LEVEL = 1 
            AND cName='$cname'
            GROUP BY
                S.StockId";
        $querySqlRes = $this->db->format($querySql);

        if($querySqlRes == null){
            //如果是空的，则强制插入
            //查询是否为待排产构件
            $isPaichanSql = "SELECT COUNT(PD.TypeId) ProNum ,PD.FloorNo,PD.TypeId,0 STATUS FROM
                        (SELECT DISTINCT P.ProductId, P.TypeId ,P.FloorNo
                        FROM (select productId,TypeId,FloorNo from productdata
                        where productdata.cName='$cname' 
                        ) 
                        P JOIN
                        yw1_ordersheet YO ON YO.ProductId = P.ProductId 
                        JOIN yw1_scsheet YS ON YS.POrderId = YO.POrderId
                        WHERE 1 AND YS.Scdate is NULL) PD WHERE PD.TypeId is not NULL
                        GROUP BY  PD.TypeId,PD.FloorNo";

            $isPaichanSqlRes = $this->db->format($isPaichanSql);

            if($isPaichanSqlRes!=null){
                throw new Exception("当前构件处于待排产状态，无法添加");
            }

            //判断是否为已领料
            $sql = "SELECT  L.POrderId,L.sPOrderId,L.creator,L.created,L.Estate
                FROM ck5_llsheet L 
                INNER JOIN yw1_scsheet SC ON SC.sPOrderId = L.sPOrderId
                LEFT  JOIN staffmain STM  ON STM.Number = L.creator
                LEFT JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
                LEFT JOIN yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
                LEFT JOIN productdata P ON P.ProductId=Y.ProductId
                LEFT JOIN trade_object O ON O.CompanyId = OM.CompanyId
                WHERE  1 
                AND P.cName = '$cname'
                GROUP BY SC.sPOrderId ";
            $isLingLiaoRes = $this->db->format($sql);

            if($isLingLiaoRes!=null){
                $Estate = $isLingLiaoRes[0]['Estate'];
                if($Estate != 0){
                    //未领料
                    throw new Exception("当前构件未料数确认，无法添加");
                }
            }else{
                throw new Exception("当前构件未查询到领料数据，无法添加");
            }

            $ssql = "SELECT PD.cName,PD.FloorNo,PD.TypeId,5 STATUS,PD.Operator,PD.Date FROM
            (
            SELECT P.cName,P.FloorNo,P.TypeId,5 STATUS,SP.Operator,SP.Date
            FROM
                        ch1_shipsplit SP
                        INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
                        LEFT JOIN yw3_pisheet PI ON PI.oId = S.Id
                        LEFT JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
                        LEFT JOIN yw7_clientOutData O ON O.Mid = SP.id
                        LEFT JOIN yw7_clientOutData OP ON OP.POrderId = S.POrderId
                        AND OP.Sign = 1
                        LEFT JOIN yw7_clientOrderPo R ON R.Mid = SP.id
                        LEFT JOIN productdata P ON P.ProductId = S.ProductId
                        LEFT JOIN ch1_shipsheet SS ON P.ProductId = SS.ProductId
                        LEFT JOIN ch1_shipmain CS ON CS.Id = SS.Mid
                        LEFT JOIN productstock K ON K.ProductId = P.ProductId
                        LEFT JOIN taxtype X ON X.Id = S.taxtypeId
                       LEFT JOIN trade_drawing TG ON CONCAT_WS( '-', TG.BuildingNo, TG.FloorNo, TG.CmptNo, TG.SN ) = P.cName
            WHERE
                        S.Estate > 0
                        AND SP.Estate = '1'
                        AND P.cName='$cname'
                        AND K.tStockQty >= SP.Qty UNION ALL
            SELECT
                        S.SampName AS cName,
                        '' AS FloorNo,
                        '' AS TypeId,
                        5 STATUS,
                        '' AS Operator,
                        '' ASDate        
            FROM
                        ch5_sampsheet S
            WHERE
                        1
                        AND S.Estate = '1'
            )  PD WHERE PD.TypeId is not NULL
GROUP BY  PD.TypeId,PD.FloorNo,PD.cName;";
            if($this->db->format($ssql)!=null)
                throw new Exception("该构件当前已入库，不符合入垛条件！");
            $ssql = "	SELECT
                    S.POrderId,P.ProductId,
                        P.cName,
                        O.ForShort,
                        3 `Status`
                    FROM
                      yw1_ordersheet S
                     LEFT JOIN productdata P ON P.ProductId = S.ProductId
                     LEFT JOIN productstock K ON K.ProductId = P.ProductId
                     LEFT JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
                     LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
                        WHERE S.scflag = 0 AND S.StackId = 'LB1234'

                        AND P.cName='$cname' ";
            if($this->db->format($ssql)!=null)
                throw new Exception("该异常构件已添加，请勿重复添加！");

            $ssql ="SELECT PD.cName,PD.FloorNo,PD.TypeId,6 STATUS,PD.Operator,PD.Date FROM
            (
            SELECT P.cName,P.FloorNo,P.TypeId,5 STATUS,M.Operator,M.Date        FROM
            ch1_shipsheet S
            LEFT JOIN ch1_shipmain M ON M.Id = S.Mid
            LEFT JOIN yw1_ordersheet YS ON YS.POrderId = S.POrderId
            LEFT JOIN yw1_ordermain YM ON YM.OrderNumber = YS.OrderNumber
            LEFT JOIN productdata P ON P.ProductId = S.ProductId
            LEFT JOIN taxtype BG ON BG.Id = P.taxtypeId
            LEFT JOIN productunit U ON U.Id = P.Unit
            LEFT JOIN yw3_pisheet E ON E.oId = YS.Id
            LEFT JOIN ch1_shipsplit L ON L.ShipId = S.Id WHERE
            1
            AND S.Type = '1'
            AND M.Estate = '0'
            AND P.cName='$cname'
              ORDER BY
            M.Date DESC,
            M.CompanyId
            )  PD WHERE PD.TypeId is not NULL
            GROUP BY  PD.TypeId,PD.FloorNo,PD.cName;";
            if($this->db->format($ssql)!=null)
                throw new Exception("该构件当前已出库，不符合入垛条件！");

            //判断该构件是否是待入库
            $querySql = "SELECT
                P.ProductId,
                P.cName,
                y.StackId,y.SeatId
            FROM
                sc1_cjtj S
                INNER JOIN cg1_stocksheet G ON G.StockId = S.StockId
                INNER JOIN yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
                INNER JOIN workshopdata W ON W.Id = SC.WorkShopId
                INNER JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
                INNER JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
                LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
                INNER JOIN productdata P ON P.ProductId = Y.ProductId
                INNER JOIN productunit U ON U.Id = P.Unit
                LEFT JOIN yw3_pileadtime PI ON PI.POrderId = Y.POrderId 
            WHERE
                S.Estate <> 0 
                AND (Y.StackId IS  NULL OR Y.StackId = '')
                AND S.Estate = '2' 
                AND G.LEVEL = 1 
            AND cName='$cname'
            GROUP BY
                S.StockId";

            $querySqlRes = $this->db->format($querySql);
            if($querySqlRes == null){
                $sql = "SELECT  
                        Y.POrderId,
                        Y.OrderPO,
                        P.ProductId,
                        P.cName 
                    FROM ck5_llsheet SC
                        LEFT JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
                        LEFT JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
                        LEFT JOIN productdata P ON P.ProductId = Y.ProductId
                    WHERE 1
                        AND P.cName = '$cname'  and SC.Receiver <>'0'
GROUP BY Y.POrderId";
                $res = $this->db->format($sql);
                if ($res == null) {
                    throw new Exception("数据获取失败，当前SQL---" . $sql);
                }
                $POrderId = $res[0]['POrderId'];
                $ProductId = $res[0]['ProductId'];
                $storageTime = date("Y-m-d H:i:s");
                $queryTmpEstateSql = "SELECT Estate  from ch1_shipsplit WHERE POrderId = '$POrderId' ";
                $queryTmpEstateSqlRes = $this->db->format($queryTmpEstateSql);
                if ($queryTmpEstateSqlRes == null) {
                    //ch1_shipsplit数据获取失败
                    $insertSql = "INSERT INTO ch1_shipsplit (POrderID,ShipId,Qty,ShipType,Estate,OrderSign,shipSign,Locks,PLocks,Date,creator,created)
									VALUES	('$ProductId',0,1,'7',0,0,1,0,0,NOW(),$number,NOW())";
                    $this->db->conn->query($insertSql);

                    $query = "SELECT * FROM productstock WHERE POrderId= '$POrderId'";
                    $queryRes= $this->db->format($query);
                    if($queryRes == null){
                        //productstock未查找到数据 则新增
                        $insertSql = "INSERT INTO productstock (ProductId,tStockQty,oStockQty,Estate,Locks,Date,Operator,PLocks)
									VALUES ('$POrderId',1,0,1,0,NOW(),$number,0)";
                        $this->db->conn->query($insertSql);
                    }

                    //将状态置位待入库
                    $sql = "SELECT
                            P.ProductId,
                            P.cName,
                            y.StackId,y.SeatId,S.Id
                        FROM
                            sc1_cjtj S
                            INNER JOIN cg1_stocksheet G ON G.StockId = S.StockId
                            INNER JOIN yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
                            INNER JOIN workshopdata W ON W.Id = SC.WorkShopId
                            INNER JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
                            INNER JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
                            LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
                            INNER JOIN productdata P ON P.ProductId = Y.ProductId
                            INNER JOIN productunit U ON U.Id = P.Unit
                            LEFT JOIN yw3_pileadtime PI ON PI.POrderId = Y.POrderId 
                        WHERE 1
                            AND G.LEVEL = 1 
                        AND cName='$cname'
                        GROUP BY
                            S.StockId";
                    $queryRes = $this->db->format($sql);
                    if($queryRes != null){
                        $cjtjId = $queryRes[0]['Id'];
                        $updateSqlCjtj = "UPDATE sc1_cjtj set Estate = 2 WHERE Id = $cjtjId";
                        $this->db->conn->query($updateSqlCjtj);
                    }
                }

                $updateSql = "UPDATE yw1_ordersheet SET StackId='$stackId',scflag = 0
                          WHERE ProductId IN(SELECT ProductId FROM productdata WHERE cName='$cname' )";
            }else{
                $updateSql = "UPDATE yw1_ordersheet SET StackId='$stackId'
                          WHERE ProductId IN(SELECT ProductId FROM productdata WHERE cName='$cname' )";
            }
        }else{
            $updateSql = "UPDATE yw1_ordersheet SET StackId='$stackId'
                          WHERE ProductId IN(SELECT ProductId FROM productdata WHERE cName='$cname' )";
        }



        if(!$this->db->conn->query($updateSql)){
            return false;
        }
        return true;
    }

    /**
     * 根据垛号查询数据
     * @param $stackNo
     * @param $openId
     * @return array|null
     * @throws Exception
     */
    public function getListByStackNo($stackId,$openId){
        //首先查出目标垛号所属库位信息
        $sql = "SELECT
                    S.ProductId,S.SeatId,S.StackId
                    FROM
                     ch1_shipsplit SP
                     INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
                     LEFT JOIN productdata P ON P.ProductId = S.ProductId
                     LEFT JOIN productstock K ON K.ProductId = P.ProductId
                    WHERE
                     S.Estate > 0
                     AND SP.Estate = '1'
                     AND K.tStockQty >= SP.Qty
                     AND StackId = '$stackId'
										 GROUP BY SeatId
              ";
        $sqlRes = $this->db->format($sql);
        $seatId = '';
        if($sqlRes == null){
            $seatId = '';
        }else{
            if(count($sqlRes)>1){
                $num = 0;
                foreach($sqlRes as $item){
                    if($item['SeatId']=='' || $item['SeatId']==null){

                    }else{
                        $num++;
                        $seatId = $item['SeatId'];
                    }
                }
//                if($num>1){
//                    throw new Exception("数据库错误，该垛对应多个库位");
//                }
            }else{
                $seatId = $sqlRes[0]['SeatId'];
            }
        }

        $querySql = "SELECT
	Y.POrderId,
	P.ProductId,
	P.cName,
	O.ForShort,
	0 `Status`
FROM
	sc1_cjtj S
INNER JOIN cg1_stocksheet G ON G.StockId = S.StockId
INNER JOIN yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
INNER JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
INNER JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
INNER JOIN productdata P ON P.ProductId = Y.ProductId
LEFT JOIN (
	SELECT
		P.ProductId
	FROM
		ch1_shipsplit SP
	INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
	LEFT JOIN productdata P ON P.ProductId = S.ProductId
	LEFT JOIN productstock K ON K.ProductId = P.ProductId
	LEFT JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
	LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
	WHERE
		S.Estate > 0
	AND SP.Estate = '1'
	AND K.tStockQty >= SP.Qty
	UNION
		SELECT
			P.ProductId
		FROM
			ch1_shipsheet S
		LEFT JOIN ch1_shipmain M ON M.Id = S.Mid
		LEFT JOIN yw1_ordersheet YS ON YS.POrderId = S.POrderId
		LEFT JOIN productdata P ON P.ProductId = S.ProductId
		WHERE
			1
		AND S.Type = '1'
		AND M.Estate = '0'
) T ON P.ProductId=T.ProductId
WHERE
	S.Estate <> 0
AND S.Estate = '2'
AND G. LEVEL = 1
AND Y.StackId = '$stackId'
AND T.ProductId is NULL
GROUP BY
	S.StockId

UNION

	SELECT
		S.POrderId,
		P.ProductId,
		P.cName,
		O.ForShort,
		1 `Status`
	FROM
		ch1_shipsplit SP
	INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
	LEFT JOIN productdata P ON P.ProductId = S.ProductId
	LEFT JOIN productstock K ON K.ProductId = P.ProductId
	LEFT JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
	LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
	WHERE
		S.Estate > 0
	AND SP.Estate = '1'
	AND K.tStockQty >= SP.Qty
	AND S.StackId = '$stackId'

	UNION

		SELECT
			S.POrderId,
			P.ProductId,
			P.cName,
			O.ForShort,
			3 `Status`
		FROM
			yw1_ordersheet S
		LEFT JOIN productdata P ON P.ProductId = S.ProductId
		LEFT JOIN productstock K ON K.ProductId = P.ProductId
		LEFT JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
		LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
		WHERE
			S.scflag = 0
		AND S.StackId = '$stackId'
		AND P.ProductId NOT IN (
			SELECT
				P.ProductId
			FROM
				
	sc1_cjtj S
INNER JOIN cg1_stocksheet G ON G.StockId = S.StockId
INNER JOIN yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
INNER JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
INNER JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
INNER JOIN productdata P ON P.ProductId = Y.ProductId
LEFT JOIN (
	SELECT
		P.ProductId
	FROM
		ch1_shipsplit SP
	INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
	LEFT JOIN productdata P ON P.ProductId = S.ProductId
	LEFT JOIN productstock K ON K.ProductId = P.ProductId
	LEFT JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
	LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
	WHERE
		S.Estate > 0
	AND SP.Estate = '1'
	AND K.tStockQty >= SP.Qty
	UNION
		SELECT
			P.ProductId
		FROM
			ch1_shipsheet S
		LEFT JOIN ch1_shipmain M ON M.Id = S.Mid
		LEFT JOIN yw1_ordersheet YS ON YS.POrderId = S.POrderId
		LEFT JOIN productdata P ON P.ProductId = S.ProductId
		WHERE
			1
		AND S.Type = '1'
		AND M.Estate = '0'
) T ON P.ProductId=T.ProductId
WHERE
	S.Estate <> 0
AND S.Estate = '2'
AND G. LEVEL = 1
AND Y.StackId = '$stackId'
AND T.ProductId is NULL
GROUP BY
	S.StockId
			UNION
				SELECT
					P.ProductId
				FROM
					ch1_shipsplit SP
				INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
				LEFT JOIN productdata P ON P.ProductId = S.ProductId
				LEFT JOIN productstock K ON K.ProductId = P.ProductId
				LEFT JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
				LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
				WHERE
					S.Estate > 0
				AND SP.Estate = '1'
				AND K.tStockQty >= SP.Qty
				AND S.StackId = '$stackId'
		)";
//        $querySql = "SELECT
//                        Y.POrderId,
//                        P.ProductId,
//                        P.cName,
//                        O.ForShort,
//                        0 `Status`
//                    FROM
//                        sc1_cjtj S
//                        INNER JOIN cg1_stocksheet G ON G.StockId = S.StockId
//                        INNER JOIN yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
//                        INNER JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
//                        INNER JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
//                        LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
//                        INNER JOIN productdata P ON P.ProductId = Y.ProductId
//                    WHERE
//                        S.Estate <> 0
//                        AND S.Estate = '2'
//                        AND G.LEVEL = 1
//                        AND Y.StackId = '$stackId'
//                        AND P.ProductId not in ( ".$this->toOutSql." )
//                    GROUP BY
//                        S.StockId
//                        UNION
//                        SELECT
//                    S.POrderId,P.ProductId,
//                        P.cName,
//                        O.ForShort,
//                        1 `Status`
//                    FROM
//                     ch1_shipsplit SP
//                     INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
//                     LEFT JOIN productdata P ON P.ProductId = S.ProductId
//                     LEFT JOIN productstock K ON K.ProductId = P.ProductId
//                     LEFT JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
//                     LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
//                    WHERE
//                     S.Estate > 0
//                     AND SP.Estate = '1'
//                     AND K.tStockQty >= SP.Qty
//                     AND S.StackId = '$stackId' ";
        $querySqlRes = $this->db->format($querySql);
        $returnArr = array(
            "stackInfo"=>array(
                "StackNo"=>$stackId,
                "SeatId"=>$seatId
            ),
            "list"=>$querySqlRes
        );
        return $returnArr;
    }


    /**
     * 删除构件
     * @param $inventoryDataIds
     * @return bool
     * @throws Exception
     */
    public function deleteProductByIds($inventoryDataIds){
        foreach ($inventoryDataIds as $item) {
            $productId = $item["productId"];
            $updateSql ="UPDATE yw1_ordersheet set StackId = NULL WHERE ProductId = $productId";
            if(!$this->db->conn->query($updateSql)){
                return false;
            }
        }
       return true;
    }

    /**
     * 添加构件到垛
     * @param $products
     * @param $stackId
     * @param $openId
     * @return bool
     * @throws Exception
     */
    public function addFinishedProducts($products, $stackId, $openId)
    {

        //首先查出目标垛号所属库位信息
//        $sql = "SELECT
//                    S.ProductId,S.SeatId,S.StackId
//                    FROM
//                     ch1_shipsplit SP
//                     INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
//                     LEFT JOIN productdata P ON P.ProductId = S.ProductId
//                     LEFT JOIN productstock K ON K.ProductId = P.ProductId
//                    WHERE
//                     S.Estate > 0
//                     AND SP.Estate = '1'
//                     AND K.tStockQty >= SP.Qty
//                     AND StackId = '$stackId'
//              ";
//        $sqlRes = $this->db->format($sql);
//        if($sqlRes == null){
//            $seatId = '';
//        }else{
//            $seatId = $sqlRes[0]['SeatId'];
//        }


        foreach ($products as $item) {
            //对应 productdata  的 productid字段
            $productId = $item["productId"];

            //校验该构件是否为待入库的构件

            $sql = "SELECT
                        Y.POrderId,
                        Y.OrderPO,
                        S.StockId,
                        P.ProductId,
                        P.cName,
                        P.eCode,
                        P.TestStandard,
                        P.pRemark,
                        S.Estate,
                        S.Date
                    FROM
                        sc1_cjtj S
                        INNER JOIN cg1_stocksheet G ON G.StockId = S.StockId
                        INNER JOIN yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
                        INNER JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
                        INNER JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
                        INNER JOIN productdata P ON P.ProductId = Y.ProductId
                    WHERE
                        S.Estate <> 0 
                        AND S.Estate = '2' 
                        AND G.LEVEL = 1 
                        AND P.ProductId = $productId
                    GROUP BY
                        S.StockId";
            $res = $this->db->format($sql);
            if ($res == null) {
                throw new Exception("该构件非待入库构件---" . $productId);
            }

            $updateSql ="UPDATE yw1_ordersheet set StackId = '$stackId' WHERE ProductId = $productId";
            if(!$this->db->conn->query($updateSql)){
                return false;
            }
        }
        return true;
    }

    /**
     * 获取库位列表
     * @return array|null
     */
    public function getSeats()
    {
        $sql = "select SeatId FROM wms_seat where 	WareHouseId = 1";
        return $this->db->format($sql);

    }


    /**
     * 入库确认
     * @param $products
     * @return bool
     * @throws Exception
     */
    public function storageInConfirm($products,$openId)
    {
        //查询操作人
        $checkSql = "select B.Number from  usertable UT
                LEFT JOIN staffmain B ON B.Number = UT.Number where UT.openid = '$openId' limit 1";
        $res = $this->db->format($checkSql);
        if ($res == null)
            throw new Exception("未查找到操作人员！");
        $number = $res[0]['Number'];
        foreach ($products as $item) {
            $productId = $item["productId"];
            $storageNO = $item["storageNO"];//入库单号
            $SeatId = $item['SeatId'];

            if($SeatId==''|| $SeatId == null){
                throw new Exception("SeatId不能为空---");
            }
//            $duoNO = $item['StackNo'];
//注释代码以后会用

//            $sql = "SELECT
//                        Y.POrderId,
//                        Y.OrderPO,
//                        S.StockId,
//                        P.ProductId,
//                        P.cName,
//                        P.eCode,
//                        P.TestStandard,
//                        P.pRemark,
//                        S.Estate,
//                        S.Date
//                    FROM
//                        sc1_cjtj S
//                        INNER JOIN cg1_stocksheet G ON G.StockId = S.StockId
//                        INNER JOIN yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
//                        INNER JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
//                        INNER JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
//                        INNER JOIN productdata P ON P.ProductId = Y.ProductId
//                    WHERE
//                        S.Estate <> 0
//                        AND S.Estate = '2'
//                        AND G.LEVEL = 1
//                        AND P.ProductId = $productId
//                    GROUP BY
//                        S.StockId";
            $sql = "SELECT
                        Y.POrderId,
                        Y.OrderPO,
                        S.StockId,
                        P.ProductId,
                        P.cName,
                        P.eCode,
                        P.TestStandard,
                        P.pRemark,
                        S.Estate,
                        S.Date
                    FROM
                        sc1_cjtj S
                        INNER JOIN cg1_stocksheet G ON G.StockId = S.StockId
                        INNER JOIN yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
                        INNER JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
                        INNER JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
                        INNER JOIN productdata P ON P.ProductId = Y.ProductId
                    WHERE 1
                        AND (P.ProductId =  '$productId' OR  P.cName='$productId')
                    GROUP BY
                        S.StockId";
            $res = $this->db->format($sql);
            if ($res == null) {
                $sql = "SELECT  
                        Y.POrderId,
                        Y.OrderPO,
                        P.ProductId,
                        P.cName 
                    FROM ck5_llsheet SC
                        LEFT JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
                        LEFT JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
                        LEFT JOIN productdata P ON P.ProductId = Y.ProductId
                    WHERE 1
                        AND P.ProductId = '$productId' 
                        and SC.Receiver <>'0'
                        GROUP BY Y.POrderId;";
                $resLl = $this->db->format($sql);
                if ($resLl == null) {
                    $this->INSERTLOG("批量入库","入库确认","数据获取失败，ck5_llsheet未获取到数据".$productId.  "当前SQL---" . $sql,'N');
                    throw new Exception("数据获取失败，ck5_llsheet未获取到数据".$productId.  "当前SQL---" . $sql);
                }else{
                    $POrderId = $resLl[0]['POrderId'];
                    $ProductId = $resLl[0]['ProductId'];
                    $StockId = -1;
                }
            }
            if($res!=null){
                $POrderId = $res[0]['POrderId'];
                $StockId = $res[0]['StockId'];
                $chooseDate = $res[0]['Date'];
            }

            $storageTime = date("Y-m-d H:i:s");
//            $UpdateSql = "UPDATE sc1_cjtj SET Estate='0' WHERE POrderId='$POrderId' AND StockId = '$StockId' AND Estate =2 AND DATE_FORMAT(Date,'%Y-%m-%d') = '$chooseDate'";
            $UpdateSql = "UPDATE sc1_cjtj SET Estate='0' WHERE POrderId='$POrderId' AND StockId = '$StockId' ";
            $UpdateResult = $this->db->conn->query($UpdateSql);
            if ($UpdateResult) {
                $PutawayDate = date("Y-m-d H:i:s");
//                $UpdateSeatId = $this->db->conn->query("update yw1_ordersheet set StorageNO = '$storageNO',SeatId='$SeatId', PutawayDate = '$PutawayDate' WHERE POrderId='$POrderId' AND Estate > 0");
                $UpdateSeatId = $this->db->conn->query("update yw1_ordersheet set StorageNO = '$storageNO',SeatId='$SeatId', PutawayDate = '$PutawayDate' WHERE POrderId='$POrderId' ");
                if (!$UpdateSeatId) {
                    $this->INSERTLOG("批量入库","入库确认","编号为" . $productId . "   yw1_ordersheet数据更新失败"."当前SQL---" . "update yw1_ordersheet set StorageNO = '$storageNO',SeatId='$SeatId', PutawayDate = '$PutawayDate' WHERE POrderId='$POrderId' ",'N');
                    throw new Exception("编号为" . $productId . "   yw1_ordersheet数据更新失败");
                }
                $queryTmpEstateSql = "SELECT Estate  from ch1_shipsplit WHERE POrderId = '$POrderId' ";
                $queryTmpEstateSqlRes = $this->db->format($queryTmpEstateSql);
                if ($queryTmpEstateSqlRes == null) {
//                    throw new Exception("编号为" . $productId . "  ch1_shipsplit数据获取失败");
                    //ch1_shipsplit数据获取失败
                    $insertSql = "INSERT INTO ch1_shipsplit (POrderID,ShipId,Qty,ShipType,Estate,OrderSign,shipSign,Locks,PLocks,Date,creator,created)
									VALUES	('$POrderId',0,1,'7',0,0,1,0,0,NOW(),$number,NOW())";
                    $this->db->conn->query($insertSql);

                    $query = "SELECT * FROM productstock WHERE ProductId= '$productId'";
                    $queryRes= $this->db->format($query);
                    if($queryRes == null){
                        //productstock未查找到数据 则新增
                        $insertSql = "INSERT INTO productstock (ProductId,tStockQty,oStockQty,Estate,Locks,Date,Operator,PLocks)
									VALUES ('$productId',1,0,1,0,NOW(),$number,0)";
                        $this->db->conn->query($insertSql);
                    }

                }

                $UpdateCH = $this->db->conn->query("update ch1_shipsplit set Estate= 1,storageOperator = $number,storageTime = '$storageTime' WHERE POrderId='$POrderId' ");
//                $UpdateCH = $this->db->conn->query("update ch1_shipsplit set Estate= 1,storageOperator = $number,storageTime = '$storageTime' WHERE POrderId='$POrderId' AND Estate > 0");
                if (!$UpdateCH) {
                    throw new Exception("编号为" . $productId . "  ch1_shipsplit数据更新失败");
                }

            } else {
                throw new Exception("入库确认失败");
            }
        }
        return true;
    }

    /**
     * 移库
     * @param $stackId
     * @param $seatId
     * @return bool
     * @throws Exception
     */
    public function moveSeat($stackId, $seatId)
    {
        //首先获取stackId下所有构件的入库状态
        $querySql = "SELECT
                    S.ProductId,S.SeatId,S.StackId
                    FROM
                     ch1_shipsplit SP
                     INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
                     LEFT JOIN productdata P ON P.ProductId = S.ProductId
                     LEFT JOIN productstock K ON K.ProductId = P.ProductId
                    WHERE
                     S.Estate > 0 
                     AND SP.Estate = '1' 
                     AND K.tStockQty >= SP.Qty AND S.StackId='$stackId'";
        $querySqlRes = $this->db->format($querySql);
        if ($querySqlRes == null) {
            throw new Exception("该垛下没有任何已入库的构件，无法移库");
        }


        $updateSql = "update yw1_ordersheet set SeatId='$seatId' WHERE ProductId in (
                                    SELECT ProductId from (
                                        SELECT
                                        S.ProductId,StackId
                                        FROM
                                         ch1_shipsplit SP
                                         INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
                                         LEFT JOIN productdata P ON P.ProductId = S.ProductId
                                         LEFT JOIN productstock K ON K.ProductId = P.ProductId
                                        WHERE
                                         S.Estate > 0 
                                         AND SP.Estate = '1' 
                                         AND K.tStockQty >= SP.Qty
                                         AND S.StackId = '$stackId'		
                                         ) N
                                        )";
        $UpdateSeatId = $this->db->conn->query($updateSql);
        if (!$UpdateSeatId) {
            throw new Exception("yw1_ordersheet更新失败，请重试。");
        }


        return true;
    }

    /**
     *  移垛
     * @param $originStackId
     * @param $newStackId
     * @param $productIds
     * @param $openId
     * @return bool
     * @throws Exception
     */
    public function moveStack($originStackId, $newStackId, $productIds, $openId)
    {

        //首先查出目标垛号所属库位信息
        $sql = "SELECT
                    S.ProductId,S.SeatId,S.StackId
                    FROM
                     ch1_shipsplit SP
                     INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
                     LEFT JOIN productdata P ON P.ProductId = S.ProductId
                     LEFT JOIN productstock K ON K.ProductId = P.ProductId
                    WHERE
                     S.Estate > 0 
                     AND SP.Estate = '1' 
                     AND K.tStockQty >= SP.Qty
                     AND StackId = '$newStackId'
              ";
        $sqlRes = $this->db->format($sql);
        if($sqlRes == null){
            $seatId = '';
        }else{
            $seatId = $sqlRes[0]['SeatId'];
        }
        foreach ($productIds as $item) {
            $productId = $item["productId"];

            //首先获取$inventoryDataId 数据详情入库状态
            $stackDetailSql = "SELECT
                    S.ProductId,S.SeatId,S.StackId
                    FROM
                     ch1_shipsplit SP
                     INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
                     LEFT JOIN productdata P ON P.ProductId = S.ProductId
                     LEFT JOIN productstock K ON K.ProductId = P.ProductId
                    WHERE
                     S.Estate > 0 
                     AND SP.Estate = '1' 
                     AND K.tStockQty >= SP.Qty
                     AND S.ProductId = '$productId'";
            $stackDetailSqlRes = $this->db->format($stackDetailSql);
            if ($stackDetailSqlRes == null) {
                throw new Exception($productId . "构件状态为未入库或已发货，无法移垛");
            }
            //更新yw1_ordersheet
            $updateOrderSheet = "update yw1_ordersheet set SeatId='$seatId', StackId = '$newStackId' WHERE ProductId='$productId' AND Estate > 0";
            $updateOrderSheetRes = $this->db->conn->query($updateOrderSheet);
            if (!$updateOrderSheetRes) {
                throw new Exception("yw1_ordersheet更新失败，请重试。productId" . $productId);
            }

        }


        return true;
    }


    /**
     * 入库回退
     * @param $products
     * @return bool
     * @throws Exception
     */
    public function cancelFinishedProducts($products)
    {
        foreach ($products as $item) {
            $productId = $item["productId"];
            $storageNO = $item["storageNO"];//入库单号
            $sql = "SELECT
                    S.POrderId,P.ProductId,
                        P.cName,
                        O.ForShort,
                        SC.sPOrderId,C.Id CjtjId,S.scflag
                    FROM
                     ch1_shipsplit SP
                     INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
                     LEFT JOIN productdata P ON P.ProductId = S.ProductId
                     LEFT JOIN productstock K ON K.ProductId = P.ProductId
                     LEFT JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
                     LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
										 LEFT JOIN sc1_cjtj C ON C.POrderId = SP.POrderId
										 INNER JOIN yw1_scsheet SC ON SC.sPOrderId = C.sPOrderId
                    WHERE
                     S.Estate > 0 
                     AND SP.Estate = '1' 
                     AND K.tStockQty >= SP.Qty
										 AND SC.ActionId = 101
                     AND S.ProductId = $productId
                ";
            $res = $this->db->format($sql);
            if ($res == null) {
                throw new Exception("该构件为非待出构件，无法回退---" . $productId);
            }
            $POrderId = $res[0]['POrderId'];
            $scFlag = $res[0]['scflag'];

            $CjtjId = $res[0]['CjtjId'];
//            $preEstate = $res[0]['PreEstate'];

            if($scFlag == 0 && $scFlag!=null){
                throw new Exception("该构件为强制插入构件，无法回退---" . $productId);
            }

            $UpdateSql = "UPDATE sc1_cjtj SET Estate=2 WHERE Id = $CjtjId";
            $UpdateResult = $this->db->conn->query($UpdateSql);
            if ($UpdateResult) {
                $UpdateSeatId = $this->db->conn->query("update yw1_ordersheet set SeatId='', StorageNO = '' WHERE POrderId='$POrderId' AND Estate > 0");
                if (!$UpdateSeatId) {
                    throw new Exception("编号为" . $productId . "   yw1_ordersheet数据更新失败");
                }
                $UpdateCH = $this->db->conn->query("update ch1_shipsplit set Estate= 3 WHERE POrderId='$POrderId' ");
                if (!$UpdateCH) {
                    throw new Exception("编号为" . $productId . "  ch1_shipsplit数据更新失败");
                }


            } else {
                throw new Exception("入库回退失败");
            }
        }
        return true;
    }


    /**
     * 待出构件根据库位查询垛号
     * @param $seatId
     * @return array|null
     */
    public function getStackIdBySeat($seatId)
    {
        $sql = "SELECT
                 DISTINCT StackId
                FROM
                 ch1_shipsplit SP
                 INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
                 LEFT JOIN productdata P ON P.ProductId = S.ProductId
                 LEFT JOIN productstock K ON K.ProductId = P.ProductId
                WHERE
                 S.Estate > 0 
                 AND SP.Estate = '1' 
                 AND K.tStockQty >= SP.Qty
                AND S.SeatId='$seatId'";
        return $this->db->format($sql);
    }

    /**
     * 根据垛号获取已入库构件
     * @param $stackId
     * @return array|null
     */
    public function getProductByStackId($stackId)
    {
        $sql = "SELECT
                 P.cName,p.BuildingNo,p.FloorNo,T.Forshort
                FROM
                 ch1_shipsplit SP
                 INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
                 LEFT JOIN productdata P ON P.ProductId = S.ProductId
                 LEFT JOIN productstock K ON K.ProductId = P.ProductId
                 LEFT JOIN trade_object T ON P.CompanyId = T.CompanyId
                WHERE
                 S.Estate > 0 
                 AND SP.Estate = '1' 
                 AND K.tStockQty >= SP.Qty
                AND S.StackId='$stackId'";
        return $this->db->format($sql);
    }

    //根据构件查库位与垛号
    public function  getStackIdAndSeatByProduct($cName)
    {
        $sql ="SELECT
             S.StackId,S.SeatId
            FROM
             ch1_shipsplit SP
             INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
             LEFT JOIN productdata P ON P.ProductId = S.ProductId
             LEFT JOIN productstock K ON K.ProductId = P.ProductId
            WHERE
             S.Estate > 0 
             AND SP.Estate = '1' 
             AND K.tStockQty >= SP.Qty
            AND P.cName='$cName'";
        return $this->db->format($sql);
    }



    /**
     * 获取产品构件名
     * @param $tradeId
     * @param $buildNo
     * @param $floorNo
     * @param $typeId
     * @param $pName
     * @return array|null
     */
    public function searchCName($tradeId, $buildNo, $floorNo, $typeId)
    {
        $searchRows = "";
        if (!is_null($tradeId)) {
            $searchRows .= " AND C.Id = $tradeId ";
        }
        if (!is_null($buildNo)) {
            $searchRows .= " AND M.BuildNo = '$buildNo' ";
        }
        if (!is_null($floorNo)) {
            $searchRows .= " AND P.FloorNo = '$floorNo' ";
        }
        if (!is_null($typeId)) {
            $searchRows .= " AND P.TypeId = $typeId";
        }

        $sql ="SELECT
             S.StackId,S.SeatId,P.cName,S.POrderId
            FROM
             ch1_shipsplit SP
             INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
            LEFT JOIN  yw1_ordermain M ON M.OrderNumber=S.OrderNumber
            LEFT JOIN  trade_object C ON C.CompanyId=M.CompanyId
            LEFT JOIN  productdata P ON P.ProductId = S.ProductId
            LEFT JOIN  productstock K ON K.ProductId = P.ProductId
            WHERE
            1 $searchRows
            AND S.Estate > 0 
             AND SP.Estate = '1' 
             AND K.tStockQty >= SP.Qty";
        return $this->db->format($sql);
    }


    /**
     * 扫码添加
     * @param $cname
     * @param $stackId
     * @param $openId
     * @return bool
     * @throws Exception
     */
    public function addFinishedProductByProductNameV2($cname,$stackId,$openId){

        //判断该构件是否是待入库
        $querySql = "SELECT
                P.ProductId,
                P.cName,
                y.StackId,y.SeatId
            FROM
                sc1_cjtj S
                INNER JOIN cg1_stocksheet G ON G.StockId = S.StockId
                INNER JOIN yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
                INNER JOIN workshopdata W ON W.Id = SC.WorkShopId
                INNER JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
                INNER JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
                LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
                INNER JOIN productdata P ON P.ProductId = Y.ProductId
                INNER JOIN productunit U ON U.Id = P.Unit
                LEFT JOIN yw3_pileadtime PI ON PI.POrderId = Y.POrderId 
            WHERE
                S.Estate <> 0 
                AND (Y.StackId IS  NULL OR Y.StackId = '')
                AND S.Estate = '2' 
                AND G.LEVEL = 1 
            AND cName='$cname'
            GROUP BY
                S.StockId";
        $querySqlRes = $this->db->format($querySql);
        if($querySqlRes == null){
            //判断是否排产
            $ssql = "SELECT
            PD.cName,PD.FloorNo,PD.TypeId,1     STATUS,PD.Operator,PD.Date FROM
            (
            SELECT
            A.ProductId,A.TypeId,A.FloorNo ,A.Operator,A.Date ,A.cName FROM
            (
            SELECT
    PD.ProductId, PD.TypeId ,PD.FloorNo,
                        SC.POrderId,
                        SC.sPOrderId,
    SC.Operator,
    SC.Date,
                        O.Forshort,
                        SC.Qty,
                        SC.mStockId,
                        SC.ActionId,
                        SC.scDate,
                        WA.NAME AS WorkShopName,
                        getCanStock ( SC.sPOrderId, 1 ) AS canSign,
                        ( CG.addQty + CG.FactualQty ) AS xdQty,
                        D.StuffId,
                        D.StuffCname,
                        D.Price,
                        D.Picture,
                        D.Gfile,
                        D.Gstate,
            IF
                        ( CG.DeliveryWeek > 0, CG.DeliveryDate, '2099-12-31' ) AS DeliveryDate,
                        CG.DeliveryWeek,
                        getOrderStockTime ( SC.sPOrderId ) AS StockTime,
                        S.OrderPO,
                        P.cName
            FROM
                        yw1_scsheet SC
                        LEFT JOIN yw1_ordersheet S ON S.POrderId = SC.POrderId
                        LEFT JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
                        LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
                        LEFT JOIN workorderaction W ON W.ActionId = SC.ActionId
                        LEFT JOIN workshopdata WA ON WA.Id = SC.WorkShopId
                        LEFT JOIN cg1_semifinished SM ON SM.mStockId = SC.mStockId
                        LEFT JOIN cg1_stocksheet CG ON CG.StockId = SC.mStockId
                        INNER JOIN productdata PD ON PD.ProductId = S.ProductId
                        LEFT JOIN stuffdata D ON D.StuffId = CG.StuffId
                        LEFT JOIN productdata P ON P.ProductId = S.ProductId
            WHERE
                        1
                        AND SC.scFrom > 0
                        AND SC.Estate > 0
                        AND SC.Qty > SC.ScQty
                        AND SC.ActionId = '104'
    AND P.cName='$cname'
--                    AND O.Forshort = 'G66_3_4'  -- 项目
--                    AND M.BuildNo = '3' -- 楼栋
                        AND CG.DeliveryWeek > 0
            GROUP BY
                        SC.sPOrderId
            ) A
WHERE
            A.canSign = 1
                        ) PD
WHERE PD.TypeId is not NULL
GROUP BY  PD.TypeId,PD.FloorNo,PD.cName;";
            if($this->db->format($ssql)!=null)
                throw new Exception("该构件当前处于待排产状态，不符合入垛条件！");
            //判断是否浇筑
            $ssql = "SELECT
    DISTINCT P.ProductId, P.TypeId ,P.FloorNo
FROM
	yw1_scsheet SC
	LEFT JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
	LEFT JOIN yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
	LEFT JOIN productdata P ON P.ProductId = Y.ProductId
	LEFT JOIN trade_object O ON O.CompanyId = OM.CompanyId
	LEFT JOIN cg1_semifinished M ON M.StockId = SC.StockId
	LEFT JOIN cg1_stocksheet G ON G.StockId = M.mStockId
	LEFT JOIN cg1_stockmain SM ON SM.Id = G.Mid
	LEFT JOIN stuffdata D ON D.StuffId = M.mStuffId
	LEFT JOIN stuffdata SD ON SD.StuffId = M.StuffId 
WHERE
	1 
	AND SC.ActionId = '104' 
	AND SC.scFrom > 0 
	AND SC.Estate > 0 
	AND P.cName='$cname'
	AND getCanStock ( SC.sPOrderId, 3 ) = 3 ";
            if($this->db->format($ssql)!=null)
                throw new Exception("该构件当前处于骨架搭建状态，不符合入垛条件！");
            //判断是否已经脱模
            $ssql = "SELECT PD.cName,PD.FloorNo,PD.TypeId,3 STATUS,PD.Operator,PD.Date FROM ( SELECT
            P.cName,
                  P.FloorNo,
                  P.TypeId,
                  2 STATUS,
                  SC.Operator,
                  SC.Date
                FROM
                            yw1_scsheet SC
                            INNER JOIN yw1_ordersheet S ON S.POrderId = SC.POrderId
                            INNER JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
                            LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
                            INNER JOIN productdata P ON P.ProductId = S.ProductId
                            INNER JOIN productunit U ON U.Id = P.Unit
                            INNER JOIN cg1_stocksheet G ON G.StockId = SC.StockId
                            INNER JOIN stuffdata D ON D.StuffId = G.StuffId
                            LEFT JOIN yw3_pileadtime PI ON PI.POrderId = S.POrderId
                            LEFT JOIN yw2_orderexpress E ON E.POrderId = S.POrderId WHERE
                            1
                            AND SC.scFrom > 0
                            AND SC.Estate > 0
                            AND SC.ActionId = '101'
                            AND P.cName='$cname'
                --        AND P.TypeId = '8001'
                            AND getCanStock ( SC.sPOrderId, 3 ) = 3 GROUP BY
                            SC.Id
                ORDER BY
                            PI.Leadweek DESC
                )PD WHERE PD.TypeId is not NULL
                GROUP BY  PD.TypeId,PD.FloorNo,PD.cName;";
            if($this->db->format($ssql)!=null)
                throw new Exception("该构件当前处于脱模状态，不符合入垛条件！");


            //半成品未质检
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
                AND P.cName='$cname'";
            if($this->db->format($ssql)!=null)
                throw new Exception("该构件半成品未质检，不符合入垛条件！");
            //待入库（成品） 根据后台页面
//            $ssql = "SELECT PD.cName,PD.FloorNo,PD.TypeId,4 STATUS,PD.Operator,PD.Date FROM (SELECT
//            P.ProductId,P.cName, P.TypeId ,P.FloorNo ,S.Operator,S.Date FROM
//            sc1_cjtj S
//            INNER JOIN cg1_stocksheet G ON G.StockId = S.StockId
//            INNER JOIN yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
//            INNER JOIN workshopdata W ON W.Id = SC.WorkShopId
//            INNER JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
//            INNER JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
//            LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
//            INNER JOIN productdata P ON P.ProductId = Y.ProductId
//            INNER JOIN productunit U ON U.Id = P.Unit
//            LEFT JOIN yw3_pileadtime PI ON PI.POrderId = Y.POrderId WHERE
//            S.Estate <> 0
//						AND P.cName='$cname'
//            AND S.Estate = '2'
//            AND G.LEVEL = 1
//)PD WHERE PD.TypeId is not NULL
//GROUP BY  PD.TypeId,PD.FloorNo,PD.cName;";
//            if($this->db->format($ssql)!=null)
//                throw new Exception("该构件当前处于待入库状态，不符合入垛条件！");
            $ssql = "SELECT PD.cName,PD.FloorNo,PD.TypeId,5 STATUS,PD.Operator,PD.Date FROM
            (
                    SELECT
                      P.cName,
                      P.FloorNo,
                      P.TypeId,
                      5 STATUS,
                      SP.Operator,
                      SP.Date
                    FROM
                        ch1_shipsplit SP
                        INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
                        LEFT JOIN yw3_pisheet PI ON PI.oId = S.Id
                        LEFT JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
                        LEFT JOIN yw7_clientOutData O ON O.Mid = SP.id
                        LEFT JOIN yw7_clientOutData OP ON OP.POrderId = S.POrderId
                        AND OP.Sign = 1
                        LEFT JOIN yw7_clientOrderPo R ON R.Mid = SP.id
                        LEFT JOIN productdata P ON P.ProductId = S.ProductId
                        LEFT JOIN ch1_shipsheet SS ON P.ProductId = SS.ProductId
                        LEFT JOIN ch1_shipmain CS ON CS.Id = SS.Mid
                        LEFT JOIN productstock K ON K.ProductId = P.ProductId
                        LEFT JOIN taxtype X ON X.Id = S.taxtypeId
                       LEFT JOIN trade_drawing TG ON CONCAT_WS( '-', TG.BuildingNo, TG.FloorNo, TG.CmptNo, TG.SN ) = P.cName
            WHERE
                        S.Estate > 0
                        AND SP.Estate = '1'
                        AND P.cName='$cname'
--                    AND S.OrderPO = '17-05-27-13-30'
--                    AND P.TypeId = '8001'
                        AND K.tStockQty >= SP.Qty UNION ALL
            SELECT
                        S.SampName AS cName,
                        '' AS FloorNo,
                        '' AS TypeId,
                        5 STATUS,
                        '' AS Operator,
                        '' ASDate        
            FROM
                        ch5_sampsheet S
            WHERE
                        1
                        AND S.Estate = '1'
            )  PD WHERE PD.TypeId is not NULL
GROUP BY  PD.TypeId,PD.FloorNo,PD.cName;";
            if($this->db->format($ssql)!=null)
                throw new Exception("该构件当前已入库，不符合入垛条件！");

            $ssql ="SELECT PD.cName,PD.FloorNo,PD.TypeId,6 STATUS,PD.Operator,PD.Date FROM
            (
            SELECT
            P.cName,
            P.FloorNo,
  P.TypeId,
  5 STATUS,
  M.Operator,
  M.Date         
FROM
            ch1_shipsheet S
            LEFT JOIN ch1_shipmain M ON M.Id = S.Mid
            LEFT JOIN yw1_ordersheet YS ON YS.POrderId = S.POrderId
            LEFT JOIN yw1_ordermain YM ON YM.OrderNumber = YS.OrderNumber
            LEFT JOIN productdata P ON P.ProductId = S.ProductId
            LEFT JOIN taxtype BG ON BG.Id = P.taxtypeId
            LEFT JOIN productunit U ON U.Id = P.Unit
            LEFT JOIN yw3_pisheet E ON E.oId = YS.Id
            LEFT JOIN ch1_shipsplit L ON L.ShipId = S.Id WHERE
            1
            AND S.Type = '1'
            AND M.Estate = '0'
--        AND M.Date = '2018-11-14'
            AND P.cName=''
--        AND P.cName LIKE '3-19%'
--        AND P.TypeId = '8011'
ORDER BY
            M.Date DESC,
            M.CompanyId
            )  PD WHERE PD.TypeId is not NULL
GROUP BY  PD.TypeId,PD.FloorNo,PD.cName;";
            if($this->db->format($ssql)!=null)
                throw new Exception("该构件当前已出库，不符合入垛条件！");
            throw new Exception("该构件不符合入垛条件，请确认当前该构件状态");
        }
        $updateSql = "UPDATE yw1_ordersheet SET StackId='$stackId'
WHERE ProductId IN(SELECT ProductId FROM productdata WHERE cName='$cname' )";

        if(!$this->db->conn->query($updateSql)){
            return false;
        }
        return true;
    }
}
<?php

include_once "../basic/parameter.inc";
include_once "../model/modelfunction.php";
include "../basic/chksession.php";

$OrderIdDate=date("Ymd");
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;

@$proId = addslashes($_POST['proId']);
$BuildNo = $_POST['BuildNo'];
/*$proId = 34;
$BuildNo = 22;*/
$mySql="select Estate,CreateOrder from $DataIn.bom_object WHERE TradeId='$proId' ";
$result = mysql_query($mySql);
if($result && $myRow = mysql_fetch_array($result)){
    $Estate = $myRow["Estate"];
    $CreateOrder = $myRow["CreateOrder"];
    
    //审核
    if ($Estate != 2) {
        echo json_encode(array(
                'rlt'=> false,
                'msg'=> '项目审核状态不对'
        ));
        return;
    }
    if ($CreateOrder != 0) {
        echo json_encode(array(
                'rlt'=> false,
                'msg'=> '该项目已经生成订单'
        ));
        return;
    }
    
}
else{
    echo json_encode(array(
            'rlt'=> false,
            'msg'=> '数据错误,请重新检索'
    ));
    return;
}

//判断构件产品 是否都审核通过
$result = mysql_query("SELECT a.Id, a.ProductId, a.cName, a.Estate
        from $DataIn.productdata a
        where a.Estate <> 1
        and a.cName in (
                select CONCAT_WS('-', b.BuildingNo, b.FloorNo, b.CmptNo, b.SN)
                from $DataIn.trade_drawing b where b.TradeId = $proId )");

if($result && $myRow = mysql_fetch_array($result)){
    echo json_encode(array(
            'rlt'=> false,
            'msg'=> '构件产品未审核通过,不能生产订单'
    ));
    return;
}

/*//申购单
$result = mysql_query("select b.TypeId, a.MouldNo, SUM(a.ProQty) as ProQty
        from $DataIn.bom_mould a
        LEFT JOIN producttype b on b.typename = a.MouldCat
        where a.TradeId = $proId
        group by a.MouldCat, a.MouldNo");

if($result && $myRow = mysql_fetch_array($result)){
    
    //供应商 查询第一个
    $checkResult = mysql_query("SELECT A.Letter,A.CompanyId,A.Forshort FROM $DataPublic.nonbom3_retailermain A WHERE A.Estate=1 ORDER BY A.Letter,A.Forshort",$link_id);
    if ($checkRow = mysql_fetch_array($checkResult)){
        $CompanyId=$checkRow["CompanyId"];
    }
    
    $Unit = "副";
    
    do {
        //$TypeId = $myRow["TypeId"]; //分类
         $TypeId = '22'; //设置为模台对应的生产分类 by.h 180420
        $MouldNo = $myRow["MouldNo"];
        $ProQty = $myRow["ProQty"];
        
        //检查模具 申购商品
        $checkSql1=mysql_query("SELECT A.GoodsId,A.Price,A.Unit,A.TypeId,B.wStockQty,B.oStockQty,B.mStockQty,B.CompanyId, C.mainType,C.BuyerId,E.AddTaxValue
                FROM $DataPublic.nonbom4_goodsdata A
                LEFT JOIN $DataPublic.nonbom5_goodsstock  B ON B.GoodsId=A.GoodsId
                LEFT JOIN $DataPublic.nonbom2_subtype C ON C.Id=A.TypeId
                LEFT JOIN $DataPublic.nonbom5_goodsstock D ON D.GoodsId=A.GoodsId
                LEFT JOIN $DataPublic.nonbom3_retailermain E ON E.CompanyId=D.CompanyId
                WHERE A.GoodsName='$MouldNo' LIMIT 1",$link_id);
        if($checkRow1=mysql_fetch_array($checkSql1)){
            $GoodsId = $checkRow1["GoodsId"];
            $Price = $checkRow1["Price"];
            $Unit = $checkRow1["Unit"];
            $CompanyId = $checkRow1["CompanyId"];
        } else {
            //加入 商品
            $maxSql = mysql_query("SELECT MAX(GoodsId) AS MGoodsId FROM $DataPublic.nonbom4_goodsdata",$link_id);
            $GoodsId=mysql_result($maxSql,0,"MGoodsId");
            $GoodsId=$GoodsId + 1;
            
            $InSql="INSERT INTO $DataPublic.nonbom4_goodsdata(
            GoodsId,
            GoodsName,
            BarCode,
            TypeId,
            Attached,Price,Unit,CkId,nxId,pdDate,ReturnReasons,Remark,
            Date,Operator
            )VALUES(
            $GoodsId,
            $MouldNo,
            '',
            $TypeId,
            0,0,'$Unit',0,0,0,'','',
            '$Date','$Operator')";
            
            $InRecode=@mysql_query($InSql);
            
            //属性
            $InSql="INSERT INTO $DataPublic.nonbom5_goodsstock(GoodsId,
            wStockQty,oStockQty,CompanyId,Date,Operator
            )VALUES($GoodsId,
            0,0,$CompanyId,'$Date','$Operator')";
            
            $InRecode=@mysql_query($InSql);
        }
        
        $sheetInSql="INSERT INTO $DataIn.nonbom6_cgsheet
        (Id,Mid,fromMid,qkId,mainType,GoodsId,CompanyId,BuyerId,Qty,Price,AddTaxValue,Remark,ReturnReasons,rkSign,Estate,Locks,Date,Operator)
        VALUES
        (NULL,'0',0,'0','$TypeId','$GoodsId','$CompanyId','','$ProQty','0','0','','','1','2','1','$Date','$Operator') ";
        $sheetInAction=@mysql_query($sheetInSql);
        
    }while($myRow = mysql_fetch_array($result));
}*/
        
//业务-下单
$SubClientId=0;
//项目编号-楼栋编号-楼层编号
$result = mysql_query("select  c.CompanyId, b.TradeNo, a.BuildingNo, a.FloorNo
        from $DataIn.trade_drawing a
        LEFT JOIN $DataIn.trade_info b on a.TradeId = b.TradeId
        left join trade_object c on a.TradeId = c.id
        where a.TradeId = $proId and a.BuildingNo = '$BuildNo' GROUP BY c.CompanyId, b.TradeNo, a.BuildingNo, a.FloorNo order by a.FloorNo+0");

if($result && $myRow = mysql_fetch_array($result)){
    do {
        $CompanyId = $myRow["CompanyId"];
        
        $TradeNo = $myRow["TradeNo"];
        $BuildingNo = $myRow["BuildingNo"];
        $FloorNo = $myRow["FloorNo"];
        
        $OrderPO = $TradeNo . "-" . $BuildingNo . "-" . $FloorNo;
        

//         //查找最大 订单id
//         $maxSql = mysql_query("SELECT IFNULL(MAX(OrderNumber),0) AS Mid FROM $DataIn.yw1_ordermain",$link_id);
//         $OrderNumber=mysql_result($maxSql,0,"Mid");
//         $OrderNumber=$OrderNumber + 1;
        
//         $InSql="INSERT INTO $DataIn.yw1_ordermain (
//         CompanyId,SubClientId,OrderNumber,OrderPO,OrderDate,ClientOrder,Locks,Operator)
//         VALUES
//         ($CompanyId,$SubClientId,$OrderNumber,'$OrderPO','$Date','','0','$Operator')";
        
//         $InRecode=@mysql_query($InSql);
        
        
        //查找对应的产品
        $productResult = mysql_query("select p.ProductId,p.CName, p.Price from trade_drawing a 
LEFT JOIN productdata p on p.drawingId=a.Id
where a.TradeId = $proId and a.BuildingNo = '$BuildingNo' and a.FloorNo = '$FloorNo'");
        
        if($productResult && $myproRow = mysql_fetch_array($productResult)){
            do {
                
                $ProductId = $myproRow["ProductId"];
   
                $checkResult = mysql_query("select a.id from $DataIn.yw1_ordermain a
                        inner join $DataIn.yw1_ordersheet b on a.OrderNumber = b.OrderNumber
                        where a.CompanyId=$CompanyId and a.OrderPO='$OrderPO' and b.ProductId=$ProductId");
                if($checkResult && $checkRow = mysql_fetch_array($checkResult)){
                    echo json_encode(array(
                            'rlt'=> false,
                            'msg'=> '订单已经存在！'
                    ) );
                    exit;
                } else {

                    $Pid[] = $myproRow["ProductId"];
                    $Qty[] = 1;
                    $ProductPrice[] = $myproRow["Price"];
                }
                /*
                //订单号
                $maxSql = mysql_query("SELECT IFNULL(MAX(A.POrderId),'') AS POrderId FROM (
                        SELECT IFNULL(MAX(POrderId),0) AS POrderId FROM yw1_ordersheet WHERE POrderId LIKE CONCAT('$OrderIdDate','%')
                        UNION
                        SELECT IFNULL(MAX(POrderId),0) AS POrderId FROM yw1_orderdeleted WHERE POrderId LIKE CONCAT('$OrderIdDate','%')
                        UNION
                        SELECT SUBSTR( IFNULL(MAX(StockId) , 0 ) ,1,12) AS POrderId FROM cg1_stocksheet WHERE StockId LIKE CONCAT('$OrderIdDate',  '%' ) 
                                AND SUBSTR(StockId,1,9)!=CONCAT('$OrderIdDate','9')
                        )A WHERE 1",$link_id);
                $POrderId=mysql_result($maxSql,0,"POrderId");
                if ($POrderId) {
                    $POrderId=$POrderId + 1;
                } else {
                    $POrderId = $OrderIdDate . "0001";
                }

                $taxResult = mysql_query("SELECT taxtypeId FROM $DataIn.productdata WHERE ProductId=$Pid");
                $taxtypeId=mysql_result($taxResult,0,"taxtypeId");
                if (!$taxtypeId) $taxtypeId = 0;
                
                $InSql="INSERT INTO $DataIn.yw1_ordersheet (
                    OrderNumber,OrderPO,POrderId,ProductId,Qty,Price,PackRemark,cgRemark,sgRemark,dcRemark,
                    DeliveryDate,ShipType,taxtypeId,scFrom,Estate,Locks,Date,Operator) VALUES (
                    $OrderNumber,'$OrderPO','$POrderId',$Pid,$Qty,$ProductPrice,'','','','',
                '0000-00-00','',$taxtypeId,'1','1','0','$Date','$Operator')";

                $InRecode=@mysql_query($InSql);
                */


            }while($myproRow = mysql_fetch_array($productResult));
        }
        
        $_ProductId=implode("|", $Pid);
        $_Qty=implode("|", $Qty);
        $_Price=implode("|", $ProductPrice);

        $myResult=mysql_query("CALL proc_yw1_ordersheet_insert($CompanyId,$SubClientId,'$OrderPO','$Date','$_ProductId','0','$_Qty','$_Price',$Operator);");
        echo "CALL proc_yw1_ordersheet_insert($CompanyId,$SubClientId,'$OrderPO','$Date','$_ProductId','0','$_Qty','$_Price',$Operator);";
        $myInsertRow = mysql_fetch_array($myResult);
        $OperationResult = $myInsertRow['OperationResult'];
        $OrderNumber=$OrderNumber+$myInsertRow['OrderNumber'];
        $Log=$myInsertRow['OperationLog'];
unset($Pid);
unset($Qty);
unset($ProductPrice);
        if ($OperationResult=="Y"){

        }
        
        //重复调用 添加
        mysql_close($link_id);
        $link_id = mysql_connect($host, $user, $pass);
        mysql_query("SET NAMES 'utf8'");
        mysql_select_db($DataIn, $link_id) or die("无法选择数据库!");//默认数据库为$DataIn
        
    }while(($myRow = mysql_fetch_array($result)) );
}

if  ($OperationResult=="Y")
{

    $BuildSql = "SELECT A.BuildingNo from (SELECT T.BuildingNo from trade_drawing T where (T.TradeId=$proId) 
group by T.BuildingNo) A
WHERE A.BuildingNo not in (
SELECT yw1_ordermain.BuildNo from yw1_ordermain 
LEFT JOIN trade_object on trade_object.CompanyId=yw1_ordermain.CompanyId
 where trade_object.Id=$proId GROUP BY yw1_ordermain.BuildNo)";
    $BuildResult = mysql_query($BuildSql,$link_id);
    $BuildRow = mysql_fetch_array($BuildResult);
    if(!$BuildResult  || !$BuildRow) {
        $updateSql = "update $DataIn.bom_object set CreateOrder=1 WHERE TradeId='$proId' ";
        @mysql_query($updateSql);
    }
    echo json_encode(array(
        'rlt'=> true,
        'msg'=> '添加'.$BuildNo.'栋产品数量为：'.$OrderNumber
        ) );
}
else
{
    echo json_encode(array(
    'rlt'=> false,
    'msg'=> '添加订单出错！'
) );
}




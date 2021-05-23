<?php

include_once "../basic/parameter.inc";
include_once "../model/modelfunction.php";
include "../basic/chksession.php";

$OrderIdDate = date("Ymd");
$Date = date("Y-m-d");
$DateTime = date("Y-m-d H:i:s");
$Operator = $Login_P_Number;

@$proId = addslashes($_POST['proId']);
$PostBuild = $_POST['BuildNo'];
$Build = explode( '-', $PostBuild,2);
$BuildNo = $Build[0];
$FloorNo = $Build[1];
$Type = $Build[2];

$mySql = "select Estate,CreateOrder from $DataIn.bom_object WHERE TradeId='$proId' ";
$result = mysql_query($mySql);
if ($result && $myRow = mysql_fetch_array($result)) {
    $Estate = $myRow["Estate"];
    $CreateOrder = $myRow["CreateOrder"];

    //审核
    if ($Estate != 2) {
        echo json_encode(
            array(
                'rlt' => false,
                'msg' => '项目审核状态不对',
            ));

        return;
    }
    if ($CreateOrder != 0) {
        echo json_encode(
            array(
                'rlt' => false,
                'msg' => '该项目已经生成订单',
            ));

        return;
    }

}
else {
    echo json_encode(
        array(
            'rlt' => false,
            'msg' => '数据错误,请重新检索',
        ));

    return;
}

//判断构件产品 是否都审核通过
$result = mysql_query("select p.ProductId,p.CName, p.Price from trade_drawing a 
LEFT JOIN productdata p on p.drawingId=a.Id
where a.TradeId = $proId and p.Estate <> 1 and a.BuildingNo = '$BuildNo' and a.FloorNo = '$FloorNo' and a.CmptTypeId = '$Type'");

if ($result && $myRow = mysql_fetch_array($result)) {
    echo json_encode(array(
                         'rlt' => false,
                         'msg' => '构件产品未审核通过,不能生产订单',
                     ));

    return;
}
//业务-下单
$SubClientId = 0;
//项目编号-楼栋编号-楼层编号
$result = mysql_query("select  c.CompanyId, b.TradeNo, a.BuildingNo, a.FloorNo, a.CmptTypeId 
        from $DataIn.trade_drawing a
        LEFT JOIN $DataIn.trade_info b on a.TradeId = b.TradeId
        LEFT JOIN $DataIn.trade_object c on a.TradeId = c.id
        where a.TradeId = $proId and a.BuildingNo = '$BuildNo' and a.FloorNo = '$FloorNo' and a.CmptTypeId = '$Type' GROUP BY c.CompanyId, b.TradeNo, a.BuildingNo, a.FloorNo order by a.FloorNo+0");
if ($result && $myRow = mysql_fetch_array($result)) {
    do {
        $CompanyId = $myRow["CompanyId"];
        $TradeNo = $myRow["TradeNo"];
        $BuildingNo = $myRow["BuildingNo"];
        $FloorNo = $myRow["FloorNo"];
        $CmptTypeId = $myRow["CmptTypeId"];
        $OrderPO = $TradeNo . "-" . $BuildingNo . "-" . $FloorNo;

        $checkResult = mysql_query("SELECT YM.Id FROM $DataIn.yw1_ordermain YM LEFT JOIN $DataIn.yw1_ordersheet YS ON YS.OrderNumber = YM.OrderNumber LEFT JOIN $DataIn.productdata P ON P.ProductId = YS.ProductId where YM.CompanyId=$CompanyId AND YM.OrderPO='$OrderPO' AND P.TypeId = '$CmptTypeId'");
        if ($checkResult && $checkRow = mysql_fetch_array($checkResult)) {
            continue;
        }
        else {
            //查找对应的产品
            $productResult = mysql_query("select p.ProductId,p.CName, p.Price from $DataIn.trade_drawing a LEFT JOIN $DataIn.productdata p on p.drawingId=a.Id where a.TradeId = $proId and a.BuildingNo = '$BuildingNo' and a.FloorNo = '$FloorNo' AND a.CmptTypeId = '$CmptTypeId'");

            if ($productResult && $myproRow = mysql_fetch_array($productResult)) {
                do {
                    $Pid[] = $myproRow["ProductId"];
                    $Qty[] = 1;
                    $ProductPrice[] = $myproRow["Price"];
                } while ($myproRow = mysql_fetch_array($productResult));
            }
            $_ProductId = implode("|", $Pid);
            $_Qty = implode("|", $Qty);
            $_Price = implode("|", $ProductPrice);

            $myResult = mysql_query("CALL proc_yw1_ordersheet_insert($CompanyId,$SubClientId,'$OrderPO','$Date','$_ProductId','0','$_Qty','$_Price',$Operator);");
            $myInsertRow = mysql_fetch_array($myResult);
            $OperationResult = $myInsertRow['OperationResult'];
            $OrderNumber = $OrderNumber + $myInsertRow['OrderNumber'];
            $Log = $myInsertRow['OperationLog'];
            unset($Pid);
            unset($Qty);
            unset($ProductPrice);
            //重复调用 添加
            mysql_close($link_id);
            $link_id = mysql_connect($host, $user, $pass);
            mysql_query("SET NAMES 'utf8'");
            mysql_select_db($DataIn, $link_id) or die("无法选择数据库!");//默认数据库为$DataIn
            if ($OperationResult == "N") {
                break;
            }
        }
    } while (($myRow = mysql_fetch_array($result)));
}

mysql_free_result($result);

$Date=date("Y-m-d");
$DateTemp=date("Y");

$sql = "SELECT S.id,S.StockId,S.CompanyId,S.BuyerId,P.TypeName 
FROM cg1_stocksheet S 
LEFT JOIN yw1_ordersheet Y ON Y.POrderId = S.POrderId 
LEFT JOIN stuffdata SD ON SD.StuffId = S.StuffId 
INNER JOIN productdata PD ON PD.ProductId = Y.ProductId 
INNER JOIN producttype P ON P.TypeId = PD.TypeId 
WHERE Y.POrderId IN ( SELECT POrderId FROM yw1_ordersheet WHERE OrderPO = '$OrderPO' ) 
AND (SD.TypeId = 9002 or SD.TypeId = 9019 or SD.TypeId = 9006) 
AND S.FactualQty > 0";

$ret = mysql_query($sql);
if ($res = mysql_fetch_array($ret)) {

    do {
        $Id = $res['id'];
        $StockId = $res['StockId'];
        $CompanyId = $res['CompanyId'];
        $BuyerId = $res['BuyerId'];
        $TypeName = $res['TypeName'];

        $checkSql = mysql_query("SELECT S.StockId FROM $DataIn.cg1_stocksheet S WHERE S.Id = $Id AND EXISTS(
  SELECT G.StockId FROM cg1_semifinished G WHERE G.mStockId=S.StockId)", $link_id);
        $semiSign = mysql_num_rows($checkSql);

//echo $semiSign;
        if ($semiSign > 0 && $Login_P_Number != 10341 && $Login_P_Number != 10868 && $Login_P_Number != 10871) {// && $Login_P_Number!=10868
            $Log = $Log . "<div class=redB>$TitleSTR 失败! 半成品不能直接下采购单！</div><br>";
            $OperationResult = "N";
            $fromWebPage = "cg_cgdsheet_read";
        }
        else {
            if (($CompanyId == "") or ($BuyerId == "")) {
                $GetCidRow = mysql_fetch_array(mysql_query("SELECT CompanyId,BuyerId FROM $DataIn.cg1_stocksheet S WHERE Id = $id LIMIT 1", $link_id));
                $CompanyId = $GetCidRow["CompanyId"];
                $BuyerId = $GetCidRow["BuyerId"];
            }

            $Bill_Temp = mysql_query("SELECT MAX(PurchaseID) AS maxID FROM $DataIn.cg1_stockmain WHERE PurchaseID LIKE '$DateTemp%'", $link_id);
            $PurchaseID = mysql_result($Bill_Temp, 0, "maxID");
            if ($PurchaseID) {
                $PurchaseID = $PurchaseID + 1; //四位时到9999就变成下一年了。
                $thisyear = substr($PurchaseID, 0, 4);
                $thisarray = substr($PurchaseID, 4);
                if ($thisyear > $DateTemp) {
                    $PurchaseID = $DateTemp . "1" . $thisarray;
                }
            }
            else {
                $PurchaseID = $DateTemp . "0001";
            }

            $insertFlag = 0;
            $Remark = $Remark == "" ? "" : $Remark;
            $inRecode = "INSERT INTO $DataIn.cg1_stockmain (Id,CompanyId,BuyerId,PurchaseID,DeliveryDate,Remark,Date,Operator,purchaseOrderNo) VALUES (NULL,'$CompanyId','$BuyerId','$PurchaseID','0000-00-00','$Remark','$Date','$Operator','$OrderPO-$TypeName')";
            $inAction = @mysql_query($inRecode);
            $Mid = mysql_insert_id();
            if ($inAction && $Mid > 0) {
                $Log = "$TitleSTR 成功!<br>";
                $Sql = "UPDATE $DataIn.cg1_stocksheet SET Mid='$Mid',Locks=0 WHERE Id = $Id AND Estate='0' and CompanyId='$CompanyId'";
                $Result = mysql_query($Sql);
                if ($Result && mysql_affected_rows() > 0) {
                    $Log .= "需求单明细 ($Id) 加入主采购单 $Mid 成功!<br>";
                    $insertFlag = 1;
                    $OperationResult = "Y";

                    if ($semiSign > 0) {
                        $upSql = "UPDATE cg1_stocksheet G 
							INNER JOIN yw1_scsheet S ON S.mStockId=G.StockId 
							INNER JOIN cg1_stocksheet A ON A.StockId=S.StockId
							SET G.Price=A.Price 
							WHERE G.Id =$Id AND A.Id>0";
                        $upResult = mysql_query($upSql);
                    }
                    //备份初始采购单
                    echo "<script language='javascript'>retCode1=openUrl('cg_cgdmain_tohtml.php?Id=$Mid');</script>";
                    //生成PDF采购单
                    $fromWebPageSign = 1;
                    $Id = $Mid;
                    include "PurchaseToPDF.php";
                }
                else {
                    $Log .= "<div class=redB>需求单明细 ($Id) 加入主采购单 $Mid 失败!(检查是否未审核)</div><br>";
                    $OperationResult = "N";
                    $fromWebPage = "cg_cgdsheet_read";
                    //删除主单
                    $DelSql = "DELETE FROM $DataIn.cg1_stockmain WHERE Id='$Mid' LIMIT 1";
                    $DelResult = mysql_query($DelSql);
                    if ($DelResult && mysql_affected_rows() > 0) {
                        $Log .= "主采购单 $Mid 已取消<br>";
                    }
                    else {
                        $Log .= "<div class=redB>主采购单 $Mid 未取消,请手动清除!( $DelSql )</div><br>";
                        $OperationResult = "N";

                    }
                }
            }
            else {
                $Log = $Log . "<div class=redB>$TitleSTR 失败!  请选择 供应商 或 采购员 后再生成订单！ </div><br>";
                $OperationResult = "N";
                $fromWebPage = "cg_cgdsheet_read";
            }
        }

        $IN_Recode = "INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
        $IN_res = @mysql_query($IN_recode);
    } while ($res = mysql_fetch_array($ret));



}
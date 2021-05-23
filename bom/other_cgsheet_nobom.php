<?php
//$MyPDOEnabled = 1;
session_start();
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");


$DateTime = date("Y-m-d H:i:s");
$Date = date("Y-m-d");
$Operator = $Login_P_Number;
$OperationResult = "Y";

//步骤3：需处理
$DateTemp = date("Ymd");
$value = explode('^^', $value);

//锁定表
include "../model/subprogram/FireFox_Safari_PassVar.php";

$POrderId = strlen($POrderId) >= 12 ? $POrderId : '';

$oldLevel = $oldLevel == '' ? 1 : $oldLevel;

foreach ($value as $v) {
    $z = explode('|', $v);
    $newStuffId = strip_tags($z[0]);            //配件ID
    $newFactualQty = strip_tags($z[1]);        //采购数量
    $newPrice = strip_tags($z[2]);        //采购价格
    $TradeId = strip_tags($z[3]);        //项目编号

//生成 非bom配件资料
    $mySql = "select DISTINCT su.name as unitname,sd.StuffCname as MaterName,
sd.Spec, sd.Price,sd.TypeId,sd.brand
from $DataIn.stuffdata sd 
LEFT JOIN $DataIn.stuffunit su on sd.unit = su.Id
where sd.StuffId = '$newStuffId' ";

    $result = mysql_query($mySql, $link_id);
    if ($result && $myRow = mysql_fetch_array($result)) {

        $CompanySql = mysql_query("SELECT A.Letter,A.CompanyId,A.Forshort FROM $DataPublic.nonbom3_retailermain A WHERE A.Estate=1 ORDER BY A.Letter,A.Forshort limit 1", $link_id);
        if ($CompanyRow = mysql_fetch_array($CompanySql)) {
            $CompanyId = $CompanyRow["CompanyId"];
        }
        if ($CompanyId == "") $CompanyId = "60001";

        $tradeSql = "SELECT TradeNo FROM trade_info WHERE TradeId = $TradeId";
        $tradeRow = mysql_fetch_array(mysql_query($tradeSql,$link_id));
        $TradeNo = $tradeRow["TradeNo"];

        do {
            $GoodsName = '['.$TradeNo.']'.$myRow["MaterName"];
            $Spec = $myRow["Spec"];
            $unitname = $myRow["unitname"];
            $brand = $myRow["brand"];
            $StuffType = $myRow["TypeId"];
            if ($StuffType == '9018') { //钢筋
                $TypeId = '49';
            } elseif ($StuffType == '9021') { //混凝土
                $TypeId = '58';
            } elseif ($StuffType == '9019') { //预埋
                $TypeId = '45';
                $Field = explode("-", $GoodsName);
                $Spec = $Field[count($Field)-1];
            }


            $checkResult = mysql_query("select GoodsId FROM $DataPublic.nonbom4_goodsdata where GoodsName='$GoodsName' AND TradeId ='$TradeId' limit 1");
            if ($checkRow = mysql_fetch_array($checkResult)) {
                //已经存在配件
                $GoodsId = $checkRow["GoodsId"];

            } else {

                $maxSql = @mysql_query("SELECT MAX(GoodsId) AS GoodsId FROM $DataPublic.nonbom4_goodsdata ORDER BY GoodsId DESC LIMIT 1", $link_id);
                $GoodsId = @mysql_result($maxSql, 0);
                if ($GoodsId) {
                    $GoodsId = $GoodsId + 1;
                } else {
                    $GoodsId = 70001;
                }

                $inRecode = "INSERT INTO $DataPublic.nonbom4_goodsdata (Id,TradeId,GoodsId,GoodsName,GoodSpec,BarCode,TypeId,Attached,Price,Unit,CkId,nxId,pdDate,
            ByNumber,ByCompanyId,WxNumber,WxCompanyId,AssetType,DepreciationId,Salvage,ReturnReasons,Remark,GetSign,GetQty,Date,Estate,Locks,Operator,brand) VALUES (
             NULL,'$TradeId','$GoodsId','$GoodsName','$Spec','','$TypeId','0','0','$unitname','0','0','0',
             '0','0','0','0','3','0','0.05','','','0','0','$Date','1','0','$Operator','$brand')";
                $inAction = @mysql_query($inRecode);
                //echo $inRecode, "<br>";
                $Id = mysql_insert_id();
                if ($inAction) {
                    //属性
                    //$inSql3="INSERT INTO $DataPublic.nonbom4_goodsproperty(Id,GoodsId,Property)VALUES(NULL,'$GoodsId','$Property[$k]')";
                    //$inRes3=@mysql_query($inSql3);

                    //写入库存表
                    $inRecode2 = "INSERT INTO $DataPublic.nonbom5_goodsstock (Id,GoodsId,wStockQty,lStockQty,oStockQty,mStockQty,CompanyId) VALUES 
                (NULL,'$GoodsId','0','0','0','0','$CompanyId')";
                    $inRes2 = @mysql_query($inRecode2);
                }else{
                    $OperationResult = "新增物料资料失败！";
                }
            }

            //新增申购单
            $sgDate = $sgDate == "" ? $DateTime : $sgDate;
            $Operator = $Login_P_Number;
            $sheetInSql = "INSERT INTO $DataIn.nonbom6_cgsheet (Id,Mid,fromMid,qkId,mainType,GoodsId,CompanyId,BuyerId,Qty,Price,AddTaxValue,Remark,ReturnReasons,rkSign,Estate,Locks,Date,Operator) VALUES 
                (NULL,'0',0,'0','$TypeId','$GoodsId','$CompanyId','10024','$newFactualQty','$newPrice','0','特采','','1','2','1','$sgDate','$Operator') ";
            $sheetInAction = @mysql_query($sheetInSql);
            if ($sheetInAction && mysql_affected_rows() > 0) {

            }else{
                $OperationResult = "新增采购单失败！";
            }

        } while ($myRow = mysql_fetch_array($result));
    }
}

echo $OperationResult;


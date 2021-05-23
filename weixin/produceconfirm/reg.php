<?php

session_start();

error_reporting(E_ALL & ~E_NOTICE);

include_once('../configure.php');

include_once('../log.php');

$Log_Item = "生产记录";            //需处理

$Log_Funtion = "保存";

$DateTime = date("Y-m-d H:i:s");

$Date = date("Y-m-d");

$Operator = $_SESSION['user_id'];

$OperationResult = "Y";

$fromAction = $_POST['fromAction'];

$POrderId = $_POST['POrderId'];

$sPOrderId = $_POST['sPOrderId'];

$StockId = $_POST['StockId'];

$mStockId = $_POST['mStockId'];

$DateTemp = date("Ymd");

$Tempyear = date("Y");
if ($fromAction == 1) {//成品登记页面

    $Relation = $_POST['Relation'];

    if ($Relation > 0) {

        $inRelation = "REPLACE INTO sc1_newrelation (Id,POrderId,Relation,Date,Operator) VALUES 
			(NULL,'$POrderId','1','$DateTime','$Operator')";

        $inAction = @mysql_query($inRelation);

    }

}

if (isset($_POST['Qty'])) {//传入了数量

    $Qty = $_POST['Qty'];

}
else {

    //没传入则获取数量
    $query = "select Qty from yw1_scsheet where POrderId='$POrderId' AND sPOrderId = '$sPOrderId'";

    $cursor = mysql_query($query);

    $row = mysql_fetch_row($cursor);

    $Qty = $row[0];

}
if ($fromAction == 1) {//成品登记页面

    $inRecode = "INSERT INTO sc1_cjtj (Id,GroupId,POrderId,sPOrderId,StockId,Qty,Remark,Date,Estate,Locks,Leader) VALUES 
(NULL,'$Operator','$POrderId','$sPOrderId','$StockId','$Qty','','$Date','1','0','$Operator')";
}
else {
    $inRecode = "INSERT INTO sc1_cjtj (Id,GroupId,POrderId,sPOrderId,StockId,Qty,Remark,Date,Estate,Locks,Leader) VALUES 
(NULL,'$Operator','$POrderId','$sPOrderId','$StockId','$Qty','','$Date','1','0','$Operator')";
}
$inAction = @mysql_query($inRecode);

if ($inAction) {

    $Log = "$TitleSTR 成功!<br>";

    $result = 1;

    $title = urlencode("登记成功");
    if ($fromAction == 2) {
        $shQty = $Qty;
        if ($shQty > 0 && $mStockId > 0 && $sPOrderId > 0) {
            $checkResult = mysql_fetch_array(mysql_query("
	                     SELECT D.SendFloor,D.StuffId,S.CompanyId 
	                     FROM $DataIn.cg1_stocksheet S
           	             LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
           	             WHERE S.StockId = $mStockId", $link_id));
            $myCompanyId = $checkResult["CompanyId"];
            $floor = $checkResult["SendFloor"];
            $StuffId = $checkResult["StuffId"];

            $maxBillResult = mysql_fetch_array(mysql_query("SELECT MAX(BillNumber) AS BillNumber FROM $DataIn.gys_shmain WHERE BillNumber  LIKE '$DateTemp%'", $link_id));
            $TempBillNumber = $maxBillResult["BillNumber"];
            if ($TempBillNumber) {

                $TempBillNumber = $TempBillNumber + 1;
            }
            else {
                $TempBillNumber = $DateTemp . "0001";//默认
            }

            $maxGysResult = mysql_fetch_array(mysql_query("SELECT MAX(GysNumber) AS GysNumber FROM $DataIn.gys_shmain WHERE GysNumber  LIKE '$Tempyear%' AND CompanyId = '$myCompanyId'", $link_id));
            $tempGysNumber = $maxGysResult["GysNumber"];
            if ($tempGysNumber) {

                $tempGysNumber = $tempGysNumber + 1;
            }
            else {
                $tempGysNumber = $Tempyear . "00001";//默认
            }

            if ($Mid == 0) {//如果没生成主送货单就先生成主送货单
                $inRecode = "INSERT INTO $DataIn.gys_shmain 
		      (Id,BillNumber,GysNumber,CompanyId,Locks,Date,Remark,Floor,Operator,creator,created) 
		      VALUES (NULL,'$TempBillNumber','$tempGysNumber','$myCompanyId','1','$DateTime','半成品入库','$floor','$Operator','$Operator',NOW())";
                $inAction = @mysql_query($inRecode);
                $Mid = mysql_insert_id();
            }

            if ($Mid > 0) {
                $addRecodes = "INSERT INTO $DataIn.gys_shsheet (Id,Mid,sPOrderId,StockId,StuffId,Qty,SendSign,Estate,Locks,Operator,creator,created) 
			    VALUES (NULL,'$Mid','$sPOrderId','$mStockId','$StuffId','$shQty','0','2','1','$Operator','$Operator',NOW())";
                $addAction = @mysql_query($addRecodes);
                if ($addAction) {
                    $saveSign = 1;
                    $updatesql = "update $DataIn.yw1_scsheet set Estate = '0' where sPOrderId='$sPOrderId' ";
                    @mysql_query($updatesql);
                }
            }
        }
        $msg = urlencode('如有疑问，请联系信息部人员</br>电话：13775147477');

        $IN_recode = "INSERT INTO oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";

        $IN_res = @mysql_query($IN_recode);
    }
}
else {

    $Log = $Log . "<div class=redB>$TitleSTR.$inRecode 失败!</div><br>";

    $result = 0;

    $OperationResult = "N";

    $title = urlencode("登记失败");

}

header("Location:msg.php?result=$result&title=$title&msg=$msg");

?>

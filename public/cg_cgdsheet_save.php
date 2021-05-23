<?php
$MyPDOEnabled=1;
include "../model/modelhead.php";
$Log_Item="特采单";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination&CompanyId=$CompanyId&Number=$Number";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
$TradeNo = $_REQUEST['TradeNo1'];
//步骤3：需处理
$DateTemp=date("Ymd");
//锁定表
include "../model/subprogram/FireFox_Safari_PassVar.php";

$POrderId=strlen($POrderId)>=12?$POrderId:'';

$oldLevel=$oldLevel==''?1:$oldLevel;

for($i=1;$i<=$RecordCount;$i++){
	$newStuffId=$StuffId[$i];			//配件ID
	$newPrice=$Price[$i];		//采购价格
	$newFactualQty=$FactualQty[$i];		//采购数量
	$CompanyId=$Company[$i];			//供应商
	$newAddRemark=$AddRemark[$i];

	if ($newFactualQty>0){
		$tempStr="$newPrice|$newAddRemark";
		$myResult=$myPDO->query("CALL proc_cg1_stocksheet_add('$POrderId',$newStuffId,$newFactualQty,'$tempStr','$oldLevel',$Operator);");
	    $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
	    $OperationResult = $myRow['OperationResult']!="Y"?$myRow['OperationResult']:$OperationResult;
        if (trim($OperationResult) == 'Y') {
            $sql = "INSERT INTO yw1_ordersheet ( `OrderNumber`, `POrderId`, `OrderPO`, `ProductId`, `Qty`, `Price`, `PackRemark`, `cgRemark`, `sgRemark`, `dcRemark`,`ShipType`, `creator`, `created` ) VALUES ( ( SELECT max(`OrderNumber`) + 1 FROM yw1_ordermain ), (select max(POrderId) from cg1_stocksheet ), '-', '0', '$newFactualQty', '$newPrice', '-', '-', '-','-','-', $Operator, '$DateTime' )";
            $link_id = mysql_connect($host, $user, $pass);
            mysql_query("SET NAMES 'utf8'");
            mysql_select_db($DataIn, $link_id) or die("无法选择数据库!");
            mysql_query($sql);
            $sql = "insert into yw1_ordermain (`CompanyId`,`OrderNumber`,`OrderDate`,`Operator`,`Estate`,`OrderPO`) VALUES ($TradeNo,(select max(`OrderNumber`) from yw1_ordersheet),'$Date',$Operator,1,'')";
            mysql_query($sql);
        }
	    $Log.=$OperationResult=="Y"?$myRow['OperationLog']:"<div class=redB>" .$myRow['OperationLog'] . "</div>";
	    $Log.="</br>";

	    $myResult=null; $myRow=null;
    }

}
include "../model/logpage.php";
?>

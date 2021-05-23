<?php 
/*电信-yang 20120801
二合一已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤2：
$Log_Item="经销商或其它公司";//需处理
$Log_Funtion="删除";
$Type=1;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$Ids="";
for($i=0;$i<=count($checkid);$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:$Ids.",".$Id;
		}
	}
	
//检查是否闲置，如果不是闲置，则不能删除
//$LockSql=" LOCK TABLES $DataPublic.dealerdata WRITE,$DataIn.companyinfo WRITE,$DataIn.linkmandata WRITE";$res=@mysql_query($LockSql);

echo "DELETE $DataPublic.dealerdata,$DataIn.companyinfo,$DataIn.linkmandata
	FROM $DataPublic.dealerdata
	LEFT JOIN $DataIn.companyinfo ON $DataPublic.dealerdata.CompanyId=$DataIn.companyinfo.CompanyId
	LEFT JOIN $DataIn.linkmandata ON $DataIn.companyinfo.CompanyId=$DataIn.linkmandata.CompanyId
	WHERE $DataPublic.dealerdata.Id IN ($Ids)";

$delResult = mysql_query("DELETE $DataPublic.dealerdata,$DataIn.companyinfo,$DataIn.linkmandata
	FROM $DataPublic.dealerdata
	LEFT JOIN $DataIn.companyinfo ON $DataPublic.dealerdata.CompanyId=$DataIn.companyinfo.CompanyId
	LEFT JOIN $DataIn.linkmandata ON $DataIn.companyinfo.CompanyId=$DataIn.linkmandata.CompanyId
	WHERE $DataPublic.dealerdata.Id IN ($Ids)",$link_id);
if($delResult){
	$Log="&nbsp;&nbsp;ID在( $Ids )的 $TitleSTR 成功.<br>";
	}
else{
	$Log="<div class='redB'>&nbsp;&nbsp;ID在( $Ids )的 $TitleSTR 失败!</div><br>";
	$OperationResult="N";
	}
//$sql="UNLOCK TABLES";$res=@mysql_query($sql);
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataPublic.dealerdata");
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.companyinfo");
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.linkmandata");

$ALType="From=$From&Orderby=$Orderby&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
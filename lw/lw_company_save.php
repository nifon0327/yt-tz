<?php 
include "../model/modelhead.php";
$Log_Item="劳务公司资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Company=FormatSTR($Company);
$Forshort=FormatSTR($Forshort);
$LinkMan=FormatSTR($LinkMan);
$Tel=FormatSTR($Tel);
$Address=FormatSTR($Address);
$Bank=FormatSTR($Bank);
$Remark=FormatSTR($Remark);

$maxSql = mysql_query("SELECT MAX(CompanyId) AS MaxCompanyId FROM $DataPublic.lw_company",$link_id);
$CompanyId=mysql_result($maxSql,0,"MaxCompanyId");
if($CompanyId){
	$CompanyId=$CompanyId+1;
	}
else{
	$CompanyId=50001;
	}


$inRecode="INSERT INTO $DataPublic.lw_company (Id,CompanyId,Company,Forshort,LinkMan,Tel,Address,Bank,Remark,Estate,Locks,
Date,Operator,PLocks,creator,created,modifier,modified) VALUES (NULL,'$CompanyId','$Company','$Forshort','$LinkMan','$Tel',
'$Address','$Bank','$Remark','1','0','$Date','$Operator','0','$Operator','$DateTime','$Operator','$DateTime')";
$inAction=@mysql_query($inRecode);
if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";

	} 
else{
	$Log="<div class=redB>$TitleSTR 失败!</div><br>";
	$OperationResult="N";
	} 
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

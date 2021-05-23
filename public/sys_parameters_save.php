<?php
include "../model/modelhead.php";
$Log_Item="系统参数";			
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


$pRow = mysql_fetch_array(mysql_query("SELECT MAX(PNumber) AS PNumber FROM $DataIn.sys6_parameters",$link_id));
$PNumber=$pRow["PNumber"];
if($PNumber){
	$PNumber=$PNumber+1;
	}
else{
	$PNumber=701;
	}
	
$inRecode="INSERT INTO $DataIn.sys6_parameters (Id,PNumber,ActionId,Name,pValue,Remark,Estate,Locks,Date,Operator,PLocks, creator,created,modifier,modified) VALUES (NULL,'$PNumber','$ActionId','$Name','$pValue','$Remark','1','0','$Date',
'$Operator','0','$Operator','$DateTime','$Operator','$DateTime')";
	$inAction=@mysql_query($inRecode);
	if ($inAction){ 
		$Log="$TitleSTR 成功!<br>";
		} 
	else{
		$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
		$OperationResult="N";
		}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

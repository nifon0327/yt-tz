<?php 
 ob_start();
include "../model/modelhead.php";
include "../basic/downloadFileIP.php";  //取得下载文档的远程
header("Content-Type: text/html; charset=gb2312");
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="产品资料";		//需处理
$upDataSheet="$DataIn.productdata";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
//echo "ActionId:$ActionId";
switch($ActionId){
	case 3:
	    $Log_Funtion="转发价格更新";
	    $SetStr="OutPrice='$OutPrice'";	
		$updateSQL = "UPDATE $upDataSheet SET $SetStr WHERE $upDataSheet.Id='$Id' ";
		echo "$updateSQL <br>";
		$updateResult = mysql_query($updateSQL);
		if ($updateResult && mysql_affected_rows()>0){
			$Log=$Log."&nbsp;&nbsp;ID号为 $Id 的 $ItemName $Log_Item 资料 $Log_Funtion 成功!<br>";
		}else{
			$Log=$Log."<div class=redB>&nbsp;&nbsp;ID号为 $Id 的 $ItemName $Log_Item 资料 $Log_Funtion 失败! $updateSQL </div><br>";
			$OperationResult="N";			
		}
		
	default:
			break;
		}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
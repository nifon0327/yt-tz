<?php 
//$DataIn.电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="图档业务初审";		//需处理
$upDataSheet="$DataIn.stuffverify";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
$delSql="DELETE FROM $upDataSheet WHERE Mid='$Id'";
$delResult=@mysql_query($delSql);
if($delSql){
     $Log.="图档业务初审取消成功!";
     }
else{
     $Log.="<div class='redB'>图档业务初审取消成功! $delSql<div>";
	 $OperationResult="N";
    }
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

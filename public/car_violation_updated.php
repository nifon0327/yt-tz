<?php 
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="车辆违规记录";		//需处理
$upDataSheet="$DataPublic.car_violation";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$x=1;
$SetStr="CarId='$CarId',Person='$Person',Charge='$Charge',Date='$Date',Reason='$Reason'";
include "../model/subprogram/updated_model_3a.php";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
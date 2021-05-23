<?php 
//电信-ZX
//代码、数据库合并后共享-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="外出记录";		//需处理
$upDataSheet="$DataPublic.info1_business";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
$Remark=FormatSTR($Remark);
$sCourses=$sCourses==""?0:$sCourses;
$eCourses=$eCourses==""?0:$eCourses;
$SetStr="Businesser='$Businesser',StartTime='$StartTime',EndTime='$EndTime',CarId='$CarId',Drivers='$Drivers',Remark='$Remark',sCourses='$sCourses',eCourses='$eCourses',Date='$DateTime',Operator='$Operator'";
include "../model/subprogram/updated_model_3a.php";

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
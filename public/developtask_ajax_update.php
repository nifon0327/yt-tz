<?php 
//电信---yang 20120801
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
//模板基本参数:模板$Login_WebStyle

ChangeWtitle("$SubCompany 所属配件对应关系更新");
$Log_Funtion="更新";
$Login_help="developtask_ajax_update";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
$ALType="";
$x=1;
$y=1;
//echo "Action";
$OperationResult="N";
switch ($Action){
	case "jq":
             $sql = "UPDATE $DataIn.developsheet  SET Relation='$Relation' WHERE Id='$sId'";
             $result = mysql_query($sql);
	break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=mysql_query($IN_recode);	
?>
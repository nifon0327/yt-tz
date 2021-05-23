<?php   
//电信-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_remark";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="标准图备注";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
$delsql="delete from $DataIn.test_remark where ProductId='$ProductId'";
$delResult=mysql_query($delsql,$link_id);
if($TestRemark!=""){
		$Date=date("Y-m-d");
		$sql = "INSERT INTO $DataIn.test_remark (Id,ProductId,Remark)VALUES (NULL,'$ProductId','$TestRemark')";
		$result = mysql_query($sql);
		if ($result){
			$Log="&nbsp;&nbsp;ID号为 $Id 产品标准图备注加入成功!<br>";
			}
		else{
			$Log="<div class=redB>&nbsp;&nbsp;ID号为 $Id 产品标准图备注加入成功!</div><br>";
			$OperationResult="N";
			}
     }
$ALType="chooseDate=$chooseDate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
  
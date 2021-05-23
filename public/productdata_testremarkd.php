<?php 
//$DataPublic.net_cpcheckdiary二合一已更新电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_ts";
$nowWebPage=$funFrom."_testremarkd";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="产品标准图备注";		//需处理
$upDataSheet="$DataIn.test_remark";	//需处理
$Log_Funtion="修改";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
if($ActioToS=91){
$checkRemark="select Id,ProductId,Remark FROM $DataIn.test_remark WHERE ProductId='$ProductId'";
$RemarkResult=mysql_fetch_array(mysql_query($checkRemark));
//$ProductId2=$checkRemark["ProductId"];

if($RemarkResult==""){
$insert="INSERT INTO $DataIn.test_remark (Id,ProductId,Remark) VALUES (NULL,'$ProductId','$Remark')";
$inAction=@mysql_query($insert);
}
else{
$updateRemark="update $DataIn.test_remark set Remark='$Remark' where ProductId='$ProductId'";
$inAction=@mysql_query($updateRemark);
}
if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	$updateStandard="update $DataIn.productdata set TestStandard=4  where ProductId='$ProductId'";
	$upresult=@mysql_query($updateStandard);
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败! $updateRemark</div><br>";
	$OperationResult="N";
	} 

}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
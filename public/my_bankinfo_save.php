<?php 
//电信---yang 20120801
//代码、数据库合并后共享-EWEN
include "../model/modelhead.php";
//步骤2：
$Log_Item="公司收款帐号资料";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

$Title=FormatSTR($Title);
$Beneficary=FormatSTR($Beneficary);
$Bank=FormatSTR($Bank);
$BankAdd=FormatSTR($BankAdd);
$SwiftID=FormatSTR($SwiftID);
$ACNO=FormatSTR($ACNO);

$bankLogoPath ="../download/banklogo/";
if(!file_exists($bankLogoPath)){
		makedir($bankLogoPath);
} 

$IN_recode="INSERT INTO $DataPublic.my2_bankinfo (Id,cSign,Title,Beneficary,Bank,BankAdd,SwiftID,ACNO,CnapsCode,Locks,creator,created,Date,Operator) VALUES 
		(NULL,'$cSign','$Title','$Beneficary','$Bank','$BankAdd','$SwiftID','$ACNO','$CnapsCode','0','$Operator','$DateTime','$Date','$Operator')";
$res=@mysql_query($IN_recode);
$newId=mysql_insert_id();
if($res){
	    $Log="$TitleSTR 成功. <br>";
	    //上传公司logo
		if($Logo!=""){
			$newLogoFileName="newbank_".$newId.".png";
			$uploadInfo=UploadPictures($Logo,$newLogoFileName,$bankLogoPath);
		}
	}
else{
	$Log="<div class='redB'>$TitleSTR 失败.</div>$IN_recode<br>";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

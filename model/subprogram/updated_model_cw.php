<?php 
//二合一已更新
$PreFileName1="P".$Mid.".jpg";
$FilePath="../download/$FileDir/";
if(!file_exists($FilePath)){
	makedir($FilePath);
	}
if($Payee!=""){
	$OldFile1=$Payee;
	$uploadInfo1=UploadFiles($OldFile1,$PreFileName1,$FilePath);				
	$PayeeSTR=$uploadInfo1==""?",Payee='0'":",Payee='1'";
	}
if($PayeeSTR=="" && $oldPayee!=""){//没有上传文件并且已选取删除原文件
	$FilePath1=$FilePath."$PreFileName1";
	if(file_exists($FilePath1)){
		unlink($FilePath1);
		}
	$PayeeSTR=",Payee='0'";
	}
	
$PreFileName2="R".$Mid.".jpg";
if($Receipt!=""){
	$OldFile2=$Receipt;
	$uploadInfo2=UploadFiles($OldFile2,$PreFileName2,$FilePath);
	$ReceiptSTR=$uploadInfo2==""?",Receipt='0'":",Receipt='1'";
	}

if($ReceiptSTR=="" && $oldReceipt!=""){//没有上传文件并且已选取删除原文件
	$FilePath2=FilePath."$PreFileName2";
	if(file_exists($FilePath2)){
		unlink($FilePath2);
		}
	$ReceiptSTR=",Receipt='0'";
	}
	
$PreFileName3="C".$Mid.".jpg";
if($Checksheet!=""){
	$OldFile3=$Checksheet;
	$uploadInfo3=UploadFiles($OldFile3,$PreFileName3,$FilePath);
	$ChecksheetSTR=$uploadInfo3==""?",Checksheet='0'":",Checksheet='1'";
	}
if($ChecksheetSTR=="" && $oldChecksheet!=""){//没有上传文件并且已选取删除原文件
	$FilePath3=$FilePath."/$PreFileName3";
	if(file_exists($FilePath3)){
		unlink($FilePath3);
		}
	$ChecksheetSTR=",Checksheet='0'";
	}

$Locks=$Locks==1?",Locks='1'":",Locks='0'";
$PayDateSTR=$PayDate!=""?",PayDate='$PayDate'":"";
$BankSTR=$BankId!=""?",BankId='$BankId'":"";
$Remark=addslashes(FormatSTR($Remark));
$upSql="UPDATE $upDataMain SET Date='$DateTime',Remark='$Remark' $PayeeSTR $ReceiptSTR $ChecksheetSTR $Locks $PayDateSTR $BankSTR WHERE Id=$Mid";
$upResult = mysql_query($upSql);
if($upResult){
	$Log="$Log_Funtion 成功！$upSql <br>";
	}
else{
	$Log="<div class='redB'>$Log_Funtion 失败！$upSql </div><br>";
	$OperationResult="Y";
	}
?>
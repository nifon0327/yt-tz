<?php 
//电信---yang 20120801
//代码共享-EWEN
include "../model/modelhead.php";
//步骤2：
$Log_Item="供应商资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
if($Prov!=$City){
	$Area=$Prov."|".$City;}
else{
	$Area=$City;
	}
//记录字段值
$Type=3;
$chinese=new chinese;
$Letter=substr($chinese->c($Forshort),0,1);
$Company=FormatSTR($Company);
$Forshort=FormatSTR($Forshort);
$Tel=FormatSTR($Tel);
$Fax=FormatSTR($Fax);
$Area=FormatSTR($Area);
$Website=FormatSTR($Website);
$ZIP=FormatSTR($ZIP);
$Address=FormatSTR($Address);
$Bank=FormatSTR($Bank);
$Judge="";
//$Judge=FormatSTR($Judge);
$Remark=FormatSTR($Remark);
$Currency=$Currency;

$Legalperson=FormatSTR($Legalperson);
$Description=FormatSTR($Description);
$Aptitudes=FormatSTR($Aptitudes);
$EAQF=FormatSTR($EAQF);
$FscNo=FormatSTR($FscNo);
$Quality=0;
$Normative=0;
$Effect=0;
$Qos=0;
$Results=0;

//默认联系人信息
$Linkman=FormatSTR($Linkman);
$Nickname=FormatSTR($Nickname);
$Headship=FormatSTR($Headship);
$Mobile=FormatSTR($Mobile);
$Tel2==FormatSTR($Tel2);
$Email=FormatSTR($Email);
$Remark2=FormatSTR($Remark2);
$Defaults=0;
$Date=date("Y-m-d");

$ProviderPhotoPath="../download/providerfile/";
if(!file_exists($ProviderPhotoPath)){
		makedir($ProviderPhotoPath);
}

//锁定表
//$LockSql=" LOCK TABLES $DataIn.trade_object WRITE";$LockRes=@mysql_query($LockSql);
$maxSql = mysql_query("SELECT MAX(CompanyId) AS Mid FROM $DataIn.trade_object ORDER BY CompanyId DESC LIMIT 1",$link_id);
$CompanyId=mysql_result($maxSql,0,"Mid");
if($CompanyId){
	$CompanyId=$CompanyId+1;
	}
else{
	$CompanyId=2001;
	}
	
if($BusinessLicence!=""){
	$OldFile1=$BusinessLicence;
	$PreFileName1="B".$CompanyId.".jpg";
	$uploadInfo1=UploadPictures($OldFile1,$PreFileName1,$ProviderPhotoPath);
}
$upValue1=$uploadInfo1==""?0:1;

if($TaxCertificate!=""){
	$OldFile2=$TaxCertificate;
	$PreFileName2="T".$CompanyId.".jpg";
	$uploadInfo2=UploadPictures($OldFile2,$PreFileName2,$ProviderPhotoPath);
}
$upValue2=$uploadInfo2==""?0:1;

if($ProductionCertificate!=""){
	$OldFile3=$ProductionCertificate;
	$PreFileName3="P".$CompanyId.".jpg";
	$uploadInfo3=UploadPictures($OldFile3,$PreFileName3,$ProviderPhotoPath);
}
$upValue3=$uploadInfo3==""?0:1;
	


$inRecode="INSERT INTO $DataIn.trade_object (Id,cSign,CompanyId,Letter,Forshort,PayMode,GysPayMode,Currency,ExpNum,PayType,BankId,CompanySign,PriceTerm,Staff_Number,PickNumber,FscNo,ProviderType,Prepayment,Judge,LimitTime,PackFile,TipsFile,OrderBy,ObjectSign,Estate,Locks,Date,Operator)VALUES (NULL,'0','$CompanyId','$Letter','$Forshort','$PayMode','$GysPayMode','$Currency','','0','0','0','','0','0','$FscNo','$ProviderType','0','$Judge','$LimitTime','0','0','0','3','1','0','$Date','$Operator')";
$inAction=@mysql_query($inRecode);



//解锁表
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
if ($inAction){ 
	$Log="主表 $TitleSTR 成功!<br>";
	$infoRecode="INSERT INTO $DataIn.companyinfo (Id,Type,CompanyId,Company,Tel,Fax,Area,Website,Address,ZIP,Bank,Remark) VALUES (NULL,'$Type','$CompanyId','$Company','$Tel','$Fax','$Area','$Website','$Address','$ZIP','$Bank','$Remark')";
	$infoAction=@mysql_query($infoRecode);
	$Log.="从表 $TitleSTR 成功!<br>";
        
	$sheetRecode="INSERT INTO $DataIn.providersheet  (Id,CompanyId ,LegalPerson,BLdate ,TRCdate ,PLdate,BusinessLicence,TaxCertificate,ProductionCertificate,Description,Aptitudes,EAQF,Quality,Normative,Effect,Qos,Results) VALUES (NULL,'$CompanyId','$LegalPerson','$BLdate','$TRCdate','$PLdate','$upValue1','$upValue2','$upValue3','$Description','$Aptitudes','$EAQF','$Quality','$Normative','$Effect','$Qos','$Results')";
	$sheetAction=@mysql_query($sheetRecode);
	$Log.="其它信息表 $TitleSTR 成功!<br>";
        
	//if($Linkman!=""){
		$LinkmanRecode="INSERT INTO $DataIn.linkmandata (Id,CompanyId,Name,Sex,Nickname,Headship,Mobile,Tel,MSN,SKYPE,Email,Remark,Date,Defaults,Type,Estate,Locks,Operator) VALUES (NULL,'$CompanyId','$Linkman','$Sex','$Nickname','$Headship','$Mobile','$Tel2','$MSN','$SKYPE','$Email','$Remark2','$Date','0','$Type','1','0','$Operator')";
		$LinkmanRes=@mysql_query($LinkmanRecode);
		if($LinkmanRes){
			$Log.="&nbsp;&nbsp;供应商 $Forshort 的默认联系人资料新增成功！";
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;供应商 $Forshort 的默认联系人资料新增失败！$LinkmanRecode</div>";
			$OperationResult="N";
			}
		//}
	} 
else{
	$Log="<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
	$OperationResult="N";
	} 
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

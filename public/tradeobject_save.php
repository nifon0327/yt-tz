<?php 
//电信---yang 20120801
//代码共享-EWEN
include "../model/modelhead.php";
//步骤2：
$Log_Item="交易对象资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination&ObjectSign=$ObjectSign";

//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
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
$BankUID=FormatSTR($BankUID);
$BankAccounts=FormatSTR($BankAccounts);

$IBAN=FormatSTR($IBAN);
$Judge="";
//$Judge=FormatSTR($Judge);
$Remark=FormatSTR($Remark);
$Currency=$Currency;

switch($ObjectSign){
	case 2://客人
	 $ProviderType=-1;
	 $GysPayMode=99;
	 $InvoiceTax=0;
	 $LimitTime=0;
	 $LegalPerson="";
	 $FscNo="";
	 $BusinessLicence="";
	 $TaxCertificate="";
	 $BankPermit="";
	 $SalesAgreement="";
	 $PaymentOrder="";
	 $ProductionCertificate="";
	 $Description="";
	 $Aptitudes="";
	 $EAQF="";
	 break;
    case 3://供应商
     $PayType=0;
     $PayMode=0;
     $SaleMode=0;
     $BankId=0;
     $PriceTerm="";
     $ChinaSafeSign=0;
     $ChinaSafe="0";
     break;
    default:
     $InvoiceTax=$InvoiceTax==""?0:$InvoiceTax;
	 $Legalperson=FormatSTR($Legalperson);
	 $Description=FormatSTR($Description);
	 $Aptitudes=FormatSTR($Aptitudes);
	 $EAQF=FormatSTR($EAQF);
	 $FscNo=FormatSTR($FscNo);
     break;
}


$Quality=0;
$Normative=0;
$Effect=0;
$Qos=0;
$Results=0;
$LimitTime=0;

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

$tradeLogoPath ="../download/tradelogo/";
if(!file_exists($tradeLogoPath)){
		makedir($tradeLogoPath);
} 


$maxSql = mysql_query("SELECT MAX(CompanyId) AS Mid FROM $DataIn.trade_object WHERE CompanyId>100000",$link_id);
$CompanyId=mysql_result($maxSql,0,"Mid");
if($CompanyId){
	 $CompanyId=$CompanyId+1;
	}
else{
	  $CompanyId=100001;
	}

//上传公司logo
if($Logo!=""){
	$newLogoFileName="L".$CompanyId.".png";
	$uploadInfo0=UploadPictures($Logo,$newLogoFileName,$tradeLogoPath);
}
$LogoPng=$uploadInfo0==""?"":$newLogoFileName;


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

if($BankPermit!=""){
	$OldFile4=$BankPermit;
	$PreFileName4="K".$CompanyId.".jpg";
	$uploadInfo4=UploadPictures($OldFile4,$PreFileName4,$ProviderPhotoPath);
}
$upValue4=$uploadInfo4==""?0:1;

if($SalesAgreement!=""){
	$OldFile5=$SalesAgreement;
	$PreFileName5="S".$CompanyId.".jpg";
	$uploadInfo5=UploadPictures($OldFile5,$PreFileName5,$ProviderPhotoPath);
}
$upValue5=$uploadInfo5==""?0:1;

if($PaymentOrder!=""){
	$OldFile6=$PaymentOrder;
	$PreFileName6="O".$CompanyId.".jpg";
	$uploadInfo6=UploadPictures($OldFile6,$PreFileName6,$ProviderPhotoPath);
}
$upValue6=$uploadInfo6==""?0:1;

if($TaxpayerIdentifi!=""){
	$OldFile7=$PaymentOrder;
	$PreFileName7="TI".$CompanyId.".jpg";
	$uploadInfo7=UploadPictures($OldFile7,$PreFileName7,$ProviderPhotoPath);
}
$upValue7=$uploadInfo7==""?0:1;
$CompanySign = 1;
$tempCount=count($TempCompanySign);
for($k=0;$k<$tempCount;$k++){
    if($TempCompanySign[$k]>0){
        $CompanySign = $CompanySign * $TempCompanySign[$k];
    }
}

$GysPayMode = $GysPayMode ==""?0:$GysPayMode;//客供供应商

$CompanySign = $CompanySign ==1?$ASH_CONFIG['COMPANY_CSIGN_2']:$CompanySign;
$Type=8;
$ErrorUd = false;
@mysql_query('BEGIN');
$inRecode="INSERT INTO $DataIn.trade_object (Id,cSign,CompanyId,Letter,Forshort,Logo,PayMode,GysPayMode,SaleMode,Currency,
ExpNum,PayType,BankId,CompanySign,PriceTerm,ChinaSafeSign,ChinaSafe,Staff_Number,PickNumber,FscNo,ProviderType,Prepayment,
Judge,LimitTime,PackFile,TipsFile,OrderBy,ObjectSign,UpdateReasons,ReturnReasons,Estate,Locks,Date,Operator)VALUES (NULL,'0','$CompanyId','$Letter','$Forshort','$LogoPng','$PayMode','$GysPayMode','$SaleMode','$Currency','$ExpNum','$PayType',
'$BankId','$CompanySign','$PriceTerm','$ChinaSafeSign','$ChinaSafe','$Staff_Number','0','$FscNo','$ProviderType','0','$Judge','$LimitTime','0','0','0','$ObjectSign','','','2','0','$Date','$Operator')";
$inAction=@mysql_query($inRecode);
$ErrorUd = mysql_errno($link_id)?true:false;
//获取trade_object.id by ckt 2017-12-27
$TradeId = @mysql_insert_id($link_id);

if ($inAction){ 
	   $Log="主表 trade_object  $TitleSTR 成功!<br>";
	   $infoRecode="INSERT INTO $DataIn.companyinfo (Id,Type,CompanyId,Company,Tel,Fax,Area,Website,Address,
	   ZIP,Bank,BankUID,BankAccounts,IBAN,Remark) VALUES (NULL,'$Type','$CompanyId','$Company','$Tel','$Fax','$Area',
	   '$Website','$Address','$ZIP','$Bank','$BankUID','$BankAccounts','$IBAN','$Remark')";
	    $infoAction=@mysql_query($infoRecode);
		$ErrorUd = mysql_errno($link_id)?true:false;
	    if($infoAction && mysql_affected_rows()>0){
                $Log.="2--交易对象$Forshort 子表companyinfo保存成功!<br>";
            }
        else{
			 $Log.="<div class=redB>2--交易对象$Forshort 子表companyinfo 保存失败！</br>$infoRecode</div>"."<div class=redB>".mysql_error($link_id)."</div>";
            }
        $AddValueTax = $AddValueTax==""?0:$AddValueTax;
        $BulidTime  = $BulidTime ==""?"0000-00-00":$BulidTime;
        $CompanyNature = $CompanyNature==""?0:$CompanyNature;
        $CompanyCategory = $CompanyCategory==""?0:$CompanyCategory;
        $InvoiceTax=$InvoiceTax==""?0:$InvoiceTax;
	    $sheetRecode="INSERT INTO $DataIn.providersheet(Id,CompanyId,LegalPerson,BulidTime,ValidTime,Capital,CompanySize,StaffNum,
	    CompanyNature,CompanyCategory,BLdate ,TRCdate ,PLdate,BusinessLicence,TaxCertificate,ProductionCertificate,BankPermit,
	    SalesAgreement,PaymentOrder,TaxpayerIdentifi,AddValueTax,InvoiceTax,Description,Aptitudes,EAQF,Quality,Normative,Effect,
        Qos,Results,CompanyPicture,MainBusiness,DealRange,Estate,Locks,Date,Operator) VALUES  (NULL,'$CompanyId','$LegalPerson',
        '$BulidTime','$ValidTime','$Capital','$CompanySize','$StaffNum','$CompanyNature','$CompanyCategory',
        '$BLdate','$TRCdate','$PLdate','$upValue1','$upValue2','$upValue3','$upValue4','$upValue5','$upValue6','$upValue7',
        '$AddValueTax','$InvoiceTax','$Description','$Aptitudes','$EAQF','$Quality','$Normative','$Effect','$Qos','$Results',
        '$CompanyPicture','$MainBusiness','$DealRange','1','0','$Date','$Operator')";
	     $sheetAction=@mysql_query($sheetRecode);
		 $ErrorUd = mysql_errno($link_id)?true:false;
	     if($infoAction && mysql_affected_rows()>0){
                $Log.="3--交易对象$Forshort 子表providersheet保存成功!$sheetRecode<br>";
             }
          else{
			 $Log.="<div class=redB>3--交易对象$Forshort 子表providersheet 保存失败！</br>$sheetRecode</div>"."<div class=redB>".mysql_error($link_id)."</div>";
             }
        
          $LinkmanRecode="INSERT INTO $DataIn.linkmandata (Id,CompanyId,Name,Sex,Nickname,Headship,Mobile,Tel,
          MSN,SKYPE,Email,Remark,Date,Defaults,Type,Estate,Locks,Operator) VALUES (NULL,'$CompanyId','$Linkman','$Sex','$Nickname',
          '$Headship','$Mobile','$Tel2','$MSN','$SKYPE','$Email','$Remark2','$Date','0','$Type','1','0','$Operator')";
	   	  $LinkmanRes=@mysql_query($LinkmanRecode);
		  $ErrorUd = mysql_errno($link_id)?true:false;
		  if($LinkmanRes){
			  $Log.="4--交易对象 $Forshort 的默认联系人资料新增成功！<br>";
			}
		else{
			$Log.="<div class=redB>4--交易对象 $Forshort 的默认联系人资料新增失败！</br>$LinkmanRecode</div>"."<div class=redB>".mysql_error($link_id)."</div>";
			}

        if($ObjectSign==1 || $ObjectSign==2){//客户，或者即是客户又是供应商            //生成PI模板
             $piRecode="INSERT INTO  $DataIn.yw3_pimodel (Id, CompanyId, Model, Estate, Locks, Date, Operator) 
             VALUES (NULL, '$CompanyId', '$CompanyId', '1', '0', '$Date','$Operator')";
	          $piAction=@mysql_query($piRecode);
			  $ErrorUd = mysql_errno($link_id)?true:false;
			  if($piAction){
				  $Log.="5--交易对象 $Forshort 的PI模板资料新增成功！<br>";
				}
			else{
				$Log.="<div class=redB>5--交易对象 $Forshort 的PI模板资料新增失败！</br>$piRecode</div>"."<div class=redB>".mysql_error($link_id)."</div>";
				}
			//trade_info 写入 by ckt 2017-12-27	
			$TradeNo = FormatSTR($TradeNo);
			$Members = FormatSTR($Members);
			$CmptTotal = FormatSTR($CmptTotal);
			$TradeInfoRecode = "insert into $DataIn.trade_info(TradeId, TradeNo, Proofreader, Proofreader1, Checker, Members, Producer, CmptTotal)
			values('$TradeId','$TradeNo','$Proofreader','$Proofreader1','$Checker','$Members','$Producer','$CmptTotal')";
			$TradeInfoRes = @mysql_query($TradeInfoRecode);
			$ErrorUd = mysql_errno($link_id)?true:false;
			if($TradeInfoRes){
				$Log.= "6--交易对象 $Forshort 的默认项目资料新增成功！";
			}else{
				$Log.="<div class=redB>6--交易对象 $Forshort 的默认项目资料新增失败！</br>$TradeInfoRecode</div>"."<div class=redB>".mysql_error($link_id)."</div>";
			}
	   }
		
	} 
else{
	     $Log="<div class=redB>$TitleSTR 失败! </br>$inRecode </div>"."<div class=redB>".mysql_error($link_id)."</div>";
	     $OperationResult="N";
	} 
if($ErrorUd){
	@mysql_query('ROLLBACK');
	$Log.="<div class=greenB>数据存储错误，已回滚！</div>";
}else{
	@mysql_query('COMMIT');
}	
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

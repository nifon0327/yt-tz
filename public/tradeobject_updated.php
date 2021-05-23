<?php
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage;
//步骤2：
$Log_Item="交易对象资料";		//需处理
$upDataSheet="$DataIn.trade_object";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$ProviderPhotoPath="../download/providerfile/";
$Type=8;
$ErrorUd = false;
@mysql_query('BEGIN');

switch($ActionId){
	case -1: //更新供应商对应的图片职责人
		$JNField=explode("|",$PicJobid);
		$Pjobid=$JNField[0];
		$PicNumber=$JNField[1];
		//echo "$CompanyId:$Pjobid:$PicNumber";
		if ($Pjobid==0 || $PicNumber==0){
			$Pjobid=-1;
		}

		$sql = "UPDATE $DataIn.stuffdata 
				SET Pjobid='$Pjobid',PicNumber='$PicNumber' WHERE Estate>0 AND  StuffId IN (select StuffId  from  $DataIn.bps  where CompanyId='$CompanyId' )";
		//echo "$sql";

		$result = mysql_query($sql,$link_id);
		$ErrorUd = mysql_errno($link_id)?true:false;
		if($result){
			$Log.="供应商ID号为:$CompanyId 传图职责人为:$PicNumber </br>";
			}
		else{
			$Log.="供应商ID号为:$CompanyId 传图职责人为:$PicNumber  失败 </br>";
			$Log.="<div class=redB>$sql</br>".mysql_error($link_id)."</div>";
			$OperationResult="N";
			}
	break;

	case 5:				//则联系人，联系人的帐号可用
		$Log_Funtion="可用";
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if($Id!=""){
				$Ids=$Ids==""?$Id:($Ids.",".$Id);
				}
			}
		$sql = "UPDATE $upDataSheet A 
				LEFT JOIN $DataIn.linkmandata B ON B.CompanyId=A.CompanyId AND B.Type='$Type'
				LEFT JOIN $DataIn.usertable C ON C.Number=B.Id AND C.uType='$Type'
				SET A.Estate='1',A.Locks='0',B.Estate='1',B.Locks='0',C.Estate='1',C.Locks='0' WHERE A.Id IN ($Ids) AND A.Estate=0";
		$result = mysql_query($sql);
		$ErrorUd = mysql_errno($link_id)?true:false;
		if($result & mysql_affected_rows()>0){
			$Log.="ID号在 $Ids 的 $Log_Item 设为可用成功!</br>";
			}
		else{
			$Log.="<div class='redB'>ID号在 $Ids 的 $Log_Item 设为可用失败!是否在审核状态?</div>";
			$Log.="<div class=redB>$sql</br>".mysql_error($link_id)."</div>";
			$OperationResult="N";
			}
		break;
	case 6:
		$Log_Funtion="禁用";
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if($Id!=""){
				$Ids=$Ids==""?$Id:($Ids.",".$Id);
				}
			}
		$sql = "UPDATE $upDataSheet A
				LEFT JOIN $DataIn.linkmandata B ON B.CompanyId=A.CompanyId AND B.Type='$Type'
				LEFT JOIN $DataIn.usertable C ON C.Number=B.Id AND C.uType='$Type'
				SET A.Estate='0',A.Locks='0',B.Estate='0',B.Locks='0',
				C.Estate='0',C.Locks='0'
				WHERE A.Id IN ($Ids)";
		$result = mysql_query($sql);
		$ErrorUd = mysql_errno($link_id)?true:false;
		if($result){
			$Log.="ID号在 $Ids 的 $Log_Item 设为禁用成功!</br>";
			}
		else{
			$Log.="<div class='redB'>ID号在 $Ids 的 $Log_Item 设为禁用失败! </div>";
			$Log.="<div class=redB>$sql</br>".mysql_error($link_id)."</div>";
			$OperationResult="N";
			}

		//连出货模板也禁用掉
		$sql = "UPDATE $DataIn.ch8_shipmodel B
				LEFT JOIN $upDataSheet A ON A.CompanyId=B.CompanyId 
				SET B.Estate='0',B.Locks='0'
				WHERE A.ObjectSign=2 AND A.Id IN ($Ids)";
		//echo "$sql";
		$result = mysql_query($sql);
		$ErrorUd = mysql_errno($link_id)?true:false;
		if($result){
			$Log.="trade_object.ID号在 $Ids 的模板设为禁用成功!</br>";
			}
		else{
			$Log.="<div class='redB'>ID号在 $Ids 的模板设为禁用失败!</div>";
			$Log.="<div class=redB>$sql</br>".mysql_error($link_id)."</div>";
			$OperationResult="N";
			}
		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	case 17://审核通过
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if($Id!=""){
	    		$Ids=$Ids==""?$Id:($Ids.",".$Id);
	    		}
	  		}
		$updatesql="UPDATE $DataIn.trade_object SET ReturnReasons='', UpdateReasons='',Estate=1 WHERE Id IN ($Ids)";
		$result = mysql_query($updatesql);
		$ErrorUd = mysql_errno($link_id)?true:false;
		if($result){
			       $Log="<p>交易对象 $Ids 的资料审核成功.";
			}
		else{
			       $Log.="<div class='redB'>交易对象 $Ids 的资料审核失败.</div>";
				   $Log.="<div class=redB>$updatesql</br>".mysql_error($link_id)."</div>";
			       $OperationResult="N";
			}
		$fromWebPage=$funFrom."_m";
		break;

	case 15:
			$Log_Funtion="审核退回";			$SetStr="ReturnReasons='$ReturnReasons',UpdateReasons='',Estate=4";		include "../model/subprogram/updated_model_3d.php";
		     $fromWebPage=$funFrom."_m";
		break;

   case 109:

       break;
   case 87:
       $Log_Funtion="上传文件";
       $FilePath="../download/providerfile/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
		}
        //上传文档
          if ($PackFile!=""){
                            $FileType=".png";
                            $PreFileName="Pack_$CompanyId". $FileType;

                            $uploadInfo=UploadFiles($PackFile,$PreFileName,$FilePath);
                            if($uploadInfo!=""){
                                $upRecode="UPDATE $DataIn.trade_object SET PackFile='1' WHERE Id='$Id'";
                                //echo $inRecode;
                                $upAction=@mysql_query($upRecode,$link_id);
								$ErrorUd = mysql_errno($link_id)?true:false;
                                if($upAction){
                                        $Log.="交易对象 $CompanyId 送货胶框图 $uploadInfo 上传成功.<br>";
                                 }
                                else{
                                        $Log.="<div class='redB'>交易对象 $CompanyId 送货胶框图 $uploadInfo 上传失败</div>";
                                        $Log.="<div class=redB>$upRecode</br>".mysql_error($link_id)."</div>";
										$OperationResult="N";
                                        }
                                }
                 }
                 if ($TipsFile!=""){
                            $FileType=".png";
                            $PreFileName="Tips_$CompanyId". $FileType;

                            $uploadInfo=UploadFiles($TipsFile,$PreFileName,$FilePath);
                            if($uploadInfo!=""){
                                $upRecode="UPDATE $DataIn.trade_object SET TipsFile='1' WHERE Id='$Id'";
                                //echo $inRecode;
                                $upAction=@mysql_query($upRecode,$link_id);
								$ErrorUd = mysql_errno($link_id)?true:false;
                                if($upAction){
                                        $Log.="交易对象 $CompanyId 提示图 $uploadInfo 上传成功.<br>";
                                 }
                                else{
                                        $Log.="<div class='redB'>交易对象 $CompanyId 提示图  $uploadInfo 上传失败</div>";
										$Log.="<div class=redB>$upRecode</br>".mysql_error($link_id)."</div>";
										$OperationResult="N";
                                        }
                                }
                 }


            //******logo
			$tradeLogoPath ="../download/tradelogo/";
			if(!file_exists($tradeLogoPath)){
					makedir($tradeLogoPath);
			}

			if($Logo!=""){
				$newLogoFileName="L".$CompanyId.".png";
				$uploadInfo0=UploadPictures($Logo,$newLogoFileName,$tradeLogoPath);
				if($uploadInfo0!=""){
                    $upLogoSql="UPDATE $DataIn.trade_object SET Logo='$newLogoFileName' 
                                WHERE Id='$Id'";

                    $upLogoAction=mysql_query($upLogoSql,$link_id);
					$ErrorUd = mysql_errno($link_id)?true:false;
                    if($upLogoAction){
                            $Log.="交易对象 $CompanyId 的Logo $uploadInfo0 上传成功.<br>";
                     }
                    else{
                           $Log.="<div class='redB'>交易对象 $CompanyId 的Logo $uploadInfo0 上传失败</div>";
                           $Log.="<div class=redB>$upLogoSql</br>".mysql_error($link_id)."</div>";
						   $OperationResult="N";
                        }
                  }
			}

     break;
	default:
	     $Type =8;
	     if($Forshort==""){
			 return false;
			 break;
		 }
		$tradeLogoPath ="../download/tradelogo/";
		if(!file_exists($tradeLogoPath)){
				makedir($tradeLogoPath);
		}
		//记录字段值
		$chinese=new chinese;
		$Letter=substr($chinese->c($Forshort),0,1);
		$Company=FormatSTR($Company);
		$Forshort=FormatSTR($Forshort);
		$FscNo=FormatSTR($FscNo);
		$Tel=FormatSTR($Tel);
		$Fax=FormatSTR($Fax);
		$Area=FormatSTR($Area);
		$Website=FormatSTR($Website);
		$ZIP=FormatSTR($ZIP);
		$Address=FormatSTR($Address);
		$Bank=FormatSTR($Bank);
		$BankUID=FormatSTR($BankUID);
        $BankAccounts=FormatSTR($BankAccounts);
		$Judge=FormatSTR($Judge);

		$Currency=$Currency;
		switch($ObjectSign){
		case 2://客人
		 $ProviderType=-1;
		 $GysPayMode=99;
		 $InvoiceTax=0;
		 $AddValueTax=0;
		 $LimitTime=0;
		 $LegalPerson="";
		 $FscNo="";
		 $BusinessLicence="";
		 $TaxCertificate="";
		 $BankPermit="";
		 $TaxpayerIdentifi="";
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


		//默认联系人信息
		$Linkman=FormatSTR($Linkman);
		$Nickname=FormatSTR($Nickname);
		$Headship=FormatSTR($Headship);
		$Mobile=FormatSTR($Mobile);
		$Tel2==FormatSTR($Tel2);
		$Email=FormatSTR($Email);
		$Remark=FormatSTR($Remark);
		$Remark2=FormatSTR($Remark2);
        $UpdateReasons=FormatSTR($UpdateReasons);
		$Defaults=0;
		$Date=date("Y-m-d");

		$CompanyIdResult = mysql_query("SELECT CompanyId FROM $upDataSheet A  
		WHERE A.Id='$Id'",$link_id);
		if($CompanyIdRow= mysql_fetch_array($CompanyIdResult)) {
				$CompanyId=$CompanyIdRow["CompanyId"];

				if ($GysPayMode==1){
					$PrepaymentSTR=$Prepayment=="on"?",A.Prepayment=1":",A.Prepayment=0";
				}
				else{
					$PrepaymentSTR=",A.Prepayment=0";
					$BankUID=FormatSTR($BankUID);
                    $BankAccounts=FormatSTR($BankAccounts);
				}
				$ProviderType =$ProviderType==""?0:$ProviderType;
				$GysPayMode =  $GysPayMode ==""?0:$GysPayMode;
				$SaleMode =  $SaleMode ==""?0:$SaleMode;

				//上传公司logo
			if($Logo!=""){
				$newLogoFileName="L".$CompanyId.".png";
				$uploadInfo0=UploadPictures($Logo,$newLogoFileName,$tradeLogoPath);
			}

			$LogoPngUpload=$uploadInfo0==""?"":",A.Logo ='$newLogoFileName'";

			$CompanySign = 1;
			$tempCount=count($TempCompanySign);
			for($k=0;$k<$tempCount;$k++){
			    if($TempCompanySign[$k]>0){
			        $CompanySign = $CompanySign * $TempCompanySign[$k];
			    }
			}
			$CompanySign = $CompanySign ==1?$ASH_CONFIG['COMPANY_CSIGN_2']:$CompanySign;

	        $mainSql = "UPDATE $upDataSheet A  SET A.ObjectSign='$ObjectSign',A.FscNo='$FscNo',  
	            A.ProviderType='$ProviderType',A.Letter='$Letter', A.Forshort='$Forshort',   
                A.Currency='$Currency',A.PayMode='$PayMode', A.GysPayMode='$GysPayMode',A.SaleMode='$SaleMode',
                A.ExpNum='$ExpNum',A.PayType='$PayType',A.BankId='$BankId', 
                A.PriceTerm='$PriceTerm', A.ChinaSafe='$ChinaSafe',A.CompanySign ='$CompanySign', 
                A.Staff_Number='$Staff_Number', A.Date='$Date', A.Operator='$Operator',    
                A.Locks='0',A.Judge='$Judge',A.Estate='2',
                A.UpdateReasons ='$UpdateReasons',A.ReturnReasons='' $PrepaymentSTR
                $LogoPngUpload 
				WHERE A.Id='$Id'";
				$Result = mysql_query($mainSql);
				$ErrorUd = mysql_errno($link_id)?true:false;
				if($Result){
					$Log.="交易对象 $Forshort 的主资料更新成功.<br>";
					}
				else{
					$Log.="<div class=redB>交易对象 $Forshort 的主资料更新失败!</div>";
					$Log.="<div class=redB>$mainSql</br>".mysql_error($link_id)."</div>";
					$OperationResult="N";
					}

				$PreFileName1="B".$CompanyId.".jpg";
				if($BusinessLicence!=""){
					$OldFile1=$BusinessLicence;
					$uploadInfo1=UploadPictures($OldFile1,$PreFileName1,$ProviderPhotoPath);
				}

				$upValue1=$uploadInfo1==""?"":",BusinessLicence='1'";
				if($upValue1=="" && $oldB==1){//没有上传文件并且已选取删除原文件
					$delFilePath=$ProviderPhotoPath.$PreFileName1;
					if(file_exists($delFilePath)){
						unlink($delFilePath);
						$upValue1=",BusinessLicence='0'";
						}
					}

				$PreFileName2="T".$CompanyId.".jpg";
				if($TaxCertificate!=""){
					$OldFile2=$TaxCertificate;
					$uploadInfo2=UploadPictures($OldFile2,$PreFileName2,$ProviderPhotoPath);
				}

				$upValue2=$uploadInfo2==""?"":",TaxCertificate='1'";
				if($upValue2=="" && $oldT==1){//没有上传文件并且已选取删除原文件
					$delFilePath=$ProviderPhotoPath.$PreFileName2;
					if(file_exists($delFilePath)){
						unlink($delFilePath);
						$upValue2=",TaxCertificate='0'";
						}
					}

				$PreFileName3="P".$CompanyId.".jpg";
				if($ProductionCertificate!=""){
					$OldFile3=$ProductionCertificate;
					$uploadInfo3=UploadPictures($OldFile3,$PreFileName3,$ProviderPhotoPath);
				}

				$upValue3=$uploadInfo3==""?"":",ProductionCertificate='1'";
				if($upValue3=="" && $oldP==1){//没有上传文件并且已选取删除原文件
					$delFilePath=$ProviderPhotoPath.$PreFileName3;
					if(file_exists($delFilePath)){
						unlink($delFilePath);
						$upValue3=",ProductionCertificate='0'";
						}
					}


				if($BankPermit!=""){
					$OldFile4=$BankPermit;
					$PreFileName4="K".$CompanyId.".jpg";
					$uploadInfo4=UploadPictures($OldFile4,$PreFileName4,$ProviderPhotoPath);
				}
				$upValue4=$uploadInfo4==""?"":",BankPermit='1'";
					if($upValue4=="" && $oldK==1){//没有上传文件并且已选取删除原文件
						$delFilePath=$ProviderPhotoPath.$PreFileName4;
						if(file_exists($delFilePath)){
							unlink($delFilePath);
							$upValue4=",BankPermit='0'";
					}
				}


				if($SalesAgreement!=""){
					$OldFile5=$SalesAgreement;
					$PreFileName5="S".$CompanyId.".jpg";
					$uploadInfo5=UploadPictures($OldFile5,$PreFileName5,$ProviderPhotoPath);
				}
				$upValue5=$uploadInfo5==""?"":",SalesAgreement='1'";
					if($upValue5=="" && $oldS==1){//没有上传文件并且已选取删除原文件
						$delFilePath=$ProviderPhotoPath.$PreFileName5;
						if(file_exists($delFilePath)){
							unlink($delFilePath);
							$upValue5=",SalesAgreement='0'";
					}
				}

				if($PaymentOrder!=""){
					$OldFile6=$PaymentOrder;
					$PreFileName6="O".$CompanyId.".jpg";
					$uploadInfo6=UploadPictures($OldFile6,$PreFileName6,$ProviderPhotoPath);
				}

				$upValue6=$uploadInfo6==""?"":",PaymentOrder='1'";
					if($upValue6=="" && $oldO==1){//没有上传文件并且已选取删除原文件
						$delFilePath=$ProviderPhotoPath.$PreFileName6;
						if(file_exists($delFilePath)){
							unlink($delFilePath);
							$upValue6=",PaymentOrder='0'";
					}
				}


				if($TaxpayerIdentifi!=""){
					$OldFile7=$TaxpayerIdentifi;
					$PreFileName7="TI".$CompanyId.".jpg";
					$uploadInfo7=UploadPictures($OldFile7,$PreFileName7,$ProviderPhotoPath);
				}

				$upValue7=$uploadInfo7==""?"":",TaxpayerIdentifi='1'";
			    if($upValue7=="" && $oldTI==1){//没有上传文件并且已选取删除原文件
					$delFilePath=$ProviderPhotoPath.$uploadInfo7;
					if(file_exists($delFilePath)){
						unlink($delFilePath);
						$upValue7=",TaxpayerIdentifi='0'";
				    }
				 }

			    $checkSheet = mysql_query("SELECT Id FROM  $DataIn.providersheet WHERE CompanyId='$CompanyId'",$link_id);
	            if(mysql_num_rows($checkSheet)<=0) {
	               $insertSql="INSERT INTO $DataIn.providersheet(Id,CompanyId,LegalPerson,BulidTime,ValidTime,Capital,
	    CompanySize,StaffNum,CompanyNature,CompanyCategory,BLdate,TRCdate,PLdate,BusinessLicence,TaxCertificate,
	    ProductionCertificate,BankPermit,SalesAgreement,PaymentOrder,TaxpayerIdentifi,AddValueTax,InvoiceTax,
        Description,Aptitudes,EAQF,Quality,Normative,Effect,Qos,Results,CompanyPicture,MainBusiness,DealRange,Estate,
        Locks,Date,Operator) VALUES (NULL,'$CompanyId','0','0000-00-00','','','','','0','0','','','','0','0','0','0','0',
        '0','0','0','0','','','','0','0','0','0','0','','','','1','0','$Date','$Operator')";
                   $inResult = mysql_query($insertSql);
				   $ErrorUd = mysql_errno($link_id)?true:false;
				   if(!$inResult)
					   $Log.="<div class=redB>$insertSql</br>".mysql_error($link_id)."</div>";
	            }
	            $AddValueTax = $AddValueTax==""?0:$AddValueTax;
		        $BulidTime  = $BulidTime ==""?"0000-00-00":$BulidTime;
		        $CompanyNature = $CompanyNature==""?0:$CompanyNature;
		        $CompanyCategory = $CompanyCategory==""?0:$CompanyCategory;
                $mainSql = "UPDATE $DataIn.providersheet B
				SET B.LegalPerson='$LegalPerson',
				B.BulidTime='$BulidTime',
				B.ValidTime='$ValidTime',
				B.Capital='$Capital',
				B.CompanySize    ='$CompanySize',
				B.StaffNum       ='$StaffNum',
				B.CompanyNature  ='$CompanyNature',
				B.CompanyCategory='$CompanyCategory',
				B.BLdate         ='$BLdate',
				B.TRCdate        ='$TRCdate',
				B.PLdate         ='$PLdate',
				B.AddValueTax = '$AddValueTax',
				B.InvoiceTax='$InvoiceTax',
			    B.CompanyPicture='$CompanyPicture',
			    B.MainBusiness='$MainBusiness',
			    B.DealRange='$DealRange'
				$upValue1 $upValue2 $upValue3 $upValue4 $upValue5 $upValue6  $upValue7 
				WHERE B.CompanyId='$CompanyId' ";

				$Result = mysql_query($mainSql);
				$ErrorUd = mysql_errno($link_id)?true:false;
				if($Result){
					$Log.="交易对象 $Forshort 的公司其它信息更新成功.<br>";
					}
				else{
					$Log.="<div class=redB>交易对象 $Forshort 的公司其它信息更新失败!</div>";
					$Log.="<div class=redB>$mainSql</br>".mysql_error($link_id)."</div>";
					$OperationResult="N";
					}

			$CheckInfoResult=mysql_fetch_array(mysql_query("SELECT  Id  FROM  $DataIn.companyinfo  WHERE  CompanyId=$CompanyId AND Type='$Type'",$link_id));
	      $CheckInfoId=$CheckInfoResult["Id"];
	      if($CheckInfoId!=""){

			$mainSql = "UPDATE $DataIn.companyinfo B  SET B.Company='$Company',B.Tel='$Tel',B.Fax='$Fax',B.Area='$Area',B.Website='$Website',B.ZIP='$ZIP',B.Address='$Address',B.Bank='$Bank',B.BankUID='$BankUID',B.BankAccounts='$BankAccounts',B.Remark='$Remark' 
			WHERE B.CompanyId='$CompanyId' AND B.Type='$Type'";
			$Result = mysql_query($mainSql);
			$ErrorUd = mysql_errno($link_id)?true:false;
			if($Result){
				$Log.="交易对象 $Forshort 的公司资料更新成功.<br>";
				}
			else{
				$Log.="<div class=redB>交易对象 $Forshort 的公司资料更新失败!</div>";
				$Log.="<div class=redB>$mainSql</br>".mysql_error($link_id)."</div>";
				$OperationResult="N";
				}
		  }
		  else{
			   $infoRecode="INSERT INTO $DataIn.companyinfo (Id,Type,CompanyId,Company,Tel,Fax,Area,Website,Address,ZIP,Bank,BankUID,BankAccounts,IBAN,Remark) 
	VALUES (NULL,'$Type','$CompanyId','$Company','$Tel','$Fax','$Area','$Website','$Address','$ZIP','$Bank','$BankUID','$BankAccounts','$IBAN','$Remark')";
	           $infoRes=@mysql_query($infoRecode);
			   $ErrorUd = mysql_errno($link_id)?true:false;
	            if($infoRes){
	             	  $Log.="交易对象 $Forshort 的公司资料新增成功！<br>";
	          	}
	      	else{
	              	$Log.="<div class=redB>交易对象 $Forshort 的公司资料新增失败！$infoRecode</div>";
					$Log.="<div class=redB>$infoRecode</br>".mysql_error($link_id)."</div>";
				  }
		  }

		$CheckLinkResult=mysql_fetch_array(mysql_query("SELECT  Id  FROM  $DataIn.linkmandata 
		 WHERE  CompanyId=$CompanyId AND Type='$Type'",$link_id));
        $CheckLinkId=$CheckLinkResult["Id"];
         if($CheckLinkId!=""){
				$mainSql = "UPDATE $DataIn.linkmandata C   SET  
				C.Name='$Linkman',C.Sex='$Sex',C.Nickname='$Nickname',C.Headship='$Headship',
                C.Mobile='$Mobile',C.Tel='$Tel2',C.MSN='$MSN',C.SKYPE='$SKYPE',
                C.Email='$Email',C.Remark='$Remark2',C.Date='$Date',C.Operator='$Operator'
				WHERE C.CompanyId='$CompanyId' AND C.Defaults='0' AND C.Type='$Type'";
				$Result = mysql_query($mainSql);
				$ErrorUd = mysql_errno($link_id)?true:false;
				if($Result){
					$Log.="交易对象 $Forshort 的联系人资料更新成功.<br>";
					}
				else{
					$Log.="<div class=redB>名供应商 $Forshort 的联系人资料更新失败!</div>";
					$Log.="<div class=redB>$mainSql</br>".mysql_error($link_id)."</div>";
					$OperationResult="N";
					}
               }
            else{
                     $LinkmanRecode="INSERT INTO $DataIn.linkmandata (Id,CompanyId,Name,Sex,Nickname,Headship,Mobile,Tel,MSN,SKYPE,Email,Remark,Date,Defaults,Type,Estate,Locks,Operator) VALUES (NULL,'$CompanyId','$Linkman','$Sex','$Nickname','$Headship','$Mobile','$Tel2','$MSN','$SKYPE','$Email','$Remark2','$Date','0','$Type','1','0','$Operator')";
	   	            $LinkmanRes=@mysql_query($LinkmanRecode);
					$ErrorUd = mysql_errno($link_id)?true:false;
		            if($LinkmanRes){
		             	  $Log.="交易对象 $Forshort 的默认联系人资料新增成功！<br>";
		          	}
	          	else{
		              	$Log.="<div class=redB>4--交易对象 $Forshort 的默认联系人资料新增失败！</div>";
						$Log.="<div class=redB>$LinkmanRecode</br>".mysql_error($link_id)."</div>";
					  }
                }
			//更新或新建项目信息 by ckt 2017-12-28
			$TradeNo = FormatSTR($TradeNo);
			$Members = FormatSTR($Members);
			$CmptTotal = FormatSTR($CmptTotal);
			$CheckTradeInfoResult = mysql_fetch_array(mysql_query("select Id from $DataIn.trade_info 
			 where TradeId=$Id"));
			 $CheckTradeInfoId = $CheckTradeInfoResult['Id'];
			 if($CheckTradeInfoId!=""){
				 $mainSql = "UPDATE $DataIn.trade_info i set 
				  i.TradeNo='$TradeNo',i.Proofreader='$Proofreader',i.Proofreader1='$Proofreader1',i.Checker='$Checker',
				  i.Members='$Members',i.Producer='$Producer',i.CmptTotal='$CmptTotal' 
				  where i.TradeId=$Id";
			 $Result = mysql_query($mainSql);
			 $ErrorUd = mysql_errno($link_id)?true:false;
				if($Result){
					$Log.="交易对象 $Forshort 的项目资料更新成功.<br>";
					}
				else{
					$Log.="<div class=redB>交易对象 $Forshort 的项目资料更新失败! </div>";
					$Log.="<div class=redB>$mainSql</br>".mysql_error($link_id)."</div>";
					$OperationResult="N";
					}
               }
            else{
				 $TradeInfoRecode="insert into $DataIn.trade_info(TradeId, TradeNo, Proofreader, Proofreader1, Checker, Members, Producer, CmptTotal)
			values('$Id','$TradeNo','$Proofreader','$Proofreader1','$Checker','$Members','$Producer','$CmptTotal')";
				$TradeInfoRes=@mysql_query($TradeInfoRecode);
				$ErrorUd = mysql_errno($link_id)?true:false;
				if($TradeInfoRes){
					  $Log.="5--交易对象 $Forshort 的项目资料新增成功！<br>";
				}
			else{
					$Log.="<div class=redB>5--交易对象 $Forshort 的项目资料新增失败！</div>";
					$Log.="<div class=redB>$TradeInfoRecode</br>".mysql_error($link_id)."</div>";
				  }
			}


            //发送登录模板消息
            include_once "../weixin/weixin_api.php";

            $weixin = new weixin_api();

            $touser = 'op_Tyw_8h2hzceNzjvmkDMICU60s'; //微信刘文豪 open_id

            $next_user = '刘文豪';//发送给的用户名字，与$touser相对应

			$login_user = $_SESSION['Login_Name'];  //当前登录用户

            $login_time = date('Y-m-d H:i:s');//操作时间

            $time = explode(' ', $login_time);

            $time = $time[1];

            $login_detail = $login_user.'于今日'.$time.'完成'.$Log_Item.'流程。现需要您完成下一步"审核"工作，请及时登录研砼治筑运营平台进行操作。';//登录详情

            $remark = "\n 流程测试，如有疑问，请及时联系".$login_user."或ＩＴ部。";//备注

            $res = $weixin->send_login_temp_msg($touser, $login_user, $next_user, $Log_Item, $login_time, $login_detail, $remark);

            if ($res){
            	$Log.="已通知 <span style='color: red'>$next_user</span> 进行下一步操作. <br>";
			}

	   	}
	break;
	}
if($ErrorUd){
	@mysql_query('ROLLBACK');
	$Log.="<div class=greenB>数据存储错误，已回滚！</div>";
}else{
	@mysql_query('COMMIT');
}
$ALType="From=$From&Estate=$Estate&Pagination=$Pagination&Page=$Page&ProviderType=$ProviderType&Orderby=$Orderby&ObjectSign=$ObjectSign";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
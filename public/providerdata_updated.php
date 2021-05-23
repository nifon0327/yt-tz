<?
//电信---yang 20120801
//代码共享-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="供应商资料";		//需处理
$upDataSheet="$DataIn.trade_object";	//需处理
$Type=3;
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$ProviderPhotoPath="../download/providerfile/";
//步骤3：需处理，更新操作
//echo "$ActionId:";




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
		
		if($result){
			$Log.="供应商ID号为:$CompanyId 传图职责人为:$PicNumber </br>";
			}
		else{
			$Log.="供应商ID号为:$CompanyId 传图职责人为:$PicNumber  失败 </br>";
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
				SET A.Estate='1',A.Locks='0',B.Estate='1',B.Locks='0',C.Estate='1',C.Locks='0' WHERE A.Id IN ($Ids)";
		$result = mysql_query($sql);
		if($result){
			$Log.="ID号在 $Ids 的 $Log_Item 设为可用成功!</br>";
			}
		else{
			$Log.="<div class='redB'>ID号在 $Ids 的 $Log_Item 设为可用失败!</div></br>";
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
		if($result){
			$Log.="ID号在 $Ids 的 $Log_Item 设为禁用成功!</br>";
			}
		else{
			$Log.="<div class='redB'>ID号在 $Ids 的 $Log_Item 设为禁用失败! $sql </div></br>";
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
		$updatesql="UPDATE $DataIn.trade_object set Estate=1 WHERE Id IN ($Ids)";
		$result = mysql_query($updatesql);
		if($result){
			$Log="<p>供应商 $Ids 的资料审核成功.";
			}
		else{
			$Log.="<div class='redB'>供应商 $Ids 的资料审核失败.</div>";
			$OperationResult="N";
			}
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
                                if($upAction){
                                        $Log.="供应商 $CompanyId 送货胶框图 $uploadInfo 上传成功.<br>";
                                 }
                                else{
                                        $Log.="<div class='redB'>供应商 $CompanyId 送货胶框图 $uploadInfo 上传失败</div><br>";
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
                                if($upAction){
                                        $Log.="供应商 $CompanyId 提示图 $uploadInfo 上传成功.<br>";
                                 }
                                else{
                                        $Log.="<div class='redB'>供应商 $CompanyId 提示图  $uploadInfo 上传失败</div><br>";
                                        $OperationResult="N";
                                        }
                                }
                 }

     break;
	default:
	     if($Forshort==""){
			 return false;
			 break;
		 }
		if($Prov!=$City){
			$Area=$Prov."|".$City;}
		else{
			$Area=$City;
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
		$Judge=FormatSTR($Judge);
		$Remark=FormatSTR($Remark);
		$Currency=$Currency;
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
		
		
		
		/*
		$mainSql = "UPDATE $upDataSheet A
		LEFT JOIN $DataIn.companyinfo B ON B.CompanyId=A.CompanyId AND B.Type='$Type'
		LEFT JOIN $DataIn.linkmandata C ON C.CompanyId=A.CompanyId AND C.Defaults='0' AND C.Type='$Type'
		SET A.FscNo='$FscNo', A.ProviderType='$ProviderType',A.Letter='$Letter', A.Forshort='$Forshort', A.Currency='$Currency', A.GysPayMode='$GysPayMode', A.Date='$Date', A.Operator='$Operator', A.Locks='0',A.Judge='$Judge',
		B.Company='$Company',B.Tel='$Tel',B.Fax='$Fax',B.Area='$Area',B.Website='$Website',B.ZIP='$ZIP',B.Address='$Address',B.Bank='$Bank',B.Remark='$Remark',
		C.Name='$Linkman',C.Sex='$Sex',C.Nickname='$Nickname',C.Headship='$Headship',C.Mobile='$Mobile',C.Tel='$Tel2',C.MSN='$MSN',C.SKYPE='$SKYPE',C.Email='$Email',C.Remark='$Remark2',C.Date='$Date',C.Operator='$Operator'
		WHERE A.Id='$Id'";
		$Result = mysql_query($mainSql);
		if($Result){
			$Log="供应商 $Forshort 的资料更新成功.<br>";
			}
		else{
			$Log="<div class=redB>名供应商 $Forshort 的资料更新失败! $mainSql </div></br>";
			$OperationResult="N";
			}
		*/
		$CompanyIdResult = mysql_query("SELECT CompanyId FROM $upDataSheet A  WHERE A.Id='$Id'",$link_id);
		if($CompanyIdRow= mysql_fetch_array($CompanyIdResult)) {
             
				$CompanyId=$CompanyIdRow["CompanyId"];
				
				if ($GysPayMode==1){
					$PrepaymentSTR=$Prepayment=="on"?",A.Prepayment=1":",A.Prepayment=0";
				}
				else{
					$PrepaymentSTR=",A.Prepayment=0";
				}
				
				
				
				$mainSql = "UPDATE $upDataSheet A
				SET A.FscNo='$FscNo', A.ProviderType='$ProviderType',A.Letter='$Letter', A.Forshort='$Forshort', A.Currency='$Currency', A.LimitTime='$LimitTime',A.GysPayMode='$GysPayMode', A.Date='$Date', A.Operator='$Operator', A.Locks='0',A.Judge='$Judge' $PrepaymentSTR
				WHERE A.Id='$Id'";
				
				//echo "$mainSql";
				$Result = mysql_query($mainSql);
				if($Result){
					$Log.="供应商 $Forshort 的主资料更新成功.<br>";
					}
				else{
					$Log.="<div class=redB>名供应商 $Forshort 的主资料更新失败! $mainSql </div></br>";
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
					
				$mainSql = "UPDATE $DataIn.providersheet B
				SET B.CompanyId='$CompanyId' $upValue1 $upValue2 $upValue3 
				WHERE B.CompanyId='$CompanyId' ";
				
				//echo "$mainSql";
				$Result = mysql_query($mainSql);
				if($Result){
					$Log.="供应商 $Forshort 的公司其它信息更新成功.<br>";
					}
				else{
					$Log.="<div class=redB>名供应商 $Forshort 的公司其它信息更新失败! $mainSql </div></br>";
					$OperationResult="N";
					}	
					
					
				$mainSql = "UPDATE $DataIn.companyinfo B
				SET B.Company='$Company',B.Tel='$Tel',B.Fax='$Fax',B.Area='$Area',B.Website='$Website',B.ZIP='$ZIP',B.Address='$Address',B.Bank='$Bank',B.Remark='$Remark'
				WHERE B.CompanyId='$CompanyId' AND B.Type='$Type'";
				
				//echo "$mainSql";
				$Result = mysql_query($mainSql);
				if($Result){
					$Log.="供应商 $Forshort 的公司资料更新成功.<br>";
					}
				else{
					$Log.="<div class=redB>名供应商 $Forshort 的公司资料更新失败! $mainSql </div></br>";
					$OperationResult="N";
					}	
					
				$mainSql = "UPDATE $DataIn.linkmandata C
				SET C.Name='$Linkman',C.Sex='$Sex',C.Nickname='$Nickname',C.Headship='$Headship',C.Mobile='$Mobile',C.Tel='$Tel2',C.MSN='$MSN',C.SKYPE='$SKYPE',C.Email='$Email',C.Remark='$Remark2',C.Date='$Date',C.Operator='$Operator'
				WHERE C.CompanyId='$CompanyId' AND C.Defaults='0' AND C.Type='$Type'";
				
				//echo "$mainSql";
				$Result = mysql_query($mainSql);
				if($Result){
					$Log.="供应商 $Forshort 的联系人资料更新成功.<br>";
					}
				else{
					$Log.="<div class=redB>名供应商 $Forshort 的联系人资料更新失败! $mainSql </div></br>";
					$OperationResult="N";
					}						
					
		}		
	break;
	}
$ALType="From=$From&Estate=$Estate&Pagination=$Pagination&Page=$Page&ProviderType=$ProviderType&Orderby=$Orderby";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
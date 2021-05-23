<?php
//电信-zxq 2012-08-01
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage;
//步骤2：
$Log_Item="客户资料";		//需处理
$upDataSheet="$DataIn.trade_object";	//需处理
$Type=2;
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 5:				//则联系人，联系人的帐号可用
		$Log_Funtion="可用";
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if($Id!=""){
				$Ids=$Ids==""?$Id:($Ids.",".$Id);
				}
			}
		$sql = "UPDATE $upDataSheet  A
				LEFT JOIN $DataIn.linkmandata B ON B.CompanyId=A.CompanyId AND B.Type='$Type'
				LEFT JOIN $DataIn.usertable C ON C.Number=B.Id AND C.uType='$Type'
				SET A.Estate='1',A.Locks='0',B.Estate='1',B.Locks='0',C.Estate='1',C.Locks='0'
				WHERE A.Id IN ($Ids)";
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
		$sql = "UPDATE $upDataSheet  A
				LEFT JOIN $DataIn.linkmandata B ON B.CompanyId=A.CompanyId AND B.Type='$Type'
				LEFT JOIN $DataIn.usertable C ON C.Number=B.Id AND C.uType='$Type'
				SET A.Estate='0',A.Locks='0',B.Estate='0',B.Locks='0',C.Estate='0',C.Locks='0'
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
	case 40:
		$Log_Funtion="客户图档上传";
		/////////////////////////////////////////
		//之前最后一个记录
		$FilePath="../download/clientfile/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$Date=date("Y-m-d");
		$EndNumber=1;
		$checkEndFile=mysql_fetch_array(mysql_query("SELECT MAX(Picture) AS EndPicture FROM $DataIn.clientimg WHERE CompanyId='$CompanyId'",$link_id));
		$EndFile=$checkEndFile["EndPicture"];
		if($EndFile!=""){
			$TempArray1=explode("_",$EndFile);
			$TempArray2=explode(".",$TempArray1[1]);
			$EndNumber=$TempArray2[0]+1;
			}
		$uploadNums=count($Picture);
		for($i=0;$i<$uploadNums;$i++){
			//上传文档
			$upPicture=$Picture[$i];
			$TempOldImg=$OldImg[$i];
			$imgName=$Name[$i];
			if ($upPicture!=""){
				$OldFile=$upPicture;
				//检查是否有原档，如果有则使用原档名称，如果没有，则分配新档名
				if($TempOldImg!=""){
					$PreFileName=$TempOldImg;
					}
				else{
					$PreFileName=$CompanyId."_".$EndNumber.".pdf";
					}
				$uploadInfo=$PreFileName;
				$uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
				if($uploadInfo!=""){
					if($TempOldImg==""){//写入记录
						$inRecode="INSERT INTO $DataIn.clientimg (Id,CompanyId,Picture,Date,Name,Operator) VALUES (NULL,'$CompanyId','$uploadInfo','$Date','$imgName','$Operator')";
						$inAction=@mysql_query($inRecode);
						if($inAction){
							$Log.="客户 $CompanyId 的图档 $uploadInfo 添加成功.<br>";
							$EndNumber++;}
						else{
							$Log.="<div class='redB'>客户 $CompanyId 的图档 $uploadInfo 添加失败. </div><br>";
							$OperationResult="N";
							}
						}
					}
				}
			}
			break;
	case 49:$Log_Funtion="调至皮套"; $SetStr="cSign=3";			include "../model/subprogram/updated_model_3d.php";		break;
	case 50:$Log_Funtion="调至研砼"; $SetStr="cSign=7";			include "../model/subprogram/updated_model_3d.php";		break;
	default:
		//记录字段值
		$Company=FormatSTR($Company);
		$Forshort=FormatSTR($Forshort);
		$Tel=FormatSTR($Tel);
		$Fax=FormatSTR($Fax);
		$Area=FormatSTR($Area);
		$Website=FormatSTR($Website);
		$ZIP=FormatSTR($ZIP);
		$Address=FormatSTR($Address);
		$Bank=FormatSTR($Bank);
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
		$mainSql = "UPDATE $upDataSheet A
		LEFT JOIN $DataIn.companyinfo B ON B.CompanyId=A.CompanyId AND B.Type='$Type'
		LEFT JOIN $DataIn.linkmandata C ON C.CompanyId=A.CompanyId AND C.Defaults='0' AND C.Type='$Type'
		SET 
		A.Forshort='$Forshort', 
		A.ExpNum='$ExpNum',
		A.PayType='$PayType',
		A.PayMode='$PayMode',
		A.Currency='$Currency',
		A.BankId='$BankId',
		A.Staff_Number='$Staff_Number', 
		A.PriceTerm='$PriceTerm',
		A.Date='$Date', 
		A.Operator='$Operator', 
		A.Locks='0',
		B.Company='$Company',
		B.Tel='$Tel',
		B.Fax='$Fax',
		B.Area='$Area',
		B.Website='$Website',
		B.ZIP='$ZIP',
		B.Address='$Address',
		B.Bank='$Bank',
		B.Remark='$Remark',
		C.Name='$Linkman',
		C.Sex='$Sex',
		C.Nickname='$Nickname',
		C.Headship='$Headship',
		C.Mobile='$Mobile',
		C.Tel='$Tel2',
		C.MSN='$MSN',
		C.SKYPE='$SKYPE',
		C.Email='$Email',
		C.Remark='$Remark2',
		C.Date='$Date',
		C.Operator='$Operator'
		WHERE A.Id='$Id'";
		$Result = mysql_query($mainSql);
		if($Result){
			$Log="$Log_Item $Forshort 的资料更新成功.<br>";
			}
		else{
			$Log="<div class=redB>$Log_Item $Forshort 的资料更新失败! $mainSql </div></br>";
			$OperationResult="N";
			}
	break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
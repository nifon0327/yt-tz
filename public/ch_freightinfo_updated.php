<?php 
//电信-zxq 2012-08-01
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="货运公司资料";		//需处理
$upDataSheet="$DataPublic.freightdata";	//需处理
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
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if($Id!=""){
				$Ids=$Ids==""?$Id:($Ids.",".$Id);
				}
			}
		$Log_Funtion="可用";
		$upSql = "UPDATE $upDataSheet A LEFT JOIN 
		$DataIn.linkmandata B ON A.CompanyId=B.CompanyId  and B.Type='$Type' SET A.Estate='1',A.Locks='0',B.Estate='1',B.Locks='0' WHERE A.Id IN ($Ids)";
		//echo "$upSql";
		 $upResult = mysql_query($upSql);
		if($upResult){
			$Log="&nbsp;&nbsp;ID在( $Ids ) 的 $TitleSTR 成功!</br>";
			}
		else{
			$OperationResult="N";
			$Log="<div class='redB'>&nbsp;&nbsp;ID在( $Ids ) 的 $TitleSTR 失败!</div></br>";
			}
		break;
	case 6:
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if($Id!=""){
				$Ids=$Ids==""?$Id:($Ids.",".$Id);
				}
			}
		$Log_Funtion="禁用";
		$upSql = "UPDATE $upDataSheet A 
		LEFT JOIN $DataIn.linkmandata B ON A.CompanyId=B.CompanyId and B.Type='$Type' SET A.Estate='0',A.Locks='0',B.Estate='0',B.Locks='0' WHERE A.Id IN ($Ids)";
		 $upResult = mysql_query($upSql);
		if($upResult){
			$Log="&nbsp;&nbsp;ID在( $Ids ) 的 $TitleSTR 成功!</br>";
			}
		else{
			$OperationResult="N";
			$Log="<div class='redB'>&nbsp;&nbsp;ID在( $Ids ) 的 $TitleSTR 失败!</div></br>";
			}
		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
		//主表信息
		$Forshort=FormatSTR($Forshort);
		$Currency=$Currency;
		
		//从表信息		
		$Company=FormatSTR($Company);
		$Tel=FormatSTR($Tel);
		$Fax=FormatSTR($Fax);
		$Area=FormatSTR($Area);
		$Website=FormatSTR($Website);
		$Address=FormatSTR($Address);
		$ZIP=FormatSTR($ZIP);
		$Bank=FormatSTR($Bank);
		$Remark=FormatSTR($Remark);
		
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
		LEFT JOIN $DataIn.companyinfo B ON B.CompanyId=A.CompanyId
		SET A.Forshort='$Forshort',A.Currency='$Currency',A.MType ='$MType',B.Company='$Company',B.Tel='$Tel',
		B.Fax='$Fax',B.Area='$Area',B.Website='$Website',B.Address='$Address',B.ZIP='$ZIP',
		B.Bank='$Bank',B.Remark='$Remark'		
		WHERE A.Id='$Id' AND B.Type='$Type'";
		$Result = mysql_query($mainSql);
		if($Result){
			$Log="货运公司 $Forshort 的资料更新成功.<br>";
			//联系人更新，如果有记录则更新，如果没有则新增
			$checkLinkman=mysql_query("SELECT Id FROM $DataIn.linkmandata 
			WHERE Id='$LinkId' AND Defaults='0' AND Type='$Type' LIMIT 1",$link_id);
			if($checkRow=mysql_fetch_array($checkLinkman)){//更新
				if($Linkman!=""){
					$Linkman_recode="UPDATE $DataIn.linkmandata SET 
					Name='$Linkman',Sex='$Sex',Nickname='$Nickname',Headship='$Headship',
					Mobile='$Mobile',Tel='$Tel2',MSN='$MSN',SKYPE='$SKYPE',Email='$Email',
					Remark='$Remark2',Date='$Date',Operator='$Operator' WHERE Id='$LinkId' AND Defaults='0'";
					$Linkman_res=mysql_query($Linkman_recode);
					if($Linkman_res){
						$Log.="&nbsp;&nbsp;该货运公司的默认联系人更新成功! <br>";
						}
					else{
						$Log.="<div class=redB>&nbsp;&nbsp;该货运公司的默认联系人更新失败!</div>";
						$OperationResult="N";
						}
					}
				else{
					//删除默认联系人
					$delSql="DELETE FROM $DataIn.linkmandata WHERE Id='$LinkId' and Defaults='0'";
					$delRresult = mysql_query($delSql);
					if ($delRresult && mysql_affected_rows()>0){
						//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.linkmandata");
						}
					}
				}
			else{//新增
				if($Linkman!=""){
					$LinkmanRecode="INSERT INTO $DataIn.linkmandata 
					(Id,CompanyId,Name,Sex,Nickname,Headship,Mobile,Tel,MSN,SKYPE,Email,Remark,Date,Defaults,Type,Estate,Locks,Operator) VALUES 
					(NULL,'$CompanyId','$Linkman','$Sex','$Nickname','$Headship','$Mobile','$Tel2','$MSN','$SKYPE','$Email','$Remark2','$Date','0','$Type','1','0','$Operator')";
					$LinkmanRes=@mysql_query($LinkmanRecode);
					if($LinkmanRes){
						$Log.="&nbsp;&nbsp; $Forshort 的默认联系人资料新增成功！";
						}
					else{
						$Log.="<div class=redB>&nbsp;&nbsp; $Forshort 的默认联系人资料新增失败！$LinkmanRecode</div>";
						$OperationResult="N";
						}
					}
				}				
			}//end if($Result)
		else{
			$Log="<div class=redB>货运公司 $Forshort 的资料更新失败! $mainSql </div></br>";
			$OperationResult="N";
			}//end if($Result)*/
	
          if($MType==2){		
				//forward 空运标准费用 
				$CFSCharge1 = $CFSCharge1 ==""?0.00:$CFSCharge1;
				$THCCharge1 = $THCCharge1 ==""?0.00:$THCCharge1;
				$WJCharge1  = $WJCharge1  ==""?0.00:$WJCharge1;
				$SXCharge1  = $SXCharge1  ==""?0.00:$SXCharge1;
				$ENSCharge1 = $ENSCharge1 ==""?0.00:$ENSCharge1;
				$BXCharge1  = $BXCharge1  ==""?0.00:$BXCharge1;
				$GQCharge1  = $GQCharge1  ==""?0.00:$GQCharge1;
				$DFCharge1  = $DFCharge1  ==""?0.00:$DFCharge1;
				$TDCharge1  = $TDCharge1  ==""?0.00:$TDCharge1;
				$CheckChargeResult1 = mysql_query("SELECT Id FROM $DataIn.forwardcharge WHERE Type=1 AND CompanyId = '$CompanyId'",$link_id);
				if($CheckChargeRow1 = mysql_fetch_array($CheckChargeResult1)){
					$UpdateChargeSql1 =  "UPDATE $DataIn.forwardcharge SET  CFSCharge ='$CFSCharge1',THCCharge ='$THCCharge1',WJCharge ='$WJCharge1',SXCharge ='$SXCharge1',ENSCharge ='$ENSCharge1',BXCharge ='$BXCharge1',GQCharge ='$GQCharge1',DFCharge ='$DFCharge1',TDCharge ='$TDCharge1'  WHERE CompanyId = '$CompanyId' AND Type=1";
					$UpdateChargeResult1 = mysql_query($UpdateChargeSql1);
				}else{
				      $InsertChargeSql1 = "INSERT INTO $DataIn.forwardcharge(Id,CompanyId,CFSCharge,THCCharge,WJCharge,SXCharge,ENSCharge,
				      BXCharge,GQCharge,DFCharge,TDCharge,Type,Estate,Locks,Date,Operator,PLocks,creator,created,modifier,modified)
				      VALUES(NULL,'$CompanyId','$CFSCharge1','$THCCharge1','$WJCharge1','$SXCharge1','$ENSCharge1','$BXCharge1',
				      '$GQCharge1','$DFCharge1','$TDCharge1','1','1','0','$Date','$Operator','0','$Operator',
				      '$DateTime','$Operator','$DateTime')";
				     
				      $InsertChargeResult1 = mysql_query($InsertChargeSql1);		
				}
				//forward 海运标准费用 
				$CFSCharge2 = $CFSCharge2 ==""?0.00:$CFSCharge2;
				$THCCharge2 = $THCCharge2 ==""?0.00:$THCCharge2;
				$WJCharge2  = $WJCharge2  ==""?0.00:$WJCharge2;
				$SXCharge2  = $SXCharge2  ==""?0.00:$SXCharge2;
				$ENSCharge2 = $ENSCharge2 ==""?0.00:$ENSCharge2;
				$BXCharge2  = $BXCharge2  ==""?0.00:$BXCharge2;
				$GQCharge2  = $GQCharge2  ==""?0.00:$GQCharge2;
				$DFCharge2  = $DFCharge2  ==""?0.00:$DFCharge2;
				$TDCharge2  = $TDCharge2  ==""?0.00:$TDCharge2;
				$CheckChargeResult2 = mysql_query("SELECT Id FROM $DataIn.forwardcharge WHERE Type=2 AND CompanyId = '$CompanyId'",$link_id);
				if($CheckChargeRow2 = mysql_fetch_array($CheckChargeResult2)){
					$UpdateChargeSql2 =  "UPDATE $DataIn.forwardcharge SET  CFSCharge ='$CFSCharge2',THCCharge ='$THCCharge2',WJCharge ='$WJCharge2',SXCharge ='$SXCharge2',ENSCharge ='$ENSCharge2',BXCharge ='$BXCharge2',GQCharge ='$GQCharge2',DFCharge ='$DFCharge2',TDCharge ='$TDCharge2'  WHERE CompanyId = '$CompanyId' AND Type=2";
					$UpdateChargeResult2 = mysql_query($UpdateChargeSql2);
				}else{
				      $InsertChargeSql2 = "INSERT INTO $DataIn.forwardcharge(Id,CompanyId,CFSCharge,THCCharge,WJCharge,SXCharge,ENSCharge,
				      BXCharge,GQCharge,DFCharge,TDCharge,Type,Estate,Locks,Date,Operator,PLocks,creator,created,modifier,modified)
				      VALUES(NULL,'$CompanyId','$CFSCharge2','$THCCharge2','$WJCharge2','$SXCharge2','$ENSCharge2','$BXCharge2',
				      '$GQCharge2','$DFCharge2','$TDCharge2','2','1','0','$Date','$Operator','0','$Operator',
				      '$DateTime','$Operator','$DateTime')";
				      $InsertChargeResult2 = mysql_query($InsertChargeSql2);		
				}
		    }
			
	break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
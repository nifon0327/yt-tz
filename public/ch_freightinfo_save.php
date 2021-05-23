<?php 
//电信-zxq 2012-08-01
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：
$Log_Item="货运公司资料";			//需处理
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

if ($MType==1) {  // 把货运公司，跟Forward公司合并
	$maxSql = mysql_query("SELECT MAX(CompanyId) AS Mid FROM $DataPublic.freightdata where MType='$MType' ",$link_id);
	$CompanyId=mysql_result($maxSql,0,"Mid");
	if($CompanyId){
		$CompanyId=$CompanyId+1;
		}
	else{
		$CompanyId=40001;
		}
}
else {
	$maxSql = mysql_query("SELECT MAX(CompanyId) AS Mid FROM $DataPublic.freightdata where MType='$MType' ",$link_id);
	$CompanyId=mysql_result($maxSql,0,"Mid");
	if($CompanyId){
		$CompanyId=$CompanyId+1;
		}
	else{
		$CompanyId=30001;
		}

}

$inRecode="INSERT INTO $DataPublic.freightdata (Id,CompanyId,Forshort,Currency,MType,Estate,Locks,Date,Operator) VALUES (NULL,'$CompanyId','$Forshort','$Currency','$MType','1','0','$Date','$Operator')";
$inAction=@mysql_query($inRecode);
//解锁表
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
if ($inAction){ 
	$Log="3-1:主信息 $TitleSTR 成功!<br>";
	$infoRecode="INSERT INTO $DataIn.companyinfo 
	(Id,Type,CompanyId,Company,Tel,Fax,Area,Website,Address,ZIP,Bank,Remark) VALUES (NULL,'$Type','$CompanyId','$Company','$Tel','$Fax','$Area','$Website','$Address','$ZIP','$Bank','$Remark')";
	$infoAction=@mysql_query($infoRecode);
	$Log.="3-2:次信息 $TitleSTR 成功!<br>";
	if($Linkman!=""){
		$LinkmanRecode="INSERT INTO $DataIn.linkmandata 
		(Id,CompanyId,Name,Sex,Nickname,Headship,Mobile,Tel,MSN,SKYPE,Email,Remark,Estate,Locks,Date,Defaults,Type,Operator) VALUES (NULL,'$CompanyId','$Linkman','$Sex','$Nickname','$Headship','$Mobile','$Tel2','$MSN','$SKYPE','$Email','$Remark2','1','0','$Date','0','$Type','$Operator')";
		$LinkmanRes=@mysql_query($LinkmanRecode);
		if($LinkmanRes){
			$Log.="3-3: $Forshort 的默认联系人资料新增成功！";
			}
		else{
			$Log.="<div class=redB>3-3: $Forshort 的默认联系人资料新增失败！$LinkmanRecode</div>";
			$OperationResult="N";
			}
		}

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
	} 
else{
	$Log="<div class=redB>$TitleSTR 失败!</div><br>";
	$OperationResult="N";
	} 
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

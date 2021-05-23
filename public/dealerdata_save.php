<?php 
/*电信-yang 20120801
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
$Log_Item="经销商或其它公司资料";			//需处理
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
$Type=4;
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
//锁定表
//$LockSql=" LOCK TABLES $DataPublic.dealerdata WRITE";$LockRes=@mysql_query($LockSql);
$maxSql = mysql_query("SELECT MAX(CompanyId) AS Mid FROM $DataPublic.dealerdata ORDER BY CompanyId DESC LIMIT 1",$link_id);
$CompanyId=mysql_result($maxSql,0,"Mid");
if($CompanyId){
	$CompanyId=$CompanyId+1;
	}
else{
	$CompanyId=50001;
	}

$inRecode="INSERT INTO $DataPublic.dealerdata (Id,CompanyId,Forshort,Currency,Estate,Locks,Date,Operator) VALUES (NULL,'$CompanyId','$Forshort','$Currency','1','0','$Date','$Operator')";
$inAction=@mysql_query($inRecode);
//解锁表
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
if ($inAction){ 
	$Log="主表 $TitleSTR 成功!<br>";
	$infoRecode="INSERT INTO $DataIn.companyinfo
	(Id,Type,CompanyId,Company,Tel,Fax,Area,Website,Address,ZIP,Bank,Remark) VALUES (NULL,'5','$CompanyId','$Company','$Tel','$Fax','$Area','$Website','$Address','$ZIP','$Bank','$Remark')";
	$infoAction=@mysql_query($infoRecode);
	$Log.="从表 $TitleSTR 成功!<br>";
	if($Linkman!=""){
		$LinkmanRecode="INSERT INTO $DataIn.linkmandata 
		(Id,CompanyId,Name,Sex,Nickname,Headship,Mobile,Tel,MSN,SKYPE,Email,Remark,Estate,Locks,Date,Defaults,Type,Operator) VALUES (NULL,'$CompanyId','$Linkman','$Sex','$Nickname','$Headship','$Mobile','$Tel2','$MSN','$SKYPE','$Email','$Remark2','1','0','$Date','0','6','$Operator')";
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
else{
	$Log="<div class=redB>$TitleSTR 失败! $inRecode</div><br>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO  $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

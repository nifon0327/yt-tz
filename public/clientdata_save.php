<?php 
//电信-zxq 2012-08-01
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：
$Log_Item="客户资料";			//需处理
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
$Type=2;
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
//$LockSql=" LOCK TABLES $DataIn.trade_object WRITE";$LockRes=@mysql_query($LockSql);
$maxSql = mysql_query("SELECT MAX(CompanyId) AS Mid FROM $DataIn.trade_object ORDER BY CompanyId DESC LIMIT 1",$link_id);
$CompanyId=mysql_result($maxSql,0,"Mid");
if($CompanyId){
	$CompanyId=$CompanyId+1;
	}
else{
	$CompanyId=1001;
	}

$inRecode="INSERT INTO $DataIn.trade_object (Id,cSign,CompanyId,Letter,Forshort,PayMode,GysPayMode,Currency,ExpNum,PayType,BankId,CompanySign,PriceTerm,Staff_Number,
PickNumber,FscNo,ProviderType,Prepayment,Judge,LimitTime,PackFile,TipsFile,OrderBy,ObjectSign,Estate,Locks,Date,Operator) VALUES (NULL,'$Login_cSign','$CompanyId',
'$Letter','$Forshort','$PayMode','99','$Currency','$ExpNum','$PayType','$BankId','0','$PriceTerm','$Staff_Number','7','','0','0','','0','0','0','0','1','1','0','$Date','$Operator')";
$inAction=@mysql_query($inRecode);
//解锁表
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
if ($inAction){ 
	$Log="主表 $TitleSTR 成功!<br>";
	$infoRecode="INSERT INTO $DataIn.companyinfo (Id,Type,CompanyId,Company,Tel,Fax,Area,Website,Address,ZIP,Bank,Remark) VALUES (NULL,'$Type','$CompanyId','$Company','$Tel','$Fax','$Area','$Website','$Address','$ZIP','$Bank','$Remark')";
	$infoAction=@mysql_query($infoRecode);
	$Log.="从表 $TitleSTR 成功!<br>";
	//if($Linkman!=""){
		$LinkmanRecode="INSERT INTO $DataIn.linkmandata (Id,CompanyId,Name,Sex,Nickname,Headship,Mobile,Tel,MSN,SKYPE,Email,Remark,Date,Defaults,Type,Estate,Locks,Operator) VALUES (NULL,'$CompanyId','$Linkman','$Sex','$Nickname','$Headship','$Mobile','$Tel2','$MSN','$SKYPE','$Email','$Remark2','$Date','0','$Type','1','0','$Operator')";
		$LinkmanRes=@mysql_query($LinkmanRecode);
		if($LinkmanRes){
			$Log.="&nbsp;&nbsp; $Forshort 的默认联系人资料新增成功！";
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp; $Forshort 的默认联系人资料新增失败！$LinkmanRecode</div>";
			$OperationResult="N";
			}
		//}
        //生成默认PI模版        
        $piRecode="INSERT INTO  $DataIn.yw3_pimodel (Id, CompanyId, Model, Estate, Locks, Date, Operator) VALUES (NULL, '$CompanyId', '$CompanyId', '1', '0', '$Date','$Operator')";
	     $piAction=@mysql_query($piRecode);
	} 
else{
	$Log="<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
	$OperationResult="N";
	} 
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

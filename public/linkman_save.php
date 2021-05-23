<?php 
//电信-ZX  2012-08-01
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：
$Log_Item="联系人资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination&Type=$Type&ComeFrom=$ComeFrom";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
switch($Type){
	case 2:$LogSTR="客户";break;
	case 3:$LogSTR="供应商";break;
	case 4:$LogSTR="Forward";break;
	case 5:$LogSTR="快递公司";break;
	}
//默认联系人信息
$Linkman=FormatSTR($Linkman);
$Nickname=FormatSTR($Nickname);
$Headship=FormatSTR($Headship);
$Mobile=FormatSTR($Mobile);
$Tel==FormatSTR($Tel);
$Email=FormatSTR($Email);
$Remark=FormatSTR($Remark);
$Date=date("Y-m-d");
if($Defaults!=1){//如果设为默认联系人，则需先清除原默认人
	$sql = "UPDATE $DataIn.linkmandata SET Defaults=1 WHERE CompanyId=$CompanyId";
	$result = mysql_query($sql);
	$Defaults=0;
	}
//添加联系人资料
$LinkmanRecode="INSERT INTO $DataIn.linkmandata (Id,CompanyId,Name,Sex,Nickname,Headship,Mobile,Tel,MSN,SKYPE,Email,Remark,Date,Defaults,Type,Estate,Locks,Operator) VALUES (NULL,'$CompanyId','$Linkman','$Sex','$Nickname','$Headship','$Mobile','$Tel','$MSN','$SKYPE','$Email','$Remark','$DateTime','$Defaults','$Type','1','0','$Operator')";
$Linkman_res=@mysql_query($LinkmanRecode);
if($Linkman_res){
	$Log=$LogSTR." $CompanyId 的联系人 $Linkman 资料添加成功！";
	}
else{
	$Log="<div class='redB'>".$LogSTR." $CompanyId 的联系人 $Linkman 资料添加失败！</div>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

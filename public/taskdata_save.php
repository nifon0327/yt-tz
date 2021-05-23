<?php 
//电信-joseph
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：
$Log_Item="特殊功能";			//需处理
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
//锁定表?

$maxSql = mysql_query("SELECT MAX(ItemId) AS Mid FROM $DataPublic.tasklistdata ORDER BY ItemId DESC",$link_id);
$ItemId=mysql_result($maxSql,0,"Mid");
if($ItemId){
	$ItemId=$ItemId+1;
	}
else{
	$ItemId="101";
	}
$Date=date("Y-m-d");
$Title=FormatSTR($Title);
$Description=FormatSTR($Description);
$Extra=FormatSTR($Extra);
$inRecode="INSERT INTO $DataPublic.tasklistdata (Id,cSign,ItemId,Title,Description,Extra,TypeId,InCol,Oby,Estate,Locks,Date,Operator) VALUES (NULL,'7','$ItemId','$Title','$Description','$Extra','$TypeId','$InCol','0','1','0','$Date','$Operator')";
$inAction=@mysql_query($inRecode);
if($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
	$OperationResult="N";
	}
//表解锁?
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

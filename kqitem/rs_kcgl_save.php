<?php 
//电信-ZX  2012-08-01
/*
$DataIn.staffrandp
$DataPublic.staffmain
$DataIn.cwxzsheet
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
$Log_Item="新扣工龄记录";			//需处理
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
$Counts=count($_POST["ListId"]);
$Ids="";
for($i=0;$i<$Counts;$i++){
	$thisId=$_POST["ListId"][$i];
	$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
	}
$NumberSTR="AND Number IN ($Ids)";
$Remark=FormatSTR($Remark);
$thisMonth=substr($Date,0,7);
//写入数据的条件：该日期大于入职日期，且未生成月统计资料
if($DataIn !== 'ac'){
	$inRecode="INSERT INTO $DataPublic.rs_kcgl
	SELECT NULL,Number,'$Month','$Months','$Remark','$DateTime','1','0','$Operator' 
	FROM $DataPublic.staffmain 
	WHERE 1 $NumberSTR AND Estate='1'";
}else{
	$inRecode="INSERT INTO $DataPublic.rs_kcgl
	SELECT NULL,Number,'$Month','$Months','$Remark','$DateTime','1','0','$Operator', 0, '$Operator', NOW(), '$Operator', NOW()
	FROM $DataPublic.staffmain 
	WHERE 1 $NumberSTR AND Estate='1'";
}
$inResult=@mysql_query($inRecode);
if($inResult){
	$Log.="&nbsp;&nbsp;员工(".$Ids.")的".$TitleSTR."成功!</br>";
	}
else{
	$Log.="<div class='redB'>&nbsp;&nbsp;员工(".$Ids.")的".$TitleSTR."失败! $inRecode </div></br>";
	$OperationResult="N";
	}
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

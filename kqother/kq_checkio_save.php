<?php 
//内部使用,非验厂文件
include "../model/modelhead.php";
//步骤2：
$Log_Item="考勤统计";			//需处理
$fromWebPage="kq_checkio_count";
$nowWebPage="kq_checkio_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&CheckMonth=$CheckMonth&Number=$Number&CountType=1";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Date=date("Y-m-d");

//保存月统计结果
if($DataIn !== 'ac'){
	$inRecode1="INSERT INTO $DataIn.kqdataother 
	SELECT NULL,Number,'$Dhours','$Whours',
	'$Ghours','$GOverTime','$GDropTime',
	'$Xhours','$XOverTime','$XDropTime',
	'$Fhours','$FOverTime','$FDropTime',
	'$InLates','$OutEarlys','$SJhours',
	'$BJhours','$YXJhours','$WXJhours','$QQhours','$YBs','$WXhours','$KGhours','$dkhours','$CheckMonth','1','$Operator','1' 
	FROM $DataPublic.staffmain WHERE Number='$Number' AND Number NOT IN (SELECT Number FROM $DataIn.kqdataother WHERE Month='$CheckMonth' and Number='$Number')";
}else{
	$inRecode1="INSERT INTO $DataIn.kqdataother 
	SELECT NULL,Number,'$Dhours','$Whours',
	'$Ghours','$GOverTime','$GDropTime',
	'$Xhours','$XOverTime','$XDropTime',
	'$Fhours','$FOverTime','$FDropTime',
	'$InLates','$OutEarlys','$SJhours',
	'$BJhours','$YXJhours','$WXJhours','$QQhours','$YBs','$WXhours','$KGhours','$dkhours','$CheckMonth','1','$Operator','1', 1, 0, '$Operator', NOW(), '$Operator', NOW(), NOW()
	FROM $DataPublic.staffmain WHERE Number='$Number' AND Number NOT IN (SELECT Number FROM $DataIn.kqdataother WHERE Month='$CheckMonth' and Number='$Number')";
}
$inAction1=@mysql_query($inRecode1);
if ($inAction1){ 
	$Log.="员工".$Number.$chooseMonth."的".$TitleSTR."成功!<br>";
	} 
else{
	$Log.="<div class=redB>员工".$Number.$chooseMonth."的".$TitleSTR."失败! $inRecode1 </div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
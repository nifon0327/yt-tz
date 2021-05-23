<?php 
//内部模式:OK ewen2013-07-30
include "../model/modelhead.php";
//步骤2：
$Log_Item="考勤统计";			//需处理
$fromWebPage="kq_checkio_count";
$nowWebPage="kq_checkio_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&CheckMonth=$CheckMonth&Number=$Number&CountType=1";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Date=date("Y-m-d");

//更新5-6月份数据
$sql = "UPDATE $DataIn.kqdata SET Ghours='$Ghours',GOverTime='$GOverTime',GDropTime='$GDropTime',Xhours='$Xhours',XOverTime='$XOverTime',XDropTime='$XDropTime',Fhours='$Fhours',FOverTime='$FOverTime',FDropTime='$FDropTime' WHERE Month='$CheckMonth' and Number='$Number'";
//echo $sql."<br>";
$result = mysql_query($sql);

//更新薪资表：如果有假日加班费，则合计需把假日加班费一起计算=$JJ+$Jbf-jbf
$upRecode="UPDATE $DataIn.cwxzsheet SET Jj='$Jj',Jbf='$Jbf'	WHERE Month='$CheckMonth' and Number='$Number'";
//echo $upRecode."<br>";
$upAction=@mysql_query($upRecode);

$upRecode="UPDATE $DataIn.cwxzsheet SET Amount=Dx+Gljt+Gwjt+Jj+Shbz+Zsbz+Jtbz+Jbf+Yxbz+taxbz-Jz-Sb-Gjj-Ct-Kqkk-dkfl-RandP-Otherkk 	WHERE Month='$CheckMonth' and Number='$Number'";
$upAction=@mysql_query($upRecode);

/*
//保存月统计结果
$inRecode1="INSERT INTO $DataIn.kqdataother 
SELECT NULL,Number,'$Dhours','$Whours',
'$Ghours','$GOverTime','$GDropTime',
'$Xhours','$XOverTime','$XDropTime',
'$Fhours','$FOverTime','$FDropTime',
'$InLates','$OutEarlys','$SJhours',
'$BJhours','$YXJhours','$WXJhours','$QQhours','$YBs','$WXhours','$KGhours','$dkhours','$CheckMonth','1','$Operator','1' 
FROM $DataPublic.staffmain WHERE Number='$Number' AND Number NOT IN (SELECT Number FROM $DataIn.kqdataother WHERE Month='$CheckMonth' and Number='$Number')";
$inAction1=@mysql_query($inRecode1);
if ($inAction1){ 
	$Log.="员工".$Number.$chooseMonth."的".$TitleSTR."成功!<br>";
	} 
else{
	$Log.="<div class=redB>员工".$Number.$chooseMonth."的".$TitleSTR."失败! $inRecode1 </div><br>";
	$OperationResult="N";
	}
*/
//步骤4：
//$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
//$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";

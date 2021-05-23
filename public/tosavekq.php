<?php 
//电信-joseph
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../basic/functions.php";
$url="checkinout_mounthreport";
include "../model/modelfunction.php";
//模板基本参数:模板$Login_WebStyle

WinTitle("保存员工月考勤统计结果");
$Login_help="checkinout_mounthreport_save";
//session_register("Login_help"); 
$_SESSION["Login_help"] = $Login_help;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

$ALType="DefaultNumber=$DefaultNumber&defaultMonth=$defaultMonth";

$kqResult = mysql_query("SELECT * FROM $DataIn.kqdata WHERE Number=$DefaultNumber and Month='$defaultMonth'",$link_id);
if($kqRow = mysql_fetch_array($kqResult)){
	$Locks=$kqRow["Locks"];
	if($Locks==1){
		$sql2 = "UPDATE $DataIn.kqdata SET Dhours='$Dhours',Whours='$Whours',Ghours='$Ghours',Xhours='$Xhours',Fhours='$Fhours',InLates='$InLates',OutEarlys='$OutEarlys',SJhours='$SJhours',BJhours='$BJhours',BXhours='$BXhours',WXJhours='$WXJhours',QQhours='$QQhours',WXhours='$WXhours',KGhours='$KGhours',BKhours='$BKhours',YBs='$YBs',Operator='$Operator' WHERE Number=$DefaultNumber and Month='$defaultMonth'";
		$result2 = mysql_query($sql2);
		if($result2){
			$Log="&nbsp;&nbsp;&nbsp;&nbsp; $defaultMonth 员工ID为 $DefaultNumber 考勤统计数据已经更新!($sql2)<br>";
			}
		else{
			$Log="<div class=redB>&nbsp;&nbsp;&nbsp;&nbsp; $defaultMonth 员工ID为 $DefaultNumber 考勤统计数据更新失败!($sql2)</div><br>";
			$OperationResult="N";
			}
		}
	else{
		$Log="<div class=redB>&nbsp;&nbsp;&nbsp;&nbsp; $defaultMonth 员工ID为 $DefaultNumber 考勤统计数据已锁定，不能更新！($sql2)</div><br>";
		$OperationResult="N";
		}
	$Log_Funtion="更新";
	}		
else{
//员工ID/月份/应到工时/实到工时/加点工时/休息日加班工时/法定假日加班工时/迟到次数/早退次数/事假工时/病假工时/补休工时/无薪假工时/缺勤工时/无薪工时/旷工工时/被扣工时/夜班次数
	$IN_recodeN="INSERT INTO $DataIn.kqdata 
	(Number,Month,Dhours,Whours,Ghours,Xhours,Fhours,InLates,OutEarlys,SJhours,BJhours,BXhours,WXJhours,QQhours,WXhours,KGhours,BKhours,YBs,Operator)
	VALUES ('$DefaultNumber','$defaultMonth','$Dhours','$Whours','$Ghours','$Xhours','$Fhours','$InLates','$OutEarlys','$SJhours','$BJhours','$BXhours','$WXJhours','$QQhours','$WXhours','$KGhours','$BKhours','$YBs','$Operator')";
	$res=@mysql_query($IN_recodeN);
	if($res){
		$Log="&nbsp;&nbsp;&nbsp;&nbsp; $defaultMonth 员工ID为 $DefaultNumber 考勤统计数据已经保存!($IN_recodeN)<br>";
		}
	else{
		$Log="<div class=redB>&nbsp;&nbsp;&nbsp;&nbsp; $defaultMonth 员工ID为 $DefaultNumber 考勤统计数据保存失败!($IN_recodeN)</div><br>";
		$OperationResult="N";
		}
	$Log_Funtion="新增";
	}
//操作日志
	$Log_Item="配件分类";
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
	include "../model/logpage.php";

?>

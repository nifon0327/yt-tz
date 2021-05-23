<?php 
//电信-joseph
//代码、数据共享-EWEN 2012-08-15
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//检查是否已经存在记录，是则更新，否则新增
//DoorId 门禁ID
//WeekDay星期
//TimeType起始还是结束
//PowerType 权限类型 
//Parameters 三次参数部门或职位ID 
//Time 时间
$Operator=$Login_P_Number;
$DateTime=date("Y-m-d H:i:s");
$Time=$Time.":00";
if($PowerType==1){//自定义
	$checkSql=mysql_query("SELECT * FROM $DataPublic.accessguard_powermyself WHERE DoorId='$DoorId' AND WeekDay='$WeekDay' AND Number='$Parameters'",$link_id);
	if($checkRow=mysql_fetch_array($checkSql)){
		$SetStr=$TimeType==1?"TimeS='$Time'":"TimeE='$Time'";
		$inRecode= "UPDATE $DataPublic.accessguard_powermyself SET $SetStr WHERE DoorId='$DoorId' AND WeekDay='$WeekDay' AND Number='$Parameters'";
		}
	else{
		$TimeS=$TimeType==1?$Time:"00:00:00";
		$TimeE=$TimeType!=1?$Time:"00:00:00";
		$inRecode="INSERT INTO $DataPublic.accessguard_powermyself (Id,Number,DoorId,WeekDay,TimeS,TimeE,Date,Estate,Locks,Operator) VALUES (NULL,'$Parameters','$DoorId','$WeekDay','$TimeS','$TimeE','$DateTime','1','0','$Operator')";
		}
	$inAction=@mysql_query($inRecode);
	if($inAction){ 
		echo "1";
		} 
	else{
		echo "0";
		} 	
	}
else{//非自定义
	$checkSql=mysql_query("SELECT * FROM $DataPublic.accessguard_powerfixed WHERE PowerType='$PowerType' AND DoorId='$DoorId' AND WeekDay='$WeekDay' AND Parameters='$Parameters'",$link_id);
	if($checkRow=mysql_fetch_array($checkSql)){
		$SetStr=$TimeType==1?"TimeS='$Time'":"TimeE='$Time'";
		$inRecode= "UPDATE $DataPublic.accessguard_powerfixed SET $SetStr WHERE PowerType='$PowerType' AND DoorId='$DoorId' AND WeekDay='$WeekDay' AND Parameters='$Parameters'";
		}
	else{
		$TimeS=$TimeType==1?$Time:"00:00:00";
		$TimeE=$TimeType!=1?$Time:"00:00:00";
		$inRecode="INSERT INTO $DataPublic.accessguard_powerfixed (Id,PowerType,DoorId,Parameters,WeekDay,TimeS,TimeE,Date,Estate,Locks,Operator) VALUES (NULL,'$PowerType','$DoorId','$Parameters','$WeekDay','$TimeS','$TimeE','$DateTime','1','0','$Operator')";
		}
	$inAction=@mysql_query($inRecode);
	if($inAction){ 
		echo "1";
		} 
	else{
		echo "0";
		} 
	}
?>
<?php 
	include "../../basic/parameter.inc";
	date_default_timezone_set('Asia/Shanghai');
	
	$Today = date("Y-m-d");
	$chkIOMsg = array();
	
	$dataSql = "SELECT * From 
				(SELECT st.Name, st.Number,chk.CheckTime,chk.CheckType,af.Floor,chk.dFrom, ais.Floor as targetFloor 
				FROM $DataIn.checkinout as chk 
				Left Join $DataPublic.staffmain as st On chk.Number = st.Number
				Left Join $DataPublic.attendance_floor as af On af.Id = st.AttendanceFloor
				Left Join $DataPublic.attendanceipadsheet as ais  On ais.Name = chk.dFrom
				WHERE  chk.KrSign=0 
				AND DATE_FORMAT(chk.CheckTime,'%Y-%m-%d')='$Today'
				Union ALL
				SELECT st.Name, st.Number,chk.CheckTime,chk.CheckType,af.Floor,chk.dFrom, ais.Floor as targetFloor 
				FROM $DataIn.checkinout_dd as chk 
				Left Join $DataPublic.staffmain as st On chk.Number = st.Number
				Left Join $DataPublic.attendance_floor as af On af.Id = st.AttendanceFloor
				Left Join $DataPublic.attendanceipadsheet as ais  On ais.Name = chk.dFrom
				WHERE  chk.KrSign=0 
				AND DATE_FORMAT(chk.CheckTime,'%Y-%m-%d')='$Today') as a
				ORDER BY a.CheckTime DESC ";
	
	//echo $dataSql;
	
	$dataResult = mysql_query($dataSql);
	while($dataMsg = mysql_fetch_assoc($dataResult))
	{
		$name = $dataMsg["Name"];
		$Number = $dataMsg["Number"];
		$chkTime = $dataMsg["CheckTime"];
		//$chkTime = substr($chkTime,11,5);
		$chkType = $dataMsg["CheckType"];
		
		
		
		include_once("../../model/subprogram/factoryCheckDate.php");
		if(skipStaff($Number))
		{
			continue;
		}
		
		$floor = $dataMsg["Floor"];
		$targetFloor = $dataMsg["targetFloor"];
		$dFrom = ($dataMsg["dFrom"] != "")?$dataMsg["dFrom"]:"无信息";
		if($dFrom == "1")
		{
			$dFrom = "人事";
		}
		
		$error = "N";
		if($floor != $targetFloor)
		{
			$error = "Y";
		}
		
		$chkIOMsg[] = array("$name", "$chkTime", "$chkType", "$dFrom", "$error");
	}
	
	//print_r($chkIOMsg);
	echo json_encode($chkIOMsg);
	
?>
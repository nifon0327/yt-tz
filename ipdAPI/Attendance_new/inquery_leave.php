<?php
	
	include "../../basic/parameter.inc";
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/LeaveDecorator.php");

	$number = $_POST["Number"];
	//$number = "11008";
	if($number == "")
	{
		return ;
	}

	$staff = new LeaveStaffAvatar($number, $DataIn, $DataPublic, $link_id);

	$leaveArray = array();
	$leaveResultSql ="SELECT J.Id,J.StartDate,J.EndDate,J.Reason,J.bcType,J.Estate, J.Operator,J.Type, B.Name,T.Name AS TypeName
			FROM $DataPublic.kqqjsheet J  
			LEFT JOIN $DataPublic.qjtype T ON J.Type=T.Id
			Left Join $DataPublic.staffmain B On B.Number = J.Operator
			WHERE 1 AND J.Number=".$staff->getStaffNumber()." ORDER BY J.StartDate Desc";

	$leaveResult = mysql_query($leaveResultSql);
	while($leaveResultRow = mysql_fetch_assoc($leaveResult))
	{
		$staffClone = clone $staff;
		$staffClone->getLeaveInfomation($leaveResultRow, $DataIn, $DataPublic, $link_id);

		$leaveArray[] = $staffClone->outputLeaveInf();
	}

	$titles = array("请假起始日期","请假结束日期","请假工时","请假分类","请假原因","状态");
	$widths = array("160","160","80","100","250","100");

	echo json_encode(array(

			"titles" => $titles,
			"widths" => $widths,
			"inquerys" => $leaveArray
		));

?>
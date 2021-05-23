<?php

	include "../../basic/parameter.inc";
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/WageDecorator.php");
	include_once("$path/ipdAPI/Attendance_new/AttendanceClass/TotleWageDecorator.php");

	$number = $_POST["Number"];
	//$number = "11008";
	if($number == ""){
		return ;
	}

	$wages = array();
	$totleWage = new TotleWageStaffAvatar($number,$DataIn, $DataPublic, $link_id);
	$originalSingleWage = new WageStaffAvatar($number,$DataIn, $DataPublic, $link_id);

	$baseMonth_TEST = "2015-08";
	if ($number == 11010 || $number == 11093) {
		$baseMonth_TEST = "2014-08";
	}

	$wagesSql = sprintf("Select S.Month,S.Dx,S.Gljt,S.Gwjt,S.Jj,S.Shbz,S.Zsbz,S.Jbf,S.Yxbz,S.Jz,S.Sb,S.Kqkk,S.RandP,S.Otherkk,S.Amount, S.Gjj,S.Jtbz,S.taxbz,S.Estate, S.Ywjj,M.ComeIn From $DataIn.cwxzsheet S Left Join $DataPublic.staffmain M ON M.Number=S.Number Where 1 And S.Number='".$originalSingleWage->getStaffNumber()."' AND Month > '$baseMonth_TEST' Order By S.Month Desc");
		
	$wageResult = mysql_query($wagesSql);
	while($wagesRow = mysql_fetch_assoc($wageResult))
	{
		$cloneWage = clone $originalSingleWage;
		$cloneWage->getWageInfomation($wagesRow);

		$wages[] = $cloneWage->outputWageInfomation();
		$totleWage->calculateTotleWage($cloneWage);
	}

	//array_unshift($wages, $totleWage->outputWageInfomation());

	// $titles = array("月份","实发","底薪","工龄津贴","岗位津贴","奖金", "其他奖金","生活补助","住宿补助","加班费","夜宵补助","交通补助","个税补贴","借支","社保扣款","公积金","考勤扣款","个税","其他扣款");
	// $widths = array("100","100","100","100","100","100","100", "100","100","100","100","100","100","100","100","100","100","100","100");

	$titles = array("月份","实发","底薪","加班费","工龄津贴","岗位津贴", "生活/住宿补助","交通补助","就学补助","绩效奖金","其他奖金","考勤扣款", "取消津贴","社保","公积金","个税");
	$widths = array("100","100","100","100","100","100","150", "100","100","100","100","100","100","100","100","100");

	echo json_encode(array(

			"titles" => $titles,
			"widths" => $widths,
			"inquerys" => $wages
		));

?>
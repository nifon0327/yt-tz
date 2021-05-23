<?php 
	
	include "../../basic/parameter.inc";
	include("getStffNumber.php");
	
	$number = $_POST["idNum"];
	if(strlen($number) != 5)
	{
		$number = getStaffNumber($number, $DataPublic);
	}

	$personalListSql = "Select A.Month From $DataPublic.wage_list A
						Left Join $DataPublic.staffmain B On B.Number = $number 
						Left Join $DataPublic.wage_list_sign C On C.Number = B.Number And A.Month = C.SignMonth
						Where DATE_FORMAT( B.ComeIn, '%Y-%m') <= A.Month
						And A.Month > '2012-06'
						And A.cSign = B.cSign
						Order By A.Month Desc";

						
	$listArray = array();					
	$listResult = mysql_query($personalListSql);
	while($listRow = mysql_fetch_assoc($listResult))
	{
		$listArray[] = $listRow["Month"];
	}
	
	echo json_encode($listArray);
	
?>
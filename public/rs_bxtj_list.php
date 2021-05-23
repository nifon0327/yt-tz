<?php
	
	include "../model/modelhead.php";
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/ipdAPI/webClass/BxClass/StaffBxItem.php");
	$From=$From==""?"read":$From;
	
	$number = $_GET["number"];
	
	//需处理参数
	$ColsNumber=16;				
	$tableMenuS=450;
	ChangeWtitle("$SubCompany 员工补休记录");
	$funFrom="rs_bx";
	$nowWebPage=$funFrom."_read";
	//$sumCols="10";		//求和列
	$Th_Col="序号|35|员工Id|45|员工姓名|60|加班开始时间|125|加班结束时间|125|备注|150|小计|50|登记日期|70|操作员|50|";
	$Pagination=$Pagination==""?1:$Pagination;
	$Page_Size = 200;

	include "../model/subprogram/read_model_3.php";
	//include "../model/subprogram/read_model_5.php";
	
	$bxItemOriginal = new StaffBxItem();
	$bxItemOriginal->setStaffInfomaiton($number, $DataIn, $DataPublic, $link_id);
	
	//步骤6：需处理数据记录处理
	$i=1;
	List_Title($Th_Col,"1",1);
	$mySql="SELECT J.Id,J.StartDate,J.EndDate,J.Date,J.Operator,J.Note, J.type,J.Number,J.hours
			FROM $DataPublic.bxSheet J 
			LEFT JOIN $DataPublic.staffmain M ON J.Number=M.Number
			WHERE J.Number = '$number' and M.Estate = 1 $SearchRows $Orderby";	
	//echo($mySql); 
	$myResult = mysql_query($mySql." $PageSTR",$link_id);
	if($myResult  && $myRow = mysql_fetch_array($myResult))
	{
		do
		{
			$m=1;
			$cloneBxItem = clone $bxItemOriginal;
			$cloneBxItem->setupBxItem($myRow, $DataIn, $DataPublic, $link_id);			
			
			echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
			echo"<td class='A0111' width=35 align='center' $colColor> $i</td>";
			echo"<td class='A0101' width=45 align='center'>".$cloneBxItem->getStaffNumber()."</td>";
			echo"<td class='A0101' width=60 align='center'>".$cloneBxItem->getStaffName()."</td>";//
			echo"<td class='A0101' width=125 align='center'>".$cloneBxItem->getStartDate()." </td>";//
			echo"<td class='A0101' width=125 align='center'>".$cloneBxItem->getEndDate()."</td>";//
			echo"<td class='A0101' width=150 align='center'>".$cloneBxItem->getNote()."</td>";//
			echo"<td class='A0101' width=50 align='center'>".$cloneBxItem->getHours()."h</td>";//
			echo"<td class='A0101' width=70 align='center'>".$cloneBxItem->getLogDate()."</td>";//
			echo"<td class='A0101' width=50 align='center'>".$cloneBxItem->getOperator()."</td>";//
			echo"</tr></table>";
			$i++;
			$checkidValue=$Id;
			//include "../model/subprogram/read_model_6.php";
		}
		while ($myRow = mysql_fetch_array($myResult));
	}
	else
	{
		noRowInfo($tableWidth);
  	}

	//步骤7：
echo '</div>';
		
?>
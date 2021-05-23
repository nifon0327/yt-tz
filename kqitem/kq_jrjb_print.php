<?php 
//电信-EWEN
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=15;				
$tableMenuS=600;
$sumCols="11";		//求和列
ChangeWtitle("$SubCompany 假日加班统计");
$funFrom="kq_jrjb";
$nowWebPage=$funFrom."_read";
$Th_Col="序号|40|员工ID|50|员工姓名|60|职位|60|2倍时薪|60|2倍工时|60|3倍时薪|60|3倍工时|60|小计|60";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$Field[$i];
	$tableWidth=$tableWidth+$Field[$i];
	}
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
//步骤3：
$j=1;
List_Title($Th_Col,"1",0);
//有效的员工
$mySql="SELECT S.Id,S.Number,S.Month,S.xHours,S.xWage,S.fHours,S.fWage,S.Amount,S.Date,S.Estate,S.Locks,S.Operator,M.Name,J.Name AS Job
FROM $DataIn.hdjbsheet S
LEFT JOIN $DataPublic.staffmain M ON S.Number=M.Number
LEFT JOIN $DataPublic.jobdata J ON J.Id=S.JobId
WHERE 1 and S.Month='$chooseMonth' ORDER BY M.BranchId,M.JobId,M.Number";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];	
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$Job=$myRow["Job"];
		$xWage=$myRow["xWage"];
		$fWage=$myRow["fWage"];
		$xHours=zerotospace($myRow["xHours"]);
		$sumxHours+=$xHours;
		$fHours=zerotospace($myRow["fHours"]);
		$sumfHours+=$fHours;
		$Amount=sprintf("%.0f",$myRow["Amount"]);
		$sumAmount+=$Amount;
		$ValueArray=array(
			array(0=>$Number,	1=>"align='center'"),
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$Job, 		1=>"align='center'"),
			array(0=>$xWage, 	1=>"align='center'"),
			array(0=>$xHours, 	1=>"align='center'"),
			array(0=>$fWage,	1=>"align='center'"),
			array(0=>$fHours,	1=>"align='center'"),
			array(0=>$Amount,	1=>"align='center'")
			);
		$checkidValue=$Id;
		$ChooseOut="N";
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	$sumxHours=zerotospace($sumxHours);
	$sumfHours=zerotospace($sumfHours);
	echo"
	<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>
	<td width='210' align='center' class='A0111'>合计</td>
	<td width='60' class='A0101'>&nbsp;</td>
	<td width='60' class='A0101' align='center'>$sumxHours</td>
	<td width='60' class='A0101'>&nbsp;</td>
	<td width='60' class='A0101' align='center'>$sumfHours</td>
	<td width='60' class='A0101' align='center'>$sumAmount</td>	
	</table>";
	}
else{
	noRowInfo($tableWidth);
	}
//步骤7：
echo '</div>';
?>

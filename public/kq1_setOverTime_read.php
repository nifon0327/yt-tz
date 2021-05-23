<?php 
//电信-EWEN
	include "../model/modelhead.php";
	$From=$From==""?"read":$From;
	//需处理参数
	$ColsNumber=16;				
	$tableMenuS=450;
	ChangeWtitle("$SubCompany 加班时间设定");
	$funFrom="kq1_setOverTime";
	$nowWebPage=$funFrom."_read";
	$Th_Col="选项|40|序号|40|日期|100|星期|100|1.5倍|60|2倍|60|3倍|60";
	$Pagination=$Pagination==""?1:$Pagination;
	$Page_Size = 200;
	$ActioToS="1,3";
	//步骤3：
	include "../model/subprogram/read_model_3.php";
	//步骤4：需处理-条件选项
	$chooseMonth = $chooseMonth==''?date("Y-m"):$chooseMonth;
	$hasCurrentMonthSql = "Select * From $DataIn.kqovertime Where DATE_FORMAT(otDate,'%Y-%m') = '$chooseMonth'";
	$hasCurrentMonthResult = mysql_query($hasCurrentMonthSql);
	if(mysql_num_rows($hasCurrentMonthResult) == 0){
		$FristDay=$chooseMonth."-01";
		$EndDay=date("Y-m-t",strtotime($FristDay));
		$Days=date("t",strtotime($FristDay));
		$insertVauleArray = array();
		for($i=0;$i<$Days;$i++)
		{
			$CheckDate=date("Y-m-d",strtotime("$FristDay + $i days"));
			$insertVauleArray[] = "(NULL, '$CheckDate', '1')";
		}
		
		$insertValue = implode(",", $insertVauleArray);
		$insertValueSql = "insert into $DataIn.kqovertime (Id, otDate, Estate) values $insertValue";
		mysql_query($insertValueSql);
	}
	
	//选择框
	// echo"<select name='chooseMonth' id='chooseMonth' onchange='RefreshPage(\"$nowWebPage\")'>";
	// $date_Result = mysql_query("SELECT DATE_FORMAT(otDate,'%Y-%m') AS Month FROM $DataIn.kqovertime group by DATE_FORMAT(otDate,'%Y-%m') order by otDate DESC",$link_id);
	// if ($dateRow = mysql_fetch_array($date_Result)) 
	// {
	// 	do
	// 	{
	// 		$dateValue=$dateRow["Month"];
	// 		$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
	// 		if($chooseMonth==$dateValue){

	// 			echo"<option value='$dateValue' selected>$dateValue</option>";
	// 			$SearchRows.="DATE_FORMAT(otDate,'%Y-%m')='$dateValue'";
	// 		}
	// 		else
	// 		{
	// 			echo"<option value='$dateValue'>$dateValue</option>";					
	// 		}
	// 	}while($dateRow = mysql_fetch_array($date_Result));
	// }
	
	// echo"</select>&nbsp;";
	// $searchMonth = $SearchRows;
	echo"<input type='text' name='chooseMonth' id='chooseMonth' value='$chooseMonth' onchange='RefreshPage(\"$nowWebPage\")'>";
	
	//步骤5：
	include "../model/subprogram/read_model_5.php";
	//步骤6：需处理数据记录处理
	$i=1;
	$j=1;
	List_Title($Th_Col,"1",1);
	
	$sumWorkHours = 0;
	$sumWeekHours = 0;
	$sumHolidayHours = 0;
	
	$mySql="Select * From $DataIn.kqovertime Where DATE_FORMAT(otDate,'%Y-%m') = '$chooseMonth'";
	$myResult = mysql_query($mySql);
	if($myRow = mysql_fetch_array($myResult))
	{
		do
		{
			$m=1;
			$Id =  $myRow["Id"];
			$otDate = $myRow["otDate"];
			$workdayHours = $myRow["workday"];
			$weekdayHours = $myRow["weekday"];
			$holidayHours = $myRow["holiday"];
			$eState = $myRow["Estate"];
			
			//星期确定
			$DateTemp=date("Y-m-d",strtotime($otDate));	
			$Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");	//星期数组
			$weekTemp=date("w",strtotime($DateTemp));									//当天属于星期几 
			$ddSTR="";
			//日期类型确定:工作日\休息日\假日(休息日和假日不计迟到早退)
			$DateType=($weekTemp==6 || $weekTemp==0)?"X":"G";		
			$holidayResult = mysql_query("SELECT * FROM $DataPublic.kqholiday WHERE Date='$DateTemp'",$link_id);
			if($holidayRow = mysql_fetch_array($holidayResult)){
				switch($holidayRow["Sign"]){
				case "F":
				$DateType="F";
				$ddSTR="<br><div class='yellowB'>$holidayRow[Name]</div>";
				break;
				case "Y":
				$DateType="Y";
				$ddSTR="<br><div class='yellowB'>$holidayRow[Name]</div>";
				break;
				case "W":
				$DateType="W";
				$ddSTR="<br><div class='yellowB'>$holidayRow[Name]</div>";
				break;
				}
			}
//是否存在调班？是,则工作日变休息日,休息日变工作日
		//$rqddResult = mysql_query("SELECT * FROM $DataIn.kqrqdd WHERE GDate='$DateTemp' or XDate='$DateTemp'",$link_id);
		$rqddResult = mysql_query("SELECT * FROM $DataIn.kqrqdd WHERE (GDate='$DateTemp' or XDate='$DateTemp') and Number='$Number'",$link_id);
		if($rqddRow = mysql_fetch_array($rqddResult))
		{			
			$ddSTR=$DateType=="X"?"<br><div class='yellowB'>调为工作日</div>":"<br><div class='yellowB'>调为休息日</div>";
			$DateType=$DateType=="X"?"G":"X";
		}
			
		$weekDay=$DateType."-"."星期".$Darray[$weekTemp].$ddSTR;							//用于输出的星期标签
		//星期确定结束
		
		$sumWorkHours += $workdayHours;
		$sumWeekHours += $weekdayHours;
		$sumHolidayHours += $holidayHours;
		
		$ValueArray=array(
			array(0=>$otDate,		1=>"align='center'"),
			array(0=>$weekDay,		 	1=>"align='center'"),
			array(0=>$workdayHours,		1=>"align='center'"),
			array(0=>$weekdayHours,			1=>"align='center'"),
			array(0=>$holidayHours, 		1=>"align='center'")
			);
		$checkidValue=$Id;
		//$LockRemark="";
		//
		
		$Keys = 31;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
	else
	{
		noRowInfo($tableWidth);
  	}
  	
  	echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
  	echo "<tr>";
  	echo "<td> </tr>";
  	echo "<td> </tr>";
  	echo "<td>1.5倍总工时: $sumWorkHours</tr>";
  	echo "<td>2倍总工时: $sumWeekHours</tr>";
  	echo "<td>3倍总工时: $sumHolidayHours</tr>";
  	echo "</tr>";
  	echo "</table>";
  	
  	//步骤7：
echo '</div>';
  	List_Title($Th_Col,"0",1);
  	$myResult = mysql_query($mySql,$link_id);
  	if ($myResult ) $RecordToTal= mysql_num_rows($myResult);
  	pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
  	include "../model/subprogram/read_model_menu.php";
?>

<?php
	//用于验厂时读取加班时间的设置
	//先检查是否有当月的时间
	include "../model/modelhead.php";
	$From=$From==""?"read":$From;
	//需处理参数
	$ColsNumber=7;				
	$tableMenuS=450;
	ChangeWtitle("$SubCompany 加班时间设定");
	$funFrom="kq_overTimeSet";
	$nowWebPage=$funFrom."_read";
	$Th_Col="选项|40|序号|40|日期|100|星期|100|1.5倍|60|2倍|60|3倍|60";
	$Pagination=$Pagination==""?1:$Pagination;
	$Page_Size = 200;
	$ActioToS="1,2,3,4,7,8";
	//步骤3：
	include "../model/subprogram/read_model_3.php";
	
	//步骤4：需处理-条件选项
	include "../model/subprogram/read_model_5.php";
	$i=0;
	$j=1;
	List_Title($Th_Col,"1",0);
	$mySql="Select * From $DataIn.kqovertime Where $searchMonth";
	$myResult = mysql_query($mySql);
	if($myRow = mysql_fetch_array($myResult))
	{
		do
		{
			$m=1;
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
		$ValueArray=array(
			array(0=>$otDate,		1=>"align='center'"),
			array(0=>$weekDay,		 	1=>"align='center'"),
			array(0=>$workdayHours,		1=>"align='center'"),
			array(0=>$weekdayHours,			1=>"align='center'"),
			array(0=>$holidayHours, 		1=>"align='center'")
			);
		$checkidValue=$Id;
		//$LockRemark="";
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
	else
	{
		noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
//echo $Keys."?";
include "../model/subprogram/read_model_menu.php";
?>
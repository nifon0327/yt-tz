<?php 
//电信-ZX  2012-08-01
include "../basic/parameter.inc";
include "../model/modelfunction.php";
include "kqcode/kq_function.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

//拆分参数
$TempArray=explode(",",$searchStr);
$sAction=$TempArray[0];	//选框：1为年份，2为月份，3为项目
$sType=$TempArray[1];	//查询项目：1为考勤，2为薪资，3为请假，4为年假
$Number=$TempArray[2];	//员工ID
$sYear=$TempArray[3];	//查询的年份
$sMonth=$TempArray[4];	//查询的月份

$StaffType=0;
//$checkSql=mysql_query("SELECT Id FROM $DataPublic.staffmain WHERE Number='$Number' AND cSign=7 AND Estate='1' LIMIT 1",$link_id);
$checkSql=mysql_query("SELECT Id FROM $DataPublic.staffmain WHERE Number='$Number' AND cSign=$Login_cSign AND Estate='1' LIMIT 1",$link_id);

if($checkRow=mysql_fetch_array($checkSql)){
	$StaffType=1;
	}
//返回值预设
$T="";
$Y="";
$M="";
switch($sType){
	case 1://考勤查询
			if($StaffType==0){		//临时工查询
				//////////////////
				//选框来源
				if($sAction==3){//来自于查询项目改变，则要求计算该员工有效的考勤年份和月份，且需返回年份和月份选框
					///////////////////
					//年份
					$CheckYearSql=mysql_query("SELECT DATE_FORMAT(CheckTime,'%Y') AS Year FROM $DataIn.checkiotemp WHERE 1 AND Number=$Number GROUP BY DATE_FORMAT(CheckTime,'%Y') ORDER BY CheckTime DESC",$link_id);
					if($CheckYearRow=mysql_fetch_array($CheckYearSql)){
						$Y="<select name='sYear' id='sYear' style='width:85px' onChange='javascript:ToSearch(1)'>";
						$sYear="";
						do{
							$YearTemp=$CheckYearRow["Year"];
							if($sYear=="")$sYear=$YearTemp;
							$Y.="<option value='$YearTemp'>$YearTemp</option>";
							}while ($CheckYearRow=mysql_fetch_array($CheckYearSql));
						$Y.="</select> 年";
						}
					//月份
					$CheckMonthSql=mysql_query("SELECT DATE_FORMAT(CheckTime,'%m') AS Month FROM $DataIn.checkiotemp WHERE 1 AND Number=$Number AND DATE_FORMAT(CheckTime,'%Y')='$sYear' GROUP BY DATE_FORMAT(CheckTime,'%Y-%m') ORDER BY CheckTime DESC",$link_id);
					if($CheckMonthRow=mysql_fetch_array($CheckMonthSql)){
						$M="<select name='sMonth' id='sMonth' style='width:85px' onChange='javascript:ToSearch(2)'>";
						$sMonth="";
						do{
							$MonthTemp=$CheckMonthRow["Month"];
							if($sMonth=="")$sMonth=$MonthTemp;
							$M.="<option value='$MonthTemp'>$MonthTemp</option>";
							}while ($CheckMonthRow=mysql_fetch_array($CheckMonthSql));
						$M.="</select> 月";
						}
					///////////////////
					}
				else{
					if($sAction==1){//来自于改变年份，则要求月份重新计算，默认返回当年最后一个月资料
						//计算当年最后一个月以及需返回的月份选框
						//月份
						$CheckMonthSql=mysql_query("SELECT DATE_FORMAT(CheckTime,'%m') AS Month FROM $DataIn.checkiotemp WHERE 1 AND Number=$Number AND DATE_FORMAT(CheckTime,'%Y')='$sYear' GROUP BY DATE_FORMAT(CheckTime,'%Y-%m') ORDER BY CheckTime DESC",$link_id);
						if($CheckMonthRow=mysql_fetch_array($CheckMonthSql)){
							$M="<select name='sMonth' id='sMonth' style='width:85px' onChange='javascript:ToSearch(2)'>";
							$sMonth="";
							do{
								$MonthTemp=$CheckMonthRow["Month"];
								if($sMonth=="")$sMonth=$MonthTemp;
								$M.="<option value='$MonthTemp'>$MonthTemp</option>";
								}while ($CheckMonthRow=mysql_fetch_array($CheckMonthSql));
							$M.="</select> 月";
							}
						}
					}
				if($sYear!="" && $sMonth!=""){
					///////////////////
					include "report_kq_lsg.php";
					}
				echo $T."@".$Y."@".$M;
				}
			else{					//正式工查询 OK
				//选框来源
				if($sAction==3){//来自于查询项目改变，则要求计算该员工有效的考勤年份和月份，且需返回年份和月份选框
					///////////////////
					//年份
					$CheckYearSql=mysql_query("SELECT DATE_FORMAT(CheckTime,'%Y') AS Year FROM $DataIn.checkinout WHERE 1 AND Number=$Number GROUP BY DATE_FORMAT(CheckTime,'%Y') ORDER BY CheckTime DESC",$link_id);
					if($CheckYearRow=mysql_fetch_array($CheckYearSql)){
						$Y="<select name='sYear' id='sYear' style='width:85px' onChange='javascript:ToSearch(1)'>";
						$sYear="";
						do{
							$YearTemp=$CheckYearRow["Year"];
							if($sYear=="")$sYear=$YearTemp;
							$Y.="<option value='$YearTemp'>$YearTemp</option>";
							}while ($CheckYearRow=mysql_fetch_array($CheckYearSql));
						$Y.="</select> 年";
						}
					//月份
					$CheckMonthSql=mysql_query("SELECT DATE_FORMAT(CheckTime,'%m') AS Month FROM $DataIn.checkinout WHERE 1 AND Number=$Number AND DATE_FORMAT(CheckTime,'%Y')='$sYear' GROUP BY DATE_FORMAT(CheckTime,'%Y-%m') ORDER BY CheckTime DESC",$link_id);
					if($CheckMonthRow=mysql_fetch_array($CheckMonthSql)){
						$M="<select name='sMonth' id='sMonth' style='width:85px' onChange='javascript:ToSearch(2)'>";
						$sMonth="";
						do{
							$MonthTemp=$CheckMonthRow["Month"];
							if($sMonth=="")$sMonth=$MonthTemp;
							$M.="<option value='$MonthTemp'>$MonthTemp</option>";
							}while ($CheckMonthRow=mysql_fetch_array($CheckMonthSql));
						$M.="</select> 月";
						}
					///////////////////
					}
				else{
					if($sAction==1){//来自于改变年份，则要求月份重新计算，默认返回当年最后一个月资料
						//计算当年最后一个月以及需返回的月份选框
						//月份
						$CheckMonthSql=mysql_query("SELECT DATE_FORMAT(CheckTime,'%m') AS Month FROM $DataIn.checkinout WHERE 1 AND Number=$Number AND DATE_FORMAT(CheckTime,'%Y')='$sYear' GROUP BY DATE_FORMAT(CheckTime,'%Y-%m') ORDER BY CheckTime DESC",$link_id);
						if($CheckMonthRow=mysql_fetch_array($CheckMonthSql)){
							$M="<select name='sMonth' id='sMonth' style='width:85px' onChange='javascript:ToSearch(2)'>";
							$sMonth="";
							do{
								$MonthTemp=$CheckMonthRow["Month"];
								if($sMonth=="")$sMonth=$MonthTemp;
								$M.="<option value='$MonthTemp'>$MonthTemp</option>";
								}while ($CheckMonthRow=mysql_fetch_array($CheckMonthSql));
							$M.="</select> 月";
							}
						}
					}
				if($sYear!="" && $sMonth!=""){
					include "report_kq_zsg.php";
					}
				echo $T."@".$Y."@".$M;
				}
	break;
	case 2://薪资查询
		if($StaffType==0){		//临时工查询
			if($sAction==3){//来自于查询项目改变，则要求计算该员工有效的薪资年份，且需返回年份
				///////////////////
				//年份
				$CheckYearSql=mysql_query("SELECT left(Month,4) AS Year FROM $DataIn.cwxztempsheet WHERE 1 AND Number=$Number GROUP BY left(Month,4) ORDER BY Month DESC",$link_id);
				if($CheckYearRow=mysql_fetch_array($CheckYearSql)){
					$Y="<select name='sYear' id='sYear' style='width:85px' onChange='javascript:ToSearch(1)'>";
					$sYear="";
					do{
						$YearTemp=$CheckYearRow["Year"];
						if($sYear=="")$sYear=$YearTemp;
						$Y.="<option value='$YearTemp'>$YearTemp</option>";
						}while ($CheckYearRow=mysql_fetch_array($CheckYearSql));
					$Y.="</select> 年";
					}
				///////////////////
				}
			if($sYear!=""){
				include "report_xz_lsg.php";
				}
			echo $T."@".$Y."@".$M;
			}
		else{					//正式工查询 OK
			if($sAction==3){//来自于查询项目改变，则要求计算该员工有效的薪资年份，且需返回年份
				///////////////////
				//年份
				$CheckYearSql=mysql_query("SELECT left(Month,4) AS Year FROM $DataIn.cwxzsheet WHERE 1 AND Number=$Number GROUP BY left(Month,4) ORDER BY Month DESC",$link_id);
				if($CheckYearRow=mysql_fetch_array($CheckYearSql)){
					$Y="<select name='sYear' id='sYear' style='width:85px' onChange='javascript:ToSearch(1)'>";
					$sYear="";
					do{
						$YearTemp=$CheckYearRow["Year"];
						if($sYear=="")$sYear=$YearTemp;
						$Y.="<option value='$YearTemp'>$YearTemp</option>";
						}while ($CheckYearRow=mysql_fetch_array($CheckYearSql));
					$Y.="</select> 年";
					}
				///////////////////
				}
			if($sYear!=""){
				include "report_xz_zsg.php";
				}
			echo $T."@".$Y."@".$M;
			}
	break;
	case 3://请假查询
		if($StaffType==1){		//正式工查询	OK
			if($sAction==3){//来自于查询项目改变，则要求计算该员工有效的请假年份，且需返回年份,请假年份，从入职当年起至现在
				///////////////////
				//年份
				$CheckYearSql=mysql_query("SELECT DATE_FORMAT(StartDate,'%Y') AS Year FROM $DataPublic.kqqjsheet WHERE 1 AND Number=$Number GROUP BY DATE_FORMAT(StartDate,'%Y') ORDER BY StartDate DESC",$link_id);
				if($CheckYearRow=mysql_fetch_array($CheckYearSql)){
					$Y="<select name='sYear' id='sYear' style='width:85px' onChange='javascript:ToSearch(1)'>";
					$sYear="";
					do{
						$YearTemp=$CheckYearRow["Year"];
						if($sYear=="")$sYear=$YearTemp;
						$Y.="<option value='$YearTemp'>$YearTemp</option>";
						}while ($CheckYearRow=mysql_fetch_array($CheckYearSql));
					$Y.="</select> 年";
					}
				///////////////////
				}
			if($sYear!=""){			
				include "report_qj_zsg.php";
				}
			}
		echo $T."@".$Y."@".$M;
	break;
	case 4://年假查询
		if($StaffType==1){		//正式工查询	OK
			include "report_lj_zsg.php";
			echo $T."@".$Y."@".$M;
			}
	break;
	}
?>

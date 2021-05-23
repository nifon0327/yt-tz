<?php 
//电信-ZX  2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
include "../model/kq_YearHolday.php";
//拆分参数
$TempArray=explode(",",$qjStr);
$Number=$TempArray[0];
$Type=$TempArray[1];
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$StartDate=$TempArray[2];
$EndDate=$TempArray[3];
$MonthTemp=substr($StartDate,0,7);
$ClassTemp="color: #FF0000;font-weight: bold;font-size:70";

if(strtotime($StartDate)>strtotime($DateTime) && strtotime($EndDate)>strtotime($DateTime)){//不允许请少于当前时间的假期
	//include "../model/kq_YearHolday.php"; 函数在这里
	if($Type==4) {
		$ThisHoldDays=GetBetweenDateDays($Number,$StartDate,$EndDate,$Type,$DataIn,$DataPublic,$link_id)/8;  //本次请假换算小时数
		//echo "本次请假天数：$ThisHoldDays <br>";
		$AnnualLeave1=GetYearHolDayDays($Number,$StartDate,$EndDate,$DataIn,$DataPublic,$link_id);
		//echo "应休年休假：$AnnualLeave1 <br>";
		$qjAllDays=HaveYearHolDayDays($Number,$StartDate,$EndDate,$DataIn,$DataPublic,$link_id)/8 ;
		//echo "已休年休假：$qjAllHours <br>";
		$MainDays=$AnnualLeave1-$qjAllDays;
	}
	//$BackInfo="<div class='redB'>申请失败,你的年假还有 $MainDays 天.</div>";
	if(($Type==4) && (($AnnualLeave1==0) || ($ThisHoldDays>$MainDays)) ){  //假期已完成，则显示
		$BackInfo="<div class='redB'>申请失败,你的年假数为$StartDate,$EndDate  <br> $ThisHoldDays <br> $AnnualLeave1 还有 $MainDays 天.</div>";	
	}
	else{
		//新加条件,加入的月份未生成
		$inRecode="INSERT INTO $DataPublic.kqqjsheet 
				SELECT NULL,Number,'$StartDate','$EndDate','','0','$Type','0','1','$Date','0','$Number' 
				FROM $DataPublic.staffmain WHERE Number='$Number' 
				AND Number NOT IN (SELECT Number FROM $DataIn.kqdata WHERE Month='$MonthTemp' ORDER BY Id)";
		$inAction=@mysql_query($inRecode);
		if($inAction){ 
			$BackInfo="<div class='greenB'>已申请</div>";
			$ClassTemp="color: #009900;font-weight: bold;font-size:70";
			} 
		else{
			$BackInfo="<div class='redB'>申请失败,请通知管理员.</div>";
			}
	}
}
else{
	$BackInfo="<div class='redB'>申请失败,时间过期.</div>";
}
//返回信息
echo "<table width=100% height=480><tr>
		<td align='center' valign='middle' style='$ClassTemp'>$BackInfo</td>
		</tr></table>";
?>
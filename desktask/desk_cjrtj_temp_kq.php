<?php   
//步骤1 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
include "kqcode/kq_function.php";
include "subprogram/onehoursalary.php";
$CheckDate=$Date;
//当月的天数
$days=date("t",strtotime($CheckDate));//当月天数
$CheckMonth=substr($CheckDate,0,7);
//当月总工时
$checkTotalWork=mysql_fetch_array(mysql_query("SELECT ifnull(Dhours,0) AS Dhours FROM $DataIn.kqdata WHERE Month='$CheckMonth' ORDER BY Month DESC LIMIT 1",$link_id));
$TotalWork=$checkTotalWork["Dhours"];//该月总工时
$ToDay=$CheckDate;

//试用期薪标准
if ($checkSXFlag!=1){  //首次加载
     $checkSX=mysql_fetch_array(mysql_query("SELECT (Value/2) AS tempSX FROM $DataPublic.cw3_basevalue WHERE Id=3",$link_id));
    $tempygSX=sprintf("%2.f",$checkSX["tempSX"]);  //时薪 
    $checkSXFlag=1;
}

ChangeWtitle("$SubCompany日考勤统计");
echo"$CheckDate"."日 $GroupName 考勤统计";
echo"<table height='61' border='0' cellspacing='0' bgcolor='#FFFFFF'><tr class=''>
		<td width='40' rowspan='2' class='A1111' align='center'>序号</td>
		<td width='40' rowspan='2' class='A1101' align='center'>考勤</td>
		<td width='60' rowspan='2' class='A1101' align='center'>编号</td>
		<td width='60' rowspan='2' class='A1101' align='center'>姓名</td>
		<td width='80' rowspan='2' class='A1101' align='center'>小组</td>
		<td height='19' colspan='2' class='A1101' align='center'>签卡记录</td>
		<td width='60' rowspan='2' class='A1101' align='center'>实到工时</td>
		<td height='19' colspan='3' class='A1101' align='center'>加点加班工时(+直落)</td>
		<td width='70' rowspan='2' class='A1101' align='center'>预估支出</td>
		<td width='70' rowspan='2' class='A1101' align='center'>实际支出</td>
 	</tr>
  	<tr class=''>
		<td width='60' height='20'  align='center' class='A0101'>签到</td>
		<td width='60' class='A0101' align='center'>签退</td>
		<td width='60' align='center' class='A0101'>1.5倍</td>
		<td width='60' class='A0101' align='center'>2倍</td>
		<td width='60' align='center' class='A0101'>3倍</td>
	</tr>";

$MySql="
SELECT K.Number,M.Name,G.GroupName,K.SdTime,K.JbTime,K.JbTime2,K.JbTime3,K.Ybs  
FROM $DataIn.kqdaytemptj K 
LEFT JOIN $DataIn.stafftempmain M ON M.Number=K.Number
LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
WHERE 1 AND K.Date='$CheckDate' GROUP BY K.Number ORDER BY M.GroupId,K.Number";
$Result = mysql_query($MySql,$link_id);
$workTimeSUM=0;
$ygAmount_SUM=0;$sjAmount_SUM=0;
$i=1;
if($myrow = mysql_fetch_array($Result)) {
	do{
		$Name=$myrow["Name"];
		$Number=$myrow["Number"];
		$GroupName=$myrow["GroupName"]==""?"&nbsp;":$myrow["GroupName"];
		$SdTime=$myrow["SdTime"];
		$JbTime=$myrow["JbTime"];
		$JbTime2=$myrow["JbTime2"];
		$JbTime3=$myrow["JbTime3"];
		$Ybs=$myrow["Ybs"];
		$workTime=$SdTime+$JbTime*1.5+$JbTime2*2+$JbTime3*3;
		$workTimeSUM+=$workTime;
		//预估支出
		$ygAmount=$workTime*$tempygSX+$Ybs*5;
		$ygAmount_SUM+=$ygAmount;
		
		//实际支出 统计员工当月总工时
		$checkWTime=mysql_fetch_array(mysql_query("SELECT SUM(SdTime+JbTime*1.5+JbTime2*2+JbTime3*3) AS TotalTime 
		FROM $DataIn.kqdaytemptj WHERE Number='$Number' AND DATE_FORMAT(Date,'%Y-%m')='$CheckMonth' GROUP BY Number",$link_id));
		$TotalTime=$checkWTime["TotalTime"];
		//员工当前薪资
		$checkGZ=mysql_fetch_array(mysql_query("SELECT  Amount FROM $DataIn.cwxztempsheet WHERE Number='$Number' AND Month='$CheckMonth'",$link_id));
	$sjAmount=sprintf("%.0f",$workTime*$checkGZ["Amount"]/$TotalTime);
		$sjAmount_SUM+=$sjAmount;
		$ygSTR=$ygAmount;
		if($ygAmount<$sjAmount){//低估
			$ygSTR="<span class='greenB'>$ygAmount</span>";
			}
		else{
			if($ygAmount>$sjAmount){
				$ygSTR="<span class='redB'>$ygAmount</span>";
				}
			}
		
		//读取签卡记录:签卡记录必须是已经审核的
		$ioResult = mysql_query("SELECT CheckTime,CheckType,KrSign FROM $DataIn.checkiotemp WHERE 1 and Number=$Number and ((CheckTime LIKE '$CheckDate%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$CheckDate%' and KrSign='1')) ORDER BY CheckTime",$link_id);
		if($ioRow = mysql_fetch_array($ioResult)) {
			do{
				$CheckTime=$ioRow["CheckTime"];
				$CheckType=$ioRow["CheckType"];
				$KrSign=$ioRow["KrSign"];
				switch($CheckType){
					case "I":
						$AI=date("Y-m-d H:i:00",strtotime("$CheckTime"));
						$aiTime=date("H:i",strtotime("$CheckTime"));						
						break;
					case "O":
						$AO=date("Y-m-d H:i:00",strtotime("$CheckTime"));
						$aoTime=date("H:i",strtotime("$CheckTime"));
						break;
					}					
				}while ($ioRow = mysql_fetch_array($ioResult));
		}
		$sjAmount=zerotospace($sjAmount);
		$SdTime=(float)$SdTime;$SdTime=zerotospace($SdTime);
		$JbTime=(float)$JbTime;$JbTime=zerotospace($JbTime);
		$JbTime2=(float)$JbTime2;$JbTime2=zerotospace($JbTime2);
		$JbTime3=(float)$JbTime3;$JbTime3=zerotospace($JbTime3);
		
		echo"<tr><td class='A0111' align='center'>$i</td>";
		echo"<td class='A0101' align='center'><span class='greenB'>是</span></td>";
		echo"<td class='A0101' align='center'>$Number</td>";
		echo"<td class='A0101' align='center'>$Name</td>";
		echo"<td class='A0101'>$GroupName</td>";
		echo"<td class='A0101' align='center'><span $AIcolor>$aiTime</span></td>";
		echo"<td class='A0101' align='center'><span $AOcolor>$aoTime</span></td>";
		echo"<td class='A0101' align='center'>$SdTime</td>";
		echo"<td class='A0101' align='center'>$JbTime</td>";
		echo"<td class='A0101' align='center'>$JbTime2</td>";
		echo"<td class='A0101' align='center'>$JbTime3</td>";
		echo"<td class='A0101' align='center'>$ygSTR</td>";
		echo"<td class='A0101' align='center'>$sjAmount</td>";
		//echo"<td class='A0101' align='center'>$saveGS</td>";
		echo"</tr>";
        $i++;
	 }while ($myrow = mysql_fetch_array($Result));
}
else{
	echo"<tr bgcolor='#FFFFFF'><td colspan='13' scope='col' height='60' class='A0111' align='center'><p>没有考勤员工记录</td></tr>";
	}
echo"
<tr class=''>
	<td colspan='7' class='A0111' align='center'>合计</td>
	<td colspan='4' class='A0101' align='center'>$workTimeSUM &nbsp;</td>
	<td class='A0101' align='center'>$ygAmount_SUM</td>
	<td class='A0101' align='center'>$sjAmount_SUM</td>
</tr></table>";
//步骤5：<td class='A0101' align='center'>$Sum_saveGS</td>
echo"<br>签卡记录空白的,是签卡记录有问题或未审核,需人事处理.";
?>
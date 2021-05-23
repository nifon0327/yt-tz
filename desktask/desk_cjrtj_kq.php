<?php   
//2014-01-07 ewen 修正OK
include "../model/modelhead.php";
include "../public/kqcode/kq_function.php";

$CheckDate=$Date;
//当月的天数
$checkDays=date("t",strtotime($CheckDate));//当月天数
$CheckMonth=substr($CheckDate,0,7);
$ToDay=$CheckDate;
ChangeWtitle("$SubCompany 日考勤统计");//需处理<td width='80' rowspan='2' class='A1101'><div align='center'>记录表工时</div></td>

//预估时薪
include "../model/subprogram/onehoursalary.php";
$XZSign=0;//薪资表是否已经生成
//注意：时薪初始化，由于非考勤在职员工会计入统计，所当月无工资的员工（请长假的情况）需做时薪初始化置0值，即此类员工将不计算实际支出
$checkQJSql=mysql_query("SELECT Number FROM $DataPublic.kqqjsheet WHERE StartDate LIKE '$CheckMonth%' OR EndDate LIKE '$CheckMonth%' GROUP BY Number",$link_id);
if($checkQJRow=mysql_fetch_array($checkQJSql)){
	do{
		$sNumber=$checkQJRow["Number"];
		$TempSXSTR="SX".strval($sNumber); 
		$$TempSXSTR=0;								//初始化请假员工的时薪
		}while ($checkQJRow=mysql_fetch_array($checkQJSql));
	}
//实际时薪
//只有当月工资以：6品检、7仓库、8车间三个部门来计算的员工才做统计
$checkKqSqlB=mysql_query("SELECT A.Number,IFNULL(A.Amount+A.Jz+A.Sb+A.RandP+A.Gjj+A.Ct+A.Otherkk,0)/(IFNULL(B.Whours,0)+IFNULL((B.Ghours+B.GOverTime+B.GDropTime)*1.5,0)+IFNULL((B.xHours+B.XOverTime+B.XDropTime)*2,0)+IFNULL((B.FHours+B.FOverTime+B.FDropTime)*3,0)) AS SX
		FROM $DataIn.cwxzsheet A 
		LEFT JOIN $DataIn.kqdata B ON B.Number=A.Number AND B.Month='$CheckMonth'
		WHERE A.Month='$CheckMonth' AND A.Kqsign=1 AND A.BranchId>5
	UNION ALL
	SELECT A.Number,IFNULL(A.Amount+A.Jz+A.Sb+A.RandP+A.Gjj+A.Ct+A.Otherkk,0)/( $checkDays*10) AS SX
		FROM $DataIn.cwxzsheet A 
		WHERE A.Month='$CheckMonth' AND A.KqSign>1 AND A.BranchId>5
",$link_id);
if($checkKqRowB=mysql_fetch_array($checkKqSqlB)){
	do{
		$sNumber=$checkKqRowB["Number"];
		$TempSXSTR="SX".strval($sNumber); 
		$$TempSXSTR=$checkKqRowB["SX"];	//记录每位员工的时薪
		}while($checkKqRowB=mysql_fetch_array($checkKqSqlB));
	}


$checkGroup=mysql_fetch_array(mysql_query("SELECT GroupName FROM $DataIn.staffgroup WHERE GroupId='$GroupId'",$link_id));
$GroupName=$checkGroup["GroupName"];
echo"$CheckDate"."日 $GroupName 考勤统计";
echo"<table height='61' border='0' cellspacing='0' bgcolor='#FFFFFF'><tr class=''><td width='40' rowspan='2' class='A1111' align='center'>序号</td><td width='40' rowspan='2' class='A1101' align='center'>考勤</td><td width='60' rowspan='2' class='A1101' align='center'>编号</td><td width='60' rowspan='2' class='A1101' align='center'>姓名</td><td width='80' rowspan='2' class='A1101' align='center'>小组</td><td height='19' colspan='2' class='A1101' align='center'>签卡记录</td><td width='60' rowspan='2' class='A1101' align='center'>实到工时</td><td height='19' colspan='3' class='A1101' align='center'>加点加班工时(+直落)</td><td width='70' rowspan='2' class='A1101' align='center'>预估支出</td><td width='70' rowspan='2' class='A1101' align='center'>实际支出</td></tr><tr class=''><td width='60' height='20'  align='center' class='A0101'>签到</td><td width='60' class='A0101' align='center'>签退</td><td width='60' align='center' class='A0101'>1.5倍</td><td width='60' class='A0101' align='center'>2倍</td><td width='60' align='center' class='A0101'>3倍</td></tr>";

if($GroupId==0){
	$GroupStr="AND S.GroupId>600 AND S.GroupId<802";
	}
else{
	$GroupStr="AND S.GroupId='$GroupId'";
	}
$MySql="
SELECT S.Number,M.Name,G.GroupName,K.SdTime,K.JbTime,K.JbTime2,K.JbTime3 
FROM $DataIn.sc1_memberset S 
LEFT JOIN $DataIn.kqdaytj K ON S.Number=K.Number
LEFT JOIN $DataPublic.staffmain M ON M.Number=K.Number
LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
WHERE 1 $GroupStr AND S.Date='$CheckDate' AND S.KqSign=1 AND K.Date='$CheckDate' GROUP BY K.Number ORDER BY M.GroupId,K.Number";

$Result = mysql_query($MySql,$link_id);
$workTimeSUM=0;
$ygAmount_SUM=0;$sjAmount_SUM=0;
$i=1;
if($myrow = mysql_fetch_array($Result)) {
	do{
		$Name=$myrow["Name"];
		$Number=$myrow["Number"];
		$TempSXSTR="SX".strval($Number);
		$GroupName=$myrow["GroupName"];
		$SdTime=$myrow["SdTime"];
		$JbTime=$myrow["JbTime"];
		$JbTime2=$myrow["JbTime2"];
		$JbTime3=$myrow["JbTime3"];
		$workTime=$SdTime+$JbTime*1.5+$JbTime2*2+$JbTime3*3;
		$workTimeSUM+=$workTime;
		//预估支出
		$ygAmount=$workTime*$OneHourSalaryt;
		$ygAmount_SUM+=$ygAmount;
		//实际支出
		$sjAmount=sprintf("%.0f",$workTime*$$TempSXSTR);
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
		$ioResult = mysql_query("SELECT CheckTime,CheckType,KrSign 
		FROM $DataIn.checkinout WHERE 1 and Number=$Number and ((CheckTime LIKE '$CheckDate%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$CheckDate%' and KrSign='1')) ORDER BY CheckTime",$link_id);
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
		echo"<td class='A0101' align='center'>$JbTime2 $TotalTime</td>";
		echo"<td class='A0101' align='center'>$JbTime3</td>";
		echo"<td class='A0101' align='center'>$ygSTR</td>";
		echo"<td class='A0101' align='center'>$sjAmount</td>";
		//echo"<td class='A0101' align='center'>$saveGS</td>";
		echo"</tr>";
        $i++;
	 }while ($myrow = mysql_fetch_array($Result));
}

//列出不考勤员工名单
$checkSql="
SELECT S.Number,M.Name,G.GroupName
FROM $DataIn.sc1_memberset S 
LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number
LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
WHERE 1 $GroupStr AND S.KqSign>1 AND S.Date='$CheckDate' ORDER BY M.KqSign DESC,M.GroupId,M.ComeIn";
$checkResult = mysql_query($checkSql,$link_id);
if($checkRow = mysql_fetch_array($checkResult)){
	do{
		$Number=$checkRow["Number"];
		$TempSXSTR="SX".strval($Number);
		$Name=$checkRow["Name"];
		$GroupName=$checkRow["GroupName"];
		$ygAmount=10*$OneHourSalaryt;
		//如果工资已审核，则取工资计算
		$checkGZ=mysql_fetch_array(mysql_query("SELECT (X.Amount+X.Jz+X.Sb+X.RandP+X.Gjj+X.Ct+X.Otherkk) AS Amount 
		FROM $DataIn.cwxzsheet X
		WHERE X.Number='$Number' AND X.Month='$CheckMonth'",$link_id));
		$sjAmount=sprintf("%.0f",$$TempSXSTR*10);
		$ygSTR=$ygAmount;
		if($ygAmount<$sjAmount){//低估
			$ygSTR="<span class='greenB'>$ygAmount</span>";
			}
		else{
			if($ygAmount>$sjAmount){
				$ygSTR="<span class='redB'>$ygAmount</span>";
				}
			}
		$sjAmount=zerotospace($sjAmount);
		echo"<tr><td class='A0111' align='center'>$i</td>";
		echo"<td class='A0101' align='center'><span class='redB'>否</span></td>";
		echo"<td class='A0101' align='center'>$Number</td>";
		echo"<td class='A0101' align='center'>$Name</td>";
		echo"<td class='A0101'>$GroupName</td>";
		echo"<td class='A0101' align='center'>8:00</td>";
		echo"<td class='A0101' align='center'>20:00</td>";
		echo"<td class='A0101' align='center'>10</td>";
		echo"<td class='A0101' align='center'>&nbsp;</td>";
		echo"<td class='A0101' align='center'>&nbsp;</td>";
		echo"<td class='A0101' align='center'>&nbsp;</td>";
		echo"<td class='A0101' align='center'>$ygSTR</td>";
		echo"<td class='A0101' align='center'>$sjAmount</td>";
		echo"</tr>";
		$workTimeSUM+=10;
		$ygAmount_SUM+=$ygAmount;
		$sjAmount_SUM+=$sjAmount;
		$i++;
		}while($checkRow = mysql_fetch_array($checkResult));
	}
$ygSTR=$ygAmount_SUM;
if($ygAmount_SUM<$sjAmount_SUM){//低估
	$ygSTR="<span class='greenB'>$ygAmount_SUM</span>";
	}
else{
	if($ygAmount_SUM>$sjAmount_SUM){
		$ygSTR="<span class='redB'>$ygAmount_SUM</span>";
		}
	}
$sjAmount_SUM=zerotospace($sjAmount_SUM);
if($i==1){
	echo"<tr bgcolor='#FFFFFF'><td colspan='13' scope='col' height='60' class='A0111' align='center'><p>没有考勤员工记录</td></tr>";
	}
echo"
<tr class=''>
	<td colspan='7' class='A0111' align='center'>合计</td>
	<td colspan='4' class='A0101' align='center'>$workTimeSUM &nbsp;</td>
	<td class='A0101' align='center'>$ygSTR</td>
	<td class='A0101' align='center'>$sjAmount_SUM</td>
</tr></table>";
//步骤5：<td class='A0101' align='center'>$Sum_saveGS</td>
echo"<br>签卡记录空白的,是签卡记录有问题或未审核,需人事处理.";
?>
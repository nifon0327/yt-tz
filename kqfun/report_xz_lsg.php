<?php 
//电信-ZX  2012-08-01
//试用期薪资查询
$nowYear=date("Y");//默认年
$sYear=$sYear==""?$nowYear:$sYear;
$T="<br><table border='0' cellspacing='0' bgcolor='#CCCCCC' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;font-size:9px;'>
	<tr>
		<td width='80' class='A1111' align='center'>薪资月份</td>
		<td width='80' class='A1101' align='center'>1倍薪资</td>
		<td width='80' class='A1101' align='center'>1.5倍薪</td>
		<td width='80' class='A1101' align='center'>2倍薪资</td>
		<td width='80' class='A1101' align='center'>3倍薪资</td>
		<td width='80' class='A1101' align='center'>夜宵补助</td>
		<td width='80' class='A1101' align='center'>实付</td>
 	</tr>
	";
//记录内容
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$checkGZ="SELECT S.Axz,S.Bxz,S.Cxz,S.Dxz,S.YBbz,S.Amount,S.Month FROM $DataIn.cwxztempsheet S WHERE 1 AND S.Number='$Number' AND left(S.Month,4)='$sYear' ORDER BY S.Month";
$GZResult = mysql_query($checkGZ." $PageSTR",$link_id);
$sumAxz=0;
$sumBxz=0;
$sumCxz=0;
$sumDxz=0;
$sumYBbz=0;
$sumAmount=0;
if($myRow = mysql_fetch_array($GZResult)){
	do{
		$Month=substr($myRow["Month"],5,2);
		$Axz=$myRow["Axz"];				$sumAxz+=$Axz;
		$Bxz=$myRow["Bxz"];				$sumBxz+=$Bxz;
		$Cxz=$myRow["Cxz"];				$sumCxz+=$Cxz;
		$Dxz=$myRow["Dxz"];				$sumDxz+=$Dxz;
		$YBbz=$myRow["YBbz"];			$sumYBbz+=$YBbz;
		$Amount=$myRow["Amount"];		$sumAmount+=$Amount;
		
		$Axz=SpaceValue0($Axz);
		$Bxz=SpaceValue0($Bxz);
		$Cxz=SpaceValue0($Cxz);
		$Dxz=SpaceValue0($Dxz);
		$YBbz=SpaceValue0($YBbz);
		$Amount=SpaceValue0($Amount);
		$T.="<tr>
			<td class='A0111' align='center' height='25'>$Month 月</td>
			<td class='A0101' align='center'>$Axz</td>
			<td class='A0101' align='center'>$Bxz</td>
			<td class='A0101' align='center'>$Cxz</td>
			<td class='A0101' align='center'>$Dxz</td>
			<td class='A0101' align='center'>$YBbz</td>
			<td class='A0101' align='center'>$Amount</td>
			</tr>";
		}while($myRow = mysql_fetch_array($GZResult));
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$sumAxz=SpaceValue0($sumAxz);
$sumBxz=SpaceValue0($sumBxz);
$sumCxz=SpaceValue0($sumCxz);
$sumDxz=SpaceValue0($sumDxz);
$sumYBbz=SpaceValue0($sumYBbz);
$sumAmount=SpaceValue0($sumAmount);

$T.="
	<tr>
	<td class='A0111' align='center' height='25'>合计</td>
	<td class='A0101' align='center'>$sumAxz</td>
	<td class='A0101' align='center'>$sumBxz</td>
	<td class='A0101' align='center'>$sumCxz</td>
	<td class='A0101' align='center'>$sumDxz</td>
	<td class='A0101' align='center'>$sumYBbz</td>
	<td class='A0101' align='center'>$sumAmount</td>
	</tr>
	</table>";
?>
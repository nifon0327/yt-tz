<?php 
//电信-ZX  2012-08-01
//正式工薪资查询
$nowYear=date("Y");//默认年
$sYear=$sYear==""?$nowYear:$sYear;
$T="<br><table border='0' cellspacing='0' bgcolor='#CCCCCC' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;font-size:9px;'>
	<tr>
		<td width='40' class='A1111' align='center'>月份</td>
		<td width='55' class='A1101' align='center'>底薪</td>
		<td width='55' class='A1101' align='center'>加班费</td>
		<td width='55' class='A1101' align='center'>工龄<br>津贴</td>
		<td width='55' class='A1101' align='center'>岗位<br>津贴</td>
		<td width='65' class='A1101' align='center'>奖金</td>
		<td width='55' class='A1101' align='center'>生活<br>补助</td>
		<td width='55' class='A1101' align='center'>住宿<br>补助</td>
		<td width='55' class='A1101' align='center'>夜宵<br>补助</td>
		<td width='55' class='A1101' align='center'>考勤<br>扣款</td>
		<td width='70' class='A1101' align='center'>小计</td>
		<td width='50' class='A1101' align='center'>借支</td>
		<td width='50' class='A1101' align='center'>社保</td>
		<td width='50' class='A1101' align='center'>奖惩</td>
		<td width='50' class='A1101' align='center'>其它</td>
		<td width='70' class='A1101' align='center'>实付</td>
 	</tr>
	";
//记录内容
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$checkGZ="SELECT 
S.Month,S.Dx,S.Gljt,S.Gwjt,S.Jj,S.Shbz,S.Zsbz,S.Jbf,S.Yxbz,S.Jz,S.Sb,S.Kqkk,S.RandP,S.Otherkk,S.Amount,S.Estate,M.ComeIn
FROM $DataIn.cwxzsheet S 
LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
WHERE 1 AND S.Number='$Number' AND left(S.Month,4)='$sYear'
ORDER BY S.Month";
$GZResult = mysql_query($checkGZ." $PageSTR",$link_id);
$sumDX=0;
$sumGljt=0;
$sumGwjt=0;
$sumJj=0;
$sumShbz=0;
$sumZsbz=0;
$sumJbf=0;
$sumYxbz=0;
$sumJz=0;
$sumSb=0;
$sumKqkk=0;
$sumRandP=0;
$sumOtherkk=0;
$sumAmount=0;
$sumsAmount=0;
if($myRow = mysql_fetch_array($GZResult)){
	do{
		$chooseMonth=$myRow["Month"];
		$Month=substr($chooseMonth,5,2);
		$Dx=$myRow["Dx"];				$sumDX+=$DX;
		$Gljt=$myRow["Gljt"];			$sumGljt+=$Gljt;
		$Gwjt=$myRow["Gwjt"];			$sumGwjt+=$Gwjt;
		$Jj=$myRow["Jj"];				$sumJj+=$Jj;
		$Shbz=$myRow["Shbz"];			$sumShbz+=$Shbz;
		$Zsbz=$myRow["Zsbz"];			$sumZsbz+=$Zsbz;
		$Jbf=$myRow["Jbf"];				$sumJbf+=$Jbf;
		$Yxbz=$myRow["Yxbz"];			$sumYxbz+=$Yxbz;
		$Jz=$myRow["Jz"];				$sumJz+=$Jz;
		$Sb=$myRow["Sb"];				$sumSb+=$Sb;
		$Kqkk=$myRow["Kqkk"];			$sumKqkk+=$Kqkk;
		$RandP=$myRow["RandP"];			$sumRandP+=$RandP;
		$Otherkk=$myRow["Otherkk"];		$sumOtherkk+=$Otherkk;
		$Amount=$myRow["Amount"];		$sumAmount+=$Amount;
		$sAmount=$Amount+$Otherkk+$RandP+$Sb+$Jz;	$sumsAmount+=$sAmount;
		
		$Dx=SpaceValue0($Dx);
		$Gljt=SpaceValue0($Gljt);
		$Gwjt=SpaceValue0($Gwjt);
		$Jj=SpaceValue0($Jj);
		$Shbz=SpaceValue0($Shbz);
		$Zsbz=SpaceValue0($Zsbz);
		$Jbf=SpaceValue0($Jbf);
		$Yxbz=SpaceValue0($Yxbz);
		$Jz=SpaceValue0($Jz);
		$Sb=SpaceValue0($Sb);
		$Kqkk=SpaceValue0($Kqkk);
		$RandP=SpaceValue0($RandP);
		$Otherkk=SpaceValue0($Otherkk);
		$Amount=SpaceValue0($Amount);
		$sAmount=SpaceValue0($sAmount);
		
		$Eatate=$myRow["Eatate"];
		$ComeIn=$myRow["ComeIn"];
		//工龄计算
		include "../admin/subprogram/staff_model_gl.php";
		$sumD=$sumD>0?$sumD:"-";
		$sumM=$sumM>0?$sumM:"-";
		$sumY=$sumY>0?"<span class='redB'>".$sumY."</span>":"";
		$Gl=$sumY."(".$sumM.")";
		$T.="<tr>
			<td class='A0111' align='center' height='25'>$Month</td>
			<td class='A0101' align='center'>$Dx</td>
			<td class='A0101' align='center'>$Jbf</td>
			<td class='A0101' align='center'>$Gljt</td>
			<td class='A0101' align='center'>$Gwjt</td>
			<td class='A0101' align='center'>$Jj</td>
			<td class='A0101' align='center'>$Shbz</td>
			<td class='A0101' align='center'>$Zsbz</td>
			<td class='A0101' align='center'>$Yxbz</td>
			<td class='A0101' align='center'>$Kqkk</td>
			<td class='A0101' align='center'>$sAmount</td>
			<td class='A0101' align='center'>$Jz</td>
			<td class='A0101' align='center'>$Sb</td>
			<td class='A0101' align='center'>$RandP</td>
			<td class='A0101' align='center'>$Otherkk</td>
			<td class='A0101' align='center'>$Amount</td>
			</tr>";
		$i++;
		}while($myRow = mysql_fetch_array($GZResult));
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$sumDX=SpaceValue0($sumDX);
$sumJbf=SpaceValue0($sumJbf);
$sumGljt=SpaceValue0($sumGljt);
$sumGwjt=SpaceValue0($sumGwjt);
$sumJj=SpaceValue0($sumJj);
$sumShbz=SpaceValue0($sumShbz);
$sumZsbz=SpaceValue0($sumZsbz);
$sumYxbz=SpaceValue0($sumYxbz);
$sumKqkk=SpaceValue0($sumKqkk);
$sumsAmount=SpaceValue0($sumsAmount);
$sumJz=SpaceValue0($sumJz);
$sumSb=SpaceValue0($sumSb);
$sumRandP=SpaceValue0($sumRandP);
$sumOtherkk=SpaceValue0($sumOtherkk);
$sumAmount=SpaceValue0($sumAmount);

$T.="
	<tr>
	<td class='A0111' align='center' height='25'>合计</td>
	<td class='A0101' align='center'>$sumDX</td>
	<td class='A0101' align='center'>$sumJbf</td>
	<td class='A0101' align='center'>$sumGljt</td>
	<td class='A0101' align='center'>$sumGwjt</td>
	<td class='A0101' align='center'>$sumJj</td>
	<td class='A0101' align='center'>$sumShbz</td>
	<td class='A0101' align='center'>$sumZsbz</td>
	<td class='A0101' align='center'>$sumYxbz</td>
	<td class='A0101' align='center'>$sumKqkk</td>
	<td class='A0101' align='center'>$sumsAmount</td>
	<td class='A0101' align='center'>$sumJz</td>
	<td class='A0101' align='center'>$sumSb</td>
	<td class='A0101' align='center'>$sumRandP</td>
	<td class='A0101' align='center'>$sumOtherkk</td>		
	<td class='A0101' align='center'>$sumAmount</td>
	</tr>
	</table>";
?>
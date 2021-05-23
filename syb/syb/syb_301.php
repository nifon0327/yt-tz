<?php 
//电信
//代码共享-EWEN 2012-08-19
//员工薪资
$MonthSTR=$Month==""?"":" AND  A.Month='$Month'";
$chooseMonth=$Month;
switch($DataT){
	case "Y":$DataTSTR="已结付记录"; $EstateSTR=" AND A.Estate=0"; break;
	case "W":$DataTSTR="未结付记录"; $EstateSTR=" AND A.Estate=3";break;
	case "A":$DataTSTR="全部记录"; $EstateSTR=" AND (A.Estate=0 OR A.Estate=3)";break;
	}
$ShowInfo=$ItemName." ".$Month.$DataTSTR."：".$Remark;
$Parameters=$Parameters==""?"":" AND A.Branchid='$Parameters'";
echo"
<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1330;height:480px' align='center'>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td width='40' height='20' class='A1011'>序号</td>
<td width='30' class='A1001'>分类</td>
<td width='60' class='A1001'>请款月份</td>
<td width='50' class='A1001'>员工姓名</td>
<td width='40' class='A1001'>部门</td>
<td width='40' class='A1001'>职位</td>
<td width='40' class='A1001'>工龄</td>
<td width='40' class='A1001'>状态</td>
<td width='40' class='A1001'>结付<br>货币</td>
<td width='40' class='A1001'>底薪</td>
<td width='40' class='A1001'>加班费</td>
<td width='40' class='A1001'>工龄<br>津贴</td>
<td width='40' class='A1001'>岗位<br>津贴</td>
<td width='40' class='A1001'>绩效<br>奖金</td>
<td width='40' class='A1001'>额外<br>奖金</td>
<td width='40' class='A1001'>生活<br>补助</td>
<td width='40' class='A1001'>住宿<br>补助</td>
<td width='40' class='A1001'>交通<br>补助</td>
<td width='40' class='A1001'>夜宵<br>补助</td>
<td width='40' class='A1001'>个税<br>补助</td>
<td width='40' class='A1001'>考勤<br>扣款</td>
<td width='60' class='A1001'>小计</td>
<td width='40' class='A1001'>借支</td>
<td width='40' class='A1001'>社保</td>
<td width='40' class='A1001'>个税</td>
<td width='50' class='A1001'>公积金</td>
<td width='40' class='A1001'>餐费<br>扣款</td>
<td width='40' class='A1001'>其它<br>扣款</td>
<td width='60' class='A1001'>实付(RMB)</td>
</tr>
<tr>
<td colspan='28' height='450px'>
<div style='width:1331px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1330' align='center'>
";
//读取记录
$checkSql=mysql_query("SELECT A.Month,A.KqSign,A.Number,
					  A.Dx,A.Gljt,A.Gwjt,(A.Jj+A.Jbjj) AS Jj,A.Ywjj,A.Shbz,A.Zsbz,A.Jtbz,A.Jbf,A.Yxbz,A.taxbz,
					  A.Jz,A.Sb,A.Gjj,A.Ct,A.Kqkk,A.RandP,A.Otherkk,A.Amount,A.Estate,A.Remark,B.Name,B.ComeIn,B.Estate AS mEsate,B.Id As PID,C.Name AS Branch,D.Name AS Job,R.Rate,R.Symbol   
					  FROM $DataIn.cwxzsheet A 
					  LEFT JOIN $DataPublic.staffmain B ON B.Number=A.Number 
					  LEFT JOIN $DataPublic.branchdata C ON C.Id=A.BranchId 
					  LEFT JOIN $DataPublic.jobdata D ON D.Id=A.JobId 
					  LEFT JOIN $DataPublic.currencydata R ON R.Id=A.Currency 
					  WHERE  1 $MonthSTR $EstateSTR $Parameters ORDER BY C.SortId,A.JobId,B.ComeIn",$link_id);
			  
$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	$d=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
	$d1=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
	do{
		//add by cabbage 20150113 app採集單月紀錄
		$detailList[] = $checkRow;
		
		$Number=$checkRow["Number"];
		$Month=$checkRow["Month"];
		$KqSignStr=$checkRow["KqSign"]==1?"※":"●";
		$Name=$checkRow["Name"];
		$Branch=$checkRow["Branch"];
		$Job=$checkRow["Job"];
		//工龄计算
		$ComeIn=$checkRow["ComeIn"];
		include "../public/subprogram/staff_model_gl.php";
		
		$Estate=$checkRow["Estate"]==3?"<span class=\"redB\">未付</span>":"<span class=\"greenB\">已付</span>";
		$Dx=$checkRow["Dx"];
		$Gljt=$checkRow["Gljt"];
		$Gwjt=$checkRow["Gwjt"];
		$Jj=$checkRow["Jj"];
		$Ywjj=$checkRow["Ywjj"];
		$Shbz=$checkRow["Shbz"];
		$Zsbz=$checkRow["Zsbz"];
		$Jtbz=$checkRow["Jtbz"];
		$Jbf=$checkRow["Jbf"];
		$Yxbz=$checkRow["Yxbz"];
		$taxbz=$checkRow["taxbz"];
		
		$Jz=$checkRow["Jz"];
		$Sb=$checkRow["Sb"];
		$Gjj=$checkRow["Gjj"];
		$Ct=$checkRow["Ct"];
		$Kqkk=$checkRow["Kqkk"];
		$RandP=$checkRow["RandP"];
		$Otherkk=$checkRow["Otherkk"];

        $Rate=$checkRow["Rate"];
        $Symbol=$checkRow["Symbol"];
        $Symbol=$Symbol=="RMB"?$Symbol:"<span class='redB'>$Symbol</span>";
		$AmountRMB=round($checkRow["Amount"]*$Rate);
		$SumAmount+=$AmountRMB;
		
		$Holidayjb=0;
		$checkResult = mysql_query("SELECT Amount FROM $DataIn.hdjbsheet WHERE Number='$Number' and Month='$Month' AND Estate IN (0,3)",$link_id);
		if($checkRow = mysql_fetch_array($checkResult)){
			$Holidayjb=$checkRow["Amount"];
		}
		
		$Jbf+=$Holidayjb;
		$SumAmount+=$Holidayjb;
		
		$Amount=$Dx+$Jbf+$Gljt+$Gwjt+$Jj+$Shbz+$Zsbz+$Jtbz+$Yxbz+$taxbz-$Kqkk;
		
		$Dx=SpaceValue0($Dx);
		$Jbf=SpaceValue0($Jbf);
		$Gljt=SpaceValue0($Gljt);
		$Gwjt=SpaceValue0($Gwjt);
		$Jj=SpaceValue0($Jj);
		$Ywjj=SpaceValue0($Ywjj);
		$Shbz=SpaceValue0($Shbz);
		$Zsbz=SpaceValue0($Zsbz);
		$Jtbz=SpaceValue0($Jtbz);
		$Yxbz=SpaceValue0($Yxbz);
		$taxbz=SpaceValue0($taxbz);
		$Kqkk=SpaceValue0($Kqkk);
		$Jz=SpaceValue0($Jz);
		$Otherkk=SpaceValue0($Otherkk);
		$RandP=SpaceValue0($RandP);
		$Gjj=SpaceValue0($Gjj);
		$Ct=SpaceValue0($Ct);
		$Amount=SpaceValue0($Amount);
		//$Holidayjb=SpaceValue0($Holidayjb);
		
		echo"
			<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \" align='right'>
			<td width='40' height='20' class='A0111' align='center'>$i</td>
			<td width='30' class='A0101' align='center'>$KqSignStr</td>
			<td width='60' class='A0101' align='center'>$Month</td>
			<td width='50' class='A0101' align='center'>$Name</td>
			<td width='40' class='A0101' align='center'>$Branch</td>
			<td width='40' class='A0101' align='center'>$Job</td>
			<td width='40' class='A0101' align='center'>$Gl</td>
			<td width='40' class='A0101' align='center'>$Estate</td>
			<td width='40' class='A0101' align='center'>$Symbol</td>
			<td width='40' class='A0101' >$Dx</td>
			<td width='40' class='A0101'>$Jbf</td>
			<td width='40' class='A0101'>$Gljt</td>
			<td width='40' class='A0101'>$Gwjt</td>
			<td width='40' class='A0101'>$Jj</td>
			<td width='40' class='A0101'>$Ywjj</td>
			<td width='40' class='A0101'>$Shbz</td>
			<td width='40' class='A0101'>$Zsbz</td>
			<td width='40' class='A0101'>$Jtbz</td>
			<td width='40' class='A0101'>$Yxbz</td>
			<td width='40' class='A0101'>$taxbz</td>
			<td width='40' class='A0101'>$Kqkk</td>
			<td width='60' class='A0101'>$Amount</td>
			
			<td width='40' class='A0101'>$Jz</td>
			<td width='40' class='A0101'>$Sb</td>
			<td width='40' class='A0101'>$RandP</td>
			<td width='40' class='A0101'>$Gjj</td>
			<td width='40' class='A0101'>$Ct</td>
			<td width='40' class='A0101'>$Otherkk</td>
			<td width='60' class='A0101'>$AmountRMB</td>
			</tr>";
		$i++;
		}while($checkRow=mysql_fetch_array($checkSql));
	}
for($j=$i;$j<27;$j++){//补空行
	echo"
	<tr bgcolor='#ECEAED' align='center' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
	<td width='40' height='20' class='A0111'>$j</td>
	<td width='30' class='A0101'>&nbsp;</td>
	<td width='60' class='A0101'>&nbsp;</td>
	<td width='50' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='60' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='' class='A0101'>&nbsp;</td>
	</tr>";
	}
$SumAmount=number_format(sprintf("%.0f",$SumAmount));
echo"</table>
</div>
</td>
</tr>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td height='20' class='A0111' colspan='2'>合计</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0101' align='right' colspan='2'>$SumAmount</td>
</tr>
</table>
";
?>
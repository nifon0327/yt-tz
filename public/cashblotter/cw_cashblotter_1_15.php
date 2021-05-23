<?php
//15 员工薪资			OK
//ewen 2013-09-04 OK
$checkSql=mysql_query("SELECT A.Id,A.PayDate,A.PayAmount,A.Payee,A.Remark AS PayRemark,B.Title
FROM $DataIn.cwxzmain A LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId WHERE A.Id='$Id_Remark'",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='70' class='A1111'>结付日期</td>
<td width='30' class='A1101'>结付<br>凭证</td>
<td width='30' class='A1101'>结付<br>备注</td>
<td width='60' class='A1101'>结付金额</td>
<td width='100' class='A1101'>结付银行</td>

<td width='40' class='A1101'>序号</td>
<td width='70' class='A1101'>薪资<br>月份</td>
<td width='50' class='A1101'>员工<br>姓名</td>
<td width='40' class='A1101'>底薪</td>
<td width='40' class='A1101'>加班<br>费</td>
<td width='40' class='A1101'>工龄<br>津贴</td>
<td width='40' class='A1101'>岗位<br>津贴</td>
<td width='40' class='A1101'>奖金</td>
<td width='40' class='A1101'>生活<br>补助</td>
<td width='40' class='A1101'>住宿<br>补助</td>
<td width='40' class='A1101'>交通<br>补助</td>
<td width='35' class='A1101'>夜宵<br>补助</td>
<td width='35' class='A1101'>个税<br>补助</td>
<td width='40' class='A1101'>考勤<br>扣款</td>
<td width='40' class='A1101'>津贴<br>扣款</td>
<td width='40' class='A1101'>小计</td>
<td width='35' class='A1101'>借支</td>
<td width='40' class='A1101'>社保</td>
<td width='40' class='A1101'>个税</td>
<td width='40' class='A1101'>公积金</td>
<td width='40' class='A1101'>餐费<br>扣款</td>
<td width='40' class='A1101'>其他</td>
<td width='50' class='A1101'>实付</td>
</tr>";
if($checkRow=mysql_fetch_array($checkSql)){
	$Dir=anmaIn("download/cwgyssk/",$SinkOrder,$motherSTR);
	$i=1;
	//**********
	//结付主表数据
	$Mid=$checkRow["Id"];
	$PayDate=$checkRow["PayDate"];
	$djAmount=$checkRow["djAmount"];
	$PayAmount=$checkRow["PayAmount"];
	$BankName=$checkRow["Title"];
	$ImgDir="download/cwxz/";
	$Payee=$checkRow["Payee"];
	include "../model/subprogram/cw0_imgview.php";		
	$PayRemark=$checkRow["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$checkRow[PayRemark]' width='16' height='16'>";
	$checkSheetSql=mysql_query("
	SELECT S.Id,S.Mid,S.KqSign,S.Number,S.Month,S.Dx,S.Gljt,S.Gwjt,(S.Jj+S.Jbjj) AS Jj,S.Shbz,S.Zsbz,S.Jtbz,S.Jbf,S.Yxbz,S.taxbz,S.Jz,S.Sb,S.Kqkk,S.dkfl,S.RandP,S.Otherkk,S.Amount,S.Estate,S.Locks,S.Gjj,S.Ct,
	P.Name,P.ComeIn,P.Estate AS mEsate,B.Name AS Branch,J.Name AS Job
	FROM $DataIn.cwxzsheet S
	LEFT JOIN $DataPublic.staffmain P ON P.Number=S.Number 
	LEFT JOIN $DataPublic.branchdata B ON B.Id=S.BranchId
	LEFT JOIN $DataPublic.jobdata J ON J.Id=S.JobId
	WHERE S.Mid='$Mid' ORDER BY S.BranchId,S.JobId,P.ComeIn
	",$link_id);
	if($checkSheetRow=mysql_fetch_array($checkSheetSql)){
		//计算子记录数量
		$Rowspan=mysql_num_rows($checkSheetSql);
		//输出首行前段
		echo"<tr><td scope='col' class='A0111' align='center' rowspan='$Rowspan' valign='top'>$PayDate</td>";	//结付日期
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$Payee</td>";							//凭证
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$PayRemark</td>";					//结付备注
		echo"<td rowspan='$Rowspan' class='A0101' align='right' valign='top'>$PayAmount</td>";						//结付总额
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$BankName</td>";						//结付银行	
		$j=1;
		do{
			//结付明细数据
			//结付明细数据
		$Id=$checkSheetRow["Id"];
		$KqSign=$checkSheetRow["KqSign"];
		$Branch=$checkSheetRow["Branch"];
		$Job=$checkSheetRow["Job"];
		$Month=$checkSheetRow["Month"];
		$JRJBRow=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Amount),0) AS JRJB FROM $DataIn.hdjbsheet WHERE Number='$Number' AND Month='$Month'",$link_id));
		$JRJB=$JRJBRow["JRJB"];
		$chooseMonth=$Month;
		$Number=$checkSheetRow["Number"];
        $strName=$checkSheetRow["Name"];
		$Name=$checkSheetRow["mEsate"]==1?$checkSheetRow["Name"]:"<div class='yellowB'>".$checkSheetRow["Name"]."</div>";
		$ComeIn=$checkSheetRow["ComeIn"];
		$Dx=$checkSheetRow["Dx"];					//底薪
		$Jbf=$checkSheetRow["Jbf"];					//加班费
		$Gljt=$checkSheetRow["Gljt"];				//工龄津贴
		$Gwjt=$checkSheetRow["Gwjt"];			//岗位津贴
		$Jj=$checkSheetRow["Jj"];						//奖金+加班奖金
		$Shbz=$checkSheetRow["Shbz"];			//生活补助
		$Zsbz=$checkSheetRow["Zsbz"];			//住宿补助
		$Jtbz=$checkSheetRow["Jtbz"];				//交通补助
		$Yxbz=$checkSheetRow["Yxbz"];			//夜宵补助
		$taxbz=$checkSheetRow["taxbz"];			//个税补助
		$Kqkk=$checkSheetRow["Kqkk"];			//考勤扣款
		$dkfl=$checkSheetRow["dkfl"];	    		//抵扣福利  //add by zx 2013-05-29
		$RandP=$checkSheetRow["RandP"];		//扣税
		$Otherkk=$checkSheetRow["Otherkk"];	//考勤扣款
		$Total=sprintf("%0.f",$Dx+$Jbf+$Gljt+$Gwjt+$Jj+$Shbz+$Zsbz+$Jtbz+$Yxbz+$taxbz-$Kqkk-$dkfl);
		$Jz=$checkSheetRow["Jz"];			//借支
		$Sb=$checkSheetRow["Sb"];			//社保
		$Gjj=$checkSheetRow["Gjj"];		//公积金
		$Ct=$checkSheetRow["Ct"];		//餐费
		$AmountSys=sprintf("%.0f",$Total-$Jz-$Sb-$Gjj-$Ct-$RandP-$Otherkk);		//实付
		$Amount=$checkSheetRow["Amount"];		//数据表值
		
		$Dx=SpaceValue0($Dx);
		$Gljt=SpaceValue0($Gljt);
		$Gwjt=SpaceValue0($Gwjt);
		$Jj=SpaceValue0($Jj);
		$Shbz=SpaceValue0($Shbz);
		$Zsbz=SpaceValue0($Zsbz);
		$Jtbz=SpaceValue0($Jtbz);
		$Yxbz=SpaceValue0($Yxbz);
		$taxbz=SpaceValue0($taxbz);
		$Kqkk=SpaceValue0($Kqkk);
		$dkfl=SpaceValue0($dkfl);
		$Total=SpaceValue0($Total);
		
		$Jz=SpaceValue0($Jz);
		$RandP=SpaceValue0($RandP);
		$Gjj=SpaceValue0($Gjj);
		$Ct=SpaceValue0($Ct);
		$Otherkk=SpaceValue0($Otherkk);
		$Amount=SpaceValue0($Amount);
		$Jbf=SpaceValue0($Jbf);
			if($j>1) echo "<tr>";
			echo"<td class='A0101' align='center' height='20'>$j</td>";
			echo"<td class='A0101' align='center'>$Month</td>";	
			echo"<td class='A0101'>$Name</td>";
			echo"<td class='A0101' align='right'>$Dx</td>";
			echo"<td class='A0101' align='right'>$Jbf</td>";
			echo"<td class='A0101' align='right'>$Gljt</td>";
			echo"<td class='A0101' align='right'>$Gwjt</td>";
			echo"<td class='A0101' align='right'>$Jj</td>";
			echo"<td class='A0101' align='right'>$Shbz</td>";
			echo"<td class='A0101' align='right'>$Zsbz</td>";
			echo"<td class='A0101' align='right'>$Jtbz</td>";
			echo"<td class='A0101' align='right'>$Yxbz</td>";
			echo"<td class='A0101' align='right'>$taxbz</td>";
			echo"<td class='A0101' align='right'>$Kqkk</td>";
			echo"<td class='A0101' align='right'>$dkfl</td>";
			echo"<td class='A0101' align='right'>$Total</td>";
			echo"<td class='A0101' align='right'>$Jz</td>";
			echo"<td class='A0101' align='right'>$Sb</td>";
			echo"<td class='A0101' align='right'>$RandP</td>";
			echo"<td class='A0101' align='right'>$Gjj</td>";
			echo"<td class='A0101' align='right'>$Ct</td>";
			echo"<td class='A0101' align='right'>$Otherkk</td>";
			echo"<td class='A0101' align='right'>$Amount</td>";
			echo"</tr>";
			$j++;
			}while ($checkSheetRow=mysql_fetch_array($checkSheetSql));
		}
	$i++;
	}
else{
	
	}
?>
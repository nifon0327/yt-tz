<?php 
//员工离职补助
$MonthSTR=$Month==""?"":" AND DATE_FORMAT(S.Date,'%Y-%m') ='$Month'";
switch($DataT){
	case "Y":$DataTSTR="已结付记录"; $EstateSTR=" AND S.Estate=0"; break;
	case "W":$DataTSTR="未结付记录"; $EstateSTR=" AND S.Estate=3";break;
	case "A":$DataTSTR="全部记录"; $EstateSTR=" AND (S.Estate=0 OR S.Estate=3)";break;
	}
$ShowInfo=$ItemName." ".$Month.$DataTSTR."：".$Remark;
echo"
<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:480px' align='center'>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td width='50' height='20' class='A1011'>序号</td>
<td width='60' class='A1001'>员工姓名</td>
<td width='60' class='A1001'>部门</td>
<td width='60' class='A1001'>离职日期</td>
<td width='60' class='A1001'>离职类型</td>
<td width='60' class='A1001'>离职原因</td>
<td width='70' class='A1001'>月均工资</td>
<td width='70' class='A1001'>补助比例</td>
<td width='70' class='A1001'>补助次数</td>
<td width='70' class='A1001'>金额</td>
<td width='50' class='A1001'>单据</td>
<td width='40' class='A1001'>状态</td>
<td width='80' class='A1001'>请款日期</td>
<td width='100' class='A1001'>请款金额</td>
</tr>
<tr>
<td colspan='10' height='450px'>
<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px' align='center'>
";
//读取记录
$checkSql=mysql_query("SELECT S.Id,S.Mid,S.Content,S.Amount,S.Bill,S.ReturnReasons,S.Date,S.Estate,S.Locks,S.Operator,C.Symbol AS Currency,S.TotalRate,S.Time,M.Name,B.Name AS Branch,M.ComeIn,D.outDate,S.AveAmount,S.Number,S.PaySign,D.Reason AS LeaveReason,T.Name AS TypeName
 	FROM $DataIn.staff_outsubsidysheet S 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
    LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
    LEFT JOIN $DataPublic.dimissiondata D ON D.Number=M.Number 
    LEFT JOIN $DataPublic.dimissiontype T ON T.Id =D.LeaveType
 WHERE 1 $MonthSTR $EstateSTR AND S.TypeId=2",$link_id);
$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	 $Dir=anmaIn("download/staff_subsidy/",$SinkOrder,$motherSTR);
	do{
		//add by cabbage 20141203 app採集單月紀錄
		$detailList[] = $checkRow;
		
		$Id=$checkRow["Id"];
         $Date=$checkRow["Date"];
		$Number=$checkRow["Number"];
		$Amount=sprintf("%.2f",$checkRow["Amount"]);
		$SumAmount+=$Amount;
		$Amount=number_format($Amount);
       $AveAmount=$checkRow["AveAmount"];
		$Currency=$checkRow["Currency"];		
		$Content=$checkRow["Content"];
		$Name=$checkRow["Name"];
		$Branch=$checkRow["Branch"];
        $TypeName=$checkRow["TypeName"];
		$LeaveReason=$checkRow["LeaveReason"]==""?"&nbsp":"<img src='../images/remark.gif' title='$$checkRow[LeaveReason]' width='16' height='16'>";
		$ComeIn=$checkRow["ComeIn"];
         /*********************************************/
		 //工龄计算
		 $ComeInYM=substr($ComeIn,0,7);
		 include "../public/subprogram/staff_model_gl.php";
       $outDate=$checkRow["outDate"];
       $TotalRate =$checkRow["TotalRate"];
       $Time ="第".$checkRow["Time"]."次";
       $PaySign =$checkRow["PaySign"];
       if($PaySign==1)$Time="<span class='redB'>一次性支付</span>";
       $Rate =$TotalRate."个月";
		$ReturnReasons=$checkRow["ReturnReasons"]==""?"&nbsp;":"<sapn class=\"redB\">".$checkRow["ReturnReasons"]."</span>";
		$Bill=$checkRow["Bill"];
		$Dir=anmaIn("download/staff_subsidy/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill=$Number.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\",\"\",\"Limit\")'  style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="-";
			}
		$Locks=$checkRow["Locks"];			
		$Estate=$checkRow["Estate"];	
		switch($Estate){
			case "3":
				$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
				break;
			case "0":
				$Estate="<div align='center' class='greenB' >√</div>";
				break;
			}

		echo"
			<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td width='50' height='20' class='A0111' align='center'>$i</td>
			<td width='60'  class='A0101' align='center'>$Name</td>
			<td width='60' class='A0101' align='center'>$Branch</td>
			<td width='60' class='A0101' align='center'>$outDate</td>
			<td width='60' class='A0101' align='center'>$TypeName</td>
			<td width='60' class='A0101' align='center'>$LeaveReason</td>
			<td width='70' class='A0101' align='center'>$AveAmount</td>
			<td width='70' class='A0101' align='center'>$Rate</td>
			<td width='70' class='A0101' align='center'>$Time</td>
			<td width='70' class='A0101' align='center'>$Amount</td>
			<td width='50' class='A0101' align='center'>$Bill</td>
			<td width='40' class='A0101'  align='center'>$Estate</td>
			<td width='80' class='A0101' align='center'>$Month</td>
			<td width='101' class='A0100' align='right'>$Amount</td>
			</tr>";
		$i++;
		}while($checkRow=mysql_fetch_array($checkSql));
	}
for($j=$i;$j<27;$j++){//补空行
	echo"
	<tr bgcolor='#ECEAED' align='center' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
	<td width='50' height='20' class='A0111'>$j</td>
	<td width='60'  class='A0101'>&nbsp;</td>
	<td width='60'  class='A0101'>&nbsp;</td>
	<td width='60' class='A0101'>&nbsp;</td>
	<td width='70' class='A0101'>&nbsp;</td>
	<td width='70' class='A0101'>&nbsp;</td>
	<td width='70' class='A0101'>&nbsp;</td>
	<td width='70' class='A0101'>&nbsp;</td>
	<td width='70' class='A0101'>&nbsp;</td>
	<td width='70' class='A0101'>&nbsp;</td>
	<td width='50' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
	<td width='101' class='A0100'>&nbsp;</td>
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
<td  class='A0101' align='right'>¥$SumAmount</td>
</tr>
</table>
";
?>
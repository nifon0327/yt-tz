<?php
//辞退补偿			OK
//ewen 2013-09-04 OK

$checkSql=mysql_query("SELECT S.Id,S.Mid,S.Content,S.Amount,S.Bill,S.ReturnReasons,S.Date,S.Estate,S.Locks,S.Operator,SM.PayDate,C.Symbol AS Currency,S.TotalRate,S.Time,M.Name,B.Name AS Branch,M.ComeIn,D.outDate,S.AveAmount,S.Number,S.PaySign,D.Reason AS LeaveReason,T.Name AS TypeName
 	FROM $DataIn.staff_outsubsidysheet S 
 	INNER JOIN $DataIn.staff_outsubsidymain SM ON SM.Id=S.Mid 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
    LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
    LEFT JOIN $DataPublic.dimissiondata D ON D.Number=M.Number 
    LEFT JOIN $DataPublic.dimissiontype T ON T.Id =D.LeaveType
 WHERE S.Mid='$Id_Remark'  ",$link_id);

echo"<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;' align='center'>
<tr bgcolor='#CCCCCC' align='center' >
<td width='50' height='20' class='A1111'>序号</td>
<td width='60' class='A1101'>员工姓名</td>
<td width='60' class='A1101'>部门</td>
<td width='60' class='A1101'>离职日期</td>
<td width='60' class='A1101'>离职类型</td>
<td width='60' class='A1101'>离职原因</td>
<td width='70' class='A1101'>月均工资</td>
<td width='70' class='A1101'>补助比例</td>
<td width='70' class='A1101'>补助次数</td>
<td width='70' class='A1101'>金额</td>
<td width='50' class='A1101'>单据</td>
<td width='40' class='A1101'>状态</td>
<td width='80' class='A1101'>请款日期</td>
<td width='100' class='A1101'>请款金额</td>
</tr>
";

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
      
      $Month=substr($checkRow["PayDate"],0,7);		
		echo"
			<tr bgcolor='#FFF'>
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
else{
	
	}
echo "</table>";
echo "</div>";
?>
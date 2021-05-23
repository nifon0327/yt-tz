<?php //货款返利
		$ReturnTable="";
		$ReturnResult="";
		$ReturnResult=mysql_query("SELECT S.Id,S.Amount,S.Remark,S.Date,P.Name AS Operator 
		          FROM $DataIn.cw2_hksheet  S
				  LEFT JOIN $DataIn.cw2_hkmain M ON M.Id=S.Mid
	              LEFT JOIN $DataPublic.staffmain P ON P.Number=S.Operator
				  WHERE S.Did=0  AND S.Estate IN (0,3) AND S.CompanyId='$CompanyId'",$link_id);
		if($ReturnRow=mysql_fetch_array($ReturnResult)){
		   $ReturnTable="<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
		<tr><td class='A0111' height='40'><span class='greenB'>供应商返利列表</span></td></tr></table>
		<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
		<tr bgcolor='$Title_bgcolor'>
		<td width='40' height='25' class='A0111' align='center'>选项</td><td width='40' class='A0101' align='center'>序号</td>
		<td width='80' class='A0101' align='center'>请款日期</td>
		<td class='A0101' align='center'>返利说明</td>
		<td width='115' class='A0101' align='center'>返利金额</td>
		<td width='70' class='A0101' align='center'>请款人</td>
		</tr>";
	     	$r=1;
		    do{
			 $ReturnId=$ReturnRow["Id"];
			$ReturnPayDate=$ReturnRow["PayDate"];
			$ReturnAmount=$ReturnRow["Amount"];
			$ReturnRemark=$ReturnRow["Remark"];
			$ReturnDate=$ReturnRow["Date"];
			$ReturnOperator=$ReturnRow["Operator"];
			$ReturnTable.="<tr>
			<td align='center' class='A0111' height='20'><input name='checkReturn[]' type='checkbox' id='checkReturn$r' value='$ReturnId'></td>
			<td align='center' class='A0101'>$r</td>
			<td align='center' class='A0101'>$ReturnDate</td>
			<td class='A0101'>$ReturnRemark</td>
			<td align='right' class='A0101'>$ReturnAmount</td>
			<td align='center' class='A0101'>$ReturnOperator</td>
			</tr>";
              $r++;
		    }while($ReturnRow=mysql_fetch_array($ReturnResult));
			$ReturnTable.="</table>";
		 }
?>
<?php
//34  其它奖金 		OK
//ewen 2013-09-04 OK
//读取记录
$checkSql=mysql_query("SELECT  A.Id,A.Content,A.Operator,A.Bill,A.Date,C.Symbol,A.Amount,(A.Amount*C.Rate) AS AmountRMB, '其它奖金' AS ItemName ,M.Name,B.Name AS BranchName
 									  FROM $DataIn.cw20_bonussheet A
                                      LEFT JOIN $DataPublic.staffmain M ON M.Number=A.Number 
	                                  LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
									  LEFT JOIN $DataPublic.currencydata C ON C.Id=A.Currency 
                                      WHERE  A.Mid='$Id_Remark'",$link_id);
                                                                          
echo"
<div >$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;' align='center'>
<tr bgcolor='#CCCCCC' align='center' style='Color:#000'>
<td width='50' height='20' class='A1111'>序号</td>
<td width='100' class='A1101'>费用名称</td>
<td width='80' class='A1101'>员工姓名</td>
<td width='60' class='A1101'>部门</td>
<td  class='A1101'>请款说明</td>
<td width='80' class='A1101'>请款人</td>
<td width='80' class='A1101'>请款日期</td>
<td width='70' class='A1101'>请款凭证</td>
<td width='40' class='A1101'>请款货币</td>
<td width='80' class='A1101'>请款金额</td>
<td width='80' class='A1101'>转RMB金额</td>
</tr>";

$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	do{
		//add by cabbage 20141203 app採集單月紀錄
		$detailList[] = $checkRow;
	
		$Type=$checkRow["Type"];//1月结，0现金
		$Id=$checkRow["Id"];
		$ItemName=$checkRow["ItemName"];
		$Name=$checkRow["Name"];
        $BranchName=$checkRow["BranchName"];
		$Content=$checkRow["Content"];
		$Symbol=$checkRow["Symbol"];
		$Operator=$checkRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Date=$checkRow["Date"];
		$Bill=$checkRow["Bill"];
		$Amount=sprintf("%.2f",$checkRow["Amount"]);
		$AmountRMB=sprintf("%.2f",$checkRow["AmountRMB"]);
		$SumAmount+=$AmountRMB;
		$Amount=number_format($Amount);
		$AmountRMB=number_format($AmountRMB);
		 $Dir=anmaIn("../download/cw_bonus/",$SinkOrder,$motherSTR);
		 if($Bill==1){
			$Bill="C".$Id.".jpg";
			
			//add by cabbage 20141229 app紀錄附件的檔案路徑
			$detailList[$i - 1]["FilePath"] = "/download/cw_bonus/".$Bill;
			
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			 }
		 else{$Bill="&nbsp;";}
		echo"
			<tr bgcolor='#FFF' >
			<td width='50' height='20' class='A0111' align='center'>$i</td>
			<td width='100'  class='A0101'>$ItemName</td>
			<td width='80' class='A0101' align='center'>$Name</td>
			<td width='60' class='A0101' align='center'>$BranchName</td>
			<td class='A0101'>$Content&nbsp;</td>
			<td width='80' class='A0101' align='center'>$Operator</td>
			<td width='80' class='A0101' align='center'>$Date</td>
			<td width='70' class='A0101'  align='center'  bgcolor='#ECEAED'>$Bill</td>
			<td width='40' class='A0101' align='center'>$Symbol</td>
			<td width='80' class='A0101' align='right'>$Amount</td>
			<td width='80' class='A0101' align='right'>$AmountRMB</td>
			</tr>";
		$i++;
		}while($checkRow=mysql_fetch_array($checkSql));
	}
else{
	
	}
echo "</table>";
echo "</div>";
?>
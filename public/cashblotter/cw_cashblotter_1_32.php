<?php
//车辆费用			OK
//ewen 2013-09-04 OK
//读取记录
$checkSql=mysql_query("SELECT  A.Id,A.Content,A.Operator,A.Bill,A.Date,C.Symbol,A.Amount,(A.Amount*C.Rate) AS AmountRMB,B.Name AS ItemName,'2' AS Type 
 	FROM $DataIn.carfee A 
	LEFT JOIN $DataPublic.carfee_type B ON A.TypeId=B.Id
   LEFT JOIN $DataPublic.currencydata C ON C.Id=A.Currency 
   	WHERE  A.Mid='$Id_Remark' ",$link_id);

echo"<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;' align='center'>
<tr bgcolor='#CCCCCC' align='center' >
<td width='50' height='20' class='A1111'>序号</td>
<td width='100' class='A1101'>费用名称</td>
<td  class='A1101'>请款说明</td>
<td width='80' class='A1101'>请款人</td>
<td width='80' class='A1101'>请款日期</td>
<td width='70' class='A1101'>请款凭证</td>
<td width='60' class='A1101'>请款货币</td>
<td width='90' class='A1101'>请款金额</td>
<td width='90' class='A1101'>转RMB金额</td>
</tr>
";
if($checkRow=mysql_fetch_array($checkSql)){
	do{
		$Id=$checkRow["Id"];
		$ItemName=$checkRow["ItemName"];
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
        $Type=$checkRow["Type"];
				
		//add by cabbage 20141210 app用，附件路徑
		$appFileLink = "";
			
if($Type==1){
		 $Dir=anmaIn("../download/cwadminicost/",$SinkOrder,$motherSTR);
		 if($Bill==1){
			$Bill="H".$Id.".jpg";
				
			//add by cabbage 20141210 app用，附件路徑
			$appFileLink = "/download/cwadminicost/".$Bill;
			
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			 }
		 else{$Bill="&nbsp;";}

 }
else{
		 $Dir=anmaIn("../download/carfee/",$SinkOrder,$motherSTR);
		 if($Bill==1){
			$Bill="C".$Id.".jpg";
				
			//add by cabbage 20141210 app用，附件路徑
			$appFileLink = "/download/carfee/".$Bill;
			
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			 }
		 else{$Bill="&nbsp;";}
}
		echo"
			<tr bgcolor='#FFF'>
			<td width='50' height='20' class='A0111' align='center'>$i</td>
			<td width='100'  class='A0101'>$ItemName</td>
			<td class='A0101'>$Content&nbsp;</td>
			<td width='80' class='A0101' align='center'>$Operator</td>
			<td width='80' class='A0101' align='center'>$Date</td>
			<td width='70' class='A0101'  align='center'>$Bill</td>
			<td width='60' class='A0101' align='right'>$Symbol</td>
			<td width='90' class='A0101' align='right'>$Amount</td>
			<td width='90' class='A0101' align='right'>$AmountRMB</td>
			</tr>";
			
		//add by cabbage 20141210 app採集單月紀錄
		$detailList[$i - 1] = array(
			"Date" => $Date,
			"AmountRMB" => $AmountRMB,
			"ItemName" => $ItemName,
			"Content" => $Content,
			"Operator" => $Operator
		);
		if (strlen($appFileLink) > 0) {
			$detailList[$i - 1]["FileLink"] = $appFileLink;
		}
			
		$i++;
		}while($checkRow=mysql_fetch_array($checkSql));
	}
else{
	
	}
echo "</table>";
echo "</div>";
?>
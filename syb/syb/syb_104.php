<?php 
//电信
//代码共享-EWEN 2012-08-19
//免抵退税收益
$MonthSTR=$Month==""?"":" AND DATE_FORMAT(A.Taxdate,'%Y-%m')='$Month'";
switch($DataT){
	case "Y":$DataTSTR="已结付记录"; $EstateSTR=" AND A.Estate=0"; break;
	case "W":$DataTSTR="未结付记录"; $EstateSTR=" AND A.Estate=3";break;
	case "A":$DataTSTR="全部记录"; $EstateSTR="AND ( A.Estate=0 OR A.Estate=3)";break;
	}
$ShowInfo=$ItemName." ".$Month.$DataTSTR."：".$Remark;
echo"
<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:480px' align='center'>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td width='50' height='20' class='A1011'>序号</td>
<td width='80' class='A1001'>国税时间</td>
<td width='100' class='A1001'>免抵退税发票号</td>
<td width='100' class='A1001'>扫描附件</td>
<td class='A1001'>备注</td>
<td width='100' class='A1001'>收款日期</td>
<td width='50' class='A1001'>状态</td>
<td width='80' class='A1001'>免抵退税金额</td>
</tr>
<tr>
<td colspan='8' height='450px'>
<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px' align='center'>
";
//读取记录
$checkSql=mysql_query("SELECT * FROM $DataIn.cw14_mdtaxmain A WHERE A.Taxdate>='2008-07-01' $MonthSTR $EstateSTR GROUP BY A.Id ORDER BY A.Taxdate DESC",$link_id);
$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
	do{
		//add by cabbage 20141120 app採集單月紀錄
		$detailList[] = $checkRow;	
	
		$Id=$checkRow["Id"];
		$Taxdate=$checkRow["Taxdate"];
		$TaxNo=$checkRow["TaxNo"];
		$Attached=$checkRow["Attached"];
		 $Dir=anmaIn("download/cwmdtax/",$SinkOrder,$motherSTR);
		if($Attached!=""){
			
			//add by cabbage 20141120 app共用
			$linkList[$Id] = "/download/cwmdtax/".$Attached;
			
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);			
			$Attached="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			
			}
		else{
			$Attached="&nbsp;";
			}
		$Proof=$checkRow["Proof"];
		if($Proof!=""){
			$Proof=anmaIn($Proof,$SinkOrder,$motherSTR);
			$Proof="<span onClick='OpenOrLoad(\"$Dir\",\"$Proof\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Proof="&nbsp;";
			}
		$Remark=$checkRow["Remark"];
		$Taxgetdate=$checkRow["Taxgetdate"];
		$Estate=$checkRow["Estate"]==0?"<div class='greenB'>已收</div>":"<div class='redB'>未收</div>";
		$Amount=sprintf("%.2f",$checkRow["Taxamount"]);
		$SumAmount+=$Amount;
		$Amount=number_format($Amount);
		echo"
			<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td width='50' height='20' class='A0111' align='center'>$i</td>
			<td width='80' class='A0101' align='center'>$Taxdate</td>
			<td width='100' class='A0101'>$TaxNo</td>
			<td width='100' class='A0101' bgcolor='#ECEAED' align='center'>$Attached</td>
			<td class='A0101'>$Remark</td>
			<td width='100' class='A0101' align='center'>$Taxgetdate</td>
			<td width='50' class='A0101' align='center' bgcolor='#ECEAED'>$Estate</td>		
			<td width='80' class='A0101' align='right'>$Amount</td>
			</tr>";
		$i++;
		}while($checkRow=mysql_fetch_array($checkSql));
	}
for($j=$i;$j<27;$j++){//补空行
	echo"
	<tr bgcolor='#ECEAED' align='center' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
	<td width='50' height='20' class='A0111'>$j</td>
	<td width='80'  class='A0101'>&nbsp;</td>
	<td width='100'  class='A0101'>&nbsp;</td>
	<td width='100' class='A0101' bgcolor='#ECEAED'>&nbsp;</td>
	<td class='A0101'>&nbsp;</td>
	<td width='100' class='A0101'>&nbsp;</td>
	<td width='50' class='A0101' bgcolor='#ECEAED'>&nbsp;</td>
	<td width='80' class='A0100'>&nbsp;</td>
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
<td  class='A0101' align='right'>¥$SumAmount</td>
</tr>
</table>
";
?>
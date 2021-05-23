<?php 
//电信
//代码共享-EWEN 2012-08-19
//开发费用
$MonthSTR=$Month==""?"":" AND DATE_FORMAT(A.Date,'%Y-%m')='$Month'";
switch($DataT){
	case "Y":$DataTSTR="已结付记录"; $EstateSTR=" AND A.Estate=0"; break;
	case "W":$DataTSTR="未结付记录"; $EstateSTR=" AND A.Estate=3";break;
	case "A":$DataTSTR="全部记录"; $EstateSTR=" AND (A.Estate=0 OR A.Estate=3)";break;
	}
$ShowInfo=$ItemName." ".$Month.$DataTSTR."：".$Remark;
//$Parameters=$Parameters==""?"":" AND A.TypeID IN($Parameters)";
//拆分参数
$ParametersArray =explode(";" , $Parameters); 
$Parameters1=$ParametersArray[0];
$Parameters1=$Parameters1==""?"":" AND A.TypeId IN($Parameters1)";
$Parameters2=$ParametersArray[1];
$Parameters2=$Parameters2==""?"":" AND A.TypeId IN($Parameters2)";
echo"
<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:480px' align='center'>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td width='50' height='20' class='A1011'>序号</td>
<td width='100' class='A1001'>供应商</td>
<td width='100' class='A1001'>费用名称</td>
<td  class='A1001'>请款说明</td>
<td width='80' class='A1001'>请款人</td>
<td width='80' class='A1001'>请款日期</td>
<td width='70' class='A1001'>请款凭证</td>
<td width='50' class='A1001'>请款货币</td>
<td width='80' class='A1001'>请款金额</td>
<td width='80' class='A1001'>转RMB金额</td>
</tr>
<tr>
<td colspan='10' height='450px'>
<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px' align='center'>
";
//读取记录
$checkSql=mysql_query("
					  SELECT Type,Id,Content,Provider,Operator,Bill,Date,Symbol,Amount,AmountRMB,ItemName FROM(
						SELECT  '0' AS Type,A.Id,A.Content,'' AS Provider,A.Operator,A.Bill,A.Date,C.Symbol,A.Amount,(A.Amount*C.Rate) AS AmountRMB,'行政费用-刀模' AS ItemName 
										  FROM $DataIn.hzqksheet A
										  LEFT JOIN $DataPublic.adminitype B ON B.TypeId=A.TypeId
										  LEFT JOIN $DataPublic.currencydata C ON C.Id=A.Currency 
										  WHERE 1 AND A.Date>='2008-07-01' 
										  $MonthSTR $Parameters1
										  $EstateSTR
						UNION ALL
					  SELECT  '1' AS Type,A.Id,A.Description AS Content,A.Provider,A.Operator,A.Bill,A.Date,C.Symbol,A.Amount,(A.Amount*C.Rate) AS AmountRMB,B.Name AS ItemName 
										  FROM $DataIn.cwdyfsheet A
										  LEFT JOIN $DataPublic.kftypedata B ON B.Id=A.TypeId
										  LEFT JOIN $DataPublic.currencydata C ON C.Id=A.Currency 
										  WHERE 1 AND A.Date>='2008-07-01' 
										  $MonthSTR $Parameters2
										  $EstateSTR 
						) D ORDER BY D.Date DESC,D.Id DESC
										  ",$link_id);
$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	$Dir1=anmaIn("../download/cwadminicost/",$SinkOrder,$motherSTR);
	$Dir2=anmaIn("../download/dyf/",$SinkOrder,$motherSTR);
	do{
		$Id=$checkRow["Id"];
		$Type=$checkRow["Type"];
		$Provider=$checkRow["Provider"];
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
		  if($Bill==1){
			 if($Type==0){//行政费用
				$Bill="H".$Id.".jpg";
				$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
				$Bill="<span onClick='OpenOrLoad(\"$Dir1\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
				$ItemName="<div class='redB'>".$ItemName."</div>";
			 	}
			else{//打样费
				$Bill="DYF".$Id.".jpg";
				$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
				$Bill="<span onClick='OpenOrLoad(\"$Dir2\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
				}
			}
		else{
			$Bill="&nbsp;";
			}
		echo"
			<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td width='50' height='20' class='A0111' align='center'>$i</td>
			<td width='100'  class='A0101'>$Provider</td>
			<td width='100'  class='A0101'>$ItemName</td>
			<td class='A0101'>$Content&nbsp;</td>
			<td width='80' class='A0101' align='center'>$Operator</td>
			<td width='80' class='A0101' align='center'>$Date</td>
			<td width='70' class='A0101'  align='center'  bgcolor='#ECEAED'>$Bill</td>
			<td width='50' class='A0101' align='center'>$Symbol</td>
			<td width='80' class='A0101' align='right'>$Amount</td>
			<td width='80' class='A0100' align='right'>$AmountRMB</td>
			</tr>";
		$i++;
		}while($checkRow=mysql_fetch_array($checkSql));
	}
for($j=$i;$j<27;$j++){//补空行
	echo"
	<tr bgcolor='#ECEAED' align='center' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
	<td width='50' height='20' class='A0111'>$j</td>
	<td width='100'  class='A0101'>&nbsp;</td>
	<td width='100'  class='A0101'>&nbsp;</td>
	<td class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
	<td width='70' class='A0101'>&nbsp;</td>
	<td width='50' class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
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
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0101' align='right'>¥$SumAmount</td>
</tr>
</table>
";
?>
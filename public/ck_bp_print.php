<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=6;
$tableMenuS=500;
$Th_Col="序号|30|配件ID|80|配件名称|300|转入总数|80|单价|80|汇率|100|金额(RMB)|100";
ChangeWtitle("$SubCompany $chooseDate 备品转入统计");
echo"<table width='710' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td align='center'>$SubCompany $chooseDate 备品转入统计</td></table>";
include "../model/subprogram/read_model_3.php";
$mySql="
SELECT SUM(F.Qty) AS Qty,F.StuffId,D.StuffCname,D.Price,C.Rate
FROM $DataIn.ck7_bprk F
LEFT JOIN $DataIn.stuffdata D ON F.StuffId=D.StuffId 
LEFT JOIN $DataIn.bps B ON B.StuffId=F.StuffId
LEFT JOIN $DataIn.trade_object P ON  P.CompanyId=B.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
WHERE 1 AND DATE_FORMAT(F.Date,'%Y-%m')='$chooseDate' GROUP BY F.StuffId ORDER BY D.StuffCname";
List_Title($Th_Col,"1",1);
$j=1;
$myResult = mysql_query($mySql." $PageSTR",$link_id);$ChooseOut="N";
if($myRow = mysql_fetch_array($myResult)){
	$QtySum=0;
	$AmountSum=0;
	do{
		$m=1;
		$StuffId=$myRow["StuffId"];
		$Qty=$myRow["Qty"];			  
		$StuffCname=$myRow["StuffCname"];
		$Price=$myRow["Price"];
		$Rate=$myRow["Rate"];
		$Amount=sprintf("%.2f",$Qty*$Price*$Rate);
		$QtySum+=$Qty;
		$AmountSum+=$Amount;
		$ValueArray=array(
			array(0=>$StuffId, 		1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$Qty,			1=>"align='right'"),
			array(0=>$Price,		1=>"align='right'"),
			array(0=>$Rate,		1=>"align='right'"),
			array(0=>$Amount,		1=>"align='right'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	//合计
	echo"
	<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>
	<tr>
	<td align='center' width='410' class='A0111'>合 计</td>
	<td align='right' width='80' class='A0101'>$QtySum</td>
	<td width='180' class='A0101'>&nbsp;</td>
	<td align='right' width='100' class='A0101'>$AmountSum</td>
	</tr>
	</table>
	";
	}
else{
	noRowInfo($tableWidth);
  	}
List_Title($Th_Col,"0",1);
?>

<?php   
/*电信---yang 20120801
$DataIn.sc1_chefeng
$DataIn.yw1_ordersheet
$DataIn.productdata
独立已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$TempMonth=$TempId;
//AI-热转需求单统计
$AI=0;$AO=0;$A=0;
$AISTR=" AND (G.StuffId='96111' OR G.StuffId='96112' OR G.StuffId='96177')";//当月出货订单的需求单中,配件为"96111加工(热转)","96112加工(烫片/金/银/钻)","96177相机包热转人工"的需求单总额
$myResultAI = mysql_query("
		SELECT SUM(G.Price*G.OrderQty) AS Amount
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
			WHERE 1 $AISTR AND DATE_FORMAT(M.Date,'%Y-%m')='$TempMonth'
		",$link_id);
if($myRowAI = mysql_fetch_array($myResultAI)){
	$AI=sprintf("%.0f",$myRowAI["Amount"]);
	}

//AO-热转实际支出
$AOSTR=" AND X.JobId='16' AND (X.Estate='0' OR X.Estate='3')";
/*
1.热转员工(16)薪资(实发+借支+员工出社保费+奖惩+其它扣款)
2.热转员工(16)假日加班费
3.热转员工(16)社保费(公司出的那部分)
*/
$myResultAO = mysql_query("
	SELECT SUM(Amount) AS Amount FROM(
			SELECT SUM(X.Amount+X.Jz+X.Sb+X.RandP+X.Otherkk) AS Amount 
			FROM $DataIn.cwxzsheet X WHERE X.Month='$TempMonth' $AOSTR
			UNION ALL
			SELECT SUM(X.Amount) AS Amount FROM $DataIn.hdjbsheet X WHERE X.Month='$TempMonth' $AOSTR
			UNION ALL
			SELECT SUM(X.cAmount) AS Amount FROM $DataIn.sbpaysheet X WHERE X.Month='$TempMonth' $AOSTR
			)A 
		",$link_id);
if($myRowAO = mysql_fetch_array($myResultAO)){
	$AO=sprintf("%.0f",$myRowAO["Amount"]);
	}
$A=number_format($AI-$AO,2);
$AI=number_format($AI,2);
$AO=number_format($AO,2);
$A=$A<0?"<div class='redB'>$A&nbsp;</div>":"<div class='greenB'>$A&nbsp;</div>";
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='78' height='20' align='right'>热转人工&nbsp;</td>
		<td width='118' align='right'>$AI&nbsp;</td>
		<td width='119' align='right'>$AO&nbsp;</td>
		<td width='119' align='right'>$A</td>
		<td width='89' align='center'>&nbsp;</td>
	</tr></table>";


//BI-车缝人工1需求单统计
$BI=0;
$BISTR=" AND G.StuffId='96178'";	//当月出货订单的需求单中,配件为"96178相机包车缝人工1"的需求单总额
$myResultBI = mysql_query("
		SELECT SUM(G.Price*G.OrderQty) AS Amount
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
			WHERE 1 $BISTR AND DATE_FORMAT(M.Date,'%Y-%m')='$TempMonth'
		",$link_id);
if($myRowBI = mysql_fetch_array($myResultBI)){
	$BI=sprintf("%.0f",$myRowBI["Amount"]);
	}
//BO-车缝人工1实际支出
$BO=0;
$BOSTR=" AND X.JobId='18' AND (X.Estate='0' OR X.Estate='3')";
/*
1.车缝1员工(18)薪资(实发+借支+员工出社保费+奖惩+其它扣款)
2.车缝1员工(18)假日加班费
3.车缝1员工(18)社保费(公司出的那部分)
*/
$myResultBO = mysql_query("
	SELECT SUM(Amount) AS Amount FROM(
			SELECT SUM(X.Amount+X.Jz+X.Sb+X.RandP+X.Otherkk) AS Amount 
			FROM $DataIn.cwxzsheet X WHERE X.Month='$TempMonth' $BOSTR
			UNION ALL
			SELECT SUM(X.Amount) AS Amount FROM $DataIn.hdjbsheet X WHERE X.Month='$TempMonth' $BOSTR
			UNION ALL
			SELECT SUM(X.cAmount) AS Amount FROM $DataIn.sbpaysheet X WHERE X.Month='$TempMonth' $BOSTR
			)A 
		",$link_id);
if($myRowBO = mysql_fetch_array($myResultBO)){
	$BO=sprintf("%.0f",$myRowBO["Amount"]);
	}
$B=number_format($BI-$BO,2);
$BI=number_format($BI,2);$BO=number_format($BO,2);
$B=$B<0?"<div class='redB'>$B&nbsp;</div>":"<div class='greenB'>$B&nbsp;</div>";

//CI-车缝人工2需求单统计
$CI=0;
$CISTR=" AND (G.StuffId='98014' OR G.StuffId='96231')";	//当月出货订单的需求单中,配件为"98014相机包车缝人工2"和"96231加工(吊绳车逢)"
$myResultCI = mysql_query("
		SELECT SUM(G.Price*G.OrderQty) AS Amount
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
			WHERE 1 $CISTR AND DATE_FORMAT(M.Date,'%Y-%m')='$TempMonth'
		",$link_id);
if($myRowCI = mysql_fetch_array($myResultCI)){
	$CI=sprintf("%.0f",$myRowCI["Amount"]);
	}
//CO-车缝人工2实际支出
$CO=0;
$COSTR=" AND X.JobId='21' AND (X.Estate='0' OR X.Estate='3')";
/*
1.车缝2员工(21)薪资(实发+借支+员工出社保费+奖惩+其它扣款)
2.车缝2员工(21)假日加班费
3.车缝2员工(21)社保费(公司出的那部分)
*/
$myResultCO = mysql_query("
	SELECT SUM(Amount) AS Amount FROM(
			SELECT SUM(X.Amount+X.Jz+X.Sb+X.RandP+X.Otherkk) AS Amount 
			FROM $DataIn.cwxzsheet X WHERE X.Month='$TempMonth' $COSTR
			UNION ALL
			SELECT SUM(X.Amount) AS Amount FROM $DataIn.hdjbsheet X WHERE X.Month='$TempMonth' $COSTR
			UNION ALL
			SELECT SUM(X.cAmount) AS Amount FROM $DataIn.sbpaysheet X WHERE X.Month='$TempMonth' $COSTR
			)A 
		",$link_id);
if($myRowCO = mysql_fetch_array($myResultCO)){
	$CO=sprintf("%.0f",$myRowCO["Amount"]);
	}
$C=number_format($CI-$CO,2);
$CI=number_format($CI,2);$CO=number_format($CO,2);
$C=$C<0?"<div class='redB'>$C&nbsp;</div>":"<div class='greenB'>$C&nbsp;</div>";

echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='78' height='20' align='right'>车缝人工1&nbsp;</td>
		<td width='118' align='right'>$BI&nbsp;</td>
		<td width='119' align='right'>$BO&nbsp;</td>
		<td width='119' align='right'>$B</td>
		<td width='89' align='center'>&nbsp;</td>
	</tr>
<tr bgcolor='#99FF99'>
		<td width='78' height='20' align='right'>车缝人工2&nbsp;</td>
		<td width='118' align='right'>$CI&nbsp;</td>
		<td width='119' align='right'>$CO&nbsp;</td>
		<td width='119' align='right'>$C</td>
		<td width='89' align='center'>&nbsp;</td>
	</tr>	</table>";


//DI-修剪1需求单统计
$DI=0;
$DISTR=" AND G.StuffId='96179'";//当月出货订单的需求单中,配件为"96179相机包包装人工"的需求单总额
$myResultDI = mysql_query("
		SELECT SUM(G.Price*G.OrderQty) AS Amount
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
			WHERE 1 $DISTR AND DATE_FORMAT(M.Date,'%Y-%m')='$TempMonth'
		",$link_id);
if($myRowDI = mysql_fetch_array($myResultDI)){
	$DI=sprintf("%.0f",$myRowDI["Amount"]);
	}

//EI-修剪2需求单统计
$EI=0;
$EISTR=" AND G.StuffId='97544'";//当月出货订单的需求单中,配件为"97544相机包修剪人工2"的需求单总额
$myResultEI = mysql_query("
		SELECT SUM(G.Price*G.OrderQty) AS Amount
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
			WHERE 1 $EISTR AND DATE_FORMAT(M.Date,'%Y-%m')='$TempMonth'
		",$link_id);
if($myRowEI = mysql_fetch_array($myResultEI)){
	$EI=sprintf("%.0f",$myRowEI["Amount"]);
	}
//FI-作业员需求单统计
$FI=0;
$FISTR=" AND (D.TypeId='9078' OR D.TypeId='9088' OR D.TypeId='9092') AND D.StuffId NOT IN(96111,96112,96177,96178,96179,96231,97544,98014)";
/*
当月出货订单的需求单中,配件分类为"9078人工"且配件不为
"96111加工(热转),
96112加工(烫片/金/银/钻),
96177相机包热转人工,
96178相机包车缝人工1,
98014相机包车缝人工2,
96231加工(吊绳车逢),
96179相机包修剪人工1,
97544相机包修剪人工2"的需求单总额
*/
$myResultFI = mysql_query("
		SELECT SUM(G.Price*G.OrderQty) AS Amount
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
			WHERE 1 $FISTR AND DATE_FORMAT(M.Date,'%Y-%m')='$TempMonth'
		",$link_id);
if($myRowFI = mysql_fetch_array($myResultFI)){
	$FI=sprintf("%.0f",$myRowFI["Amount"]);
	}

//DO-修剪1实际支出统计
$DOSTR=" AND X.BranchId='2' AND X.JobId=22 AND (X.Estate='0' OR X.Estate='3')";
/*
1.修剪1(22)员工薪资(实发+借支+员工出社保费+奖惩+其它扣款)
2.修剪1(22)员工假日加班费
3.修剪1(22)员工社保费(公司出的那部分)
*/
$myResultDO = mysql_query("
	SELECT SUM(Amount) AS Amount FROM(
			SELECT SUM(X.Amount+X.Jz+X.Sb+X.RandP+X.Otherkk) AS Amount 
			FROM $DataIn.cwxzsheet X WHERE X.Month='$TempMonth' $DOSTR
			UNION ALL
			SELECT SUM(X.Amount) AS Amount FROM $DataIn.hdjbsheet X WHERE X.Month='$TempMonth' $DOSTR
			UNION ALL
			SELECT SUM(X.cAmount) AS Amount FROM $DataIn.sbpaysheet X WHERE X.Month='$TempMonth' $DOSTR
			)A 
		",$link_id);
if($myRowDO = mysql_fetch_array($myResultDO)){
	$DO=sprintf("%.0f",$myRowDO["Amount"]);
	}

//EO-修剪2实际支出统计
$EOSTR=" AND X.BranchId='2' AND X.JobId=23 AND (X.Estate='0' OR X.Estate='3')";
/*
1.修剪2(23)员工薪资(实发+借支+员工出社保费+奖惩+其它扣款)
2.修剪2(23)员工假日加班费
3.修剪2(23)员工社保费(公司出的那部分)
*/
$myResultEO = mysql_query("
	SELECT SUM(Amount) AS Amount FROM(
			SELECT SUM(X.Amount+X.Jz+X.Sb+X.RandP+X.Otherkk) AS Amount 
			FROM $DataIn.cwxzsheet X WHERE X.Month='$TempMonth' $EOSTR
			UNION ALL
			SELECT SUM(X.Amount) AS Amount FROM $DataIn.hdjbsheet X WHERE X.Month='$TempMonth' $EOSTR
			UNION ALL
			SELECT SUM(X.cAmount) AS Amount FROM $DataIn.sbpaysheet X WHERE X.Month='$TempMonth' $EOSTR
			)A 
		",$link_id);
if($myRowEO = mysql_fetch_array($myResultEO)){
	$EO=sprintf("%.0f",$myRowEO["Amount"]);
	}

//FO:行政费用实际支出统计
$FOSTR=" AND X.BranchId='2' AND X.JobId NOT IN(16,18,21,22,23) AND (X.Estate='0' OR X.Estate='3')";
$myResultFO = mysql_query("
	SELECT SUM(Amount) AS Amount FROM(
			SELECT SUM(X.Amount+X.Jz+X.Sb+X.RandP+X.Otherkk) AS Amount 
			FROM $DataIn.cwxzsheet X WHERE X.Month='$TempMonth' $FOSTR
			UNION ALL
			SELECT SUM(X.Amount) AS Amount FROM $DataIn.hdjbsheet X WHERE X.Month='$TempMonth' $FOSTR
			UNION ALL
			SELECT SUM(X.cAmount) AS Amount FROM $DataIn.sbpaysheet X WHERE X.Month='$TempMonth' $FOSTR
			UNION ALL
			SELECT SUM(L.Amount) AS Amount FROM $DataIn.hzqksheet L 
			WHERE DATE_FORMAT(L.Date,'%Y-%m')='$TempMonth' AND L.TypeId='635' AND (L.Estate='0' OR L.Estate='3')
			UNION ALL
			SELECT SUM(L.Amount) AS Amount FROM $DataIn.cwxztempsheet L WHERE L.Month='$TempMonth' AND L.Estate IN(0,3)
			)A 
		",$link_id);
if($myRowFO = mysql_fetch_array($myResultFO)){
	$FO=sprintf("%.0f",$myRowFO["Amount"]);
	}


$D=number_format($DI-$DO,2);$DI=number_format($DI,2);$DO=number_format($DO,2);
$D=$D<0?"<div class='redB'>$D&nbsp;</div>":"<div class='greenB'>$D&nbsp;</div>";
$E=number_format($EI-$EO,2);$EI=number_format($EI,2);$EO=number_format($EO,2);
$E=$E<0?"<div class='redB'>$E&nbsp;</div>":"<div class='greenB'>$E&nbsp;</div>";
$F=number_format($FI-$FO,2);$FI=number_format($FI,2);$FO=number_format($FO,2);
$F=$F<0?"<div class='redB'>$F&nbsp;</div>":"<div class='greenB'>$F&nbsp;</div>";

echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='78' height='20' align='right'>修剪1&nbsp;</td>
		<td width='118' align='right'>$DI&nbsp;</td>
		<td width='119' align='right'>$DO&nbsp;</td>
		<td width='119' align='right'>$D</td>
		<td width='89' align='center'>&nbsp;</td>
	</tr>
		<tr bgcolor='#99FF99'>
		<td width='78' height='20' align='right'>修剪2&nbsp;</td>
		<td width='118' align='right'>$EI&nbsp;</td>
		<td width='119' align='right'>$EO&nbsp;</td>
		<td width='119' align='right'>$E</td>
		<td width='89' align='center'>&nbsp;</td>
	</tr>
	<tr bgcolor='#99FF99'>
		<td width='78' height='20' align='right'>作业员&nbsp;</td>
		<td width='118' align='right'>$FI&nbsp;</td>
		<td width='119' align='right'>$FO&nbsp;</td>
		<td width='119' align='right'>$F</td>
		<td width='89' align='center'>&nbsp;</td>
	</tr>
	</table>";
?>
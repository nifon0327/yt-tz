<?php   
/*
已更新电信---yang 20120801
*/
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 行政费用分析表");
echo "<SCRIPT src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>";
$tableWidth=1080;
$ChooseY=$ChooseY==""?date("Y"):$ChooseY;
$ToYear=date("Y");
if($ChooseY==$ToYear){
	$ToMonth=date("n");
	}
else{
	$ToMonth=12;
	}
//取公司名称
include "../model/subprogram/mycompany_info.php";

$NowQK_Date="  AND DATE_FORMAT(M.Date,'%Y')='$ChooseY'";
$NowQK_Month="  AND LEFT(M.Month,4)='$ChooseY'";
$NowQK_qkDate="  AND DATE_FORMAT(M.qkDate,'%Y')='$ChooseY'";
$NowQK_SendDate ="  AND DATE_FORMAT(M.SendDate,'%Y')='$ChooseY'";

//1- 手续费
$CheckSql=mysql_query("SELECT SUM(M.Handingfee*C.Rate) AS Amount,DATE_FORMAT(M.PayDate,'%c') AS Month FROM $DataIn.cw6_orderinmain M, $DataIn.trade_object K, $DataPublic.currencydata C WHERE K.CompanyId=M.CompanyId AND K.Currency=C.Id AND DATE_FORMAT(M.PayDate,'%Y')='$ChooseY' GROUP BY DATE_FORMAT(M.PayDate,'%Y-%m') ORDER BY M.PayDate",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];
		$Amount=sprintf("%.0f",$CheckRow["Amount"]);
		$Handingfee[$Month]=$Amount;
		$SumAmount+=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$Handingfee[13]=$SumAmount;
	$Handingfee[14]=$AvgAmount;
	}

/*
//1- 代购的销售收入，要减去 add by zx 2010-09-30
$CheckSql=mysql_query("SELECT SUM(G.OrderQty*G.Price*C.Rate*M.Sign) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month FROM $DataIn.ch1_shipsheet S 
					   LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
					   LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
					   LEFT JOIN $DataIn.trade_object D ON D.CompanyId=G.CompanyId 
					   LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 					   
					   WHERE 1 $NowQK_Date AND M.Estate=0 AND D.Currency=2  GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];
		$Amount=sprintf("%.0f",$CheckRow["Amount"]);
		$ProxyFee[$Month]=$Amount;
		$SumAmount+=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$ProxyFee[13]=$SumAmount;
	$ProxyFee[14]=$AvgAmount;
	}
*/
//美金采购部分  add by zx 20100719
$CheckSql=mysql_query("SELECT SUM(G.OrderQty*G.Price*C.Rate) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month 
                       FROM $DataIn.ch1_shipsheet S 
					   LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
			           LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
			           LEFT JOIN $DataIn.trade_object P ON G.CompanyId=P.CompanyId		   
					   LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
					   WHERE 1 $NowQK_Date AND M.Estate=0  AND S.Type='1' AND P.Currency='2' GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);

if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];
		$Amount=sprintf("%.0f",$CheckRow["Amount"]);
		$DataList1_M[$Month]=$Amount;
		$SumAmount+=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList1_M[13]=$SumAmount;
	$DataList1_M[14]=$AvgAmount;
	}



//1- 销售收入
$CheckSql=mysql_query("SELECT SUM(S.Qty*S.Price*S.YandN*C.Rate*M.Sign) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month 
FROM $DataIn.ch1_shipsheet S 
LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.currencydata C ON C.Id=D.Currency 
WHERE 1 $NowQK_Date AND M.Estate=0 GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];
		$Amount=sprintf("%.0f",$CheckRow["Amount"]);
		$DataList1[$Month]=$Amount;
		$SumAmount+=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));
	$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);  
	$DataList1[13]=$SumAmount;
	$DataList1[14]=$AvgAmount;
	}

//2- 行政、业务、采购、开发人工
$CheckSql=mysql_query(" SELECT SUM(M.Amount+M.Jz+M.Sb) AS Amount,RIGHT(M.Month,2)*1 AS Month 
FROM $DataIn.cwxzsheet M 
WHERE 1 AND M.BranchId<5 $NowQK_Month AND M.Estate IN (0,3) GROUP BY M.Month ORDER BY M.Month",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];
		$Amount=sprintf("%.0f",$CheckRow["Amount"]);
		$DataList2[$Month]=$Amount;
		$SumAmount+=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList2[13]=$SumAmount;
	$DataList2[14]=$AvgAmount;
	}

//3-行政、业务、采购、开发假日加班费
$CheckSql=mysql_query(" SELECT SUM(M.Amount) AS Amount,RIGHT(M.Month,2)*1 AS Month 
FROM $DataIn.hdjbsheet M 
WHERE 1 AND M.BranchId<5 $NowQK_Month AND M.Estate IN (0,3) GROUP BY M.Month ORDER BY M.Month",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];
		$Amount=sprintf("%.0f",$CheckRow["Amount"]);
		$DataList3[$Month]=$Amount;
		$SumAmount+=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList3[13]=$SumAmount;
	$DataList3[14]=$AvgAmount;
	}

//4-行政、业务、采购、开发社保
$CheckSql=mysql_query(" SELECT SUM(M.cAmount) AS Amount,RIGHT(M.Month,2)*1 AS Month 
FROM $DataIn.sbpaysheet M 
WHERE 1 AND M.BranchId <5 $NowQK_Month AND M.Estate IN (0,3) GROUP BY M.Month ORDER BY M.Month",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];
		$Amount=sprintf("%.0f",$CheckRow["Amount"]);
		$DataList4[$Month]=$Amount;
		$SumAmount+=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList4[13]=$SumAmount;
	$DataList4[14]=$AvgAmount;
	}

//5-总务采购费用
$CheckSql=mysql_query(" SELECT SUM(M.Price*M.Qty) AS Amount,DATE_FORMAT(M.qkDate,'%c') AS Month FROM $DataIn.zw3_purchases M WHERE 1  $NowQK_qkDate AND M.Estate IN (0,3) GROUP BY DATE_FORMAT(M.qkDate,'%Y-%m') ORDER BY M.qkDate",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];
		$Amount=sprintf("%.0f",$CheckRow["Amount"]);
		$DataList5[$Month]=$Amount;
		$SumAmount+=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList5[13]=$SumAmount;
	$DataList5[14]=$AvgAmount;
	}

//6-行政费用:其它总务费用
$CheckSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month 
FROM $DataIn.hzqksheet M 
LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency 
WHERE 1 AND TypeId=610 $NowQK_Date AND M.Estate IN (0,3) GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];
		$Amount=sprintf("%.0f",$CheckRow["Amount"]);
		$DataList6[$Month]=$Amount;
		$SumAmount+=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList6[13]=$SumAmount;
	$DataList6[14]=$AvgAmount;
	}

//7-行政费用：厂房租金
$CheckSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month 
FROM $DataIn.hzqksheet M 
LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency 
WHERE 1 AND TypeId=601 $NowQK_Date AND M.Estate IN (0,3) GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];$Amount=sprintf("%.0f",$CheckRow["Amount"]);$SumAmount+=$Amount;
		$DataList7[$Month]=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList7[13]=$SumAmount;
	$DataList7[14]=$AvgAmount;
	}
//8-行政费用：厂房水电费
$CheckSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month 
FROM $DataIn.hzqksheet M 
LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency 
WHERE 1  AND TypeId=602 $NowQK_Date AND M.Estate IN (0,3)GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];$Amount=sprintf("%.0f",$CheckRow["Amount"]);$SumAmount+=$Amount;
		$DataList8[$Month]=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList8[13]=$SumAmount;
	$DataList8[14]=$AvgAmount;
	}
//9-行政费用：厂房管理费
$CheckSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month 
FROM $DataIn.hzqksheet M 
LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency 
WHERE 1  AND TypeId=603 $NowQK_Date AND M.Estate IN (0,3)GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];$Amount=sprintf("%.0f",$CheckRow["Amount"]);$SumAmount+=$Amount;
		$DataList9[$Month]=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList9[13]=$SumAmount;
	$DataList9[14]=$AvgAmount;
	}
//10-行政费用：员工网络费
$CheckSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND TypeId=606 $NowQK_Date AND M.Estate IN (0,3)GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];$Amount=sprintf("%.0f",$CheckRow["Amount"]);$SumAmount+=$Amount;
		$DataList10[$Month]=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList10[13]=$SumAmount;
	$DataList10[14]=$AvgAmount;
	}
//11-行政费用：电话费
$CheckSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND TypeId=607 $NowQK_Date AND M.Estate IN (0,3)GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];$Amount=sprintf("%.0f",$CheckRow["Amount"]);$SumAmount+=$Amount;
		$DataList11[$Month]=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList11[13]=$SumAmount;
	$DataList11[14]=$AvgAmount;
	}
//12-行政费用：办公耗材
$CheckSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND TypeId=608 $NowQK_Date AND M.Estate IN (0,3)GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];$Amount=sprintf("%.0f",$CheckRow["Amount"]);$SumAmount+=$Amount;
		$DataList12[$Month]=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList12[13]=$SumAmount;
	$DataList12[14]=$AvgAmount;
	}
//13-行政费用：车辆支出
$CheckSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND TypeId=609 $NowQK_Date AND M.Estate IN (0,3)GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];$Amount=sprintf("%.0f",$CheckRow["Amount"]);$SumAmount+=$Amount;
		$DataList13[$Month]=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList13[13]=$SumAmount;
	$DataList13[14]=$AvgAmount;
	}
//14-行政费用：差旅费
$CheckSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND TypeId=613 $NowQK_Date AND M.Estate IN (0,3)GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];$Amount=sprintf("%.0f",$CheckRow["Amount"]);$SumAmount+=$Amount;
		$DataList14[$Month]=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList14[13]=$SumAmount;
	$DataList14[14]=$AvgAmount;
	}
//15-行政费用：报刊费
$CheckSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND TypeId=614 $NowQK_Date AND M.Estate IN (0,3)GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];$Amount=sprintf("%.0f",$CheckRow["Amount"]);$SumAmount+=$Amount;
		$DataList15[$Month]=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList15[13]=$SumAmount;
	$DataList15[14]=$AvgAmount;
	}
//16-行政费用：交际费
$CheckSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND TypeId=615 $NowQK_Date AND M.Estate IN (0,3)GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];$Amount=sprintf("%.0f",$CheckRow["Amount"]);$SumAmount+=$Amount;
		$DataList16[$Month]=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList16[13]=$SumAmount;
	$DataList16[14]=$AvgAmount;
	}
//17-行政费用：银行手续费
$CheckSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND TypeId=617 $NowQK_Date AND M.Estate IN (0,3)GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];$Amount=sprintf("%.0f",$CheckRow["Amount"]);$SumAmount+=$Amount;
		$DataList17[$Month]=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList17[13]=$SumAmount;
	$DataList17[14]=$AvgAmount;
	}
//18-行政费用：报刊费
$CheckSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND TypeId=624 $NowQK_Date AND M.Estate IN (0,3)GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];$Amount=sprintf("%.0f",$CheckRow["Amount"]);$SumAmount+=$Amount;
		$DataList18[$Month]=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList18[13]=$SumAmount;
	$DataList18[14]=$AvgAmount;
	}
//19-行政费用：快递费
$CheckSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND TypeId=618 $NowQK_Date AND M.Estate IN (0,3)GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];$Amount=sprintf("%.0f",$CheckRow["Amount"]);$SumAmount+=$Amount;
		$DataList19[$Month]=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList19[13]=$SumAmount;
	$DataList19[14]=$AvgAmount;
	}
//20-行政费用：运费
$CheckSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND TypeId=632 $NowQK_Date AND M.Estate IN (0,3)GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];$Amount=sprintf("%.0f",$CheckRow["Amount"]);$SumAmount+=$Amount;
		$DataList20[$Month]=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList20[13]=$SumAmount;
	$DataList20[14]=$AvgAmount;
	}

//23-行政费用：机模费
$CheckSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND TypeId=626 $NowQK_Date AND M.Estate IN (0,3)GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];$Amount=sprintf("%.0f",$CheckRow["Amount"]);$SumAmount+=$Amount;
		$DataList23[$Month]=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList23[13]=$SumAmount;
	$DataList23[14]=$AvgAmount;
	}
//24-行政费用：模具费
$CheckSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND TypeId=627 $NowQK_Date AND M.Estate IN (0,3)GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];$Amount=sprintf("%.0f",$CheckRow["Amount"]);$SumAmount+=$Amount;
		$DataList24[$Month]=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList24[13]=$SumAmount;
	$DataList24[14]=$AvgAmount;
	}
//25-行政费用：样品费
$CheckSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND TypeId=630 $NowQK_Date AND M.Estate IN (0,3)GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];$Amount=sprintf("%.0f",$CheckRow["Amount"]);$SumAmount+=$Amount;
		$DataList25[$Month]=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList25[13]=$SumAmount;
	$DataList25[14]=$AvgAmount;
	}
//21-船务快递费
$CheckSql=mysql_query("SELECT SUM(M.Amount) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month FROM $DataIn.ch9_expsheet M WHERE 1 $NowQK_Date AND M.Estate IN (0,3) GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];$Amount=sprintf("%.0f",$CheckRow["Amount"]);$SumAmount+=$Amount;
		$DataList21[$Month]=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList21[13]=$SumAmount;
	$DataList21[14]=$AvgAmount;
	}
//22-船务寄样费
$CheckSql=mysql_query("SELECT SUM(M.Amount) AS Amount,DATE_FORMAT(M.SendDate,'%c') AS Month FROM $DataIn.ch10_samplemail M WHERE 1 $NowQK_SendDate AND M.Estate IN (0,3) GROUP BY DATE_FORMAT(M.SendDate,'%Y-%m') ORDER BY M.SendDate",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];$Amount=sprintf("%.0f",$CheckRow["Amount"]);$SumAmount+=$Amount;
		$DataList22[$Month]=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList22[13]=$SumAmount;
	$DataList22[14]=$AvgAmount;
	}
//26-开发费
$CheckSql=mysql_query("SELECT SUM(M.Amount) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month FROM $DataIn.cwdyfsheet M WHERE 1 $NowQK_Date AND M.Estate IN (0,3) GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];$Amount=sprintf("%.0f",$CheckRow["Amount"]);$SumAmount+=$Amount;
		$DataList26[$Month]=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList26[13]=$SumAmount;
	$DataList26[14]=$AvgAmount;
	}
//27-开办费用
$CheckSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND TypeId=643 $NowQK_Date AND M.Estate IN (0,3)GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];$Amount=sprintf("%.0f",$CheckRow["Amount"]);$SumAmount+=$Amount;
		$DataList27[$Month]=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList27[13]=$SumAmount;
	$DataList27[14]=$AvgAmount;
	}
//add by zx 2010-11-22 购买手机费用
//28-手机费用
$CheckSql=mysql_query("SELECT SUM(M.Amount*C.Rate) AS Amount,DATE_FORMAT(M.Date,'%c') AS Month FROM $DataIn.hzqksheet M LEFT JOIN $DataPublic.currencydata C ON C.Id=M.Currency WHERE 1 AND TypeId=649 $NowQK_Date AND M.Estate IN (0,3)GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$SumAmount=0;
	do{
		$Month=$CheckRow["Month"];$Amount=sprintf("%.0f",$CheckRow["Amount"]);$SumAmount+=$Amount;
		$DataList28[$Month]=$Amount;
		}while($CheckRow=mysql_fetch_array($CheckSql));$AvgAmount=sprintf("%.0f",$SumAmount/$ToMonth);
	$DataList28[13]=$SumAmount;
	$DataList28[14]=$AvgAmount;
	}

$Ni=1;
$bgcolor1="bgcolor='#FFFFCC'";
$bgcolor2="bgcolor='#FFCCFF'";
$bgcolor3="bgcolor='#FFFFFF'";

?>
<style type="text/css">
<!--
.style1 {font-size: x-large}
.style4 {font-size: 18px}
-->
</style>
<style   media="print">   
  .noprint           {   display:   none   }   
 </style>
<table width="<?php    echo $tableWidth?>" border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
  <tr><td height="30" colspan="3" align="center"><span class="style1"><?php    echo $S_Company?></span></td>
  </tr>
  <tr><td height="30" width="200"><form name="form1" method="post" action="">
    <select name="ChooseY" id="ChooseY" onchange="document.form1.submit()"; class="noprint">
	<?php   
	$sYear=2009;
	$nYear=date("Y");
	for($Y=$sYear;$Y<=$nYear;$Y++){
		if($Y==$ChooseY){
			echo"<option value='$Y' selected>$Y 年</option>";
			}
		else{
			echo"<option value='$Y'>$Y 年</option>";
			}
		}
	?>
    </select>
  </form></td>
  <td align="center"><span class="style4"><?php    echo $ChooseY?>年行政费用分析表</span></td>
    <td align="center" width="200">&nbsp;</td>
  </tr>
</table>

<table width="<?php    echo $tableWidth?>" border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>
  <tr align="center" class="">
    <td width="40" class="A1111" height="22">序号</td>
    <td width="160" class="A1101">项目</td>
    <td width="60" class="A1101">1月</td>
    <td width="60" class="A1101">2月</td>
    <td width="60" class="A1101">3月</td>
    <td width="60" class="A1101">4月</td>
    <td width="60" class="A1101">5月</td>
    <td width="60" class="A1101">6月</td>
	<td width="60" class="A1101">7月</td>
	<td width="60" class="A1101">8月</td>
  	<td width="60" class="A1101">9月</td>
	<td width="60" class="A1101">10月</td>
	<td width="60" class="A1101">11月</td>
  	<td width="60" class="A1101">12月</td>
	<td width="80" class="A1101">全年累计</td>
  	<td width="80" class="A1101">月平均</td>
  </tr>

 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
  <tr align="center"  <?php    echo $CurBackcolor1 ?>>
    <td  class="A0111"><?php    echo ($Ni++) ?></td>
    <td height="22" class="A0101">销售收入</td>
	<?php   
	for($i=1;$i<15;$i++){
		$TAmount=$DataList1[$i]-$Handingfee[$i];
    	echo"<td class='A0101' align='right'>".number_format($TAmount)."</td>";
		}
	?>
  </tr>
  <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td  class="A0111"><?php    echo ($Ni++) ?></td>
    <td height="22" class="A0101">自有销售收入</td>
	<?php   
	for($i=1;$i<15;$i++){
		$TAmount=$DataList1[$i]-$DataList1_M[$i]-$Handingfee[$i];
		//$TAmount=$DataList1[$i]-$DataList1_M[$i];
    	echo"<td class='A0101' align='right'>".number_format($TAmount)."</td>";
		}
	?>
  </tr> 
  <tr align="center">
    <td height="10" colspan="16" class="A0111">&nbsp;</td>
  </tr>

  <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">行政/业务/采购/开发人工</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList2[$i])."</td>";
		}
	?>
  </tr>

 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">行政/业务/采购/开发加班费</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList3[$i])."</td>";
		}
	?>
  </tr>

 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">行政/业务/采购/开发社保</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList4[$i])."</td>";
		}
	?>
  </tr>

 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">总务采购费用</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList5[$i])."</td>";
		}
	?>
  </tr>

 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">其他总务费用610(行政费用)</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList6[$i])."</td>";
		}
	?>
  </tr>

 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">厂房租金601(行政费用)</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList7[$i])."</td>";
		}
	?>
  </tr>

 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">厂房水电费602(行政费用)</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList8[$i])."</td>";
		}
	?>
  </tr>

 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">厂区管理费603(行政费用)</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList9[$i])."</td>";
		}
	?>
  </tr>
<!--
 </? include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" </?=$CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"></?=($Ni++) ?></td>
    <td align="left" class="A0101">员工网络费606(行政费用)</td>
	</?
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList10[$i])."</td>";
		}
	?>
  </tr>
-->
 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">电话费607(行政费用)</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList11[$i])."</td>";
		}
	?>
  </tr>

 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">办公耗材608(行政费用)</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList12[$i])."</td>";
		}
	?>
  </tr>

 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">车辆支出费用609(行政费用)</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList13[$i])."</td>";
		}
	?>
  </tr>

 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">差旅费613(行政费用)</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList14[$i])."</td>";
		}
	?>
  </tr>
<!--
 < include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <=$CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><=($Ni++) ?></td>
    <td align="left" class="A0101">报刊费614(行政费用)</td>
	<
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList15[$i])."</td>";
		}
	?>
  </tr>
-->
 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">交际费615(行政费用)</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList16[$i])."</td>";
		}
	?>
  </tr>

 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">银行手续费617(行政费用)</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList17[$i])."</td>";
		}
	?>
  </tr>

 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">税款624(行政费用)</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList18[$i])."</td>";
		}
	?>
  </tr>

 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">非月结快递费618(行政费用)</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList19[$i])."</td>";
		}
	?>
  </tr>

 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">运费632(行政费用)</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList20[$i])."</td>";
		}
	?>
  </tr>

 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">船务快递费(月结费用)</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList21[$i])."</td>";
		}
	?>
  </tr>

 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
	  <td align="left" class="A0101">船务寄样费</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList22[$i])."</td>";
		}
	?>
  </tr>
<!--
 < include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <=$CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><=($Ni++) ?></td>
    <td align="left" class="A0101">机模费626(行政费用)</td>
	<
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList23[$i])."</td>";
		}
	?>
  </tr>
-->
 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">模具费627(行政费用)</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList24[$i])."</td>";
		}
	?>
  </tr>

 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">样品费630(行政费用)</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList25[$i])."</td>";
		}
	?>
  </tr>

 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">开发费用</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList26[$i])."</td>";
		}
	?>
  </tr>

 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">开办费用643</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList27[$i])."</td>";
		}
	?>
  </tr>


 <?php    include "subprogram/desk_hztj_color.php"  //分间隔色彩 ?>
   <tr align="center" <?php    echo $CurBackcolor1 ?>>
    <td height="22" align="center" class="A0111"><?php    echo ($Ni++) ?></td>
    <td align="left" class="A0101">购买手机费用 649</td>
	<?php   
	for($i=1;$i<15;$i++){
    	echo"<td class='A0101' align='right'>".number_format($DataList28[$i])."</td>";
		}
	?>
  </tr> 
  
  
  <tr align="center" class="">
    <td class="A0111" height="22">&nbsp;</td>
    <td class="A0101">合计</td>
	<?php   
	
	for($i=1;$i<15;$i++){
		$CountAmount=0;
		for($j=2;$j<29;$j++){
		$TempStr="DataList".strval($j); 
		$CountTemp=$$TempStr;
		$CountAmount+=$CountTemp[$i];
		}
		$DataListSum[$i]=$CountAmount;
		echo"<td class='A0101' align='right'>".number_format($CountAmount)."</td>";
		}
	?>
  </tr>
  <tr align="center" class="">
    <td class="A0111" height="22">&nbsp;</td>
    <td class="A0101">占销售比率</td>
	<?php   
	for($i=1;$i<15;$i++){
		$Pce=sprintf("%.2f",$DataListSum[$i]*100/($DataList1[$i]-$DataList1_M[$i]-$Handingfee[$i]));
		
    	echo"<td class='A0101' align='right'>".$Pce."%</td>";
		}
	?>
  </tr>
</table>
</body>
</html>

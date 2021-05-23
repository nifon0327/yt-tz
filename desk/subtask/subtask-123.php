<?php   
//ewen-2012-12-31
if($noshipAmount==""){
	$noshipResult = mysql_query("SELECT SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount 
	FROM $DataIn.yw1_ordermain M 
	LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	WHERE 1 AND S.Estate>'0'",$link_id);
	if($noshipRow = mysql_fetch_array($noshipResult)) {
	    $noshipQty1=number_format($noshipRow["Qty"]);//用于iphone
		$noshipAmount=sprintf("%.0f",$noshipRow["Amount"]);
		}
	}
//配件成本:
$CostResult=mysql_query("SELECT 
	SUM(A.OrderQty*IF(T.mainType=getSysConfig(103),D.costPrice,A.Price)*IFNULL(C.Rate,1)) AS oTheCost
		FROM $DataIn.cg1_stocksheet A
		LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
		LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber 
		LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
		LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
		WHERE 1 AND S.Estate>'0' AND A.Level=1 ORDER BY A.Id DESC",$link_id);
if($CostRow= mysql_fetch_array($CostResult)){
	do{
		$TempoTheCost=$CostRow["oTheCost"];
		$AmountCB=sprintf("%.0f",$TempoTheCost);
		}while($CostRow= mysql_fetch_array($CostResult));
	}
$GrossProfit=$noshipAmount-$AmountCB;
$GrossProfitPcnt=$noshipAmount>0?sprintf("%.0f",($GrossProfit*100/$noshipAmount)):0;

/*$contentSTR="<li class=TitleA>未出</li>";
$contentSTR.="<li class=DataA><span class='yellowN'>¥".number_format($noshipAmount)."</span></li>";
$contentSTR.="<li class=DataA><span class='yellowN'>$noshipQty1</span>pcs</a></li>";
$contentSTR.="<li class=TitleBL>毛利</li><li class=TitleBR><a href='$Extra' target='_blank'>$GrossProfitPcnt%</a></li>";
$contentSTR.="<li class=DataA><span class='yellowN'>¥".number_format($GrossProfit)."</span></li>";*/
$contentSTR="<li class=TitleBL>未出  <span class='yellowN'>$noshipQty1 pcs</span></li><li class=TitleBR><a href='$Extra' target='_blank'>¥".number_format($noshipAmount)."</a></li>";
$contentSTR.="<li class=TitleBL>利润 <span class='yellowN'>$GrossProfitPcnt%</span></li><li class=TitleBR><a href='$Extra' target='_blank'>¥".number_format($GrossProfit)."</a></li>";
?>
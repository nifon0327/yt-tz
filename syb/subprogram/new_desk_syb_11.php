<?php 
//电信
//代码共享-EWEN 2012-08-19
//预估费用
//FOB需求计算
	//RGXQ：人工-需求单统计：当月出货订单的需求单中,配件主分类为3的配件需求单总额
	//FOBXQ：FOB-需求单统计：当月出货订单的需求单中,配件分类为9080FOB费用的需求单总额
	$checkDataISql=mysql_query("
		SELECT IFNULL(SUM(G.Price*G.OrderQty),0) AS Amount,'2' AS Type FROM ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
			WHERE T.mainType=3 AND DATE_FORMAT(M.Date,'%Y-%m')='$CheckTime'
	UNION ALL
		SELECT IFNULL(SUM(G.Price*G.OrderQty),0) AS Amount,'4' AS Type 
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
			WHERE  D.TypeId='8000' AND DATE_FORMAT(M.Date,'%Y-%m')='$CheckTime'
	",$link_id);
	if($checkDataIRow=mysql_fetch_array($checkDataISql)){
		do{
			$Type=$checkDataIRow["Type"];
			if($Type==2){
				$DataCheck2B[$Subscript]=$checkDataIRow["Amount"];
				}
			else{
				$DataCheck4B[$Subscript]=$checkDataIRow["Amount"];
				}
			}while($checkDataIRow=mysql_fetch_array($checkDataISql));
		}
	/*辅料与行政费用合并
	$checkDataISql=mysql_fetch_array(mysql_query("
		SELECT IFNULL(SUM(G.Price*G.OrderQty),0) AS Amount 
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
			WHERE  D.TypeId='9101' AND DATE_FORMAT(M.Date,'%Y-%m')='$CheckTime'
	",$link_id));
	//$DataCheck3B[$Subscript]=$checkDataISql["Amount"];
	*/
//行政摊提需求单统计	+辅料
$checkDataISql=mysql_fetch_array(mysql_query("
		SELECT IFNULL(SUM(G.Price*G.OrderQty),0) AS Amount 
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
			WHERE  D.TypeId IN(9101,9118) AND DATE_FORMAT(M.Date,'%Y-%m')='$CheckTime'
	",$link_id));
$DataCheck1B[$Subscript]=$checkDataISql["Amount"];

//仓储摊提需求单统计	
$checkDataISql=mysql_fetch_array(mysql_query("
		SELECT IFNULL(SUM(G.Price*G.OrderQty),0) AS Amount 
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
			WHERE  D.TypeId='9122' AND DATE_FORMAT(M.Date,'%Y-%m')='$CheckTime'
	",$link_id));
$DataCheck5B[$Subscript]=$checkDataISql["Amount"];

	//行政费用需求单统计
	/*
	$checkDataI3Sql=mysql_query("SELECT SUM(S.Qty*S.Price*B.Rate*M.Sign) AS CB
		FROM $DataIn.ch1_shipsheet S 
		LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
		LEFT JOIN $DataIn.trade_object G ON G.CompanyId=M.CompanyId
		LEFT JOIN $DataPublic.currencydata B ON B.Id=G.Currency
		WHERE 1 AND M.Estate=0 AND DATE_FORMAT(M.Date,'%Y-%m')='$CheckTime' ORDER BY M.Date DESC",$link_id);	
	if($checkDataI3Row=mysql_fetch_array($checkDataI3Sql)){
		//采购美金部分
		$checkDataI3SqlUSD=mysql_query("SELECT SUM(G.OrderQty*G.Price*B.Rate*M.Sign) AS CB
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=G.CompanyId
			LEFT JOIN $DataPublic.currencydata B ON B.Id=P.Currency
			WHERE 1 AND M.Estate=0 AND DATE_FORMAT(M.Date,'%Y-%m')='$CheckTime' AND P.Currency=2 ORDER BY M.Date DESC",$link_id);
		if($checkDataI3RowUSD=mysql_fetch_array($checkDataI3SqlUSD)){
			$I3USD=$checkDataI3RowUSD["CB"]==""?0:$checkDataI3RowUSD["CB"];
			}
		$I3=$checkDataI3Row["CB"]==""?0:$checkDataI3Row["CB"];
		include "../model/subprogram/sys_parameters.php";
		$HZXQ=($I3-$I3USD)*$HzRate;
		}

	$DataCheck1B[$Subscript]=$HZXQ;
		*/
?>
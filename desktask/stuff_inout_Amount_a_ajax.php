<?php   
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$TempArray=explode("|",$TempId);
$TypeId=$TempArray[0];	
switch($TypeId){
	case 1:
	       $mySql="SELECT SUM(G.Price*C.Rate*K.Qty) AS Amount1,
				   SUM(G.Price/(1+T.Value)*C.Rate*K.Qty) AS Amount2,DATE_FORMAT(M.Date,'%Y-%m') AS Month 
	               FROM $DataIn.ck1_rksheet K 
	               LEFT JOIN $DataIn.ck1_rkmain M ON K.Mid = M.Id 
				   LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = K.StockId 
				   LEFT JOIN $DataIn.trade_object O ON O.CompanyId = G.CompanyId 
				   LEFT JOIN $DataIn.providersheet P ON P.CompanyId = O.CompanyId
			       LEFT JOIN $DataIn.currencydata C ON C.Id = O.Currency
	               LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax
				   WHERE K.Type =1 AND G.Price>0  GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY DATE_FORMAT(M.Date,'%Y-%m') DESC ";
	break;
	
	case 2:
	
	    $mySql  = "SELECT SUM(B.Price*C.Rate*B.Qty) AS Amount1,
			     SUM(B.Price/(1+T.Value)*C.Rate*B.Qty) AS Amount2,DATE_FORMAT(B.Date,'%Y-%m') AS Month 
                 FROM $DataIn.ck7_bprk B  
			     LEFT JOIN $DataIn.trade_object O ON O.CompanyId = B.CompanyId 
			     LEFT JOIN $DataIn.providersheet P ON P.CompanyId = O.CompanyId
		         LEFT JOIN $DataIn.currencydata C ON C.Id = O.Currency
                 LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax
			     WHERE B.Estate =0 AND B.CompanyId>0 AND B.Price>0 GROUP BY DATE_FORMAT(B.Date,'%Y-%m') 
			     ORDER BY DATE_FORMAT(B.Date,'%Y-%m') DESC ";
	break;
	
	case 3:
	     $mySql  = "SELECT SUM(K.Price*C.Rate*K.Qty) AS Amount1,
			     SUM(K.Price/(1+T.Value)*C.Rate*K.Qty) AS Amount2,DATE_FORMAT(M.Date,'%Y-%m') AS Month 
                 FROM $DataIn.ck1_rksheet K 
                 LEFT JOIN $DataIn.ck1_rkmain M ON K.Mid = M.Id 
                 LEFT JOIN $DataIn.ck3_bcsheet B ON B.RkId = K.Id 
                 LEFT JOIN $DataIn.ck3_bcmain BM ON BM.Id = B.Mid 
			     LEFT JOIN $DataIn.trade_object O ON O.CompanyId = BM.CompanyId 
			     LEFT JOIN $DataIn.providersheet P ON P.CompanyId = O.CompanyId
		         LEFT JOIN $DataIn.currencydata C ON C.Id = O.Currency
                 LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax
			     WHERE K.Type =3 AND K.Price>0 GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY DATE_FORMAT(M.Date,'%Y-%m') DESC ";
	break;
	case 4:
	     $mySql  = "SELECT SUM(G.Price*C.Rate*L.Qty) AS Amount1,
			     SUM(G.Price/(1+T.Value)*C.Rate*L.Qty) AS Amount2,DATE_FORMAT(L.Date,'%Y-%m') AS Month 
                 FROM $DataIn.ck5_llsheet L  
			     LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = L.StockId 
			     LEFT JOIN $DataIn.trade_object O ON O.CompanyId = G.CompanyId 
			     LEFT JOIN $DataIn.providersheet P ON P.CompanyId = O.CompanyId
		         LEFT JOIN $DataIn.currencydata C ON C.Id = O.Currency
                 LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax
			     WHERE L.Type =1  AND G.Price>0 GROUP BY DATE_FORMAT(L.Date,'%Y-%m') ORDER BY DATE_FORMAT(L.Date,'%Y-%m') DESC ";
	break;
	case 5:
	     $mySql  = "SELECT SUM(D.Price*C.Rate*L.Qty) AS Amount1,
			     SUM(D.Price/(1+T.Value)*C.Rate*L.Qty) AS Amount2,DATE_FORMAT(L.Date,'%Y-%m') AS Month
                 FROM $DataIn.ck8_bfsheet L   
                 LEFT JOIN $DataIn.stuffdata  D ON D.StuffId = L.StuffId
                 LEFT JOIN $DataIn.bps  B ON B.StuffId = D.StuffId
			     LEFT JOIN $DataIn.trade_object O ON O.CompanyId = B.CompanyId 
			     LEFT JOIN $DataIn.providersheet P ON P.CompanyId = O.CompanyId
		         LEFT JOIN $DataIn.currencydata C ON C.Id = O.Currency
                 LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax
			     WHERE L.Estate =0  AND D.Price>0 GROUP BY DATE_FORMAT(L.Date,'%Y-%m') ORDER BY DATE_FORMAT(L.Date,'%Y-%m') DESC";
	break;
	case 6:
	     $mySql  = "SELECT SUM(L.Price*C.Rate*L.Qty) AS Amount1,
			     SUM(L.Price/(1+T.Value)*C.Rate*L.Qty) AS Amount2,DATE_FORMAT(L.Date,'%Y-%m') AS Month
                 FROM $DataIn.ck5_llsheet L 
                 LEFT JOIN $DataIn.ck2_thsheet B ON B.Id = L.FromId 
                 LEFT JOIN $DataIn.ck2_thmain BM ON BM.Id = B.Mid 
			     LEFT JOIN $DataIn.trade_object O ON O.CompanyId = BM.CompanyId 
			     LEFT JOIN $DataIn.providersheet P ON P.CompanyId = O.CompanyId
		         LEFT JOIN $DataIn.currencydata C ON C.Id = O.Currency
                 LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax
			     WHERE L.Type =3 AND L.Price>0 GROUP BY DATE_FORMAT(L.Date,'%Y-%m') ORDER BY DATE_FORMAT(L.Date,'%Y-%m') DESC";
	break;
	
}

$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=780;
$TotalAmount1 = $TotalAmount2 = $TotalAmount3 = 0 ;
$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
if($myRow = mysql_fetch_array($myResult)){
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#CCCCCC'>
				<td width='50' height='20' align='center'>序号</td>
				<td width='250' align='center'>月份</td>
				<td width='150' align='center'>含税金额</td>
				<td width='150' align='center'>成本金额</td>
				<td width='150' align='center'>差额</td>
			</tr></table>";
	do{
		
		$Month = $myRow["Month"];
		$Amount1 = sprintf("%.3f", $myRow["Amount1"]);
		$TotalAmount1+=$Amount1;
	    $Amount2 = sprintf("%.3f", $myRow["Amount2"]);
	    $TotalAmount2+=$Amount2;
	    $Amount3 = $Amount1 - $Amount2;
	    $TotalAmount3=$Amount3;
	    $Amount1 = number_format($Amount1);
	    $Amount2 = number_format($Amount2);
	    $Amount3 = number_format($Amount3);
		$DivNum=$predivNum."b".$i;
		$TempId="$TypeId|$Month";
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"stuff_inout_Amount_b\",\"desktask\");' 
		id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		$htmlstr="<table width='$tableWidth' cellspacing='0' border='0' id='HideTable_$DivNum$i' style='display:none'>
			<tr >
				<td height='30'>
				<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
				</td>
			</tr></table>";
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#CCCCCC'>
				<td width='50' height='20' align='center'>$showPurchaseorder $i</td>
				<td width='250' align='center'>$Month</td>
				<td width='150' align='right'>$Amount1</td>
				<td width='150' align='right'>$Amount2</td>
				<td width='150' align='right'>$Amount3</td>
			</tr></table>";
         echo $htmlstr;
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
		$TotalAmount1 = number_format($TotalAmount1);
		$TotalAmount2 = number_format($TotalAmount2);
		$TotalAmount3 = number_format($TotalAmount3);
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#CCCCCC'>
				<td width='300' height='20' align='right'>总计</td>
				<td width='150' align='right'>$TotalAmount1</td>
				<td width='150' align='right'>$TotalAmount2</td>
				<td width='150' align='right'>$TotalAmount3</td>
			</tr></table>";
		
	}
?>
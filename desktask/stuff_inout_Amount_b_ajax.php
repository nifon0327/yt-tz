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
$Month=$TempArray[1];	
switch($TypeId){
	case 1:
	    $InOutDateStr = "入库日期";
	    $mySql  =  "SELECT D.StuffId,D.StuffCname,U.Name AS UnitName,D.Picture,O.Forshort,K.Qty,G.Price,
	               (G.Price*C.Rate*K.Qty) AS Amount1,(G.Price/(1+T.Value)*C.Rate*K.Qty) AS Amount2,
	               T.Name AS AddTaxName,M.Date 
	               FROM $DataIn.ck1_rksheet K 
	               LEFT JOIN $DataIn.ck1_rkmain M ON K.Mid = M.Id 
				   LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = K.StockId 
				   LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
				   LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
				   LEFT JOIN $DataIn.trade_object O ON O.CompanyId = G.CompanyId 
				   LEFT JOIN $DataIn.providersheet P ON P.CompanyId = O.CompanyId
			       LEFT JOIN $DataIn.currencydata C ON C.Id = O.Currency
	               LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax
				   WHERE K.Type =1 AND G.Price>0 AND DATE_FORMAT(M.Date,'%Y-%m')='$Month'";
		
	break;
	
	case 2:
	    $InOutDateStr = "入库日期";
	     $mySql  = "SELECT D.StuffId,D.StuffCname,U.Name AS UnitName,D.Picture,O.Forshort,B.Qty,B.Price,
	               (B.Price*C.Rate*B.Qty) AS Amount1,(B.Price/(1+T.Value)*C.Rate*B.Qty) AS Amount2,
	               T.Name AS AddTaxName,B.Date 
                 FROM $DataIn.ck7_bprk B  
                 LEFT JOIN $DataIn.stuffdata D ON D.StuffId = B.StuffId
                 LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
			     LEFT JOIN $DataIn.trade_object O ON O.CompanyId = B.CompanyId 
			     LEFT JOIN $DataIn.providersheet P ON P.CompanyId = O.CompanyId
		         LEFT JOIN $DataIn.currencydata C ON C.Id = O.Currency
                 LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax
			     WHERE B.Estate =0 AND B.Price>0 AND B.CompanyId>0 AND  DATE_FORMAT(B.Date,'%Y-%m') ='$Month'";
	break;	
	case 3:
	    $InOutDateStr = "入库日期";
	    $mySql  = "SELECT D.StuffId,D.StuffCname,U.Name AS UnitName,D.Picture,O.Forshort,K.Qty,K.Price,
	               (K.Price*C.Rate*K.Qty) AS Amount1,(K.Price/(1+T.Value)*C.Rate*K.Qty) AS Amount2,
	               T.Name AS AddTaxName,M.Date 
                 FROM $DataIn.ck1_rksheet K 
                 LEFT JOIN $DataIn.ck1_rkmain M ON K.Mid = M.Id 
                 LEFT JOIN $DataIn.ck3_bcsheet B ON B.RkId = K.Id 
                 LEFT JOIN $DataIn.ck3_bcmain BM ON BM.Id = B.Mid 
                 LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
				 LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
			     LEFT JOIN $DataIn.trade_object O ON O.CompanyId = BM.CompanyId 
			     LEFT JOIN $DataIn.providersheet P ON P.CompanyId = O.CompanyId
		         LEFT JOIN $DataIn.currencydata C ON C.Id = O.Currency
                 LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax
			     WHERE K.Type =3 AND K.Price>0 AND DATE_FORMAT(M.Date,'%Y-%m')='$Month' ";
	break;	
	
	case 4:
	    $InOutDateStr = "出库日期";
	    $mySql  = "SELECT D.StuffId,D.StuffCname,U.Name AS UnitName,D.Picture,O.Forshort,L.Qty,G.Price,
	               (G.Price*C.Rate*L.Qty) AS Amount1,(G.Price/(1+T.Value)*C.Rate*L.Qty) AS Amount2,
	               T.Name AS AddTaxName,L.Date 
                 FROM $DataIn.ck5_llsheet L  
			     LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = L.StockId 
			     LEFT JOIN $DataIn.stuffdata D ON D.StuffId = L.StuffId
				 LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
			     LEFT JOIN $DataIn.trade_object O ON O.CompanyId = G.CompanyId 
			     LEFT JOIN $DataIn.providersheet P ON P.CompanyId = O.CompanyId
		         LEFT JOIN $DataIn.currencydata C ON C.Id = O.Currency
                 LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax
			     WHERE L.Type =1 AND G.Price>0 AND DATE_FORMAT(L.Date,'%Y-%m')='$Month' ";
	break;	
	
	case 5:
	    $InOutDateStr = "出库日期";
	    $mySql  = "SELECT D.StuffId,D.StuffCname,U.Name AS UnitName,D.Picture,O.Forshort,L.Qty,D.Price,
	               (D.Price*C.Rate*L.Qty) AS Amount1,(D.Price/(1+T.Value)*C.Rate*L.Qty) AS Amount2,
	               T.Name AS AddTaxName,L.Date 
                 FROM $DataIn.ck8_bfsheet L   
                 LEFT JOIN $DataIn.stuffdata  D ON D.StuffId = L.StuffId
                 LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
                 LEFT JOIN $DataIn.bps  B ON B.StuffId = D.StuffId
			     LEFT JOIN $DataIn.trade_object O ON O.CompanyId = B.CompanyId 
			     LEFT JOIN $DataIn.providersheet P ON P.CompanyId = O.CompanyId
		         LEFT JOIN $DataIn.currencydata C ON C.Id = O.Currency
                 LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax
			     WHERE L.Estate =0  AND DATE_FORMAT(L.Date,'%Y-%m')='$Month' ";
	break;	
	
	case 6:
	    $InOutDateStr = "出库日期";
        $mySql  = "SELECT D.StuffId,D.StuffCname,U.Name AS UnitName,D.Picture,O.Forshort,L.Qty,L.Price,
	               (L.Price*C.Rate*L.Qty) AS Amount1,(L.Price/(1+T.Value)*C.Rate*L.Qty) AS Amount2,
	               T.Name AS AddTaxName,L.Date 
                 FROM $DataIn.ck5_llsheet L 
                 LEFT JOIN $DataIn.ck2_thsheet B ON B.Id = L.FromId 
                 LEFT JOIN $DataIn.ck2_thmain BM ON BM.Id = B.Mid 
                 LEFT JOIN $DataIn.stuffdata  D ON D.StuffId = L.StuffId
                 LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
			     LEFT JOIN $DataIn.trade_object O ON O.CompanyId = BM.CompanyId 
			     LEFT JOIN $DataIn.providersheet P ON P.CompanyId = O.CompanyId
		         LEFT JOIN $DataIn.currencydata C ON C.Id = O.Currency
                 LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax
			     WHERE L.Type =3 AND L.Price>0 AND DATE_FORMAT(L.Date,'%Y-%m')='$Month'";
	    
	break;	
	
}
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=880;
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
if($myRow = mysql_fetch_array($myResult)){
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#CCCCCC'>
				<td width='40' height='20' align='center'>序号</td>
				<td width='70' align='center'>$InOutDateStr</td>
				<td width='70' align='center'>配件ID</td>
				<td align='center'>配件名称</td>
				<td width='100' align='center'>供应商</td>
				<td width='70' align='center'>增值税率</td>
				<td width='60' align='center'>数量</td>
				<td width='70' align='center'>含税价</td>
				<td width='70' align='center'>含税金额</td>
				<td width='70' align='center'>成本金额</td>
				
			</tr></table>";
	do{
	    
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Qty=$myRow["Qty"];
		$Price = $myRow["Price"];
		$Amount1=sprintf("%.3f", $myRow["Amount1"]);
		$Amount2=sprintf("%.3f", $myRow["Amount2"]);;
		$AddTaxName=$myRow["AddTaxName"];
		$Date=$myRow["Date"];
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		
		$Picture=$myRow["Picture"];
		$Forshort=$myRow["Forshort"];
		include "../model/subprogram/stuffimg_model.php";
		
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr >
				<td width='40' height='20' align='center'>$i</td>
				<td width='70' align='center'>$Date</td>
				<td width='70' align='center'>$StuffId</td>
				<td >$StuffCname</td>
				<td width='100' align='center'>$Forshort</td>
				<td width='70' align='center'>$AddTaxName</td>
				<td width='60' align='center'>$Qty</td>
				<td width='70' align='right'>$Price</td>
				<td width='70' align='right'>$Amount1</td>
				<td width='70' align='right'>$Amount2</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>
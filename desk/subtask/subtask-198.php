<?php   
$MonthFirstDay=date('Y-m-01');
$MonthLastDay=date('Y-m-t');
$dzAmount=0;$nodzAmount=0;
$totalMyResult=mysql_fetch_array(mysql_query("SELECT  SUM(S.Qty*S.Price*D.Rate) AS Amount 
	FROM $DataIn.ch1_shipmain M 
	LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
    LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	WHERE 1 AND M.Estate='0'  AND (S.Type=1 OR S.Type=3) AND M.Date>='$MonthFirstDay' AND  M.Date<='$MonthLastDay'  AND P.ProductId>0 ",$link_id));
$ShipTotal=$totalMyResult["Amount"];

$dzchMysql="SELECT P.dzSign,SUM(S.Qty*S.Price*D.Rate) AS Amount 
	FROM $DataIn.ch1_shipmain M 
	LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
    LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
    LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	WHERE 1 AND M.Estate='0' AND (S.Type=1 OR S.Type=3) AND M.Date>='$MonthFirstDay' AND  M.Date<='$MonthLastDay' 
    AND P.ProductId>0 AND T.Type=1 GROUP BY P.dzSign";
$dzchResult = mysql_query($dzchMysql,$link_id);
$dzAmount=0;$nodzAmount=0;
if($dzchRow= mysql_fetch_array($dzchResult)){
	do{
    	$dzSign=$dzchRow["dzSign"];
        if ($dzSign==1){
        	$dzAmount=$dzchRow["Amount"]==""?0:sprintf("%.2f",$dzchRow["Amount"]);
            }
         else{
            $nodzAmount=$dzchRow["Amount"]==""?0:sprintf("%.2f",$dzchRow["Amount"]);
         	}
     	}while($dzchRow= mysql_fetch_array($dzchResult));
    $BgAmount=$dzAmount+$nodzAmount;
    $dzPercent=sprintf("%.1f",($dzAmount*100/$BgAmount));
    $BgPre=sprintf("%.0f",($BgAmount*100/$ShipTotal));

	$contentSTR="<li class=TitleBL>报关  <span class='yellowN'>$BgPre%</span></li><li class=TitleBR><a href='../../admin/ch_shiping_BG.php' target='_blank' style='CURSOR: pointer;color:#FF6633'><span class='yellowN'>".number_format($BgAmount)."</a></span></li>";

	$contentSTR.="<li class=DataBL>电子类  <span class='yellowN'>$dzPercent%</span></li><li class=DataBR><a href='../../admin/order_ship_dz.php' target='_blank' style='CURSOR: pointer;color:#FF6633'<span class='yellowN'>".number_format($dzAmount)."</a></span></li>";
	}
?>
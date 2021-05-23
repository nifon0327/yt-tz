<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw1_fkoutsheet
$DataIn.trade_object
$DataPublic.currencydata
$DataIn.stuffdata
二合一已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$tableWidth=780;

//参数拆分
$TempArray=explode("|",$TempId);
$Mid=$TempArray[0];
$Sign=$TempArray[1];
$predivNum=$TempArray[2];
if ($Sign==1){
    echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='50' align='center'>出货流水号</td>
		<td width='90' align='center'>客户</td>
		<td width='200' align='center'>Invoice名称</td>
		<td width='100' align='center'>出货金额</td>
		<td width='59' align='center'>出货日期</td>
	</tr>";
	
	
$mySql="
		SELECT M.PayDate,M.PayAmount,M.Remark,S.Mid,S.chId,H.InvoiceNO,H.InvoiceFile,P.Forshort,C.Rate,C.PreChar,
		SUM(HS.Qty*HS.Price*H.Sign) AS ShipAmount 
		FROM $DataIn.cw6_orderinmain M 
		LEFT JOIN $DataIn.cw6_orderinsheet S ON S.Mid=M.Id 
		LEFT JOIN $DataIn.ch1_shipmain H ON H.Id=S.chId 
		LEFT JOIN $DataIn.ch1_shipsheet HS ON HS.Mid=H.Id 
		LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
		LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
		WHERE M.Id = '$Mid' GROUP BY S.chId 
		ORDER BY S.chId";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
$AmountSum=0;
$QtySum=0;
if($myRow = mysql_fetch_array($myResult)){
        $d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
	do{
		$PayDate=$myRow["PayDate"];
		$PayAmount=$myRow["PayAmount"];
		$Forshort=$myRow["Forshort"];
		$InvoiceNO=$myRow["InvoiceNO"];
		$InvoiceFile=$myRow["InvoiceFile"];
		$Rate=$myRow["Rate"];
		$chId=$myRow["chId"];
		$PreChar=$myRow["PreChar"];
		
		$ShipAmount=number_format($myRow["ShipAmount"],2);
		
		//加密参数
		 if ($InvoiceFile==1){
            $dfname=urldecode($InvoiceNO);
            $f1=anmaIn($InvoiceNO.".pdf",$SinkOrder,$motherSTR);
	        $InvoiceNO=strlen($InvoiceNO)>20?"<a href=\"../admin/openorload.php?dfname=$dfname&Type=invoice\" target=\"download\">$InvoiceNO</a>":"<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\" >$InvoiceNO</a>";
        }

		echo"
			<tr bgcolor='#FFFFFF'>
				<td height='20' align='center'>$i</td>
				<td align='center'>$chId</td>
				<td align='center'>$Forshort</td>
				<td align='center'>$InvoiceNO</td>
				<td align='right'>$PreChar$ShipAmount</td>
				<td align='center'>$PayDate</td>
			</tr>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
}else{
    echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='90' align='center'>客户</td>
		<td width='535' align='center'>预收说明</td>
		<td width='80 align='center'>预收金额</td>
		<td width='80' align='center'>收款人</td>
		<td width='80' align='center'>收款日期</td>
	</tr>";

	$mySql="
		SELECT S.Id,S.Remark,S.Amount,S.PayDate,N.Name,P.Forshort,C.PreChar  
		FROM  $DataIn.cw6_advancesreceived  S 
	    LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
	    LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency  
		LEFT JOIN $DataPublic.staffmain N ON N.Number=S.Operator 
		WHERE S.Id = '$Mid' 
		ORDER BY S.Id";
	$myResult = mysql_query($mySql,$link_id);
$i=1;
$AmountSum=0;
$QtySum=0;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Forshort=$myRow["Forshort"];
		$Remark=$myRow["Remark"];
		$Amount=$myRow["Amount"];
		$PreChar=$myRow["PreChar"];
		$Date=$myRow["PayDate"];
		$Name=$myRow["Name"];
		echo"
			<tr bgcolor='#FFFFFF'>
				<td height='20' align='center'>$i</td>
				<td align='center'>$Forshort</td>
				<td>$Remark</td>
				<td align='right'><div class='greenB'>$PreChar$Amount</div></td>
				<td align='center'>$Name</td>
				<td align='center'>$Date</td>
			</tr>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
}

?>
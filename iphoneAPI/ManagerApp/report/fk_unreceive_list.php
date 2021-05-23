<?

$jsonArray = array();

if ($companyID && $month ) {
if ($onlyRemark != 1) {
	$checkDj=mysql_query("SELECT 
M.Id,M.CompanyId,M.Number,M.InvoiceNO,D.PreChar,M.Ship,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.cwSign,M.Operator,C.Forshort 
FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency  
WHERE M.Date like '".$month."%' and M.CompanyId=$companyID and M.cwSign IN (1,2)
ORDER BY M.Date DESC",$link_id); 

		 while ($checkDjRow = mysql_fetch_array($checkDj)) {
			 $Id = $checkDjRow["Id"];
			 $operId = $checkDjRow["Operator"];
			 $checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$Id'",$link_id));
			 $checkAmount = $checkAmount["Amount"];
			  $PreChar = $checkDjRow["PreChar"];
			  $Forshort = $checkDjRow["Forshort"];
			 $color = '#000000';
			 if ($checkDjRow["cwSign"] == 2) {
				 $color = '#4876FF';
				 $alreadyAmount = mysql_fetch_array(mysql_query("SELECT SUM(Amount) AS Amount FROM $DataIn.cw6_orderinsheet WHERE chId='$Id'",$link_id));
			 
			 $checkAmount -= $alreadyAmount["Amount"];
			 }
			 
			 $operName = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$operId ORDER BY Number LIMIT 1",$link_id));
			 
			 $jsonArray[] = array("sum"=>$checkAmount,"InvoiceNO"=>$checkDjRow["InvoiceNO"],
			 					   "Date"=>$checkDjRow["Date"],"Estate"=>$checkDjRow["Estate"],
								   "Operator"=>$operName["Name"],"Wise"=>$checkDjRow["Wise"],
								   "cwSign"=>$checkDjRow["cwSign"],"Number"=>$checkDjRow["Number"],
								   "Ship"=>$checkDjRow["Ship"],"Sign"=>$checkDjRow["Sign"],"SumColor"=>$color,'prechar'=>"$PreChar",'forshort'=>"$Forshort");
		 }
		 
		 }
		 $remDictAll = array('no'=>'no');
		 if ($getRemark==1 ) {
			$monthes = $info[3];

			if  (strlen( $monthes)>0) {
			$monthes = explode(',', $monthes);
			$monthes = implode("','", $monthes);
			$monthes = "'".$monthes."'";
				$sql = "
				select L.remark,L.time,L.comId_y_m,M.Name from unpay_unreceive_log L 
Left join staffmain M on M.Number=L.Operator
where L.estate>=1  and L.pay_reci=0 and L.comId_y_m  in ($monthes);
";
//echo($sql);
				$allRs = mysql_query($sql,$link_id);
				while ($allRsRow = mysql_fetch_array($allRs)) {
					$remark = $allRsRow['remark'];//comId_y_m
					$time = $allRsRow['time'];
					$time = GetDateTimeOutString($time,'');
					$comId_y_m = $allRsRow['comId_y_m'];
					$Name = $allRsRow['Name'];
					$remDictAll[$comId_y_m] = array('remark'=>"$remark\n$Name",'remarktime'=>"$time");
					
				}
				
			}
		}
		 
		 $jsonArray = array("success"=>1,"result"=>$jsonArray ,'remark'=>$remDictAll);
} else {
	$jsonArray = array("success"=>0 );
}


?>
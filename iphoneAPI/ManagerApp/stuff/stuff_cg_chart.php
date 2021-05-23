<?php 
$typeidSelect = $info[0];

  $sqlEach = mysql_query("SELECT 
                           SUM(CG.FactualQty  + CG.AddQty) AS OrderQty,
						   DATE_FORMAT(M.Date,'%y/%m') as Month
                        FROM $DataIn.cg1_stocksheet CG
						left join $DataIn.stuffdata D on D.StuffId=CG.StuffId
							LEFT JOIN $DataIn.cg1_stockmain M ON CG.Mid=M.Id 
left join $DataIn.bps P on P.StuffId=CG.StuffId
LEFT JOIN $DataIn.trade_object   AS R        ON R.companyid=P.companyid
left join $DataPublic.currencydata C on C.Id=R.currency
where D.TypeId=$typeidSelect and CG.Mid>0 and  DATE_FORMAT(M.Date,'%y/%m')<>'00/00'
group by   DATE_FORMAT(M.Date,'%y/%m') 
						");
			
			$aTypeMonth = array();			
	 while ($sqlEachRow = mysql_fetch_assoc($sqlEach)) {
		
		 $eachOrderQty = $sqlEachRow["OrderQty"];
		 $eachMonth = $sqlEachRow["Month"];
		
		 $aTypeMonth[] = array("QTY"=>"$eachOrderQty","Title"=>"$eachMonth");
 
	 } 
 
 $jsonArray= array("aType"=>$aTypeMonth);
?>
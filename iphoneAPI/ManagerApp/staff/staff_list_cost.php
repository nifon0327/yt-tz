<?
//行政请款记录

$mySql="SELECT S.Id,S.Estate,S.Content,S.Amount,S.Bill,S.Date,S.Operator,S.OPdatetime,T.Name AS Type,C.PreChar,C.Symbol   
 	FROM $DataIn.hzqksheet S 
	LEFT JOIN $DataPublic.adminitype T ON S.TypeId=T.TypeId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
	WHERE  S.Operator='$Number'   ORDER BY S.Date DESC LIMIT 100";//AND TIMESTAMPDIFF(DAY,S.Date,CURDATE())<366 
//echo $mySql;
  $myResult = mysql_query($mySql);
  while($myRow = mysql_fetch_assoc($myResult))
 {
		     $Content=$myRow["Content"];
		     $Date=$myRow["Date"];
		     $Type=$myRow["Type"];
		     $PreChar=$myRow["PreChar"];
		     $Symbol=$myRow["Symbol"];
		     $Estate=$myRow["Estate"];
		     $Amount=number_format($myRow["Amount"],2);
	        
	        $jsonArray[]=array("Title"=>"$Type",
									        "Col1"=>"$PreChar$Amount",
									        "Col2"=>"$Date",
									        "Remark"=>"$Content",
									        "Estate"=>"$Estate"
									        	        );
    }
?>
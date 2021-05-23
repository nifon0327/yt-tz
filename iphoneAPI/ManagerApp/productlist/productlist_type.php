<?php 

/*

NSString *const key_stuff_count =@"stuff_count";
NSString *const key_typename =@"typename";
NSString *const key_typeid=@"typeid";*/
  $sql = mysql_query("SELECT M.CompanyId, M.Forshort,Count(*) AS ProductCounts, C.PreChar
					FROM $DataIn.trade_object M 
					LEFT JOIN $DataIn.productdata P ON P.CompanyId = M.CompanyId 
					LEFT JOIN $DataPublic.currencydata C ON M.Currency = C.Id
					WHERE M.Estate = 1 
					AND M.ObjectSign IN (1,2)
					AND P.Estate = 1 
					GROUP BY M.CompanyId ORDER BY ProductCounts DESC");
 $jsonArray = array();
 while ($row = mysql_fetch_assoc($sql)) {
	 
	 $stuff_count = $row["ProductCounts"];
	 $typename = $row["Forshort"];
	 $typeid = $row["CompanyId"];
	 $PreChar = $row["PreChar"];
	 $jsonArray[]= array("stuff_count"=>"$stuff_count","typename"=>"$typename","typeid"=>"$typeid","prechar"=>"$PreChar");
 }
 
 
?>
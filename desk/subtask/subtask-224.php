<?php   
//未下单禁用产品
$Result224=mysql_fetch_array(mysql_query(" SELECT count(*) AS nums
	FROM $DataIn.productdata P
	LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	LEFT JOIN (
			    SELECT DATE_FORMAT(MAX(M.OrderDate),'%Y-%m') AS LastMonth,TIMESTAMPDIFF(MONTH,MAX(M.OrderDate),now()) AS Months,S.ProductId            
                FROM $DataIn.yw1_ordermain M 
	            LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
                LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
                WHERE 1 AND P.Estate=1 GROUP BY S.ProductId ORDER BY M.OrderDate DESC
	    ) E ON E.ProductId=P.ProductId
	WHERE 1 AND P.Estate>0 AND E.Months>11 AND E.Months IS NOT NULL",$link_id));
$temp_C224=$Result224["nums"]==""?0:$Result224["nums"];
$tmpTitle="<font color='red'>" .$temp_C224."</font>";
?>
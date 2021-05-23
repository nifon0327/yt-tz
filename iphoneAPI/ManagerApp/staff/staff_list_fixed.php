<?
//固定资产领用记录
/*
$mySql="SELECT C.TypeName,C.Model,C.Qty,C.Price FROM $DataPublic.fixed_userdata D,
						   (SELECT A.MID,MAX(A.SDate) AS SDate,B.TypeId,B.Model,B.Qty,B.Price,B.Estate,T.Name AS TypeName
						      FROM $DataPublic.fixed_userdata A 
						      LEFT JOIN $DataPublic.fixed_assetsdata B ON A.MID=B.Id
						      LEFT JOIN $DataPublic.oa2_fixedsubtype T ON T.Id=B.TypeId 
						    WHERE B.TypeId IN (14,17,46) AND A.UserType=1 GROUP BY A.MID
	                    ) C WHERE D.User='$Number' AND C.Estate=1 AND D.UserType=1 AND C.MID=D.MID AND C.SDate=D.SDate GROUP BY D.MID";
*/
//modify by cabbage 加上 left join dealerdata，對應供應商的幣別，取正確的幣別符號
$mySql="SELECT C.TypeName, C.Model, C.Qty, F.PreChar, C.Price
		FROM $DataPublic.fixed_userdata D, 
		(
			SELECT A.MID, MAX( A.SDate ) AS SDate, B.TypeId, B.Model, B.Qty, B.Price, B.CompanyId, B.Estate, T.Name AS TypeName
			FROM $DataPublic.fixed_userdata A
			LEFT JOIN $DataPublic.fixed_assetsdata B ON A.MID = B.Id
			LEFT JOIN $DataPublic.oa2_fixedsubtype T ON T.Id = B.TypeId
			WHERE B.TypeId
			IN ( 14, 17, 46 ) 
			AND A.UserType =1
			GROUP BY A.MID
		) C
		LEFT JOIN $DataPublic.dealerdata E ON C.CompanyId=E.CompanyId 
		LEFT JOIN $DataPublic.currencydata F ON IFNULL(E.Currency, 1) = F.Id 
		WHERE D.User = '$Number'
		AND C.Estate =1
		AND D.UserType =1
		AND C.MID = D.MID
		AND C.SDate = D.SDate
		GROUP BY D.MID
		LIMIT 0 , 30";
//echo $mySql;
  $myResult = mysql_query($mySql);
  while($myRow = mysql_fetch_assoc($myResult))
 {
		     $Model=$myRow["Model"];
		     $TypeName=$myRow["TypeName"];
		     $Qty=$myRow["Qty"];
		     $Price=number_format($myRow["Price"]);
		     //add by cabbage 20141118 加上幣別的前置字
		     $PreChar = $myRow["PreChar"];
	        
	         $jsonArray[] = array("Title"=>"$Model",
									        "Col1"=>"$TypeName",
									        "Col2"=>"$Qty",
									        "Col3"=>"$PreChar$Price"
	         );
    }
?>
<?php 
//读取点餐信息
$mySql="SELECT A.Id,A.Price,A.Qty,A.Amount,A.Estate,A.Locks,A.Date,A.Operator,B.Name AS MenuName,C.Name AS CTName,D.Name AS MenuType 
FROM $DataPublic.ct_myorder A
LEFT JOIN $DataPublic.ct_menu B ON B.Id=A.MenuId
LEFT JOIN $DataPublic.ct_data C ON C.Id=B.CtId
LEFT JOIN $DataPublic.ct_type D ON D.Id=B.mType
WHERE A.Operator='$LoginNumber' ORDER BY A.Locks DESC,A.Id DESC";

    $myResult = mysql_query($mySql);
    if($myRow = mysql_fetch_assoc($myResult))
    {
            do 
            {	
                    $Id=$myRow["Id"];
                    $MenuName=$myRow["MenuName"];
	                $Date= $myRow["Date"];
                    $CTName=$myRow["CTName"];
                    $Qty=$myRow["Qty"];
                    $Price=$myRow["Price"];
                    $Amount=$myRow["Amount"];
                    $Operator=$myRow["Operator"];
                     include "../../model/subprogram/staffname.php";
                    $Estate=$myRow["Locks"];
                    $jsonArray[]=array(
								 "Id" =>"$Id",
								  "Date"=>"$Date",
								  "Name"=>"$CTName",
								  "Menu"=>"$MenuName", 
								  "Qty"=>"$Qty",
								  "Price"=>"$Price",
								  "Amount"=>"$Amount",
								  "Estate"=>"$Estate"               
			              );
            }
            while($myRow = mysql_fetch_assoc($myResult));
    }
?>
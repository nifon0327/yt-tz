<?php 
//读取行政费用信息
$mySql="SELECT S.Id,S.Mid,S.Content,S.Amount,S.Bill,S.ReturnReasons,S.Date,S.Estate,S.Locks,S.Operator,T.Name AS Type,C.PreChar,S.Bill,S.ReturnReasons  
 	FROM $DataIn.hzqksheet S 
	LEFT JOIN $DataPublic.adminitype T ON S.TypeId=T.TypeId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
	WHERE 1 AND S.Operator='$LoginNumber'  order by S.Date DESC";
    $myResult = mysql_query($mySql);
     $Dir= "http://".$_SERVER ['HTTP_HOST']. "/download/cwadminicost/";
    if($myRow = mysql_fetch_assoc($myResult))
    {
            do 
            {	
                    $Id=$myRow["Id"];
                    $Content=$myRow["Content"];
	                $Amount=$myRow["Amount"];
                    $Date=  $myRow["Date"];
                    $Type=$myRow["Type"];
                    $Operator=$myRow["Operator"];
                     include "../../model/subprogram/staffname.php";
                    $PreChar=$myRow["PreChar"];
                    $Estate=$myRow["Estate"];
                   
                   $Bill=$myRow["Bill"];
		           $Receipt=$Bill==1?$Dir . "H" . $Id .".jpg":"";
		           $ReturnReasons=$myRow["ReturnReasons"];
			
                     $jsonArray[]=array("Id"=>"$Id",
					                        "Title"=>"$Type",
									        "Amount"=>"$PreChar$Amount",
									        "Date"=>"$Date",
									        "Remark"=>"$Content",
									        "Estate"=>"$Estate",
									        "ReturnReasons"=>"$ReturnReasons",
									        "Receipt"=>"$Receipt"

	               );
            }while($myRow = mysql_fetch_assoc($myResult));
}
?>
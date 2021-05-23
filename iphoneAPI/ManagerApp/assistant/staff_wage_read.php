<?php 
//读取员工薪资信息
$curDate=date("Y-m-d");
/*
	
	UNION ALL 
	SELECT S.Month,S.Amount,'¥' AS PreChar FROM  $DataOut.cwxzsheet S 
	      WHERE S.Number='$LoginNumber' AND S.Estate=0 AND S.Month>='$sMonth'
*/
$sMonth=date("Y-m",strtotime("$curDate  -7   month"));
$mySql="SELECT S.Month,S.Amount,D.PreChar FROM  $DataIn.cwxzsheet S 
                LEFT JOIN $DataPublic.currencydata D ON D.Id = S.Currency 
		WHERE S.Number='$LoginNumber' AND S.Estate=0 AND S.Month>='$sMonth' 

order by Month DESC";
    $myResult = mysql_query($mySql,$link_id);
    if($myRow = mysql_fetch_assoc($myResult))
    {
            do 
            {	
                    $Month=$myRow["Month"];
                    $Amount=number_format($myRow["Amount"]) ;
                    //是否已签收
                    $Sign=1;
                    $checkSign=mysql_query("SELECT Id FROM $DataPublic.wage_list_sign WHERE Number='$LoginNumber' AND SignMonth='$Month' LIMIT 1",$link_id);
                     if($checkRow = mysql_fetch_assoc($checkSign))
                     {
	                     $Sign=0;
                     }
              
                    $PreChar=$myRow["PreChar"];
                    
                    $jsonArray[] = array("Month"=>"$Month","Amount"=>"$PreChar$Amount","Estate"=>"$Sign");
            }
            while($myRow = mysql_fetch_assoc($myResult));
    }
?>
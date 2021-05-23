<?
//员工公积金缴费记录

 $mySql="SELECT * FROM (
                 SELECT Month,mAmount,cAmount FROM $DataIn.sbpaysheet WHERE Number='$Number' AND TypeId=2 
        
                )A ORDER BY A.Month DESC";
//echo $mySql;
  $myResult = mysql_query($mySql);
  while($myRow = mysql_fetch_assoc($myResult))
 {
	        $Month=$myRow["Month"];
	        $mAmount=$myRow["mAmount"];
	        $cAmount=$myRow["cAmount"];
	        $Amount=$mAmount+$cAmount ;
	        
	        $jsonArray[]=array("Title"=>"$Month","Col1"=>"$mAmount","Col2"=>"$cAmount","Col3"=>"¥$Amount");
    }
?>
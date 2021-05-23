<?php
//补料单	
   $mySql="SELECT DATE_FORMAT(R.Date,'%Y-%m') AS Month,COUNT(*) AS Nums,SUM(R.Qty) AS Qty
   				    FROM $DataIn.ck13_replenish R
					WHERE R.Estate=0  GROUP BY DATE_FORMAT(R.Date,'%Y-%m') ORDER BY Month DESC";

	$Result=mysql_query($mySql, $link_id); 
    $jsondata=array();$TotalQty=0;$hidden=0;
	while($myRow = mysql_fetch_array($Result)) 
	{
	     		$Month=$myRow["Month"];
	     		$Nums=$myRow["Nums"];
	     		$Qty=$myRow["Qty"];
	     		$TotalQty+=$Qty;
	     		
		         $headArray=array(
				      "Id"=>"$Month",
				      "onTap"=>array("Value"=>"1","Args"=>"List0|$Month"),
				       "Title"=>array("Text"=>"$Month"),
				       "Col3"=>array("Text"=>"$Qty($Nums)","Margin"=>"0,0,10,0")
				   );
				   
				   $dataArray=array();
					if ($hidden==0 && $Month==date("Y-m")){
						  $FromPage="Read";
						  $CheckMonth=$Month;
						  include "ck_replenish_list0.php";
						  
						   $CountSTR="COUNT_" . $SegmentIndex;
	                       $$CountSTR=$Nums;
					}
					else{
						 $hidden=1;
					}
				   $jsondata[]=array("head"=>$headArray,"hidden"=>"$hidden","data"=>$dataArray);			   
	  }
?>
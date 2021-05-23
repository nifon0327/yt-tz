<?php 
//补货记录
$monthResult=mysql_query("SELECT  DATE_FORMAT(R.Date,'%Y-%m-%d') AS Date,SUM(R.Qty) AS Qty,COUNT(*) AS Counts   
			FROM  $DataIn.ck13_replenish R  
			WHERE  R.Estate=0 AND DATE_FORMAT(R.Date,'%Y-%m')='$CheckMonth'  GROUP BY  DATE_FORMAT(R.Date,'%Y-%m-%d')  ORDER BY Date DESC ",$link_id);
$dataArray=array();
if($monthRow = mysql_fetch_array($monthResult)) 
  {
     do {
            $CheckDate=$monthRow["Date"];
            
            $FactoryCheck_Sign=1;
            include "factory_check.php";
            if ($FactoryData_Hidden) continue;
            
            $Qty=number_format($monthRow["Qty"]);
            $Counts=$monthRow["Counts"];
            
            $DateTitle=date("m-d",strtotime($CheckDate));
            $wName=date("D",strtotime($CheckDate));
             $totalArray=array(
							                      "Id"=>"$CheckDate",
							                      "onTap"=>array("Value"=>"1","Args"=>"List1|$CheckDate"),
							                      "Title"=>array("Text"=>"$DateTitle","rIcon"=>"$wName","Frame"=>"10, 5, 35, 25","Color"=>"#000000"),
							                      "Col3"=>array("Text"=>"$Qty($Counts)","Margin"=>"0,0,10,0")
							                   );  
			
			$dayArray=array();
			 if ((versionToNumber($AppVersion)>=324)) {
			     include "inware/ck_replenish_list1_new.php";
		     } else
			include "ck_replenish_list1.php";			
				                   
             $dataArray[]=array("Tag"=>"Total","onTap"=>"2","hidden"=>"1","data"=>$totalArray,"extList"=>$dayArray); 
      } while($monthRow = mysql_fetch_array($monthResult));
  }
  
if ($FromPage!="Read"){
	    $jsonArray=$dataArray;
}
?>

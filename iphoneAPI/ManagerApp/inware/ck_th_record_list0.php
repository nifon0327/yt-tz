<?php 
//退货记录
$monthResult=mysql_query("SELECT  DATE_FORMAT(M.Date,'%Y-%m-%d') AS Date,SUM(S.Qty) AS Qty,COUNT(*) AS Counts   
			FROM  $DataIn.ck12_thmain M  
			LEFT JOIN $DataIn.ck12_thsheet S ON S.Mid=M.Id  
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId  
			WHERE  DATE_FORMAT(M.Date,'%Y-%m')='$CheckMonth' AND D.SendFloor='$Floor'   GROUP BY  DATE_FORMAT(M.Date,'%Y-%m-%d')  ORDER BY Date DESC ",$link_id);
$dataArray=array();
if($monthRow = mysql_fetch_array($monthResult)) 
  {
     do {
            $CheckDate=$monthRow["Date"];
            
            $FactoryCheck_Sign=1;
            //include "factory_check.php";
            if ($FactoryData_Hidden) continue;
            
            $shQty=number_format($monthRow["shQty"]);
            $Qty=number_format($monthRow["Qty"]);
            $Counts=$monthRow["Counts"];
            
            $DateTitle=date("m-d",strtotime($CheckDate));
            $wName=date("D",strtotime($CheckDate));
             $totalArray=array(
							                      "Id"=>"$CheckDate",
							                      "onTap"=>array("Value"=>"1","Args"=>"List1|$CheckDate"),
							                      "Title"=>array("Text"=>"$DateTitle","rIcon"=>"$wName","Frame"=>"10, 8, 35, 25"),
							                      "Col3"=>array("Text"=>"$Qty($Counts)","Margin"=>"0,0,10,0")
							                   );  
							                   
             $dataArray[]=array("Tag"=>"Total","onTap"=>"1","hidden"=>"1","data"=>$totalArray); 
      } while($monthRow = mysql_fetch_array($monthResult));
  }
  
if ($FromPage!="Read"){
	    $jsonArray=$dataArray;
}

?>

<?php 
//抽检、全检
if ($Floor=="") $Floor=$dModuleId==2152?3:6;

$monthResult=mysql_query("SELECT  DATE_FORMAT(B.Date,'%Y-%m-%d') AS Date,SUM(B.shQty) AS shQty,SUM(B.Qty) AS Qty,COUNT(*) AS Counts   
			FROM  $DataIn.qc_badrecord  B 
			LEFT JOIN $DataIn.gys_shmain M  ON M.Id=B.shMid 
			WHERE  DATE_FORMAT(B.Date,'%Y-%m')='$CheckMonth' AND M.Floor='$Floor'   GROUP BY  DATE_FORMAT(B.Date,'%Y-%m-%d') ORDER BY Date DESC ",$link_id);
$dataArray=array();

if($monthRow = mysql_fetch_array($monthResult)) 
  {
     do {
            $CheckDate=$monthRow["Date"];
            
            $FactoryCheck_Sign=1;
          //  include "factory_check.php";
            if ($FactoryData_Hidden) continue;

            $shQty=number_format($monthRow["shQty"]);
            $Qty=number_format($monthRow["Qty"]);
            $Qty= $Qty =="0"?"":$Qty;
            $Counts=$monthRow["Counts"];
            
            $DateTitle=date("m-d",strtotime($CheckDate));
            $wName=date("D",strtotime($CheckDate));
             $totalArray=array(
							                      "Id"=>"$CheckDate",
							                      "onTap"=>array("Value"=>"1","Args"=>"List1|$CheckDate"),
							                      "Title"=>array("Text"=>"$DateTitle","rIcon"=>"$wName","Frame"=>"10, 8, 35, 25"),
							                      "Col1"=>array("Text"=>"$Qty","Color"=>"#FF0000","Margin"=>"25,0,0,0"),
							                      "Col3"=>array("Text"=>"$shQty($Counts)","Margin"=>"0,0,10,0")
							                   );  
							                   
             $dataArray[]=array("Tag"=>"Total","onTap"=>"1","hidden"=>"1","data"=>$totalArray); 
      } while($monthRow = mysql_fetch_array($monthResult));
  }
  
if ($FromPage!="Read"){
	    $jsonArray=$dataArray;
}

?>

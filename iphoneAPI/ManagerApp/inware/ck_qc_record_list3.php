<?php 
//抽检、全检
if ($Floor=="") $Floor=$dModuleId==2152?3:6;

$monthResult=mysql_query("SELECT  M.CompanyId, C.Forshort,SUM(B.shQty) AS shQty,
			SUM(B.Qty) AS Qty   
			FROM  $DataIn.qc_badrecord  B 
			LEFT JOIN $DataIn.gys_shmain M  ON M.Id=B.shMid 
			LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId
			WHERE  DATE_FORMAT(B.Date,'%Y-%m')='$CheckMonth' AND M.Floor='$Floor'   
			GROUP BY  M.CompanyId  ORDER BY B.Id DESC ",$link_id);
$dataArray=array();
if($monthRow = mysql_fetch_array($monthResult)) 
  {
     do {
            $CheckCompany=$monthRow["CompanyId"];
            $Forshort=$monthRow["Forshort"];
            $shQty=number_format($monthRow["shQty"]);
            $Qty=number_format($monthRow["Qty"]);
              $Qty= $Qty =="0"?"":$Qty;
            $DateTitle=date("m-d",strtotime($CheckDate));
            $wName=date("D",strtotime($CheckDate));
             $totalArray=array(
							                      "Id"=>"$CheckCompany",
							                      "onTap"=>array("Value"=>"1","Args"=>"List4|$CheckCompany"),
							                      "Title"=>array("Text"=>"$Forshort","Frame"=>"10, 5, 95, 25",'Color'=>'#3772BF'),
							                      "Col1"=>array("Text"=>"$Qty","Color"=>"#FF0000","Margin"=>"25,0,0,0"),
							                      "Col3"=>array("Text"=>"$shQty","Margin"=>"0,0,10,0")
							                   );  
							                   
             $dataArray[]=array("Tag"=>"Total","onTap"=>"1","hidden"=>"1","data"=>$totalArray); 
      } while($monthRow = mysql_fetch_array($monthResult));
  }
  
if ($FromPage!="Read"){
	    $jsonArray=$dataArray;
}

?>

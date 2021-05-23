<?php 
//可备料
$lastyear=date("Y-m-d",strtotime("-30 days"));
$cztest = true;
function mySort($a,$b) {
	if ($a["Date"] == $b["Date"] ) {

		return 0;

	} else {

		return ($a["Date"] < $b["Date"]) ? 1 : -1;
	}
}
$secArray = array();
$allMysql = mysql_query("SELECT M.Date as Mdate ,Y.Qty AS mainQty
FROM $DataIn.ck5_llsheet S 
LEFT JOIN $DataIn.ck5_llmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId
LEFT JOIN $DataIn.sc1_mission A on A.POrderId =G.POrderId 
WHERE  S.Estate=0 and M.Date>'$lastyear' and A.Id is not null AND G.POrderId>0 Group BY G.POrderId ORDER BY Mdate DESC");

$lastMdate = "-1";$secCount = 0;$QEstateC =0;

while ($allMysqlR = mysql_fetch_assoc($allMysql)) {
	$Mdate = $allMysqlR["Mdate"];
	$mainQty = $allMysqlR["mainQty"];

	$QEstate = $allMysqlR["QEstate"];
	
	if ($lastMdate == $Mdate) {
		$secArray[$secCount-1]["Num"] ++;
		
		$secArray[$secCount-1]["Sum"]+=$mainQty;
	} else {
		
		$secArray[] = array("Num"=>1,"Sum"=>$mainQty,"Date"=>"$Mdate");
		$secCount ++;
	}
	/*
	if (($QEstate==NULL || $QEstate!=0 ) && $secCount==1) {
		$QEstateC ++;
	}
	*/
	
	$lastMdate = $Mdate;
}

//usort($secArray,"mySort");
$newSecArr = array();

for ($i=0; $i<$secCount;$i++) {
	$eachSec = $secArray[$i];
	$Mdate = $eachSec["Date"];
	$DateTitle=date("m-d",strtotime($Mdate));
	$wName=date("D",strtotime($Mdate)); 
	
	$Qty = $eachSec["Sum"];
	$Qty = $Qty>0? number_format($Qty)."(".$eachSec["Num"].")":"";
	if ($i == 0) {
		$dateArg = $Mdate;
		include "order_ybl_detail.php";
		$newSecArr[]= $jsonArray;
		$newSecArr = array_merge($newSecArr,$deSecArray);
	} else {
	 $tempData=array(
		"RowSet"=>array("height"=>""),
		
				                      "Title"=>array("Text"=>"$DateTitle","FontSize"=>"13","rIcon"=>"$wName","Frame"=>"10,10,60,14"),
				                     
									  
				                      "Col3"=>array("Text"=>"$Qty","Frame"=>"200, 10, 103, 14","FontSize"=>"13"),
				                      //"Rank"=>array("Icon"=>"1"),
				                      // "AddRows"=>$AddRows
									  "dateVal"=>$Mdate
				                   ); 
	$newSecArr[]= array("data"=>$tempData,
						  "Tag"=>"day","CellID"=>"Sects",
						  "Args"=>"$Mdate",
						  "onTap"=>array("value"=>"1","Args"=>"$Mdate","hidden"=>"1","shrink"=>"UpAccessory_blue","Frame"=>"8,16.5,12,12"),
						  "load"=>"0"
						  );
	}
}


$jsonArray = array("cellList"=>$newSecArr,"Segment"=>array("Segmented"=>array("待备料","已备料($QEstateC)"),"SegmentedId"=>array("0","1"),"SegmentIndex"=>"1"));
		//echo json_encode($eachStock);
		//print_r($eachStock);
?>
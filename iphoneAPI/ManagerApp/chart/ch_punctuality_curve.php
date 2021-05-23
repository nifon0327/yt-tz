<?php 
//订单出货准时率
	$curMonth=date("Y-m");
	$dataArray=array();
	
	for($m=0;$m<13;$m++){
		 $checkMonth=date("Y-m",strtotime("$curMonth  -$m  month"));
		 $PuncSelectType=2;
	     include "submodel/order_punctuality.php";
	     $dataArray[]=round($Punc_Value);
	}
	
	$jsonArray=array("Type"=>1,"PickType"=>"1","data"=>$dataArray); 
?>
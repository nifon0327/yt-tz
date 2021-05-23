<?php
   $isLastscOrderSign = 0 ;
   if($scQty == $Qty){ //工单生产数量 == 订单数量
	   $isLastscOrderSign = 1 ;
   }else{
	   //已备料的工单
	   $checkllOrderResult = mysql_fetch_array(mysql_query("SELECT COUNT(Id) AS orderCount FROM $DataIn.yw1_scsheet WHERE POrderId= $POrderId AND scFrom =1",$link_id));
	   $orderCount = $checkllOrderResult["orderCount"];
	   if($orderCount == 1){
		   $isLastscOrderSign = 2 ; // 最后一张单，按余下数量分配
	   }else{
		   $isLastscOrderSign = 3 ; //中间单，按比例分配
	   }  
   }
?>
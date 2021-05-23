 <?php  
      //PI交期转换成周数显示
      if ($DeliveryDate!="" && $DeliveryDate!="&nbsp;" ){
         if ($curWeeks==""){
	          $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
              $curWeeks=$dateResult["CurWeek"];
         }
	      $DeliveryDate=str_replace("*", "", $DeliveryDate);
	      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$DeliveryDate',1) AS Week",$link_id));
          $toWeek=$dateResult["Week"];
    
         if ($toWeek>0){
	          $week=substr($toWeek, 4,2);
		      $dateArray= GetWeekToDate($toWeek,"m/d");
		      $weekName="Week " . $week;
		      $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
		      
		      $Delivery_Color=($toWeek<=$curWeeks && $Delivery_NoColor==0)?"#FF0000" : "#000000";
		      $DeliveryDate="<div style='color:$Delivery_Color;'>$weekName</div><div style='font-size:10px;color:#AAAAAA'>$dateSTR</div>";
	      }
      }
?>
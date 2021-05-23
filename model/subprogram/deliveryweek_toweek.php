 <?php  
  //采购交期转换成周数显示
  $yearWeek = date("Y")."53";
  
  if (($DeliveryWeek!="" && $DeliveryWeek!="&nbsp;" && ($DeliveryWeek>0 && $DeliveryWeek<=$yearWeek)) || ($DeliveryDate!="" && $DeliveryDate!="&nbsp;" ) ){
  
     if ($curWeeks==""){
          $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
          $curWeeks=$dateResult["CurWeek"];
     }



     if($DeliveryWeek!="" && $DeliveryWeek!="&nbsp;" && ($DeliveryWeek>0 && $DeliveryWeek<=$yearWeek) ){
         $toWeek =  $DeliveryWeek;
     }else{
          $DeliveryDate=str_replace("*", "", $DeliveryDate);
	      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$DeliveryDate',1) AS DeliveryWeek",$link_id));
          $toWeek=$dateResult["DeliveryWeek"];
     }
         
     if ($toWeek>0){
          $week=substr($toWeek, 4,2);
	      $dateArray= GetWeekToDate($toWeek,"m/d");
	      $weekName="Week " . $week;
	      $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
	      
	      $Delivery_Color=($toWeek<$curWeeks && $Delivery_NoColor==0)?"#FF0000" : "#000000";
	      $DeliveryWeek="<div style='color:$Delivery_Color;'>$weekName</div><div style='font-size:10px;color:#AAAAAA'>$dateSTR</div>";
	       $DeliveryWeek_span="<span style='color:$Delivery_Color;'>$weekName</span><span style='font-size:10px;color:#555555'>($dateSTR)</span>";
	      
      }else{
	      $DeliveryWeek = "<span class='redB'>未设置</span>";
      }
       
  }else{

	  $DeliveryWeek = "<span class='redB'>未设置</span>";
  }
?>
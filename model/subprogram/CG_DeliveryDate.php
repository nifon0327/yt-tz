 <?php  
      //采购交期转换成周数显示
      $DeliveryDateShow="<span class='yellowN' style='vertical-align:middle;'>未设置</span>"; 
      if ($DeliveryDate!="" && $DeliveryDate!="0000-00-00" ){
         if ($curWeeks==""){
	          $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
              $curWeeks=$dateResult["CurWeek"];
         }
	      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$DeliveryDate',1) AS Week",$link_id));
          $CGWeek=$dateResult["Week"];
    
         if ($CGWeek>0){
	          $week=substr($CGWeek, 4,2);
		      $dateArray= GetWeekToDate($CGWeek,"m/d");
		      $weekName="Week " . $week;
		      $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
		      
		      $week_Color=$CGWeek<$curWeeks ?"#FF0000":"#000000";
		      $week_Color=$FactualQty+$AddQty==$rkQty?"#339900":$week_Color;
		      if ($DateShow_Style==1){
			      $DeliveryDate="<div style='color:$week_Color;' title='$dateSTR'>$weekName</div>";
		      }
		      else{
		        $DeliveryDateShow="<div style='color:$week_Color; text-align:center;'>$weekName<br><span style='font-size:10px;color:#AAAAAA'>$dateSTR</span></div>";
		      }
	      }
      }
?>
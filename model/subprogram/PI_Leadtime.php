 <?php  
      //PI交期转换成周数显示
     $weekName="";
      if ($Leadtime!="" && $Leadtime!="&nbsp;" ){
         if ($curWeeks==""){
	          $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
              $curWeeks=$dateResult["CurWeek"];
         }
	      $Leadtime=str_replace("*", "", $Leadtime);
	      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$Leadtime',1) AS PIWeek",$link_id));
          $PIWeek=$dateResult["PIWeek"];
    
         if ($PIWeek>0){
	          $week=substr($PIWeek, 4,2);
		      $dateArray= GetWeekToDate($PIWeek,"m/d");
		      $weekName="Week " . $week;
		      $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
		      
		      $PI_Color=($PIWeek<$curWeeks && $PI_NoColor==0)?"#FF0000":"#000000";
		      $Leadtime="<div style='color:$PI_Color;'>$weekName</div><div style='font-size:10px;color:#AAAAAA'>$dateSTR</div>";
	      }
      }
?>
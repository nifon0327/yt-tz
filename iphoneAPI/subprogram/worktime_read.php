<?php 
      //读取上班工作时间
     $curTime=strtotime(date("Y-m-d H:i:s"));
     $workTime1=strtotime(date("Y-m-d") . " 08:00:00");
     $workTime2=strtotime(date("Y-m-d") . " 12:00:00");
     $workTime3=strtotime(date("Y-m-d") . " 13:00:00");
     $workTime4=strtotime(date("Y-m-d") . " 17:00:00");
     $workTime5=strtotime(date("Y-m-d") . " 18:00:00");
     $workTime6=strtotime(date("Y-m-d") . " 22:00:00");
     
     $htimes=$curTime-$workTime1;
     $hour = floor($htimes/3600);
     $dtimes = $htimes % 3600;
     $minute = floor($dtimes/60);
     
     if ($curTime<$workTime1){
	         $hour=0;$minute=0;
     }
     else{
		     if ($curTime>$workTime6){
			     $hour=12;$minute=0;
		     }
		     else{
			      if ($curTime>$workTime5){
			          $hour-=2;
			      }
			      else{
				      if ($curTime>$workTime4){
			                   $hour=8;$minute=0;
			          }
			          else{
				            if ($curTime>$workTime3){
			                     $hour-=1;
			               }
			               else{
				                if ($curTime>$workTime2){
			                         $hour=4;$minute=0;
			                    }
			               }
			          }
			      }
		     }
     }
     $hour=$hour<10?"0" . $hour:$hour;
     $minute =$minute<10?"0" . $minute:$minute;
     $workTimes=$hour . ":" . $minute;
?>
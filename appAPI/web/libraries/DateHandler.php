<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  DateHandler{  
	
//时长显示
//  $time = $this->datehandler->GetTimeInterval($minutes);
public function GetTimeInterval($seconds=0)
{
    $returnValue="";
    $minutes = $seconds / 60;
	$hours=floor($minutes/60);
    $days=floor($hours/24);
      
    if ($days>0){
    	$returnValue=$days . "天";
    }
      else{
          if ($hours>0){
               $returnValue=$hours>0?$hours . "时":"";
           }
           else{
                $minutes=$minutes-$hours*60-$days*24;
                $minutes=$minutes<=0?1:$minutes;
	            $returnValue=round($minutes) . "分";
           }
      }    
	   $returnValue=$returnValue==""?" ":$returnValue;
	   return $returnValue;
}
	
public function geDifferDateTimeNum($time1,$time2,$sign=1)
{
       $returnValue=0;
	   if ($time2=="") $time2=date("Y-m-d H:i:s");
	   switch($sign){
		   case 3: //天数
		        $returnValue=floor((strtotime($time2)-strtotime($time1))/60); 
		       break;
		   case 1: //小时
		         $returnValue=floor((strtotime($time2)-strtotime($time1))/3600); 
		       break;
		    case 2: //天数
		        $returnValue=floor((strtotime($time2)-strtotime($time1))/86400); 
		       break;
	   }
	   return $returnValue;
}



public function get_worktimes() {
	
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
      $hour = $hour + $minute/60;
	return  array($workTimes,$hour,$minute);
}
		
function GetSecTimeOutString($time1,$time2,$sign=0)
{
       $returnValue="";
	   if ($time2=="") $time2=date("Y-m-d H:i:s");
	   switch($sign){
	       case 3://英文
	              $minutes=floor((strtotime($time2)-strtotime($time1))/60); 
		          $hours=floor($minutes/60);
		          $days=floor($hours/24);
		          
		          if ($days>0){
			           $returnValue=$days==1?" yesterday":$days . " days ago";
		          }
		          else{
		              if ($hours>0){
			               $returnValue=$hours>0?$hours . " hours ago":"";
		               }
		               else{
			                $minutes=$minutes-$hours*60-$days*24;
			                $minutes=$minutes<=0?1:$minutes;
				            $returnValue=$minutes . " minutes ago";
		               }
		          }
	           break;
		   default://分钟
		   		 $seconds = (strtotime($time2)-strtotime($time1));
		          $minutes=floor($seconds/60); 
		          $hours=floor($minutes/60);
		          $days=floor($hours/24);
		          
		          if ($days>0){
			           $returnValue=$days==1?"昨天":$days . "天前";
		          }
		          else{
		              if ($hours>0){
			               $returnValue=$hours>0?$hours . "时前":"";
		               } else if ($minutes >0) {
			               $minutes=$minutes-$hours*60-$days*24;
			                $minutes=$minutes<=0?1:$minutes;
				            $returnValue=$minutes . "分前";
		               }
		               else{
			               $seconds = $seconds>0?$seconds:1;
			                $returnValue=$seconds . "秒前";
		               }
		          }    
		       break;
	   }
	   $returnValue=$returnValue==""?" ":$returnValue;
	   return $returnValue;
}

//时长显示
public function GetDateTimeOutString($time1,$time2,$sign=0)
{
       $returnValue="";
	   if ($time2=="") $time2=date("Y-m-d H:i:s");
	   switch($sign){
	       case 3://英文
	              $minutes=floor((strtotime($time2)-strtotime($time1))/60); 
		          $hours=floor($minutes/60);
		          $days=floor($hours/24);
		          
		          if ($days>0){
			           $returnValue=$days==1?" yesterday":$days . " days ago";
		          }
		          else{
		              if ($hours>0){
			               $returnValue=$hours>0?$hours . " hours ago":"";
		               }
		               else{
			                $minutes=$minutes-$hours*60-$days*24;
			                $minutes=$minutes<=0?1:$minutes;
				            $returnValue=$minutes . " minutes ago";
		               }
		          }
	           break;
		   default://分钟
		          $minutes=floor((strtotime($time2)-strtotime($time1))/60); 
		          $hours=floor($minutes/60);
		          $days=floor($hours/24);
		          
		          if ($days>0){
			           $returnValue=$days==1?"昨天":$days . "天前";
		          }
		          else{
		              if ($hours>0){
			               $returnValue=$hours>0?$hours . "时前":"";
		               }
		               else{
			                $minutes=$minutes-$hours*60-$days*24;
			                $minutes=$minutes<=0?1:$minutes;
				            $returnValue=$minutes . "分前";
		               }
		          }    
		       break;
	   }
	   $returnValue=$returnValue==""?" ":$returnValue;
	   return $returnValue;
}

 }
?>

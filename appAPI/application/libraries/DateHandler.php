<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  DateHandler{  
	
	//获取第几周的开始、结束时间
   public function getWeekToDate($Weeks,$dateFormat='m/d')
	{
	       $year=substr($Weeks, 0,4);
		   $week=substr($Weeks, 4,2);
		   
		   $timestamp = mktime(1,0,0,1,1,$year);
	       $firstday = date("N",$timestamp);
	       if($firstday >4)
	          $firstweek = strtotime('+'.(8-$firstday).' days', $timestamp);
	       else
	           $firstweek = strtotime('-'.($firstday-1).' days', $timestamp);
	    
	        $monday = strtotime('+'.($week - 1).' week', $firstweek);
	        $sunday = strtotime('+6 days', $monday);
	        
	        $start = date("$dateFormat", $monday);
	        $end   = date("$dateFormat", $sunday);
	        return $start . '-' . $end;
	}
	
	//获取第几周的开始、结束时间
   public function getWeekToDate_getdate($Weeks,$dateFormat='m/d')
	{
	       $year=substr($Weeks, 0,4);
		   $week=substr($Weeks, 4,2);
		   
		   $timestamp = mktime(1,0,0,1,1,$year);
	       $firstday = date("N",$timestamp);
	       if($firstday >4)
	          $firstweek = strtotime('+'.(8-$firstday).' days', $timestamp);
	       else
	           $firstweek = strtotime('-'.($firstday-1).' days', $timestamp);
	    
	        $monday = strtotime('+'.($week - 1).' week', $firstweek);
	        $sunday = strtotime('+6 days', $monday);
	        
	        $start = date("$dateFormat", $monday);
	        $end   = date("$dateFormat", $sunday);
	        
	        $iddate =  date("Y-m-d", strtotime('+4 days', $monday));
	        return array('title'=>$start . '-' . $end,'id'=>$iddate);
	}
	
		//取得相差月份数
public function getDifferMonthNum( $date1, $date2, $tags="-" )
{
        $date1 = explode($tags,$date1);
        $date2 = explode($tags,$date2);
        
        if (count($date1)>1 &&count($date2)>1 )
        
        return ($date1[0]-$date2[0]) * 12 + $date1[1]-$date2[1];
        
        
        return 0;
 }
 
 
//时长显示
//  $time = $this->datehandler->GetTimeInterval($minutes);
public function GetTimeInterval($seconds=0, $sign=0)
{
    $returnValue="";
    $minutes = $seconds / 60;
	$hours=floor($minutes/60);
    $days=floor($hours/24);
      
    if ($days>0){
    	$returnValue=$days . ($sign==1?'d': "天");
    }
      else{
          if ($hours>0){
               $returnValue=$hours>0?$hours .  ($sign==1?'h': "时"):"";
           }
           else{
                $minutes=$minutes-$hours*60-$days*24;
                $minutes=$minutes<=0?1:$minutes;
	            $returnValue=round($minutes) . ($sign==1?'min': "分");
           }
      }    
	   $returnValue=$returnValue==""?" ":$returnValue;
	   return $returnValue;
}

public function getDateTimeInterval($time1,$time2,$sign=1)
{
    return $this->geDifferDateTimeNum($time1,$time2,$sign);
}

public function geDifferDateTimeNum($time1,$time2,$sign=1)
{
       $returnValue=0;
	   if ($time2=="") $time2=date("Y-m-d H:i:s");
	   switch($sign){
		   case 3: //分钟
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
	return  array($workTimes,$hour,$minute,$workTime2,$workTime4);
}
		
		
		
function timediff_arr( $begin_time, $end_time )
{
$begin_time = strtotime($begin_time);
$end_time = strtotime($end_time);
  if ( $begin_time < $end_time ) {
    $starttime = $begin_time;
    $endtime = $end_time;
  } else {
    $starttime = $end_time;
    $endtime = $begin_time;
  }
  $timediff = $endtime - $starttime;
  $days = intval( $timediff / 86400 );
  $remain = $timediff % 86400;
  $hours = intval( $remain / 3600 );
  $remain = $remain % 3600;
  $mins = intval( $remain / 60 );
  $secs = $remain % 60;
  $res = array( "day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs );
  return $res;
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

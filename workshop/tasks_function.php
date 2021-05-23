<?php 
//备料时间时长显示
function GetDateTimeOutString($time1,$time2,$sign=0){
       $returnValue="";
       if ($time2=="") $time2=date("Y-m-d H:i:s");
       $minutes=floor((strtotime($time2)-strtotime($time1))/60); 
       $hours=floor($minutes/60);
       $days=floor($hours/24);

       //echo $hours.'  '.$days.'<br>';
       switch($sign){
           case 3://英文
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
          case 1://小时
              if ($hours>0){
                           $returnValue=$hours>0?$hours . "小时前":"";
                       }
                       else{
                            $minutes=$minutes-$hours*60;
                            $minutes=$minutes<=0?1:$minutes;
                            $returnValue=$minutes . "分钟前";
                       }
              break;
           default://分钟
                  if ($days>0){
                       $returnValue=$days==1?"昨天":$days . "天前";
                  }
                  else{
                      if ($hours>0){

                           $returnValue=$hours>0?$hours . "小时前":"";
                       }
                       else{
                            $minutes=$minutes-$hours*60-$days*24;
                            $minutes=$minutes<=0?1:$minutes;
                            $returnValue=$minutes . "分钟前";
                       }
                  }    
               break;
       }
       $returnValue=$returnValue==""?" ":$returnValue;
       return $returnValue;
}

//读取用户端IP
function GetIP(){ 
    if(getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) 
        $ip = getenv("HTTP_CLIENT_IP"); 
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) 
            $ip = getenv("HTTP_X_FORWARDED_FOR"); 
        else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) 
                $ip = getenv("REMOTE_ADDR"); 
            else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) 
                    $ip = $_SERVER['REMOTE_ADDR']; 
                else 
                    $ip = "unknown"; 
                    return($ip); 
}


function socket_send_msg($host,$port,$msg='reload'){
    set_time_limit(0);
    
    $fp = stream_socket_client("tcp://$host:$port", $errno, $errstr, 30,STREAM_CLIENT_CONNECT);  
    if (!$fp) {  
            //fclose($fp); 
            return "$errstr ($errno)";  
    } 
    else { 
            $msg="reload"; 
            $status=fwrite($fp, "reload",strlen($msg));
            fclose($fp); 
            return  $status;
     }
}
?>
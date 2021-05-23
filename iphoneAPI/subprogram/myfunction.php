<?php 
	
	
	
function num2rmb($num){
	$c1="零壹贰叁肆伍陆柒捌玖"; 	
    $c2="分角元拾佰仟万拾佰仟亿";  
	$step=3;				//注意：UTF格式是，中文占3个字符,GB2312为2个字符
    $num=round($num,2);	
    $num=$num*100;  
    if(strlen($num)>10){  
		return  "oh,sorry,the  number  is  too  long!";  
     	}         
	$i=0;  
    $c="";   
	while(1){  
       if($i==0){  
    	   $n=substr($num,strlen($num)-1,1);}
		else{  
             $n=$num  %10;}  
		 
        $p1=substr($c1,$step*$n,$step);$p2=substr($c2,$step*$i,$step);  
        if($n!='0'||($n=='0'&&($p2=='亿'||$p2=='万'||$p2=='元'))){    
			$c=$p1.$p2.$c;}
		else{  
         	$c=$p1.$c;}
		$i=$i+1;  
        $num=$num/10;  
        $num=(int)$num;               
        if($num==0){  
			break;}
		}
	$j  =  0; 
    $slen=strlen($c);  
    while($j<  $slen){  
		$m = substr($c,$j,$step*2);         
		if($m=='零元'||$m=='零万'||$m=='零亿'||$m=='零零'){  
			$left=substr($c,0,$j);  
            $right=substr($c,$j+$step);      
            $c  =  $left.$right;            
            $j  =  $j-$step;  
            $slen  =  $slen-$step;    
            }        
		$j=$j+$step;  
        }  
           
	if(substr($c,strlen($c)-$step,$step)=='零'){  
    	$c=substr($c,0,strlen($c)-$step);}
	return  $c."整";
}


//取得相差月份数
function getDifferMonthNum( $date1, $date2, $tags="-" )
{
        $date1 = explode($tags,$date1);
        $date2 = explode($tags,$date2);
        return ($date1[0]-$date2[0]) * 12 + $date1[1]-$date2[1];
 }

//取得两个时间相差数
function geDifferDateTimeNum($time1,$time2,$sign=1)
{
       $returnValue=0;
	   if ($time2=="") $time2=date("Y-m-d H:i:s");
	   switch($sign){
		   case 1: //小时
		         $returnValue=floor((strtotime($time2)-strtotime($time1))/3600); 
		       break;
		    case 2: //天数
		        $returnValue=floor((strtotime($time2)-strtotime($time1))/86400); 
		       break;
	   }
	   return $returnValue;
}

//审核时间时长显示
function GetDateTimeOutString($time1,$time2,$sign=0,$base='前')
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
			           $returnValue=$days==1?"1天":$days . "天$base";
		          }
		          else{
		              if ($hours>0){
			               $returnValue=$hours>0?$hours . "小时$base":"";
		               }
		               else{
			                $minutes=$minutes-$hours*60-$days*24;
			                $minutes=$minutes<=0?1:$minutes;
				            $returnValue=$minutes . "分钟$base";
		               }
		          }    
		       break;
	   }
	   $returnValue=$returnValue==""?" ":$returnValue;
	   return $returnValue;
}


//获取第几周的开始、结束时间
function GetWeekToDate($Weeks,$dateFormat)
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
        return array($start, $end);
}

function GetWeekDate($week,$year,$dateFormat)
 {
      $timestamp = mktime(0,0,0,1,1,$year);
      $dayofweek = date("w",$timestamp); 
      $distance = $week == 1?0:($week-1)*7-$dayofweek;
     //$distance=$week*7-$dayofweek;
      $passed_seconds = $distance * 86400;
      $timestamp += $passed_seconds;  
      $firt_date_of_week = date("$dateFormat",$timestamp);
      //$distance =6;
      $distance =$week == 1?6-$dayofweek:6;
      $timestamp += $distance * 86400;
      $last_date_of_week = date("$dateFormat",$timestamp);
      
      return array($firt_date_of_week,$last_date_of_week);
 }
 
 function getWorkLimitedTime($Id=0,$ModelId,$DataIn,$link_id)
 {
         $returnArray=array(4,"4h","");
		 $SearchRows=$Id==0?"  ModuleId='$ModelId'":"  Id='$Id' ";
		 
		 $Result=mysql_query("SELECT Timeout,Unit,Auditor  FROM $DataIn.worktime_limited WHERE $SearchRows ",$link_id);
		 if($myRow = mysql_fetch_array($Result)){
			  $TimeOut=$myRow["Timeout"];
			  $Operator=$myRow["Auditor"];
			  
			  if ($Operator>0){
					 $pResult = mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number='$Operator'  LIMIT 1",$link_id);
					if($pRow = mysql_fetch_array($pResult)){
						   $Operator=$pRow["Name"];
					}
			 }
			 else{
				 $Operator="";
			 }

			  switch($myRow["Unit"]){
				  case 1://小时
				      $returnArray=array($TimeOut,$TimeOut ."h",$Operator);
				      break;
				  case 2://天数
				      $returnArray=array($TimeOut,$TimeOut ."days",$Operator);
				   break;   
			  }
		  }
	  return   $returnArray;
 }
 
 function getCgOperateDate($StockId,$Opcode,$DataIn,$link_id)
 {
     $returnArray=array();
     $DataPublic=$DataIn=='ac'?$DataIn:"d0";
     $Result=mysql_query("SELECT IFNULL(S.created,S.Date) AS Date,M.Name FROM $DataIn.cg1_stocksheet_log S 
           LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Operator 
           WHERE S.Opcode='$Opcode' AND S.StockId='$StockId' ORDER BY Date DESC LIMIT 1",$link_id);
		 if($myRow = mysql_fetch_array($Result)){
		        $returnArray=array("Date"=>$myRow["Date"],"Operator"=>$myRow["Name"]);
		 }
	  return $returnArray;
 }
 
 function get_semifinished_relation($StuffId,$DataIn,$link_id,&$IdList,$depth)
{
			$checkResult=mysql_query("SELECT mStuffId AS mStuffId 
	             FROM $DataIn.semifinished_bom WHERE StuffId IN ($StuffId) GROUP BY mStuffId",$link_id);
	         while($checkRow=mysql_fetch_array($checkResult)) {       
                    $mStuffId = $checkRow['mStuffId'];
				      if (!in_array($mStuffId, $IdList)){
					        $IdList[]=$mStuffId;  
					        if ($depth<10){
								  get_semifinished_relation($mStuffId,$DataIn,$link_id,$IdList,$depth+1);
							 }
				      }
			   }
   }
    
 
function versionToNumber($version){
	  return $version==""?0:str_replace(".", "", $version);
 }
 
 /*
	 add by 思芸，20141113, iphone用的加密方法
 */
 //加密开始
//随机码（载体）
$ReferenceMark="abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$motherSTR[] = array();
for($i=0;$i<32;$i++){
	$motherSTR[$i]=$ReferenceMark[rand(0,60)];
	}
//渗透序号码(不变)
$SinkOrder="xacdefghijklmbnopqrstuvwyz";	
function anmaIn($oldStr,$SinkOrder,$motherSTR){	
	$len = strlen($oldStr);
	//渗透过程
	$SinkOrderTemp=$SinkOrder;
	$RuleStr="";
	for($i=0;$i<$len;$i++){	
		$tl= strlen($SinkOrderTemp)-1;					//渗透码长度
		$DisturbChar=rand(0,9);							//随机干扰码(数字0-9)
		$inChar=$SinkOrderTemp[rand(0,$tl)];			//渗透码字母
		if ($inChar!="")
		    $inNum=strpos($SinkOrder,$inChar);				//将 渗透码字母 转为数字
		$motherSTR[$inNum]=substr($oldStr,$i,1);		//原文字符替代随机码某位置的字符
		$RuleStr.=$DisturbChar.$inChar;					//完整渗透码
		$SinkOrderTemp = str_replace($inChar, "",$SinkOrderTemp);//新的渗透序号码
		}
	//渗透结果
	$EncryptStr="";
	for($i=0;$i<32;$i++){
		$EncryptStr.=$motherSTR[$i];
		}
	$reValue=$RuleStr."|".$EncryptStr;		
	return $reValue;
	}
//加密结束 



function do_hash($string, $salt = NULL)
{
       
		if(null === $salt){
		    $salt = substr(md5(uniqid(rand(), true)), 0, 32);
		}
		else{
		    $salt = substr($salt, 0, 32);
		}	    
       return $salt . sha1($salt . $string);
}
?>


<?php 
include_once "../basic/parameter.inc";
$startDay = $chooseMonth."-01";
$endDay = $EndDay=date("Y-m-t",strtotime($startDay));

$pbSql = "Select A.pbSign, A.KqSign, B.DTimeIn, B.DTimeOut From $DataPublic.staffmain A
		  Left Join $DataPublic.pbsheet B On B.Id = A.pbSign
		  Where A.Number = '$Number'";
$pbResult = mysql_query($pbSql);
$pbRow = mysql_fetch_assoc($pbResult);
$timeIn = $pbRow["DTimeIn"];
$timeOut = $pbRow["DTimeOut"];

$qjResult=mysql_query("SELECT StartDate,EndDate,bcType FROM $DataPublic.kqqjsheet 
					   WHERE Number='$Number' 
					   AND (
					   		(StartDate LIKE  '$chooseMonth%' OR EndDate LIKE  '$chooseMonth%')
					   		OR (StartDate <  '$startDay' AND EndDate >  '$endDay')
					   )",$link_id);
if($qjRow=mysql_fetch_array($qjResult)){
		   do{
		     $bcType=$qjRow["bcType"];
		     
		     $StartDate=$qjRow["StartDate"];
			 $EndDate=$qjRow["EndDate"];
			 
			 if($StartDate <= $endDay && $EndDate > $endDay)
			 {
				 $EndDate = $endDay." $timeOut";
			 }
			 
			 if($EndDate >= $startDay && $StartDate < $startDay)
			 {
				 $StartDate = $startDay." $timeIn";
			 }
			 
			 $HoursTemp=abs(strtotime($StartDate)-strtotime($EndDate))/3600;//向上取整
			 $Days=intval($HoursTemp/24);
			 $HolidayTemp=0;$isHolday=0;
			 $DateTemp=$StartDate;
			 $DateTemp=date("Y-m-d",strtotime("$DateTemp-1 days"));
			 for($n=0;$n<=$Days;$n++){
				$isHolday=0;  //0 表示工作日
				$DateTemp=date("Y-m-d",strtotime("$DateTemp+1 days"));
				$weekDay=date("w",strtotime("$DateTemp"));	 
				if($weekDay==6 || $weekDay==0){
					$HolidayTemp=$HolidayTemp+1;
					$isHolday=1;
					}
				else{
					//读取假日设定表
					$holiday_Result = mysql_query("SELECT * FROM $DataPublic.kqholiday WHERE 1 and Date=\"$DateTemp\"",$link_id);
					if($holiday_Row = mysql_fetch_array($holiday_Result)){
						$HolidayTemp=$HolidayTemp+1;
						$isHolday=1;
						}
					}
                
				//分析是否有工作日对调
				if($isHolday==1){  //节假日上班，所以其休息时间要减
					$kqrqdd_Result = mysql_query("SELECT XDate FROM $DataIn.kqrqdd WHERE XDate='$DateTemp' AND  Number='$Number'",$link_id);
					if($kqrqdd_Row = mysql_fetch_array($kqrqdd_Result)){
							$HolidayTemp=$HolidayTemp-1;
					   }				
				    }			
				
				else{  //非节假日调班，则其休息时间要加,
					$kqrqdd_Result = mysql_query("SELECT XDate FROM $DataIn.kqrqdd WHERE GDate='$DateTemp' AND  Number='$Number'",$link_id);
					if($kqrqdd_Row = mysql_fetch_array($kqrqdd_Result)){
							$HolidayTemp=$HolidayTemp+1;
					      }
			         }
               
			 }//endfor
			//计算请假工时
			$Hours=($HoursTemp*10)%(24*10)/10;//求余取相隔小时数
			if($bcType==0){
				$Hours=$Hours>4?($Hours<=9?$Hours-1:8):$Hours;//相隔小时数的实际工时
				}
			$HourTotal=$Days*8-$HolidayTemp*8+$Hours;//总工时 
			$HourTotal=$HourTotal<0?0:$HourTotal;
			    
		    }while($qjRow=mysql_fetch_array($qjResult));		
		}
		
		$qjTotleDay = round($HourTotal/8.0, 2);
?>
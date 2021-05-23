<?php 
 $qjcolor="&nbsp;";
$today=date("Y-m-d");
$qjResult=mysql_query("SELECT StartDate,EndDate,bcType FROM $DataPublic.kqqjsheet WHERE Number='$Number' AND EndDate>='$today'",$link_id);
//echo "SELECT StartDate,EndDate,bcType FROM $DataPublic.kqqjsheet WHERE Number='$Number' AND EndDate>='$today' <br>";
if($qjRow=mysql_fetch_array($qjResult)){
$SumHourTotal=0;
		   do{
		     $bcType=$qjRow["bcType"];
		     $StartDate=$qjRow["StartDate"];
			 $EndDate=$qjRow["EndDate"];
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
			//echo "$HoursTemp <br>";
			$Hours=($HoursTemp*10)%(24*10)/10;//求余取相隔小时数
			if($bcType==0){
				$Hours=$Hours>4?($Hours<=9?$Hours-1:8):$Hours;//相隔小时数的实际工时
				}
			$HourTotal=$Days*8-$HolidayTemp*8+$Hours;//总工时 
			$HourTotal=$HourTotal<0?0:$HourTotal;
            $SumHourTotal+=$HourTotal;
		    }while($qjRow=mysql_fetch_array($qjResult));
			//echo "<br> $SumHourTotal";
		     if($SumHourTotal>=105){//超过15天颜色显示
			     $qjcolor="style='background:#FF0000' title='请假超过半个月'";
			     }
		      else{
				 $qjcolor="style='background:#FF00FF' title='请假中...'"; 
			  }
		}
?>
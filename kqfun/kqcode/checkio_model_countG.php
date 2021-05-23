<?php 
//电信-ZX  2012-08-01
//$DataPublic.kqqjsheet 二合一已更新
if($AI=="" || $AO==""){//任何一个为空，皆为缺勤
	$WorkTime=0;
	$aiTime="";
	$aoTime="";
	}
else{//有上下班签到的情况:总共18种情况
	if($AI<=date("Y-m-d H:i:00",strtotime("$dDateTimeIn + $dInLate minute"))){	//A:签到时间少于或等于预设的签到时间：即上班签到正常;下班有五种情况
		//A答到在8:00之前；情况在1、2、3、4、5
		include "kqcode/checkio_model_GA.php";
		
		 //echo "1";

		}
	else{
		if($AI<$dRestTime1){
			//B:签到在8:00-12:00；情况6、7、8、9、10
			include "kqcode/checkio_model_GB.php";
			//echo "2";
			}
		else{
			if($AI<$dRestTime2){
				//C:签到在12:00-13:00；情况11、12、13
				include "kqcode/checkio_model_GC.php";
				//echo "3";
				}
			else{
				if($AI<$dDateTimeOut){
					//D:签到在13:00-17:00；情况14、15、16
					include "kqcode/checkio_model_GD.php";
					//echo "4";
					}
				else{
					if($AI<$dRestTime3){
						//E:签到在17:00-18:00；情况17
						include "kqcode/checkio_model_GE.php";
						//echo "5";
						}
					else{
						//F:签到在18:00之后:情况18
						$GJTime=abs(strtotime(rounding_out($AO))-strtotime(rounding_in($AI)))/3600;
						echo "GJTime:$GJTime";
						$test="E";
						if($KrSign==1){		//情况6：如果跨日签退
							$AOcolor="class='greenB'";
							$YBs=1;
							$test="F";
							}
						}
					}
				}
			}
		}
	}
//是否需要检查请假
if($WorkTime<8){//检查请假记录
	//检查有没有请假
	//$qjHours=0;
	$qjHours_sum=0;
	$qjSTemp=$dDateTimeIn;
	$qjETemp=$dDateTimeOut;
	$test="";
	//条件：考勤当天$CheckDate在请假起始日期~请假结束日期之间
	$qjResult1 = mysql_query("SELECT StartDate,EndDate,Type FROM $DataPublic.kqqjsheet WHERE Number=$Number and ('$CheckDate' between left(StartDate,10) and left(EndDate,10))",$link_id);
	if($qjRow1=mysql_fetch_array($qjResult1)){//有请假
	do{
		$qjHours=0;
		$StartDate=$qjRow1["StartDate"];
		$EndDate=$qjRow1["EndDate"];
		$qjType=$qjRow1["Type"];
		$test=$StartDate."-".$EndDate;
		//请假情况分析，总共12种情况
		if($StartDate<=$dDateTimeIn){				//请假的起始时间<=8:00
			include "kqcode/checkio_model_qjA.php";
			}
		else{
			if($StartDate<$dRestTime1){				//请假的起始时间<=12:00
				include "kqcode/checkio_model_qjB.php";
				}
			else{
				if($StartDate<$dRestTime2){			//请假的起始时间<13:00
					include "kqcode/checkio_model_qjC.php";
					}
				else{								//请假的起始时间在13:00之后
					
					include "kqcode/checkio_model_qjD.php";
					}
				}
			}
		$QjTimeTemp="QjTime".strval($qjType); 
                
		$qjHours_sum=$qjHours_sum+$qjHours;
                 //三八妇女节放假
                 $WomensDay=1;
                 include "kqcode/checkio_Womens_Day.php";
		 $$QjTimeTemp+=$qjHours; //=$qjHours
	  }while($qjRow1=mysql_fetch_array($qjResult1));
          
	    $QQTime=8-$WorkTime-$qjHours_sum;
            //三八妇女节放假
            $WomensDay=2;
            include "kqcode/checkio_Womens_Day.php";
            
	    $QQTime=$QQTime<"0"?"0":$QQTime;
		//$QQTime=8-$WorkTime-$qjHours;
		if($QQTime==0){//如果没有缺勤，则不计算迟到早退
			$InLates=0;
			$OutEarlys=0;
			}
		else{
			if($QQTime<1){//缺勤时间在0.5小时内
				$QQTime=0;
				}
			else{//如果缺勤时间大于0.5小时,则按工时扣款，但不计算次数
				$InLates=0;
				$OutEarlys=0;
				}
		}
		//$QjTimeTemp="QjTime".strval($qjType); 
		//$$QjTimeTemp=$qjHours;
		}
	else{//没有请假计算缺勤；
		$QQTime=8-$WorkTime;
                //三八妇女节放假
                $WomensDay=2;
                include "kqcode/checkio_Womens_Day.php";
                
		if($QQTime==8){
			$QQTime=0;
			$KGTime=8;
			}
		else{
                    if ($QQTime==4 && $GTime==4){
                        $QQTime=0;
			$KGTime=4;
                       }
                    else{
			if($QQTime<1){//缺勤时间在0.5小时内
				$QQTime=0;
				}
			else{//如果缺勤时间大于0.5小时,则按工时扣款，但不计算次数
				$InLates=0;
				$OutEarlys=0;
				}
			}
                     }
		}
	}
//以上计算出当天请假工时、实到工时、加点工时、迟到次数、早退次数、夜班数、缺勤工时
?>
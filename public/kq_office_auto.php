<?php
include "D:/website/mc/basic/parameter.inc";

function maketime($date){
list($year,$month,$day) = explode('-',$date);
return mktime(0,0,0,$month,$day,$year);
}

if($CheckDate == "")
{
$toDay=date("Y-m-d");
}
else
{
$toDay=$CheckDate;
}

$FristDay=date("Y-m-d",strtotime("$toDay - 2 days"));
//$FristDay="2013-06-10";//要加入记录的月份
$CheckMonth=substr($FristDay,0,7);//要处理的月份
$nowMonth=date("Y-m");//当前系统月份
$NumberResult=mysql_query("SELECT Number FROM $DataPublic.staffmain WHERE KqSign='3' AND cSign='7' AND Estate>0 AND Number!='10001'",$link_id);
if($NumberRow=mysql_fetch_assoc($NumberResult)){
do{
      $thisNumber[]=$NumberRow;
      }while($NumberRow=mysql_fetch_assoc($NumberResult));
}
$Nums=count($thisNumber);
if (count($Nums)>0 ){
           $days=round((strtotime($toDay)-strtotime($FristDay))/3600/24)+1 ;
           for($i=0;$i<$days;$i++){
                     $theDay=date("Y-m-d",strtotime("$FristDay + $i days"));
                 //******************************************1、给人员排个序
                    for($j=0;$j<$Nums;$j++){
                             $TempNumber=$thisNumber[$j]["Number"];
                             $CheckResult1=mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.kq_officenum WHERE Number='$TempNumber' AND Date='$theDay'",$link_id));
                             $CheckId=$CheckResult1["Id"];
                             if($CheckId==""){
                                      $SortId=rand(1,10000);
                                      $In_Sql="INSERT INTO $DataIn.kq_officenum(Id, Number, SortId, Date)VALUES(Null,'$TempNumber','$SortId','$theDay')";
                                      $In_Result=@mysql_query($In_Sql);
                                 }
                         }
                     //******************************************1、插入 考勤信息     
                     $weekDay=date("w",strtotime($theDay));
					 
					 $isHolday=0;
					 if($weekDay==6 || $weekDay==0){
							 $isHolday=1;
							 }
					 else{
						   //读取假日设定表
							  $holiday_Result = mysql_query("SELECT * FROM $DataPublic.kqholiday WHERE 1 and Date=\"$theDay\"",$link_id);
							  if($holiday_Row = mysql_fetch_array($holiday_Result)){
								$isHolday=1;
							}
					 }  
					 
                     //if($weekDay>0 && $weekDay<6){//工作日
					 if($weekDay>=0 ){//工作日,节假日？调班?请假
					 
                              /*
							  //分钟的随机数
                              $InMinute=mt_rand(45,59);
                              $OutMinute=mt_rand(30,50);
                              $InTime=$theDay." 07:".$InMinute.":00";//随机起始签到时间
                              $OutTime=$theDay." 17:".$OutMinute.":00";//随机起始签退时间
                              $InTime=strtotime($InTime);
                              $OutTime=strtotime($OutTime);
							  */
							  
                             $CheckResult2=mysql_query("SELECT K.Number,M.BranchId,M.JobId FROM $DataIn.kq_officenum  K
                             LEFT JOIN $DataPublic.staffmain M ON M.Number=K.Number
                             WHERE  K.Date='$theDay' ORDER BY K.SortId",$link_id);
                             while($CheckRow2=mysql_fetch_array($CheckResult2)){
                                        $Number=$CheckRow2["Number"];
										
										/*
 //分析是否有工作日对调
										 if($isHolday==1){  //节假日上班，所以其休息时间要减
											$kqrqdd_Result = mysql_query("SELECT XDate FROM $DataIn.kqrqdd WHERE XDate='$theDay' AND  Number='$Number'
																		  UNION 
																		  SELECT XDate FROM $DataOut.kqrqdd WHERE XDate='$theDay' AND  Number='$Number'
																		 ",$link_id);
											 if($kqrqdd_Row = mysql_fetch_array($kqrqdd_Result)){
												 $isHolday=0;
											  }				
										}	
										else{  //非节假日调班，则其休息时间要加,
												 $kqrqdd_Result = mysql_query("SELECT XDate FROM $DataIn.kqrqdd WHERE GDate='$theDay' AND  Number='$Number'
																			   UNION 
																			   SELECT XDate FROM $DataOut.kqrqdd WHERE GDate='$theDay' AND  Number='$Number'
																			  ",$link_id);
												  if($kqrqdd_Row = mysql_fetch_array($kqrqdd_Result)){
													 $isHolday=1;
												 }
											}					
*/					
										
										$qjSign=0;
										$qjTimeSql=mysql_query("SELECT StartDate,EndDate,bcType FROM $DataPublic.kqqjsheet WHERE Number='$Number'   AND  substring(StartDate,1,10)<='$theDay' AND substring(EndDate,1,10)>='$theDay' ",$link_id);
										//echo "SELECT StartDate,EndDate,bcType FROM $DataPublic.kqqjsheet WHERE Number=$Number  AND Type  IN (1,3) AND  (substring(StartDate,1,10)<='$theDay' OR substring(EndDate,1,10)>='$theDay' <br>";
                                        if($qjTimeRow=mysql_fetch_array($qjTimeSql)){//垮年
										        $qjSign=3;
												$StartDate=$qjTimeRow["StartDate"];
		       									$EndDate=$qjTimeRow["EndDate"];
			  									$qfirst_date=substr($StartDate,0,10);
			   									$qend_date=substr($EndDate,0,10);
												$qfirst_H=substr($StartDate,11,2);
												$qend_H=substr($EndDate,11,2);
												if($qend_date==$theDay && $qend_H<=12){  //上午请假了，
														$qjSign=1;
												}
												else {
													if($qfirst_date==$theDay  && $qfirst_H>=12 ){  //下午请假了，
														$qjSign=2;
														}
												}
												
												if($qend_date==$theDay && $qend_H>=17){  //请假结束时间，在考17点后，
														$qjSign=17;  //请假结束时间就是他的下班时间
												}
												else 
													if($qfirst_date==$theDay  && $qfirst_H<=8 ){  //请假在8点前
														$qjSign=8;   //请假结束时间就是他的上班时间
														}
												
												
																							
										}
										
 										if( $isHolday==1 ||  $qjSign==3){  //节假日，休息日,请假则跳过
											continue;
										}
										
										
                                        $BranchId=$CheckRow2["BranchId"];
                                        $JobId=$CheckRow2["JobId"];
                                        $CheckResult3=mysql_fetch_array(mysql_query("SELECT Id FROM $DataPublic.kqqjsheet WHERE Number='$Number' AND left(StartDate,12)<='$theDay' AND left(EndDate,12)>='$theDay'",$link_id));
                                        $CheckId3=$CheckResult3["Id"];
                                        if($CheckId3==""){
											  
											  //分钟的随机数
											  $InMinute=mt_rand(45,59);
											  $OutMinute=mt_rand(30,50);
											  $InTime=$theDay." 07:".$InMinute.":00";//随机起始签到时间
											  $OutTime=$theDay." 17:".$OutMinute.":00";//随机起始签退时间
											  $InTime=strtotime($InTime);
											  $OutTime=strtotime($OutTime);
											  											
                                              $InTime+=mt_rand(1,4);//考勤签到时间
                                              $OutTime+=mt_rand(1,4);//考勤签退时间
											  switch($qjSign){
												  case 0: //未请假
												  case 1: // modify by zx 2014-04-28 上午请假了,考勤记录都记一天，在日统记那里直接扣请假时长 
												  case 2: // modify by zx 2014-04-28 上午请假了,考勤记录都记一天，在日统记那里直接扣请假时长 
												    $CheckInTime=date('Y-m-d H:i:s',$InTime);
                                              		$CheckOutTime=date('Y-m-d H:i:s',$OutTime);
													break;
												  /*	
												  case 1: //上午请假了
												    $InMinute=mt_rand(15,30);
												    $InTime=$theDay." 13:".$InMinute.":00";//随机起始签到时间
													$InTime=strtotime($InTime);												    
												    $CheckInTime=date('Y-m-d H:i:s',$InTime);
                                              		$CheckOutTime=date('Y-m-d H:i:s',$OutTime);
													break;	
												  case 2: //下午请假了
												    $CheckInTime=date('Y-m-d H:i:s',$InTime);
													$OutMinute=mt_rand(0,15);
													$OutTime=$theDay." 12:".$OutMinute.":00";//随机起始签退时间
													$OutTime=strtotime($OutTime);													
                                              		$CheckOutTime=date('Y-m-d H:i:s',$OutTime);
													break;
													*/
												  case 8: // modify by zx 2014-04-28 上午8点前请假了,/请假结束时间就是他的上班时间 
												    $CheckInTime=date('Y-m-d H:i:s',$$StartDate);
                                              		$CheckOutTime=date('Y-m-d H:i:s',$OutTime);
													break;
												  case 17: // modify by zx 2014-04-28 请假结束时间，在考17点后，//请假结束时间就是他的下班时间 
												    $CheckInTime=date('Y-m-d H:i:s',$InTime);
                                              		$CheckOutTime=date('Y-m-d H:i:s',$EndDate);
													break;
													
												  default:
												    $CheckInTime='0000-00-00 00:00:00';
                                              		$CheckOutTime='0000-00-00 00:00:00';
												   break;
											  }
											  
											  											  
		                                      $CheckInNext= mysql_query("SELECT CheckTime,CheckType FROM $DataIn.kq_office WHERE 1 and Number=$Number and CheckType='I' AND 
LEFT(CheckTime,10)='$theDay' LIMIT 1",$link_id);
																				
	                                   	    if(!$CheckInNextRow = mysql_fetch_array($CheckInNext)){
	                                   	    	  
                                                  $In_Sql3="INSERT INTO $DataIn.kq_office(Id, BranchId, JobId, Number, CheckTime, CheckType, dFrom, Estate, Locks, ZlSign, KrSign, Operator) VALUES(NULL,'$BranchId','$JobId','$Number','$CheckInTime','I','0','0','1','0','0','0')";
                                                   $In_Result3=@mysql_query($In_Sql3);
                                                 }
                                                 
		                                       $CheckOutNext= mysql_query("SELECT CheckTime,CheckType FROM $DataIn.kq_office WHERE 1 and Number=$Number and CheckType='O' AND LEFT(CheckTime,10)='$theDay' LIMIT 1",$link_id);
                                              $d = (maketime($toDay) - maketime($theDay)) / (3600*24);
                                            if($d!=0){
	                                   	           if(!$CheckOutNextRow = mysql_fetch_array($CheckOutNext )){
                                                        $In_Sql4="INSERT INTO $DataIn.kq_office(Id, BranchId, JobId, Number, CheckTime, CheckType, dFrom, Estate, Locks, ZlSign, KrSign, Operator) VALUES(NULL,'$BranchId','$JobId','$Number','$CheckOutTime','O','0','0','1','0','0','0')";
                                                       $In_Result4=@mysql_query($In_Sql4);
                                                      }                   
                                                  }           
                                           }
                                  }
                       }
            }
}
?>
<?php 
 $Log_Item="请假登记";
        switch($ActionId){
            case "SAVE"://新增记录
                $Log_Funtion="保存";
                $TypeId = $info[0];$StartTime = $info[1];$EndTime = $info[2]; $Remark = $info[3];
                $bcType=0;
                $MonthTemp=substr($StartDate,0,7);
                
                $EnabledSign=0; //允许请假状态
                $Estate = 1;    //请假审核状态
                switch($TypeId){
                  case 4://请年假
                  
	                 $ipadTag = "yes";
	                 include "../../model/kq_YearHolday.php";
	                //检查年假天数
	                $ComeInResult=mysql_fetch_array(mysql_query("SELECT ComeIn FROM $DataPublic.staffmain 
					WHERE Number='$Operator'",$link_id));
					$ComeIn=$ComeInResult["ComeIn"];
					$startYear=date("Y-m-d",strtotime("1 year",strtotime($ComeIn)));
					$NowToday=date("Y-m-d");
					
					$chooseYear=date("Y");
					$NextYear=$chooseYear+1;
					$LastYear=$chooseYear-1;
					$mySql="SELECT M.Number,M.Name,M.BranchId,M.JobId,M.ComeIn,M.KqSign,B.Name AS Branch,J.Name AS Job,G.GroupName
						FROM $DataPublic.staffmain M
					    LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
						LEFT JOIN $DataPublic.branchdata B ON M.BranchId=B.Id
						LEFT JOIN $DataPublic.jobdata J ON M.JobId=J.Id
						WHERE 1 AND M.Estate=1 AND M.Number='$Operator'";
					$myResult = mysql_query($mySql,$link_id);
				    if($myRow = mysql_fetch_array($myResult)){
						$KqSign=$myRow["KqSign"];
						$Number=$myRow["Number"];
						$Name=$myRow["Name"];
						$Branch=$myRow["Branch"];
						$Job=$myRow["Job"];
						$ComeIn=$myRow["ComeIn"];
						$GroupName=$myRow["GroupName"];
						//入职当年
						$ComeInY=substr($ComeIn,0,4);
						//年份间隔:间隔在2以上的，全休，间隔1实计；间隔0无
						$ValueY=$chooseYear-$ComeInY;
								
						$DefaultLastM=$chooseYear."-12-01";
						$ThisEndDay=$chooseYear."-12-".(date("t",strtotime($DefaultLastM)));	//当年最后一天	
						$CountDays=date("z",strtotime($ThisEndDay));	//年假当年总天数
						
						
						//计算本年请假的时间(除年休)，超过15天以上的要扣除
						$sumQjTime=0;
						$qjTimeSql=mysql_query("SELECT StartDate,EndDate FROM $DataPublic.kqqjsheet WHERE Number='$Number'  AND Type NOT IN (4,8) and (substring(StartDate,1,4)='$LastYear' OR substring(EndDate,1,4)='$LastYear')",$link_id);
						if($qjTimeRow=mysql_fetch_array($qjTimeSql)){//垮年处理
						    do{
						       $StartDate=$qjTimeRow["StartDate"];
						       $EndDate=$qjTimeRow["EndDate"];
							   $frist_Year=substr($StartDate,0,4);
							   $end_Year=substr($EndDate,0,4);
							   if($frist_Year<$LastYear)$StartDate=$LastYear."-01-01 08:00:00";
							   if($end_Year>$chooseYear)$EndDate=$chooseYear."-12-31 17:00:00";
							   $qjTime=abs(intval((strtotime($EndDate)-strtotime($StartDate))/3600/24));
							   $sumQjTime+=$qjTime;
						       }while($qjTimeRow=mysql_fetch_array($qjTimeSql));
						    }
							if($sumQjTime<15)$sumQjTime=0;//少于15天忽略。
							//echo $sumQjTime;
							if($ValueY>1){	//年份间隔在2以上的
								$inDays=$CountDays-$sumQjTime;
								$AnnualLeave=intval((5*8*$inDays)/$CountDays);
								if($ValueY>=10){
									$AnnualLeave=intval((10*8*$inDays)/$CountDays);
									}
								if($ValueY>=20){
									$AnnualLeave=intval((15*8*$inDays)/$CountDays);
									}					
								}
							else{
								if($ValueY==1){
									$inDays=abs(strtotime($ThisEndDay)-strtotime($ComeIn))/3600/24-$CountDays-$sumQjTime;
									$AnnualLeave=intval((5*8*$inDays)/$CountDays);
									}
								else{
									$AnnualLeave=0;
									$inDays=0;
									}
								}	
						$qjAllDays=HaveYearHolDayDays($Number,$chooseYear."-01-01 00:00:00",$chooseYear."-01-01 00:00:00",$DataIn,$DataPublic,$link_id)/8 ;		
						$AnnualLeave1=intval($AnnualLeave/8);
						$LastDay=$AnnualLeave1-$qjAllDays;
					 }
					 
					    //当前请假天数
					    $HourTotal=GetBetweenDateDays($Operator,$StartTime,$EndTime,0,$DataIn,$DataPublic,$link_id);
						
						if ($LastDay>0 && $LastDay*8>=$HourTotal){
							$EnabledSign = 1;
						}
						$Estate=IsAuditHolDayDays($Operator,$StartTime,$EndTime,$DataIn,$DataPublic,$link_id);
						 
						$info="可休年假为$LastDay" . "天";
				     break;
				  case 5://请补休
				        $ipadTag = "yes";
				        $mySql="SELECT J.Id, J.Number, Sum(J.hours) as hours
								FROM $DataPublic.bxSheet J 
								LEFT JOIN $DataPublic.staffmain M ON J.Number=M.Number
								WHERE M.Estate = 1 AND J.Number='$Operator' group by J.Number ";
						$myResult = mysql_query($mySql);
						$myRow = mysql_fetch_assoc($myResult);
						
				        include_once($_SERVER["DOCUMENT_ROOT"] . "/ipdAPI/webClass/BxClass/StaffBxStatisticsItem.php");
				        $bxStatisticsItemOriginal = new StaffBxStatisticsItem();
				        
				        $cloneBxStatisticItem = clone $bxStatisticsItemOriginal;
		                $cloneBxStatisticItem->setupStatisticBxItem($myRow, $DataIn, $DataPublic, $link_id);
		                $cloneBxStatisticItem->setStaffInfomaiton($cloneBxStatisticItem->getStaffNumber(), $DataIn, $DataPublic, $link_id); 
		                $LastTimes=$cloneBxStatisticItem->getLeftBxHours();
		                
		                $HourTotal=GetBetweenDateDays($Operator,$StartTime,$EndTime,0,$DataIn,$DataPublic,$link_id);
		                
		                if ($LastTimes>=$HourTotal){
			                $EnabledSign = 1;
		                }
		                $info="可补休假为$LastTimes" . "小时";
				     break;
				  default:
				        $EnabledSign = 1;
				     break;
				    
		        }
			   
				if ($EnabledSign==1){
				           
			         $inRecode="INSERT INTO $DataPublic.kqqjsheet (Id, Number, StartDate, EndDate, Reason, Proof, Type, bcType, Estate, Date, Locks, Operator, Opdatetime, Checker, PLocks, creator, created, modifier, modified) 
					SELECT NULL,'$Operator','$StartTime','$EndTime','$Remark','0','$TypeId','$bcType','$Estate','$Date','0','$Operator','$DateTime','0','0','$Operator','$DateTime','$Operator','$DateTime'   
					FROM $DataPublic.staffmain WHERE Number='$Operator' 
					AND Number NOT IN (SELECT Number FROM $DataIn.cwxzsheet WHERE Month='$MonthTemp' ORDER BY Id)";

	                $inAction=@mysql_query($inRecode);
	                if ($inAction){ 
	                        $Log=$Log_Item .$Log_Funtion . "成功!<br>";
	                        $OperationResult="Y";
	                        $infoSTR=$Log_Funtion ."数据成功";
	                } 
	                else{
	                        $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $inRecode </div><br>";
	                        $infoSTR=$Log_Funtion ."数据失败";
	                        }
	            }
	            else{
		             $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $inRecode </div><br>";
	            }
                    break;
                    
                     case "DEL":
                     $Log_Funtion="删除";
                     $Id= $info[0];
                      //删除数据库记录
                    $delSql = "DELETE FROM $DataPublic.kqqjsheet  WHERE Id ='$Id'  and Estate=1"; 
                    $delRresult = mysql_query($delSql);
                    if($delRresult && mysql_affected_rows()>0){
                           $OperationResult="Y";
                            $Log=$Log_Item .$Log_Funtion ."成功!<br>";
                           $info=$Log_Funtion ."数据成功";
                           $fileName="H".$Id.".jpg";
                           $path = "../../download/cwadminicost/".$fileName;
                           if(file_exists($path)){
                                unlink($path);
                                }
                         }
                    else{
                            $Log="<div class='redB'>$Log_Item $Log_Funtion</div><br>";
                            $infoSTR=$Log_Funtion ."数据失败";
                            }
                    //$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.hzqksheet ");
                  break;
        }

?>
<?php 
//电信-EWEN
/////////////////////////////////
//签卡时间<=第一个默认的下班时间
////////////////////////////////
if($CHECKTIME<=$kqTime[1]){				//第一时间段的上班签卡:条件是上班签到时间<第一个下班签退时间
	$SetTime=$kqTime[0];				//默认的签卡时间
//*******当天首条记录	
	if($Ai==""){						//如果之前没有上班签卡记录：则做为Ai，即第一时间段的签到
		if($CHECKTIME<=$kqTime[0]){		//如果上班签卡记录<=上班默认签卡时间；则非迟到记录
			//注意上班签卡时间与默认时间相差30分钟以上的：提醒异常记录；更改意见：临时排班或不处理（以默认时间计算）
			$TimeGap=strtotime($CHECKTIME)-strtotime($kqTime[0]);$TimeTemp1=intval($TimeGap/3600/0.5)*0.5;
//情况1:第一个签到记录,但签到时间早于默认时间30分钟以上;结果:1.员工超前签到 2.存在临时排班(虽先进行临时排班)										
			if($TimeTemp1>=0.5){
				$CHECKSIGN="<div align='center' class='yellowB'>上班签到1</div>";
				$UnusualRecord="<div align='center' class='yellowB'>异常记录</div>";
				$RevisionPrompt="<div class='yellowB'>临时排班或不处理(按默认时间计算)</div>";
				}
			else{
//情况2:第一个签到记录,但签到时间在正常范围内,即正常签到
				$CHECKSIGN="<div align='center' class='greenB'>上班签到2</div>";
				}										
			}
		else{	
//情况3:第一个签到记录,但签到时间大于默认签到时间,如果是正常工作日,则计迟到，否则当正常签到
			$CHECKSIGN="<div align='center' class='greenB'>上班签到3</div>";
			if($DateType=="G"){//如果是工作日，检查是否有请假，无请假则为迟到？？？？？？？？
				$CHECKSIGN="<div align='center' class='yellowB'>上班签到3</div>";
				$UnusualRecord="<div align='center' class='yellowB'>迟到或请假</div>";
				}
			}//end if($CHECKTIME<=$kqTime[0])
									
		$Ai=$CHECKTIME;
//第一个签到时间是否有有效:
//根据下一条记录判断，如果下一条记录为I（无下班记录）或无下一条记录(无效记录)
		$CheckNext= mysql_query("SELECT CHECKTIME,CHECKTYPE FROM $DataIn.checkinout WHERE 1 and NUMBER=$Number and CHECKTIME>'$CHECKTIME' order by CHECKTIME limit 0,1",$link_id);
		if($CheckNextRow = mysql_fetch_array($CheckNext)){//如果后面有签卡记录
//情况4:如果后面还有签卡记录,并且下一条签卡记录是签到记录,则本条记录是无效记录,可能1.错签(隔日签退) 2.无对应下班记录 3.上班签到重复
			if($CheckNextRow["CHECKTYPE"]=="I"){
				$CHECKSIGN="<div align='center' class='redB'>上班签到4</div>";
				$UnusualRecord="<div align='center' class='redB'>无效记录</div>";
				$RevisionPrompt="<div class='redB'>删除或补下班记录</div>";
				}
			}
//情况5:如果后面没有记录,即当天只有一条记录时,为无效记录,可能1.错签 2.无对应下班记录 3.隔日签退
		else{
			$CHECKSIGN="<div align='center' class='redB'>上班签到5</div>";
			$UnusualRecord="<div align='center' class='redB'>无效记录</div>";
			$RevisionPrompt="<div class='redB'>删除或补下班记录</div>";
			}
		}
//********非首条记录;Ai!=""
	else{//如果Ai已经存在，则当前记录可能是重复记录；也可能是错签早退的下班记录，需根据下一个记录才能进行判断
		 //如果下一个记录是正常签退记录，则属于重复记录否则为错签记录
//情况6:仍属第一时间段上班签到,但是重复签到
		$CHECKSIGN="<div align='center' class='redB'>上班签到7</div>";
		$UnusualRecord="<div align='center' class='redB'>重复记录</div>";
		$RevisionPrompt="<div class='redB'>删除</div>";
		//读取下一记录是否属于正常的签退记录，是则不再做处理，否则提示改为 错签记录 更新意见改为 下班签退
		$nextRecord= mysql_query("SELECT CHECKTIME,CHECKTYPE FROM $DataIn.checkinout WHERE 1 and NUMBER=$Number and CHECKTIME>'$CHECKTIME' limit 0,1",$link_id);
		if($nextRecordRow = mysql_fetch_array($nextRecord)){//如果后面有签卡记录
//情况7:如果下一条记录是签到,那当前记录应该是第一时间段的下班签退			
			if($nextRecordRow["CHECKTYPE"]!="O"){
				$UnusualRecord="<div align='center' class='redB'>错签记录</div>";
				$RevisionPrompt="<div align='center' class='redB'>改为下班签退</div>";
				}
			}
//情况8:如果后面没有记录:则是无效记录,可能1.错签 可能2.无对应签退记录
		else{//如果后面没有签卡记录
			$UnusualRecord="<div align='center' class='redB'>无效记录</div>";
			$RevisionPrompt="<div class='redB'>删除或补签退</div>";
			}
		}
	}
///////////////////////////////
//签卡时间>第一个下班时间的情况
///////////////////////////////
else{
	$SetTime=$kqTime[2];			//时间段2的默认签到时间
	
////////////////////////////////////////////////////////
//签卡时间<=第二个时间段的下班时间,即时间段二的上班签卡
///////////////////////////////////////////////////////
	
	if($CHECKTIME<=$kqTime[3]){		//第一时间段的上班签卡
		if($Ai!="" && $Ao!=""){		//第一时间段正常
			if($Bi==""){
				if($CHECKTIME<=$kqTime[2]){//签到时间<=默认的签到时间
					$TimeGap=strtotime($CHECKTIME)-strtotime($kqTime[2]);
					$TimeTemp1=intval($TimeGap/3600/0.5)*0.5;
					if($TimeTemp1>=0.5){		//签到时间早30分钟以上，为异常记录，有可能是临时排班，也有可能是员工提前签到
//情况9:第一时间段上班正常,第二时间段的上班签到,签到时间早于默认时间30分钟以上,可能1.员工提前签卡 可能2.临时上班时间
						$CHECKSIGN="<div align='center' class='yellowB'>上班签到9</div>";
						$UnusualRecord="<div align='center' class='yellowB'>异常记录</div>";
						$RevisionPrompt="<div class='yellowB'>临时排班或不处理(按默认时间计算)</div>";
						}
					else{
//情况10:第一时间段上班正常,第二时间段正常签到
						$CHECKSIGN="<div align='center' class='greenB'>上班签到10</div>";
						}
					}
				else{//签到时间>默认的签到时间,即迟到
//情况11:第一时间段上班正常,第二时间段上班签到,但迟到
					if($DateType=="G"){
						$CHECKSIGN="<div align='center' class='yellowB'>上班签到11</div>";
						$UnusualRecord="<div align='center' class='yellowB'>迟到</div>";
						}
					else{
//情况12:第一时间段上班正常,第二时间段上班签到,但不是工作日,所以仍然属于正常签到
						$CHECKSIGN="<div align='center' class='greenB'>上班签到12</div>";
						}
					}
				$Bi=$CHECKTIME;
				}
			else{//if($Bi!="")
//情况13:第一时间段上班正常,第二时间段已有签到记录,则当前记录为重复记录,或错签的下班记录
				$CHECKSIGN="<div align='center' class='redB'>上班签到13</div>";
				$UnusualRecord="<div align='center' class='redB'>重复记录</div>";
				$RevisionPrompt="<div class='redB'>删除</div>";
				}
			}
		else{//第一时间段不正常,$Ai=="" || $Ao==""
//情况14:
			$CHECKSIGN="<div align='center' class='greenB'>上班签到14</div>";
			$Bi=$CHECKTIME;
			}
		}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//签到时间>第二个时间段的下班时间,可能1.应该下班签退(Bo=="") 2.完全无效记录(Bi!="" && Bo!="") 3.可能是加班签到//
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	else{
		$CHECKSIGN="<div align='center' class='redB'>上班签到15</div>";
		$UnusualRecord="<div align='center' class='redB'>无效记录</div>";
		$RevisionPrompt="<div class='redB'>删除或改加班记录</div>";
		if($Bi!="" && $Bo==""){
			$CHECKSIGN="<div align='center' class='redB'>上班签到16</div>";
			$UnusualRecord="<div align='center' class='redB'>无效记录</div>";
			$RevisionPrompt="<div class='redB'>改为下班签退</div>";
			}
		else{
			if($Ci!=""){
				$CHECKSIGN="<div align='center' class='redB'>上班签到15</div>";
				$UnusualRecord="<div align='center' class='redB'>无效记录</div>";
				$RevisionPrompt="<div class='redB'>删除或改加班签退</div>";
				}
			}
		}
	}
?>
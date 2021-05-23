<?php 
//电信-EWEN
/////////////////////////////////
//签卡时间<=第一个默认的下班时间$CheckTime<=$dDateTimeOut
////////////////////////////////
$dSetTime=$dTimeIn;
if($ioTime<=$dTimeOut){	
	if($ChickIn==""){//正常:签到检查
		if($ioTime<=$dTimeIn){
			$TimeGap=strtotime($CheckTime)-strtotime($dDateTimeIn);$TimeTemp1=intval($TimeGap/3600/0.5)*0.5;				
			if($TimeTemp1>=0.5){
				//情况1:第一个签到记录,但签到时间早于默认时间30分钟以上;结果:1.员工超前签到 2.存在临时排班(虽先进行临时排班)	
				$CHECKSIGN="<div align='center' class='yellowB'>上班签到1</div>";
				$UnusualInfo="<div align='center' class='yellowB'>异常记录</div>";
				$UpdateInfo="<div class='yellowB'>临时排班或不处理(按默认时间计算)</div>";
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
				$UnusualInfo="<div align='center' class='yellowB'>迟到或请假</div>";
				$UpdateInfo="<div class='yellowB'>不修改或临时排班或有请假</div>";
				}
			}//end ($ioTime<=$dTimeIn)
		$ChickIn=$CheckTime;
		//第一个签到时间是否有有效:
		//根据下一条记录判断，如果下一条记录为I（无下班记录）或无下一条记录(无效记录)
		$CheckNext= mysql_query("SELECT CheckTime,CheckType FROM $DataIn.checkinout WHERE 1 and Number=$Number and CheckTime>'$CheckTime' order by CheckTime limit 0,1",$link_id);
		if($CheckNext && $CheckNextRow = mysql_fetch_array($CheckNext)){//如果后面有签卡记录			
			if($CheckNextRow["CheckType"]=="I"){
				//情况4:如果后面还有签卡记录,并且下一条签卡记录是签到记录,则本条记录是无效记录,可能1.错签(隔日签退) 2.无对应下班记录 3.上班签到重复
				$CHECKSIGN="<div align='center' class='redB'>上班签到4</div>";
				$UnusualInfo="<div align='center' class='redB'>无效记录</div>";
				$UpdateInfo="<div class='redB'>删除或补下班记录</div>";
				}
			}
		else{
			//情况5:如果后面没有记录,即当天只有一条记录时,为无效记录,可能1.错签 2.无对应下班记录 3.隔日签退
			$CHECKSIGN="<div align='center' class='redB'>上班签到5</div>";
			$UnusualInfo="<div align='center' class='redB'>无效记录</div>";
			$UpdateInfo="<div class='redB'>删除或补下班记录</div>";
			}
		}
	else{//如果ChickIn已经存在，则当前记录可能是重复记录；也可能是错签早退的下班记录，需根据下一个记录才能进行判断
		 //如果下一个记录是正常签退记录，则属于重复记录否则为错签记录
//情况6:仍属第一时间段上班签到,但是重复签到
		$CHECKSIGN="<div align='center' class='redB'>上班签到7</div>";
		$UnusualInfo="<div align='center' class='redB'>重复记录</div>";
		$UpdateInfo="<div class='redB'>删除或改下班签退</div>";
		}
	}
	else{
		if($ChickIn==""){
			$CHECKSIGN="<div align='center' class='greenB'>上班签到8</div>";
			$ChickIn=$CheckTime;
			}
		else{
			$CHECKSIGN="<div align='center' class='redB'>上班签到9</div>";
			$UnusualInfo="<div align='center' class='redB'>重复记录</div>";
			$UpdateInfo="<div class='redB'>删除或改为下班签退</div>";
			}
		}	
///////////////////////////////
//签卡时间>第一个下班时间的情况
///////////////////////////////
?>
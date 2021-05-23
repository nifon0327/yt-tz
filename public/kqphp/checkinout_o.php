<?php 
//电信-EWEN
if($kqtime_length>2){
	/////////////////////////////////
	//第一时间段的下班签退
	/////////////////////////////////
	if($CHECKTIME<$kqTime[2]){//签退时间在13:00之内
		if($CHECKTIME<$kqTime[0]){
//情况1:第一时间段的签退时间比第一个上班签到时间还早,可能1.隔日签退 2.错签,应该为上班签到(可进一步区分:检查下一记录是否为O)
			$SetTime=$kqTime[0];
			$CHECKSIGN="<div align='center' class='redB'>下班签退1</div>";
			$UnusualRecord="<div align='center' class='redB'>异常记录</div>";
			$RevisionPrompt="<div class='redB'>删除|改上班|隔日签退</div>";
			}
		else{
			$SetTime=$kqTime[1];
			if($Ai!=""){
				if($Ao==""){
					if($CHECKTIME>=$kqTime[1]){//签卡时间在12:00-13:00
						$TimeGap=strtotime($CHECKTIME)-strtotime($kqTime[1]);$TimeTemp1=intval($TimeGap/3600/0.5)*0.5;
						if ($TimeTemp1>=0.5){//迟签30分钟以上
							if($ZLSign==0){
//情况2:第一时间段的签退时间比默认的迟30分钟以上,且已标记直落有效							
								$CHECKSIGN="<div align='center' class='greenB'>下班签退2</div>";
								$UnusualRecord="<div align='center' class='greenB'>直落有效</div>";
								}
							else{
//情况3:第一时间段的签退时间比默认的迟30分钟以上,没有标记直落有效,则为异常记录							
								$CHECKSIGN="<div align='center' class='yellowB'>下班签退3</div>";
								$UnusualRecord="<div align='center' class='yellowB'>异常记录</div>";
								$RevisionPrompt="<div class='yellowB'>直落或不处理(直落无效)</div>";
								}
							}
						else{//正常签到
//情况4:第一时间段的签退时间在正常范围内						
							$CHECKSIGN="<div align='center' class='greenB'>下班签退4</div>";
							}
						}
					else{//签卡时间在8:00-12:00
//情况5:第一时间段签退早于默认时间,即有早退现象????????????可检测有没有请假					
						$CHECKSIGN="<div align='center' class='yellowB'>下班签退5</div>";
						//如果是工作日，检查是否有请假
						if($DateType=="G"){
							$UnusualRecord="<div align='center' class='yellowB'>早退或请假</div>";
							}
						else{
							$UnusualRecord="<div align='center' class='yellowB'>假日早退</div>";
							}
						}
					$Ao=$CHECKTIME;
					}
				else{//Ao已存在
//情况6:第一时间段签退,但签退记录已经存在,即重复签退
					$CHECKSIGN="<div align='center' class='redB'>下班签退6</div>";
					$UnusualRecord="<div align='center' class='redB'>重复记录</div>";
					$RevisionPrompt="<div class='redB'>删除</div>";
					}
				}
			else{//if($Ai!="")不成立,即没有上班签到记录
//情况7:第一时间段签退,但没有签到记录,则本记录可能是没有效的或记录正常但需要补上班记录(补上班记录后再重新检测记录的其它状况)
				$CHECKSIGN="<div align='center' class='redB'>下班签退7</div>";
				$UnusualRecord="<div align='center' class='redB'>无效记录</div>";
				$RevisionPrompt="<div class='redB'>跨日签退或补上班记录或删除</div>";
				}
			}									
		}
	else{//签退时间在13:00之后;
		$SetTime=$kqTime[3];
		if($Bi!=""){//第二时间段已有上班签到记录
			if($Bo==""){
				if($CHECKTIME>=$kqTime[3]){//>=17:00 如果下班签卡时间>=默认的时间,则正常签退,但如果相差30分钟以上,则要检查直落是否有效
					$TimeGap=strtotime($CHECKTIME)-strtotime($kqTime[3]);$TimeTemp1=intval($TimeGap/3600/0.5)*0.5;
					if($Class!=""){
//情况8:第二时间段签退,签退时时间比默认时间迟30分钟以上,且有跨日标记
						$CHECKSIGN="<div align='center' class='greenB'>下班签退8</div>";
						$UnusualRecord="<div align='center' class='greenB'>跨日签退</div>";
						}
					else{
//情况9:第二时间段签退,签退时时间比默认时间迟30分钟以上,没有有跨日标记,有直落标记
						if ($TimeTemp1>=0.5){
							if($ZLSign==0){
								$CHECKSIGN="<div align='center' class='greenB'>下班签退9</div>";
								$UnusualRecord="<div align='center' class='greenB'>直落有效</div>";
								}
							else{
								//如果是跨日记录，则直落自然有效
//情况10:第二时间段签退,签退时时间比默认时间迟30分钟以上,没有有跨日标记,没直落标记
								$CHECKSIGN="<div align='center' class='yellowB'>下班签退10</div>";
								$UnusualRecord="<div align='center' class='yellowB'>异常记录</div>";
								$RevisionPrompt="<div class='yellowB'>直落或不处理(直落无效)</div>";
								}
							}
						else{
//情况11:第二时间段签退,正常签退						
							$CHECKSIGN="<div align='center' class='greenB'>下班签退11</div>";
							}	
						}
					}
				else{//<17:00 如果下班签卡时间少于默认的时间,则早退(工作日)
//情况12:第二时间段签退,签退时间早于默认时间,即早退				
					$CHECKSIGN="<div align='center' class='yellowB'>下班签退12</div>";
					//如果是工作日，检查是否有请假
					if($DateType=="G"){
						$UnusualRecord="<div align='center' class='yellowB'>早退</div>";
						}
					else{
						$UnusualRecord="<div align='center' class='yellowB'>假日早退</div>";
						}
					}
					$Bo=$CHECKTIME;
				}
			else{//if($Bo==""){不成立
//情况13:第二时间段的下班签退已经存在,如果加班签到已存在,则可能是加班签退			
				if($Ci!=""){
					$CHECKSIGN="<div align='center' class='redB'>下班签退13</div>";
					$UnusualRecord="<div align='center' class='redB'>错签记录</div>";
					$RevisionPrompt="<div class='redB'>改为加班签退</div>";
					}
				else{
//情况14:第二时间段的下班签退已经存在,如果加班签到未存在,则可能1.是重复记录 可能2.是错签记录(加班签到)			
					if($Ci==""){
						$CHECKSIGN="<div align='center' class='redB'>下班签退14</div>";
						$UnusualRecord="<div align='center' class='redB'>重复记录</div>";
						$RevisionPrompt="<div class='redB'>删除或改加班签到</div>";
						}
					else{
						$CHECKSIGN="<div align='center' class='redB'>下班签退15</div>";
						$UnusualRecord="<div align='center' class='redB'>重复记录</div>";
						$RevisionPrompt="<div class='redB'>删除</div>";
						
						}
					}
				}
			}
		else{//if($Bi!="")不成立,即第二时间段没有上班签到
			if($Ai!="" && $Ao!=""){
				if($Ci==""){
//情况16:第一时间段正常，第二时间段没有上班签到
					$CHECKSIGN="<div align='center' class='redB'>下班签退16</div>";
					$UnusualRecord="<div align='center' class='redB'>无效记录</div>";
					$RevisionPrompt="<div class='redB'>删除或补上班签到</div>";
					}
				else{
//情况17:第一时间段正常，第二时间段没有上班，加班有签到				
					$CHECKSIGN="<div align='center' class='redB'>下班签退17</div>";
					$UnusualRecord="<div align='center' class='redB'>无效记录</div>";
					$RevisionPrompt="<div class='redB'>改加班签退</div>";
					}
				}
		else{
			if($Ai!="" && $Ao==""){
//情况18:中午直落
				if($ZLSign==0){
					$CHECKSIGN="<div align='center' class='greenB'>下班签退18</div>";
					$UnusualRecord="<div align='center' class='greenB'>直落有效</div>";
					}
				else{
					$CHECKSIGN="<div align='center' class='yellowB'>下班签退19</div>";
					$UnusualRecord="<div align='center' class='yellowB'>异常记录</div>";
					$RevisionPrompt="<div class='yellowB'>直落或不处理(直落无效)</div>";
					}
				}
			else{
				if($Ci!=""){
					$CHECKSIGN="<div align='center' class='redB'>下班签退20</div>";
					$UnusualRecord="<div align='center' class='redB'>无效记录</div>";
					$RevisionPrompt="<div class='redB'>改加班签退</div>";
					}
				else{
					$CHECKSIGN="<div align='center' class='redB'>下班签退21</div>";
					$UnusualRecord="<div align='center' class='redB'>无效记录</div>";
					$RevisionPrompt="<div class='redB'>删除</div>";
					}
				}
			}	
		}}
	}
else{//只有一个时间段的临时排班情况
	$SetTime=$kqTime[1];
	if($Ai!=""){
		//检查时间是否有直落		
		$Bo=$CHECKTIME;
		$TimeGap=strtotime($CHECKTIME)-strtotime($kqTime[1]);$TimeTemp1=intval($TimeGap/3600/0.5)*0.5;
		if($TimeTemp1>=0.5){
			if($Class!=""){
//情况22:
				$CHECKSIGN="<div align='center' class='greenB'>下班签退22</div>";
				$UnusualRecord="<div align='center' class='greenB'>跨日签退</div>";
				}
			else{
				if($ZLSign==0){			
					$CHECKSIGN="<div align='center' class='greenB'>下班签退23</div>";
					$UnusualRecord="<div align='center' class='greenB'>直落有效</div>";
					}
				else{
					$CHECKSIGN="<div align='center' class='yellowB'>下班签退24</div>";
					$UnusualRecord="<div align='center' class='yellowB'>异常记录</div>";
					$RevisionPrompt="<div class='yellowB'>直落或不处理(直落无效)</div>";
					}
				}
			}
		else{//正常签退			
			$CHECKSIGN="<div align='center' class='greenB'>下班签退25</div>";
			}
		}
	else{//if($Ai!=""){不成立
//情况26:无上班签到
		$CHECKSIGN="<div align='center' class='redB'>下班签退26</div>";
		if($Ci!=""){
			$UnusualRecord="<div align='center' class='redB'>无效记录</div>";
			$RevisionPrompt="<div class='redB'>改加班签退</div>";
			}
		else{
			$UnusualRecord="<div align='center' class='redB'>无效记录</div>";
			$RevisionPrompt="<div class='redB'>删除或补上班签到或隔日签退</div>";
			}
		}
	}								
?>
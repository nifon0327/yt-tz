<?php 
//分开已更新

if($AI=="" || $AO==""){//任何一个为空，皆为缺勤
	$ATime=0;
	$aiTime="";
	$aoTime="";
	}
else{//有上下班签到的情况:总共18种情况
	if($AI<=$dDateTimeIn){	//A:签到时间少于或等于预设的签到时间：即上班签到正常;下班有五种情况
		//A签到在8:00之前；情况在1、2、3、4、5	OK
		include "kqcodetemp/checkio_model_GA.php";
		}
	else{
		if($AI<$dRestTime1){
			//B:签到在8:00-12:00；情况6、7、8、9、10
			include "kqcodetemp/checkio_model_GB.php";
			}
		else{
			if($AI<$dRestTime2){
				//C:签到在12:00-13:00；情况11、12、13
				include "kqcodetemp/checkio_model_GC.php";
				}
			else{
				if($AI<$dDateTimeOut){
					//D:签到在13:00-17:00；情况14、15、16  OK
					include "kqcodetemp/checkio_model_GD.php";
					}
				else{
					if($AI<$dRestTime3){
						//E:签到在17:00-18:00；情况17
						include "kqcodetemp/checkio_model_GE.php";
						}
					else{
						//F:签到在18:00之后:情况18
						$GJTime=abs(strtotime(rounding_out($AO))-strtotime(rounding_in($AI)))/3600;
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
?>
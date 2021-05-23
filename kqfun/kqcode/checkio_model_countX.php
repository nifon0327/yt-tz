<?php 
//二合一已更新
if($AI!="" && $AO!=""){
	if($AI<=date("Y-m-d H:i:00",strtotime("$dDateTimeIn + $dInLate minute"))){	//A:签到时间少于或等于预设的签到时间：即上班签到正常;下班有五种情况
		//A答到在8:00之前；情况在1、2、3、4、5
		include "kqcode/checkio_model_XA.php";
		}
	else{
		if($AI<$dRestTime1){//B:签到在8:00-12:00；情况6、7、8、9、10			
			include "kqcode/checkio_model_XB.php";
			}
		else{
			if($AI<$dRestTime2){//C:签到在12:00-13:00；情况11、12、13				
				include "kqcode/checkio_model_XC.php";				
				}
			else{
				if($AI<$dDateTimeOut){
					//D:签到在13:00-17:00；情况14、15、16
					include "kqcode/checkio_model_XD.php";
					}
				else{
					if($AI<$dRestTime3){
						//E:签到在17:00-18:00；情况17
						include "kqcode/checkio_model_XE.php";
						}
					else{
						//F:签到在18:00之后:情况18
						include "kqcode/checkio_model_XF.php";
						}
					}
				}
			}
		}
	}
else{
	$aiTime="";
	$aoTime="";
	}
//假日类型
switch($DateType){
	case "X":
	$XJTime=$jbTime;
	break;
	case "F":
	if($jbTimes==3){
		$FJTime=$jbTime;}
	else{
		$XJTime=$jbTime;
		}
	break;
	case "W":
	$GJTime=$jbTime;
	break;
	case "Y":
	$GJTime=$jbTime;
	break;
	}
?>
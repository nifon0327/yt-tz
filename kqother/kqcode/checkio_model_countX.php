<?php 
//电信-EWEN
if($AI!="" && $AO!=""){
	if($AI<=date("Y-m-d H:i:00",strtotime("$dDateTimeIn + $dInLate minute"))){	//A:签到时间少于或等于预设的签到时间：即上班签到正常;下班有五种情况
		//A答到在8:00之前；情况在1、2、3、4、5
		$includePath = ($ipadTag == "yes")?"../../public/kqcode/checkio_model_XA.php":"kqcode/checkio_model_XA.php";
		include $includePath;
		}
	else{
		if($AI<$dRestTime1){//B:签到在8:00-12:00；情况6、7、8、9、10	]
			$includePath = ($ipadTag == "yes")?"../../public/kqcode/checkio_model_XB.php":"kqcode/checkio_model_XB.php";
			include $includePath;		
			}
		else{
			if($AI<$dRestTime2){//C:签到在12:00-13:00；情况11、12、13	
				$includePath = ($ipadTag == "yes")?"../../public/kqcode/checkio_model_XC.php":"kqcode/checkio_model_XC.php";
				include $includePath;							
				}
			else{
				if($AI<$dDateTimeOut){
					//D:签到在13:00-17:00；情况14、15、16
					$includePath = ($ipadTag == "yes")?"../../public/kqcode/checkio_model_XD.php":"kqcode/checkio_model_XD.php";
					include $includePath;
					}
				else{
					if($AI<$dRestTime3){echo  "调 $dRestTime3";
						//E:签到在17:00-18:00；情况17
						$includePath = ($ipadTag == "yes")?"../../public/kqcode/checkio_model_XE.php":"kqcode/checkio_model_XE.php";
						include $includePath;
						}
					else{
						//F:签到在18:00之后:情况18
						$includePath = ($ipadTag == "yes")?"../../public/kqcode/checkio_model_XF.php":"kqcode/checkio_model_XF.php";
						include $includePath;
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
		
if($jbTimes==2)
		{
			$XJTime=$jbTime;
		}
		else
		{
		$GJTime=$jbTime;		
		}
	break;
	}
?>
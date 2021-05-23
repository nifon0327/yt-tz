<?php 
//电信-ZX  2012-08-01
//签卡分析
$i=0;//当天记录数
$Today=date("Y-m-d");
$KrSign=0;
//检查当天的记录(不包括上一天的跨日签退记录)
$checkKQsql=mysql_query("SELECT * FROM $DataIn.checkiotemp WHERE Number='$Number' AND KrSign=0 AND DATE_FORMAT(CheckTime,'%Y-%m-%d')='$Today' ORDER BY CheckTime",$link_id);
if($checkKQrow=mysql_fetch_array($checkKQsql)){			
	do{
		$TempCheckType=$checkKQrow["CheckType"];
		$TempCheckTime=$checkKQrow["CheckTime"];
		$i++;
		}while ($checkKQrow=mysql_fetch_array($checkKQsql));
	}
switch($i){
	case 0://没有记录时，只能签到，否则不能保存
		if($CheckType=="I"){//状态为签到
			include "kqtemp_sign_ajax_10.php";
			}
		else{//状态为签退,1为则无效，2为跨日签退
			//检查上一日是否只有一个签到记录，是则为跨日签退,需检查是否已经存在跨日签退，如果存在，记录无效或重复
			$Preday=date("Y-m-d",strtotime("$Today - 1 day"));
			$CheckPre=mysql_query("SELECT * FROM $DataIn.checkiotemp WHERE Number='$Number' AND KrSign=0 AND DATE_FORMAT(CheckTime,'%Y-%m-%d')='$Preday' ORDER BY CheckTime",$link_id);
			if($CheckPreRow=mysql_fetch_array($CheckPre)){
				//检查跨日签退记录是否已经存在，是则重复签卡
				$CheckNow=mysql_query("SELECT * FROM $DataIn.checkiotemp WHERE Number='$Number' AND KrSign=1 AND DATE_FORMAT(CheckTime,'%Y-%m-%d')='$Today' ORDER BY CheckTime",$link_id);
				if($CheckNowRow=mysql_fetch_array($CheckNow)){
					$ReInfo="$Name<br>签退无效,重复签卡";
					}
				else{
					$KrSign=1;
					include "kqtemp_sign_ajax_10.php";
					}
				}
			else{
				$ReInfo="$Name<br>无效,没有签到";
				}
			}
		break;
	case 1:	//有一个记录
		if($CheckType=="I"){//状态为签到,则无效
			$ReInfo="$Name<br>签卡无效,重复签到";
			}
		else{//状态为签退,相隔第一条记录不得少于10分钟
			$TimeGap=strtotime($CheckTime)-strtotime($TempCheckTime);
			$TimeTemp1=intval($TimeGap/60);		
			if($TimeTemp1<10){//签卡间隔在10分钟内，禁止
				$ReInfo="$Name<br>无效,连续签卡";				//返回提示信息和员工姓名
				}
			else{
				include "kqtemp_sign_ajax_10.php";
				}
			}
		break;
	case 2: //有两个记录,表示查询，显示签卡记录
		$ReInfo="签卡无效,重复签卡<br>$Name";		
		$OperationResult="Y";
		break;
	default://没有记录时，只能签到，否则不能保存
		$ReInfo="签卡异常,需人事检查<br>$Name";
		break;
	}
?>

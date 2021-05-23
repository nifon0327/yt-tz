<?php 

	$i=0;//当天记录数
	$Today=date("Y-m-d");
	$KrSign=0;
	
	$chksql = "SELECT * FROM $DataIn.checkinout WHERE Number='$Number' AND KrSign=0 AND DATE_FORMAT(CheckTime,'%Y-%m-%d')='$Today' ORDER BY CheckTime";
	//echo $chksql;
	$checkKQsql=mysql_query($chksql);
	if($checkKQrow=mysql_fetch_assoc($checkKQsql))
	{			
		do{
			$TempCheckType=$checkKQrow["CheckType"];
			$TempCheckTime=$checkKQrow["CheckTime"];
			$i++;
		}while ($checkKQrow=mysql_fetch_assoc($checkKQsql));
	}
	//$resultInfo  = "in $i";
	switch($i){
	case 0://没有记录时，只能签到，否则不能保存
		if($CheckType=="I"){//状态为签到
			//$resultInfo  = "in I";
			include "chkWriteIn.php";
			
			if(strtotime($CheckTime) > strtotime("08:00"))
				{
					$message = $Name."  ".$CheckTime."签到"."($today)";
					//include "push.php";
				}
		}
		else
		{//状态为签退,1为则无效，2为跨日签退
			$resultInfo .=":无效,没有签到";
			$errorState = "yes";
		}
		break;
	case 1:	//有一个记录
		if($CheckType=="I"){//状态为签到,则无效
			$resultInfo .= ":签卡无效,重复签到";
			$errorState = "yes";
			}
		else{//状态为签退,相隔第一条记录不得少于10分钟
			$TimeGap=strtotime($CheckTime)-strtotime($TempCheckTime);
			$TimeTemp1=intval($TimeGap/60);		
			if($TimeTemp1<10){//签卡间隔在10分钟内，禁止
				$resultInfo .= ":无效,连续签卡";				//返回提示信息和员工姓名
				$errorState = "yes";
				}
			else{
				include "chkWriteIn.php";
				}
			}
		break;
	case 2: //有两个记录,表示查询，显示签卡记录
		$resultInfo .= ":签卡无效,重复签卡";
		$errorState = "yes";
		$OperationResult="N";
		break;
	default://没有记录时，只能签到，否则不能保存
		$resultInfo .= ":签卡异常,需人事检查";
		$errorState = "yes";
		break;
	}

?>
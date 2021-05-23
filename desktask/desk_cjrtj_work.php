<?php   
//电信---yang 20120801
$CheckDate=$DateNow;
$CheckMonth=substr($CheckDate,0,7);
$ToDay=$CheckDate;
//3		读取需统计的有效的员工资料
$checkStaffSql=mysql_query("SELECT K.Number 
FROM $DataIn.checkinout K 
LEFT JOIN $DataIn.sc1_memberset S ON S.Number=K.Number
WHERE K.CheckTime LIKE '$CheckDate%' $SearchDay $TEST GROUP BY K.Number",$link_id);
if($checkStaffRow = mysql_fetch_array($checkStaffSql)) {
	do{
		$aiTime=0;		$aoTime=0;		$GTime=0;		$WorkTime=0;		$GJTime=0;
		$AI="";
		$AO="";
		$aiTime="";
		$aoTime="";
		$Number=$checkStaffRow["Number"];
		//读取员工的工资，以便计算当月该员工时薪:员工工资取值？
		//$checkYGXZ=mysql_fetch_array(mysql_query("SELECT IFNULL(Amount,0) AS Amount FROM $DataIn.cwxzsheet WHERE Number=$Number AND Month='$CheckMonth' ORDER BY Id DESC LIMIT 1",$link_id));
		//$MonthYGXZ=$checkYGXZ["Amount"];
		include "kqcode/checkio_model_pb.php";//读取班次
		//读取签卡记录:签卡记录必须是已经审核的
		$ioResult = mysql_query("SELECT CheckTime,CheckType,KrSign FROM $DataIn.checkinout WHERE 1 and Number=$Number and ((CheckTime LIKE '$CheckDate%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$CheckDate%' and KrSign='1')) ORDER BY CheckTime",$link_id);
		if($ioRow = mysql_fetch_array($ioResult)) {
			do{
				$CheckTime=$ioRow["CheckTime"];
				$CheckType=$ioRow["CheckType"];
				$KrSign=$ioRow["KrSign"];
				switch($CheckType){
					case "I":
						$AI=date("Y-m-d H:i:00",strtotime("$CheckTime"));
						$aiTime=date("H:i",strtotime("$CheckTime"));						
						break;
					case "O":
						$AO=date("Y-m-d H:i:00",strtotime("$CheckTime"));
						$aoTime=date("H:i",strtotime("$CheckTime"));
						break;
					}					
				}while ($ioRow = mysql_fetch_array($ioResult));
			
			if($pbType==0){		//正常排班
				include "kqsccode/checkio_model_countG.php";
				}
			else{				//临时排班
				include "kqsccode/checkio_model_countGL.php";
				}
			}
		
		//计算直落工时
		$ZL_Result = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Hours),0) AS Hours FROM $DataPublic.kqzltime  WHERE Date='$CheckDate' AND Number='$Number'",$link_id));
		$ZL_Hours=$ZL_Result["Hours"];
		//该员工当天的总工价
		$TempSXSTR="SX".strval($Number);
		//$+=时薪*
		$oneStaffTodayXZ=($WorkTime+$ZL_Hours)*$$TempSXSTR;
		$sjAmount+=$oneStaffTodayXZ;
		$Hours+=$WorkTime+$ZL_Hours;//累加工时
		$WorksB++;
		}while($checkStaffRow = mysql_fetch_array($checkStaffSql));
	}
?>
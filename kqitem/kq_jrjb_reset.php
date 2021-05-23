<?php 
//电信-EWEN
//读取加班时薪资料
$checkResult=mysql_query("SELECT ValueCode,Value FROM $DataPublic.cw3_basevalue WHERE ValueCode='103' OR ValueCode='104' and Estate=1",$link_id);
if($checkRow = mysql_fetch_array($checkResult)){
	do{
		$ValueCode=$checkRow["ValueCode"];
		switch($ValueCode){
			case "103"://2倍时薪
				$HourlyWage2=$checkRow["Value"];
				break;
			case "104"://3倍时薪
				$HourlyWage3=$checkRow["Value"];
				break;
			}
		}while ($checkRow = mysql_fetch_array($checkResult));
	}
$HourlyWage2=$HourlyWage2==""?0:$HourlyWage2;
$HourlyWage3=$HourlyWage3==""?0:$HourlyWage3;

$FristDay=$CheckMonth."-01";
$EndDay=date("Y-m-t",strtotime($FristDay));
$Days=date("t",strtotime($FristDay));
$Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
$Result = mysql_query("SELECT Number FROM $DataIn.hdjbsheet WHERE Id='$Id'",$link_id);
if($Row = mysql_fetch_array($Result)){
	do{
		$Number=$Row["Number"];
		$Name=$Row["Name"];
		//员工数据初始化
		$sumXJTime=0;//2倍加班工时之和
		$sumFJTime=0;//3倍加班工时之和
		for($i=0;$i<$Days;$i++){//日循环
			$aiTime="";$aoTime="";$AI="";$AO="";$XJTime=0;$FJTime=0;$jbTime=0;$ZL_Hours=0;
			$CheckDate=date("Y-m-d",strtotime("$FristDay + $i days"));
			$ToDay=$CheckDate;
			$weekDay=date("w",strtotime($CheckDate));	 
			$DateType=($weekDay==6 || $weekDay==0)?"X":"G";
			$holidayResult = mysql_query("SELECT Type,jbTimes FROM $DataPublic.kqholiday WHERE Date='$CheckDate'",$link_id);
			if($holidayRow = mysql_fetch_array($holidayResult)){
				$jbTimes=$holidayRow["jbTimes"];
				switch($holidayRow["Type"]){
					case 0:		$DateType="W";		break;
					case 1:		$DateType="Y";		break;
					case 2:		$DateType="F";		break;
					}
				}
			$rqddResult = mysql_query("SELECT Id FROM $DataIn.kqrqdd WHERE Number='$Number' and (GDate='$CheckDate' OR XDate='$CheckDate') LIMIT 1",$link_id);
			if($rqddRow = mysql_fetch_array($rqddResult)){			
				$DateType=$DateType=="X"?"G":"X";
				}
			//最终决定日期类型
			if($DateType!="G"){
				include "kqcode/checkio_model_pb.php";
				//读取签卡记录:签卡记录必须是已经审核的
				$ioResult = mysql_query("SELECT CheckTime,CheckType,KrSign FROM $DataIn.checkinout WHERE 1 and Number=$Number and ((CheckTime LIKE '$CheckDate%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$CheckDate%' and KrSign='1')) order by CheckTime",$link_id);
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
					}
				if($pbType==0){
					include "kqcode/checkio_model_countX.php";
					}
				else{
					include "kqcode/checkio_model_countXL.php";
					}
				}			
			$sumXJTime=$sumXJTime+$XJTime;
			$sumFJTime=$sumFJTime+$FJTime;
			include "kqcode/checkio_model_zl.php";
			}//end for 
		//////////////入库
		$jrAmount=intval($sumXJTime*$HourlyWage2+$sumFJTime*$HourlyWage3);//取整
		if($jrAmount>0){
			$Date=date("Y-m-d");
			$inRecode2="update $DataIn.hdjbsheet set xHours='$sumXJTime',fHours='$sumFJTime',Amount='$jrAmount',Date='$Date',Locks='0',Operator='$Operator' WHERE Month='$CheckMonth' and Number='$Number LIMIT 1'";
			$inAction2=mysql_query($inRecode2);
			if ($inAction2){ 
				$Log.="员工".$Number." ".$CheckMonth."的假日加班统计重置成功! $inRecode2 <br>";
				} 
			else{
				$Log.="<div class=redB>员工".$Number." ".$CheckMonth."的的假日加班统计重置失败或重置无变化!</div><br>";
				$OperationResult="N";
				}
			}
		else{
			//删除
			$Del = "DELETE FROM $DataIn.hdjbsheet WHERE Month='$CheckMonth' and Number='$Number and Mid='0' LIMIT 1"; 
			$result = mysql_query($Del);
			if($result && mysql_affected_rows()>0){
				$Log.="员工".$Number.$CheckMonth."的假日加班记录重置后加班工时为0，删除成功!</br>";			
				$y++;
				}
			else{
				$Log.="<div class='redB'>员工".$Number.$CheckMonth."的假日加班记录重置后加班工时为0，删除失败!</div></br>";
				$OperationResult="N";
				}
			}
		}while($Row = mysql_fetch_array($Result));
	}
?>
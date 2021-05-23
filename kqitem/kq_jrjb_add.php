<?php 
//电信-EWEN
include "../model/modelhead.php";
include "kqcode/kq_function.php";
//步骤2：
$Log_Item="员工假日加班统计";			//需处理
$fromWebPage="kq_jrjb_read";
$nowWebPage="kq_jrjb_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&CheckMonth=$CheckMonth";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Date=date("Y-m-d");
//自动追加上个月有效员工的假日加班记录，条件1：有效；2：有记录；3：还没生成记录
$CheckMonth=date("Y-m",strtotime("-1 months"));			//前一月份
$FristDay=$CheckMonth."-01";
$EndDay=date("Y-m-t",strtotime($FristDay));
$Days=date("t",strtotime($FristDay));
$Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
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
/*有效员工的条件：
	1.需要考勤且没有调动记录的（即入职起就需要考勤）；
		M.Number NOT IN (SELECT K.Number FROM redeployk K GROUP BY K.Number ORDER BY K.Id) and M.KqSign=1
	2.或有调动记录,则取考勤月份比调动生效月份大的最小那个月份的调入状态；
		M.Number IN(SELECT K.Number FROM redeployk K WHERE K.ActionIn=1 and K.Month<='$CheckMonth' GROUP BY K.Number ORDER BY K.Month))
	同时员工的入职月份要少于或等于考勤那个月份
	且员工不在离职日期少于考勤月份的员工
	且没有生成记录
	且薪资没有结付
*/
$Result = mysql_query("SELECT M.Number,M.Name
FROM $DataPublic.staffmain M 
WHERE M.cSign='$Login_cSign' AND ((M.Number NOT IN (SELECT K.Number FROM $DataPublic.redeployk K GROUP BY K.Number ORDER BY K.Id) and M.KqSign=1)
OR (M.Number IN(SELECT K.Number FROM $DataPublic.redeployk K WHERE K.ActionIn=1 and K.Month<='$CheckMonth' GROUP BY K.Number ORDER BY K.Month)))
and left(M.ComeIn,7) <='$CheckMonth' 
and M.Number NOT IN (SELECT D.Number FROM $DataPublic.dimissiondata D WHERE D.Number=M.Number and  left(D.outDate,7)<'$CheckMonth')
and M.Number NOT IN(SELECT Number FROM $DataIn.hdjbsheet WHERE Month='$CheckMonth')
ORDER BY M.Estate DESC,M.BranchId,M.JobId,M.Number",$link_id);//and M.Number NOT IN(SELECT Number FROM cwxzsheet WHERE Month='$CheckMonth')
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
            if($CheckDate=='2012-10-04')$DateType="X";
			include "kqcode/checkio_model_zl.php";
	}//end for 
		//////////////入库
		$jrAmount=intval($sumXJTime*$HourlyWage2+$sumFJTime*$HourlyWage3);//取整
		if($jrAmount>0){
			$Date=date("Y-m-d");
			if($DataIn !== 'ac'){
				$inRecode2="INSERT INTO $DataIn.hdjbsheet SELECT NULL,'0',staffmain.BranchId,staffmain.JobId,staffmain.Number,'$CheckMonth','$sumXJTime','$HourlyWage2','$sumFJTime','$HourlyWage3','$jrAmount','$Date','1','1','$Operator'
				FROM $DataPublic.staffmain WHERE Number='$Number'  and  cSign='$Login_cSign'  and Number NOT IN (SELECT Number FROM $DataIn.hdjbsheet WHERE Month='$CheckMonth' and Number='$Number')";
			}else{
				$inRecode2="INSERT INTO $DataIn.hdjbsheet SELECT NULL,'0',staffmain.BranchId,staffmain.JobId,staffmain.Number,'$CheckMonth','$sumXJTime','$HourlyWage2','$sumFJTime','$HourlyWage3','$jrAmount','$Date','1','1','$Operator', 0, null, null, null, null
				FROM $DataPublic.staffmain WHERE Number='$Number'  and  cSign='$Login_cSign'  and Number NOT IN (SELECT Number FROM $DataIn.hdjbsheet WHERE Month='$CheckMonth' and Number='$Number')";
			}
			$inAction2=@mysql_query($inRecode2);
			if ($inAction2){ 
				$Log.="员工".$Number.$CheckMonth."的假日加班统计保存成功!<br>";
				} 
			else{
				$Log.="<div class=redB>员工".$Number.$CheckMonth."的的假日加班统计保存失败!</div><br>";
				$OperationResult="N";
				}
			}
		}while($Row = mysql_fetch_array($Result));
	}
	
//上月在职员假日加班费处理完毕
$Date=date("Y-m-d");
//自动追加上个月有效员工的假日加班记录，条件1：有效；2：有记录；3：还没生成记录
$CheckMonth=date("Y-m");			//本月
$FristDay=$CheckMonth."-01";
$EndDay=date("Y-m-t",strtotime($FristDay));
$Days=date("t",strtotime($FristDay));

//本月离职职工假日加班费处理开始
$Result = mysql_query("SELECT M.Number,M.Name
FROM $DataPublic.staffmain M 
LEFT JOIN $DataPublic.dimissiondata D ON D.Number=M.Number
WHERE Estate=0 AND KqSign=1  and  M.cSign='$Login_cSign' 
AND left(D.outDate,7)='$CheckMonth'
AND M.Number NOT IN(SELECT Number FROM $DataIn.hdjbsheet WHERE Month='$CheckMonth')
ORDER BY M.Estate DESC,M.BranchId,M.JobId,M.Number",$link_id);//and M.Number NOT IN(SELECT Number FROM cwxzsheet WHERE Month='$CheckMonth')
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
			if($DataIn !== 'ac'){
				$inRecode2="INSERT INTO $DataIn.hdjbsheet SELECT NULL,'0',staffmain.BranchId,staffmain.JobId,staffmain.Number,'$CheckMonth','$sumXJTime','$HourlyWage2','$sumFJTime','$HourlyWage3','$jrAmount','$Date','1','1','$Operator'
				FROM $DataPublic.staffmain WHERE Number='$Number' and  cSign='$Login_cSign'   and Number NOT IN (SELECT Number FROM $DataIn.hdjbsheet WHERE Month='$CheckMonth' and Number='$Number')";
			}else{
				$inRecode2="INSERT INTO $DataIn.hdjbsheet SELECT NULL,'0',staffmain.BranchId,staffmain.JobId,staffmain.Number,'$CheckMonth','$sumXJTime','$HourlyWage2','$sumFJTime','$HourlyWage3','$jrAmount','$Date','1','1','$Operator', 0, null, null, null, null
				FROM $DataPublic.staffmain WHERE Number='$Number'  and  cSign='$Login_cSign'  and Number NOT IN (SELECT Number FROM $DataIn.hdjbsheet WHERE Month='$CheckMonth' and Number='$Number')";
			}
			$inAction2=@mysql_query($inRecode2);
			if ($inAction2){ 
				$Log.="本月 $CheckMonth 离职员工 $Name(".$Number.")的假日加班统计保存成功!<br>";
				} 
			else{
				$Log.="<div class=redB>本月 $CheckMonth 离职员工 $Name(".$Number.")的的假日加班统计保存失败!</div><br>";
				$OperationResult="N";
				}
			}
		}while($Row = mysql_fetch_array($Result));
	}


//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
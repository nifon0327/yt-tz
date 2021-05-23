<?php 
//电信-EWEN
include "../model/modelhead.php";
include "kqcode/kq_function.php";
//步骤2：
ChangeWtitle("$SubCompany 考勤统计列印");//需处理
$nowWebPage =$funFrom."_count";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=845;$tableMenuS=600;
$ColsNumber=21;
if($CheckDate==""){
	$CheckDate=date("Y-m-d");
	}
$CheckMonth=substr($CheckDate,0,7);
//步骤4：星期处理
$ToDay=$CheckDate;
//2		计算当天属于那一类型日期：G 工作日：X 休息日：F 法定假日：Y 公司有薪假日：W 公司无薪假日：D 调换工作日：
$Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
$weekDay=date("w",strtotime($CheckDate));	 
$weekInfo="星期".$Darray[$weekDay];
$DateTypeTemp=($weekDay==6 || $weekDay==0)?"X":"G";
$holidayResult = mysql_query("SELECT Sign FROM $DataPublic.kqholiday WHERE Date='$CheckDate'",$link_id);
if($holidayRow = mysql_fetch_array($holidayResult)){
	switch($holidayRow["Sign"]){
	case 0:		$DateTypeTemp="W";		break;
	case 1:		$DateTypeTemp="Y";		break;
	case 2:		$DateTypeTemp="F";		break;
		}
	}
echo"<table width='$tableWidth' height='61' border='0' cellspacing='0' bgcolor='#FFFFFF'>";
echo"<tr class=''>
		<td width='30' rowspan='2' class='A1111'><div align='center'>序号</div></td>
		<td width='50' rowspan='2' class='A1101'><div align='center'>姓名</div></td>
		<td width='40' rowspan='2' class='A1101'><div align='center'>日期<br>类别</div></td>
		<td height='19' width='80' colspan='2' class='A1101'><div align='center'>签卡记录</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>应到<br>工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>实到<br>工时</div></td>
		<td width='120' colspan='3' class='A1101'><div align='center'>加点加班工时(+直落)</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>迟到</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>早退</div></td>
		<td width='160' colspan='8' class='A1101'><div align='center'>请、休假工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>缺勤<br>工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>旷工<br>工时</div></td>
		<td width='30' rowspan='2' class='A1101'><div align='center'>夜班</div></td>
 	</tr>
  	<tr class=''>
		<td width='40' heigh38t='20'  align='center' class='A0101'>签到</td>
		<td width='40' class='A0101' align='center'>签退</td>
		<td width='38' class='A0101' align='center'>G</td>
		<td width='38' class='A0101' align='center'>X</td>
		<td width='38' class='A0101' align='center'>F</td>
		<td width='20' class='A0101' align='center'>事</td>
		<td width='20' class='A0101' align='center'>病</td>		
		<td width='20' class='A0101' align='center'>无</td>
		<td width='20' class='A0101' align='center'>年</td>
		<td width='20' class='A0101' align='center'>补</td>
		<td width='20' class='A0101' align='center'>婚</td>
		<td width='20' class='A0101' align='center'>丧</td>
		<td width='20' class='A0101' align='center'>产</td>
	</tr>";
//3		读取需统计的有效的员工资料
/*有效员工的条件：
	需要考勤且没有调动记录的（即入职起就需要考勤）；
	或有调动记录,则取考勤月份比调动生效月份大的最小那个月份的调入状态；
	同时员工的入职月份要少于或等于考勤那个月份
	且员工不在离职日期少于考勤月份的员工

*/
$MySql="SELECT M.Number,M.Name,J.Name AS Job FROM $DataPublic.staffmain M LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId 
WHERE M.cSign='$Login_cSign' AND
((M.Number NOT IN (SELECT K.Number FROM $DataPublic.redeployk K GROUP BY K.Number ORDER BY K.Id) and M.KqSign<3)
OR(
	M.Number IN(
		SELECT K.Number FROM $DataPublic.redeployk K 
			INNER JOIN(
				SELECT Number,max(Month) as Month FROM $DataPublic.redeployk group by Number) k2 ON K.Number=k2.Number and K.Month=k2.Month 	
			WHERE K.ActionIn<3 and K.Month<='$CheckMonth'))
OR(
	M.Number IN(
		SELECT Ka.Number FROM $DataPublic.redeployk Ka 
			INNER JOIN(
				SELECT Number,min(Month) as Month FROM $DataPublic.redeployk WHERE Month>'$CheckMonth' group by Number) k2a ON Ka.Number=k2a.Number and Ka.Month=k2a.Month 
			WHERE Ka.ActionOut<3)))
and left(M.ComeIn,7) <='$CheckMonth' 
and M.Number NOT IN (SELECT D.Number FROM $DataPublic.dimissiondata D WHERE D.Number=M.Number and  D.outDate<'$CheckDate')
ORDER BY M.Estate DESC,M.BranchId,M.JobId,M.Number";
$i=1;
$Qty_SUM=0;
$Amount_SUM=0;
$Result = mysql_query($MySql,$link_id);
if($myrow = mysql_fetch_array($Result)) {
	do{
		$test="";		$AIcolor="";	$AOcolor="";	$jbTime=0;
		$AI="";			$AO="";			$aiTime="";		$aoTime="";		$ddInfo="";
		$aiTime=0;		$aoTime=0;		$GTime=0;		$WorkTime=0;		$GJTime=0;
		$XJTime=0;		$FJTime=0;		$InLates=0;		$OutEarlys=0;		
		$QjTime1=0;		$QjTime2=0;		$QjTime3=0;		$QjTime4=0;		$QjTime5=0;		$QjTime6=0;		$QjTime7=0;		$QjTime8=0;
		$QQTime=0;		$KGTime=0;		$YBs=0;
		$DateType=$DateTypeTemp;
		$Name=$myrow["Name"];
		$Number=$myrow["Number"];
		$Job=$myrow["Job"];
		//对调情况落实到个人：因为有时是部分员工对调
		$rqddResult = mysql_query("SELECT Id FROM $DataIn.kqrqdd WHERE Number='$Number' and (GDate='$CheckDate' OR XDate='$CheckDate') LIMIT 1",$link_id);
		if($rqddRow = mysql_fetch_array($rqddResult)){			
			$DateType=$DateType=="X"?"G":"X";
			$ddInfo="(调)";
			}
		$DateTypeColor=$DateType=="G"?"":"class='greenB'";
		//读取班次
		include "kqcode/checkio_model_pb.php";
		//读取签卡记录:签卡记录必须是已经审核的
		$ioResult = mysql_query("SELECT CheckTime,CheckType,KrSign 
		FROM $DataIn.checkinout 
		WHERE 1 and Number=$Number and ((CheckTime LIKE '$CheckDate%' and KrSign='0') OR (DATE_SUB(CheckTime,INTERVAL 1 DAY) LIKE '$CheckDate%' and KrSign='1'))
		 order by CheckTime",$link_id);
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
			//读取当天的班次时间
			}
		//当天的数据计算开始
		$GTime=$DateType=="G"?8:0;
		if($DateType=="G"){
			//工作日的数据计算
			if($pbType==0){
				include "kqcode/checkio_model_countG.php";}
			else{
				include "kqcode/checkio_model_countGL.php";}
			}
		else{
			//非工作日的数据计算			
			if($pbType==0){
				include "kqcode/checkio_model_countX.php";}
			else{
				include "kqcode/checkio_model_countXL.php";}
			}
		//当天的数据计算结束
		$aiTime=SpaceValue($aiTime);
		$aoTime=SpaceValue($aoTime);
		$GTime=zerotospace($GTime);
		$WorkTime=zerotospace($WorkTime);
		$GJTime=zerotospace($GJTime);
		$XJTime=zerotospace($XJTime);
		$FJTime=zerotospace($FJTime);
		$InLates=zerotospace($InLates);
		$OutEarlys=zerotospace($OutEarlys);
		$QjTime1=zerotospace($QjTime1);
		$QjTime2=zerotospace($QjTime2);
		$QjTime3=zerotospace($QjTime3);
		$QjTime4=zerotospace($QjTime4);
		$QjTime5=zerotospace($QjTime5);
		$QjTime6=zerotospace($QjTime6);
		$QjTime7=zerotospace($QjTime7);
		$QjTime8=zerotospace($QjTime8);
		$QQTime=zerotospace($QQTime);
		$WXTime=zerotospace($WXTime);
		$KGTime=zerotospace($KGTime);
		$BKTime=zerotospace($BKTime);
		$YBs=zerotospace($YBs);
		//直落工时计算
		include "kqcode/checkio_model_zl.php";
		echo"<tr><td class='A0111' align='center'>$i</td>";
		echo"<td class='A0101' align='center'>$Name</td>";
		echo"<td class='A0101' align='center'><div $DateTypeColor>$DateType$ddInfo</div></td>";
		echo"<td class='A0101' align='center'><span $AIcolor>$aiTime</span></td>";
		echo"<td class='A0101' align='center'><span $AOcolor>$aoTime</span></td>";
		echo"<td class='A0101' align='center'>$GTime</td>";
		echo"<td class='A0101' align='center'>$WorkTime</td>";
		echo"<td class='A0101' align='center'>$GJTime</td>";
		echo"<td class='A0101' align='center'>$XJTime</td>";
		echo"<td class='A0101' align='center'>$FJTime</td>";
		echo"<td class='A0101' align='center'>$InLates</td>";
		echo"<td class='A0101' align='center'>$OutEarlys</td>";
		echo"<td class='A0101' align='center'>$QjTime1</td>";
		echo"<td class='A0101' align='center'>$QjTime2</td>";
		echo"<td class='A0101' align='center'>$QjTime3</td>";
		echo"<td class='A0101' align='center'>$QjTime4</td>";
		echo"<td class='A0101' align='center'>$QjTime5</td>";
		echo"<td class='A0101' align='center'>$QjTime6</td>";// $test
		echo"<td class='A0101' align='center'>$QjTime7</td>";
		echo"<td class='A0101' align='center'>$QjTime8</td>";// $qjTest
		echo"<td class='A0101' align='center'>$QQTime</td>";
		echo"<td class='A0101' align='center'>$KGTime</td>";
		echo"<td class='A0101' align='center'>$YBs</td>";
		echo"</tr>";
		$i++;
		}while ($myrow = mysql_fetch_array($Result));
	}
else{
	echo"<tr bgcolor='#FFFFFF'><td colspan='25' scope='col' height='60' class='A0111' align='center'><p>暂时还没有资料。</td></tr>";
	}

		echo"<input name='IdCount' type='hidden' id='IdCount' value='$i'><input name='TempValue' type='hidden' value='0'>";
		echo"
			<tr class=''>
				<td rowspan='2' class='A0111'><div align='center'>序号</div></td>
				<td rowspan='2' class='A0101'><div align='center'>姓名</div></td>
				<td rowspan='2' class='A0101'><div align='center'>日期<br>类别</div></td>
				<td height='19' colspan='2' class='A0101'><div align='center'>签卡记录</div></td>
				<td rowspan='2' class='A0101'><div align='center'>应到<br>工时</div></td>
				<td rowspan='2' class='A0101'><div align='center'>实到<br>工时</div></td>
				<td colspan='3' class='A0101'><div align='center'>加点加班工时(+直落)</div></td>
				<td rowspan='2' class='A0101'><div align='center'>迟到</div></td>
				<td rowspan='2' class='A0101'><div align='center'>早退</div></td>
				<td colspan='8' class='A0101'><div align='center'>请、休假工时</div></td>
				<td rowspan='2' class='A0101'><div align='center'>缺勤<br>工时</div></td>
				<td rowspan='2' class='A0101'><div align='center'>旷工<br>工时</div></td>
				<td rowspan='2' class='A0101'><div align='center'>夜班</div></td>
			</tr>
			<tr class=''>
				<td heigh38t='20'  align='center' class='A0101'>签到</td>
				<td class='A0101' align='center'>签退</td>
				<td class='A0101' align='center'>G</td>
				<td class='A0101' align='center'>X</td>
				<td class='A0101' align='center'>F</td>
				<td class='A0101' align='center'>事</td>
				<td class='A0101' align='center'>病</td>		
				<td class='A0101' align='center'>无</td>
				<td class='A0101' align='center'>年</td>
				<td class='A0101' align='center'>补</td>
				<td class='A0101' align='center'>婚</td>
				<td class='A0101' align='center'>丧</td>
				<td class='A0101' align='center'>产</td>
			</tr></table>";
?>
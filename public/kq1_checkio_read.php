<style type="text/css">
<!--
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)};
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)};
/* 为 DIV 加阴影 */
.out {position:relative;background:#006633;margin:10px auto;width:300px;}
.in {background:#FFFFE6;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}
/* 为 图片 加阴影 */
.imgShadow {position:relative;     background:#bbb;      margin:10px auto;     width:220px; }
.imgContainer {position:relative;      top:-5px;     left:-5px;     background:#fff;      border:1px solid #555;     padding:0; }
.imgContainer img {     display:block; }
.glow1 { filter:glow(color=#FF0000,strengh=2)}
-->
</style>
<?php
/*
mc 验厂文件 ewen 2013-08-03 OK
验厂模式2013-05后的记录
工作日：加班时间截止20:00
周六：加班截止时间为20:00
周日：独立计算，不在此页面显示
要求：每周加班时间不超过60小时，超出的另计

原模式：
全部显示,加班时间无限制

注意调动的情况

修改：
#1、验厂模式过滤周日记录
#2、记录分上述两类处理
#3、17:30至18:30之间签退的，均前移
*/

include "../model/modelhead.php";

$path = $_SERVER["DOCUMENT_ROOT"];
include_once("$path/public/kqClass/Kq_pbSet.php");
include_once("$path/public/kqClass/Kq_dailyItem.php");
include_once("$path/public/kqClass/Kq_totleItem.php");
include_once("$path/public/kqClass/Kq_otHourSet.php");


echo"<SCRIPT src='../model/js/kq_checkio.js' type=text/javascript></script>";

$From=$From==""?"read":$From;
$Pagination=$Pagination==""?0:$Pagination;
//需处理参数
$ColsNumber=9;
$tableMenuS=500;
ChangeWtitle("$SubCompany 原始考勤记录");
$funFrom="kq1_checkio";
$nowWebPage=$funFrom."_read";
$Th_Col="日期|70|星 期|70|部门|50|小组|50|职位|50|员工姓名|60|选项|40|序号|40|考勤楼层|60|设定时间|60|出勤时间|60|出勤状态|80|异常状态|70|修改意见|260|来源|40|审核|40";
$Page_Size = 100;
$ActioToS="1,7,8,12,17";
$CheckDate=$CheckDate==""?date("Y-m-d"):$CheckDate;//默认日期

$checkInTime = "08:00:00";
$checkOutTime = "17:00:00";


//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";
	echo"<input name='CheckDate' type='text' id='CheckDate' size='10' maxlength='10' value='$CheckDate' onchange='ResetPage(this.name)' >&nbsp;";
	$SearchRows.="and (
	(C.CheckTime LIKE '$CheckDate%' and C.KrSign='0')  
	OR 
	(DATE_SUB(C.CheckTime,INTERVAL 1 DAY) LIKE '$CheckDate%' and C.KrSign='1')
	)";
	$FormalSign=$FormalSign==""?0:$FormalSign;
		$selStr="selFlag" . $FormalSign;
		$$selStr="selected";
		echo"<select name='FormalSign' id='FormalSign' onchange='ResetPage(this.name)'>
		     <option value='0' $selFlag0>全部</option>
			 <option value='1' $selFlag1>考勤有效</option>
			 <option value='2' $selFlag2>考勤参考</option>";
		echo "</select>&nbsp;";
		if($FormalSign>0){$SearchRows.=" AND M.FormalSign='$FormalSign'";}


		$attendaceFloor=$attendaceFloor==""?'-1':$attendaceFloor;
		$attendanceFloorSql = mysql_query("SELECT Id, Name From $DataPublic.attendanceipadsheet WHERE cSign = 7 and Estate = 1
										   UNION Select -1 as Id, '全部' as Name
										   UNION Select 0 as Id, '人事' as Name
										   Order by Id");
		echo"<select name='attendaceFloor' id='attendaceFloor' onchange='ResetPage(this.name)'>";
		while($attendaceFloorRow = mysql_fetch_assoc($attendanceFloorSql)){
			$floorId = $attendaceFloorRow['Id'];
			$floorName = $attendaceFloorRow['Name'];
			if($attendaceFloorRow['Id'] === $attendaceFloor){
				echo "<option value='$floorId' selected>$floorName</iption>";
			}
			else{
				echo "<option value='$floorId'>$floorName</iption>";
			}
		}
		echo "</select>&nbsp;";
		if($attendaceFloor != '-1'){$SearchRows.=" AND C.dFromId='$attendaceFloor'";}

		//选择部门
		$SelectFrom = 1;
		$SelectTB = 'M';
		include "../model/subselect/BranchId.php";
	}
echo $CencalSstr;
//echo "<a href='Kq_change.php' target='_blank'  title=''><font color='red'>考勤调动</font></a>"	;
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理

$pbSet = new KqPbSet();
echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:250px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ActionColId' type='hidden' id='ActionColId'><input name='ObjId' type='hidden' id='ObjId'><input name='A_Id' type='hidden' id='A_Id'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";
//#1 2013-05之后的过滤周日加班记录:周日和调至周日的
$WeekTemp=date("w",strtotime($CheckDate));
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
$weekDay=date("w",strtotime($CheckDate));
$mySql="SELECT C.Id,C.CheckTime,C.CheckType,C.dFrom,C.Estate,C.Locks,C.ZLSign,C.KrSign,C.Number,M.Name,M.BranchId,M.JobId,G.GroupName,A.Floor		FROM $DataIn.checkinout C 
		LEFT JOIN $DataPublic.staffmain M ON M.Number=C.NUMBER 
		LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
		Left Join $DataPublic.attendance_floor A On A.Id = M.AttendanceFloor
		WHERE 1 $SearchRows
		ORDER BY  M.BranchId,M.GroupId,M.Number,C.CheckTime";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$tbDefalut=0;
	$midDefault="";
	do{
		$m=1;
		$YYY="";
		$Id=$myRow["Id"];
		$CheckTime=$myRow["CheckTime"];												//签卡时间
		$Name=$myRow["Name"];														//员工姓名
		$Number=$myRow["Number"];													//员工Id

		include_once("../model/subprogram/factoryCheckDate.php");
		if(skipStaff($Number))
		{
			continue;
		}

		$BranchId=$myRow["BranchId"];
		$GroupName=$myRow["GroupName"];
		$CheckType=$myRow["CheckType"];
		$KrSign=$myRow["KrSign"];
		$Floor = $myRow["Floor"];

		$dFrom = $myRow["dFrom"];
		if($dFrom != 1)
		{
			$isFromIpadSql = "Select * From $DataPublic.attendanceipadsheet Where Name = '$dFrom' and Estate = '1'";
			$isFromIpadResult = mysql_query($isFromIpadSql);
			$ipadFloorRow = mysql_fetch_assoc($isFromIpadResult);
			$ipadFloor = $ipadFloorRow["Floor"];
			if($Floor != $ipadFloor)
			{
				$dFrom = "<span class='redB'>$dFrom</span>";
			}
		}
		else
		{
			$dFrom = "人事";
		}
        $Kr_bgColor="";
		//如果请假期间提前上班，则显示红色名字 add by zx 2014-01-21
		$B_Result = mysql_fetch_array(mysql_query("SELECT Number FROM $DataPublic.kqqjsheet where 1 and SUBSTR(StartDate,1,10)<=SUBSTR('$CheckTime',1,10) and SUBSTR(EndDate,1,10)>=SUBSTR('$CheckTime',1,10) and Number='$Number' LIMIT 1",$link_id));
		//echo "SELECT Number FROM $DataPublic.kqqjsheet where 1 and SUBSTR(StartDate,1,10)<=SUBSTR('$CheckTime',1,10) and SUBSTR(EndDate,1,10)>=SUBSTR('$CheckTime',1,10) and Number=$Number LIMIT 1";
		$H_W_Number=$B_Result["Number"];
		if($H_W_Number!=""){
		   $Kr_bgColor=" class='redB' title='核查下是否有请假！' ";
			//$Name="<div  title='核查下是否有请假！'>$Name</div>";
		}

		$tmpEstate=$myRow["Estate"];  //用于是否可更改时间
		$Estate=$myRow["Estate"]==0?"<div class='greenB'>已核</div>":"<div class='redB'>未核</div>";
		$Locks=$myRow["Locks"];
		$B_sql =mysql_query("SELECT Name FROM $DataPublic.branchdata where 1 and Id=$BranchId LIMIT 1",$link_id);
		if ($B_sql ){
			$B_Result = mysql_fetch_array($B_sql);
	      	$Branch=$B_Result["Name"];
	    	$JobId=$myRow["JobId"];
			}
		else{
			$Branch="&nbsp;";
			}
		$J_sql=mysql_query("SELECT Name FROM $DataPublic.jobdata where 1 and Id=$JobId LIMIT 1",$link_id);
		if($J_sql){
			$J_Result = mysql_fetch_array($J_sql);
			$Job=$J_Result["Name"];
			}
         else{
	        $Job="&nbsp;";
         	}
		include "kqcode/checkio_model_week.php";
		//#1 周日、或调为周日则过滤**********************

		if($CheckDate>="2013-05-01" && $DateType=="X"){//5.1之后的休息日中
			if(($Info=="(调为周日)"  && $ddSign==1) || ($ddSign=="" && $weekDay=="星期日")){//如果是调动且调为周日，以及没有调动且为周日的
				continue;
				}
			}
		//**********************************************
		$UnusualInfo="&nbsp";$UpdateInfo="&nbsp";		//提示信息每行均初始化
		if($midDefault!=$Number.$ToDay){						//不同员工或日期换行，重新初始化
			$ChickIn="";$ChickOut="";
			include "kqcode/checkio_model_pb.php";
			$pbTypeSTR=$pbType==1?"临":"";
			}
		$Kr_bgColor=$KrSign==0?$Kr_bgColor:"class='greenB'";

		//#2 验厂模式，可放在文件 checkio_model_o.php***************************
		$CheckYM=date("Y-m",strtotime($CheckTime));//默认日期
		//echo $CheckYM;
		$Yischeck=0;
		if($Number!=10572 && $Number!=11710 && $Number!=11359 && $Number!=10551 && $Number!=10812 && $Number!=10470 && $Number!=11271
			   && $Number!=11742 && $Number!=11661 && $Number!=11624 && $Number!=10641 && $Number!=10518 && $Number!=10397) {
			$Yischeck=1;

		}else {
			if ($CheckYM>'2013-07') {
				$Yischeck=1;
			}
		}

		//echo $CheckType;
		if($CheckDate>="2014-03" && $CheckType == "I")
		{
			if($ioTime != "")
			{
				$inTime = $CheckDate." ".$ioTime;
			}
			else
			{
				$inTime = "";
			}
		}
		else if($CheckDate>="2014-03" && $CheckType == "O" && $ioTime!="")
		{
			$outTime = $CheckDate." ".$ioTime;

			//echo("$outTime    $inTime <br>");

			$dayItem = new KqDailyItem($inTime, $outTime, $CheckDate);
			$dayItem->setupDateType($CheckDate, $Number,$DataIn, $DataPublic, $link_id);
			$otHours = new KqOtHourSet($Number,$CheckDate, $DataIn, $DataPublic, $link_id);

			//开始计算加班时间
			if(($otHours->getOtHours($dayItem->dateType) != 0 && ($dayItem->dateType == "X" || $dayItem->dateType == "Y")) || $dayItem->dateType == "G")
			{
				$dayItem->calculateHours($Number, $otHours->getOtHours($dayItem->dateType), $otHours->zlHours, $pbSet, $DataIn, $DataPublic, $link_id);
			}
			else
			{
				if($dayItem->dateType == "X" || $dayItem->dateType == "Y")
				{
					$dayItem->checkInTime = "";
					$dayItem->checkOutTime = "";
				}
			}

			$ioTime = $dayItem->returnCheckOutTime();

		}
		else if($CheckDate>="2013-05-01" && $pbTypeSTR=="" )
		{
				$EndTimeGap=strtotime($CheckTime)-strtotime($CheckDate." 20:00:00");
				//$EndTimeTemp1=intval($EndTimeGap/3600/0.5);//30分钟的次数
				$EndTimeTemp1=intval($EndTimeGap/3600/0.25);//15分钟的次数
				if($EndTimeTemp1>0){//超出时间:
					//$EndMinute=$EndTimeTemp1*30;//超出部分的总分钟数
					$EndMinute=$EndTimeTemp1*15;//超出部分的总分钟数
					$CheckTime=date("Y-m-d H:i:s",strtotime("$CheckTime-$EndMinute minute"));	//重置时间
					$ioTime=date("H:i",strtotime($CheckTime));
					}
				else{//有直落？
					//##################################17:30-18:30签退的情况：需将时间前移 ewen 2013-08-27
					//if($Number!=11710 && $Number!=11359 && $Number!=10572 && $Number!=10812 && $Number!=10551 && $Number!=11271 && $Number!=11742){
					 if($Yischeck==1) {
						if($CheckTime>=($CheckDate." 17:00:00")){
							$EndTimeGap=strtotime($CheckTime)-strtotime($CheckDate." 17:00:00");
						}else{
							if($CheckTime>=($CheckDate." 12:00:00")){
								$EndTimeGap=strtotime($CheckTime)-strtotime($CheckDate." 12:00:00");
							}
						}
						//$EndTimeTemp1=intval($EndTimeGap/3600/0.5);//30分钟的次数
						$EndTimeTemp1=intval($EndTimeGap/3600/0.25);//15分钟的次数
						//if($EndTimeTemp1>0 && $EndTimeTemp1<3){//17:30-18:30签退，则时间前移处理
						if($EndTimeTemp1>0 && $EndTimeTemp1<6){//17:30-18:30签退，则时间前移处理
							//$EndMinute=$EndTimeTemp1*30;//超出17:00的总分钟数
							$EndMinute=$EndTimeTemp1*15;//超出17:00的总分钟数
							$CheckTime=date("Y-m-d H:i:s",strtotime("$CheckTime-$EndMinute minute"));
							$ioTime=date("H:i",strtotime($CheckTime));
							}
						}
					//##################################17:30-18:30签退的情况：需将时间前移
					}

		}
		//**********************************************************************

		include "../public/kqcode/checkio_model_".$CheckType.".php";
		if($Locks==0){//锁定状态
			if($Keys & mLOCK){
				$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$Id' disabled><img src='../images/lock.png' alt='此记录已锁定操作!' width='15' height='15'>";
				}
			else{
				$Choose="&nbsp;<img src='../images/lock.png' alt='此记录已锁定操作!' width='15' height='15'>";
				}
			}
		else{		//非锁定状态，分权限
			if(($Keys & mUPDATE)||($Keys & mDELETE)||($Keys & mLOCK)){//有更新、删除、锁定权限
				$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$Id' disabled><img src='../images/unlock.png' alt='此记录未锁定操作!' width='15' height='15'>";
				}
			else{//无权限
				$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$Id' disabled><img src='../images/unlock.png' alt='此记录未锁定操作!' width='15' height='15'>";//不可选
				}
			}
		if($midDefault==""){//首行
			//并行列
			$k=1;
			echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
			echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$ToDay</td>";			//签到日期
			$unitWidth=$tableWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$weekInfo</td>";		//结付日期
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Branch</td>";			//部门
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$GroupName</td>";	//小组
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Job</td>";				//职位
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$Name</td>";							//员工姓名
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			//并行宽
			echo"<td width='' class='A0101'>";
			$midDefault=$Number.$ToDay;
			}
		if($midDefault!="" && $midDefault==$Number.$ToDay){//同属于一个主Id，则依然输出明细表格
			$m=13;
			echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
			echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
			$unitFirst=$Field[$m]-1;
			echo"<td class='A0001' width='$unitFirst' height='20' align='right' $tdBGCOLOR>$Choose&nbsp;</td>";	//选项
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]'><div align='center'>$i</div></td>";										//序号
			//考勤楼层
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Floor</td>";//

			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$dSetTime $pbTypeSTR</td>";						//设定时间

			$m=$m+2;
			$NowC=3;
			$NowR=0;
			$tempstr="$Name-$ioTime";
			if ($tmpEstate==0){
				echo"<td class='A0001' width='$Field[$m]' align='center' ><div align='center' $Kr_bgColor>$ioTime</div></td>";//出勤时间
				}
			else{
				echo"<td class='A0001' width='$Field[$m]' align='center'  onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,$NowC,$NowR,$Id,\"$tempstr\",18)' ><div align='center' $Kr_bgColor>$ioTime</div></td>";//出勤时间
				}
			$m=$m+2;
			if($Locks==1){
				echo"<td class='A0001' width='$Field[$m]' id='text$i' onmousedown='window.event.cancelBubble=true;'	
				onclick='updateTHIS(this.id,\"CheckType\",$Id,\"$CheckType\")' 
				onDblClick='backTHIS(this.id,\"CheckType\",$Id,\"$CheckType\")'>	
				<input type='hidden' name='hField$i' value='$Id'>$CHECKSIGN</td>";
				}
			else{
				echo"<td class='A0001' width='$Field[$m]' align='center'>$CHECKSIGN</td>";//出勤状态
				}
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$UnusualInfo</td>";//异常状态
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]'>$UpdateInfo $YYY</td>";//修改意见
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$dFrom</td>";//来源
			$m=$m+2;
			echo"<td  class='A0001' width='' align='center'>$Estate</td>";//审核状态
			echo"</tr></table>";
			$k++;
			$i++;
			}
		else{
			//新行开始
			$k=1;
			//echo "k3:$k";
			echo"</td></tr></table>";//结束上一个表格
			////该员工首记录初始化
			//并行列
			echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
			echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$ToDay</td>";//签到日期
			$unitWidth=$tableWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$weekInfo</td>";//结付日期
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Branch</td>";		//部门
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$GroupName</td>";	//小组
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Job</td>";		//职位
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$Name</td>";		//员工姓名
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			//并行宽
			//echo"<td width='$unitWidth' class='A0101'>";
			echo"<td width='' class='A0101'>";
			$midDefault=$Number.$ToDay;
			echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
			echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
			$unitFirst=$Field[$m]-1;
			echo"<td class='A0001' width='$unitFirst' height='20' align='right' $tdBGCOLOR>$Choose&nbsp;</td>";//选项
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]'><div align='center'>$i</div></td>";//序号

			//考勤楼层
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Floor</td>";//

			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$dSetTime $pbTypeSTR</td>";//设定时间

			$m=$m+2;
			$NowC=3;
			$NowR=$k-1;  //相当是0
			//echo "NowR:$NowR";
			$tempstr="$Name-$ioTime";
			if ($tmpEstate==0)
			{
				echo"<td class='A0001' width='$Field[$m]' align='center' ><div align='center' $Kr_bgColor>$ioTime</div></td>";//出勤时间
			}
			else{
				echo"<td class='A0001' width='$Field[$m]' align='center'  onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,$NowC,$NowR,$Id,\"$tempstr\",18)' ><div align='center' $Kr_bgColor>$ioTime</div></td>";//出勤时间
			}
			$m=$m+2;
			if($Locks==1){
				echo"<td class='A0001' width='$Field[$m]' id='text$i' onmousedown='window.event.cancelBubble=true;'		
				onclick='updateTHIS(this.id,\"CheckType\",$Id,\"$CheckType\")' 
				onDblClick='backTHIS(this.id,\"CheckType\",$Id,\"$CheckType\")'>	
				<input type='hidden' name='hField$i' value='$Id'>$CHECKSIGN</td>";
				}
			else{
				echo"<td class='A0001' width='$Field[$m]' align='center'>$CHECKSIGN</td>";//出勤状态
				}
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$UnusualInfo</td>";//异常状态
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]'>$UpdateInfo  $YYY</td>";//修改意见
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$dFrom</td>";//来源
			$m=$m+2;
			echo"<td  class='A0001' width='' align='center'>$Estate</td>";//审核状态
			echo"</tr></table>";
			$k++;
			$i++;
			}
		}while($myRow = mysql_fetch_array($myResult));
	echo"</tr></table>";
	}
else{
	noRowInfo($tableWidth);
	}
//步骤7：
echo '</div>';
$RecordToTal=$i-1;
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script  src='../model/IE_FOX_MASK.js' type=text/javascript></script>
<script  type=text/javascript>
function webpage_read(){
	}
function AutoInducts(WebPage,From){//自动导入数据
	if (From!=""){
		From="From="+From;}
	location.href="../admin/"+WebPage+".php?"+From;

	}
</script>

<script language="JavaScript" type="text/JavaScript">

function updateJq(TableId,ColId,RowId,CheckId,runningNum,toObj){//行即表格序号;列，流水号，更新源
	//CloseDiv();
	showMaskBack();  // add by zx 加入庶影   20110323  IE_FOX_MASK.js
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	theDiv.style.visibility="hidden";
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	theDiv.style.left=event.clientX + document.body.scrollLeft;//-parseInt(theDiv.style.width)+'px';
	if(theDiv.style.visibility=="hidden" || toObj!=ObjId || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;
		document.form1.ActionColId.value=ColId;
		document.form1.ActionRowId.value=RowId;
		document.form1.A_Id.value=CheckId;
		document.form1.ObjId.value=toObj;
		switch(toObj){
			case 18://更新出勤时间  //add my zx 20100511
				InfoSTR="姓名--时间：<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly><br>新出勤时间：<input name='NewTime' type='text' id='NewTime' size='12' class='INPUT0100'>";
				break;
			}
		if(toObj>1){
			var buttonSTR="&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate()'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
			}
		infoShow.innerHTML=InfoSTR+buttonSTR;
		theDiv.className="moveRtoL";
		if (isIe()) {  //只有IE才能用   add by zx 加入庶影   20110323  IE_FOX_MASK.js
			theDiv.filters.revealTrans.apply();//防止错误
			theDiv.filters.revealTrans.play(); //播放
		}
		else{
			theDiv.style.opacity=0.9;
		}
		theDiv.style.visibility = "";
		theDiv.style.display="";
		}
	}

function CloseDiv(){
	var theDiv=document.getElementById("Jp");
	theDiv.className="moveLtoR";
	if (isIe()) {  //只有IE才能用 add by zx 加入庶影   20110323  IE_FOX_MASK.js
		theDiv.filters.revealTrans.apply();
		//theDiv.style.visibility = "hidden";
		theDiv.filters.revealTrans.play();
	}
	theDiv.style.visibility = "hidden";
	//theDiv.filters.revealTrans.play();
	infoShow.innerHTML="";
	closeMaskBack();    //add by zx 关闭庶影   20110323   add by zx 加入庶影   20110323  IE_FOX_MASK.js
	}

function aiaxUpdate(){
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	var tempRowId=document.form1.ActionRowId.value;
	var tempColId=document.form1.ActionColId.value;
	var temprunningNum=document.form1.runningNum.value;
	switch(ObjId){
		case "18":		//更新时间 add by zx 20100817
			var NewTime=document.form1.NewTime.value;
			NewTime=strtrim(NewTime);
			var checkid=document.form1.A_Id.value;

			var strs=new Array(); //定义一数组
			strs=NewTime.split(":");
			tempTime1=strs[0];
			tempTime2=strs[1];
			var isTime=0;
			//alert(NewTime+":"+tempTime1+":"+tempTime2);
			if (NewTime.length==5  &&  IsNum(tempTime1) && (tempTime1*1)<24)
			{
				if (IsNum(tempTime2) && (tempTime2*1)<60)
				{
					isTime=1;
				}
			}
			//alert(tempUnDeliveryReson);
			var tempStr="";
			var myurl="";
			//tempUnDeliveryReson=strtrim(tempUnDeliveryReson);  //除掉空格
			if(isTime==1){
				myurl="kq_checkio_updated.php?NewTime="+NewTime+":00"+"&checkid[]="+checkid+"&ActionId=UpdateTime";
				//myurl="test.php?NewTime="+NewTime+":00"+"&checkid[]="+checkid+"&ActionId=UpdateTime";
				//window.open(myurl);
				//NewTime=NewTime;
			}
			if (myurl.length>0)
			{
				//alert(myurl);
				//alert(tempRowId+":"+tempColId);
				/*
				retCode=openUrl(myurl);
				if (retCode!=-2){
				eval("ListTable"+tempTableId).rows[0].cells[tempColId].innerHTML="<DIV align='center' STYLE='overflow: hidden; ' >"+NewTime+"</DIV>";

				CloseDiv();
				}
				*/
				var ajax=InitAjax();
				ajax.open("GET",myurl,true);
				ajax.onreadystatechange =function(){
				if(ajax.readyState==4){// && ajax.status ==200
					eval("ListTable"+tempTableId).rows[0].cells[tempColId].innerHTML="<DIV align='center' STYLE='overflow: hidden; ' >"+NewTime+"</DIV>";
						CloseDiv();
						}
					}
				ajax.send(null);

			}

			break;

		}
	}

function strtrim(str){ //删除左右两端的空格
return str.replace(/(^\s*)|(\s*$)/g, "");
}

function IsNum(s)
{
    if (s!=null && s!="")
    {
        return !isNaN(s);
    }
    return false;
}
//-->
</script>
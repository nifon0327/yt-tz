<?php 
//电信-joseph
//步骤1
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=14;
$tableMenuS=500;
$sumCols="3";		//求和列
$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 我的请假记录");
$funFrom="office_qj";
$Th_Col="选项|40|序号|35|部门|60|职位|60|员工Id|45|员工姓名|60|请假开始时间|125|请假结束时间|125|班次|35|请假<br>工时|40|请假<br>类别|60|病历<br>证明|35|请假<br>原因|35|审核状态|60|退回原因|100|登记日期|70|审核|50";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,7,8";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消结付,16审核通过，17结付

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录
	$SearchRows="";
	$TempEstateSTR="EstateSTR".strval($Estate); 
	$$TempEstateSTR="selected";	
	$SearchRows.=$Estate==""?"":" and J.Estate=$Estate";
	//记录状态
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='' >全  部</option>
	<option value='1' 1>申请中</option>
	<option value='0' 0>申请通过</option>
	</select>&nbsp;";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="
SELECT J.Id,J.StartDate,J.EndDate,J.Reason,J.Proof,J.bcType,J.Date,J.Estate,J.Locks,J.Operator,M.Number,M.Name,M.KqSign,M.JobId,M.BranchId,T.Name AS Type
 FROM $DataPublic.kqqjsheet J 
	LEFT JOIN $DataPublic.staffmain M ON J.Number=M.Number 
	LEFT JOIN $DataPublic.qjtype T ON J.Type=T.Id
	WHERE 1 AND J.Number=$Login_P_Number $SearchRows order by J.StartDate DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$StartDate=$myRow["StartDate"];
		$EndDate=$myRow["EndDate"];
		$MonthTemp=substr($EndDate,0,7);
		$Reason=$myRow["Reason"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Reason]' width='18' height='18'>";
		$Type=$myRow["Type"];
		$bcType=$myRow["bcType"];
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$KqSign=$myRow["KqSign"];
		$BranchId=$myRow["BranchId"];				
		$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata where 1 and Id=$BranchId LIMIT 1",$link_id));
		$Branch=$B_Result["Name"];				
		$JobId=$myRow["JobId"];
		$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata where 1 and Id=$JobId LIMIT 1",$link_id));
		$Job=$J_Result["Name"];
		$Date=substr($myRow["Date"],0,10);		
		$Proof=$myRow["Proof"];
				
		if($Proof==1){
			$d=anmaIn("download/bjproof/",$SinkOrder,$motherSTR);
			$Proof="proof".$Id.".jpg";
			$f=anmaIn($Proof,$SinkOrder,$motherSTR);
			$Proof="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Proof="&nbsp;";
			}


		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		if($Operator==0){
			$Operator="<span class=\"redB\">未审核</span>";
			}
		else{	
			include "../model/subprogram/staffname.php";
			}
		$HoursTemp=abs(strtotime($StartDate)-strtotime($EndDate))/3600;//向上取整
		//$Days=intval($HoursTemp/24);//取整求相隔天数
		$Days=intval($HoursTemp/24);//取整求相隔天数
			//分析请假时间段包括几个休息日/法定假日/公司有薪假日
			//初始假日数
			$HolidayTemp=0;
			//分析是否有休息日
			$isHolday=0;  //0 表示工作日
			$DateTemp=$StartDate;
			//echo "开始日期:$StartDate <br>";
			$DateTemp=date("Y-m-d",strtotime("$DateTemp-1 days"));
			for($n=0;$n<=$Days;$n++){
				$isHolday=0;  //0 表示工作日
				//$DateTemp=date("Y-m-d",strtotime("$DateTemp+1 days"));
				$DateTemp=date("Y-m-d",strtotime("$DateTemp+1 days"));
				//echo "DateTemp:$DateTemp <br>";
				$weekDay=date("w",strtotime("$DateTemp"));	 
				if($weekDay==6 || $weekDay==0){
					$HolidayTemp=$HolidayTemp+1;
					//echo "$n:周六或日<br>";
					$isHolday=1;
					}
				else{
					//读取假日设定表
					$holiday_Result = mysql_query("SELECT * FROM $DataPublic.kqholiday WHERE 1 and Date=\"$DateTemp\"",$link_id);
					if($holiday_Row = mysql_fetch_array($holiday_Result)){
						$HolidayTemp=$HolidayTemp+1;
						//echo "$n:节假日<br>";
						$isHolday=1;
						}
					}
                
				//分析是否有工作日对调
				if($isHolday==1){  //节假日上班，所以其休息时间要减
					$kqrqdd_Result = mysql_query("SELECT XDate FROM $DataIn.kqrqdd WHERE XDate='$DateTemp' AND  Number='$Number'",$link_id);
					//echo "SELECT XDate FROM $DataIn.kqrqdd WHERE XDate='$DateTemp' AND  Number='$Number'";
					if($kqrqdd_Row = mysql_fetch_array($kqrqdd_Result)){
						    //echo "$n: 节假日调班 <br>";
							$HolidayTemp=$HolidayTemp-1;
					}				
				}			
				
				else{  //非节假日调班，则其休息时间要加,
					$kqrqdd_Result = mysql_query("SELECT XDate FROM $DataIn.kqrqdd WHERE GDate='$DateTemp' AND  Number='$Number'",$link_id);
					//echo "SELECT XDate FROM $DataIn.kqrqdd WHERE XDate='$DateTemp' AND  Number='$Number'";
					if($kqrqdd_Row = mysql_fetch_array($kqrqdd_Result)){
						    //echo "$n: 非节假日调班 <br>";
							$HolidayTemp=$HolidayTemp+1;
					}
			   }
               
			}
			//计算请假工时
			$Hours=($HoursTemp*10)%(24*10)/10;//求余取相隔小时数
			//如果是临时班，则按实际计算
			if($bcType==0){
				$Hours=$Hours>4?($Hours<=9?$Hours-1:8):$Hours;//相隔小时数的实际工时
				}
			$HourTotal=$Days*8-$HolidayTemp*8+$Hours;//总工时
			//echo $HourTotal;
			$HourTotal=$HourTotal<0?0:$HourTotal;   //有时假，只请半天，但调假一天，所以要去掉
			$Estate=$myRow["Estate"];
	  $LockRemark="";
		switch($Estate){
			case 0:$Estate="<div class='greenB'>通过</div>";$LockRemark="已审核通过，锁定操作;";
			break;
			case 1:$Estate="<div class='yellowB'>申请中</div>";
			break;
			case 2:
			{
				$Estate="<div class='blueB'>退回</div>";
			}
			break;
			default:
			$Estate="<div class='redB'>未通过</div>";
			break;
			}
			
			//退回原因
			$returnReasonSql = mysql_query("Select * From $DataPublic.returnreason Where tableId = '$Id' and targetTable = '$DataPublic.kqqjsheet' order by DateTime Desc Limit 1");
			$returnReasonRow = mysql_fetch_assoc($returnReasonSql);
			$returnReason = ($returnReasonRow["Reason"]=="")?"&nbsp;":$returnReasonRow["Reason"];
			
			
			$bcType=$bcType==0?"标准":"<div class=yellowB>临时</div>";
			
			$checkMonth=mysql_query("SELECT Id FROM $DataIn.kqdata WHERE Month='$MonthTemp' and Number='$Number' ORDER BY Month LIMIT 1",$link_id);
			if($checkRow = mysql_fetch_array($checkMonth)){
				$LockRemark="该月考勤统计已生成,禁止修改.";
				}
		$ValueArray=array(
			array(0=>$Branch,		1=>"align='center'"),
			array(0=>$Job, 			1=>"align='center'"),
			array(0=>$Number, 		1=>"align='center'"),
			array(0=>$Name,			1=>"align='center'"),
			array(0=>$StartDate, 	1=>"align='center'"),
			array(0=>$EndDate, 		1=>"align='center'"),
			array(0=>$bcType, 		1=>"align='center'"),
			array(0=>$HourTotal, 	1=>"align='center'"),
			array(0=>$Type,			1=>"align='center'"),
			array(0=>$Proof,		1=>"align='center'"),
			array(0=>$Reason, 		1=>"align='center'"),
			array(0=>$Estate, 		1=>"align='center'"),
			array(0=>$returnReason, 		1=>"align='center'"),
			array(0=>$Date,			1=>"align='center'"),
			array(0=>$Operator, 	1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
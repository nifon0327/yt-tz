<?php 
//二合一已更新，未使用$DataIn.电信---yang 20120801
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=16;
$tableMenuS=500;
ChangeWtitle("$SubCompany 满16周岁未满18周岁员工资料列表");
$funFrom="staff";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|员工ID|50|姓名|60|出生日期|75|年龄|50|部门|60|职位|60|考勤状态|70|移动电话|80|短号|60|邮件|40|性别|40|籍贯|40|社保|50|介绍人|50";
$ColsNumber=17;
$GradeHidden="Y";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="0,1,2,3,31,6,7,8,9,30";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$ToDate=date("Y-m-d");
$mySql="
	SELECT 
	M.Id,M.Number,M.Name,M.Grade,M.BranchId,M.JobId,M.Mail,M.ComeIn,M.Introducer,M.Estate,M.Locks,M.Date,M.Operator,S.Birthday,
	S.Sex,S.Rpr,S.Mobile,S.Dh,M.KqSign 
	FROM $DataPublic.staffmain M
	LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
	WHERE M.Estate=1 $SearchRows AND S.Birthday>DATE_SUB('$ToDate',INTERVAL 18 YEAR) ORDER BY M.BranchId,M.JobId,M.ComeIn,M.Number";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Number=$myRow["Number"];
		//年龄计算
		$Birthday =$myRow["Birthday"];
		$age = date('Y', time()) - date('Y', strtotime($Birthday)) - 1;
		if (date('m', time()) == date('m', strtotime($Birthday))){
			if (date('d', time()) > date('d', strtotime($Birthday))){
				$age++;
				}
			}
		else{
			if (date('m', time()) > date('m', strtotime($Birthday))){
			$age++;
			}
		}
		
		$Name="<a href='staff_view.php?Id=$Id' target='_blank'>$myRow[Name]</a>";
		$BranchId=$myRow["BranchId"];				
		$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata where 1 and Id=$BranchId LIMIT 1",$link_id));
		$Branch=$B_Result["Name"];				
		$JobId=$myRow["JobId"];
		$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata where 1 and Id=$JobId LIMIT 1",$link_id));
		$Job=$J_Result["Name"];
		$Grade=$myRow["Grade"]==0?"&nbsp;":$myRow["Grade"];
		$KqSign=$myRow["KqSign"];
		$KqSign=$KqSign==1?"<div class='greenB'>√</div>":($KqSign==2?"<div class='yellowB'>√</div>":"&nbsp;");
		$Mobile=$myRow["Mobile"]==""?"&nbsp;":$myRow["Mobile"];
		$Dh=$myRow["Dh"]==""?"&nbsp;":$myRow["Dh"];
		$Mail=$myRow["Mail"]==""?"&nbsp;":"<a href='mailto:$myRow[Mail]'><img src='../images/email1.gif' alt='$myRow[Mail]' width='18' height='18' border='0'></a>";
		$ComeIn=$myRow["ComeIn"];
		$Name="<span class='greenB'>$Name</span>";
		$Sex=$myRow["Sex"]==1?"男":"女";
		$Birthday=$myRow["Birthday"];
		$Rpr=$myRow["Rpr"];
		$rResult = mysql_query("SELECT Name FROM $DataPublic.rprdata WHERE Estate=1 and Id=$Rpr order by Id",$link_id);
		if($rRow = mysql_fetch_array($rResult)){
			$Rpr=$rRow["Name"];
			}
		$Idcard=$myRow["Idcard"]==""?"&nbsp;":$myRow["Idcard"];
		$sbResult = mysql_query("SELECT Id FROM $DataPublic.sbdata WHERE Number=$Number order by Id LIMIT 1",$link_id);
		if($sbRow = mysql_fetch_array($sbResult)){
			$Sb="<a href='staff_sbview.php?Number=$Number' target='_blank'>查看</a>";
			}
		else{
			$Sb="&nbsp;";
			}
		$Introducer=$myRow["Introducer"];
		$iResult = mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$Introducer order by Id",$link_id);
		if($iRow = mysql_fetch_array($iResult)){
			$Introducer=$iRow["Name"];
			}
		else{
			$Introducer="&nbsp;";
			}
		//计算在职时间
		$ThisDay=date("Y-m-d");
		$ThisEndDay=$Month."-".date("t",strtotime($ThisDay));		
		$Years=date("Y",strtotime($ThisDay))-date("Y",strtotime($ComeIn));
		$ThisMonth=date("m",strtotime($ThisDay));
		$CominMonth=date("m",strtotime($ComeIn));
		//年计算
		if($ThisMonth<$CominMonth){//计薪月份少于进公司月份
			$Years=($Years-1);
			$MonthSTR=$ThisMonth+12-$CominMonth;
			$gl_STR=$Years<=0?"&nbsp;":$Years."年";
			}
		else{
			$MonthSTR=$ThisMonth-$CominMonth;
			$gl_STR=$Years<=0?"&nbsp;":$Years."年";
			}

		//月计算
		//如果是当月，如果入职日期是3号之前，则当整月，否则不足月
		if(date("d",strtotime($ComeIn))<4){
			$MonthSTR=$MonthSTR+1;
			}
		$MonthSTR=$MonthSTR>0?$MonthSTR."个月":"";
		$gl_STR=$gl_STR.$MonthSTR;
		$Locks=$myRow["Locks"];
			$ValueArray=array(
				0=>array(0=>$Number,
						 1=>"align='center'"),
				1=>array(0=>$Name,
						 1=>"align='center'"),
				2=>array(0=>$Birthday,
						 1=>"align='center'"),
				3=>array(0=>$age,
						 1=>"align='center'"),
				4=>array(0=>$Branch,
						 1=>"align='center'"),
				5=>array(0=>$Job,
						 1=>"align='center'"),
				6=>array(0=>$KqSign,
						 1=>"align='center'"),
				7=>array(0=>$Mobile,					
						 1=>"align='center'"),
				8=>array(0=>$Dh,
						 1=>"align='center'"),
				9=>array(0=>$Mail,
						 1=>"align='center'"),
				10=>array(0=>$Sex,
						 1=>"align='center'"),
				11=>array(0=>$Rpr,
						 1=>"align='center'"),
				12=>array(0=>$Sb,					
						 1=>"align='center'"),
				13=>array(0=>$Introducer,
						 1=>"align='center'")					 
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
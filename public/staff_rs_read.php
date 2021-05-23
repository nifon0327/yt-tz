<?php 
/*电信---yang 20120801
$DataPublic.staffmain
$DataPublic.staffsheet
$DataPublic.branchdata
$DataPublic.jobdata
$DataPublic.rprdata
$DataPublic.sbdata
已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=16;
$tableMenuS=500;
ChangeWtitle("$SubCompany 员工资料列表");
$funFrom="staff";
$nowWebPage=$funFrom."_read";
if($Keys & mLOCK){
	$Th_Col="选项|40|序号|40|员工ID|50|姓名|60|身份证号码|120|部门|50|职位|60|等级|50|考勤|40|移动电话|80|短号|60|邮件|40|分机号|40|入职日期|75|在职时间|80|性别|40|籍贯|40|社保|50|入职档案|60|介绍人|50";
	$ColsNumber=19;
	$GradeHidden="";
	}
else{
	$Th_Col="选项|40|序号|40|员工ID|50|姓名|60|身份证号码|120|部门|50|职位|60|考勤|40|移动电话|80|短号|60|邮件|40|分机号|40|入职日期|75|在职时间|80|性别|40|籍贯|40|社保|50|入职档案|60|介绍人|50";
	$ColsNumber=18;
	$GradeHidden="Y";
	}
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//增加快带查询Search按钮
$searchtable="staffmain|M|Name|0|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无
include "../model/subprogram/QuickSearch.php";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理

$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="
	SELECT 
	M.Id,M.Number,M.Name,M.Grade,M.Mail,M.ExtNo,M.ComeIn,M.Introducer,M.Estate,M.Locks,M.Date,M.Operator,
	S.Birthday,S.Sex,S.Rpr,S.Idcard,S.Mobile,S.Dh,M.KqSign,B.Name AS Branch,J.Name AS Job,S.InFile
	FROM $DataPublic.staffmain M
	LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
	LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
	WHERE 1 AND M.Estate=1 $SearchRows ORDER BY M.BranchId,M.JobId,M.ComeIn,M.Number";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Number=$myRow["Number"];
		$InFile=$myRow["InFile"];
		$gl_STR="&nbsp;";
		$MonthSTR=0;
		//年龄计算
		$Birthday =$myRow["Birthday"];
		$Age = date('Y', time()) - date('Y', strtotime($Birthday)) - 1;
		if (date('m', time()) == date('m', strtotime($Birthday))){
			if (date('d', time()) > date('d', strtotime($Birthday))){
				$Age++;
				}
			}
		else{
			if (date('m', time()) > date('m', strtotime($Birthday))){
			$Age++;
			}
		}
		$ViewId=anmaIn($Id,$SinkOrder,$motherSTR);
		if($Age<18){
			$Name="<a href='staff_view.php?f=$ViewId' target='_blank'><div class='redB'>$myRow[Name]</div></a>";
			}
		else{
			$Name="<a href='staff_view.php?f=$ViewId' target='_blank'><div class='greenB'>$myRow[Name]</div></a>";
			}
		$Branch=$myRow["Branch"]==""?"-":$myRow["Branch"];
		$Job=$myRow["Job"]==""?"-":$myRow["Job"];
		$Grade=$myRow["Grade"]==0?"-":$myRow["Grade"];
		$KqSign=$myRow["KqSign"];
		$KqSign=$KqSign==1?"<div class='greenB'>√</div>":($KqSign==2?"<div class='yellowB'>√</div>":"-");
		$Mobile=$myRow["Mobile"]==""?"_":$myRow["Mobile"];
		$Dh=$myRow["Dh"]==""?"_":$myRow["Dh"];
		$Mail=$myRow["Mail"]==""?"-":"-";
		$ExtNo=$myRow["ExtNo"]==""?"-":$myRow["ExtNo"];
		$ComeIn=$myRow["ComeIn"];
		$Name="<span class='greenB'>$Name</span>";
		$Sex=$myRow["Sex"]==1?"男":"女";
		$Birthday=$myRow["Birthday"];
		$Rpr=$myRow["Rpr"];
		$Idcard=$myRow["Idcard"];
		$rResult = mysql_query("SELECT Name FROM $DataPublic.rprdata WHERE Estate=1 and Id=$Rpr order by Id",$link_id);
		if($rRow = mysql_fetch_array($rResult)){
			$Rpr=$rRow["Name"];
			}
		$Idcard=$myRow["Idcard"]==""?"-":$myRow["Idcard"];
		$sbResult = mysql_query("SELECT Id FROM $DataPublic.sbdata WHERE Number=$Number order by Id LIMIT 1",$link_id);
		if($sbRow = mysql_fetch_array($sbResult)){
			$ViewNumber=anmaIn($Number,$SinkOrder,$motherSTR);
			$Sb="<a href='staff_sbview.php?f=$ViewNumber' target='_blank'>查看</a>";
			}
		else{
			$Sb="-";
			}
		$Introducer=$myRow["Introducer"];
		$iResult = mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$Introducer order by Id",$link_id);
		if($iRow = mysql_fetch_array($iResult)){
			$Introducer=$iRow["Name"];
			}
		else{
			$Introducer="-";
			}
//////////////////////////////////////////////
		//工龄计算
		include "subprogram/staff_model_gl.php";
//////////////////////////////////////////////
		if($InFile==1){
			$FileName="I".$Number.".pdf";
			$tf=anmaIn($FileName,$SinkOrder,$motherSTR);
			$td=anmaIn("download/staffPhoto/",$SinkOrder,$motherSTR);			
			$InFile="<span onClick='OpenOrLoad(\"$td\",\"$tf\",6)' style='CURSOR: pointer;' class='yellowB'>查看</span>";
			}
		else{
			$InFile="-";
			}
		$Locks=$myRow["Locks"];
			$ValueArray=array(
				array(0=>$Number,1=>"align='center'"),
				array(0=>$Name,1=>"align='center'"),
				array(0=>$Idcard,1=>"align='center'"),
				array(0=>$Branch,1=>"align='center'"),
				array(0=>$Job,1=>"align='center'"),
				array(0=>$Grade,1=>"align='center'",4=>$GradeHidden),
				array(0=>$KqSign,1=>"align='center'"),
				array(0=>$Mobile,1=>"align='center'"),
				array(0=>$Dh,1=>"align='center'"),
				array(0=>$Mail,1=>"align='center'"),
				array(0=>$ExtNo,1=>"align='center'"),
				array(0=>$ComeIn,1=>"align='center'"),
				array(0=>$Gl_STR,1=>"align='center'"),
				array(0=>$Sex,1=>"align='center'"),
				array(0=>$Rpr,1=>"align='center'"),
				array(0=>$Sb,1=>"align='center'"),
				array(0=>$InFile,1=>"align='center'"),
				array(0=>$Introducer,1=>"align='center'")					 
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
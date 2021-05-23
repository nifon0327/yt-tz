<?php 
//电信-EWEN
//代码共享，ＭＣ未使用-EWEN 2012-08-19
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=550;
ChangeWtitle("$SubCompany 部门小组-加工类配件关系表");
$funFrom="group_stuff";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|30|所属小组|100|配件名|300|更新日期|80|操作员|50";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4";
include "../model/subprogram/read_model_3.php";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.GroupId,A.StuffId,A.Date,A.Operator,C.StuffCname,B.GroupName 
FROM $DataIn.group_stuff A
LEFT JOIN $DataIn.staffgroup B ON B.GroupId=A.GroupId
LEFT JOIN $DataIn.stuffdata C ON C.StuffId=A.StuffId
WHERE 1 $SearchRows ORDER BY A.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$GroupId=$myRow["GroupId"];
		$StuffCname=$myRow["StuffCname"];
		$StuffId=$myRow["StuffId"];
		$GroupName=$myRow["GroupName"];
		$Date=substr($myRow["Date"],0,10);
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$GroupName,1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$Date,	1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth,"");
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>

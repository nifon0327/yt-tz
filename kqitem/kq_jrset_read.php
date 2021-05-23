<?php 
//电信-EWEN
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 假日列表");
$funFrom="kq_jrset";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|50|假日名称|100|日期|130|类型|130|是否有薪|80|加班薪酬|120|操作员|60";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理$SearchRows
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT H.Id,H.Name,H.Date,H.jbTimes,H.Sign,H.Type,H.Locks,H.Operator
FROM $DataPublic.kqholiday H WHERE 1 $SearchRows order by H.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$Date=$myRow["Date"];
		$Sign=$myRow["Sign"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</di>";
		switch($myRow["Type"]){
			case 2:				$Type="法定假期";				break;
			case 1:				$Type="有薪假期";				break;
			default:			$Type="无薪假期";				break;
			}
		$jbTimes=$myRow["jbTimes"];
		$JbSTR="加班".$jbTimes."倍";
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		//强制锁定已统计的记录
		$MonthTemp=substr($Date,0,7);
		$LockRemark="";
		$checkMonth=mysql_query("SELECT Id FROM $DataIn.kqdata WHERE Month='$MonthTemp' ORDER BY Month",$link_id);
		if($checkRow = mysql_fetch_array($checkMonth)){
			$LockRemark="该月考勤统计已生成,禁止修改.";
			}
		$ValueArray=array(
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$Date,		1=>"align='center'"),
			array(0=>$Type,		1=>"align='center'"),
			array(0=>$Sign, 	1=>"align='center'"),
			array(0=>$JbSTR,	1=>"align='center'"),
			array(0=>$Operator,	1=>"align='center'")
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
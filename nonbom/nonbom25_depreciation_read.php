<?php 
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 折旧期设置");
$funFrom="nonbom25_depreciation";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|60|序号|30|折旧期(月)|60|显示名称|100|Keys|80|备注|350|状态|40|更新日期|80|操作员|80";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="2,3,4,5,6";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$DefaultBgColor=$theDefaultColor;
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT * FROM $DataPublic.nonbom6_depreciation Ａ WHERE 1 $SearchRows ";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Depreciation= $myRow["Depreciation"];
		$dValues= $myRow["dValues"];
		$ListName=$myRow["ListName"];
		$Remark= $myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$Depreciation, 	1=>"align='center'"),
			array(0=>$ListName, 	1=>"align='center'"),
			array(0=>$dValues, 	1=>"align='center'"),
			array(0=>$Remark, 	1=>"align='left'"),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Date,	1=>"align='center'"),
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
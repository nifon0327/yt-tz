<?php 
//代码、数据库共享-EWEN 2012-09-17
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=8;
$tableMenuS=400;
ChangeWtitle("$SubCompany 门禁指令设置");
$funFrom="mj_order";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|开关名称|100|开门指令|200|关门指令|200|状态|40|更新日期|100|操作员|60";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,5,6,7,8";
//步骤3：
$nowWebPage=$funFrom."_read";
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
$mySql="SELECT * FROM $DataPublic.accessguard_order A WHERE 1 $SearchRows ORDER BY A.Estate DESC,A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$SharingShow="Y";
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$OpenKeys=$myRow["OpenKeys"]==""?"&nbsp;":$myRow["OpenKeys"];
		$CloseKeys=$myRow["CloseKeys"]==""?"&nbsp;":$myRow["CloseKeys"];
		$Estate=$myRow["Estate"]==1?"<span class='greenB'>√</span>":"<span class='redB'>×</span>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include"../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$Name,1=>"align='center'"),
			array(0=>$OpenKeys,1=>"align='center'"),
			array(0=>$CloseKeys,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
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
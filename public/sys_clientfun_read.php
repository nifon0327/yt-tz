<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=8;
$tableMenuS=600;
ChangeWtitle("$SubCompany 用户页面功能模块列表");
$funFrom="sys_clientfun";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|位置|40|功能ID|60|功能名称|180|参数|200|描述|300|状态|40";

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
$mySql="SELECT A.Id,A.Oby,A.ModuleId,A.ModuleName,A.Parameter,A.Remark,A.Estate,A.Locks FROM $DataIn.sys_clientfunmodule A WHERE 1 $SearchRows ORDER BY A.Estate DESC,A.Oby,A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Oby=$myRow["Oby"];
		$ModuleId=$myRow["ModuleId"];
		$ModuleName=$myRow["ModuleName"];
		$Parameter=$myRow["Parameter"]==""?"&nbsp;":$myRow["Parameter"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Locks=$myRow["Locks"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$Oby,		1=>"align='center'"),
			array(0=>$ModuleId,		1=>"align='center'"),
			array(0=>$ModuleName),
			array(0=>$Parameter),
			array(0=>$Remark),
			array(0=>$Estate,		1=>"align='center'")
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
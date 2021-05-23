<?php 
//电信-ZX  2012-08-01
//代码共享-EWEN 2012-08-14
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=9;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 部门小组列表");
$funFrom="rs_group";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|50|部门名称|60|小组编号|60|小组名称|100|组长|60|组长ID|60|生产分类|80|可用|60|更新日期|100|操作员|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8";

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
$mySql="SELECT A.Id,A.GroupId,A.GroupName,A.Date,A.Estate,A.Locks,A.Operator,B.Name AS Branch,D.Name AS GroupLeader,D.Number,C.TypeName
FROM $DataIn.staffgroup A
LEFT JOIN $DataPublic.branchdata B ON B.Id=A.BranchId
LEFt JOIN $DataIn.stufftype C ON C.TypeId=A.TypeId
LEFT JOIN $DataPublic.staffmain D ON D.Number=A.GroupLeader
 WHERE 1 $SearchRows ORDER BY A.Estate DESC,A.BranchId,A.GroupId";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Branch=$myRow["Branch"];
		$GroupId=$myRow["GroupId"];
		$GroupName=$myRow["GroupName"];
		$GroupLeader=$myRow["GroupLeader"];
		$Number=$myRow["Number"];
		$TypeName=$myRow["TypeName"]==""?"&nbsp;":$myRow["TypeName"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$Branch, 	1=>"align='center'"),
			array(0=>$GroupId,		1=>"align='center'"),
			array(0=>$GroupName,		1=>"align='center'"),
			array(0=>$GroupLeader, 	1=>"align='center'"),
			array(0=>$Number, 	1=>"align='center'"),
			array(0=>$TypeName, 	1=>"align='center'"),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Date,	 	1=>"align='center'"),
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
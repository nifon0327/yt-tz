<?php 
//电信-joseph
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=8;
$tableMenuS=400;
ChangeWtitle("$SubCompany 特殊功能列表");
$funFrom="taskdata";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|标识|60|功能ID|50|功能名称|130|描述|200|列号|50|排序号|50|特别参数|290|状态|40|权限设置|60";
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
$mySql="SELECT * FROM $DataPublic.tasklistdata A WHERE 1 $SearchRows ORDER BY A.Estate DESC,A.TypeId,A.InCol,A.Oby";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$SharingShow="Y";
	do{
		$m=1;
		$Id=$myRow["Id"];
		$ItemId=$myRow["ItemId"];
		$Title=$myRow["Title"];
		$Description=$myRow["Description"]."&nbsp;";
		$Extra=$myRow["Extra"]."&nbsp;";
		$InCol=$myRow["InCol"];
		$Oby=$myRow["Oby"];
		$Estate=$myRow["Estate"]==1?"<span class='greenB'>√</span>":"<span class='redB'>×</span>";
		$Operator=$myRow["Operator"];
		include"../model/subprogram/staffname.php";
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$cSign,1=>"align='center'"),
			array(0=>$ItemId,1=>"align='center'"),
			array(0=>$Title),
			array(0=>$Description),
			array(0=>$InCol,1=>"align='center'"),
			array(0=>$Oby,1=>"align='center'"),
			array(0=>$Extra),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>"<a href='taskdata_set.php?ItemId=$ItemId'>设置</a>",1=>"align='center'")
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

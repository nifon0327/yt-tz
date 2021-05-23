<?php 
//电信-yang 20120801
//代码、数据库共享-EWEN
include "../model/subprogram/s1_model_1.php";
$Th_Col="选项|40|序号|40|功能ID|60|功能名称|120|功能类型|100|连接参数|300|状态|40|更新日期|80|操作|60";
$ColsNumber=8;
$tableMenuS=600;
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
//查询条件的参数	
if($Action==1){
	$sSearch=" AND A.TypeId<5";
	}
else{
	if($Tid==4){
		$sSearch=" AND A.TypeId=5";
		}
	else{
		$sSearch=" AND A.TypeId=4";
		}
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
$SearchSTR=0;//不查询
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.cSign,A.TypeId,A.ModuleId,A.ModuleName,A.Parameter,A.OrderId,A.Locks,A.Date,A.Estate,A.Operator
FROM $DataPublic.funmodule A 
LEFT JOIN $DataPublic.modulenexus B ON B.dModuleId=A.ModuleId
WHERE A.Estate=1 $sSearch AND B.ModuleId IS NULL ORDER BY A.TypeId,A.ModuleId,A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$ModuleId=$myRow["ModuleId"];
		$ModuleName=$myRow["ModuleName"];
		$Parameter=$myRow["Parameter"]==""?"&nbsp;":$myRow["Parameter"];
		$OrderId=$myRow["OrderId"];
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$cSignFrom=$myRow["cSign"];
		include "../model/subselect/cSign.php";
		$TypeId=$myRow["TypeId"];
		$checkidValue="$ModuleId^^$ModuleName^^$TypeId";
		$TypeFrom=$TypeId;
		include "../model/subselect/funmoduleType.php";
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$cSign,1=>"align='center'"),
			array(0=>$ModuleId,1=>"align='center'"),
			array(0=>$ModuleName),
			array(0=>$TypeName,1=>"align='center'"),
			array(0=>$Parameter,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
			);
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
?>
<?php 
//电信-ZX
//代码共享-EWEN 2012-08-14
include "../model/subprogram/s1_model_1.php";
$Th_Col="选项|40|序号|40|功能ID|60|功能名称|120|位置|100|连接参数|300|状态|40|更新日期|80|操作|60";
$ColsNumber=8;
$tableMenuS=600;
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;
//每页默认记录数量
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
//查询条件的参数	
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
$SearchSTR=0;//不查询
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
A.Id,A.ModuleId,A.ModuleName,A.Parameter,A.OrderId,A.Locks,A.Date,A.Estate,A.Operator,A.Place
FROM $DataPublic.sc4_funmodule A 
LEFT JOIN $DataPublic.sc4_modulenexus B ON B.dModuleId=A.ModuleId
WHERE A.Estate=1 AND A.Place=2 AND B.dModuleId IS NULL  ORDER BY A.Place,A.ModuleId,A.Id";
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
		$Place=$myRow["Place"];
		$checkidValue="$ModuleId^^$ModuleName^^$Place";
		$Place="子项目";
		$ValueArray=array(
			array(0=>$ModuleId,1=>"align='center'"),
			array(0=>$ModuleName),
			array(0=>$Place,1=>"align='center'"),
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
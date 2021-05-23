<?php 
//$DataIn.电信---yang 20120801
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=8;
$tableMenuS=400;
ChangeWtitle("$SubCompany 系统功能项目点击统计");
$funFrom="sys_clicktotal";
$From=$From==""?"read":$From;
$Th_Col="选项|50|序号|50|功能位置|100|功能ID|60|功能名称|150|点击率|60|图例|60";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
//$ActioToS="1,2,3,4,5,6,7,8";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消
//$ActioToS="1";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//必选,过滤条件
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="
SELECT TotalSum,ComeFrom,FunctionId,ModuleName FROM(
SELECT count(*) AS TotalSum,'桌面功能项目' AS ComeFrom,C.FunctionId,T.Title AS ModuleName
FROM $DataPublic.sys7_clicktotal C
LEFT JOIN $DataPublic.tasklistdata T ON T.ItemId=C.FunctionId
WHERE 1 AND C.ComeFrom=0 GROUP BY C.FunctionId
UNION ALL
SELECT count(*) AS TotalSum,'右侧子项目' AS ComeFrom,C.FunctionId,T.ModuleName
FROM $DataPublic.sys7_clicktotal C
LEFT JOIN $DataPublic.funmodule T ON T.ModuleId=C.FunctionId
WHERE 1 AND C.ComeFrom=1 GROUP BY C.FunctionId
)A ORDER BY ComeFrom DESC,TotalSum DESC
";

//echo $mySql;

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$TotalSum=$myRow["TotalSum"];
		$ComeFrom=$myRow["ComeFrom"];
		$FunctionId=$myRow["FunctionId"];
		$ModuleName=$myRow["ModuleName"];
		$ValueArray=array(
			array(0=>$ComeFrom),
			array(0=>$FunctionId,
					 1=>"align='center'"),
			array(0=>$ModuleName),
			array(0=>$TotalSum,
					 1=>"align='center'"),
			array(0=>"查看",
					 1=>"align='center'")
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
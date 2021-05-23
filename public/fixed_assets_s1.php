<?php 
/*
$DataIn.usertable
$DataPublic.staffmain
$DataPublic.staffsheet
二合一已更新
//电信-joseph
*/
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|40|序号|40|员工ID|50|姓名|80|部门|80";
$ColsNumber=16;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$SearchSTR=0;//不需查询功能
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
//echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo "&nbsp;";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT M.Id,M.Number,M.Name, B.Name AS Branch
	FROM $DataPublic.staffmain M 
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId	
	WHERE 1 AND M.Estate='1' ORDER BY M.BranchId,M.JobId,M.ComeIn,M.Number";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];		
		$Branch=$myRow["Branch"];
		$Locks=1;
		$checkidValue=$Number."^^".$Name;
		$ValueArray=array(
			array(0=>$Number,
					 1=>"align='center'"),
			array(0=>$Name,
					 1=>"align='center'"),
			array(0=>$Branch,
					 1=>"align='center'"),
			);
		include "../model/subprogram/s1_model_6.php";
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
<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.redeployj
$DataPublic.staffmain
$DataPublic.jobdata
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=10;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 排班记录");
$funFrom="Rs_pbSheet";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|50|员工ID|60|员工姓名|100|班次|100|操作员|80";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";
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
$mySql="SELECT J.Id,J.Number,M.Name,C.Name as pbName,J.Operator
	FROM $DataIn.pbSetSheet J 
	LEFT JOIN $DataPublic.staffmain M ON M.Number=J.Number 
	Left Join $DataPublic.pbSheet C On C.Id = J.pbType
	WHERE 1 $SearchRows ORDER BY J.Id DESC";

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		
		$Id = $myRow["Id"];
		$Number = $myRow["Number"];
		$Name = $myRow["Name"];
		$pbName = $myRow["pbName"];
		$Operator = $myRow["Operator"];
		include "../model/subprogram/staffname.php";
		
		$ValueArray=array(
			array(0=>$Number,		1=>"align='center'"),
			array(0=>$Name,			1=>"align='center'"),
			array(0=>$pbName, 		1=>"align='center'"),
			array(0=>$Operator,		1=>"align='center'")
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
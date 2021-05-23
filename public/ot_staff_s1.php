<?php 
//电信-joseph
//代码共享-EWEN 2012-08-13
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|100|序号|100|编号|100|姓名|100|登记日期|120";
$ColsNumber=5;
$tableMenuS=400;
$Page_Size = 100;							//每页默认记录数量
$Parameter.=",Bid,$Bid,Jid,$Jid,Kid,$Kid,Month,$Month";
//非必选,过滤条件
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
$SearchSTR=0;
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
	A.Id,A.Number,A.Name,A.Date
	FROM $DataIn.ot_staff A
	LEFT JOIN $DataIn.usertable B ON B.Number=A.Number AND B.uType='4'
	WHERE B.Number IS NULL ";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];		
		$Date=$myRow["Date"];
		$checkidValue=$Number."^^".$Name;
		$Name="<span class='greenB'>$Name</span>";
		$Locks=1;
		$ValueArray=array(
			array(0=>$Number,		1=>"align='center'"),
			array(0=>$Name, 		1=>"align='center'"),
			array(0=>$Date,	1=>"align='center'")
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
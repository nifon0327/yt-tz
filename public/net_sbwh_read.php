<?php 
//电信-ZX  2012-08-01
/*
已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=7;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 设备维护");
$funFrom="net_sbwh";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|40|项目|150|连接|60|状态|50|更新日期|80|操作员|60";
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
$mySql="SELECT * FROM $DataPublic.net2_sbwh WHERE 1 $SearchRows ORDER BY Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$Name=$myRow["Name"];
		$LinkAdd="<a href='$myRow[LinkAdd]' target='_black'>登录</a>";
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";		$ValueArray=array(
			array(0=>$Name, 	1=>"align='center'"),
			array(0=>$LinkAdd,		1=>"align='center'"),
			array(0=>$Estate,	 	1=>"align='center'"),
			array(0=>$Date),
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

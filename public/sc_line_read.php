<?php 
//**************步骤1：初始化数据
include "../model/modelhead.php";
$From=$From==""?"read":$From;
$ColsNumber=9;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 拉线列表");
$funFrom="sc_line";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|50|拉线名称|80|所属小组|80|可用|60|更新日期|100|操作员|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8";

//**************步骤3：
include "../model/subprogram/read_model_3.php";
//**************步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
//**************步骤5：
include "../model/subprogram/read_model_5.php";
//**************步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.GroupId,B.GroupName,A.Date,A.Estate,A.Locks,A.Operator,A.LineName
FROM $DataIn.sc_line A
LEFT JOIN $DataIn.staffgroup B ON B.GroupId=A.GroupId
 WHERE 1 $SearchRows ORDER BY A.GroupId";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$GroupId=$myRow["GroupId"];
		$GroupName=$myRow["GroupName"];
		$LineName=$myRow["LineName"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$LineName,		1=>"align='center'"),
			array(0=>$GroupName, 	1=>"align='center'"),
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
//**************步骤7：
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
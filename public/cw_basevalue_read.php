<?php 
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 财务基本参数列表");
$funFrom="cw_basevalue";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|50|参数说明|200|参数代码|60|参数值|80|状态|50|更新日期|120|操作|80";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,5,6,7,8";

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
$mySql="SELECT A.Id,A.ValueCode,A.Remark,A.Value,A.Estate,A.Locks,A.Date,A.Operator FROM $DataPublic.cw3_basevalue A WHERE 1 $SearchRows ORDER BY A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Remark=$myRow["Remark"];
		$ValueCode=$myRow["ValueCode"];
		$Value=$myRow["Value"];	
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Estate=$myRow["Estate"]==0?"<div class='redB'>×</div>":"<div class='greenB'>√</div>";
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$ValueArray=array(
			array(0=>$Remark,1=>"align='center'"),
			array(0=>$ValueCode,1=>"align='center'"),
			array(0=>$Value,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
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

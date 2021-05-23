<?php 
//电信-ZX  2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=10;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 模板文件列表");
$funFrom="work_modelfile";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|50|模板说明|450|模板下载|60|状态|50|更新日期|80|操作员|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8";
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
$mySql="SELECT A.Id,A.Note,A.Attached,A.Date,A.Estate,A.Locks,A.Operator FROM $DataPublic.workmodelfile A WHERE 1 $SearchRows ORDER BY A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$FileDir=download."/modelfile/";
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Note=$myRow["Note"];
		$Attached=$myRow["Attached"];
		if($Attached!=""){		//有附件则加密
			$Dir=anmaIn($FileDir,$SinkOrder,$motherSTR);
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<a href=\"openorload.php?d=$Dir&f=$Attached&Type=&Action=6\" target=\"download\"><img src='../images/down.gif' alt='点击下载' width='18' height='18'></a>";
			}
		else{
			$Attached="-";
			}
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$Note),
			array(0=>$Attached,	1=>"align='center'"),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Date,		1=>"align='center'"),
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
<?php 
//电信-EWEN
//代码共享-EWEN 2012-08-14
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=10;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 职位列表");
$funFrom="jobset";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|职位ID|40|使用标识|60|职位名称|60|主管|80|职责内容|250|工作时间|200|可用|40|更新日期|80|操作员|50";
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
$mySql="SELECT A.Id,A.Name,A.WorkNote,A.WorkTime,A.Estate,A.Locks,A.Date,A.Operator,A.cSign,B.LeaderNumber 
	FROM $DataPublic.jobdata A 
	LEFT JOIN $DataIn.jobmanager B ON B.JobId=A.Id
	WHERE 1 $SearchRows  ORDER BY  A.Estate DESC,A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
$SharingShow="Y";//显示共享
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$WorkNote=$myRow["WorkNote"]==""?"&nbsp;":$myRow["WorkNote"];
		$WorkTime=$myRow["WorkTime"]==""?"&nbsp;":$myRow["WorkTime"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$Operator=$myRow["LeaderNumber"];
		include "../model/subprogram/staffname.php";
		$LeaderNumber=$Operator;
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
        $cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$ValueArray=array(
			array(0=>$Id,1=>"align='center'"),
            array(0=>$cSign,1=>"align='center'"),
			array(0=>$Name,1=>"align='center'"),
			array(0=>$LeaderNumber,1=>"align='center'"),
			array(0=>$WorkNote),
			array(0=>$WorkTime),
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
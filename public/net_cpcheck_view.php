<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.net_cpdata
$DataPublic.net_cpcheckdiary
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=7;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 维护日志");
$funFrom="net_cpcheck";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|40|电脑名称|70|领用人|50|维护日期|70|维护内容|300|维护评述|200";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT C.Id,C.hdId,C.Remark,C.Opinion,C.Locks,C.Date,C.Operator,D.CpName,M.Name AS User
FROM $DataPublic.net_cpcheckdiary C,
$DataPublic.net_cpdata D,$DataPublic.staffmain M
 WHERE 1 AND C.hdId='$hdId' AND D.Id=C.hdId AND M.Number=D.User ORDER BY C.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$CpName=$myRow["CpName"];
		$User=$myRow["User"];
		$Date=$myRow["Date"];
		$Remark=nl2br($myRow["Remark"]);
		$Opinion=nl2br($myRow["Opinion"]);
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$CpName, 	1=>"align='center'"),
			array(0=>$User,		1=>"align='center'"),
			array(0=>$Date,	 	1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$Opinion,	1=>"align='center'")
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

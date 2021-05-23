<?php 
//电信-EWEN
//代码、数据共享-EWEN 2012-08-14
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=7;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 等级岗位津贴列表");
$funFrom="gradesubsidy";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|50|等级|100|岗位津贴|100|状态|100|更新日期|150|操作|80";
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
$mySql="SELECT A.Id,A.Grade,A.Subsidy,A.Date,A.Estate,A.Locks,A.Operator FROM $DataPublic.gradesubsidy A WHERE 1 $SearchRows order by A.Grade DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$Grade=$myRow["Grade"];
		$Subsidy=$myRow["Subsidy"];
		$Date=$myRow["Date"];				
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Estate=$myRow["Estate"]==0?"<div class='redB'>×</div>":"<div class='greenB'>√</div>";
		$Locks=$myRow["Locks"];		
		$ValueArray=array(
			array(0=>$Grade,1=>"align='center'"),
			array(0=>$Subsidy,1=>"align='center'"),
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

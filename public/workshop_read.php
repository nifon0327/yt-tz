<?php 
//ewen 2013-03-20 OK
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数		
$ColsNumber=8;			
$tableMenuS=600;
ChangeWtitle("$SubCompany 生产单位设置");
$funFrom="workshop";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|30|ID|40|车间名称|100|地点|60|楼层|60|负责人|60|备注|300|可用|60|更新日期|80|操作员|80";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,5,6";//,4,5,6,7,8

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$DefaultBgColor=$theDefaultColor;
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.*,B.Name AS WrokAdd,M.Name AS LeaderName
        FROM $DataPublic.workshopdata A 
        LEFT JOIN $DataPublic.staffworkadd B ON B.Id=A.WorkAddId 
        LEFT JOIN $DataPublic.staffmain M ON M.Number=A.LeaderNumber 
        WHERE 1 $SearchRows ORDER BY A.Estate DESC,A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$WrokAdd=$myRow["WrokAdd"];
		$Floor=$myRow["Floor"];
		$Remark=$myRow["Remark"];
		
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		$LeaderName=$myRow["LeaderName"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
		    array(0=>$Id, 	1=>"align='center'"),
			array(0=>$Name, 	1=>"align='center'"),
			array(0=>$WrokAdd, 	1=>"align='center'"),
			array(0=>$Floor,	1=>"align='center'"),
			array(0=>$LeaderName,	1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Date,	1=>"align='center'"),
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
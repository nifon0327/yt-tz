<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.net_cpdata
$DataPublic.staffmain
$DataPublic.net_cpsfdata
$DataPublic.net_cpcheckdiary
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=7;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 软件列表");
$funFrom="net_cpsoftware";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|40|软件/驱动名称|350|类型|100|许可状态|60|可用状态|60|日期|80|操作员|60";
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
$mySql="SELECT S.Id,S.Name,S.Date,S.Sign,S.Estate,S.Locks,S.Operator,T.Name AS Type
 FROM $DataPublic.net_softwarelist S
 LEFT JOIN $DataPublic.net_softwaretype T ON T.Id=S.Type WHERE 1 $SearchRows ORDER BY S.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$Name=$myRow["Name"];
		$Type=$myRow["Type"];
		$Sign=$myRow["Sign"]==1?"<span class='greenB'>√</span>":"<span class='yellowB'>○</span>";
		$Estate=$myRow["Estate"]==1?"<span class='greenB'>√</span>":"<span class='redB'>×</span>";
		$Date=$myRow["Date"];		
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$Name,),
			array(0=>$Type,		1=>"align='center'"),
			array(0=>$Sign,	1=>"align='center'"),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Date, 	1=>"align='center'"),
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

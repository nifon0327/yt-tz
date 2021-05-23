<?php 
//$DataPublic.ot1_service 二合一已更新
//电信-joseph
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=9;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 报修登记");
$funFrom="ot_service";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|报修日期|80|申请人|60|报修事项|300|处理人|60|报修状况|200|备注|40|处理<br>状态|40";
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
List_Title($Th_Col,"1",1);
$mySql="SELECT B.Id,B.Remark1,B.Remark2,B.Remark3,B.Estate,B.Locks,B.Date,B.Operator,M.Name AS Servicer FROM $DataPublic.ot1_service B,$DataPublic.staffmain M WHERE 1 AND B.Servicer=M.Number $SearchRows ORDER BY B.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Remark1=$myRow["Remark1"];
		$Remark2=$myRow["Remark2"]==""?"&nbsp;":nl2br($myRow["Remark2"]);
		$Remark3=$myRow["Remark3"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Remark3]' width='18' height='18'>";
		$Servicer=$myRow["Servicer"];
		$Estate=$myRow["Estate"];
		switch($Estate){
			case 1:$Estate="<div class='redB' title='未处理'>×</div>";break;
			case 2:$Estate="<div class='yellowB' title='处理中...'>√</div>";break;
			case 0:$Estate="<div class='greenB' title='已处理'>√</div>";break;
			}
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$Date, 		1=>"align='center'"),
			array(0=>$Operator,		1=>"align='center'"),
			array(0=>$Remark1),
			array(0=>$Servicer, 	1=>"align='center'"),
			array(0=>$Remark2),
			array(0=>$Remark3,		1=>"align='center'"),
			array(0=>$Estate, 		1=>"align='center'")
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
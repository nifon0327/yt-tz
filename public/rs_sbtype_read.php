<?php 
//代码、数据库共享-EWEN
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 社保参数设定");
$funFrom="rs_sbtype";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|50|社保类型|150|公司缴费|80|个人缴费|80|备注|200|更新日期|100|操作|80";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="2,3,4";

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
$mySql="SELECT A.Id,A.Name,A.cAmount,A.mAmount,A.Remark,A.Date,A.Operator FROM $DataIn.rs_sbtype A  ORDER BY A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
        $cAmount=$myRow["cAmount"];
		$mAmount=$myRow["mAmount"];
        $Remark=$myRow["Remark"];
		$Date=$myRow["Date"];	
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$Name,1=>"align='center'"),
			array(0=>$cAmount,1=>"align='center'"),
			array(0=>$mAmount,1=>"align='center'"),
			array(0=>$Remark,1=>"align='center'"),
			array(0=>$Date,	1=>"align='center'"),
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

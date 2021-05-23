<?php 
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=11;				
$tableMenuS=550;
ChangeWtitle("$SubCompany 车辆维修记录列表");
$funFrom="car_repair";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|40|使用公司|60|车辆类型|60|维修车辆|60|维修人|60|维修费用|60|维修时间|80|原因|300|";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4";
$SearchRows="";
//步骤3：
include "../model/subprogram/read_model_3.php";

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT * FROM $DataPublic.car_repair A WHERE 1 $SearchRows ORDER BY A.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{	
	   $m=1;
	   $Id=$myRow["Id"];
	   $CarId=$myRow["CarId"];
	   $CarSql=mysql_query("SELECT * FROM $DataPublic.cardata WHERE Id=$CarId");
	   if($CarResult=mysql_fetch_array($CarSql)){
	   		$CarNo=$CarResult["CarNo"];
			$cSignFrom=$CarResult["cSign"];
			require"../model/subselect/cSign.php";
			$TypeFrom=$CarResult["TypeId"];
			require "../model/subselect/CarType.php";
	   		}
	   $Reperson=$myRow["Reperson"];
	   $Recharge=$myRow["Recharge"];
	   $Redate=$myRow["Redate"];
	   $Rereason=$myRow["Rereason"];
	   if($Reason=="")$Reason="&nbsp;";
	   $ValueArray=array(
	     	array(0=>$cSign,1=>"align='center'"),
			array(0=>$TypeName,1=>"align='center'"),
			array(0=>$CarNo,1=>"align='center'"),
			array(0=>$Reperson,1=>"align='center'"),
			array(0=>$Recharge,1=>"align='center'"),
			array(0=>$Redate,1=>"align='center'"),
			array(0=>$Rereason),

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

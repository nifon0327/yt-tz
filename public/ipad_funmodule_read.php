<?php 
//代码、数据库共享-zx
//电信-ZX
//代码共享-EWEN 2012-08-14
include "../model/modelhead.php";
$ColsNumber=8;
$tableMenuS=600;
ChangeWtitle("$SubCompany ipad功能模块列表");
$funFrom="ipad_funmodule";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|功能标识|60|功能ID|60|功能名称|120|位置|100|连接参数|300|状态|40|更新日期|80|操作|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8";
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows ="";
      //选择公司名称
      $SelectFrom=1;
      $cSignTB="A";
      $SharingShow="Y";
      include "../model/subselect/cSign.php";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT * FROM $DataPublic.sc4_funmodule A WHERE 1 $SearchRows ORDER BY A.Estate DESC,A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$ModuleId=$myRow["ModuleId"];
		$ModuleName=$myRow["ModuleName"];
		$Place=$myRow["Place"]==1?"主项目":"子项目";
		$Parameter=$myRow["Parameter"]==""?"&nbsp;":$myRow["Parameter"];
		$OrderId=$myRow["OrderId"];
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Operator=$myRow["Operator"];
		include"../model/subprogram/staffname.php";
		$cSignFrom=$myRow["cSign"];
		include "../model/subselect/cSign.php";
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$cSign,1=>"align='center'"),			  
			array(0=>$ModuleId,1=>"align='center'"),
			array(0=>$ModuleName),
			array(0=>$Place),
			array(0=>$Parameter),
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
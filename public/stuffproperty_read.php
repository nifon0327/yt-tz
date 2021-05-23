<?php 
//电信---yang 20120801
//代码、数据共享-EWEN 2012-08-17
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=9;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 配件属性设置");
$funFrom="stuffproperty";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|40|ID|40|属性名称|80|属性类型|70|图示|40|文字颜色|100|备注|250|可用|60|操作员|80";
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
$DefaultBgColor=$theDefaultColor;
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.*,B.Name AS MainTypeName,A.ActionId FROM $DataIn.stuffpropertytype A
        LEFT JOIN $DataPublic.stuffpropertymaintype B ON B.Id=A.MainType 
        WHERE 1 $SearchRows ORDER BY A.Estate DESC,A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$TypeColor=$myRow["TypeColor"];
		//$idx=200+$Id;
		$TypeIcon="<img src='../images/gys$Id.png' width='20'/>";
		
		$TypeName="<div style='color:$TypeColor;font-weight:bold;'>" . $myRow["TypeName"] . "</div>";
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		$MainTypeName=$myRow["MainTypeName"];
		$ActionId=$myRow["ActionId"];
		$ActionName=$myRow["ActionName"]==""?($myRow["MainType"]==1?"-":"<div class='redB'>未定义</div>"):$ActionId ."-" . $myRow["ActionName"];
		$Remark=$myRow["Remark"];
		
		include "../model/subprogram/staffname.php";
		//$theDefaultColor=$myRow["TypeColor"];
		$ValueArray=array(
		    array(0=>$Id, 	1=>"align='center'"),
			array(0=>$TypeName, 	1=>"align='center'"),
			array(0=>$MainTypeName, 	1=>"align='center'"),
		//	array(0=>$ActionName, 	1=>"align='center'"),
			array(0=>$TypeIcon, 	1=>"align='center'"),
			array(0=>$TypeColor,	1=>"align='center'"),
			array(0=>$Remark,	1=>"align='left'"),
			array(0=>$Estate,	1=>"align='center'"),
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
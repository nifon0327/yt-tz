<?php 
//电信---yang 20120801
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=10;				
$tableMenuS=400;
ChangeWtitle("$SubCompany 产品购买属性列表");
$funFrom="product_property";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|50|属性名称|150|参数值|60|颜色|70|备注|350|状态|50|更新日期|80|操作|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="2,3,5,6,7,8";

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
$mySql="SELECT A.Id,A.Name,A.Color,A.pValue,A.Remark,A.Estate,A.Locks,A.Date,A.Operator 
FROM $DataIn.product_property A
WHERE 1 $SearchRows ORDER BY A.Estate DESC,A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do {
		$m=1;
		$Id=$myRow["Id"];
		$Color=$myRow["Color"];
		$Name=$myRow["Name"];
		$pValue=$myRow["pValue"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$Name,1=>"align='left'"),
			array(0=>$pValue,1=>"align='center'"),
			array(0=>$Color,1=>"align='center'"),
			array(0=>$Remark,3=>"..."),
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
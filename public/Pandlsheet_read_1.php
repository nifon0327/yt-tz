<?php 
//电信---yang 20120801
//代码、数据库共享-EWEN 2012-08-15
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=13;
$tableMenuS=900;
ChangeWtitle("$SubCompany 损益表子项目列表");
$funFrom="pandlsheet";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|项目类型|100|项目名称|120|排列|40|备注|240|参数|150|明细|60|子文件|50|行政项目|40|更新日期|80|状态|40|操作员|60";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,5,6,7,8";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows1="";
	$result = mysql_query("SELECT B.Id,B.ItemName FROM $DataPublic.sys8_pandlmain B ORDER BY B.SortId",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='ItemName' id='ItemName' onchange='ResetPage(this.name)'><option value='' selected>--全部主项目--</option>";
		do{
			$theMid=$myrow["Id"];
			$theName=$myrow["ItemName"];
			if ($theMid==$ItemName){
				echo "<option value='$ItemName' selected>$theName</option>";
				$SearchRows1=" AND A.Mid='$theMid' ";
			}
			else{
				echo "<option value='$theMid'>$theName</option>";
				
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}
}
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
$DefaultBgColor=$theDefaultColor;
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);

$mySql="SELECT A.Id,A.Mid,A.ItemName,A.SortId,A.Remark,A.Parameters,A.AjaxView,A.AjaxNo,A.Sign,A.Date,A.Estate,A.Locks,A.Operator,B.ItemName AS BigName,B.ColorCode,B.Estate AS bEstate
FROM $DataPublic.sys8_pandlsheet A
LEFT JOIN $DataPublic.sys8_pandlmain B ON B.Id=A.Mid
 WHERE 1   $SearchRows $SearchRows1 ORDER BY A.Estate DESC,B.SortId,A.SortId ";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$BigName=$myRow["BigName"];
		$ItemName=$myRow["ItemName"];
		$SortId=$myRow["SortId"];
		$Remark=$myRow["Remark"];
		$TableName=$myRow["TableName"];
		$Parameters=$myRow["Parameters"];
		$AjaxView=$myRow["AjaxView"]==0?"<div class='redB'>不显示</div>":"<div class='greenB'>显示</div>";
		$Sign=$myRow["Sign"]==0?"&nbsp;":"<div class='greenB'>是</div>";
		$AjaxNo=$myRow["AjaxNo"];
		$ColbgColor=$myRow["ColorCode"];
		$theDefaultColor=$myRow["ColorCode"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Estate=$myRow["bEstate"]==1?$Estate:"<div class='redB'>×</div>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$BigName),
			array(0=>$ItemName),
			array(0=>$SortId,1=>"align='center'"),
			array(0=>$Remark,1=>"align='left'"),
			array(0=>$Parameters,1=>"align='left'"),
			array(0=>$AjaxView,1=>"align='center'"),
			array(0=>$AjaxNo,1=>"align='center'"),
			array(0=>$Sign,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
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
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
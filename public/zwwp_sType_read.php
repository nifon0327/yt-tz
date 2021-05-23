<?php 
//ewen 2012-11-22
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=6;	
$tableMenuS=600;
ChangeWtitle("$SubCompany 物品分类");
$funFrom="zwwp_sType";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|50|分类名称|100|说 明|250|费用类别|80|可用|60|操作员|80";
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
$mySql="SELECT A.Id,A.TypeName,A.Remark,A.Estate,A.Locks,A.Operator,B.Name AS mainType 
FROM $DataPublic.zwwp2_subtype A 
LEFt JOIN $DataPublic.zwwp1_maintype B ON B.Id=A.mainType
WHERE 1 $SearchRows ORDER BY A.Id";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["TypeName"];
		$mainType=$myRow["mainType"];        
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Estate=$myRow["Estate"]==1?"<span class='greenB'>√</span>":"<span class='redB'>×</span>";
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$Name),
			array(0=>$Remark,	1=>"align='left'"),
            array(0=>$mainType,	1=>"align='center'"),
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
<?php 
//电信-joseph
//代码共享-EWEN
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=8;
$tableMenuS=400;
ChangeWtitle("$SubCompany 仓储位置列表");
$funFrom="nonbom16_ck";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|分类|60|出入库位置|150|负责人|60|说明|400|可用状态|60|更新日期|100|操作员|60";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="2,3,5,6";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//必选,过滤条件
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.Name,A.Remark,A.Estate,A.Locks,A.Date,A.Operator,B.Name AS Principal ,A.TypeId
FROM $DataPublic.nonbom0_ck A 
LEFT JOIN $DataPublic.staffmain B ON B.Number=A.Number
WHERE 1 $SearchRows ORDER BY A.Estate DESC ,A.TypeId";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		//取操作员姓名
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$Principal=$myRow["Principal"]==""?"未设置":$myRow["Principal"];
       $TypeId=$myRow["TypeId"];
       switch($TypeId){
               case "0":$TypeIdStr="<span class='greenB'>共享</span>";break;
               case "1":$TypeIdStr="<span class='blueB'>仓库</span>";break;
               case "2":$TypeIdStr="<span class='yellowB'>使用地点</span>";break;
            }
		$ValueArray=array(
			array(0=>$TypeIdStr,1=>"align='center'"),
			array(0=>$Name),
			array(0=>$Principal,1=>"align='center'"),
			array(0=>$Remark),
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
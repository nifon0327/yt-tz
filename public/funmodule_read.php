<?php 
//电信-joseph
//代码、数据库共享，加标识-EWEN
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=8;
$tableMenuS=600;
ChangeWtitle("$SubCompany 功能模块列表");
$funFrom="funmodule";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|功能标识|60|功能ID|60|功能名称|120|功能类型|100|连接参数|300|排序|50|状态|40|更新日期|80|操作|60";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,5,6,7,8";

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
     	echo"<select name='TypeId' id='TypeId' onchange='ResetPage(this.name)'>";
	$result = mysql_query("SELECT A.TypeId,T.Name	
	FROM $DataPublic.funmodule A
	LEFT JOIN $DataPublic.funmoduleType T ON T.Id=A.TypeId
	WHERE 1 $SearchRows GROUP BY A.TypeId",$link_id);
	while ($myrow = mysql_fetch_array($result)){
			$thisTypeId=$myrow["TypeId"];
           $TypeId=$TypeId==""?$thisTypeId:$TypeId;
			if ($thisTypeId==$TypeId){
				echo "<option value='$thisTypeId' selected>$myrow[Name]</option>";
                $SearchRows.=" AND A.TypeId=$thisTypeId";
				}
			else{
				echo "<option value='$thisTypeId' >$myrow[Name]</option>";
				}
			} 
		echo"</select>&nbsp;";
}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";

//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.cSign,A.ModuleId,A.ModuleName,A.Parameter,A.TypeId,A.Date,A.Estate,A.Locks,A.Operator,A.OrderId
FROM $DataPublic.funmodule A
WHERE 1 $SearchRows ORDER BY A.Estate DESC,A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$SharingShow="Y";//显示共享
	do{
		$m=1;
		$CShortName=$myRow["CShortName"];
		$Id=$myRow["Id"];
		$ModuleId=$myRow["ModuleId"];
		$ModuleName=$myRow["ModuleName"];
		$Parameter=$myRow["Parameter"]==""?"&nbsp;":$myRow["Parameter"];
		$OrderId=$myRow["OrderId"];
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Operator=$myRow["Operator"];
		include"../model/subprogram/staffname.php";
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$TypeFrom=$myRow["TypeId"];
        include "../model/subselect/funmoduleType.php";
		$Locks=$myRow["Locks"];
        $OrderId=$myRow["OrderId"];
		$ValueArray=array(
			array(0=>$cSign,1=>"align='center'"),
			array(0=>$ModuleId,1=>"align='center'"),
			array(0=>$ModuleName),
			array(0=>$TypeName),
			array(0=>$Parameter),
			array(0=>$OrderId,1=>"align='center'"),
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
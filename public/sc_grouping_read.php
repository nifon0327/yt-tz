<?php 
//$DataIn.sc1_grouping 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=9;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 车间小组列表");
$funFrom="sc_grouping";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|50|小组名称|100|小组班长|100|小组编号|100|小组人数|100|可用|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="3";
$sumCols="5";
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
$mySql="SELECT G.GroupId,G.GroupLeader,G.GroupName,G.Estate,M.Name 
FROM $DataIn.staffgroup G 
LEFT JOIN $DataPublic.staffmain M ON M.Number=G.GroupLeader 
LEFT JOIN $DataPublic.branchdata B ON B.Id=G.BranchId 
WHERE 1 $SearchRows AND  B.TypeId=2  AND G.Estate=1 ORDER BY G.GroupId";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$GroupId=$myRow["GroupId"];
		$Name=$myRow["Name"]==""?"&nbsp;":$myRow["Name"];		//组长姓名
		$GroupName=$myRow["GroupName"];	//小组分类
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Locks=$myRow["Locks"];
		$checkNums= mysql_query("SELECT * FROM $DataPublic.staffmain WHERE Estate=1 AND GroupId='$GroupId'",$link_id);
		$Nums=@mysql_num_rows($checkNums);
		$ValueArray=array(
			array(0=>$GroupName, 	1=>"align='center'"),
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$GroupId,	1=>"align='center'"),
			array(0=>$Nums,	1=>"align='center'"),
			array(0=>$Estate,	1=>"align='center'")
			);
		$checkidValue=$GroupId;
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
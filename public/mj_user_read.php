<?php 
//代码、数据共享-EWEN 2012-09-18
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=500;
ChangeWtitle("$SubCompany 门禁用户资料");
$funFrom="mj_user";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|公司或部门|100|门禁用户|60|校验方式|80|登录密码(已加密)|300|权限类型|80|状态|40|出入记录|60|更新日期|80|操作员|60";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8,125";
//步骤3：
$nowWebPage=$funFrom."_read";
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
$mySql="SELECT A.Id,A.Number,A.PowerType,A.Password,A.Estate,A.Locks,A.Date,A.Operator,B.Name,concat(C.CShortName,'-',D.Name) AS Branch,E.TypeName AS chkType,F.TypeName AS PowerType
			FROM $DataPublic.accessguard_user A 
			LEFT JOIN $DataPublic.staffmain B ON B.Number=A.Number
			LEFT JOIN $DataPublic.companys_group C ON C.cSign=B.cSign
			LEFT JOIN $DataPublic.branchdata D ON D.Id=B.BranchId
			LEFT JOIN $DataPublic.accessguard_chktype E ON E.Id=A.chkType
			LEFt JOIN $DataPublic.accessguard_powertype F ON F.Id=A.PowerType
			WHERE 1 $SearchRows ORDER BY B.cSign DESC,A.Estate DESC,D.SortId,A.Number";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$chkType=$myRow["chkType"];
		$Password=$myRow["Password"];
		$PowerType=$myRow["PowerType"]==""?"<span class='redB'>未设置</span>":$myRow["PowerType"];
		$Branch=$myRow["Branch"];
		$Date=$myRow["Date"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include"../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$Branch),
			array(0=>$Name,1=>"align='center'"),
			array(0=>$chkType,1=>"align='center'"),
			array(0=>$Password,1=>"align='center'"),
			array(0=>$PowerType,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>"查看",1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'"),
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
<script>
function UpdateFaxNO(FaxF,Id){
	var FaxNO=FaxF.value;
	myurl="user_updated.php?ActionId=9&FaxNO="+FaxNO+"&Id="+Id;
	retCode=openUrl(myurl);
	}
</script>
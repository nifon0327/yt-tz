<?php 
//电信-joseph
//代码共享、数据库共享-EWEN 2012-08-14
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=10;	
$tableMenuS=600;
ChangeWtitle("$SubCompany 部门列表");
$funFrom="branch";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|50|部门ID|50|使用标识|80|部门类型|80|部门名称|150|主管|80|排序号码|80|颜色|80|可用|60|更新日期|150";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,108,5,6,7,8";
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
$mySql="SELECT A.Id,A.cSign,A.Name,A.SortId,A.Estate,A.Locks,A.Date,A.Operator,A.Color,C.Name AS TypeName,D.Manager,A.Picture
     FROM $DataPublic.branchdata A 
	 LEFT JOIN $DataPublic.branchtype C ON C.Id=TypeId
	LEFT JOIN $DataIn.branchmanager D ON D.BranchId=A.Id
     WHERE 1 $SearchRows  ORDER BY A.SortId,A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myResult &&$myRow = mysql_fetch_array($myResult)){
$SharingShow="Y";//显示共享
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$TypeName=$myRow["TypeName"];
       $cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$SortId=$myRow["SortId"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		include "../model/subprogram/staffname.php";   
		$Operator=$myRow["Manager"];
		include "../model/subprogram/staffname.php";
        $Manager=$Operator;
        $Color=$myRow["Color"];
        $Color=$Color==""?"":"<div style='width:58px;height:25px;line-height:24px;background:$Color;vertical-align: middle;'>$Color</div>";
		$ValueArray=array(
            array(0=>$Id,1=>"align='center'"),
            array(0=>$cSign,1=>"align='center'"),
			array(0=>$TypeName, 	1=>"align='center'"),
			array(0=>$Name, 	1=>"align='center'"),
			array(0=>$Manager,	1=>"align='center'"),
			array(0=>$SortId, 	1=>"align='center'"),
            array(0=>$Color, 	1=>"align='center'"),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Date,	 	1=>"align='center'")
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
if($myResult)$RecordToTal= mysql_num_rows($myResult);
else $RecordToTal=0;
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
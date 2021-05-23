<?php 
//电信-joseph
//代码、数据库相同，记录独立-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=8;
$tableMenuS=400;
ChangeWtitle("$SubCompany 特殊功能权限");
$funFrom="taskuser";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|功能ID|50|功能名称|150|描述|300|特别参数|400|状态|40";

$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="2,4";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
//员工资料表
$checkSql ="SELECT * FROM (
SELECT A.UserId,C.Name,C.Number,C.cSign,C.BranchId,C.JobId,D.CShortName,E.Name AS Branch 
	FROM $DataIn.taskuserdata A 
	LEFT JOIN $DataIn.usertable B ON B.Number=A.UserId
	LEFT JOIN $DataPublic.staffmain C ON C.Number=B.Number 
	LEFT JOIN $DataPublic.companys_group D ON D.cSign=C.cSign
	LEFT JOIN $DataPublic.branchdata E ON E.Id=C.BranchId
	WHERE B.Estate='1' AND C.Estate='1' AND B.uType=1 GROUP BY A.UserId
UNION
SELECT A.UserId,C.Name,C.Number,'0' AS cSign,'0' AS BranchId,'0' AS JobId,'外部人员' AS CShortName,'其他' AS Branch
	FROM $DataIn.taskuserdata A
	LEFT JOIN $DataIn.usertable B ON B.Number=A.UserId
	LEFT JOIN $DataIn.ot_staff C ON C.Number=B.Number 
	WHERE B.Estate='1' AND  C.Estate>0 AND B.uType=4 GROUP BY A.UserId 
)Z ORDER BY cSign DESC,BranchId,JobId,convert(Name using gbk) ASC
";
echo"<select name='UserId' id='UserId' style='width: 150px;' onchange='document.form1.submit();'>";
$checkResult = mysql_query($checkSql); 
$i=1;
while ( $checkRow = mysql_fetch_array($checkResult)){
	$theUserId=$checkRow["UserId"];
	$UserId=$UserId==""?$theUserId:$UserId;
	$CShortName=$i." ".$checkRow["CShortName"]."-".$checkRow["Branch"]."-".$checkRow["Name"];
	if ($UserId==$theUserId){
		echo "<option value='$theUserId' selected>$CShortName</option>";
		}
	else{
		echo "<option value='$theUserId'>$CShortName</option>";
		}
	$i++;
	}
echo"</select>";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.ItemId,B.Description,B.Extra,B.Title,B.Estate 
FROM $DataIn.taskuserdata A 
LEFT JOIN $DataPublic.tasklistdata B ON A.ItemId=B.ItemId WHERE A.UserId=$UserId order by A.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$ItemId=$myRow["ItemId"];
		$Title=$myRow["Title"];
		$Description=$myRow["Description"]."&nbsp;";
		$Extra=$myRow["Extra"]."&nbsp;";
		$Estate=$myRow["Estate"]==1?"<span class='greenB'>√</span>":"<span class='redB'>×</span>";
		if(($Keys & mUPDATE)||($Keys & mDELETE)||($Keys & mLOCK)){
			$Locks=1;
			}
		else{
			$Locks=0;
			}
		$ValueArray=array(
			array(0=>$ItemId,1=>"align='center'"),
			array(0=>$Title),
			array(0=>$Description),
			array(0=>$Extra),
			array(0=>$Estate,1=>"align='center'")
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

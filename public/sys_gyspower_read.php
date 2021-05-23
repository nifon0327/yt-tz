<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=8;
$tableMenuS=400;
ChangeWtitle("$SubCompany 供应商登录权限");
$funFrom="sys_gyspower";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|功能ID|50|功能名称|120|价格/金额|80|描述|400|参数|150";

$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="2,4";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
$CheckSql = mysql_query("SELECT A.UserId,B.uName,C.Name,D.Forshort 
FROM $DataIn.sys4_gysfunpower A
LEFT JOIN $DataIn.usertable B ON B.Id=A.UserId
LEFT JOIN $DataIn.linkmandata C ON C.Id=B.Number
LEFT JOIN $DataIn.trade_object D ON D.CompanyId=C.CompanyId
WHERE B.Estate=1
 GROUP BY A.UserId ORDER BY D.Forshort,B.uName,A.UserId DESC",$link_id);
if($CheckRow = mysql_fetch_array($CheckSql)){
	$i=1;
	echo"<select name='UserId' id='UserId' style='width: 250px;' onchange='document.form1.submit();'>";
	do{
		$UserIdTemp=$CheckRow["UserId"];
		$UserId=$UserId==""?$UserIdTemp:$UserId;
		$uName=$CheckRow["uName"];		
		$Forshort=$CheckRow["Forshort"];
		$Name=$CheckRow["Name"];				
		if ($UserId==$UserIdTemp){
			echo "<option value='$UserIdTemp' selected>$i $Forshort - $uName/$Name</option>";
			$SearchRows=" AND A.UserId=$UserId";
			}
		else{
			echo "<option value='$UserIdTemp'>$i $Forshort - $uName/$Name</option>";
			}
		$i++;
		}while($CheckRow = mysql_fetch_array($CheckSql));
	echo"</select>";
	}
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.ModuleId,A.IsPrice,B.ModuleName,B.Parameter,B.Remark 
FROM $DataIn.sys4_gysfunpower A
LEFT JOIN $DataIn.sys4_gysfunmodule B ON A.ModuleId=B.ModuleId WHERE 1 $SearchRows  AND B.Estate=1 ORDER BY A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$ModuleId=$myRow["ModuleId"];
		$IsPricechecked=$myRow["IsPrice"]==1?"显示":"----";
		$ModuleName=$myRow["ModuleName"];
		$Remark=$myRow["Remark"];
		$Parameter=$myRow["Parameter"];
		if(($Keys & mUPDATE)||($Keys & mDELETE)||($Keys & mLOCK)){
			$Locks=1;
			}
		else{
			$Locks=0;
			}
		$ValueArray=array(
			array(0=>$ModuleId,	1=>"align='center'"),
			array(0=>$ModuleName),
			array(0=>$IsPricechecked,1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$Parameter)
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

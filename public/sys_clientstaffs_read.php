<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=8;
$tableMenuS=400;
ChangeWtitle("$SubCompany 公司联络名单");
$funFrom="sys_clientstaffs";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|联络人|80|说明|300|邮件|200|状态|40|更新日期|80|操作|60";

$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="2,3,4,5,6,7,8";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
//客户用户列表
$CheckSql = mysql_query("SELECT A.CompanyId,B.Forshort 
FROM $DataIn.sys_clientstaffs A 
LEFT JOIN $DataIn.trade_object B ON B.CompanyId=A.CompanyId
GROUP BY A.CompanyId ORDER BY B.OrderBy DESC,B.CompanyId",$link_id);
$i=1;
if($CheckRow = mysql_fetch_array($CheckSql)){
	echo"<select name='CompanyId' id='CompanyId' style='width: 150px;' onchange='document.form1.submit();'>";
	do{
		$CompanyIdTemp=$CheckRow["CompanyId"];
		$CompanyId=$CompanyId==""?$CompanyIdTemp:$CompanyId;
		$Forshort=$CheckRow["Forshort"];
		if ($CompanyId==$CompanyIdTemp){
			echo "<option value='$CompanyIdTemp' selected>$i $Forshort</option>";
			$SearchRows=" AND A.CompanyId=$CompanyId";
			}
		else{
			echo "<option value='$CompanyIdTemp'>$i $Forshort</option>";
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
$mySql="SELECT A.Id,A.CompanyId,A.Number,A.Remark,A.Estate,A.Locks,A.Date,A.Operator,B.Nickname,B.Name,B.Mail 
FROM $DataIn.sys_clientstaffs A 
LEFT JOIN $DataPublic.staffmain B  ON A.Number=B.Number WHERE 1 $SearchRows ORDER BY A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$CompanyId=$myRow["CompanyId"];
		$Number=$myRow["Number"];
		$Remark=$myRow["Remark"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Nickname=$myRow["Nickname"];
		$Mail=$myRow["Mail"];
		$ValueArray=array(
			array(0=>$Nickname,		1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$Mail),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Date,		1=>"align='center'"),
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

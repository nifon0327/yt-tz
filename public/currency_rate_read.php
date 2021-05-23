<?php 
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN 2012-08-14
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 每月汇率设置列表");
$funFrom="currency_rate";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|所属月份|100|货币说明|100|货币符号|80|货币简写|80|汇率|120|状态|60|设置日期|120|操作|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3";

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
$mySql="SELECT M.Id,M.Month,M.Rate,M.Date,M.Operator,M.Estate,M.Locks,A.PreChar,A.Name,A.Symbol FROM 
$DataPublic.currencyrate M 
INNER JOIN $DataPublic.currencydata A ON A.Id=M.Currency  WHERE 1 $SearchRows ORDER BY M.Month DESC,A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do {
		$m=1;
		$Id=$myRow["Id"];
		$PreChar=$myRow["PreChar"]==""?"&nbsp;":$myRow["PreChar"];
		$Month=$myRow["Month"];
		$Name=$myRow["Name"];
		$Symbol=$myRow["Symbol"];
		$Rate=$myRow["Rate"];
		$Date=$myRow["Date"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		//$LockRemark="锁定操作,请联系管理员";
		$ValueArray=array(
		   array(0=>$Month,		1=>"align='center'"),
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$Symbol,	1=>"align='center'"),
			array(0=>$PreChar,	1=>"align='center'"),
			array(0=>$Rate,		1=>"align='center'"),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Date,	 	1=>"align='center'"),
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
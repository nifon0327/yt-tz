<?php 
//电信-zxq 2012-08-01
/*
$DataIn.yw6_salesview
$DataPublic.staffmain
$DataIn.trade_object
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 业务订单查询权限列表");
$funFrom="yw_salesview";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|60|序号|60|要浏览客户|200|业务类型|60|状态|60|更新日期|150|操作|100";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="2,4";

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
//员工资料表
$salesSql = "SELECT S.SalesId,D.Name FROM $DataIn.yw6_salesview S LEFT JOIN $DataPublic.staffmain D ON D.Number=S.SalesId
GROUP BY S.SalesId ORDER BY S.SalesId DESC";
$salesResult = mysql_query($salesSql);
if($salesRow = mysql_fetch_array($salesResult)){
	echo"<select name='SalesId' id='SalesId' style='width: 100px;' onchange='document.form1.submit();'>";
	do{
		$salesTempId=$salesRow["SalesId"];
		$SalesId=$SalesId==""?$salesTempId:$SalesId;
		$salesName=$salesRow["Name"];					
		if ($SalesId==$salesTempId){
			echo "<option value='$salesTempId' selected>$salesName</option>";
			$SearchRows=" AND S.SalesId='$salesTempId'";
			}
		else{
			echo "<option value='$salesTempId'>$salesName</option>";
			}
		}while ($salesRow = mysql_fetch_array($salesResult));
		echo"</select>";
	}

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.Estate,S.Locks,S.Date,S.Operator,C.Forshort ,S.TypeId
FROM $DataIn.yw6_salesview S
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId
WHERE 1 $SearchRows
ORDER BY S.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Forshort=$myRow["Forshort"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Estate=$myRow["Estate"]==0?"<div class='redB'>×</div>":"<div class='greenB'>√</div>";
		$Locks=$myRow["Locks"];
		$TypeId=$myRow["TypeId"];
		if($TypeId==1)$TypeName="对内";
		else $TypeName="对外";
		$Locks=1;
		$ValueArray=array(
			0=>array(0=>$Forshort,
					 1=>"align='center'"),
			1=>array(0=>$TypeName,
					 1=>"align='center'"),
			2=>array(0=>$Estate,
					 1=>"align='center'"),
			3=>array(0=>$Date,					
					 1=>"align='center'"),
			4=>array(0=>$Operator,
					 1=>"align='center'")
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

<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.currencydata
$DataPublic.adminitype
$DataIn.hzqkmain
$DataIn.hzqksheet
已更新
*/
//步骤1
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=12;
$tableMenuS=500;
$sumCols="3";		//求和列
$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 扣除工龄记录");
$funFrom="rs_kcgl";
$Th_Col="选项|40|序号|40|姓名|70|扣除工龄(月)|80|起效月份|70|原因|400|状态|45|日期|80|操作员|60";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,14,4,7,8";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消结付,16审核通过，17结付

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录
	$SearchRows="";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
echo "<a href='rs_kcgl_change.php' target='_blank'  title=''><font color='red'>生成应扣工龄</font></a>"	;	
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.Month,S.Months,S.Remark,S.Date,S.Estate,S.Locks,S.Operator,M.Name
FROM $DataPublic.rs_kcgl S 
LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
WHERE 1 $SearchRows AND S.Estate=1 AND M.cSign=$Login_cSign ORDER BY S.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$Months=$myRow["Months"];
		$Month=$myRow["Month"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$Name,1=>"align='center'"),
			array(0=>$Months,1=>"align='center'"),
			array(0=>$Month,1=>"align='center'"),
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
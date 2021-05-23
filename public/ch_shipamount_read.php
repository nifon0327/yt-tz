<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ch11_shipamount
*/
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=13;
$tableMenuS=600;
ChangeWtitle("$SubCompany 每月报关金额设置");
$funFrom="ch_shipamount";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|月份|60|金额(RMB)|80|说明|60|状态|60|日期|75|设置人|50";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="2,3,4,5,6,8";
//$ActioToS="1,2,3,4,5,6,8";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//必选,过滤条件
if($From!="slist"){
	$SearchRows ="";	
	$date_Result = mysql_query("SELECT  substring(Month,1,4) as Year FROM $DataIn.ch11_shipamount WHERE 1 group by substring(Month,1,4) order by Month DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)){
		echo"<select name='chooseYear' id='chooseYear' onchange='ResetPage(this.name)'>";
		do{
			$dateValue=$dateRow["Year"];
			if($chooseYear==""){
				$chooseYear=$dateValue;
				}
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows="and substring(S.Month,1,4)='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	}

//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.Month,S.Amount,S.Remark,S.Estate,S.Locks,S.Date,S.Operator
FROM $DataIn.ch11_shipamount S 
WHERE 1 $SearchRows ORDER BY S.Date DESC";

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Month=$myRow["Month"];
		$Amount=$myRow["Amount"];
		

		
		$Remark=trim($myRow["Remark"])==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		//$LockRemark="";
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$Month),
			array(0=>$Amount,1=>"align='right'"),
			array(0=>$Remark, 1=>"align='center'"),
			array(0=>$Estate, 1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'")
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
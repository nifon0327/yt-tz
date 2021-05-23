<?php 
/*
$DataPublic.adminitype
$DataPublic.staffmain
二合一已更新
电信-joseph
*/
include "../model/modelhead.php";
$sumCols="5";		//求和列
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=10;				
$tableMenuS=400;
$sumExpenses=0;
$sumIncome=0;
$sum=0;
ChangeWtitle("$SubCompany 其他收入查询列表");
$funFrom="adminitype";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|分类ID|40|分类名称|140|说明|300|收入|60|支出|60|结存|60|更新日期|75|操作员|55";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="";

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

$mySql1="SELECT O.Amount,O.PayDate as Date,O.Operator,O.Remark,T.Name,T.hzID
	FROM $DataIn.cw4_otherin O
	LEFT JOIN $DataPublic.cw4_otherintype T ON T.Id=O.TypeId
	WHERE T.hzID>0";
	
$mySql2="SELECT A.Amount,A.Date,A.Operator,A.Remark,A.Name,T.hzID
	FROM $DataPublic.adminitype A
	LEFT JOIN $DataPublic.cw4_otherintype T ON T.hzID=A.TypeId
	WHERE T.hzID>0
	";
	
$myResult1 = mysql_query($mySql1." $PageSTR",$link_id);
if($myRow1 = mysql_fetch_array($myResult1)){
	do {
		$m=1;
		$hzID=$myRow1["hzID"];
		//$hzId=$myRow["hzID"];
		$Name=$myRow1["Name"];
		$Amount=$myRow1["Amount"];
		$tAmount="";
		$sumIncome+=$Amount;
		$Date=$myRow1["Date"];
		$Operator=$myRow1["Operator"];
		$Remark=$myRow1["Remark"];
		include "../model/subprogram/staffname.php";
		//$Locks=$myRow["Locks"];
		$ValueArray=array(
			0=>array(0=>$hzID,
					 1=>"align='center'"),
			1=>array(0=>$Name,
					 1=>"align='center'"),
			2=>array(0=>$Remark,
					 1=>"align='center'"),
			3=>array(0=>$Amount,
					 1=>"align='center'"),
			4=>array(0=>$tAmount,
					 1=>"align='center'"),
			5=>array(0=>$Amount-$tAmount,
					 1=>"align='center'"),
			6=>array(0=>$Date,					
					 1=>"align='center'"),
			7=>array(0=>$Operator,
					 1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow1 = mysql_fetch_array($myResult1));
		
		$myResult2 = mysql_query($mySql2." $PageSTR",$link_id);
		if($myRow2 = mysql_fetch_array($myResult2)){
			do {
			$m=1;
			$hzID=$myRow2["hzID"];
			//$hzId=$myRow["hzID"];
			$Name=$myRow2["Name"];
			$Amount=$myRow2["Amount"];
			$tAmount="";
			$sumExpenses+=$Amount;
			$Date=$myRow2["Date"];
			$Operator=$myRow2["Operator"];
			$Remark=$myRow2["Remark"];
			include "../model/subprogram/staffname.php";
			//$Locks=$myRow["Locks"];
			$ValueArray=array(
				0=>array(0=>$hzID,
						 1=>"align='center'"),
				1=>array(0=>$Name,
						 1=>"align='center'"),
				2=>array(0=>$Remark,
						 1=>"align='center'"),
				3=>array(0=>$tAmount,
						 1=>"align='center'"),
				4=>array(0=>-$Amount,
						 1=>"align='center'"),
				5=>array(0=>-$Amount-$tAmount,
						 1=>"align='center'"),
				6=>array(0=>$Date,					
						 1=>"align='center'"),
				7=>array(0=>$Operator,
						 1=>"align='center'")
				);
			$checkidValue=$Id;
			include "../model/subprogram/read_model_6.php";
			}while ($myRow2 = mysql_fetch_array($myResult2));
		}
$sumExpenses=$sumExpenses*-1;
$sum=$sumIncome+$sumExpenses;
echo "<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";		
echo "<tr>";
echo "<td class='A0111' align='center' height='30'>合计</td>";
echo "<td class='A0101' width=60 align='center' height='30'>$sumIncome</td>";
echo "<td class='A0101' width=60 align='center' height='30'>$sumExpenses</td>";	
echo "<td class='A0101' width=60 align='center' height='30'>$sum</td>";
echo "<td class='A0101' width=75 align='center' height='30'></td>";
echo "<td class='A0101' width=55 align='center' height='30'></td>";
}
else{
	noRowInfo($tableWidth);
  	}

echo "</tr></table>";
//步骤7：
echo '</div>';
//$myResult = mysql_query($mySql1,$link_id);
$RecordToTal= mysql_num_rows($myResult1)+mysql_num_rows($myResult2);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
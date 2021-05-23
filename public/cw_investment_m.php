<?php 
include "../model/modelhead.php";
$sumCols="4";		//求和列
$From=$From==""?"m":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=400;
ChangeWtitle("$SubCompany 长期股权投资");
$funFrom="cw_investment";
$nowWebPage=$funFrom."_m";
$Th_Col="选项|40|序号|40|公司名称|150|投资项目|210|金额|60|备注|300|文件|50|状态|50|时间|80|操作人|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="17";	

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
$mySql="SELECT * FROM $DataIn.cw22_investmentsheet C WHERE 1 AND C.Estate=2 $SearchRows ORDER BY C.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do {
		$m=1;
		$Id=$myRow["Id"];
		$Company=$myRow["Company"];
		$InvestName=$myRow["InvestName"];	
		$Amount=$myRow["Amount"];
		$Attached=$myRow["Attached"];
		$Dir=anmaIn("download/investment/",$SinkOrder,$motherSTR);
		if($Attached!=""){
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Attached="-";
			}
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$Estate="<div align='center' class='yellowB' title='审核中...'>×.</div>";	
		$ValueArray=array(
			array(0=>$Company),
			array(0=>$InvestName),
            array(0=>$Amount,	1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$Attached,		1=>"align='center'"),
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
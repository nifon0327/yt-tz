<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 图例列表");
$funFrom="chart";
$nowWebPage=$funFrom."_read";
$Th_Col="序号|50|图例名称|300|图例查看|80|数据来源说明|600";
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
$ChooseOut="N";
$mySql="SELECT * FROM $DataIn.chart1_item WHERE Estate=1 ORDER BY Id";
echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$BoldStr="";
		$m=1;
		$Id=$myRow["Id"];
		$Title=$myRow["Title"];
		$Link="<a href='../chart/$myRow[Link]' target='_blank'>查看</a>";
		$Remark=$myRow["Remark"];
		$Color=$myRow["Color"]==""?"#000000":$myRow["Color"];
		$Type=$myRow["Type"];
		if($Type%2==1){
			$BoldStr="font-weight: bold";
			}
		$Title="<span style='color: $Color;$BoldStr'>".$Title."</span>";
		$Id="<span style='color: $Color;$BoldStr'>".$Id."</span>";
		$Remark="<span style='color: $Color;$BoldStr'>".$Remark."</span>";
		$ValueArray=array(
			array(0=>$Title),
			array(0=>$Link,1=>"align='center'"),
			array(0=>$Remark)
			);
		
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
?>

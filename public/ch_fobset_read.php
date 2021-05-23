<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=9;				
$tableMenuS=600;
ChangeWtitle("$SubCompany FOB计算资料");
$funFrom="ch_fobset";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|50|客户|100|计算箱数|70|运费单价|70|杂费单价|70|固定杂费|70|入仓费|70|可用|60|操作员|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8";

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
$mySql="SELECT F.Id,F.Boxs,F.UnitYf,F.UnitZf,F.Gdzf,F.Rcf,F.Estate,F.Locks,F.Operator,C.Forshort FROM $DataIn.formula_fob F
LEFT JOIN $DataIn.trade_object C ON C.CompanyID=F.CompanyId
 WHERE 1 $SearchRows ORDER BY F.CompanyId";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Forshort=$myRow["Forshort"];
		$Boxs=$myRow["Boxs"];
		$UnitYf=$myRow["UnitYf"];
		$UnitZf=$myRow["UnitZf"];
		$Gdzf=$myRow["Gdzf"];
		$Rcf=$myRow["Rcf"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$Forshort),
			array(0=>$Boxs,		1=>"align='right'"),
			array(0=>$UnitYf,	1=>"align='right'"),
			array(0=>$UnitZf, 	1=>"align='center'"),
			array(0=>$Gdzf,		1=>"align='right'"),
			array(0=>$Rcf,	1=>"align='right'"),
			array(0=>$Estate,	1=>"align='center'"),
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
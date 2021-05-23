<?php 
//用于统计未出订单中，配件的总数$DataIn.电信---yang 20120801
//步骤1
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=17;
$tableMenuS=500;
ChangeWtitle("$SubCompany 配件数量统计");
$funFrom="productdata";
$From=$From==""?"read":$From;
$CompanyId=$CompanyId==""?1001:$CompanyId;
$Th_Col="选项|40|序号|40|配件ID|50|配件名称|400|订单总数|120|采购总数|120|报废或转入数量|120";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql= "SELECT D.StuffCname,C.StuffId,SUM(C.OrderQty) AS OrderQty,SUM(C.AddQty+C.FactualQty) AS cgQty
	FROM $DataIn.cg1_stocksheet C
	LEFT JOIN $DataIn.yw1_ordersheet S ON C.POrderId=S.POrderId
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=C.StuffId
	WHERE 1 AND M.CompanyId=1034 AND S.Estate>0 GROUP BY C.StuffId ORDER BY D.TypeId,D.StuffCname";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$StuffId=$myRow["StuffId"];		
		$StuffCname=$myRow["StuffCname"];
		$OrderQty=$myRow["OrderQty"];
		$cgQty=$myRow["cgQty"];
		$Qty=$OrderQty>=$cgQty?$OrderQty:$cgQty;
			$ValueArray=array(
				array(0=>$StuffId,				1=>"align='center'"),
				array(0=>$StuffCname),
				array(0=>$OrderQty,			1=>"align='right'"),
				array(0=>$cgQty,		1=>"align='right'"),
				array(0=>$Qty,		1=>"align='right'"),
				);
		$checkidValue=$StuffId;
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
<?php   
include "../model/modelhead.php";
$ColsNumber=13;
$tableMenuS=600;
$sumCols="10";	
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 生产类配件置换审核");
$funFrom="cg_stuffchange";
$Th_Col="操作|55|序号|30|采购流水号|120|配件ID|80|配件名称|210|需求数量|70|单价|60|置换<br>配件ID|80|置换<br>配件名称|210|需求数量|70|单价|60|更新备注|180|更新时间|60|操作员|55";
$Pagination=$Pagination==""?0:$Pagination;	
$Page_Size = 100;
$ActioToS="15,17";
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){	
	
}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
//步骤4：
include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT C.Id,C.POrderId,C.StockId,C.OldStuffId,C.NewStuffId,C.NewRelation,C.Remark,C.Date,C.Locks,C.Operator,
G.Level,G.OrderQty,G.Price AS OldPrice ,D1.StuffId,D1.StuffCname AS OldStuffCname,D2.StuffCname AS NewStuffCname,D2.Price AS NewPrice
FROM $DataIn.yw1_stuffchange C 
INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = C.StockId 
INNER JOIN $DataIn.stuffdata D1 ON D1.StuffId = G.StuffId
INNER JOIN $DataIn.stuffdata D2 ON D2.StuffId = C.NewStuffId
WHERE C.Estate=1";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$DefaultBgColor=$theDefaultColor;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$theDefaultColor=$DefaultBgColor;
		$Id=$myRow["Id"];
		$POrderId=$myRow["POrderId"];
		$StockId=$myRow["StockId"];
		$OldStuffId=$myRow["OldStuffId"];
		$OrderQty=$myRow["OrderQty"];
		$OldPrice=$myRow["OldPrice"];
		$OldStuffCname=$myRow["OldStuffCname"];
		$NewStuffId=$myRow["NewStuffId"];
		$StuffId=$myRow["StuffId"];
		$NewRelation=$myRow["NewRelation"];
		$NewStuffCname=$myRow["NewStuffCname"];
		$NewPrice=$myRow["NewPrice"];
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];
		$Remark=$myRow["Remark"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		
		$showPurchaseorder="<img onClick='ShowOrHideThisLayer(StuffList$i,showtable$i,StuffList$i,\"$StockId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
		title='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
		$XtableWidth=0;
		$StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30' align='left'><br><div id='showStuffTB$i' width='$XsubTableWidth'>&nbsp;</div><br></td></tr></table>";
			
		$ValueArray=array(
			array(0=>$StockId, 1=>"align='center'"),
			array(0=>$OldStuffId,1=>"align='center'"),
			array(0=>$OldStuffCname),
			array(0=>$OrderQty,1=>"align='center'"),
            array(0=>$OldPrice,1=>"align='center'"),
			array(0=>$NewStuffId,   1=>"align='center'"),
			array(0=>$NewStuffCname),			
			array(0=>$NewRelation,1=>"align='center'"),
			array(0=>$NewPrice,1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
        echo $StuffListTB;
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
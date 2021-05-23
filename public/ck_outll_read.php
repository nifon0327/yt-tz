<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
?>
<script>
function zhtj(obj){
	document.form1.action="ck_outll_read.php";
	document.form1.submit();
}
</script>
<?php 
//步骤2：需处理
$ColsNumber=15;
$tableMenuS=500;
ChangeWtitle("$SubCompany 外发领料记录列表");
$funFrom="ck_outll";
$From=$From==""?"read":$From;


$myTask=1;
$sumCols="9,10,12";			//求和列,需处理
$Th_Col="选项|60|序号|40|外发供应商|90|工单流水号|100|半成品名|280|配件ID|50|需求单流水号|100|配件名称|320|单位|40|需领料数|60|本次领料|60|单价|50|金额|60|外发日期|80|外发人员|70";


//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	
$Page_Size = 200;							
$ActioToS="1,3,4,7,8";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
	$SearchRows=" ";
	$month_Result = mysql_query("SELECT DATE_FORMAT(S.Date,'%Y-%m') AS Month 
	FROM $DataIn.ck5_llsheet S 
	LEFT JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
	WHERE 1 AND SC.ActionId = 105 GROUP BY DATE_FORMAT(S.Date,'%Y-%m') 
	ORDER BY DATE_FORMAT(S.Date,'%Y-%m') DESC",$link_id);
	if($monthRow = mysql_fetch_array($month_Result)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='zhtj(this.name)'>";
		do{			
			$MonthValue=$monthRow["Month"];
			$chooseMonth=$chooseMonth==""?$MonthValue:$chooseMonth;
			if($chooseMonth==$MonthValue){
				echo"<option value='$MonthValue' selected>$MonthValue</option>";
				$SearchRows.=" AND DATE_FORMAT(S.Date,'%Y-%m')='$MonthValue'";
				}
			else{
				echo"<option value='$MonthValue'>$MonthValue</option>";					
				}
			}while($monthRow = mysql_fetch_array($month_Result));
		echo"</select>&nbsp;";
		}
  }
//检查进入者是否采购
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.Id,S.sPOrderId,S.POrderId,S.StockId,S.StuffId,S.Qty,S.Locks,S.Date,S.Type,
D.StuffCname,D.Picture,G.OrderQty,U.Name AS UnitName,S.Price,MD.StuffCname AS mStuffCname,S.Operator,T.Forshort
FROM $DataIn.ck5_llsheet S
LEFT JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
LEFT JOIN $DataIn.cg1_stocksheet GM ON GM.StockId = SC.mStockId
LEFT JOIN $DataIn.trade_object T ON T.CompanyId = GM.CompanyId
LEFT JOIN $DataIn.stuffdata MD ON MD.StuffId=GM.StuffId 
WHERE 1 $SearchRows AND SC.ActionId = 105 ORDER BY S.Date DESC ";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do {
		$m=1;
		$Id=$myRow["Id"];
		$blDate=$myRow["Date"];	
		$StuffId=$myRow["StuffId"];
		$sPOrderId=$myRow["sPOrderId"];
		$POrderId=$myRow["POrderId"];
		$StockId=$myRow["StockId"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];
		$Amount = sprintf("%.2f", $Qty*$Price);
		$OrderQty=$myRow["OrderQty"];
		$UnitName=$myRow["UnitName"];
		$StuffCname=$myRow["StuffCname"];
		$mStuffCname=$myRow["mStuffCname"];
		$Picture=$myRow["Picture"];
		$Forshort=$myRow["Forshort"];
		$WorkShopName=$myRow["WorkShopName"]==""?"&nbsp;":$myRow["WorkShopName"];
		$BranchName=$myRow["BranchName"]==""?"&nbsp;":$myRow["BranchName"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		
		//配件品检报告qualityReport
        include "../model/subprogram/stuff_get_qualityreport.php"; 
        include"../model/subprogram/stuff_Property.php";//配件属性
        $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	    //检查是否有图片
	    include "../model/subprogram/stuffimg_model.php";
		$Type=$myRow["Type"];

        if($sPOrderId>0){
			$showPurchaseorder="<img onClick='ShowOrHideSc(StuffList$i,ShowStuffListTable$i,StuffListDiv$i,\"$sPOrderId\",$i,\"fromscorder\",\"$fromPage\");' name='ShowStuffListTable$i' src='../images/showtable.gif' 
				title='显示半成品明细' width='13' height='13' style='CURSOR: pointer'>";
			$StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'><tr bgcolor='#B7B7B7'><td  height='30'><br><div id='StuffListDiv$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		
		}
		$checkllQtyResult=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE  StockId='$StockId'",$link_id));
		$TotalllQty=$checkllQtyResult["llQty"];
		
		if($TotalllQty>$OrderQty){
			$Qty = "<span class='redB'>$Qty</span>";
		}else if ($TotalllQty<$OrderQty){
			$Qty = "<span class='blueB'>$Qty</span>";
		}
			$ValueArray=array(
			array(0=>$Forshort,1=>"align='center'"),
			array(0=>$sPOrderId,1=>"align='center'"),
			array(0=>$mStuffCname),
			array(0=>$StuffId,1=>"align='center'"),
			array(0=>$StockId,1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$UnitName,1=>"align='center'"),
			array(0=>$OrderQty,1=>"align='right'"),
			array(0=>$Qty,1=>"align='right'"),
			array(0=>$Price,1=>"align='right'"),
			array(0=>$Amount,1=>"align='right'"),
			array(0=>$blDate,1=>"align='center'"),
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
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
?>
<script>
function zhtj(obj){
	document.form1.action="ck_ll_read.php";
	document.form1.submit();
}
</script>
<?php 
//步骤2：需处理

$tableMenuS=500;
ChangeWtitle("$SubCompany 领料记录列表");
$funFrom="ck_ll";
$From=$From==""?"read":$From;

$TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=246 and UserId=$Login_P_Number LIMIT 1",$link_id);
if($TRow = mysql_fetch_array($TResult)){
    $myTask=1;
    $ColsNumber=18;
    $sumCols="9,10,11,12,13,14";			//求和列,需处理
    $Th_Col="选项|60|序号|40|生产车间|90|工单流水号|100|工单数量|60|配件ID|50|需求单流水号|100|配件名称|320|单位|40|需领料数|60|本次领料|60|含税价|50|含税金额|60|成本价|50|成本金额|60|备料日期|80|领料日期|80|领料人|60|领料部门|70|领料类型|80";
}else{
	$Th_Col="选项|60|序号|40|生产车间|90|工单流水号|100|工单数量|60|配件ID|50|需求单流水号|100|配件名称|320|单位|40|需领料数|60|本次领料|60|备料日期|80|领料日期|80|领料人|60|领料部门|70|领料类型|80";
	$myTask=0;
	$ColsNumber=16;
	$sumCols="8,9";			//求和列,需处理
}

//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	
$Page_Size = 500;							
$ActioToS="1,3,4,7,8,11";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
	$SearchRows=" ";
	
    $Type = $Type==""?1:$Type;
    $TempTypeSTR="TypeStr".strval($Type); 
    $$TempTypeSTR="selected";
    echo"<select name='Type' id='Type' onchange='zhtj(this.name)'>";
	echo"<option value='1' $TypeStr1>工单领料</option>";
	echo"<option value='2' $TypeStr2>报废领料</option>";
	echo"<option value='3' $TypeStr3>物料退货领料</option>";
	echo"<option value='4' $TypeStr4>删单领料</option>";
	echo"<option value='5' $TypeStr5>车间退料</option></select>";
	if($Type>0){
		 $SearchRows.=" AND S.Type='$Type'";
	 }
	 if($Type ==1){
	    $SearchRows.=" AND SC.ActionId !=105";
		 
	 }
	$month_Result = mysql_query("SELECT DATE_FORMAT(S.Date,'%Y-%m') AS Month 
	FROM $DataIn.ck5_llsheet S 
	LEFT JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId 
	WHERE 1 $SearchRows AND SC.ActionId !=105 GROUP BY DATE_FORMAT(S.Date,'%Y-%m') 
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
		
		
	$type_Result = mysql_query("SELECT T.TypeId,T.TypeName
	FROM $DataIn.ck5_llsheet S 
	LEFT JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId = S.StuffId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId
	WHERE 1 $SearchRows AND SC.ActionId !=105 GROUP BY T.TypeId ORDER BY T.Letter",$link_id);
	if($typeRow = mysql_fetch_array($type_Result)) {
		echo"<select name='TypeId' id='TypeId' onchange='zhtj(this.name)'>";
		echo "<option value='' >全部</option>";
		do{			
			$TypeIdValue=$typeRow["TypeId"];
			$TypeName=$typeRow["TypeName"];
			//$TypeId=$TypeId==""?$TypeIdValue:$TypeId;
			if($TypeId==$TypeIdValue){
				echo"<option value='$TypeIdValue' selected>$TypeName</option>";
				$SearchRows.=" AND D.TypeId ='$TypeIdValue'";
				}
			else{
				echo"<option value='$TypeIdValue'>$TypeName</option>";					
				}
			}while($typeRow = mysql_fetch_array($type_Result));
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
$mySql="SELECT S.Id,S.sPOrderId,S.POrderId,S.StockId,S.StuffId,S.Qty,S.Locks,S.Date,S.Receiver,S.Received,S.FromFunction,S.Type,
D.StuffCname,D.Picture,IFNULL(G.OrderQty,CG.OrderQty) AS OrderQty,U.Name AS UnitName,S.Price,W.Name AS WorkShopName,SC.ActionId,SC.Qty AS scQty,IFNULL(G.CompanyId,B.CompanyId) AS CompanyId 
FROM $DataIn.ck5_llsheet S
LEFT JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
LEFT JOIN $DataIn.workshopdata W ON W.Id = SC.WorkShopId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
LEFT JOIN $DataIn.bps B ON B.StuffId = D.StuffId  
LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
LEFT JOIN $DataIn.cg1_stuffcombox CG ON CG.StockId = S.StockId
WHERE 1 $SearchRows   ORDER BY S.Received ASC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do {
		$m=1;
		$Id=$myRow["Id"];
		$LockRemark ="";
		$Received=$myRow["Received"];
		$blDate=$myRow["Date"];	
		$StuffId=$myRow["StuffId"];
		$sPOrderId=$myRow["sPOrderId"];
		$POrderId=$myRow["POrderId"];
		$StockId=$myRow["StockId"];
		$Qty=$myRow["Qty"];
		$scQty=$myRow["scQty"];
		$Price=$myRow["Price"];
		$Amount = sprintf("%.2f", $Qty*$Price);
		
		$CompanyId = $myRow["CompanyId"];
		$checkTaxRow = mysql_fetch_array(mysql_query("SELECT T.Value,O.Forshort FROM $DataIn.providersheet P 
		LEFT JOIN $DataIn.trade_object O ON O.CompanyId = P.CompanyId 
		LEFT JOIN $DataIn.provider_addtax T ON T.Id = P.AddValueTax
		WHERE P.CompanyId = '$CompanyId'",$link_id));
		$AddValue = $checkTaxRow["Value"];
		$AddValue=$AddValue==""?0:$AddValue;
		$CostPrice = sprintf("%.4f", $Price/(1+$AddValue));
		$CostAmount = sprintf("%.4f", $CostPrice*$Qty);
		$Forshort = $checkTaxRow["Forshort"];
		

		$OrderQty=$myRow["OrderQty"];
		$UnitName=$myRow["UnitName"];
		$StuffCname=$myRow["StuffCname"];
		$Picture=$myRow["Picture"];
		$WorkShopName=$myRow["WorkShopName"]==""?"&nbsp;":$myRow["WorkShopName"];
		$tempYear = substr($StockId,0, 4);	
		$Operator=$myRow["Receiver"];
		include "../model/subprogram/staffname.php";
		$Receiver=$Operator;
		if($myRow["Receiver"]<50000){
			$Number = $myRow["Receiver"];
			$BranchRow = mysql_fetch_array(mysql_query("SELECT B.Name AS BranchName  FROM $DataIn.Staffmain M LEFT JOIN $DataIn.branchdata B ON B.Id = M.BranchId WHERE M.Number = '$Number'",$link_id));
			$BranchName=$BranchRow["BranchName"]==""?"&nbsp;":$BranchRow["BranchName"];
		}else{
			$BranchName="&nbsp;";
		}
		
		
		
		//配件品检报告qualityReport
        include "../model/subprogram/stuff_get_qualityreport.php"; 
        include"../model/subprogram/stuff_Property.php";//配件属性
        $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	    //检查是否有图片
	    include "../model/subprogram/stuffimg_model.php";
		$Type=$myRow["Type"];
		$FromFunction=$myRow["FromFunction"];
		$ActionId=$myRow["ActionId"];
		if($ActionId =='101'){
			$fromPage = "finished";
		}else{
			$fromPage = "semifinished";
		}
		if($sPOrderId>0){
			$showPurchaseorder="<img onClick='ShowOrHideSc(StuffList$i,ShowStuffListTable$i,StuffListDiv$i,\"$sPOrderId\",$i,\"fromscorder\",\"$fromPage\");' name='ShowStuffListTable$i' src='../images/showtable.gif' 
				title='显示半成品明细' width='13' height='13' style='CURSOR: pointer'>";
			$StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'><tr bgcolor='#B7B7B7'><td  height='30'><br><div id='StuffListDiv$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		
		}
		
		$CheckResult=mysql_query("SELECT Id FROM $DataIn.yw1_ordersheet 
		WHERE POrderId='$POrderId' AND Estate=0 ",$link_id);
		if($CheckRow = mysql_fetch_array($CheckResult)){
			
			//$LockRemark  = "订单已经出货,不能修改!";
		}
		
		
		$checkllQtyResult=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE  StockId='$StockId'",$link_id));
		$TotalllQty=$checkllQtyResult["llQty"];
		
		if($TotalllQty>$OrderQty){
			$Qty = "<span class='redB'>$Qty</span>";
		}else if ($TotalllQty<$OrderQty){
			$Qty = "<span class='blueB'>$Qty</span>";
		}
		
		if($myTask == 1){
			$ValueArray=array(
			array(0=>$WorkShopName,1=>"align='center'"),
			array(0=>$sPOrderId,1=>"align='center'"),
			array(0=>$scQty,1=>"align='center'"),
			array(0=>$StuffId,1=>"align='center'"),
			array(0=>$StockId,1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$UnitName,1=>"align='center'"),
			array(0=>$OrderQty,1=>"align='right'"),
			array(0=>$Qty,1=>"align='right'"),
			array(0=>$Price,1=>"align='right'"),
			array(0=>$Amount,1=>"align='right'"),
			array(0=>$CostPrice,1=>"align='right'"),
			array(0=>$CostAmount,1=>"align='right'"),
			array(0=>$blDate,1=>"align='center'"),
			array(0=>$Received,1=>"align='center'"),
			array(0=>$Receiver,1=>"align='center'"),
			array(0=>$BranchName,1=>"align='center'"),
			array(0=>$FromFunction,1=>"align='center'")
			);
		}else{
		    $ValueArray=array(
			array(0=>$WorkShopName,1=>"align='center'"),
			array(0=>$sPOrderId,1=>"align='center'"),
			array(0=>$scQty,1=>"align='center'"),
			array(0=>$StuffId,1=>"align='center'"),
			array(0=>$StockId,1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$UnitName,1=>"align='center'"),
			array(0=>$OrderQty,1=>"align='right'"),
			array(0=>$Qty,1=>"align='right'"),
			array(0=>$blDate,1=>"align='center'"),
			array(0=>$Received,1=>"align='center'"),
			array(0=>$Receiver,1=>"align='center'"),
			array(0=>$BranchName,1=>"align='center'"),
			array(0=>$FromFunction,1=>"align='center'")
			);
		}
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
<?php 
//电信-ZX
/*/////////////////////
$DataIn.pands
$DataIn.productdata
$DataIn.trade_object
$DataIn.producttype
$DataPublic.packingunit
二合一已更新
*/
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理

$Th_Col="选项|60|序号|40|出货流水号|80|客户|90|Invoice名称|110|Invoice文档|80|外箱标签|60|出货金额|80|出货日期|80|货运信息|120|付款账号|100|报关方式|80|How to Ship|90|备注|200|操作员|50";
$ColsNumber=17;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$isPage=1;//是否分页
//非必选,过滤条件
$Parameter.=",Bid,$Bid";
switch($Action){
	case "2"://来自新增订单
		//if($From!=slist){//$CompanyIdSTR=" and P.CompanyId=$Bid";}
		//$CompanyIdSTR.=" AND P.Estate=1 AND P.ProductId IN(SELECT ProductId FROM $DataIn.pands GROUP BY ProductId ORDER BY ProductId)";

	break;
	}   
//已传入参数：目的查询页面，来源页面，可选记录数，动作，类别uType
//$uTypeSTR=$uType==""?"":"and P.TypeId=$uType";
include "../model/subprogram/s1_model_3.php";
//步骤3：
$DateTime=date("Y-m-d");   //从现在开始，到上一年的。采购的，其它就不显示了  add mby zx 2010-12-30
$StartDate=date("Y-m-d",strtotime("$DateTime-1 years"));
$StartMonth=date("Y-m",strtotime("$DateTime-1 years"));
//echo "$sSearch:$$sSearch";
if($From!="slist"){
	//月份
	$SearchRows="";	
	$SearchRows=" and DATE_FORMAT(M.Date,'%Y-%m')>$StartMonth and M.ShipType='' and M.Estate='0' ";	
	$date_Result = mysql_query("SELECT M.Date FROM $DataIn.ch1_shipmain M WHERE 1 $SearchRows GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='zhtj(this.name)'>";
		do{			
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$StartDate=$dateValue."-01";
			$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and ((M.Date>'$StartDate' and M.Date<'$EndDate') OR M.Date='$StartDate' OR M.Date='$EndDate')";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	//客户
	$clientResult = mysql_query("SELECT M.CompanyId,C.Forshort 
	FROM $DataIn.ch1_shipmain M,$DataIn.trade_object C 
	WHERE 1 AND C.CompanyId=M.CompanyId $SearchRows GROUP BY M.CompanyId ORDER BY M.CompanyId",$link_id);
	if($clientRow = mysql_fetch_array($clientResult)) {
		echo"<select name='CompanyId' id='CompanyId' onchange='zhtj(this.name)'>";
		echo"<option value='' selected>全部</option>";
		do{			
			$thisCompanyId=$clientRow["CompanyId"];
			$Forshort=$clientRow["Forshort"];
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort</option>";
				$SearchRows.=" and M.CompanyId='$thisCompanyId' ";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";					
				}
			}while ($clientRow = mysql_fetch_array($clientResult));
		echo"</select>&nbsp;";
		}
	}

//步骤4：可选，其它预设选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处

/////
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
M.Id,M.CompanyId,M.BankId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.Remark,M.Ship,M.Operator,T.Type as incomeType,C.Forshort 
FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
WHERE 1 $SearchRows $sSearch
ORDER BY M.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);		
	do{
		$m=1;
		$Id=$myRow["Id"];
		$CompanyId=$myRow["CompanyId"];
		$Number=$myRow["Number"];
		$Forshort=$myRow["Forshort"];
		$InvoiceNO=$myRow["InvoiceNO"];
		$InvoiceFile=$myRow["InvoiceFile"];
		$Wise=$myRow["Wise"]==""?"&nbsp;":$myRow["Wise"];
		$Date=$myRow["Date"];	
		$Sign=$myRow["Sign"];//收支标记
		$checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$Id'",$link_id));
		$Amount=sprintf("%.2f",$checkAmount["Amount"])*$Sign;
		switch($Action){
		case "1"://选择产品以便进行操作
			//$Bdata=$ProductId;
			break;
		case"2"://来自新增订单
			$Bdata=$Number."^^".$Forshort."^^".$InvoiceNO."^^".$Amount."^^".$Date."^^".$Wise;	
			break;
		case "6"://多选框
			//$Bdata=$ProductId."^^".$cName;
			break;
		case "7"://选择产品以便进行BOM操作
			//$Bdata=$ProductId."^^".$cName;
			break;
			}	
					
		$BoxLable="<div class='redB'>未装箱</div>";
		//检查是否有装箱
		$checkPacking=mysql_query("SELECT Id FROM $DataIn.ch2_packinglist WHERE Mid='$Id' LIMIT 1",$link_id);
		if($PackingRow=mysql_fetch_array($checkPacking)){
			//加密参数
			$Parame1=anmaIn($Id,$SinkOrder,$motherSTR);
			$Parame2=anmaIn("Mid",$SinkOrder,$motherSTR);		
			$BoxLable=$InvoiceFile==0?"&nbsp;":"<a href='ch_shippinglist_print.php?Parame1=$Parame1&Parame2=$Parame2' target='_blank'>查看</a>";
			}
		//Invoice查看
		//加密参数
		$Sign=$myRow["Sign"];//收支标记
		//echo "InvoiceNO=$InvoiceNO ";
		$f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
		$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<span onClick='OpenOrLoad(\"$d1\",\"$f1\",7)' style='CURSOR: hand;color:#FF6633'>查看</span>";
		if($CompanyId==1001 && $Sign!=-1){
			$d2=anmaIn("download/invoice/mca/",$SinkOrder,$motherSTR);
			$InvoiceFile.="&nbsp;&nbsp;<span onClick='OpenOrLoad(\"$d2\",\"$f1\",7)' style='CURSOR: hand;color:#FF6633'>★</span>";
			}
			
		//$incomeType=$myRow["shipType"]==1?"<span class='redB'>报关</span>":"&nbsp;";
		$incomeType=$myRow["incomeType"]==1?"<span class='redB'>报关</span>":"&nbsp;";
		/*
		$incomeType="<img location.href=\"#\"' style='CURSOR: hand' onclick='upMainData(\"Ch_shippinglist_upshipType\",\"$Id\")' src='../images/edit.gif' alt='更新报关方式' width='13' height='13'>
		<span class='yellowB'>$incomeType</span>";	
		*/
		//$BankId=$myRow["BankId"]==4?"<span class='redB'>报关</span>":"&nbsp;";
		$BankId=$myRow["BankId"];
		switch($BankId){
			case 4:
				$BankId="<span class='redB'>国内对公账号</span>"; break;
			case 5:
				$BankId="<span class='redB'>香港对公账号</span>"; break;
			default:
				$BankId="&nbsp;"; break;  //其它旧的不显示
			
	
		}
		/*
		$BankId="<img location.href=\"#\"' style='CURSOR: hand' onclick='upMainData(\"Ch_shippinglist_upbankID\",\"$Id\")' src='../images/edit.gif' alt='更新银行账号' width='13' height='13'>
		<span class='yellowB'>$BankId</span>";	
		*/
		$Ship=$myRow["Ship"];
		//echo "ShipType:$ShipType <Br>";
		switch ($Ship){
			case '-1':
				$Ship="&nbsp;"; 
				break;			
			case '0': 
				$Ship="air"; 
				break;
			case '1':
				$Ship="sea";
				break;
		}
		$Ship=$Ship=""?"&nbsp;":$Ship;
		/*
		$Ship="<img location.href=\"#\"' style='CURSOR: hand' onclick='upMainData(\"Ch_shippinglist_upship\",\"$Id\")' src='../images/edit.gif' alt='更新出货方式' width='13' height='13'>
		<span class='yellowB'>$Ship</span>";		
		*/
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		/*
		$Remark="<img location.href=\"#\"' style='CURSOR: hand' onclick='upMainData(\"Ch_shippinglist_upmain\",\"$Id\")' src='../images/edit.gif' alt='更新备注' width='13' height='13'>
		<span class='yellowB'>$Remark</span>";	
		*/

		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		//出货金额
		
		$checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$Id'",$link_id));
		
		if($Amount<0){
			$Amount="<div class='redB'>$Amount</div>";
			}
		$showPurchaseorder="<img onClick='sOrhOrder(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏出货订单明细.' width='13' height='13' style='CURSOR: hand'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		

		$ValueArray=array(
			array(0=>$Number,
					 1=>"align='center'"),
			array(0=>$Forshort),
			array(0=>$InvoiceNO),
			array(0=>$InvoiceFile,
					 1=>"align='center'"),
			array(0=>$BoxLable,
					 1=>"align='center'"),
			array(0=>$Amount,					
					 1=>"align='right'"),
			array(0=>$Date,
					 1=>"align='center'"),
			array(0=>$Wise),
			array(0=>$BankId),
			array(0=>$incomeType),	
			array(0=>$Ship),			
			array(0=>$Remark),
			array(0=>$Operator,
					 1=>"align='center'")
			);
		$checkidValue=$Bdata;
		//$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}	
//步骤7：
List_Title($Th_Col,"0",1);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script language="JavaScript" type="text/JavaScript">
function zhtj(obj){
	switch(obj){
		case "chooseDate"://改变采购
			//document.forms["form1"].elements["PayMode"].value="";
			if(document.all("CompanyId")!=null){
				document.forms["form1"].elements["CompanyId"].value="";
				}

		break;


		}
	//document.form1.action="cg_cgdmainR_read.php";
	document.form1.submit();
}
</script>
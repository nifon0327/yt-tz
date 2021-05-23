<?php
//电信-EWEN
//代码共享-EWEN 2012-08-20
include "../model/modelhead.php";
$From=$From==""?"read":$From;
$ColsNumber=12;
$tableMenuS=750;
ChangeWtitle("$SubCompany 已出订单列表");
$funFrom="Export_shippinglist";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|60|序号|40|出货流水号|80|客户|90|Invoice名称|110|Invoice文档|80|外箱标签|60|出货日期|80|货运信息|120|报关方式|80|How to Ship|90|出货分类|60|备注|140|操作员|50";
$Pagination=$Pagination==""?0:$Pagination;
$ShipTypeFlag=$ShipTypeFlag==""?0:$ShipTypeFlag;
$Page_Size = 100;
$ActioToS="";
$sumCols="";			//求和列,需处理
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
	$SearchRows=" and M.Estate='0'";
	$date_Result = mysql_query("SELECT M.Date FROM $DataIn.ch1_shipmain M WHERE 1 $SearchRows GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"$nowWebPage\")'>";
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
		echo"<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"$nowWebPage\")'>";
		echo"<option value='' selected>全部客户</option>";
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
		//订单类型
		echo"<select name='ShipTypeFlag' id='ShipTypeFlag' onchange='RefreshPage(\"$nowWebPage\")'>";

		switch ($ShipTypeFlag){
			 case '1':
			   $SearchRows.=" and M.ShipType='' ";
			   $TypeSel1="selected";
				break;
			case '2':
			    $SearchRows.=" and M.ShipType='replen' ";
				$TypeSel2="selected";
				break;
			case '3':
			    $SearchRows.=" and M.ShipType='credit' ";
				$TypeSel3="selected";
				break;
			case '4':
			    $SearchRows.=" and M.ShipType='debit' ";
				$TypeSel4="selected";
				break;
		    default:
			   $TypeSel0="selected";
			    break;
		}
		echo"<option value='0' $TypeSel0>出货分类</option>";
		echo"<option value='1' $TypeSel1>出&nbsp;&nbsp;货</option>";
		echo"<option value='2' $TypeSel2 style='background-color:#FEA085;'>补&nbsp;&nbsp;货</option>";
		echo"<option value='3' $TypeSel3 style='background-color:#FFFF93;'>扣&nbsp;&nbsp;款</option>";
		echo"<option value='4' $TypeSel4 style='background-color:#6ACFFF;'>其它收款</option>";
		echo"</select>&nbsp;";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
M.Id,M.CompanyId,M.BankId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.Remark,M.ShipType,M.Ship,M.Operator,T.Type as incomeType,C.Forshort 
FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
WHERE 1 $SearchRows AND T.Type=1
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
		$BoxLable="<div class='redB'>未装箱</div>";
		//检查是否有装箱
		$checkPacking=mysql_query("SELECT Id FROM $DataIn.ch2_packinglist WHERE Mid='$Id' LIMIT 1",$link_id);
		if($PackingRow=mysql_fetch_array($checkPacking)){
			//加密参数
			$Parame1=anmaIn($Id,$SinkOrder,$motherSTR);
			$Parame2=anmaIn("Mid",$SinkOrder,$motherSTR);
			$BoxLable=$InvoiceFile==0?"&nbsp;":"<a href='../admin/ch_shippinglist_print.php?Parame1=$Parame1&Parame2=$Parame2' target='_blank'>查看</a>";
			}
		//Invoice查看
		//加密参数
		$Sign=$myRow["Sign"];//收支标记
		$f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
		$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<span onClick='OpenOrLoad(\"$d1\",\"$f1\",7)' style='CURSOR: pointer;color:#FF6633'>查看</span>";
		if($CompanyId==1001 && $Sign!=-1){
			$d2=anmaIn("download/invoice/mca/",$SinkOrder,$motherSTR);
			$InvoiceFile.="&nbsp;&nbsp;<span onClick='OpenOrLoad(\"$d2\",\"$f1\",7)' style='CURSOR: pointer;color:#FF6633'>★</span>";
			}

		$incomeType=$myRow["incomeType"]==1?"<span class='redB'>报关</span>":"&nbsp;";
		$BankId=$myRow["BankId"];
		switch($BankId){
			case 4:
				$BankId="<span class='redB'>国内对公账号</span>"; break;
			case 5:
				$BankId="<span class='redB'>上海对公账号</span>"; break;
			default:
				$BankId="&nbsp;"; break;  //其它旧的不显示
		}
		$Ship=$myRow["Ship"];
		switch ($Ship){
			case '-1':
				$Ship="";
				break;
			case '0':
				$Ship="air";
				break;
			case '1':
				$Ship="sea";
				break;
		}
		$Ship=$Ship=""?"&nbsp;":$Ship;
		$ShipType=$myRow["ShipType"];
		switch ($ShipType){
			case 'replen':
				$ShipType="补货";
				$shipColor=" bgcolor='#FEA085' ";
				break;
			case 'credit':
				$ShipType="扣款";
				$shipColor=" bgcolor='#FFFF93' ";
				break;
			case 'debit':
				$ShipType="其它收款";
				$shipColor=" bgcolor='#6ACFFF' ";
				break;
		    default:
			    $ShipType="出货";
				$shipColor="";
		}
		 $ColbgColor=$shipColor;//前面选项设置订单类型颜色
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Wise=$myRow["Wise"]==""?"&nbsp;":$myRow["Wise"];
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		//出货金额

		$checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$Id'",$link_id));
		$Amount=sprintf("%.2f",$checkAmount["Amount"])*$Sign;
		if($Amount<0){
			$Amount="<div class='redB'>$Amount</div>";
			}
		$showPurchaseorder="<img onClick='sOrhOrder(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏出货订单明细.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		$LockRemark="仅供查看!";
		$ValueArray=array(
			array(0=>$Number,
					 1=>"align='center'"),
			array(0=>$Forshort),
			array(0=>$InvoiceNO),
			array(0=>$InvoiceFile,
					 1=>"align='center'"),
			array(0=>$BoxLable,
					 1=>"align='center'"),
			array(0=>$Date,
					 1=>"align='center'"),
			array(0=>$Wise),
			array(0=>$incomeType),
			array(0=>$Ship),
			array(0=>$ShipType,
				  1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$Operator,
					 1=>"align='center'")
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
echo '</div>';//
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>

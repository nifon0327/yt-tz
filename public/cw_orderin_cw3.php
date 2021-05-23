<?php 
echo"<link rel='stylesheet' href='../model/mask.css'>";
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	//月份
	$SearchRows="";	
	$SearchRows=" and M.Estate='0'";
	//收款标记
	echo"<select name='cwSign' id='cwSign' onchange='document.form1.submit()'>";
	echo"<option value='1' selected>未收货款</option>";
	echo"<option value='0'>已收货款</option>";
	echo"</select>";
	$SearchRows.=" and M.cwSign>0";
	$date_Result = mysql_query("SELECT M.Date FROM $DataIn.ch1_shipmain M WHERE 1 $SearchRows GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"$nowWebPage\")'>";
		 echo"<option value='' selected>全部</option>";		
		do{			
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$StartDate=$dateValue."-01";
			$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
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
		do{			
			$thisCompanyId=$clientRow["CompanyId"];
			$Forshort=$clientRow["Forshort"];
			$CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort</option>";
				$SearchRows.=" and M.CompanyId='$thisCompanyId' ";
				$ModelCompanyId=$thisCompanyId;
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";					
				}
			}while ($clientRow = mysql_fetch_array($clientResult));
		echo"</select>&nbsp;";
		}
	}
//$otherAction="<span onClick='javascript:showMaskDiv(\"$funFrom\",\"$ModelCompanyId\",\"public\")' $onClickCSS>收款</span>&nbsp;";

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
//结付的银行
include "../model/selectbank1.php";
echo"$CencalSstr";
echo "<span onClick='javascript:showMaskDiv(\"$funFrom\",\"$ModelCompanyId\",\"public\")'  class='btn-confirm' style='margin-left: 50px'>收款</span>&nbsp;";
include "../model/subprogram/read_model_5.php";

$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
M.Id,M.CompanyId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.cwSign,M.Operator,C.Forshort 
FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
WHERE 1 $SearchRows
ORDER BY M.Date DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
	$d3=anmaIn("download/cw_invoice/",$SinkOrder,$motherSTR);
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
		//财务请款文件
		$cwfilename="../download/cw_invoice/$InvoiceNO.pdf";
		if(file_exists($cwfilename)){
			$f3=anmaIn($InvoiceNO.".pdf",$SinkOrder,$motherSTR);
			$cw_InvoiceFile="<a href=\"openorload.php?d=$d3&f=$f3&Type=&Action=6\" target=\"download\">查看</a>";
			}
		else{
			$cw_InvoiceFile="&nbsp;";
			}
		//Invoice查看
		//加密参数
		$f1=anmaIn($InvoiceNO.".pdf",$SinkOrder,$motherSTR);
		 if ($InvoiceFile==1){
             $dfname=urldecode($InvoiceNO);
	        $InvoiceFile=strlen($InvoiceNO)>20?"<a href=\"../admin/openorload.php?dfname=$dfname&Type=invoice\" target=\"download\">$InvoiceNO</a>":"<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\" >$InvoiceNO</a>";
        }
        else{
	        $InvoiceFile="&nbsp;";
        }

		//$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\">$InvoiceNO</a>";
		if($CompanyId==1001){
			$d2=anmaIn("download/invoice/mca/",$SinkOrder,$motherSTR);
			$InvoiceFile="&nbsp;&nbsp;<a href=\"openorload.php?d=$d2&f=$f1&Type=&Action=6\" target=\'download\'>★</a>";
			}
		$Wise=$myRow["Wise"]==""?"&nbsp;":$myRow["Wise"];
		$Date=$myRow["Date"];
		$cwSign=$myRow["cwSign"];
		
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		//出货金额
		//$checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$Id'",$link_id));
		$checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(substr(Qty*Price,1,position('.' in Qty*Price)+2)) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$Id'",$link_id));
		
		//echo "SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$Id'";
		$Amount=sprintf("%.2f",$checkAmount["Amount"]*$myRow["Sign"]);
		$LockRemark="";
		if($cwSign==2){
			//$LockRemark="部分收款，禁止直接收款.";
		}
		
		//判断是否部分结付
		$payAmountStr="";$payColor="";
		$checkPayed=mysql_fetch_array(mysql_query("SELECT (-M.PreAmount+M.PayAmount) AS PayAmount
 	FROM $DataIn.cw6_orderinsheet S 
	LEFT JOIN $DataIn.cw6_orderinmain M ON S.Mid=M.Id
	WHERE  S.chId='$Id'",$link_id));
	   if ($checkPayed["PayAmount"]>0){
		    $payAmount="已付款:" . $checkPayed["PayAmount"];
		    $payAmountStr=" title='$payAmount   标识:$cwSign' ";
		    $payColor=" style='text-decoration:underline;' ";
	   }
		
		
		//检查货款是否逾期
		include  "../admin/subprogram/ch_pay_check.php"; 
        $OrderSignColor=$PaySign==0?" bgcolor='#FF0000' title='$PayTerm' ":"";	
		
		$Locks=1;
		$showPurchaseorder="<img onClick='sOrhOrder(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏出货订单明细.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
		$ValueArray=array(
			array(0=>$Number,		1=>"align='center'"),
			array(0=>$Forshort),
			array(0=>$InvoiceNO),
			array(0=>$cw_InvoiceFile,	1=>"align='center'"),
			array(0=>$InvoiceFile,	1=>"align='center'"),
			array(0=>$BoxLable,		1=>"align='center'"),
			array(0=>$Amount,		1=>"align='right' $payAmountStr $payColor"),
			array(0=>$Date, 		1=>"align='center'"),
			array(0=>$Wise),
			array(0=>$Operator, 	1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
	//预收货款
	////////////////////////////////////////////////////////
		$DjTable="";
		$checkDj=mysql_query("SELECT S.Id,S.Amount,S.Remark,S.PayDate,P.Name AS Operator 
		FROM $DataIn.cw6_advancesreceived S
		LEFT JOIN $DataPublic.staffmain P ON P.Number=S.Operator
		 WHERE S.CompanyId='$CompanyId' AND Mid='0'",$link_id);
		if($checkRow = mysql_fetch_array($checkDj)){
			$DjTable="<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
			<tr><td class='A0111' height='30'>未抵付预收款</td></tr></table>
			<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
			<tr class=''>
			<td width='40' height='25' class='A0111' align='center'>选项</td><td width='40' class='A0101' align='center'>序号</td>
			<td width='80' class='A0101' align='center'>收款日期</td>
			<td class='A0101' align='center'>收款说明</td>
			<td width='115' class='A0101' align='center'>预收金额</td>
			<td width='50' class='A0101' align='center'>操作员</td>
			</tr>
			";
			$d=1;
			do{
				$djId=$checkRow["Id"];
				$djAmount=$checkRow["Amount"]<0?"<div class='redB'>$checkRow[Amount]</div>":$checkRow["Amount"];
				$djRemark=$checkRow["Remark"];
				$djDate=$checkRow["PayDate"];
				$djOperator=$checkRow["Operator"];
				$DjTable.="<tr>
				<td align='center' class='A0111' height='25'><input name='checkdj[]' type='checkbox' id='checkdj$d' value='$djId'></td>
				<td align='center' class='A0101'>$d</td>
				<td align='center' class='A0101'>$djDate</td>
				<td class='A0101'>$djRemark</td>
				<td align='center' class='A0101'>$djAmount</td>
				<td align='center' class='A0101'>$djOperator</td>
				</tr>
				";
				$d++;
				}while ($checkRow = mysql_fetch_array($checkDj));
			$DjTable.="</table>";
		}
	////////////////////////////////////////////////////////
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
echo $DjTable;
SetMaskDiv();//遮罩初始化
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script>
function ckeckForm(){
	//检查Invoice名称/日期
	var PayDate=document.form1.PayDate.value;
	var checkPayDate=ymdCheck(PayDate);
	var Message="";
	if(checkPayDate==false){
		Message="日期不对!";
		}
	if(Message!=""){
		alert(Message);return false;	
		}
	else{
		for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			var NameTemp=e.name;
			var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
			if (e.type=="checkbox" && Name!=-1){
				e.disabled=false;
				} 
			}
		document.form1.action="cw_orderin_updated.php?ActionId=18";
		document.form1.submit();
		}
	}
</script>
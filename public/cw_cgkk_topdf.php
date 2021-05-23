<?php
defined('IN_COMMON') || include '../basic/common.php';
include "cgkk_Blue/config.php";
require_once('../model/codefunjpg.php');

$mySql1="SELECT M.CompanyId,M.Date,M.BillNumber,P.Forshort,I.Company,I.Fax,C.Name,
         I.Address,M.Remark
         FROM $DataIn.cw15_gyskkmain M 
         LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
		 LEFT JOIN $DataIn.currencydata C ON C.Id=P.Currency
         LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=P.CompanyId and I.Type=8 
         WHERE M.Id=$Id LIMIT 1";
$mainResult = mysql_query($mySql1,$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$CompanyId=$mainRows["CompanyId"];
	$Date=date("d-M-y",strtotime($mainRows["Date"]));
	$BillNumber=$mainRows["BillNumber"];
	$Company=$mainRows["Company"];
	$Fax=$mainRows["Fax"];
	$Address=$mainRows["Address"];
	$Remark=$mainRows["Remark"];
    $Forshort=$mainRows["Forshort"];
	$CurrencyName=$mainRows["Name"];
	}


//取得公司信息
include "../model/subprogram/mycompany_info.php";


$mySql2="SELECT S.PurchaseID,S.StockId,S.StuffId,S.Qty,S.Price, S.Amount,A.Name AS CgName,S.StuffName,S.Remark AS SheetRemark,M.Picture,B.Mobile 
FROM $DataIn.cw15_gyskksheet S
LEFT JOIN $DataIn.cw15_gyskkmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
LEFT JOIN $DataIn.staffmain A ON A.Number=G.BuyerId
LEFT JOIN $DataIn.staffsheet B ON B.Number = A.Number 
WHERE S.Mid='$Id'";
$TotalAmount=0;
$TotalQty=0;
$i=1;
$sheetResult = mysql_query($mySql2,$link_id);
if($sheetRows = mysql_fetch_array($sheetResult)){
    $CgName=$sheetRows["CgName"];
    $Mobile=$sheetRows["Mobile"];
    $CgName= $CgName."(".$Mobile.")";
	do{
	    $StockId=$sheetRows["StockId"]==0?"":$sheetRows["StockId"];
		$StuffId=$sheetRows["StuffId"]==0?"":$sheetRows["StuffId"];
		$PurchaseID=$sheetRows["PurchaseID"]==0?"":$sheetRows["PurchaseID"];
		$StuffCname=$sheetRows["StuffName"];
		$SheetRemark=$sheetRows["SheetRemark"];
		$Qty=$sheetRows["Qty"];
		$TotalQty+=$Qty;
		$Price=$sheetRows["Price"];
		$Picture=$sheetRows["Picture"];
		$Amount=$sheetRows["Amount"];
		$TotalAmount+=$Amount;
		$eurTableNo="eurTableNo".strval($i);   //每一条记录都是一个表格
		$$eurTableNo="<table  border=1 ><tr>
		<td width=9 valign=middle align=center height=$RowsHight>$i</td>
		<td width=20 valign=middle align=center >$PurchaseID</td>
		<td width=70 valign=middle align=left>$StuffCname</td>
		<td width=15 valign=middle align=right >$Qty</td>
		<td width=15 valign=middle align=right >$Price</td>
		<td width=15 valign=middle align=right >$Amount</td>
		<td width=40  align=left >$SheetRemark</td>
		</tr></table>";
		$i++;
		}while ($sheetRows = mysql_fetch_array($sheetResult));
	}
    $Counts=$i;

	$eurTableNo="eurTableNo".strval($Counts);

	$$eurTableNo=" 
	<table  border=1 >
	<tr >
	<td width=9   align=center valign=middle height=$RowsHight></td>
	<td width=90  align=left valign=middle ></td>
	<td width=15  align=right valign=middle ></td>
	<td width=30  align=right valign=middle ></td>
	<td width=40  align=center valign=middle ></td>
	</tr>
	<tr >
	<td width=9  align=center valign=middle height=53></td>
	<td width=90 align=left   valign=middle ></td>
	<td width=15 align=right   valign=middle ></td>
	<td width=30 align=right   valign=middle ></td>
	<td width=40 align=center valign=middle ></td>
	</tr>
	</table>";


	$eurTableNoTotal="<table  border=0 >
	<tr bgcolor=#E8F5FC repeat>
	<td width=9   align=center valign=middle height=7>合计:</td>	
	<td width=90  align=left   valign=middle ></td>
	<td width=15  align=right  valign=middle >$TotalQty</td>	
	<td width=30  align=right  valign=middle >$TotalAmount</td>	
	<td width=40  align=center  valign=middle ></td>
	</tr></table>";

	$filename="../download/cgkkbill/$BillNumber.pdf";
	if(file_exists($filename)){unlink($filename);}
	include "cgkk_Blue/cgkkmodel.php";

	$Picture=$BillNumber.".jpg";
	$this_Photo="../download/cgkkbill/".$Picture;
	if(file_exists($this_Photo)){
			$pdf->AddPage();
			$pdf->Image($this_Photo,10,10,0,0,"JPG");
	    }

	$pdf->Output("$filename","F");
	if($ActionId==26){
	$Log.="扣款单号为 $BillNumber 重置完毕!<br>";}
	else{$Log.="扣款单号为 $BillNumber 生成成功!<br>";}
?>
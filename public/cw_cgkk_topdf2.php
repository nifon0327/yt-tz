<?php
defined('IN_COMMON') || include '../basic/common.php';
if ($DataIn==""){
	include "../basic/parameter.inc";
}
$RowsHight=5;			//表格行高
$InvoiceHeadFontSize=8;	//头文字字体大小
$TableFontSize=8;		//表格字体大小
$Commoditycode="";
$FromFunPos="CH";
//公司信息//
$mySql1="SELECT M.CompanyId,M.Date,M.BillNumber,P.Forshort,I.Company,I.Fax,C.Name,
         I.Address,M.Remark
         FROM $DataIn.cw15_gyskkmain M 
         LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
		 LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
         LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=P.CompanyId and I.Type=2 
         WHERE M.Id=$Id LIMIT 1";
//echo $mySql1;
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

$CheckMySql=mysql_query("SELECT * FROM $DataIn.my1_companyinfo WHERE Type='S' AND cSign=7",$link_id);
if($CheckMyRow=mysql_fetch_array($CheckMySql)){
      $S_Company=$CheckMyRow["Company"];
	  $S_Forshort=$CheckMyRow["Forshort"];
	  $S_Tel =$CheckMyRow["Tel"];
	  $S_Address =$CheckMyRow["Address"];
	  $S_Fax=$CheckMyRow["Fax"];
	  $S_WebSite=$CheckMyRow["WebSite"];
	  $S_Email=$CheckMyRow["Email"];
	  $S_ZIP=$CheckMyRow["ZIP"];
   }


$mySql2="SELECT S.PurchaseID,S.StockId,S.StuffId,S.Qty,S.Price, S.Amount,A.Name AS CgName,S.StuffName,S.Remark AS SheetRemark,M.Picture 
FROM $DataIn.cw15_gyskksheet S
LEFT JOIN $DataIn.cw15_gyskkmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
LEFT JOIN $DataPublic.staffmain A ON A.Number=G.BuyerId
WHERE S.Mid='$Id'";
$TotalAmount=0;
$TotalQty=0;
$i=1;
$sheetResult = mysql_query($mySql2,$link_id);
if($sheetRows = mysql_fetch_array($sheetResult)){
    $CgName=$sheetRows["CgName"];
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
		<td width=10 valign=middle align=center height=18>$i</td>
		<td width=24 valign=middle align=center >$PurchaseID</td>
		<td width=70 valign=middle align=center>$StuffCname</td>
		<td width=17 valign=middle align=right >$Qty</td>
		<td width=17 valign=middle align=right >$Price</td>
		<td width=17 valign=middle align=right >$Amount</td>
		<td width=40  align=left >$SheetRemark</td>
		</tr></table>";
		$i++;
		}while ($sheetRows = mysql_fetch_array($sheetResult));
	}
$Counts=$i;

//总计
$eurTableNo="eurTableNo".strval($Counts);
$$eurTableNo="<table  border=1 ><tr bgcolor='#CCCCCC'>
             <td width=34 valign=middle align=left height=$RowsHight style=bold>Total</td>
			 <td width=70 >&nbsp;</td>
			 <td width=17 valign=middle align=right style=bold>$TotalQty</td>
			 <td width=17 >&nbsp;</td>
			 <td width=17 valign=middle align=right style=bold>$TotalAmount</td>
			 <td width=40 >&nbsp;</td>
             </tr><tr>
  		      <td colspan=6  height=45  align='left' valign='top'>&nbsp;<br>备注:</td>
  		      </tr></table>";
$RemarkTableNo="<table border=0><tr>
  		      <td width='120' valign='top'>$Remark</td></tr></table>";

//输出Invoice
$filename="../download/cgkkbill/$BillNumber.pdf";
if(file_exists($filename)){unlink($filename);}
include "cw_cgkk_topdfmodel.php";

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

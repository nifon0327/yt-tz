<?php
defined('IN_COMMON') || include '../basic/common.php';

//电信-zxq 2012-08-01
//扣款单另行处理
$RowsHight=5;			//表格行高
$InvoiceHeadFontSize=8;	//头文字字体大小
$TiTleFontSize=8;
$TableFontSize=8;		//表格字体大小
$Commoditycode="";
//*********************注意表格宽度最长为195，否则自动新增一页生成
//公司信息
//include "../model/subprogram/mycompany_info.php";
$mainResult = mysql_query("SELECT M.CompanyId,M.InvoiceNO,M.Wise,M.Notes,M.Terms,M.PaymentTerm,M.Date,U.Symbol,I.Company,I.Fax,I.Address,D.InvoiceModel,D.SoldTo,D.Address AS ToAddress,D.FaxNo,
B.Beneficary,B.Bank,B.BankAdd,B.SwiftID,B.ACNO,S.Nickname
FROM $DataIn.ch1_shipmain M 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.currencydata U ON C.Currency=U.Id 
LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=C.CompanyId and I.Type=2 
LEFT JOIN $DataIn.ch8_shipmodel D ON D.Id=M.ModelId 
LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
LEFT JOIN $DataPublic.staffmain S ON S.Number=C.Staff_Number
WHERE M.Id=$Id LIMIT 1",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$CompanyId=$mainRows["CompanyId"];
	/*
	if($CompanyId==1031){ //Elite 的Note要显示Commodity code:8517709000
		$Commoditycode="Commodity code:8517709000";
	}
	*/
	include "invoicetopdf/company_info.php";  //相应公司附近加的信息

	$InvoiceNO=$mainRows["InvoiceNO"];
	$Invoice_PI="Invoice NO.:$InvoiceNO";
	$Wise=$mainRows["Wise"];

	$Notes=$mainRows["Notes"];
	$Terms=$mainRows["Terms"];
	$PaymentTerm=$mainRows["PaymentTerm"];
	$PaymentTerm=$PaymentTerm==""?"":"Payment term:$PaymentTerm<br>";  //放在Terms里

	$Date=date("d-M-y",strtotime($mainRows["Date"]));
	$Symbol=$mainRows["Symbol"]=="USD"?"U.S.DOLLARS":$mainRows["Symbol"];
	$Company=$mainRows["Company"];
	$Fax=$mainRows["Fax"];
	$Address=$mainRows["Address"];
	$InvoiceModel=$mainRows["InvoiceModel"];
	$SoldTo=$mainRows["SoldTo"]==""?$Company:$mainRows["SoldTo"];
	$ToAddress=$mainRows["ToAddress"]==""?$Address:$mainRows["ToAddress"];
	$FaxNo=$mainRows["FaxNo"]==""?$Fax:$mainRows["FaxNo"];
	$Beneficary=$mainRows["Beneficary"];
	$Bank=$mainRows["Bank"];
	$BankAdd=$mainRows["BankAdd"];
	$SwiftID=$mainRows["SwiftID"];
	$ACNO=$mainRows["ACNO"];
	$Nickname=$mainRows["Nickname"];

	if ($Wise=="BSD")  {
		$Address="BSD";$Company="BSD";
	}


	}

include "../model/subprogram/mycompany_info.php";  //公司信息
//Invocse列表
$chSUMQty=0;
$boxSUMQty=0;
$Total=0;

//非装箱项目
$unPackingSamp=mysql_query("
SELECT S.Id,S.POrderId,C.PO,C.Description AS cName,'' AS eCode,C.Description,S.Qty,S.Price,S.Type,S.YandN 
FROM $DataIn.ch1_shipsheet S 
LEFT JOIN $DataIn.ch6_creditnote C ON C.Number=S.POrderId WHERE S.Mid='$Id' AND S.Type='3'",$link_id);
if($unPackingRow=mysql_fetch_array($unPackingSamp)){
	$i=1;
	do{
		$OrderPO=$unPackingRow["PO"];
		$Description=$unPackingRow["Description"];
		$Qty=$unPackingRow["Qty"];
		$Price=$unPackingRow["Price"];
		$Amount=sprintf("%.2f",$Qty*$Price);
		$chSUMQty=$chSUMQty+$Qty;
		$Total=sprintf("%.2f",$Total+$Amount);

		$eurTableNo="eurTableNo".strval($i);   //每一条记录都是一个表格
		$$eurTableNo="<table  border=1 ><tr>
		<td width=15 valign=middle align=center height=$RowsHight>$i</td>
		<td width=30 valign=middle>$OrderPO</td>
		<td width=93 valign=middle>$Description</td>
		<td width=19 valign=middle align=right>$Qty</td>
		<td width=19 valign=middle align=right>$Price</td>
		<td width=19 valign=middle align=right>$Amount</td>
		</tr></table>";	 	//	<td width=35 valign=middle>$eCode</td>


		$i++;
		}while ($unPackingRow=mysql_fetch_array($unPackingSamp));
	}
//合计
/*
$iTableList.="<tr bgcolor=#CCCCCC>
<td colspan=3 height=$RowsHight valign=middle style=bold>Total</td>
<td align=right valign=middle style=bold>$chSUMQty</td>
<td></td>
<td align=right valign=middle style=bold>$Total</td>
</tr></table>";
*/
$Counts=$i;  //记录条数
$eurTableNo="eurTableNo".strval($Counts);

$Note_Bank_Height=44; //预留高度给尾页的Note,BANK
$$eurTableNo=" 
	<table  border=1 ><tr bgcolor=#CCCCCC>
	<td width=15 height=$RowsHight valign=middle style=bold>TOTAL</td>
	<td width=30></td>
	<td width=93></td>
	<td width=19 align=right valign=middle  style=bold>$chSUMQty</td>
	<td width=19></td>
	<td width=19 align=right valign=middle  style=bold >$Total</td>
	</tr>
	</table>
    
	<table  border=0  rules=none bordercolor=#FFF>
	<tr border=0  >
		<td width=0 height=$Note_Bank_Height ></td></tr>
	</table>
	
	"; //height=35,主要放NOTE，BANK 预留的高度,以便计算一页的位置!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

//为了字体颜色，只能用重叠方式来写
$eurTableToRight=145; //138=15+30+93 前面统计表的左边不需要的,还不能计算，相关7个单位
$eurTableTotal=" 
	<table  border=1 >
	<tr bgcolor=#CCCCCC>
	<td width=19 height=$RowsHight align=right valign=middle  style=bold>$chSUMQty</td>
	<td width=19></td>
	<td width=19 align=right valign=middle  style=bold >$Total</td>
	</tr>
	</table>
	";



$eurTableField="<table  border=1 >
<tr bgcolor=#E1E1E1 repeat>
<td width=15 align=center height=$RowsHight valign=middle style=bold>LN</td>
<td width=30 align=center valign=middle style=bold>PO#</td>
<td width=93 align=center valign=middle style=bold>DESCRIPTION</td>
<td width=19 align=center valign=middle style=bold>QTY</td>
<td width=19 align=center valign=middle style=bold>U/P</td>
<td width=19 align=center valign=middle style=bold>AMOUNT</td>
</tr></table>" ;
$eurEmptyField="<table  border=1 >
<tr >
<td width=15 align=center height=$RowsHight valign=middle style=bold> </td>
<td width=30 align=center valign=middle style=bold> </td>
<td width=93 align=center valign=middle style=bold> </td>
<td width=19 align=center valign=middle style=bold> </td>
<td width=19 align=center valign=middle style=bold> </td>
<td width=19 align=center valign=middle style=bold> </td>
</tr></table>" ;//$eurTableList;


//输出creditnote
//英文格式日期
$Date=date("d-M-y");
$filename="../download/invoice/".$InvoiceNO.".pdf";
//$filename="../download/invoice/123.pdf";
if(file_exists($filename)){unlink($filename);}
include "invoicetopdf_new/creditnotemodel.php";
/*
switch($CompanyId){
	case 1039:
	case 1087:
	case 1093:
	     include "invoicetopdf_new/creditnotemode2.php";
	     break;
	default:
	     include "invoicetopdf_new/creditnotemodel.php";
		 //echo "-----------------";
	     break;
}
*/
$pdf->Output("$filename","F");
$Log.="<br>扣款资料已生成.";
?>
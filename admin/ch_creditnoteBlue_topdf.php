<?php
defined('IN_COMMON') || include '../basic/common.php';

//电信-zxq 2012-08-01
//扣款单另行处理
include "invoicetopdf_blue/config.php";  //字体颜色，行高等配置文件
$Commoditycode="";
//*********************注意表格宽度最长为195，否则自动新增一页生成
//公司信息
//include "../model/subprogram/mycompany_info.php";
$mainResult = mysql_query("SELECT M.CompanyId,M.InvoiceNO,M.Wise,M.Notes,M.Terms,M.PaymentTerm,M.Date,U.Symbol,I.Company,I.Fax,I.Address,D.InvoiceModel,D.SoldTo,D.Address AS ToAddress,D.FaxNo,D.SoldFrom,D.FromAddress,D.Title AS SoldTitle,
B.Beneficary,B.Bank,B.BankAdd,B.SwiftID,B.ACNO,S.Nickname,F.Mobile
FROM $DataIn.ch1_shipmain M 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.currencydata U ON C.Currency=U.Id 
LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=C.CompanyId and I.Type=8 
LEFT JOIN $DataIn.ch8_shipmodel D ON D.Id=M.ModelId 
LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
LEFT JOIN $DataPublic.staffmain S ON S.Number=C.Staff_Number
LEFT JOIN $DataPublic.staffsheet F ON F.Number=S.Number
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
	$ShipDate=$mainRows["Date"];
	$Symbol=$mainRows["Symbol"]=="USD"?"U.S.DOLLARS":$mainRows["Symbol"];
	$Company=$mainRows["Company"];
	$Fax=$mainRows["Fax"];
	$Address=$mainRows["Address"];
	$InvoiceModel=$mainRows["InvoiceModel"];

	$SoldTo=$mainRows["SoldTo"]==""?$Company:$mainRows["SoldTo"];
	$ToAddress=$mainRows["ToAddress"]==""?$Address:$mainRows["ToAddress"];
	/*
	$SoldTo=$mainRows["SoldTo"]==""?$Company:$mainRows["SoldTo"];
	$ToAddress=$mainRows["ToAddress"]==""?$Address:$mainRows["ToAddress"];
	*/

	$FaxNo=$mainRows["FaxNo"]==""?$Fax:$mainRows["FaxNo"];

	if ($CompanyId==100129 ){ //TGC
		$Company=$mainRows["SoldFrom"]==""?$Company:$mainRows["SoldFrom"];
		$Address=$mainRows["FromAddress"]==""?$Address:$mainRows["FromAddress"];
	}

	$Beneficary=$mainRows["Beneficary"];
	$Bank=$mainRows["Bank"];
	$BankAdd=$mainRows["BankAdd"];
	$SwiftID=$mainRows["SwiftID"];
	$ACNO=$mainRows["ACNO"];
	$Nickname=$mainRows["Nickname"];
	$Mobile=$mainRows["Mobile"];

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
		<td width=8 valign=middle align=center height=$RowsHight>$i</td>
		<td width=128 valign=middle>$OrderPO $Description</td>
		<td width=14 valign=middle align=right>$Qty</td>
		<td width=17 valign=middle align=right>$Price</td>
		<td width=17 valign=middle align=right>$Amount</td>
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

$$eurTableNo=" 
<table  border=0 >
<tr  repeat>
<td width=29  align=left height=$RowsHight valign=middle >&nbsp;</td>
<td width=35 align=left valign=middle  ></td>
<td width=56 align=left valign=middle ></td>	
<td width=19 align=left valign=middle ></td>			
<td width=14 align=right valign=middle ></td>	
<td width=14 align=right valign=middle ></td>	
<td width=17 align=right valign=middle ></td></tr>
<tr >
<td width=29  align=left height=14 valign=middle ></td>
<td width=35 align=left valign=middle  ></td>
<td width=56 align=left valign=middle ></td>	
<td width=19 align=left valign=middle ></td>			
<td width=14 align=right valign=middle ></td>	
<td width=14 align=right valign=middle ></td>	
<td width=17 align=right valign=middle ></td></tr>
</table>";  //为了给签名拉开行距

$eurTableNoTotal=" 
<table  border=0 >
<tr bgcolor=#E8F5FC repeat>
<td width=29  align=left height=$RowsHight valign=middle >TOTAL:</td>
<td width=35 align=left valign=middle  ></td>
<td width=56 align=left valign=middle ></td>	
<td width=19 align=left valign=middle ></td>			
<td width=14 align=right valign=middle ></td>	
<td width=14 align=right valign=middle ></td>	
<td width=17 align=right valign=middle ></td></tr></table>";

$BankTable ="
<table  border=0 >
<tr   >
<td width=18 align=left valign=middle >BENEFICIARY:</td>
<td width=166 align=left valign=middle  >$Beneficary</td>
</tr>
<tr >
<td width=18  align=left  valign=middle >BANK:</td>
<td width=166 align=left valign=middle  >$Bank</td>
</tr>
<tr >
<td width=18  align=left valign=middle >BANK Add:</td>
<td width=166 align=left valign=middle  >$BankAdd</td>
</tr>
<tr >
<td width=18  align=left valign=middle >SWIFT ID:</td>
<td width=166  align=left valign=middle  >$SwiftID</td>
</tr>
<tr >
<td width=18  align=left valign=middle >A/C NO:</td>
<td width=166 align=left valign=middle  >$ACNO</td>
</tr>
</table>";

$ChinaBankTable ="
<table  border=0 >
<tr   >
<td width=18 align=left valign=middle >开户名:</td>
<td width=166 align=left valign=middle  >$Beneficary</td>
</tr>
<tr >
<td width=18  align=left  valign=middle >开户行:</td>
<td width=166 align=left valign=middle  >$Bank</td>
</tr>
<tr >
<td width=18  align=left valign=middle >银行地址:</td>
<td width=166 align=left valign=middle  >$BankAdd</td>
</tr>
<tr >
<td width=18  align=left valign=middle >SWIFT ID:</td>
<td width=166  align=left valign=middle  >$SwiftID</td>
</tr>
<tr >
<td width=18  align=left valign=middle >A/C NO:</td>
<td width=166 align=left valign=middle  >$ACNO</td>
</tr>
</table>";


//输出creditnote
//英文格式日期
$Date=date("d-M-y");
$filename="../download/invoice/".$InvoiceNO.".pdf";
//$filename="../download/invoice/123.pdf";
if(file_exists($filename)){unlink($filename);}

switch($CompanyId){
    case 1036://QT（rmb）
      $Company=$mainRows["SoldTitle"];
      $Address=$mainRows["SoldFrom"];
	case 1039:
	case 1087:
	case 1093:
	case 100064://魅族
	case 100031://富士康
	case 2397://孚邦
	     include "invoicetopdf_blue/creditnotemode2.php";
	     break;
	default:
	    if ($InvoiceModel==2)
	         include "invoicetopdf_blue/creditnotemode2.php";
	    else
	         include "invoicetopdf_blue/creditnotemodel.php";
		 //echo "-----------------";
	     break;
}
//include "invoicetopdf_blue/creditnotemode2.php";
$pdf->Output("$filename","F");
$Log.="<br>扣款资料已生成.";
?>
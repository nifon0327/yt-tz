<?php
defined('IN_COMMON') || include '../basic/common.php';
//电信-zxq 2012-08-01
$RowsHight=5;			//表格行高
$InvoiceHeadFontSize=8;	//头文字字体大小
$TableFontSize=8;		//表格字体大小
$Commoditycode="";
$FromFunPos="CH";
$iTableList="";
$mcaTableList="";
$eurTableList="";
//不生成packlist
$plTableList="";
$eurplList="";
//公司信息//
//include "../model/subprogram/mycompany_info.php";  //放在中间
$mySql1="SELECT M.CompanyId,M.InvoiceNO,M.Wise,M.Notes,M.Terms,M.PaymentTerm,M.Ship,M.Date,U.Symbol,I.Company,I.Fax,I.Address,D.InvoiceModel,D.SoldTo,D.Address AS ToAddress,D.FaxNo,D.SoldFrom,D.FromAddress,D.FromFaxNo,
	B.Beneficary,B.Bank,B.BankAdd,B.SwiftID,B.ACNO,S.Nickname,S.Name as ZName 
	FROM $DataIn.ch1_shipmain M 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata U ON C.Currency=U.Id 
	LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=C.CompanyId and I.Type=1 
	LEFT JOIN $DataIn.ch8_shipmodel D ON D.Id=M.ModelId 
	LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
	LEFT JOIN $DataPublic.staffmain S ON S.Number=C.Staff_Number
	WHERE M.Id=$Id LIMIT 1";
$mainResult = mysql_query($mySql1,$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$CompanyId=$mainRows["CompanyId"];
    $Ship=$mainRows["Ship"];
	include "cw_invoicetopdf/company_info.php";  //相应公司附近加的信息
	$InvoiceNO=$mainRows["InvoiceNO"];
	$Invoice_PI="$InvoiceNO";
	$Wise=$mainRows["Wise"];
	$Notes=$mainRows["Notes"];
	$Terms=$mainRows["Terms"];
	$Date=date("d-M-y",strtotime($mainRows["Date"]));
	$Symbol=$mainRows["Symbol"]=="USD"?"U.S.DOLLARS":$mainRows["Symbol"];
	$Company=$mainRows["Company"];
	$Fax=$mainRows["Fax"];
	$Address=$mainRows["Address"];
	$InvoiceModel=$mainRows["InvoiceModel"];
	$SoldTo=$mainRows["SoldTo"]==""?$Company:$mainRows["SoldTo"];
	$ToAddress=$mainRows["ToAddress"]==""?$Address:$mainRows["ToAddress"];
	$FaxNo=$mainRows["FaxNo"]==""?$Fax:$mainRows["FaxNo"];
	//放在后面才行 add by zx 2012-01-07
	$Company=$mainRows["SoldFrom"]==""?$Company:$mainRows["SoldFrom"];
	$Address=$mainRows["FromAddress"]==""?$Address:$mainRows["FromAddress"];
	$Fax=$mainRows["FromFaxNo"]==""?$Fax:$mainRows["FromFaxNo"];
	$Beneficary=$mainRows["Beneficary"];
	$Bank=$mainRows["Bank"];
	$BankAdd=$mainRows["BankAdd"];
	$SwiftID=$mainRows["SwiftID"];
	$ACNO=$mainRows["ACNO"];
	$Nickname=$mainRows["Nickname"];
	$ZName=$mainRows["ZName"];
	}

$check_Id=$Id;
include "../admin/subprogram/ch_mycompany_check.php"; //临时区分ECHO订单出货公司
include "../model/subprogram/mycompany_info.php";
$chSUMQty=0;
$boxSUMQty=0;
$Total=0;
$mySql2="SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,P.Description,S.Qty,S.Price,S.Type,S.YandN,S.ProductId  as ProductId,PI.PaymentTerm,P.bjRemark,P.TypeId
	FROM $DataIn.ch1_shipsheet S 
	LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
	LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=O.Id 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId WHERE S.Mid='$Id' AND S.Type='1'
	UNION ALL 
	SELECT S.Id,S.POrderId,O.SampPO AS OrderPO,O.SampName AS cName,O.SampName AS eCode,O.Description,
	S.Qty,S.Price,S.Type,S.YandN,'0' as ProductId,'' AS PaymentTerm ,'' AS bjRemark,'' AS TypeId 
	FROM $DataIn.ch1_shipsheet S 
	LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId WHERE S.Mid='$Id' AND S.Type='2' AND O.Type='1'";
$sheetResult = mysql_query($mySql2,$link_id);
$i=1;
if($sheetRows = mysql_fetch_array($sheetResult)){
	$PaymentTerm=$sheetRows["PaymentTerm"];
	$PaymentTerm=$PaymentTerm==""?"":"Payment term:$PaymentTerm<br>";  //放在Terms里
	do{
		$OrderPO=$sheetRows["OrderPO"];
		$OrderPO=$OrderPO==""?" ":$OrderPO;
		$cName=$sheetRows["cName"];
		$eCode=$sheetRows["eCode"];
		$Description=$sheetRows["Description"];
        $ShipType=$sheetRows["ShipType"];
		$Qty=$sheetRows["Qty"];
		$Price=$sheetRows["Price"];
		$Amount=sprintf("%.2f",$Qty*$Price);
		$chSUMQty=$chSUMQty+$Qty;
		$Total=sprintf("%.2f",$Total+$Amount);
		$bjRemark=$sheetRows["bjRemark"];
		$color="";
		$ProductId=$sheetRows["ProductId"];
		if($CompanyId==1064 && $ProductId!='0'){  //看是否出过货
			$Pre_Temp=mysql_query("SELECT ProductId  FROM $DataIn.ch1_shipsheet where ProductId='$ProductId' AND Mid!='$Id'   LIMIT 1",$link_id);  //看是否出过货
			if(!($MasterRow = mysql_fetch_array($Pre_Temp))){
				$color="color=#FF0000";
				}
			}
		//海关编码
		$TypeId=$sheetRows["TypeId"];
		if($TypeId!=""){
			$HSResult=mysql_fetch_array(mysql_query("SELECT HSCode FROM $DataIn.customscode WHERE ProductId='$ProductId'",$link_id));
			$HSCode=$HSResult["HSCode"];
			}
		else
			$HSCode="";
		$mcaTableList.="<tr><td valign=middle align=center height=$RowsHight $color>$i</td><td valign=middle $color>$OrderPO</td><td valign=middle $color>$eCode</td><td valign=middle $color>$Description</td><td valign=middle align=right $color>$Qty</td></tr>";
		$eurTableList.="<tr><td valign=middle align=center height=$RowsHight>$i</td><td valign=middle>$eCode</td><td valign=middle align=right>$Qty</td><td valign=middle align=right>$Price</td><td valign=middle align=right>$Amount</td></tr>";
		$eurTableNo="eurTableNo".strval($i);   //每一条记录都是一个表格
		if($InvoiceModel!=2){
		    $$eurTableNo="<table  border=1 ><tr>
		    <td width=8 valign=middle align=center height=$RowsHight $color>$i</td>
		    <td width=21 valign=middle $color>$OrderPO</td>
		    <td width=35 valign=middle $color>$eCode</td>
		    <td width=67 valign=middle $color>$Description</td>
		    <td width=19 valign=middle $color>$HSCode</td>
		    <td width=12 valign=middle align=right $color>$Qty</td>
		    <td width=16 valign=middle align=right $color>$Price</td>
		    <td width=17 valign=middle align=right $color>$Amount</td>
		    </tr></table>";
			}
		else{
		    if($CompanyId=='1075')$Description=$cName;
		    $$eurTableNo="<table  border=1 ><tr>
		    <td width=8 valign=middle align=center height=$RowsHight $color>$i</td>
		    <td width=25 valign=middle $color>$OrderPO</td>
		    <td width=35 valign=middle $color>$eCode</td>
		    <td width=70 valign=middle $color>$Description</td>
		    <td width=19 valign=middle align=right $color>$Qty</td>
		    <td width=19 valign=middle align=right $color>$Price</td>
		    <td width=19 valign=middle align=right $color>$Amount</td>
		    </tr></table>";
		    }

		//MCA
		$mcaTableNo="mcaTableNo".strval($i);   //不带价格的
		$$mcaTableNo="<table  border=1 ><tr>
		<td width=8 valign=middle align=center height=$RowsHight $color>$i</td>
		<td width=25 valign=middle $color>$OrderPO</td>
		<td width=35 valign=middle $color>$eCode</td>
		<td width=108 valign=middle $color>$Description</td>
		<td width=19 valign=middle align=right $color>$Qty</td>
		</tr></table>";
		//mco报价规则
		$RemarkTableNo="RemarkTableNo".strval($i);
		if($CompanyId=='1066' && $bjRemark!=""){
		   $$RemarkTableNo="<table  border=1>
				<tr bgcolor=#cccccc>
				<td width=195 align=right color='#FFFFFF' style=bold height=$RowsHight valign=middle>$bjRemark</td>
				</tr></table>";
		    }
		else{
		    $$RemarkTableNo="";
		    }
		$i++;
		}while ($sheetRows = mysql_fetch_array($sheetResult));
	}
$boxSUMQty=$chSUMQty;
//非装箱项目
$unPackingSamp=mysql_query("SELECT S.Id,S.POrderId,O.SampPO AS OrderPO,O.SampName AS cName,'' AS eCode,O.Description,S.Qty,S.Price,S.Type,S.YandN 
FROM $DataIn.ch1_shipsheet S 
LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId WHERE S.Mid='$Id' AND S.Type='2' AND O.Type='0'",$link_id);
if($unPackingRow=mysql_fetch_array($unPackingSamp)){
	do{
		$Description=$unPackingRow["Description"];
		$Qty=$unPackingRow["Qty"];
		$Price=sprintf("%.2f",$unPackingRow["Price"]);
		$Amount=sprintf("%.2f",$Qty*$Price);
		$Total=sprintf("%.2f",$Total+$Amount);
		$OrderPO="";
		$eCode="";

		$eurTableNo="eurTableNo".strval($i);   //每一条记录都是一个表格
		$$eurTableNo="<table  border=1 ><tr>
		<td width=8 valign=middle align=center height=$RowsHight>$i</td>
		<td width=21 valign=middle >$OrderPO</td>
		<td width=35 valign=middle >$eCode</td>
		<td width=67 valign=middle >$Description</td>
		<td width=19 valign=middle >$HSCode</td>
		<td width=12 valign=middle align=right >$Qty</td>
		<td width=16 valign=middle align=right >$Price</td>
		<td width=17 valign=middle align=right >$Amount</td>
		</tr></table>";
		//MCA
		$mcaTableNo="mcaTableNo".strval($i);   //不带价格的
		$$mcaTableNo="<table  border=1 ><tr>
		<td width=8 valign=middle align=center height=$RowsHight>$i</td>
		<td width=25 valign=middle>$OrderPO</td>
		<td width=35 valign=middle>$eCode</td>
		<td width=108 valign=middle>$Description</td>
		<td width=19 valign=middle align=right>$Qty</td>
		</tr></table>";
		$i++;
		}while ($unPackingRow=mysql_fetch_array($unPackingSamp));
	}
$Counts=$i;  //记录总条数
//合计
$mcaTableList.="<tr bgcolor=#CCCCCC><td colspan=4 height=$RowsHight valign=middle style=bold>Total</td><td align=right valign=middle style=bold>$chSUMQty</td></tr></table>";
$eurTableList.="<tr bgcolor=#CCCCCC>
<td colspan=2 height=$RowsHight valign=middle style=bold>Total</td>
<td align=right valign=middle style=bold>$chSUMQty</td>
<td></td>
<td align=right valign=middle style=bold>$Total</td>
</tr></table>";

//加上总计
$eurTableNo="eurTableNo".strval($Counts);
include "cw_invoicetopdf/invoicepublicTotal.php";  //把每一项分离

//mca加上总计
$mcaTableNo="mcaTableNo".strval($Counts);
$$mcaTableNo="<table  border=1 > <tr>
    <td  width=138 rowspan='4' align='left' valign='top'>Notes:<br>$Commoditycode$StableNote$Notes </td>
    <td  width=38 bgcolor='#999999' >SUBTOTAL</td>
    <td  width=19 align='right'>$Total</td>
  </tr>
  <tr>
    <td width=38 bgcolor='#999999'>DELIVERY COST</td>
    <td width=19 align='right'></td>
  </tr>
  <tr>
    <td width=38 bgcolor='#999999'>VAT</td>
    <td width=19 align='right'></td>
  </tr>
  <tr>
    <td  width=38 bgcolor='#999999'>TOTAL</td>
    <td  width=19 align='right'>$Total</td>
  </tr>
   <tr>
    <td colspan=3  height=17  align='left' valign='top'>Terms:<br>$PaymentTerm$Priceterm$Terms  </td>
  </tr>
  <tr>
  <td colspan=3  height=30  align='left' valign=middle >BANK:<br>Beneficary: $Beneficary<br>Bank         : $Bank<br>Bank Add : $BankAdd<br>Swift ID    : $SwiftID<br>A/C NO    : $ACNO</td>
  </tr> 
  </table>";

//输出Invoice
echo $InvoiceModel;
$filename="../download/cw_invoice/$InvoiceNO.pdf";
if(file_exists($filename)){unlink($filename);}
include "cw_invoicetopdf/invoicemodel_".$InvoiceModel.".php";
$pdf->Output("$filename","F");
$Log.="$InvoiceNO 请款Invoice生成完毕! <br>";
?>

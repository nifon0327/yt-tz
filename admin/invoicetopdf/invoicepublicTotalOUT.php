<?php   
//EUR专用模板  //二合一已更新
//$TableFontSize=8;
//$Priceterm=$Priceterm."BBBBB";
switch($InvoiceModel){	
	case 2:     //简体中文
		$N_SUBTOTAL="小计";
		$N_TOTAL="合计";
		$N_Currency="币种  :";		
		break;
	case 3:     //繁体中文
		$N_SUBTOTAL="小計";
		$N_TOTAL="合計";
		$N_Currency="幣别  :";
		break;
	default :   //其它英文版的
		$N_SUBTOTAL="SUBTOTAL";
		$N_TOTAL="TOTAL";
		$N_Currency="Currency  :";
		break;
}

$colspan="colspan=3";
$width="";


$tmp_Total=" <tr>
    <td  width=131 rowspan='4' align='left' valign='top'>&nbsp;       <br>$Commoditycode$StableNote$Notes </td>
    <td  width=47 bgcolor='#999999' >$N_SUBTOTAL</td>
    <td  width=17 align='right'>$Total</td>
  </tr>
  <tr>
    <td width=47 bgcolor='#999999'>DELIVERY COST</td>
    <td width=17 align='right'></td>
  </tr>
  <tr>
    <td width=47 bgcolor='#999999'>VAT</td>
    <td width=17 align='right'></td>
  </tr>
  <tr>
    <td  width=47 bgcolor='#999999'>$N_TOTAL</td>
    <td  width=17 align='right'>$Total</td>
  </tr>";
  

$tmp_Terms="<tr>
    <td $colspan  $width  height=17  align='left' valign='top'> &nbsp;      <br>$PaymentTerm$Priceterm$Terms  </td>
  </tr>
";
 
 
$tmp_Currency="
  <tr  >
  <td  $colspan  $width  height=8 align='left' valign=middle > &nbsp;           $OutSymbol</td>
  </tr>";
if ($ModelId==504){
$tmp_BANK=" <tr>
  <td $colspan  $width  height=30  align='left' valign=middle >&nbsp;     <br>Beneficiary:  $OutAccountName<br>Beneficiary bank Code: $OutBeneficiaryCode<br>Bank: $OutBeneficiary<br>Bank Add.: $OutBankAddress<br>Swift ID: $OutSWIFTAddress<br>A/C NO.: $OutAccountNumber</td>
  </tr> ";
}else{
$tmp_BANK=" <tr>
  <td $colspan  $width  height=30  align='left' valign=middle >&nbsp;     <br>Beneficiary:  $OutBeneficiary<br>Beneficiary bank Code: $OutBeneficiaryCode<br>SWIFT Address: $OutSWIFTAddress<br>Business Vantage Account Name: $OutAccountName<br>Business Vantage Account Number: $OutAccountNumber<br>Beneficiary bank add: $OutBankAddress</td>
  </tr> ";	
}

//目的是为了取得高度
/*
$$eurTableNo="<table  border=1 >
	$tmp_Total
	$tmp_Terms
	$tmp_Currency
	$tmp_BANK
</table>";
*/

if($CompanyId ==100359){
	$Origins =  "Origin of the goods: GOODS MADE IN CHINA";
}

$$eurTableNo="<table  border=1 >
	$tmp_Total
	$tmp_Terms
	$tmp_BANK
</table>";


$colspan="";
$width="width=195";


$tmp_Total="<table  border=1 >
	$tmp_Total
</table>";

$tmp_Terms="<table  border=1 >
	<tr>
    <td $colspan  $width  height=17  align='left' valign='top'>&nbsp;      <br>$PaymentTerm$Priceterm$Terms<br>$Origins  </td>
  </tr>
</table>";
  
$tmp_Currency="<table  border=1 >
	<tr  >
  <td  $colspan  $width  height=8 align='left' valign=middle >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$OutSymbol</td>
  </tr>
</table>";

$tmp_BANK="<table  border=1 >
  <tr>
	 <td $colspan  $width  height=30  align='left' valign=middle >&nbsp;     <br>Beneficiary:  $OutBeneficiary<br>Beneficiary bank Code: $OutBeneficiaryCode<br>SWIFT Address: $OutSWIFTAddress<br>Business Vantage Account Name: $OutAccountName<br>Business Vantage Account Number: $OutAccountNumber<br>Beneficiary bank add: $OutBankAddress</td>
  </tr>
</table>";



?>
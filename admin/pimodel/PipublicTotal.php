<?php   
//EUR专用模板  //二合一已更新
//$TableFontSize=8;

if($CompanyId==1018){
	$colspan="colspan=7";
	$width="";
		
	$tmp_Total=" <tr bgcolor=#CCCCCC>
		<td width=15 height=$RowsHight valign=middle style=bold>Total</td>
		<td width=30></td>
		<td width=63></td>
		<td width=19 ></td>
		<td width=19 align=right valign=middle style=bold>$QtySUM</td>
		<td width=19 align=right valign=middle style=bold>$AmountSUM</td>
		<td width=30 ></td>
		</tr>";
		
	$tmp_Notes="<tr><td $colspan  $width   height=12 align='left' valign='top'>Notes:<br>$Commoditycode$StableNote$Notes</td></tr>";	  
	$tmp_Terms="<tr>
		<td $colspan  $width  height=17  align='left' valign='top'> &nbsp;      <br>$PaymentTerm$Priceterm$Terms  </td>
	  </tr>
	";
	 	 
	$tmp_Currency="
	  <tr  >
	  <td  $colspan  $width  height=8 align='left' valign=middle > &nbsp;           $Symbol</td>
	  </tr>";
	$tmp_BANK=" <tr>
	  <td $colspan  $width  height=25  align='left' valign=middle >&nbsp;      <br>Beneficary: $Beneficary<br>Bank         : $Bank<br>Bank Add : $BankAdd<br>Swift ID    : $SwiftID &nbsp;&nbsp;&nbsp;A/C NO: $ACNO</td>
	  </tr> ";
	
	//目的是为了取得高度
	$$eurTableNo="<table  border=1 >
		$tmp_Total
		$tmp_Notes
		$tmp_Terms
		$tmp_BANK
	</table>";	

}
else {
	
	$colspan="colspan=8";
	$width="";
	$Add1058="";
	if($CompanyId==1058){  // forace 
		$Add1058="
		<tr>
			<td $colspan  $width  height=12  align='left' valign='middle'>Minus6% Rebates (1% Marketing,4.5% LTI/Ranging,0.5% Warranty/Bailment)</td>
		</tr>
		<tr>
			<td $colspan  $width  height=12  align='left' valign='middle'>Invoice Total - USD</td>
		</tr>";		
	}
	
	if ($CompanyId==1088 || $CompanyId==1090 ){
	      $tmp_Total=" <tr bgcolor=#CCCCCC>
				<td width=10 height=$RowsHight valign=middle style=bold>Total</td>
				<td width=20></td>
				<td width=30></td>
				<td width=45></td>
				<td width=18 align=right valign=middle style=bold>$QtySUM</td>
				<td width=16 align=right valign=middle style=bold>$AmountSUM</td>
				<td width=18></td>
				<td width=16></td>
				<td width=22></td>
				</tr> 
				
				";	
	}
	else{
			$tmp_Total=" <tr bgcolor=#CCCCCC>
				<td width=10 height=$RowsHight valign=middle style=bold>Total</td>
				<td width=20></td>
				<td width=47></td>
				<td width=18></td>
				<td width=16 align=right valign=middle style=bold>$QtySUM</td>
				<td width=18 align=right valign=middle style=bold>$AmountSUM</td>
				<td width=16></td>
				<td width=22></td>
				<td width=28></td>
				</tr> 
				
				";
	}	
	$tmp_Notes="	
	   <tr>
		<td $colspan  $width  height=12  align='left' valign='top'>Notes:<br>$Commoditycode$StableNote$Notes</td>
	  </tr>";
	  
	$tmp_Terms="<tr>
		<td $colspan  $width  height=17  align='left' valign='top'> &nbsp;      <br>$PaymentTerm$Priceterm$Terms  </td>
	  </tr>
	";
	 
	 
	$tmp_Currency="
	  <tr  >
	  <td  $colspan  $width  height=8 align='left' valign=middle > &nbsp;           $Symbol</td>
	  </tr>";
	$tmp_BANK=" <tr>
	  <td $colspan  $width  height=25  align='left' valign=middle >&nbsp;      <br>Beneficary: $Beneficary<br>Bank         : $Bank<br>Bank Add : $BankAdd<br>Swift ID    : $SwiftID &nbsp;&nbsp;&nbsp;A/C NO : $ACNO</td>
	  </tr> ";
	
	//目的是为了取得高度
	$$eurTableNo="<table  border=1 >
		$tmp_Total
		$Add1058
		$tmp_Notes
		$tmp_Terms
		$tmp_BANK
	</table>";		
	
}

$colspan="";
$width="width=195";
$tmp_Total="<table  border=1 >
	$tmp_Total
</table>";

$tmp_1058="<table  border=1 >	
	<tr>
		<td   width=136  height=10  align='left' valign='middle'>Minus 6% Rebates (1% Marketing,4.5% LTI/Ranging,0.5% Warranty/Bailment)</td>
	</tr>
	<tr>
		<td   width=136  height=10  align='left' valign='middle'>Invoice Total - USD</td>
	</tr>	
</table>";

$tmp_Notes="<table  border=1 >	
   <tr>
	<td $colspan  $width   height=12  align='left' valign='top'>&nbsp; <br>$Commoditycode$StableNote$Notes</td>
  </tr> 
</table>";

$tmp_Terms="<table  border=1 >
	<tr>
    <td $colspan  $width  height=17  align='left' valign='top'>&nbsp;      <br>$PaymentTerm$Priceterm$Terms  </td>
  </tr>
</table>";
  
$tmp_Currency="<table  border=1 >
	<tr  >
  <td  $colspan  $width  height=8 align='left' valign=middle >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$Symbol</td>
  </tr>
</table>";

$tmp_BANK="<table  border=1 >
  <tr>
	 <td $colspan  $width  height=25  align='left' valign=middle >&nbsp;     <br>Beneficary: $Beneficary<br>Bank        : $Bank<br>Bank Add : $BankAdd<br>Swift ID    : $SwiftID &nbsp;&nbsp;&nbsp;A/C NO: $ACNO</td>
  </tr>
</table>";

?>
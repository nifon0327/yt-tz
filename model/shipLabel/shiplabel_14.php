<?php 
//MCA DC
   if($BoxCode!=""){
		$Field=explode("|",$BoxCode);$BoxCode0=$Field[0];$BoxCode1=$Field[1];
		if(is_numeric($BoxCode0)){	
			$BoxCode1=eregi_replace(",","<br>",$BoxCode1);
			$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td align='center' valign='bottom'><iframe frameborder=0 marginheight=0 marginwidth=0 scrolling=no width='120' height='40'  src='ean_13code.php?Code=$BoxCode0&lw=1&hi=25'></iframe></td></tr><tr><td height='32' valign='top' scope='col'><div align='center' class='codebig'>$BoxCode1</div></td></tr></table>";
			}
		else{
			//$BoxCode0=eregi_replace(",","<br>",$BoxCode0);
			if(is_numeric($BoxCode1)){
				$BoxCodeTable="<table width='100%'  cellspacing='0'>
				<tr><td align='center' valign='top'><iframe frameborder=0 marginheight=0 marginwidth=0 scrolling=no width='180' height='70' src='../model/ean_13code.php?Code=$BoxCode1&lw=1.8&hi=50'></iframe></td></tr>
				</table>";
			  }
			 else{
				$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td height='32' valign='bottom' scope='col'></td></tr><tr><td align='center' valign='top'></td></tr></table>"; 
			 }
			}
		}
	else{
		$BoxCodeTable="&nbsp;";}
    
	echo"
	<table  width='570' height='305' border='0' cellSpacing=0 cellPadding=0 style='WORD-WRAP: break-word;font-size:14px;'>
	       <tr>
           		<td height='55' colspan='2' align='center' class='A1111' style='font-size:40px; font-weight:bold'> $eCode</td>
             </tr>
              <tr>
           		<td width='395' height='40' class='A0010'>&nbsp;&nbsp;PO:$OrderPO</td>
                <td width='169' class='A0111' style='font-size:20px; font-weight:bold'>&nbsp;Qty: $BoxPcs PCS</td>
             </tr>
           <tr>
	         <td height='20' class='A0010'>&nbsp;&nbsp;Invoice:$InvoiceNO</td>
	         <td class='A0111'>&nbsp;&nbsp;Dim: $BoxSpec</td>
	         </tr>
	       <tr>
           		<td height='20' class='A0010'>&nbsp;</td>
                <td class='A0111'>&nbsp;&nbsp;GW : $WG Kg</td>
             </tr>
	       <tr>
           		<td height='20' class='A0010'>&nbsp;</td>
                <td class='A0111'>&nbsp;&nbsp;NW : $NG Kg</td>
             </tr>
	       <tr>
	         <td class='A0110'>&nbsp;</td>
	         <td class='A0111'><div calss='div_boxTable' style='margin-top:10px;'>$BoxCodeTable</div></td>
	         </tr>
         </table>
		";

?>
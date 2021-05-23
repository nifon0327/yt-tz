<?php 
//FORCE 专用
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
				<tr><td align='center' valign='top'><iframe frameborder=0 marginheight=0 marginwidth=0 scrolling=no width='460' height='70' src='../model/ean_13code.php?Code=$BoxCode1&lw=1.8&hi=50'></iframe></td></tr>
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
<table style='WORD-WRAP: break-word;font-size:24px;font-weight:bold' width='590' bordercolor='#000000' height='300' border='1' cellSpacing=0 cellPadding=0>
      <tr>  
       <td>
       
	       <table style='WORD-WRAP: break-word;font-size:24px;font-weight:bold'   width='100%' height='45' border='0' cellSpacing=0 cellPadding=0>
	       <tr>
                <td width='5%' >&nbsp;</td>
           		<td width='30%' >ITEM NO:</td>
                <td> $eCode </td>
           </tr>
	     </table>  
         
	       <table style='WORD-WRAP: break-word;font-size:24px;font-weight:bold'   width='100%' height='135' border='0' cellSpacing=0 cellPadding=0>
	       <tr>
           		<td width='5%' >&nbsp;</td>
                <td width='50%'>QUANTITY:</td>
                <td width='30%'>$BoxPcs</td>
                <td  >PCS</td>
           </tr>
	       <tr>
           		<td width='5%' >&nbsp;</td>
                <td width='50%'>NET WEIGHT:</td>
                <td width='30%'> $NG </td>
                <td  >KGS</td>
           </tr>
	       <tr>
           		<td width='5%' >&nbsp;</td>
                <td width='50%'>GROSS WEIGHT:</td>
                <td width='30%'> $WG </td>
                <td  >KGS</td>
           </tr>                      
           
	     </table>  


	       <table style='WORD-WRAP: break-word;font-size:24px;font-weight:bold' width='100%' height='45' border='0' cellSpacing=0 cellPadding=0>
	       <tr>
            	<td width='5%' >&nbsp;</td>
                <td width='15%' >SIZE:</td>
                <td align='center' > $BoxSpec </td>
           </tr>
	     </table>  


	       <table style='WORD-WRAP: break-word;font-size:24px;font-weight:bold'   width='100%' height='45' border='0' cellSpacing=0 cellPadding=0>
	       <tr>
                <td width='5%' >&nbsp;</td>
           		<td width='30%' >CARTON NO:</td>
                <td align='center' > $PreWord$i </td>
           </tr>
	     </table>  		 
         
              

	 </td>
	</tr>
 </table>
		 ";

?>
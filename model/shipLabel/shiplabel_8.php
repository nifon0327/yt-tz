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
    
	echo"<table style='WORD-WRAP: break-word' width='590' bordercolor='#000000' height='300' border='1' cellSpacing=0 cellPadding=0>
      <tr>  
       	  <td width='13' class='aaa'>
	       <table style='WORD-WRAP: break-word' width='13' height='300' border='0' cellSpacing=0 cellPadding=0 frame='void'>
	       <tr><td>&nbsp;</td></tr>
		   <tr><td height='250'></td></tr>
		   <tr><td >&nbsp;</td></tr>
	     </table>
	     </td>
        <td width='560' align='center' class='aaa'>
	       <table style='WORD-WRAP: break-word' width='560' height='270' border='1'  bordercolor='#000000' cellSpacing=0 cellPadding=0>
		       <td width='70' class='aaa' cospan='5'>&nbsp;
			   </td>
			   <td width='490'>
			       <tr>
                    <td  width='19%' class='aaa' height='40' >&nbsp;</td>
		            <td  width='81%' class='aaa' style='font-size:22px'>Part:&nbsp;$eCode</td>
                  </tr>
				   <tr>
                    <td  width='19%' class='aaa' height='40'>&nbsp;</td>
		            <td  width='81%' class='aaa' style='font-size:22px' >Qty:&nbsp;$BoxPcs</td>
                  </tr>
				  <tr>
				    <td  width='19%' class='aaa' height='90'  >&nbsp;</td>
		            <td  width='81%' class='aaa' align='left' ><div calss='div_boxTable'>$BoxCodeTable</div></td>
                  </tr>
				   </tr>
				   <tr>
                    <td  width='19%' class='aaa' height='40'>&nbsp;</td>
		            <td  width='81%' class='aaa'  style='font-size:22px'>PO:&nbsp;$OrderPO</td>
                  </tr>
				   </tr>
				   <tr>
                    <td  width='19%' class='aaa' height='40' ></td>
		            <td  width='81%' class='aaa' style='font-size:22px' >Ctn&nbsp;&nbsp;<u>&nbsp;$PreWord$i&nbsp;</u>&nbsp;&nbsp;of&nbsp;&nbsp;<u>&nbsp;$PreWord$BoxTotal&nbsp;</u></td>
                  </tr>
			   
			   </td>
			   
           </table>
	 </td>
	</tr>
 </table>";

?>
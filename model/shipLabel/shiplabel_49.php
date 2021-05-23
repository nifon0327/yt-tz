<?php 
   
    $ClientFileSTR ="";
    $ClientFilePath ="../download/productClient/" .$ProductId.".jpg";    
    if(file_exists($ClientFilePath)){
	     $ClientFileSTR = "<img  src='$ClientFilePath' width='140' height='120'>";  
    }
	echo"<table  width='590' bordercolor='#000000' height='290' border='1' cellSpacing=0 cellPadding=0>
      <tr>  
       <td>

	     <table width='100%' height='70' border='0' cellSpacing=0 cellPadding=0>
	        <tr><td width='10'>&nbsp;</td>
	        <td><span style='WORD-WRAP: break-word;font-size:42px;font-weight:bold'>$eCode</span></td></tr>
	        <tr><td width='10'>&nbsp;</td>
	        <td ><span style='WORD-WRAP: break-word;font-size:17px;font-weight:bold' >$Description</span></td></tr>
	     </table>  
         
         
           <table width='100%' height='70' border='0' cellSpacing=0 cellPadding=0>
	        <tr >
                <td width='10'>&nbsp;</td>
           		<td width='140' height='15' ><span style='font-size:15px;'>QTY:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style='font-size:18px;font-weight:bold'>数量</span></td>
           		<td width='75'>&nbsp;</td>
                <td width='140' ><span style='font-size:15px;'>WEIGHT:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style='font-size:18px;font-weight:bold'>重量</span></td>
                <td width='75'>&nbsp;</td>
                <td width='140' ><span style='font-size:15px;'>CARTON NO.:</span><span style='font-size:18px;font-weight:bold'>箱号</span></td>
                <td width='10'>&nbsp;</td>
           </tr>
           <tr>
                <td width='10' height='45'>&nbsp;</td>
                <td  class='A1011' align='center' style='font-size:36px;font-weight:bold'>$BoxPcs</td>
                <td  >&nbsp;</td>
                <td  class='A1011' align='center' style='font-size:36px;font-weight:bold'>$WG</td>
                <td  >&nbsp;</td>
                <td  class='A1011' align='center' style='font-size:36px;font-weight:bold'>$PreWord$i</td>
                <td  >&nbsp;</td>
           </tr>
           
            <tr>
                <td width='10'>&nbsp;</td>
                <td  height='8' align='right' class='A0111'>pcs&nbsp;</td>
                <td  >&nbsp;</td>
                <td  align='right' class='A0111'>kg&nbsp;</td>
                <td  >&nbsp;</td>
                <td  class='A0111'>&nbsp;</td>
                <td  >&nbsp;</td>
           </tr>
	     </table> 
         
 	       <table width='100%' height='140' border='0' cellSpacing=0 cellPadding=0 style='margin-top:8px;'>
	       <tr>
                <td width='10'>&nbsp;</td>
           		<td width='215' ><span style='font-size:16px;'>ORDER NO.:</span><BR>
           		<span style='font-size:20px;font-weight:bold'>$OrderPO</span><BR><BR>
           		<img  src='../model/ean_13code.php?Code=$BoxCode1&lw=2&hi=50'><BR><div style='font-weight:bold;margin-top:8px;'>OUTER CARTON</div></td>
                <td width='215' align='center'>$ClientFileSTR</td>
                <td width='140' align='center' ><div style='margin-top:20px;'><img  src='../images/ISY.jpg'></div><br><div style='font-weight:bold;margin-top:-20px;'>MADE IN CHINA</div></td>
           </tr>
	     </table>   
	 </td>
	</tr>
 </table>";

?>
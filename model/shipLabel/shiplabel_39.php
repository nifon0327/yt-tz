<?php 
/*$Field=explode("|",$BoxCode);$BoxCode0=$Field[0];$BoxCode1=$Field[1];
//echo "PackingRemark:".$PackingRemark;
if ($PackingRemark!=""){
    $SBOField=explode("|",$PackingRemark);
	$SupplierName=$SBOField[0];
	$BrandName=$SBOField[1];
	$OrderPO=$SBOField[2];
}
else{
	$SupplierName=@"YAL";$BrandName=@"YAL";
}

//bordercolor='#D2D2D2'
echo"<TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=292 cellSpacing=0 cellPadding=0 width=590 border=0 >
    <TBODY>
              <TR height=''>
                  <TD  width='220' valign='middle'><span style='font-size:22px;font-weight:bold''>ASCENDEO</span></TD>
                  <TD valign='middle'><span style='font-size:22px; font-weight:bold' >&nbsp;Carton NO:</span>  </TD>
                  <TD valign='middle'><span style='font-size:22px; font-weight:bold' >$i&nbsp;</span> </TD>
              </TR> 
               <TR height=''>
                  <TD  width='220' valign='middle'><span style='font-size:18px;'>Supperlier Name:</span></TD>
                  <TD valign='middle' colspan='2'><span style='font-size:18px; font-weight:bold' >&nbsp;$SupplierName </span>  </TD>
              </TR> 
               <TR height=''>
                  <TD  width='220' valign='middle'><span style='font-size:18px;'>Brand Name:</span></TD>
                  <TD valign='middle'  colspan='2'><span style='font-size:18px; font-weight:bold' >&nbsp;$BrandName </span>  </TD>
              </TR> 
              
               <TR height=''>
                  <TD  width='220' valign='middle' ><span style='font-size:18px;' >Order Number:</span> </TD>
                  <TD valign='middle'  colspan='2'><span style='font-size:18px; font-weight:bold' >&nbsp;$OrderPO </span> </TD>
              </TR>
                <TR height=''>
                  <TD  width='220' valign='middle' ><span style='font-size:18px;' >Reference No.:</span> </TD>
                  <TD valign='middle'  colspan='2'><span style='font-size:18px; font-weight:bold' >&nbsp;$OrderPO </span> </TD>
              </TR>
              <TR height=''>
                 <TD  width='220' valign='middle' ><span style='font-size:18px;'  >SKU No.:</span></TD>
                  <TD valign='middle'  colspan='2'><span style='font-size:18px; font-weight:bold' >&nbsp;$eCode </span>  </TD>
              </TR>   
              
              <TR height=''>
                 <TD  width='220' valign='middle' ><span style='font-size:18px;' >Product Description:</span></TD>
                 <TD valign='middle'  colspan='2'><span style='font-size:18px; font-weight:bold' >&nbsp;$Description</span></TD>
              </TR>   
              <TR height=''>
                 <TD  width='220' valign='middle' ><span style='font-size:18px;' >Export / Inner:</span></TD>
                 <TD valign='middle' ><span style='font-size:18px; font-weight:bold' >&nbsp;$BoxPcs PCS / $InBoxPcs PCS</span></TD>
                  <TD valign='middle' rowspan='2'> <div calss='div_boxTable'>$BoxCodeTable</div></TD>
              </TR> 
         
               <TR height=''>
                  <TD  width='220' valign='middle' ><span style='font-size:18px;' >Date</span></TD>
                  <TD  valign='middle'  ><span style='font-size:18px; font-weight:bold' >&nbsp;$Udate</span></TD>
              </TR>  			  
             </TBODY>	

</TABLE>";*/

	if($FromCounty!=""){
		$sField=explode("|",$FromCounty);
		if(count($sField)>0){
			$ABrand=$sField[0];
		}
		
		if(count($sField)>1){
			$BrandName=$sField[1];
		}

		if(count($sField)>2){
			$SupplierName=$sField[2];
		}
		
		if(count($sField)>3){
			$DeliveryNo=$sField[3];
		}
	}
	$tmpDate = date("d/m/Y");
	echo"
<table style='WORD-WRAP: break-word;font-size:24px;font-weight:bold' width='590' bordercolor='#000000' height='300' border='1' cellSpacing=0 cellPadding=0>
      <tr>  
       <td>

	       <table style='WORD-WRAP: break-word;font-size:20px;font-weight:bold'   width='100%' height='40' border='0' cellSpacing=0 cellPadding=0>
	       <tr>
                
           		<td width='60%' ><span style='WORD-WRAP: break-word;font-size:24px;font-weight:bold'>$ABrand</span></td>
                <td> Carton No: &nbsp;  $PreWord$i</td>
           </tr>
	     </table>  
         
          <table style='WORD-WRAP: break-word;font-size:20px;font-weight:bold'   width='100%' height='30' border='0' cellSpacing=0 cellPadding=0>
	       <tr>
                
           		<td width='40%' >Supplier Name:</td>
                <td> $SupplierName </td>
           </tr>
	     </table>  
       
	       <table style='WORD-WRAP: break-word;font-size:20px;font-weight:bold'   width='100%' height='30' border='0' cellSpacing=0 cellPadding=0>
	       <tr>
                
           		<td width='40%' >Brand Name:</td>
                <td> $BrandName </td>
           </tr>
	     </table>  

	       <table style='WORD-WRAP: break-word;font-size:20px;font-weight:bold'   width='100%' height='30' border='0' cellSpacing=0 cellPadding=0>
	       <tr>
                
           		<td width='40%' >Order Number:</td>
                <td> $OrderPO </td>
           </tr>
	     </table> 
         
	       <table style='WORD-WRAP: break-word;font-size:20px;font-weight:bold'   width='100%' height='30' border='0' cellSpacing=0 cellPadding=0>
	       <tr>
               
           		<td width='40%' >Reference No.:</td>
                <td> $DeliveryNo </td>
           </tr>
	     </table> 
 
 	       <table style='WORD-WRAP: break-word;font-size:20px;font-weight:bold'   width='100%' height='30' border='0' cellSpacing=0 cellPadding=0>
	       <tr>
                
           		<td width='40%' >SKU No.:</td>
                <td> $eCode </td>
           </tr>
	     </table> 
 
  	       <table style='WORD-WRAP: break-word;font-size:20px;font-weight:bold'   width='100%' height='30' border='0' cellSpacing=0 cellPadding=0>
	       <tr>
               
           		<td width='40%' >Product Description:</td>
                <td><span style='WORD-WRAP: break-word;font-size:12px;font-weight:bold'> $Description </span> </td>
           </tr>
	     </table> 
 
  
  	       <table style='WORD-WRAP: break-word;font-size:20px;font-weight:bold'   width='100%' height='30' border='0' cellSpacing=0 cellPadding=0>
	       <tr>
                
           		<td width='40%' >Export/Inner:</td>
                <td> $BoxPcs$PackingUnit/5PCS </td>
           </tr>
	     </table>
          
         <table style='WORD-WRAP: break-word;font-size:20px;font-weight:bold'   width='100%' height='100' border='0' cellSpacing=0 cellPadding=0>
          <tr>
            
            <td width='40%'>&nbsp;</td>
            <td width='20%'>&nbsp;</td>
            <td rowspan='2'><img  src='../model/ean_13code.php?Code=$BoxCode1&lw=2&hi=50'  style='margin-left:10px;'></td>
          </tr>
          <tr>
           
            <td >Date:</td>
            <td>$tmpDate</td>
          </tr>
        </table>

         
              

	 </td>
	</tr>
 </table>";

?>
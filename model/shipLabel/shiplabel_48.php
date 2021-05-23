<?php 
	
	if($FromCounty!=""){
		$sField=explode("|",$FromCounty);
		if(count($sField)>0){
			$ABrand=$sField[0];
		}
		
		if(count($sField)>1){
			$BrandName=$sField[1];
		}

		if(count($sField)>2){
			$DeliveryNo=$sField[2];
		}
		
	}
	$tmpDate = date("d/m/Y");
	echo"
<table style='WORD-WRAP: break-word;font-size:24px;font-weight:bold' width='590' bordercolor='#000000' height='300' border='1' cellSpacing=0 cellPadding=0>
      <tr>  
       <td>

	       <table style='WORD-WRAP: break-word;font-size:20px;font-weight:bold'   width='100%' height='45' border='0' cellSpacing=0 cellPadding=0>
	       <tr>
                
           		<td width='60%' ><span style='WORD-WRAP: break-word;font-size:24px;font-weight:bold'>$ABrand</span></td>
                <td> Carton No: &nbsp;  $PreWord$i</td>
           </tr>
	     </table>  
         
       
	       <table style='WORD-WRAP: break-word;font-size:20px;font-weight:bold'   width='100%' height='35' border='0' cellSpacing=0 cellPadding=0>
	       <tr>
                
           		<td width='40%' >Brand Name:</td>
                <td> $BrandName </td>
           </tr>
	     </table>  

	       <table style='WORD-WRAP: break-word;font-size:20px;font-weight:bold'   width='100%' height='35' border='0' cellSpacing=0 cellPadding=0>
	       <tr>
                
           		<td width='40%' >Order Number:</td>
                <td> $OrderPO </td>
           </tr>
	     </table> 
         
 
 
 	       <table style='WORD-WRAP: break-word;font-size:20px;font-weight:bold'   width='100%' height='35' border='0' cellSpacing=0 cellPadding=0>
	       <tr>
                
           		<td width='40%' >SKU No.:</td>
                <td> $eCode </td>
           </tr>
	     </table> 
 
  	       <table style='WORD-WRAP: break-word;font-size:20px;font-weight:bold'   width='100%' height='35' border='0' cellSpacing=0 cellPadding=0>
	       <tr>
               
           		<td width='40%' >Product Description:</td>
                <td><span style='WORD-WRAP: break-word;font-size:12px;font-weight:bold'> $Description </span> </td>
           </tr>
	     </table> 
 
  
  	       <table style='WORD-WRAP: break-word;font-size:20px;font-weight:bold'   width='100%' height='35' border='0' cellSpacing=0 cellPadding=0>
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
 </table>
		 ";

?>
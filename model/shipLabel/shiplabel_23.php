<?php 
//$eCode="CK9061-HLIP5-RFC";
//$OrderPO="02090-0460";
$StartPlace="CELLULAR ITALIA SPA c/o TRANSMECLOG SRL";
echo"<TABLE  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;' height='292' cellSpacing=0 cellPadding=0 width=580 border=0>
      <tr>
      <td  width='10'>&nbsp;</td> 
     <td  width='380' height='10'>&nbsp;</td> 
     <td  width='85'>&nbsp;</td>
      <td width='105'>&nbsp;</td> 
    </tr>  
      <tr>
      <td  width='10'>&nbsp;</td> 
     <td   height='35'><div  class='label23_font1'>$StartPlace</div></td> 
     <td colspan='2'><div class='label23_font5'> Box $i of  $BoxTotal </div></td>
    </tr>  
    
      <tr>
      <td  width='10'>&nbsp;</td> 
     <td     colspan='3' height='40'><span class='label23_font3'>$eCode</span></td> 
    </tr> 

     <tr>
     <td  width='10'>&nbsp;</td> 
     <td  colspan='2' height='30'><span class='label23_font1'>G.WT </span><span class='label23_font2'>&nbsp;" . $WG ."&nbsp;kg&nbsp;</span></td> 
      <td  rowspan='4'><img  src='../plugins/barcodegen/ITF_14Code.php?Code=$BoxCode1&lw=2&hi=40'  style='margin-left:10px; filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3);-webkit-transform:rotate(-90deg); -moz-transform:rotate(-90deg); -o-transform:rotate(-90deg);width:180px;height:70px;'></td> 
    </tr>  

     <tr>
     <td  width='10'>&nbsp;</td> 
     <td   colspan='2' height='35'><span class='label23_font1'>PO</span><span class='label23_font2'>&nbsp;$OrderPO &nbsp;</span><span class='label23_font1'> invoice </span>&nbsp;<span class='label23_font2'>$InvoiceNO</span></td> 
    </tr> 

      <tr>
      <td  width='10'>&nbsp;</td> 
     <td   height='65'><img  src='../model/ean_13code.php?Code=$BoxCode1&lw=2&hi=50'  style='margin-left:10px;'></td> 
      <td rowspan='2'><div class='label23_font5'>Qty </div><br><div class='label23_font4'>$BoxPcs</div></td> 
    </tr>  
    
     <tr>
     <td  width='10'>&nbsp;</td> 
     <td valign='bottom'><span class='label23_font1'>&nbsp;MADE IN P.R.C</span></td> 
    </tr> 
</TABLE>";
?>
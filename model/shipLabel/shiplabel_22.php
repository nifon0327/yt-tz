<?php 
//$eCode="CK9061-HLIP5-RFC";
//$OrderPO="02090-0460";
echo"<TABLE  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;' height='292' cellSpacing=0 cellPadding=0 width=580 border=0>
      <tr>
     <td    width='40' height='40'>&nbsp;</td> 
     <td   >&nbsp;</td>
      <td  width='40'>&nbsp;</td> 
    </tr>  
      <tr>
     <td    width='40'>&nbsp;</td> 
     <td  ><span class='label18_font2'>&nbsp;$eCode</span></td>
      <td  width='40'>&nbsp;</td> 
    </tr> 
	

    <tr>
     <td    width='40'>&nbsp;</td> 
     <td    ><span class='label18_font1'>Q.TY PCS:</span>&nbsp;<span class='label18_font2'> &nbsp; $BoxPcs &nbsp;</span></td>

      <td  width='40'>&nbsp;</td> 
    </tr>  

     <tr>
     <td    width='40'>&nbsp;</td> 
     <td    ><span class='label18_font1'>Gross Wt:</span><span class='label18_font2'>&nbsp;" . $WG ."KG&nbsp;</span> </td>

      <td  width='40'>&nbsp;</td> 
    </tr>  

	
     <tr>
     <td    width='40'>&nbsp;</td> 
     <td  ><span class='label18_font1'>ORDER NR:</span><span class='label18_font2'>&nbsp;$OrderPO</span></td>
      <td  width='40'>&nbsp;</td> 
    </tr> 
 
  
	
     <tr>
     <td    width='40'>&nbsp;</td> 
     <td    ><span class='label18_font1'>Crt Nr:</span><span class='label18_font2'>&nbsp;$i &nbsp; OF &nbsp; $BoxTotal</span></td>
      <td  width='40'>&nbsp;</td> 
    </tr>  
  
     <tr>
     <td    width='40'>&nbsp;</td> 
     <td    ><span class='label18_font1'>MADE IN P.R.C</span></td>
      <td  width='40'>&nbsp;</td> 
    </tr> 
	
      <tr>
     <td    width='40' height='40'>&nbsp;</td> 
     <td   >&nbsp;</td>
      <td  width='40'>&nbsp;</td> 
    </tr>  
</TABLE>";
?>
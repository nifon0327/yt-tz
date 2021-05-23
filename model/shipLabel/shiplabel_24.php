<?php 
//$eCode="CK9061-HLIP5-RFC";
//$OrderPO="02090-0460";
$Field=explode("/",$Description);
$Count=count($Field);
$Colour="&nbsp;";
if($Count>=2){
 $Colour=$Field[$Count=1];
}
echo"<TABLE  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;' height='292' cellSpacing=0 cellPadding=0 width=580 border=0>
      <tr>
     <td    width='40' height='40'>&nbsp;</td> 
     <td   >&nbsp;</td>
      <td  width='40'>&nbsp;</td> 
    </tr>  
	
      <tr>
     <td    width='40'>&nbsp;</td> 
     <td  ><span class='label18_font1'>ATLANTIS INTERNACIONAL SL </span></td>
      <td  width='40'>&nbsp;</td> 
    </tr> 
	
      <tr>
     <td    width='40'>&nbsp;</td> 
     <td  ><span class='label18_font1'>Item#</span><span class='label18_font2'>&nbsp;$eCode</span></td>
      <td  width='40'>&nbsp;</td> 
    </tr>  
     <tr>
     <td    width='40'>&nbsp;</td> 
     <td  ><span class='label18_font1'>Colour:</span><span class='label18_font2'>&nbsp;$Colour</span></td>
      <td  width='40'>&nbsp;</td> 
    </tr> 

     <tr>
     <td    width='40'>&nbsp;</td> 
     <td    ><span class='label18_font1'>Qty:</span><span class='label18_font2'>&nbsp;" . $BoxPcs ." PCS &nbsp; </td>
      <td  width='40'>&nbsp;</td> 
    </tr>
  
     <tr>
     <td    width='40'>&nbsp;</td> 
     <td    ><span class='label18_font1'>Weight:</span><span class='label18_font2'>&nbsp;" . $WG ." KG &nbsp; </td>
      <td  width='40'>&nbsp;</td> 
    </tr>  

      <tr>
     <td width='40'>&nbsp;</td> 
      <td><span class='label18_font1'> Made in China </span>&nbsp;&nbsp;&nbsp;&nbsp;<span class='label18_font1'>CTN #:</span><span class='label18_font2'>&nbsp;$i &nbsp; OF &nbsp; $BoxTotal</span> </td> 
    </tr> 
 
      <tr>
     <td    width='40' height='40'>&nbsp;</td> 
     <td   >&nbsp;</td>
      <td  width='40'>&nbsp;</td> 
    </tr>  
	
</TABLE>";
?>
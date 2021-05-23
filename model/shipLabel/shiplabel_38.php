<?php 
$Field=explode("|",$BoxCode);$BoxCode0=$Field[0];$BoxCode1=$Field[1];
//bordercolor='#D2D2D2'
echo"<TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=292 cellSpacing=0 cellPadding=0 width=590 border=0 >
	<TR height='56'>
    <td align='center'> <img src='../model/shipLabel/gooey.png'  height='56' /></td>
    </TR>
    <TR height='236'>
    <td>
        <TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height='230' cellSpacing=0 cellPadding=0 width=585 border=1 bordercolor='#000000' >
            <TBODY>
               <TR height=''>
                  <TD  width='190' valign='middle'><span style='font-size:22px;'>BARCODE</span></TD>
                  <TD valign='middle'><span style='font-size:22px; font-weight:bold' >&nbsp;$BoxCode1 </span>  </TD>
              </TR> 
              
               <TR height=''>
                  <TD  width='190' valign='middle' ><span style='font-size:22px;' >ORDER NUMBER</span> </TD>
                  <TD valign='middle'><span style='font-size:22px; font-weight:bold' >&nbsp;$OrderPO </span> </TD>
              </TR>
              <TR height=''>
                 <TD  width='190' valign='middle' ><span style='font-size:22px;'  >STOCK NUMBER</span></TD>
                  <TD valign='middle'><span style='font-size:22px; font-weight:bold' >&nbsp;$eCode </span>  </TD>
              </TR>   
              
              <TR height=''>
                 <TD  width='190' valign='middle' ><span style='font-size:22px;' >DESCRIPTION</span></TD>
                 <TD valign='middle' ><span style='font-size:22px; font-weight:bold' >&nbsp;$Description</span></TD>
              </TR>   
         
               <TR height=''  >
                  <TD  width='190' valign='middle' ><span style='font-size:22px;' >TOTAL UNITS</span></TD>
                  <TD  valign='middle'  ><span style='font-size:22px; font-weight:bold' >&nbsp;$BoxPcs &nbsp;&nbsp;(&nbsp;$i OF $BoxTotal&nbsp;)</span></TD>
              </TR>  			  
             </TBODY>	  
        </TABLE>
    </td>
    </TR>

</TABLE>";
//echo "</div>";
?>
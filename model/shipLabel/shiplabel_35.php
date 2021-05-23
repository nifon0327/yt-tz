<?php 
//echo "BoxCode:".$BoxCode;



echo"<TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=292 cellSpacing=0 cellPadding=0 width=590 border=0 >
	<TBODY>
       <TR height='28' valign='middle' >
          <TD colspan='3'><span style='font-size:22px; font-weight:bold' >&nbsp;&nbsp;&nbsp;&nbsp;Item No.:</span> <span style='font-size:22px; font-weight:bold' >&nbsp;$eCode </span> </TD>
      </TR>		

       <TR height='45' valign='middle'>
	      
          <TD colspan='2'><div align='center'>$BoxCodeTable</div></TD>
		  <TD  width='62'><span style='font-size:22px; font-weight:bold' >&nbsp; </span> </TD>
      </TR>	

 
	  
       <TR height='28' valign='middle'>
          <TD  width='318'><span style='font-size:22px; font-weight:bold' >&nbsp;&nbsp;&nbsp;&nbsp;QTY:</span> <span style='font-size:22px; font-weight:bold'>$BoxPcs </span></TD>
		  <TD  width='202'><span style='font-size:22px; font-weight:bold' >&nbsp;</span> <span style='font-size:22px; font-weight:bold'>PCS</span></TD>
		  <TD  width='62'><span style='font-size:22px; font-weight:bold' >&nbsp; </span> </TD>
      </TR>
      <TR height='28' valign='middle'>
         <TD  width='318'><span style='font-size:22px; font-weight:bold'>&nbsp;&nbsp;&nbsp;&nbsp;NW: $NG </span></TD>
		  <TD  width='202'><span style='font-size:22px; font-weight:bold' >&nbsp;</span> <span style='font-size:22px; font-weight:bold'>KGS </span></TD>
		  <TD  width='62'>&nbsp;</TD>
		 
      </TR>   
	  
      <TR height='28' valign='middle'>
         <TD  width='318'><span style='font-size:22px; font-weight:bold'>&nbsp;&nbsp;&nbsp;&nbsp;GW:&nbsp;$WG </span></TD>
		  <TD  width='202'><span style='font-size:22px; font-weight:bold'>&nbsp;KGS</span></TD>
		  <TD  width='62'>&nbsp;</TD>		 
      </TR>   
 
       <TR height='28' valign='middle'>
          <TD colspan='2' valign='middle' ><span style='font-size:22px; font-weight:bold'>&nbsp;&nbsp;&nbsp;&nbsp;C/No.:  $PreWord$i &nbsp;OF&nbsp; $PreWord$BoxTotal </span><span style='font-size:22px; font-weight:bold' >&nbsp;</span> </TD>
		  <TD  width='62'><span style='font-size:22px; font-weight:bold' >&nbsp; </span> </TD>
		  
      </TR>  

	 </TBODY>	  
</TABLE>";
//echo "</div>";
?>
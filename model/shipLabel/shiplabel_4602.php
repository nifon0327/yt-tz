<?php 
//DCAsia bysize Label
//echo "<TABLE  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;' height='292' cellSpacing=0 cellPadding=0 width=580 border=0>";
/*
echo "<div style='page-break-after:always;'>";
echo"<TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=292 cellSpacing=0 cellPadding=0 width=590 border=0>
	<TBODY>
       <TR height='25'>
          <TD  width='550'><span style='font-size:24px; font-weight:bold' >&nbsp;NET WEIGHT: $NG  &nbsp; KG </span> </TD>
      </TR>
      <TR height='25'>
         <TD  width='550'><span style='font-size:24px; font-weight:bold'>&nbsp;GROSS WEIGHT:$WG &nbsp; KG </span></TD>
      </TR>   
 
      <TR height='25'>
         <TD  width='550'><span style='font-size:24px; font-weight:bold'>&nbsp;DIMENSIONS:$BoxSpec</span></TD>
      </TR>   
       <TR height='25'>
          <TD  width='550'><span style='font-size:24px; font-weight:bold'>&nbsp;CTN NO.: $PreWord$i &nbsp;OF  &nbsp; $PreWord$BoxTotal </span></TD>
      </TR>  
	 </TBODY>	  
</TABLE>";
echo "</div>";
*/
echo "<div style='page-break-after:always;'>";
echo"<TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=292 cellSpacing=0 cellPadding=0 width=590 border=0>
	<TBODY>
	
       <TR height='12'>
          <TD  width='550'><span style='font-size:24px; font-weight:bold'>&nbsp;  </span> </TD>
      </TR> 
	  
	   <TR height='25'>
         <TD  width='550'><span style='font-size:24px; font-weight:bold'>&nbsp;Dimension:$BoxSpec</span></TD>
      </TR> 
	  
       <TR height='25'>
          <TD  width='550'><span style='font-size:24px; font-weight:bold' >&nbsp;N.W: $NG  &nbsp; KG </span> </TD>
      </TR>
      <TR height='25'>
         <TD  width='550'><span style='font-size:24px; font-weight:bold'>&nbsp;G..: $WG &nbsp; KG </span></TD>
      </TR>   
 
  
       <TR height='25'>
          <TD  width='550'><span style='font-size:24px; font-weight:bold'>&nbsp;Carton NO.: $PreWord$i &nbsp;OF  &nbsp; $PreWord$BoxTotal </span></TD>
      </TR>  
	  
       <TR height='13'>
          <TD  width='550'><span style='font-size:24px; font-weight:bold'>&nbsp; </span> </TD>
      </TR>  
	  
	 </TBODY>	  
</TABLE>";
echo "</div>";
?>
<?php 
//QT(USD)出PURO

echo "<div style='page-break-after:always;'>";
echo"
<TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=292 cellSpacing=0 cellPadding=0 width=590 border=0>
	<TBODY>
	
       <TR height='20'>
          <TD  colspan='2'><span style='font-size:20px; font-weight:bold' >&nbsp;ITEM NO.: $eCode  </span> </TD>
      </TR>	
       <TR height='20'>
          <TD  width='280'><span style='font-size:20px; font-weight:bold'>&nbsp;QUANTITY: $BoxPcs </span></TD>
          <TD  width=''><span style='font-size:20px; font-weight:bold'>&nbsp;GROSS WEIGHT:$WG &nbsp; KG </span></TD>
      </TR>
      
        <TR height='20'>
         <TD  ><span style='font-size:20px; font-weight:bold' >&nbsp;CTN NO.: $PreWord$i &nbsp;OF &nbsp; $PreWord$BoxTotal </span></TD>
         <TD  width=''><span style='font-size:20px; font-weight:bold'>&nbsp;NET WEIGHT:$NG &nbsp; KG </span> </TD>
      </TR>      
       <TR height='20'>
          <TD  colspan='2'><span style='font-size:20px; font-weight:bold'>&nbsp;DIMENSIONS:$BoxSpec </span> </TD>
      </TR>	
      
       <TR height=''>
         <TD colspan='2' align='center'>$BoxCodeTable  </TD>
      </TR>  
           
      <TR height='24'>
         <TD colspan='2' align='center' ><span style='font-size:24px; font-weight:bold' >$eCode-48 </span></TD>
      </TR> 
       <TR height=''>
         <TD colspan='2' align='center' ><span style='font-size:24px; font-weight:bold' >&nbsp; </span></TD>
      </TR>	  
	 </TBODY>	  
</TABLE
";
echo "</div>";

/*
echo "<div style='page-break-after:always;'>";
echo"<TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=292 cellSpacing=0 cellPadding=0 width=590 border=0>
	<TBODY>
       <TR height='24'>
          <TD  width='550'><span style='font-size:24px; font-weight:bold' >&nbsp;ITEM NO.: $OrderPO  </span> </TD>
      </TR>	
       <TR height='24'>
          <TD  width='550'><span style='font-size:24px; font-weight:bold' >&nbsp;QUANTITY: $BoxPcs </span> </TD>
      </TR>
      
        <TR height='24'>
          <TD  width='550'><span style='font-size:24px; font-weight:bold'>&nbsp;CTN NO.: $PreWord$i &nbsp;OF &nbsp; $PreWord$BoxTotal </span></TD>
      </TR>      
      
       <TR height='24'>
         <TD  width='550'><span style='font-size:24px; font-weight:bold'>&nbsp;NET WEIGHT:$NG &nbsp; KG </span></TD>
      </TR>  
           
      <TR height='24'>
         <TD  width='550'><span style='font-size:24px; font-weight:bold'>&nbsp;GROSS WEIGHT:$WG &nbsp; KG </span></TD>
      </TR>   
 
      <TR height='24'>
         <TD  width='550'><span style='font-size:24px; font-weight:bold'>&nbsp;DIMENSIONS:$BoxSpec &nbsp; KG </span></TD>
      </TR>   
	 </TBODY>	  
</TABLE>";
echo "</div>";
*/

?>
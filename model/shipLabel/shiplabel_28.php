<?php 
//CG出CEL
$Field=explode("(",$OrderPO);  //把括号中的提取出来
$Count=count($Field);
if($Count==2){
	$OrderPO=$Field[1];	
	$Field=explode(")",$OrderPO);
	$OrderPO=$Field[0];
}
echo "<div style='page-break-after:always;'>";
echo"<TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=292 cellSpacing=0 cellPadding=0 width=590 border=0>
	<TBODY>
       <TR height='24'>
          <TD  width='550'><span style='font-size:24px; font-weight:bold' >&nbsp;ITEM CODE : $Description </span> </TD>
      </TR>	
       <TR height='24'>
          <TD  width='550'><span style='font-size:24px; font-weight:bold' >&nbsp;Q.TY : $BoxPcs </span> </TD>
      </TR>
      <TR height='24'>
         <TD  width='550'><span style='font-size:24px; font-weight:bold'>&nbsp;GROSS WEIGHT :$WG &nbsp; KG </span></TD>
      </TR>   
 
      <TR height='24'>
         <TD  width='550'><span style='font-size:24px; font-weight:bold'>&nbsp;ORDER NR :$OrderPO </span></TD>
      </TR>   
       <TR height='24'>
          <TD  width='550'><span style='font-size:24px; font-weight:bold'>&nbsp;Crt Nr.: $PreWord$i &nbsp;/  &nbsp; $PreWord$BoxTotal </span></TD>
      </TR>  
       <TR height='24'>
          <TD  width='550'><span style='font-size:24px; font-weight:bold'>MADE IN CHINA </span></TD>
      </TR> 	  
	 </TBODY>	  
</TABLE>";
echo "</div>";
?>
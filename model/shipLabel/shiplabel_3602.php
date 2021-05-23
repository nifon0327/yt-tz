<?php 
//echo "BoxCode:".$BoxCode;
$CurDate=date('d-M-Y');

$Fontheight=" style='font-size:22px; font-weight:bold;height:48px;line-height:48px;'";
echo"<TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=292 cellSpacing=0 cellPadding=0 width=590 border=0 >
	<TBODY>
       <TR height=''    >
          <TD  style=' height:21px;'><span $Fontheight >&nbsp;&nbsp;&nbsp;&nbsp;</span> </TD>
      </TR>		

       <TR height='' >
          <TD class='td_line' ><span $Fontheight >&nbsp;&nbsp;&nbsp;&nbsp;Date of Production: </span> </TD>
	  </TR>	

       <TR height='' >
          <TD class='td_line' ><span $Fontheight >&nbsp;&nbsp;&nbsp;&nbsp;$CurDate </span> </TD>
	  </TR>
      <TR height='' >
         <TD class='td_line' ><span $Fontheight>&nbsp;&nbsp;&nbsp;&nbsp;SIZE: $BoxSpec &nbsp;</span></TD>
	  </TR>   
	  
      <TR height=''>
         <TD class='td_line' ><span $Fontheight >&nbsp;&nbsp;&nbsp;&nbsp;NW:  $NG &nbsp; KGS </span></TD>
	  </TR>   
 
      <TR height=''>
         <TD class='td_line' ><span $Fontheight >&nbsp;&nbsp;&nbsp;&nbsp;GW:  $WG &nbsp; KGS </span></TD>
	  </TR>   
  
	 </TBODY>	  
</TABLE>";
//echo "</div>";
?>
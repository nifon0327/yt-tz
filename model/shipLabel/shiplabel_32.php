<?php 
//CG出CEL
//echo "FromCounty:$FromCounty";
$Field=explode("|",$FromCounty);  //把括号中的提取出来
$Count=count($Field);
if($Count==2){
	$ClientTitle=$Field[0];
	$ClientItemNO=$Field[1];	

}
$BoxSpec=explode("CM",$BoxSpec); 
$BoxSpec=$BoxSpec[0];
//echo "<div style='page-break-after:always;'>";
/*
echo"<TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=292 cellSpacing=0 cellPadding=0 width=590 border=1>
	<TBODY>
       <TR height='23'>
          <TD  width='260'><span style='font-size:22px; font-weight:bold' >PRODUCTION CODE:</span> </TD>
          <TD colspan='2'><span style='font-size:22px; font-weight:bold' >&nbsp;$eCode </span> </TD>
      </TR>		
       <TR height='40'>
          <TD  width='260'><span style='font-size:22px; font-weight:bold' >DESCRIPTION:</span> </TD>
          <TD colspan='2'><span style='font-size:15px; font-weight:bold' >&nbsp;$Description </span> </TD>
      </TR>	
       <TR height='23'>
          <TD  width='260'><span style='font-size:22px; font-weight:bold' >ITEM NO.:</span> </TD>
          <TD colspan='2'><span style='font-size:22px; font-weight:bold' >&nbsp;$ClientItemNO </span> </TD>
      </TR>	

       <TR height='23'>
          <TD  width='260'><span style='font-size:22px; font-weight:bold'>CARTON SIZE:</span></TD>
		  <TD  width='260'><span style='font-size:22px; font-weight:bold' >&nbsp;$BoxSpec </span> </TD>
		  <TD  width='62'><span style='font-size:22px; font-weight:bold' >&nbsp;CM </span> </TD>		  
      </TR> 
	  
       <TR height='23'>
          <TD  width='260'><span style='font-size:22px; font-weight:bold' >QUANTITY:</span> </TD>
		  <TD  width='260'><span style='font-size:22px; font-weight:bold' >&nbsp;$BoxPcs </span> </TD>
		  <TD  width='62'><span style='font-size:22px; font-weight:bold' >&nbsp;PCS </span> </TD>
      </TR>
      <TR height='23'>
         <TD  width='260'><span style='font-size:22px; font-weight:bold'>NET WEIGHT: </span></TD>
		  <TD  width='260'><span style='font-size:22px; font-weight:bold' >&nbsp;$NG </span> </TD>
		  <TD  width='62'><span style='font-size:22px; font-weight:bold' >&nbsp;KGS </span> </TD>
		 
      </TR>   
	  
      <TR height='23'>
         <TD  width='260'><span style='font-size:22px; font-weight:bold'>GROSS WEIGHT:</span></TD>
		  <TD  width='260'><span style='font-size:22px; font-weight:bold' >&nbsp;$WG </span> </TD>
		  <TD  width='62'><span style='font-size:22px; font-weight:bold' >&nbsp;KGS</span> </TD>		 
      </TR>   
 
       <TR height='23'>
          <TD  width='260'><span style='font-size:22px; font-weight:bold'>CARTON NO.:  </span></TD>
		  <TD  width='260'><span style='font-size:22px; font-weight:bold' >&nbsp;$PreWord$i &nbsp;/  &nbsp; $PreWord$BoxTotal </span> </TD>
		  <TD  width='62'><span style='font-size:22px; font-weight:bold' >&nbsp; </span> </TD>
		  
      </TR>  

	  
       <TR height='40'>
          <TD colspan='3' align='center' valign='middle'><span style='font-size:36px; font-weight:bold' >$ClientTitle </span> </TD>
      </TR>			  
	  
	 </TBODY>	  
</TABLE>";
*/
//echo $cName;
$tempcName =str_replace("&","",$cName);
$markstr = substr($tempcName,0,2);
if($markstr =="SB" || $markstr =="MW" || $markstr =="SK"  || $markstr =="VL"){
   $markstr=$markstr;
}
else{
    $markstr ="";
}
echo"<TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=292 cellSpacing=0 cellPadding=0 width=590 border=1 bordercolor='#720000'>
	<TBODY>
       <TR height='28'>
          <TD  width='260'><span style='font-size:22px; font-weight:bold' >PRODUCTION CODE:</span> </TD>
          <TD colspan='2'><span style='font-size:22px; font-weight:bold' >&nbsp;$eCode </span> </TD>
      </TR>		

       <TR height='28'>
          <TD  width='260'><span style='font-size:22px; font-weight:bold' >SHIPPING MARK.:</span> </TD>
          <TD colspan='2'><span style='font-size:22px; font-weight:bold' >&nbsp;$markstr </span> </TD>
      </TR>	

       <TR height='28'>
          <TD  width='260'><span style='font-size:22px; font-weight:bold'>CARTON SIZE:</span></TD>
		  <TD  width='260'><span style='font-size:22px; font-weight:bold' >&nbsp;$BoxSpec </span> </TD>
		  <TD  width='62'><span style='font-size:22px; font-weight:bold' >&nbsp;CM </span> </TD>		  
      </TR> 
	  
       <TR height='28'>
          <TD  width='260'><span style='font-size:22px; font-weight:bold' >QUANTITY:</span> </TD>
		  <TD  width='260'><span style='font-size:22px; font-weight:bold' >&nbsp;$BoxPcs </span> </TD>
		  <TD  width='62'><span style='font-size:22px; font-weight:bold' >&nbsp;PCS </span> </TD>
      </TR>
      <TR height='28'>
         <TD  width='260'><span style='font-size:22px; font-weight:bold'>NET WEIGHT: </span></TD>
		  <TD  width='260'><span style='font-size:22px; font-weight:bold' >&nbsp;$NG </span> </TD>
		  <TD  width='62'><span style='font-size:22px; font-weight:bold' >&nbsp;KGS </span> </TD>
		 
      </TR>   
	  
      <TR height='28'>
         <TD  width='260'><span style='font-size:22px; font-weight:bold'>GROSS WEIGHT:</span></TD>
		  <TD  width='260'><span style='font-size:22px; font-weight:bold' >&nbsp;$WG </span> </TD>
		  <TD  width='62'><span style='font-size:22px; font-weight:bold' >&nbsp;KGS</span> </TD>		 
      </TR>   
 
       <TR height='28'>
          <TD  width='260'><span style='font-size:22px; font-weight:bold'>CARTON NO.:  </span></TD>
		  <TD  width='260'><span style='font-size:22px; font-weight:bold' >&nbsp;$PreWord$i &nbsp;/  &nbsp; $PreWord$BoxTotal </span> </TD>
		  <TD  width='62'><span style='font-size:22px; font-weight:bold' >&nbsp; </span> </TD>
		  
      </TR>  

	  
       <TR height='45'>
          <TD colspan='3' align='center' valign='middle'><span style='font-size:36px; font-weight:bold' >$ClientTitle </span> </TD>
      </TR>			  
	  
	 </TBODY>	  
</TABLE>";
//echo "</div>";
?>
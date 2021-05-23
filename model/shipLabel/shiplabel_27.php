<?php 
//CG_ASIA出MCA
// <TD  ><div align='center'><div class='$AutoEnd'> $BoxCodeTable   </div> </TD>
//echo "<div style='page-break-after:always;'>";

$pageF=$i;
$mod=$pageF%2;
switch($mod){
	case 1:
		echo"<div id=Skech_Div$i style='margin:0'> <ul style='width:590px;height:290px;' > 
		<li style='float:left;width:290px;height:290; margin-right:5px;'>
			<TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=290 cellSpacing=0 cellPadding=0 width=290 border=0>
					<TR height='25'>
					  <TD   >&nbsp;   </TD>
				  </TR>   
				   <TR height='87'>
					  <TD  align='center'>$BoxCodeTable    &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; </TD>
				  </TR>
				  <TR height='30'> 
					 <TD  align='center' ><div align='center'><span style='font-size:30px; font-weight:bold' >$i  &nbsp&nbsp; &nbsp&nbsp; </span></div> </TD>
				  </TR>  
			 
				  <TR height='61'>
					 
					 <TD align='center' >
					 <TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=61 cellSpacing=0 cellPadding=0 width=290 border=0>
						<tr height='61' >
						<td align='right' width=150 > <div> <img src='../images/lable_box.png'  height='60' width='60' /></div>  </td>
						<td  align='left'> <div style='font-size:30px; font-weight:bold' >&nbsp;$BoxTotal  </div>  </td>
						</tr>
					 </TABLE>
					 
					 </TD>
					 
				  </TR>   
				   <TR >
					  <TD >&nbsp;</TD>
				  </TR>    
			</TABLE>		
		</li> ";	
	break;
	case $BoxTotal:
		echo "<li style='float:left;width:290px;height:290; margin-right:5px;'>
			<TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=290 cellSpacing=0 cellPadding=0 width=290 border=0>
					<TR height='25'>
					  <TD   >&nbsp;   </TD>
				  </TR>   
				   <TR height='87'>
					  <TD  align='center'>$BoxCodeTable    &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; </TD>
				  </TR>
				  <TR height='30'> 
					 <TD  align='center' ><div align='center'><span style='font-size:30px; font-weight:bold' >$i  &nbsp&nbsp; &nbsp&nbsp; </span></div> </TD>
				  </TR>  
			 
				  <TR height='61'>
					 
					 <TD align='center' >
					 <TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=61 cellSpacing=0 cellPadding=0 width=290 border=0>
						<tr height='61' >
						<td align='right' width=150 > <div> <img src='../images/lable_box.png'  height='60' width='60' /></div>  </td>
						<td  align='left'> <div style='font-size:30px; font-weight:bold' >&nbsp;$BoxTotal  </div>  </td>
						</tr>
					 </TABLE>
					 
					 </TD>
					 
				  </TR>   
				   <TR >
					  <TD >&nbsp;</TD>
			
				  </TR>    
			</TABLE>		
		</li>  </ul></div>";
	break;
	default:
		echo "<li style='float:left;width:290px;height:290; margin-right:5px;'>
			<TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=290 cellSpacing=0 cellPadding=0 width=290 border=0>
					<TR height='25'>
					  <TD   >&nbsp;   </TD>
				  </TR>   
				   <TR height='87'>
					  <TD  align='center'>$BoxCodeTable    &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; </TD>
				  </TR>
				  <TR height='30'> 
					 <TD  align='center' ><div align='center'><span style='font-size:30px; font-weight:bold' >$i  &nbsp&nbsp; &nbsp&nbsp; </span></div> </TD>
				  </TR>  
			 
				  <TR height='61'>
					 
					 <TD align='center' >
					 <TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=61 cellSpacing=0 cellPadding=0 width=290 border=0>
						<tr height='61' >
						<td align='right' width=150 > <div> <img src='../images/lable_box.png'  height='60' width='60' /></div>  </td>
						<td  align='left'> <div style='font-size:30px; font-weight:bold' >&nbsp;$BoxTotal  </div>  </td>
						</tr>
					 </TABLE>
					 
					 </TD>
					 
				  </TR>   
				   <TR >
					  <TD >&nbsp;</TD>
			
				  </TR>    
			</TABLE>		
		</li>  </ul></div>";	
	break;
}

/*
echo"<TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=292 cellSpacing=0 cellPadding=0 width=590 border=0>
	<TBODY>
        <TR height='25'>
          <TD   >&nbsp;   </TD>
      </TR>   
       <TR height='87'>
          <TD  align='center'>$BoxCodeTable    &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; &nbsp&nbsp; </TD>
      </TR>
      <TR height='30'> 
         <TD  align='center' ><div align='center'><span style='font-size:30px; font-weight:bold' >$i </span></div> </TD>
      </TR> 
	  
 
	  
 
      <TR height='61'>
         
         <TD align='center' >
		 <TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=61 cellSpacing=0 cellPadding=0 width=590 border=0>
		 	<tr height='61' >
			<td align='right' width=320 > <div> <img src='../images/lable_box.png'  height='60' width='60' /></div>  </td>
			<td  align='left'> <div style='font-size:30px; font-weight:bold' >&nbsp;$BoxTotal  </div>  </td>
			</tr>
		 </TABLE>
		 
		 </TD>
         
      </TR>   
       <TR >
          <TD >&nbsp;</TD>

      </TR>  
	 </TBODY>	  
</TABLE>";
*/

?>
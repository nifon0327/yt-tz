<?php 
	//skech专用标签模板 2013-10-21 ewen
	/*
	$mod=$i%2;
	if(($i%2)==1 ){
		echo "<div id=Skech_Div><ul>";
	}
	else {
		echo "</ul></div>";
	}
	*/
	$pos=strpos($InvoiceNO,'Skech');
	if($pos !== false){
		$InvoiceNO=substr($InvoiceNO,5);
	}
	$pos=strpos($InvoiceNO,'-');
	if($pos !== false){
		$InvoiceNO=substr($InvoiceNO,$pos+1);
	}	
	
	$YY=date("Y");
	$MM=date("m");
	$DD=date("d");
	//$DateStr=substr($YY,3,1).substr($YY,2,1).substr($MM,1,1).substr($MM,0,1).substr($DD,1,1).substr($DD,0,1);
	$DateStr=$DD.$MM.substr($YY,2,2);
	//$DateStr=
	$pageF=$i;
	$mod=$pageF%2;
	$size=40;
	$eSize='30px';
	if (strlen($eCode)<13){
		$eSize='30px';
	}
	else {
		$eSize='25px';
	}
	
	//echo "mod: $mod"; 
	switch($mod){
	case 1:
		echo"<div id=Skech_Div$i style='margin:0'> <ul style='width:580px;height:392px;' > 
		<li style='float:left;width:280px;height:392px; margin-right:5px;'>
			<table width='280px' height='300px' style='font-size:45px; font-weight:bold' >
			  <tr height='60px'><td  align='center'>$InvoiceNO</td></tr>
			  <tr height='60px'><td  align='center'>$i - $BoxTotal</td></tr>
			  <tr><td  align='center' style='font-size:$eSize;'>$eCode</td></tr>
			  <tr height='60px' style='font-size:40px;'><td  align='center'>$DateStr</td></tr>			  
			</table>			
		</li> ";
	break;
	case $BoxTotal:
		echo" <li style='float:left;width:280px;height:392px; margin-right:5px;'>
			<table width='280px' height='300px' style='font-size:45px; font-weight:bold' >
			  <tr height='60px'><td  align='center'>$InvoiceNO</td></tr>
			  <tr height='60px'><td  align='center'>$i - $BoxTotal</td></tr>
			  <tr><td  align='center' style='font-size:$eSize;'>$eCode</td></tr>
			  <tr height='60px' style='font-size:40px;'><td  align='center'>$DateStr</td></tr>			  
			</table>			
		</li></ul></div>";
	break;
	default:
		echo"<li style='float:left;width:280px;height:392px; margin-right:5px;'>
			<table width='280px' height='300px' style='font-size:45px; font-weight:bold' >
			  <tr height='60px'><td  align='center'>$InvoiceNO</td></tr>
			  <tr height='60px'><td  align='center'>$i - $BoxTotal</td></tr>
			  <tr><td  align='center' style='font-size:$eSize;'>$eCode</td></tr>
			  <tr height='60px' style='font-size:40px;'><td  align='center'>$DateStr</td></tr>			  
			</table>			
		</li> </ul></div>  ";
	break;
	}		
		
	/*
	case 1:
		echo"<div id=Skech_Div$i style='margin:0'> <ul style='width:580px;height:392px;' > 
		<li style='float:left;width:280px;height:392px; margin-right:5px;'>
			<table width='200px' height='380px' >
			  <tr>
				<td width='65px'><img src='../model/sketch_png.php?msg=$InvoiceNO&rot=90&size=$size'  > </td>
				<td width='65px'><img src='../model/sketch_png.php?msg=$i - $BoxTotal&rot=90&size=$size' > </td>
				<td width='65px'><img src='../model/sketch_png.php?msg=$DateStr&rot=90&size=$size' > </td>
			  </tr>
			</table>			
		</li> ";
	break;
	case $BoxTotal:
		echo" <li style='float:left;width:280px;height:392px; margin-right:5px;'>
			<table width='200px' height='380px' >
			  <tr>
				<td width='65px'><img src='../model/sketch_png.php?msg=$InvoiceNO&rot=90&size=$size' '> </td>
				<td width='65px'><img src='../model/sketch_png.php?msg=$i - $BoxTotal&rot=90&size=$size' > </td>
				<td width='65px'><img src='../model/sketch_png.php?msg=$DateStr&rot=90&size=$size' > </td>
			  </tr>
			</table>			
		</li></ul></div>";
	break;
	default:
		echo"<li style='float:left;width:280px;height:392px; margin-right:5px;'>
			<table width='200px'  height='380px'>
			  <tr>
				<td width='65px'><img src='../model/sketch_png.php?msg=$InvoiceNO&rot=90&size=$size' > </td>
				<td width='65px'><img src='../model/sketch_png.php?msg=$i - $BoxTotal&rot=90&size=$size' > </td>
				<td width='65px'><img src='../model/sketch_png.php?msg=$DateStr&rot=90&size=$size' > </td>
			  </tr>
			</table>			
		</li> </ul></div>  ";
	break;
	}
	*/
	/*
	
	case 1:
		echo"<div id=Skech_Div$i style='margin:0'> <ul style='width:392px;height:210px;list-style:none' > 
		<li style='float:left;width:190px;height:210px;text-align:center;line-height:33px; margin-right:5px;list-style:none;'>
		<dl><dt class='Font_val' style='list-style:none'>$InvoiceNO</dt></dl>
		<dl><dt class='Box_textB'>$i - $BoxTotal</dt></dl>
		<dl><dt class='Font_val'>$DateStr</dt></dl>
		</li> ";
	break;
	case $BoxTotal:
		echo" <li style='float:left;width:190px;height:210px;text-align:center;line-height:33px; margin-right:5px;list-style:none;'>
		<dl><dt class='Font_val' style='list-style:none'>$InvoiceNO</dt></dl>
		<dl><dt class='Box_textB'>$i - $BoxTotal</dt></dl>
		<dl><dt class='Font_val'>$DateStr</dt></dl>
		</li></ul></div>";
	break;
	default:
		echo"<li style='float:left;width:190px;height:210px;text-align:center;line-height:33px; margin-right:5px;list-style:none;'>
		<dl><dt class='Font_val' style='list-style:none'>$InvoiceNO</dt></dl>
		<dl><dt class='Box_textB'>$i - $BoxTotal</dt></dl>
		<dl><dt class='Font_val'>$DateStr</dt></dl>
		</li> </ul></div>  ";
	break;
	}	
	*/
	/*
	if(($i%2)==0  && $BoxTotal!=$i){
		echo "</ul></div>";
	}
	*/
	
/*	
			echo"
<TABLE  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;height:70px;width:385px;' cellSpacing=0 cellPadding=0 border=0>
     <TR><TD width=190><span class='Font_val'>Invoice：$InvoiceNO</sapn></TD><td width=5>&nbsp;</td><TD width=190><span class='Font_val'>Invoice：$InvoiceNO</sapn></TD></TR>
     <TR align='center'><TD><span class='Box_textB'>$i - $BoxTotal</sapn></TD><td>&nbsp;</td><TD><span class='Box_textB'>$i - $BoxTotal</sapn></TD></TR>  
  </TABLE>
";*/
?>
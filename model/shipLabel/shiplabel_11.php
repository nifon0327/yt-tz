 <style type="text/css"> 
 .fristdiv { 
     padding:2px; 
     text-align:left; 
     background-color:#ffffff; 
     border:2px solid #000000; 
     width: 580px;
     height: 292px;
}
.Destext{
	FONT-FAMILY:Arial;
	color:#000;
	font-size:32.18pt;
	text-align : left;
	font-weight:bold;
	line-height:25px;
	}
 </style> 
<?php 	
$eSize =42;
if($BoxCode!=""){
		$Field=explode("|",$BoxCode);$BoxCode0=$Field[0];$BoxCode1=$Field[1];
		if(is_numeric($BoxCode0)){	
			$BoxCode1=preg_replace("/,/","<br>",$BoxCode1);
			$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td align='center' valign='bottom'><iframe frameborder=0 marginheight=0 marginwidth=0 scrolling=no width='120' height='40'  src='../model/ean_13code.php?Code=$BoxCode0&lw=1&hi=25'></iframe></td></tr><tr><td height='32' valign='top' scope='col'><div align='center' class='code_title'>$BoxCode1</div></td></tr></table>";
			}
		else{
			$BoxCode0=preg_replace("/,/","<br>",$BoxCode0);
            if (strlen($BoxCode0)>20) $codestyle="code_title0"; else $codestyle="code_title";
			if(is_numeric($BoxCode1)){
				$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td align='left' valign='middle'><iframe frameborder=0 marginheight=0 marginwidth=0 scrolling=no width='180' height='40'  src='../model/ean_13code.php?Code=$BoxCode1&lw=1&hi=25'></iframe></td></tr></table>";
			  }
			 else{
				$BoxCodeTable="<table width='100%'  cellspacing='0'><tr><td height='32' valign='bottom' scope='col'></td></tr><tr><td align='center' valign='top'></td></tr></table>"; 
			 }
			}
		}
	else{
		$BoxCodeTable="&nbsp;";}
		
	if($eCode=="IPH-1400")$Picture="<img src='../images/iph1.jpg' height='100' width='150'>";
	if($eCode=="IPH-1050")$Picture="<img src='../images/iph2.jpg' height='100' width='150'>";
	if($eCode=="ISG-1100")$Picture="<img src='../images/iph3.jpg' height='100' width='150'>";
	
	$isPice=0;
	//$eCode="ISA-1201";
	if($eCode=="ISA-1001" ){
		$isPice=1;
		$Picture="<img src='../model/shipLabel/17381.jpg' height='100' >";
	}
	if($eCode=="ISA-1101" ){
		$isPice=1;
		$Picture="<img src='../model/shipLabel/17382.jpg' height='100' >";
	}
	if($eCode=="ISA-1201" ){
		$isPice=1;
		$Picture="<img src='../model/shipLabel/17383.jpg' height='100' >";
	}	
	
//$OrderPO="ISY-0025";
$OrderPO=$_SESSION["OrderPOS"];
if ($OrderPO=="") {
	$OrderPO="ISY-0362";
}
$dSize='16px';
if(strlen($Description)>60){
	$dSize='14px';
}
//echo $_SESSION["OrderPOS"] ;
//$OrderPO="ISY-0124";
//iph 专用
if($isPice!=1){
	
	echo"<div class='fristdiv'>
	<TABLE  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;' height='292' width=580 cellSpacing=0 cellPadding=0  border=0>
	<tr height='55' style='margin-top:5px'>
	<td width='10' >&nbsp;</td>
	<td align='left' colspan='3'><div  style='padding-top:6px;'>
				   <span  class='eCodetext' style='font-size:38 pt;'>$eCode</span></td>
	</tr>  
	<tr height='25'>
	<td width='10' >&nbsp;</td>
	<td class='Destext'  align='left' colspan='3' style='font-weight:bold;font-size:$dSize'>$Description</td>
	</tr>
	<tr height='25'><td width='10' >&nbsp;</td>
	<td width='190' align='left' valign='bottom' style='font-weight:bold'>QTY: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;  &nbsp;&nbsp; &nbsp; 数量</td>
	<td width='190' align='left' valign='bottom' style='font-weight:bold'>WEIGHT:  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp; 重量</td>
	<td width='190' align='left' valign='bottom' style='font-weight:bold'>CARTON NO.:&nbsp; &nbsp;  箱号</td>
	</tr>
	</tr>
	<tr height='70'><td width='10' >&nbsp;</td>
	<td width='190'><table border='0' style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0><tr><td class='A1011' width='150' height='50' align='center'><span style='font-weight:bold;font-size:$BoxSize pt;'>$BoxPcs</span></td><td width='40'>&nbsp;</td></tr>
	<tr><td class='A0111' width='150' height='5' align='right'><span style='font-weight:bold;font-size:14px;'>PCS</span></td><td width='40'>&nbsp;</td></tr>
	</table></td>
	<td width='190'><table border='0' style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0><tr><td class='A1011' width='150' height='50' align='center'><span style='font-weight:bold;font-size:$BoxSize pt;'>$WG</span></td><td width='40'>&nbsp;</td></tr>
	<tr><td class='A0111' width='150' height='5' align='right'><span style='font-weight:bold;font-size:14px;'>KG</span></td><td width='40'>&nbsp;</td></tr>
	</table></td>
	<td width='190'><table border='0' style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0><tr><td class='A1111' width='150' height='60' align='center'><span style='font-weight:bold;font-size:$BoxSize pt;'>$PreWord$i</span></td><td width='40'>&nbsp;</td></tr></table></td>
	</tr>
	<tr  ><td width='10'  height='112'>&nbsp;</td>
	<td >
	<table border='0' style='WORD-WRAP: break-word'  cellSpacing=0  cellPadding=0>
		   <tr><td width='190' height='40' ><span style='font-weight:bold;font-size:12px'>ORDER NO.:</span><BR><span style='font-weight:bold;font-size:18px'>$OrderPO</span></td></tr>
		   <tr><td >$BoxCodeTable</td></tr>
		   <tr><td valign='center'><span style='font-weight:bold;font-size:14px'>OUTER CARTON</span></td></tr>
	</table>
	</td>
	<td align='center'>$Picture</td>
	<td align='right' valign='bottom'><span style='font-weight:bold;font-size:52px'>I&nbsp;S&nbsp;Y&nbsp;&nbsp;&nbsp;</span><br><span style='font-weight:bold'>MADE IN CHINA&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
	</tr>
	  </TABLE></div>";
	  
}else{
	
	echo "<div >
<TABLE  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;' height='292' width=580 cellSpacing=0 cellPadding=0  border=0>
<tr height='55' style='margin-top:5px'>
<td width='10' >&nbsp;</td>
<td align='left' colspan='3'><div  style='padding-top:6px;'>
			   <span  class='eCodetext' style='font-size:38 pt;'>$eCode</span></td>
</tr>  
<tr height='25'>
<td width='10' >&nbsp;</td>
<td class='Destext'  align='left' colspan='3' style='font-weight:bold;font-size:$dSize'>$Description</td>
</tr>
<tr height='25'><td width='10' >&nbsp;</td>
<td width='190' align='left' valign='bottom' style='font-weight:bold'>QTY: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;  &nbsp;&nbsp; &nbsp; 数量</td>
<td width='190' align='left' valign='bottom' style='font-weight:bold'>WEIGHT:  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp; 重量</td>
<td width='190' align='left' valign='bottom' style='font-weight:bold'>CARTON NO.:&nbsp; &nbsp;  箱号</td>
</tr>
</tr>
<tr height='70'><td width='10' >&nbsp;</td>
<td width='190'><table border='0' style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0><tr><td class='A1011' width='150' height='50' align='center'><span style='font-weight:bold;font-size:$BoxSize pt;'>$BoxPcs</span></td><td width='40'>&nbsp;</td></tr>
<tr><td class='A0111' width='150' height='5' align='right'><span style='font-weight:bold;font-size:14px;'>PCS</span></td><td width='40'>&nbsp;</td></tr>
</table></td>
<td width='190'><table border='0' style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0><tr><td class='A1011' width='150' height='50' align='center'><span style='font-weight:bold;font-size:$BoxSize pt;'>$WG</span></td><td width='40'>&nbsp;</td></tr>
<tr><td class='A0111' width='150' height='5' align='right'><span style='font-weight:bold;font-size:14px;'>KG</span></td><td width='40'>&nbsp;</td></tr>
</table></td>
<td width='190'><table border='0' style='WORD-WRAP: break-word'  cellSpacing=0 cellPadding=0><tr><td class='A1111' width='150' height='60' align='center'><span style='font-weight:bold;font-size:$BoxSize pt;'>$PreWord$i</span></td><td width='40'>&nbsp;</td></tr></table></td>
</tr>
<tr  ><td width='10'  height='112'>&nbsp;</td>
<td >
<table border='0' style='WORD-WRAP: break-word'  cellSpacing=0  cellPadding=0>
       <tr><td width='190' height='40' ><span style='font-weight:bold;font-size:12px'>ORDER NO.:</span><BR><span style='font-weight:bold;font-size:18px'>$OrderPO</span></td></tr>
       <tr><td >$BoxCodeTable</td></tr>
       <tr><td valign='center'><span style='font-weight:bold;font-size:14px'>OUTER CARTON</span></td></tr>
</table>
</td>
<td colspan='2' align='center'>$Picture</td>
</tr>
  </TABLE></div>";	
}
?>
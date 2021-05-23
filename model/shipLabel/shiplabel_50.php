<?php 
if ($TestSign==1){
	$eCode="00137636";
	$Description="CO CLEAR SAM S6, GE<br>CO CLEAR SAM S6, YE";
	$OrderPO="2157331";
	$BoxPcs=1;
	$InBoxPcs=15;
	$BoxCode1="4049298933481";
}

$descArray=explode("|", $Description);
$counts=count($descArray);

if ($counts>1){
    $Description="";
	for ($k=0;$k<$counts;$k++){
		$Description.=$Description==""?$descArray[$k]:"<br>" . $descArray[$k];
	}
}

//echo "hama";
if ($BoxCode1!=''){
     $BoxCodeTable="<iframe frameborder=0 marginheight=0 marginwidth=0 width='250' height='70'  src='../model/ean_13code.php?Code=$BoxCode1&lw=1.8&hi=50&NewCode=1'  scrolling='no' style='margin-top:10px;'></iframe>";
}else{
	 $BoxCodeTable="&nbsp;";
}
$eCodes=explode('|', $eCode);
$eCode1 = $eCodes[0];
$eCode2 = count($eCodes)==2?$eCodes[1]:$eCodes[0];

$tempCode=strlen($eCode1)>=strlen($eCode2)?$eCode1:$eCode2;

$DivFontSize=strlen($tempCode)>=13?13-ceil((strlen($tempCode)-11)/2):13;

$DivFontStyle=$DivFontSize>0?'font-size:' . $DivFontSize .'px;':'';
$marginTop = strpos($_SERVER["HTTP_USER_AGENT"],"MSIE")?'-45px':'-75px';

$TitleStyle="font-size:14px;font-weight:bold;font-family:Arial;";
$DivStyle="font-family:Arial;font-size:16px; font-weight:bold;border: solid 1px black;display:block; vertical-align: middle;";
//bordercolor='#D2D2D2'
echo"<TABLE style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' height=292 cellSpacing=0 cellPadding=0 width=590 border=0 >
    <TBODY>
              <TR height='40'>
                  <TD  width='155'><span style='$TitleStyle'>Artikel-Nr./<br>Item NO.:</span></TD>
                  <TD  width='100'><span style='$TitleStyle' >Menge/<br>Quantity:</span></TD>
                  <TD  width='80'>&nbsp;</TD>
                  <TD width=''><span style='$TitleStyle'>Anmerkung(en)/<br>Remark(s):</span> </TD>
              </TR> 
               <TR height='45'>
                  <TD><div style='$DivStyle width:150;height:42;line-height:21px;$DivFontStyle'>
                             Art.-Nr.:&nbsp;&nbsp;$eCode1
                             MatNr:&nbsp;&nbsp;&nbsp;&nbsp;$eCode2 
                            </div>
                  </TD>
                  <TD><div style='$DivStyle width:100;height:42;line-height:42px;text-align:center;'>VE$BoxPcs</div></TD>
                  <TD><div style='$DivStyle width:70;height:42;line-height:21px;text-align:center;'>stuck/<br>Pcs.</div></TD>
                   <TD width='' rowspan='3'><div style='$DivStyle width:240;height:115;'></div> </TD>
              </TR> 
               <TR height='30'>
                  <TD colspan='3'><div style='$TitleStyle'>Artikelbezeichnung/Item description:</div></TD>
              </TR> 
               <TR height='40'>
                  <TD  width='220' colspan='3'><div style='$DivStyle width:325;height:40;'><span style='margin:2px 0px 0px 5px;display:block; font-size:12px;'>$Description</span></div> </TD>
              </TR>
              <TR height=''>
                  <td colspan='4'>
                     <table width='590' cellSpacing=0 cellPadding=0>
                      <tr>
                         <td width='125' style='$TitleStyle' height='30'>Auftrags-Nr./</br>PO No.:</td>
                         <td width='125' style='$TitleStyle'>Karton-Nr./</br>Carton No.:</td>
                         <td width='80'  rowspan='2' style='font-family:AshCloudIcon;font-size:65px;text-align:center;'>B</td>
                         <td  rowspan='2' style='text-align:center;'>$BoxCodeTable</td>
                      </tr>
                      <tr>
                         <td><div style='$DivStyle width:100;height:32;line-height:32px;'>&nbsp;$OrderPO</div></td>
                         <td><div style='$DivStyle width:100;height:32;text-align:center;line-height:32px;'>$i / $BoxTotal</div></td>
                      </tr>
                     </table>
                  </td>
              </TR>  		
               <TR height='50'>
                  <TD><div style='font-family:AshCloudIcon;font-size:150px;position: absolute;margin:$marginTop 0px 0px 0px;height:100%;'>C</div></TD>
                  <TD  colspan='3'><div style='float:right;font-family:Arial;font-size:22px;font-weight:bold;margin:26px 14px 0px 0px;' >ERLEBEN,WAS VERBINDET.</div></TD>
              </TR>  	
             </TBODY>	
</TABLE>";
//echo "</div>";
?>
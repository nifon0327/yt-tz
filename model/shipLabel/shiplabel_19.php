<?php 
$OrderNO=$OrderNO==""?"MA03 2013 00822":$OrderNO;
$KPackingList=$KPackingList==""?"12/11/2013":$KPackingList;
$CAddress=$CAddress==""?"&nbsp;&nbsp;ND PIACENZA <br> VIA BAZZANI 5/7<br>29100 LE MOSE":$CAddress;
$ABoxSpec1=explode('CM',$BoxSpec);
//$ABoxSpec=str_replace('¡Á','*',$ABoxSpec1[0]);
 
$pattern = "/[\x7f-\xff]/sim";
$replacement = '*';
$ABoxSpec=preg_replace($pattern, $replacement, $ABoxSpec1[0]); 
$ABoxSpec=preg_replace("'([\r\n])[\s]+'", "", $ABoxSpec);
$ABoxSpec=trim($ABoxSpec);
//echo $ABoxSpec;
$ABoxSpec2=explode('**',$ABoxSpec);
//echo "$ABoxSpec2[0]*$ABoxSpec2[1]*$ABoxSpec2[2]";
//$Volume="$ABoxSpec --"."$ABoxSpec2[0]*$ABoxSpec2[1]*$ABoxSpec2[2]";
//$Volume=sprintf("%.6f",$ABoxSpec2[0]*$ABoxSpec2[1]*$ABoxSpec2[2]/100/100/100);
$Volume=($ABoxSpec2[0]*$ABoxSpec2[1]*$ABoxSpec2[2])/100/100/100;

//echo $ABoxSpec2[0].'-@-'.trim($ABoxSpec2[0]);
//echo $ABoxSpec2[1]*1;
//echo $ABoxSpec2[2]*1;


$DA=explode(' ',preg_replace("'([\r\n])[\s]+'", " ", $Description));
$kCount=count($DA);
$DS='';
if($kCount>3){
	$KStyle=$DA[$kCount-3];
	$Materal=$DA[$kCount-2];
	$KColor=$DA[$kCount-1];

}
else {
	$KStyle="&nbsp;";
	$Materal="&nbsp;";
	$KColor="&nbsp;";
}
for ($k=0;$k<$kCount;$k++){
	if($DA[$k]=='GUESS'){
		break;
	}
	//echo "$DA[$k] <br>";
	$DS=$DS.' '.$DA[$k];
}
$Description=$DS;


$Drop="0";
$Tone="&nbsp;";

echo"<TABLE  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; font-size:9px;' height='292' cellSpacing=0 cellPadding=0 width='580' border=0>
  <tr height='46' >
    <td width='30%'  style='font-size:9px'  class='A1110'><table width='100%'  cellSpacing=0 cellPadding=0 border='0' style='font-size:9px'>
        <tr height='23' >
          <td valign='middle'>&nbsp;ORDER&nbsp;NO.</td>
        </tr>
        <tr  height='23' >
          <td valign='top' style='font-size:12px'>&nbsp;$OrderNO</td>
        </tr>
      </table></td>
    <td width='30%'  style='font-size:9px' class='A1110'>
	<table width='100%'   cellSpacing=0 cellPadding=0 border='0' style='font-size:9px'>
        <tr height='23' >
          <td valign='middle' >&nbsp;PACKING LIST DATE(DD/MM/YYYY)</td>
        </tr>
        <tr height='23' valign='top'>
          <td style='font-size:12px'>&nbsp;$KPackingList</td>
        </tr>
      </table></td>
    <td  width='30%'valign='middle' align='right' style='font-size:16px' class='A1110'>CG MOBILE CASE</td>
	<td  valign='middle' align='center' style='font-size:16px' class='A1101'>&nbsp;</td>
  </tr>
  <tr height='46'>
    <td align='left' valign='middle' style='font-size:9px' class='A0110'><table width='100%'  cellSpacing=0 cellPadding=0 border='0' style='font-size:9px'  >
        <tr height='23'  valign='middle'>
          <td>&nbsp;PACKAGE NO. /OF</td>
        </tr>
        <tr height='23 ' valign='top'>
          <td style='font-size:16px;'>&nbsp;$i &nbsp;/&nbsp; $BoxTotal</td>
        </tr>
      </table></td>
    <td class='A0110' >&nbsp;</td>
    <td width='30%'  class='A0110' >&nbsp;</td>
    <td width='' rowspan='2' style='font-size:16px' class='A0111' ><div align='center'>$BoxPcs</div></td>
  </tr>
  <tr>
    <td colspan='2' class='A0110'>
	<table width='100%' height='100%' cellSpacing=0 cellPadding=0 border='0'>
        <tr valign='bottom'>
          <td width='107' style='font-size:9px' valign='middle' >&nbsp;DESTINATION</td>
          <td width='27'>&nbsp;</td>
          <td width='183' rowspan='2'style='font-size:14px' valign='middle'>$CAddress</td>
          <td width='27'>&nbsp;</td>
        </tr>
        <tr valign='top'>
          <td style='font-size:16px'>&nbsp;GEPC</td>
          <td>&nbsp;</td>
          <td >&nbsp;</td>
        </tr>
      </table></td>
    <td class='A0110'><table width='100%' height='100%' cellSpacing=0 cellPadding=0 border='0'>
        <tr style='font-size:9px' valign='bottom'>
          <td width='70' >&nbsp;MEASURE(cm):</td>
          <td  align='left' style='font-size:10px'>&nbsp;$BoxSpec</td>
        </tr>
        <tr style='font-size:9px' valign='top'>
          <td>&nbsp;VOLUME(m<sup>3</sup>):</td>
          <td align='left' style='font-size:10px'>&nbsp;$Volume</td>
        </tr>
      </table></td>
  </tr>
  <tr height='46'>
    <td style='font-size:9px' class='A0110' >&nbsp;GROSS W.(KG):&nbsp;<span style='font-size:12px' >$WG</span></td>
    <td style='font-size:9px' class='A0110'>&nbsp;NET W.(KG):&nbsp;<span style='font-size:12px' >$NG</span></td>
    <td colspan='2' style='font-size:9px' class='A0111'><table width='100%' cellSpacing=0 cellPadding=0 border='0'  >
        <tr>
          <td style='font-size:9px' width='30%'>&nbsp;MADE IN:</td>
          <td style='font-size:16px'>CHINA</td>
        </tr>
      </table>
  </tr>
  <tr height='46 '>
    <td colspan='4' align='left' valign='middle' style='font-size:9px' class='A0111' ><div style='vertical-align:top;'>&nbsp;DESCRIPTION</div>
	<div style='vertical-align:center; float:left;width:580px;text-align:center;font-size:12px'>$Description</div></td>
  </tr>
  <tr height='46'>
    <td colspan='4'class='A0111'><table width='100%' height='100%' cellSpacing=0 cellPadding=0 border='0' align='center'>
        <tr align='center'  height='23'  valign='middle' >
          <td width='20%' style='font-size:9px'>STYLE</td>
          <td width='20%' style='font-size:9px'>MATERIAL</td>
          <td width='20%' style='font-size:9px'>COLOR</td>
          <td width='20%' style='font-size:9px'>DROP</td>
          <td width='20%' style='font-size:9px'>TONE</td>
        </tr>
        <tr align='center' style='font-size:16px'  height='23'  valign='top'>
          <td>$KStyle</td>
          <td>$Materal</td>
          <td>$KColor</td>
          <td>$Drop</td>
          <td>$Tone</td>
        </tr>
      </table></td>
  </tr>
</TABLE>";
?>
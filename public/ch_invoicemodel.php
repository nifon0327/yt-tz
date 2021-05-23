<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ch10_samplemail
$DataIn.ch10_mailaddress
二合一已更新
*/
//include "../basic/chksession.php" ;
include "../basic/parameter.inc";
$myResult = mysql_query("SELECT 
	M.Id,M.SendDate,M.Description,M.ExpressNO,M.Pieces,M.Weight,M.Price,M.Amount,M.Qty,
	A.Termini,A.LinkMan,A.Tel,A.Fax,A.Termini,A.Address
	FROM $DataIn.ch10_samplemail M 
	LEFT JOIN $DataIn.ch10_mailaddress A 
	ON M.LinkMan=A.Id WHERE M.Id=$I",$link_id);
if($myRow=mysql_fetch_array($myResult)){
	$Id=$myRow["Id"];
	$SendDate=$myRow["SendDate"];	
	$Pieces=$myRow["Pieces"];
	$Weight=$myRow["Weight"];
	$Price=$myRow["Price"];
	$Amount=$myRow["Amount"];		
	$Type=$myRow["Type"];	
	$Description=$myRow["Description"];
	$Qty=$myRow["Qty"];
	$ExpressNO=$myRow["ExpressNO"];
	$LinkMan=$myRow["LinkMan"];
	$Tel=$myRow["Tel"];
	$Fax=$myRow["Fax"];
	$ZIP=$myRow["ZIP"];
	$Address=$myRow["Address"];
	$Termini=$myRow["Termini"];
	}
include "../model/characterset.php";
?>
<style type="text/css">
td img {display: block;}
body {
	margin-left: 20px;
}
.style1 {
	color: #0000FF;
	font-size: 16px;
}
.style2 {
	color: #0000FF;
	font-size: 20px;
	font-weight: bold;
}
</style>

<table border="0" cellpadding="0" cellspacing="0" width="713">
  <tr>
    <td><img src="../images/spacer.gif" width="4" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="53" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="41" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="5" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="36" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="23" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="48" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="1" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="4" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="37" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="46" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="81" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="20" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="6" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="6" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="91" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="4" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="90" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="12" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="5" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="69" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="21" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="5" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="5" height="1" border="0" alt=""></td>
    <td><img src="../images/spacer.gif" width="1" height="1" border="0" alt=""></td>
  </tr>
  <tr>
    <td rowspan="2" colspan="11">&nbsp;</td>
    <td colspan="4"><img name="Invoice_r1_c12" src="../images/Invoice_r1_c12.gif" width="113" height="65" border="0" id="Invoice_r1_c12" alt=""></td>
    <td rowspan="2" colspan="9">&nbsp;</td>
    <td><img src="../images/spacer.gif" width="1" height="65" border="0" alt=""></td>
  </tr>
  <tr>
    <td colspan="4">&nbsp;</td>
    <td><img src="../images/spacer.gif" width="1" height="22" border="0" alt=""></td>
  </tr>
  <tr>
    <td rowspan="2" colspan="2"><img name="Invoice_r3_c1" src="../images/Invoice_r3_c1.gif" width="57" height="42" border="0" id="Invoice_r3_c1" alt=""></td>
    <td colspan="10" valign="bottom">
      <div align="center" class="style1">FRED CHEN </div>
    </td>
    <td rowspan="2" colspan="4"><img name="Invoice_r3_c13" src="../images/Invoice_r3_c13.gif" width="123" height="42" border="0" id="Invoice_r3_c13" alt=""></td>
    <td colspan="8" valign="bottom"><div align="center" class="style1"><?php  echo $ExpressNO?></div></td>
    <td><img src="../images/spacer.gif" width="1" height="38" border="0" alt=""></td>
  </tr>
  <tr>
    <td colspan="10"><img name="Invoice_r4_c3" src="../images/Invoice_r4_c3.gif" width="322" height="4" border="0" id="Invoice_r4_c3" alt=""></td>
    <td colspan="7"><img name="Invoice_r4_c17" src="../images/Invoice_r4_c17.gif" width="206" height="4" border="0" id="Invoice_r4_c17" alt=""></td>
    <td rowspan="13"><img name="Invoice_r4_c24" src="../images/Invoice_r4_c24.gif" width="5" height="285" border="0" id="Invoice_r4_c24" alt=""></td>
    <td><img src="../images/spacer.gif" width="1" height="4" border="0" alt=""></td>
  </tr>
  <tr>
    <td rowspan="2" colspan="2">&nbsp;</td>
    <td colspan="10">&nbsp;</td>
    <td rowspan="2" colspan="4"><img name="Invoice_r5_c13" src="../images/Invoice_r5_c13.gif" width="123" height="42" border="0" id="Invoice_r5_c13" alt=""></td>
    <td colspan="5" valign="bottom"><div align="center" class="style1"><?php  echo $Weight?></div></td>
    <td rowspan="2" colspan="2"><img name="Invoice_r5_c22" src="../images/Invoice_r5_c22.gif" width="26" height="42" border="0" id="Invoice_r5_c22" alt=""></td>
    <td><img src="../images/spacer.gif" width="1" height="38" border="0" alt=""></td>
  </tr>
  <tr>
    <td colspan="10"><img name="Invoice_r6_c3" src="../images/Invoice_r6_c3.gif" width="322" height="4" border="0" id="Invoice_r6_c3" alt=""></td>
    <td colspan="5"><img name="Invoice_r6_c17" src="../images/Invoice_r6_c17.gif" width="180" height="4" border="0" id="Invoice_r6_c17" alt=""></td>
    <td><img src="../images/spacer.gif" width="1" height="4" border="0" alt=""></td>
  </tr>
  <tr>
    <td rowspan="2" colspan="2"><img name="Invoice_r7_c1" src="../images/Invoice_r7_c1.gif" width="57" height="48" border="0" id="Invoice_r7_c1" alt=""></td>
    <td colspan="10" valign="bottom"><div align="center" class="style1"><?php  echo $LinkMan?></div></td>
    <td rowspan="2" colspan="4"><img name="Invoice_r7_c13" src="../images/Invoice_r7_c13.gif" width="123" height="48" border="0" id="Invoice_r7_c13" alt=""></td>
    <td colspan="7" valign="bottom"><div align="center" class="style1"><?php  echo $Tel?></div></td>
    <td><img src="../images/spacer.gif" width="1" height="44" border="0" alt=""></td>
  </tr>
  <tr>
    <td colspan="10"><img name="Invoice_r8_c3" src="../images/Invoice_r8_c3.gif" width="322" height="4" border="0" id="Invoice_r8_c3" alt=""></td>
    <td colspan="7"><img name="Invoice_r8_c17" src="../images/Invoice_r8_c17.gif" width="206" height="4" border="0" id="Invoice_r8_c17" alt=""></td>
    <td><img src="../images/spacer.gif" width="1" height="4" border="0" alt=""></td>
  </tr>
  <tr>
    <td rowspan="3" colspan="2"><img name="Invoice_r9_c1" src="../images/Invoice_r9_c1.gif" width="57" height="47" border="0" id="Invoice_r9_c1" alt=""></td>
    <td colspan="21" valign="bottom"><div align="left" class="style1"><?php  echo $Address?></div></td>
    <td><img src="../images/spacer.gif" width="1" height="40" border="0" alt=""></td>
  </tr>
  <tr>
    <td colspan="21"><img name="Invoice_r10_c3" src="../images/Invoice_r10_c3.gif" width="651" height="4" border="0" id="Invoice_r10_c3" alt=""></td>
    <td><img src="../images/spacer.gif" width="1" height="4" border="0" alt=""></td>
  </tr>
  <tr>
    <td rowspan="2" colspan="4">&nbsp;</td>
    <td rowspan="3" colspan="4"><img name="Invoice_r11_c7" src="../images/Invoice_r11_c7.gif" width="90" height="47" border="0" id="Invoice_r11_c7" alt=""></td>
    <td rowspan="2" colspan="2" valign="bottom"><div align="center" class="style1"><?php  echo $Termini?></div></td>
    <td rowspan="3" colspan="4"><img name="Invoice_r11_c13" src="../images/Invoice_r11_c13.gif" width="123" height="47" border="0" id="Invoice_r11_c13" alt=""></td>
    <td rowspan="2" colspan="7" valign="bottom"><div align="center" class="style1"><?php  echo $SendDate?></div></td>
    <td><img src="../images/spacer.gif" width="1" height="3" border="0" alt=""></td>
  </tr>
  <tr>
    <td rowspan="2" colspan="2"><img name="Invoice_r12_c1" src="../images/Invoice_r12_c1.gif" width="57" height="44" border="0" id="Invoice_r12_c1" alt=""></td>
    <td><img src="../images/spacer.gif" width="1" height="39" border="0" alt=""></td>
  </tr>
  <tr>
    <td colspan="4"><img name="Invoice_r13_c3" src="../images/Invoice_r13_c3.gif" width="105" height="5" border="0" id="Invoice_r13_c3" alt=""></td>
    <td colspan="2"><img name="Invoice_r13_c11" src="../images/Invoice_r13_c11.gif" width="127" height="5" border="0" id="Invoice_r13_c11" alt=""></td>
    <td colspan="7"><img name="Invoice_r13_c17" src="../images/Invoice_r13_c17.gif" width="206" height="5" border="0" id="Invoice_r13_c17" alt=""></td>
    <td><img src="../images/spacer.gif" width="1" height="5" border="0" alt=""></td>
  </tr>
  <tr>
    <td rowspan="6"><img name="Invoice_r14_c1" src="../images/Invoice_r14_c1.gif" width="4" height="462" border="0" id="Invoice_r14_c1" alt=""></td>
    <td colspan="2"><img name="Invoice_r14_c2" src="../images/Invoice_r14_c2.gif" width="94" height="4" border="0" id="Invoice_r14_c2" alt=""></td>
    <td rowspan="6"><img name="Invoice_r14_c4" src="../images/Invoice_r14_c4.gif" width="5" height="462" border="0" id="Invoice_r14_c4" alt=""></td>
    <td colspan="3"><img name="Invoice_r14_c5" src="../images/Invoice_r14_c5.gif" width="107" height="4" border="0" id="Invoice_r14_c5" alt=""></td>
    <td rowspan="2"><img name="Invoice_r14_c8" src="../images/Invoice_r14_c8.gif" width="1" height="94" border="0" id="Invoice_r14_c8" alt=""></td>
    <td rowspan="6"><img name="Invoice_r14_c9" src="../images/Invoice_r14_c9.gif" width="4" height="462" border="0" id="Invoice_r14_c9" alt=""></td>
    <td colspan="4"><img name="Invoice_r14_c10" src="../images/Invoice_r14_c10.gif" width="184" height="4" border="0" id="Invoice_r14_c10" alt=""></td>
    <td rowspan="6"><img name="Invoice_r14_c14" src="../images/Invoice_r14_c14.gif" width="6" height="462" border="0" id="Invoice_r14_c14" alt=""></td>
    <td colspan="2"><img name="Invoice_r14_c15" src="../images/Invoice_r14_c15.gif" width="97" height="4" border="0" id="Invoice_r14_c15" alt=""></td>
    <td rowspan="6"><img name="Invoice_r14_c17" src="../images/Invoice_r14_c17.gif" width="4" height="462" border="0" id="Invoice_r14_c17" alt=""></td>
    <td colspan="2"><img name="Invoice_r14_c18" src="../images/Invoice_r14_c18.gif" width="102" height="4" border="0" id="Invoice_r14_c18" alt=""></td>
    <td rowspan="6"><img name="Invoice_r14_c20" src="../images/Invoice_r14_c20.gif" width="5" height="462" border="0" id="Invoice_r14_c20" alt=""></td>
    <td colspan="2"><img name="Invoice_r14_c21" src="../images/Invoice_r14_c21.gif" width="90" height="4" border="0" id="Invoice_r14_c21" alt=""></td>
    <td rowspan="6"><img name="Invoice_r14_c23" src="../images/Invoice_r14_c23.gif" width="5" height="462" border="0" id="Invoice_r14_c23" alt=""></td>
    <td><img src="../images/spacer.gif" width="1" height="4" border="0" alt=""></td>
  </tr>
  <tr>
    <td colspan="2"><img name="Invoice_r15_c2" src="../images/Invoice_r15_c2.gif" width="94" height="90" border="0" id="Invoice_r15_c2" alt=""></td>
    <td colspan="3"><img name="Invoice_r15_c5" src="../images/Invoice_r15_c5.gif" width="107" height="90" border="0" id="Invoice_r15_c5" alt=""></td>
    <td colspan="4"><img name="Invoice_r15_c10" src="../images/Invoice_r15_c10.gif" width="184" height="90" border="0" id="Invoice_r15_c10" alt=""></td>
    <td colspan="2"><img name="Invoice_r15_c15" src="../images/Invoice_r15_c15.gif" width="97" height="90" border="0" id="Invoice_r15_c15" alt=""></td>
    <td colspan="2"><img name="Invoice_r15_c18" src="../images/Invoice_r15_c18.gif" width="102" height="90" border="0" id="Invoice_r15_c18" alt=""></td>
    <td colspan="2"><img name="Invoice_r15_c21" src="../images/Invoice_r15_c21.gif" width="90" height="90" border="0" id="Invoice_r15_c21" alt=""></td>
    <td><img src="../images/spacer.gif" width="1" height="90" border="0" alt=""></td>
  </tr>
  <tr>
    <td colspan="2"><img name="Invoice_r16_c2" src="../images/Invoice_r16_c2.gif" width="94" height="6" border="0" id="Invoice_r16_c2" alt=""></td>
    <td colspan="4"><img name="Invoice_r16_c5" src="../images/Invoice_r16_c5.gif" width="108" height="6" border="0" id="Invoice_r16_c5" alt=""></td>
    <td colspan="4"><img name="Invoice_r16_c10" src="../images/Invoice_r16_c10.gif" width="184" height="6" border="0" id="Invoice_r16_c10" alt=""></td>
    <td colspan="2"><img name="Invoice_r16_c15" src="../images/Invoice_r16_c15.gif" width="97" height="6" border="0" id="Invoice_r16_c15" alt=""></td>
    <td colspan="2"><img name="Invoice_r16_c18" src="../images/Invoice_r16_c18.gif" width="102" height="6" border="0" id="Invoice_r16_c18" alt=""></td>
    <td colspan="2"><img name="Invoice_r16_c21" src="../images/Invoice_r16_c21.gif" width="90" height="6" border="0" id="Invoice_r16_c21" alt=""></td>
    <td><img src="../images/spacer.gif" width="1" height="6" border="0" alt=""></td>
  </tr>
  <tr>
    <td colspan="2" valign="top"><br><div align="center" class="style1">/</div></td>
    <td colspan="4" valign="top"><br><div align="center" class="style1"><?php  echo $Pieces?></div></td>
    <td colspan="4" valign="top"><br><div align="center" class="style1"><?php  echo $Description?></div></td>
    <td colspan="2" valign="top"><br><div align="center" class="style1"><?php  echo $Qty?>PCS</div></td>
    <td colspan="2" valign="top"><br><div align="center" class="style1"><?php  echo $Price?></div></td>
    <td colspan="2" valign="top"><br><div align="center" class="style1"><?php  echo $Amount?></div></td>
    <td><img name="Invoice_r17_c24" src="../images/Invoice_r17_c24.gif" width="5" height="356" border="0" id="Invoice_r17_c24" alt=""></td>
    <td><img src="../images/spacer.gif" width="1" height="356" border="0" alt=""></td>
  </tr>
  <tr>
    <td colspan="2"><img name="Invoice_r18_c2" src="../images/Invoice_r18_c2.gif" width="94" height="4" border="0" id="Invoice_r18_c2" alt=""></td>
    <td rowspan="2" colspan="4"><img name="Invoice_r18_c5" src="../images/Invoice_r18_c5.gif" width="108" height="6" border="0" id="Invoice_r18_c5" alt=""></td>
    <td rowspan="2" colspan="4"><img name="Invoice_r18_c10" src="../images/Invoice_r18_c10.gif" width="184" height="6" border="0" id="Invoice_r18_c10" alt=""></td>
    <td rowspan="2" colspan="2"><img name="Invoice_r18_c15" src="../images/Invoice_r18_c15.gif" width="97" height="6" border="0" id="Invoice_r18_c15" alt=""></td>
    <td rowspan="2" colspan="2"><img name="Invoice_r18_c18" src="../images/Invoice_r18_c18.gif" width="102" height="6" border="0" id="Invoice_r18_c18" alt=""></td>
    <td rowspan="2" colspan="2"><img name="Invoice_r18_c21" src="../images/Invoice_r18_c21.gif" width="90" height="6" border="0" id="Invoice_r18_c21" alt=""></td>
    <td rowspan="5"><img name="Invoice_r18_c24" src="../images/Invoice_r18_c24.gif" width="5" height="66" border="0" id="Invoice_r18_c24" alt=""></td>
    <td><img src="../images/spacer.gif" width="1" height="4" border="0" alt=""></td>
  </tr>
  <tr>
    <td rowspan="2" colspan="2"><img name="Invoice_r19_c2" src="../images/Invoice_r19_c2.gif" width="94" height="44" border="0" id="Invoice_r19_c2" alt=""></td>
    <td><img src="../images/spacer.gif" width="1" height="2" border="0" alt=""></td>
  </tr>
  <tr>
    <td rowspan="6"><img name="Invoice_r20_c1" src="../images/Invoice_r20_c1.gif" width="4" height="259" border="0" id="Invoice_r20_c1" alt=""></td>
    <td colspan="10">&nbsp;</td>
    <td><img name="Invoice_r20_c14" src="../images/Invoice_r20_c14.gif" width="6" height="42" border="0" id="Invoice_r20_c14" alt=""></td>
    <td colspan="2"><img name="Invoice_r20_c15" src="../images/Invoice_r20_c15.gif" width="97" height="42" border="0" id="Invoice_r20_c15" alt=""></td>
    <td rowspan="2"><img name="Invoice_r20_c17" src="../images/Invoice_r20_c17.gif" width="4" height="50" border="0" id="Invoice_r20_c17" alt=""></td>
    <td><img name="Invoice_r20_c18" src="../images/Invoice_r20_c18.gif" width="90" height="42" border="0" id="Invoice_r20_c18" alt=""></td>
    <td colspan="4"><div align="center" class="style2"><?php  echo $Amount?></div></td>
    <td><img name="Invoice_r20_c23" src="../images/Invoice_r20_c23.gif" width="5" height="42" border="0" id="Invoice_r20_c23" alt=""></td>
    <td><img src="../images/spacer.gif" width="1" height="42" border="0" alt=""></td>
  </tr>
  <tr>
    <td rowspan="2" colspan="4"><img name="Invoice_r21_c2" src="../images/Invoice_r21_c2.gif" width="135" height="18" border="0" id="Invoice_r21_c2" alt=""></td>
    <td rowspan="2" colspan="8"><img name="Invoice_r21_c6" src="../images/Invoice_r21_c6.gif" width="260" height="18" border="0" id="Invoice_r21_c6" alt=""></td>
    <td><img name="Invoice_r21_c14" src="../images/Invoice_r21_c14.gif" width="6" height="8" border="0" id="Invoice_r21_c14" alt=""></td>
    <td colspan="2"><img name="Invoice_r21_c15" src="../images/Invoice_r21_c15.gif" width="97" height="8" border="0" id="Invoice_r21_c15" alt=""></td>
    <td colspan="5"><img name="Invoice_r21_c18" src="../images/Invoice_r21_c18.gif" width="197" height="8" border="0" id="Invoice_r21_c18" alt=""></td>
    <td><img name="Invoice_r21_c23" src="../images/Invoice_r21_c23.gif" width="5" height="8" border="0" id="Invoice_r21_c23" alt=""></td>
    <td><img src="../images/spacer.gif" width="1" height="8" border="0" alt=""></td>
  </tr>
  <tr>
    <td colspan="10"><img name="Invoice_r22_c14" src="../images/Invoice_r22_c14.gif" width="309" height="10" border="0" id="Invoice_r22_c14" alt=""></td>
    <td><img src="../images/spacer.gif" width="1" height="10" border="0" alt=""></td>
  </tr>
  <tr>
    <td colspan="23"><img name="Invoice_r23_c2" src="../images/Invoice_r23_c2.gif" width="709" height="116" border="0" id="Invoice_r23_c2" alt=""></td>
    <td><img src="../images/spacer.gif" width="1" height="116" border="0" alt=""></td>
  </tr>
  <tr>
    <td colspan="23">&nbsp;</td>
    <td><img src="../images/spacer.gif" width="1" height="32" border="0" alt=""></td>
  </tr>
  <tr>
    <td colspan="8">&nbsp;</td>
    <td colspan="7"><img name="Invoice_r25_c10" src="../images/Invoice_r25_c10.gif" width="287" height="51" border="0" id="Invoice_r25_c10" alt=""></td>
    <td colspan="8">&nbsp;</td>
    <td><img src="../images/spacer.gif" width="1" height="51" border="0" alt=""></td>
  </tr>
</table>

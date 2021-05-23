<?php
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$ModelCompanyId=" and CompanyId='$DeliveryValue'";
$CompanyId=$CompanyId==""?$DeliveryValue:$CompanyId;

$clientResult = mysql_query("
SELECT 
C.Forshort,C.BankId
FROM $DataIn.trade_object C
WHERE C.CompanyId=$CompanyId  LIMIT 1
",$link_id);
if($clientRows = mysql_fetch_array($clientResult)){
	$Forshort=$clientRows["Forshort"];
	$BankId=$clientRows["BankId"];
	$TmpForshort=$Forshort;
	$ForshortArray=explode(" ",$Forshort);
	$Forshort=$ForshortArray[0];
}
$whoXY="<span style='color:#F00'>研砼</span>";
?>
	<table width="430" border="0" cellspacing="0"><input name="TempValue" type="hidden" id="TempValue">
		<tr>
		  <td colspan="2" align="center" valign="top"><?php    echo $whoXY?>扣款单资料<?php    echo $Forshort?></td>
		</tr>
		<tr>
		  <td width="62" align="center">扣款单名称</td>
	  <td>
	  <?php
	  	$maxInvoiceNO=mysql_query("SELECT InvoiceNO FROM $DataIn.ch1_shipmain WHERE Sign='-1' $ModelCompanyId ORDER BY Date DESC,InvoiceNO LIMIT 1",$link_id);
		if($maxRow=mysql_fetch_array($maxInvoiceNO)){
			$maxNO=$maxRow["InvoiceNO"];
			$formatArray=explode(" ",$maxNO);
			$lencount=count($formatArray);
			$maxNum=trim(preg_replace("/([^0-9]+)/i","",$formatArray[$lencount-1]))+1;//提取编号
			$NewInvoiceNO="note ".$maxNum;
			//echo "$lencount: $NewInvoiceNO";
			}
		else{
			$NewInvoiceNO="note 001";
			}
	  ?>
	  <input name="InvoiceNO" type="text" id="InvoiceNO" value="<?php    echo $NewInvoiceNO?>" size="50"></td>
      <input name="Forshort" type="hidden" id="Forshort" value="<?php    echo $TmpForshort?>" >

	  </tr>
		<tr>
		  <td align="center">扣款单日期</td>
		  <td><input name="ShipDate" type="text" id="ShipDate" value="<?php    echo date("Y-m-d")?>" size="50" maxlength="10" <?php    echo $OnChange?> onfocus="toTempValue(this.value)"></td>
		</tr>
		<tr>
		  <td align="center">扣款单信息</td>
		  <td><input name="Wise" type="text" id="Wise" size="50"></td>
  		</tr>
		<tr>
		  <td height="10" align="center">&nbsp;</td>
		  <td>&nbsp;</td>
	  </tr>
		  <td align="center">Notes</td>
		  <td><textarea name="Notes" rows="2" id="Notes" style="width: 280px;"></textarea></td>
  		</tr>
		<tr>
		  <td height="10" align="center">&nbsp;</td>
		  <td>&nbsp;</td>
	  </tr>
      <tr>
              <td height="30" align="center" valign="top">款项种类</td>
              <td>&nbsp;<select  name='ShipType' id='ShipType' style='width:280px' dataType='Require' msg='未选择'>
              <option value=''>请选择</option>;
              <option value='credit'>CreditNote(扣款)</option>
              <option value='debit'>DebitNote(收款)</option>
              </select>


            </td>

      </tr>
		<tr>
		  <td height="30" align="center" valign="top">文档模板</td>
  		  <td>
          <?php
		  $SubMit="<span onClick='Validator.Validate(document.getElementById(document.form1.id),3,\"ch_creditnote_toship\",2)' $onClickCSS>确定</span>&nbsp;";
		  $checkBank=mysql_query("SELECT Id,Title FROM $DataIn.ch8_shipmodel WHERE 1 $ModelCompanyId ORDER BY Id",$link_id);
		  if($BankRow=mysql_fetch_array($checkBank)){
		  	echo"&nbsp;<select name='ModelId' id='ModelId' style='width:280px' dataType='Require' msg='未选'>";
			echo"<option value=''>请选择</option>";
			$i=1;
			do{
				$Id=$BankRow["Id"];
				$Title=$BankRow["Title"];
				$Checked=$i==1?"checked":"";
				echo"<option value='$Id'>$Title</option>";
				$i++;
				}while($BankRow=mysql_fetch_array($checkBank));
			echo"</select>";
			}
			else{
				$SubMit="";
				echo"<div class='redB'>此客户出货文档模板的资料不全,不能生成出货单.</div>";
				}
		  ?>
</td>
  		</tr>
		<tr>
		  <td height="30" align="center" valign="top">收款帐号</td>
 		  <td>

		  <?php

				echo"&nbsp;<select name='BankId' id='BankId' style='width:280px' dataType='Require' msg='未选择'>";
					$PayBankResult = mysql_query("SELECT Id,Title FROM $DataPublic.my2_bankinfo WHERE Id='$BankId'",$link_id);
			    if($PayBankRow = mysql_fetch_array($PayBankResult)){
					 $Id=$PayBankRow["Id"];
					 $Title=$PayBankRow["Title"];
					 echo"<option value='$Id' selected>$Title</option>";
				 }
               else{
	                  switch($DeliveryValue){  //CEL 报关，走对公账号4, 其它方式走：上海对公账号 5,
						case 1003:  //Laz
						case 1018:  //EUR
						case 1024:  //Kon
						case 1031:  //Elite
						case 1091:  //Skech
							echo "<option value='4' selected='selected'>研砼国内对公账号</option>";
							break;
						default:
							echo "<option value='5' selected='selected' >研砼上海对公账号</option>";
							//echo "<option value='1' selected='selected' >其它方式出口</option>";
							break;
					}

               }

		  ?>
          </select>
    	</td>
  </tr>
		<tr valign="bottom"><td height="27" colspan="2" align="right"><?php    echo $SubMit?> &nbsp;&nbsp; <a href="javascript:closeMaskDiv()">取消</a></td></tr>
</table>

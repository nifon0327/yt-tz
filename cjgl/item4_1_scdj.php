<?
session_start();
//读取产品资料
include "../basic/parameter.inc";
include "../model/modelfunction.php";
$CheckSql=mysql_query("SELECT P.CompanyId,C.Forshort,S.OrderPO,
S.ProductId,P.cName,P.eCode,S.POrderId,S.Qty
FROM  $DataIn.yw1_ordersheet S 
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
WHERE 1 AND S.POrderId=$POrderId",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$CompanyId=$CheckRow["CompanyId"];
	$Forshort=$CheckRow["Forshort"];
	$OrderPO=$CheckRow["OrderPO"];
	$ProductId=$CheckRow["ProductId"];
	$cName=$CheckRow["cName"];
	$eCode=$CheckRow["eCode"];
	$OrderQty=$CheckRow["Qty"];		//订单总数量
	}
?>
  <table width="800" height="100" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr align="center" bgcolor="#d6efb5">
    <td width="142" height="50" class="A1111">客户</td>
    <td width="116" class="A1101">PO</td>
    <td width="117" class="A1101">产品ID</td>
    <td width="390" class="A1101">产品名称</td>
    <td width="338" class="A1101">Product Code </td>
    <td width="195" class="A1101">订单流水号</td>
  </tr>
  <tr align="center">
    <td height="50" class="A0111"><?=$Forshort?></td>
    <td class="A0101"><?=$OrderPO?></td>
    <td class="A0101"><?=$ProductId?></td>
    <td class="A0101"><?=$cName?></td>
    <td class="A0101"><?=$eCode?></td>
    <td class="A0101"><?=$POrderId?></td>
  </tr>
</table>
<br>
<table width="800" height="221" border="0" cellpadding="0" cellspacing="0">
	<tr align="center" bgcolor="#d6efb5">
		<td height="50" colspan="5" class="A0000">登记分批生产数量</td>
  </tr>
	<tr align="center" bgcolor="#d6efb5">
		<td height="40"  width="179" class="A1111">订单数量</td>
		<td width="200" class="A1101">已登记</td>
		<td width="200" class="A1101">未登记</td>
		<td width="300" class="A1101">本次预计生产数量(可修改)</td>
		<td width="240" class="A1101">&nbsp;</td>
	</tr>
<?php
$TotalResult=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS TotalQty FROM $DataIn.yw1_scsheet WHERE POrderId=$POrderId AND Estate=0",$link_id));
$TotalQty=$TotalResult["TotalQty"]==""?0:$TotalResult["TotalQty"];
$UnQty=$OrderQty-$TotalQty;
$scSheetResult=mysql_fetch_array(mysql_query("SELECT Qty FROM $DataIn.yw1_scsheet WHERE POrderId=$POrderId AND Estate=1",$link_id));
$thisQty=$scSheetResult["Qty"]==""?0:$scSheetResult["Qty"];
$CheckStr="onfocus='ClearHere(this)'  onblur='CheckQty(this,$i)'";
echo  "<tr align='center' bgcolor='#FFFFFF'>
             <td height='50' class='A0111'>$OrderQty</td>
  	         <td class='A0101'>$TotalQty</td>
             <td class='A0101'><input name='UnQty' type='hidden' class='I0000C' id='UnQty' value='$UnQty' >$UnQty</td>
             <td class='A0101'><input name='thisQty' type='text' class='I0000C' id='thisQty' value='$thisQty' size='30'  $CheckStr ></td>
             <td class='A0101'><span class='ButtonH_25' id='saveBtn' name='saveBtn' onclick='ToSaveSC(\"$POrderId\")'>提交</span></td></tr>";
?>
  <tr>
    <td height="61" colspan="5"  align="center" class="A0000" bgcolor="#d6efb5">
	<table border="0" cellpadding="0" cellspacing="8" align="right" width="100%">
      <tr align="center">
	   <td class="A0000" height="45" id="InfoBack" >&nbsp;</td>
        <td width="20">&nbsp;</td>
        <td width="80"><input type="button" name="Submit1" value="取消" onclick="closeMaskDiv()"></td>
        <td width="15" >&nbsp;</td>
      </tr>
    </table></td>
  </tr>
</table>
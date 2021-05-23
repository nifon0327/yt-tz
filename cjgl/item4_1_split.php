<?php   
//电信-zxq 2012-08-01
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//读取产品资料
include "../basic/parameter.inc";
include "../model/modelfunction.php";
$CheckSql=mysql_query("SELECT P.CompanyId,C.Forshort,S.OrderPO,S.Qty,
S.ProductId,P.cName,P.eCode,S.POrderId,S.sgRemark
FROM $DataIn.yw1_ordersheet S
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
WHERE 1 AND S.Id=$Id",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$CompanyId=$CheckRow["CompanyId"];
	$Forshort=$CheckRow["Forshort"];
	$OrderPO=$CheckRow["OrderPO"];
	$ProductId=$CheckRow["ProductId"];
	$POrderId=$CheckRow["POrderId"];
	$cName=$CheckRow["cName"];
	$eCode=$CheckRow["eCode"];
	$sgRemark=$CheckRow["sgRemark"];
	$Qty=$CheckRow["Qty"];
	}
	
$BoxResult = mysql_query("SELECT P.Relation FROM $DataIn.pands P LEFT JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId WHERE 1 and P.ProductId='$ProductId' AND P.ProductId>0 and T.TypeId='9040'",$link_id);
if($BoxRows = mysql_fetch_array($BoxResult)){
   $Relation=$BoxRows["Relation"];
   $RelationArray=explode("/",$Relation);
   if($RelationArray[1]!="")$Relation=$RelationArray[1];
   else $Relation=$RelationArray[0];
  }
 $OrderTimeSql=mysql_query("SELECT COUNT(P.eCode) AS splitTime FROM $DataIn.yw1_ordersheet S
LEFT  JOIN $DataIn.productdata P ON S.ProductId=P.ProductId WHERE S.OrderPO='$OrderPO' AND P.eCode='$eCode'",$link_id);
$splitTime=mysql_result($OrderTimeSql,0,"splitTime");

if($splitTime<=10 || $CompanyId==1056){//除Ontario 可以拆单两次以上

  $POrderIdResult=mysql_query("SELECT * FROM $DataIn.ck5_llsheet 
	                WHERE StockId like'$POrderId%' AND Estate=0");
   if((mysql_num_rows($POrderIdResult)>0) || ($Qty>0) ){
      $llSign=1;
?>
<table width="860" height="100" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr align="center" bgcolor="#d6efb5"><td colspan="7" height="50"><span style="color:#FF0000">订单有领料或数量小于2000,需业务主管审核后方可拆单:</span></td></tr>
    <tr align="center" bgcolor="#d6efb5">
	<td height="50" width="120" class="A1111">订单流水号
	<input type="hidden" id="POrderId" name="POrderId" value="<?php    echo $POrderId?>"><input type="hidden" id="Id" name="Id" value="<?php    echo $Id?>"></td>
    <td width="110" class="A1101">PO</td>
    <td width="380" class="A1101">产品名称</td>
    <td width="300" class="A1101">Product Code </td>
	<td width="180" class="A1101">原订单数量</td>
	<td width="180" class="A1101">拆分数量1</td>
	<td width="180" class="A1101">拆分数量2</td>
  </tr>
  <tr align="center">
	<td  height="50" class="A0111"><?php    echo $POrderId?>&nbsp;<input id="Relation" type="hidden" name="Relation" value="<?php    echo $Relation?>"></td>
    <td class="A0101"><?php    echo $OrderPO?></td>
    <td class="A0101"><?php    echo $cName?></td>
    <td class="A0101"><?php    echo $eCode?></td>
	<td class="A0101"><input name="Qty" type="text" id="Qty" value="<?php    echo $Qty?>" size="10" class="noLine" readonly></td>
		<td class="A0101"><input name="Qty1" type="text" id="Qty1" size="10" class="noLine" title="输入第一个拆分的数量" onchange="ChangeQty()"></td>
		<td class="A0101"><input name="Qty2" type="text" id="Qty2" size="10" class="noLine" dataType="Number" Msg="拆分的数量不正确" readonly></td>
  </tr>
  <tr bgcolor="#d6efb5"><td colspan="3" height="60" align="right">拆单原因:</td><td colspan="4"><textarea id="SpiltRemark" name="SpiltRemark" cols="80" rows="3" dataType="Require" Msg="未填写原因" ></textarea></tr>
  <tr>
    <td height="61" colspan="7"  align="center" class="A0000" bgcolor="#d6efb5">
	<table border="0" cellpadding="0" cellspacing="8" align="right" width="100%">
      <tr align="center">
	   <td class="A0000" height="45" id="InfoBack" >&nbsp;</td>
        <td width="20">&nbsp;</td>
        <td width="80"><input type="button" id='spiltBtn1' name="spiltBtn1" value="取消" onclick="closeMaskDiv()"></td>
        <td width="20">&nbsp;</td>
        <td width="80"><input type="button" id='spiltBtn2' name="spiltBtn2" value="提交" onclick="ToSplietOrder(<?php    echo $llSign?>)" disabled></td>
      </tr>
    </table></td>
  </tr>
  <tr bgcolor="#d6efb5"><td colspan="7" height="40">&nbsp;&nbsp;说明:拆分数量1所在的子单均使用原订单流水号和原需求单的流水号，并继承原单的采购资料；拆分数量2所在的子单如果原需求单已下则使用库存，否则按需求采购</td></tr>
</table>

<?php   	  
	   }
   else{
    $llSign=0;
?>
<table width="860" height="100" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr align="center" bgcolor="#d6efb5">
	<td height="50" width="120" class="A1111">订单流水号
	<input type="hidden" id="POrderId" name="POrderId" value="<?php    echo $POrderId?>"><input type="hidden" id="Id" name="Id" value="<?php    echo $Id?>"></td>
    <td width="110" class="A1101">PO</td>
    <td width="380" class="A1101">产品名称</td>
    <td width="300" class="A1101">Product Code </td>
	<td width="180" class="A1101">原订单数量</td>
	<td width="180" class="A1101">拆分数量1</td>
	<td width="180" class="A1101">拆分数量2</td>
  </tr>
  <tr align="center">
	<td  height="50" class="A0111"><?php    echo $POrderId?>&nbsp;<input id="Relation" type="hidden" name="Relation" value="<?php    echo $Relation?>"></td>
    <td class="A0101"><?php    echo $OrderPO?></td>
    <td class="A0101"><?php    echo $cName?></td>
    <td class="A0101"><?php    echo $eCode?></td>
	<td class="A0101"><input name="Qty" type="text" id="Qty" value="<?php    echo $Qty?>" size="10" class="noLine" readonly></td>
		<td class="A0101"><input name="Qty1" type="text" id="Qty1" size="10" class="noLine" title="输入第一个拆分的数量" onchange="ChangeQty()"></td>
		<td class="A0101"><input name="Qty2" type="text" id="Qty2" size="10" class="noLine" dataType="Number" Msg="拆分的数量不正确" readonly></td>
  </tr>
  <tr>
    <td height="61" colspan="7"  align="center" class="A0000" bgcolor="#d6efb5">
	<table border="0" cellpadding="0" cellspacing="8" align="right" width="100%">
      <tr align="center">
	   <td class="A0000" height="45" id="InfoBack" >&nbsp;</td>
        <td width="20">&nbsp;</td>
        <td width="80"><input type="button"  id='spiltBtn1' name="spiltBtn1" value="取消" onclick="closeMaskDiv()"></td>
        <td width="20">&nbsp;</td>
        <td width="80"><input type="button"  id='spiltBtn2' name="spiltBtn2" value="提交" onclick="ToSplietOrder(<?php    echo $llSign?>)" disabled></td>
      </tr>
    </table></td>
  </tr>
  <tr bgcolor="#d6efb5"><td colspan="7" height="40">&nbsp;&nbsp;说明:拆分数量1所在的子单均使用原订单流水号和原需求单的流水号，并继承原单的采购资料；拆分数量2所在的子单如果原需求单已下则使用库存，否则按需求采购</td></tr>
</table>
<?php   
   }
}
else{
?>
<table width="660" height="120" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
 <tr align="center" bgcolor="#d6efb5">
 <td height="80" align="center">
 <span style="color:#FF0000;font-size:18px;">该订单已拆单两次,不能再拆单</span></td></tr>
  <tr align="center" bgcolor="#d6efb5">
  <td align="right"><input type="button" name="Submit" value="取消" onclick="closeMaskDiv()">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
 </table>
 <?php   
 }
 ?>

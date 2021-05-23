<?php   
//电信-zxq 2012-08-01
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//读取产品资料
include "../basic/parameter.inc";
include "../model/modelfunction.php";
$CheckSql=mysql_query("SELECT P.CompanyId,C.Forshort,S.OrderPO,
S.ProductId,P.cName,P.eCode,S.POrderId,S.Qty
FROM $DataIn.yw1_ordersheet S
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
	}
?>
  <table width="800" height="86" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr align="center" bgcolor="#CCCCCC">
    <td width="142" height="36" class="A1111">客户</td>
    <td width="116" class="A1101">PO</td>
    <td width="117" class="A1101">产品ID</td>
    <td width="390" class="A1101">产品名称</td>
    <td width="338" class="A1101">Product Code </td>
    <td width="195" class="A1101">工单流水号
      <input name="POrderId" type="hidden" id="POrderId" value="<?php    echo $POrderId?>"></td>
      <input name="sPOrderId" type="hidden" id="sPOrderId" value="<?php    echo $sPOrderId?>"></td>
  </tr>
  <tr align="center">
    <td height="50" class="A0111"><?php    echo $Forshort?></td>
    <td class="A0101"><?php    echo $OrderPO?></td>
    <td class="A0101"><?php    echo $ProductId?></td>
    <td class="A0101"><?php    echo $cName?></td>
    <td class="A0101"><?php    echo $eCode?></td>
    <td class="A0101"><?php    echo $sPOrderId?></td>
  </tr>
</table>
<br>
<table width="800" height="273" border="0" cellpadding="0" cellspacing="0">
	<tr align="center" bgcolor="#CCCCCC">
		<td height="44" colspan="3" class="A0000">打印任务</td>
  </tr>
	<tr align="center"  bgcolor="#CCCCCC">
		<td width="125" height="41"class="A1111">序号</td>
		<td width="168" class="A1101">分类</td>
		<td class="A1101">打印数量</td>
	</tr>
	<tr align="center" bgcolor="#CCCCCC">
		<td width="125" height="41" bgcolor="#CCCCCC" class="A0111">1、</td>
		<td width="168" bgcolor="#CCCCCC" class="A0101">背卡条码</td>
		<td class="A0101"><input name="Qty1" type="text" id="Qty1" size="68"></td>
	</tr>
  <tr align="center" bgcolor="#CCCCCC">
    <td height="42" bgcolor="#CCCCCC" class="A0111">2、</td>
  	<td bgcolor="#CCCCCC" class="A0101">PE袋标签</td>
    <td class="A0101"><input name="Qty2" type="text" id="Qty2" size="68"></td>
  </tr>
    <tr align="center" bgcolor="#CCCCCC">
    <td height="44" bgcolor="#CCCCCC" class="A0111">3、</td>
  	<td bgcolor="#CCCCCC" class="A0101">外箱标签</td>
    <td class="A0101"><input name="Qty3" type="text" id="Qty3" size="68"></td>
  </tr>
 <tr align="center" bgcolor="#CCCCCC">
    <td height="44" bgcolor="#CCCCCC" class="A0111">4、</td>
  	<td bgcolor="#CCCCCC" class="A0101">白盒/坑盒</td>
    <td class="A0101"><input name="Qty4" type="text" id="Qty4" size="68"></td>
  </tr>

  <tr bgcolor="#CCCCCC">
    <td height="61" colspan="3"  align="center" class="A0000">
	  <table border="0" cellpadding="0" cellspacing="8" align="right" width="100%">
      <tr align="center">
	   <td class="A0000" height="45" id="InfoBack" >&nbsp;</td>
        <td width="20">&nbsp;</td>
        <td width="80"><input type="button" name="Submit" value="取消" onclick="closeMaskDiv()"></td>
        <td width="20">&nbsp;</td>
        <td width="80"><input type="button" name="Submit" value="提交" onclick="ToSavePT()"></td>
      </tr>
    </table></td>
  </tr>
</table>

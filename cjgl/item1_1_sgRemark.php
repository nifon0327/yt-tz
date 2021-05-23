<?php   
//电信-zxq 2012-08-01
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//读取产品资料
include "../basic/parameter.inc";
include "../model/modelfunction.php";

$CheckSql=mysql_query("SELECT SC.sPOrderId,
P.CompanyId,C.Forshort,S.OrderPO,
S.ProductId,P.cName,P.eCode,S.POrderId,SC.Remark
FROM $DataIn.yw1_scsheet SC 
LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId = SC.POrderId
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
WHERE 1 AND SC.sPOrderId=$Id",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$CompanyId=$CheckRow["CompanyId"];
	$Forshort=$CheckRow["Forshort"];
	$OrderPO=$CheckRow["OrderPO"];
	$ProductId=$CheckRow["ProductId"];
	$POrderId=$CheckRow["POrderId"];
	$sPOrderId=$CheckRow["sPOrderId"];
	$cName=$CheckRow["cName"];
	$eCode=$CheckRow["eCode"];
	$Remark=$CheckRow["Remark"];
	}
?>
  <table width="800" height="100" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr align="center" bgcolor="#d6efb5">
    <td width="142" height="50" class="A1111">客户</td>
    <td width="116" class="A1101">PO</td>
    <td width="117" class="A1101">产品ID</td>
    <td width="390" class="A1101">产品名称</td>
    <td width="338" class="A1101">Product Code </td>
    <td width="195" class="A1101">工单流水号
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
<table width="800" height="111" border="0" cellpadding="0" cellspacing="0">
  <tr align="center">
    <td width="259" height="50" bgcolor="#d6efb5" class="A0111">生管备注</td>
  	<td bgcolor="#FFFFFF" class="A0101"><textarea name="sgRemark" cols="80" rows="3" class="I0000C" id="sgRemark" onFocus="ClearStr()"><?php    echo $Remark?></textarea></td>
  </tr>
  <tr>
    <td height="61" colspan="2"  align="center" class="A0000" bgcolor="#d6efb5">
	<table border="0" cellpadding="0" cellspacing="8" align="right" width="100%">
      <tr align="center">
	   <td class="A0000" height="45" id="InfoBack" >&nbsp;</td>
        <td width="20">&nbsp;</td>
        <td width="80"><input type="button" name="Submit" value="取消" onclick="closeMaskDiv()"></td>
        <td width="20">&nbsp;</td>
        <td width="80"><input type="button" name="Submit" value="提交" onclick="ToUpdatedsgRemark()"></td>
      </tr>
    </table></td>
  </tr>
</table>

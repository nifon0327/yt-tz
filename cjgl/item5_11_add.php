<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$tableWidth=820;
$funFrom="item5_11";
$saveWebPage=$funFrom . "_ajax.php?ActionId=1";

?>
 <table width="<?php    echo $tableWidth?>px" border="0" align="left" cellspacing="5">
		<tr>
            <td width="80" align="right">退料原因:</td>
             <td  width="180" align="left"><input name="StuffId" type="hidden" id="StuffId">
             <select id="tlReson" name="tlReson"  datatype="Require"  msg="未选择退料原因" style="width:120px;"  >
             <option value="" selected>请选择</option>
             <option value="1">不良品</option>
             <option value="2">取消生产</option>
             <option value="3">其它</option>
             </select>
             </td>
            <td width="180">是否返回仓库:<select id="ReturnCkSign" name="ReturnCkSign">
            <option value="" selected>请选择</option>
            <option value="1">是</option>
            <option value="0">否</option>
            </select>
            </td>
			<td align="right">工单流水号</td>
			<td align="left"><input name="sPOrderId" type="text" id="sPOrderId" size="20"></td>
            <td align="left">
            <input name="OrderQuery" type="button" id="OrderQuery" value="查  询" onClick="viewStuffdata(3)">
            </td>
          </tr>
   </table>
     <table border="0" width="<?php    echo $tableWidth?>px" cellpadding="0" cellspacing="0" align="center">
	       <tr bgcolor='#EEEEEE'>
		<td width="10" class="A0010" height="25" bgcolor='#CCC'>&nbsp;</td>
		<td class="A1111" width="50" align="center">操作</td>
		<td class="A1101" width="40" align="center">序号</td>
        <input type='hidden' id='llId' name='llId'/>
		<td class="A1101" width="100" align="center">需求流水号</td>
		<td class="A1101" width="60" align="center">配件ID</td>
		<td class="A1101" width="210" align="center">配件名称</td>
        <td class="A1101" width="60" align="center">已领料数</td>
		<td class="A1101" width="60" align="center">退料数量</td>
		<td class="A1101" width="150" align="center">退料原因</td>
        <select name='operator' id='operator' style='display:none;'></select>
        <input type='hidden' name='thQTY' id='thQTY' />
        <td class="A1101" width="" align="center">退料后数量</td>
        <input type='hidden' name='changeQty' id='changeQty' />
		<td width="10" class="A0000" bgcolor='#CCC'>&nbsp;</td>
	</tr>
	<tr>
		<td width="10" class="A0010" height="432">&nbsp;</td>
		<td colspan="9" align="center" class="A0111">
		<div style="width:800;height:100%;overflow-x:hidden;overflow-y:scroll;background:#FFF;">
			<table width='800' cellpadding="0" cellspacing="0"  id="ListTable">
			<input name="TempValue" type="hidden" id="TempValue">
			</table>
		</div>
		</td>
        <td width="10" class="A0000">&nbsp;</td>
	</tr>
</table>

 <table width="<?php    echo $tableWidth?>" border="0" align="center" cellspacing="5">
 <tr>
    <td>&nbsp;</td>
    <td align="center"><input class='ButtonH_25' type='button'  id='saveBtn' value='保存' onclick='saveQty()'/></td>
    <td align="center"><input class='ButtonH_25' type='button'  id='cancelBtn' value='取消' onclick='closeWinDialog()'/></td>
    <td>&nbsp;</td>
 </tr>
 </table>
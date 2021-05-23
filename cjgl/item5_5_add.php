<?php
//电信-zxq 2012-08-01
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$funFrom="item5_5";
$saveWebPage=$funFrom . "_ajax.php?ActionId=1";
$tableWidth=400;
?>
<iframe name="FormSubmit" id="FormSubmit" width="1" height="1" style="display:none;"></iframe>
<form action="<?php    echo $saveWebPage?>" method="post"  target="FormSubmit" name="saveForm" id="saveForm" onsubmit= "return Validator.Validate(this,3);">
 <table width="<?php    echo $tableWidth?>px" border="0" align="left" cellspacing="5">
 <!--显示表单内容 onsubmit="return Validator.Validate(this,3);"-->
		<tr>
          <td width="60px" height="30" align="right">入库日期</td>
          <td width="260px" align="left"><input name="rkDate" type="text" id="rkDate" size="22" value="<?php    echo date("Y-m-d")?>" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="日期不正确" readonly></td>
          </tr>
			<tr>
            	<td align="right" height="30">入库配件
           	    <input name="StuffId" type="hidden" id="StuffId"></td>
                <td  align="left"><input name="StuffCname" type="text" id="StuffCname" size="22"  datatype="Require"  msg="未选择入库配件"  oninput="SetCname()"  ><input name='CompanyId' type='hidden' id='CompanyId' ><input name='Price' type='hidden' id='Price' ><span class='ButtonH_25' name="stuffQuery"  id="stuffQuery" value="查 询" onClick="viewStuffdata()" disabled>查 询</span>
                </td>
			</tr>
			<tr>
          		<td align="right" height="30">入库库位</td>
            	<td  align="left"><input name='LocationId' type='hidden' id='LocationId' >
<!--	            	<input name="Identifier" type="text" id="Identifier" size="22" dataType="Require"  msg="未输入入库库位" onkeyup="showResult(this.value,'Identifier','ck_location','5')" onblur="LoseFocus()" autocomplete="off">-->
                    <input name="Identifier" type="text" id="Identifier" size="22" dataType="Require"  msg="未输入入库库位" onkeyup="showResult(this.value,'SeatId','wms_seat','5')" onblur="LoseFocus()" autocomplete="off">
            	</td>
          	</tr>

          	<tr>
          		<td align="right" height="30">入库数量</td>
            	<td  align="left"><input name="Qty" type="text" id="Qty" size="22" dataType="Price"  msg="入库数量不正确"></td>
          	</tr>
          	<tr>
            	<td height="25" align="right" valign="top">入库备注</td>
            	<td  align="left"><textarea name="Remark" cols="40" rows="8" id="Remark" dataType="Require"  msg="未输入入库备注"></textarea></td>
          	</tr>
</table>

 <table width="<?php    echo $tableWidth?>" border="0" align="center" cellspacing="5">
 <tr>
    <td>&nbsp;</td>
    <td align="center"><input type='submit' id='submit' value='保存' /></td>
     <td align="center"><span class='ButtonH_25'  id='cancelBtn' value='取消' onclick='closeWinDialog()'>取消</span></td>
    <td>&nbsp;</td>
 </tr>
 </table>
</form>

<?php
//电信-zxq 2012-08-01
session_start();
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$upSql="SELECT B.Id,B.StuffId,B.Qty,B.Remark,B.Date,D.StuffCname ,B.LocationId,L.Identifier AS LocationName 
         FROM $DataIn.ck7_bprk B 
         LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId
         LEFT JOIN $DataIn.ck_location L ON L.Id = B.LocationId
         WHERE  B.Id='$Id'";
$upData =mysql_fetch_array(mysql_query($upSql,$link_id));
$StuffId=$upData["StuffId"];
$StuffCname=$upData["StuffCname"];
$Qty=$upData["Qty"];
$rkDate=$upData["Date"];
$Remark=$upData["Remark"];
$LocationName=$upData["LocationName"];
$LocationId=$upData["LocationId"];

$tableWidth=400;
$funFrom="item5_5";
$updateWebPage=$funFrom . "_ajax.php?ActionId=2&Id=$Id";
$delWebPage=$funFrom . "_ajax.php?ActionId=3&Id=$Id";
?>
<iframe name="FormSubmit" id="FormSubmit" width="1" height="1" style="display:none;"></iframe>
<form action="" method="post"  target="FormSubmit" name="saveForm" id="saveForm" >
 <table width="<?php    echo $tableWidth?>px" border="0" align="left" cellspacing="5">
 <!--显示表单内容 onsubmit="return Validator.Validate(this,3);"-->
		<tr>
          <td width="60px" height="30" align="right">入库日期</td>
          <td width="300px" align="left"><input name="rkDate" type="text" id="rkDate" size="22" value="<?php    echo $rkDate?>" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="日期不正确" readonly></td>
          </tr>
			<tr>
            	<td align="right" height="30">入库配件
           	    <input name="StuffId" type="hidden" id="StuffId" value="<?php    echo $StuffId?>" ></td>
            	<td  align="left"><input name="StuffCname" type="text" id="StuffCname" size="22"  value="<?php    echo $StuffCname?>" datatype="Require"  msg="未选择入库配件"  readonly>
                </td>
			</tr>

			<tr>
          		<td align="right" height="30">入库库位</td>
            	<td  align="left"><input name='LocationId' type='hidden' id='LocationId' value="<?php echo $LocationId?>" >
	            	<input name="Identifier" type="text" id="Identifier" size="22"  value="<?php echo $LocationName?>" dataType="Require"  msg="未输入入库库位" onkeyup="showResult(this.value,'Identifier','ck_location','5')" onblur="LoseFocus()" autocomplete="off">
            	</td>
          	</tr>
          	<tr>
          		<td align="right" height="30">入库数量 <input name="oldQty" type="hidden" id="oldQty" value="<?php    echo $Qty?>"></td>
            	<td  align="left"><input name="Qty" type="text" id="Qty" size="22" value="<?php    echo $Qty?>" dataType="Price"  msg="入库数量不正确"></td>
          	</tr>
          	<tr>
            	<td height="25" align="right" valign="top">入库备注</td>
            	<td  align="left"><textarea name="Remark" cols="40" rows="8" id="Remark" dataType="Require"  msg="未输入入库备注"><?php    echo $Remark?></textarea></td>
          	</tr>
</table>
 <table width="<?php    echo $tableWidth?>" border="0" align="center" cellspacing="5">
 <tr>
    <td>&nbsp;</td>
    <td align="center"><span class='ButtonH_25' id='updateBtn' onclick="document.saveForm.action='<?php    echo $updateWebPage?>';if (Validator.Validate(document.saveForm,3)) document.saveForm.submit();">更新</span></td>
    <td align="center"><input class='ButtonH_25' type='button'  id='delBtn' value='删除' onclick="document.saveForm.action='<?php    echo $delWebPage?>';if(confirm('你确认要删除该记录吗？')) document.saveForm.submit();"/></td>
    <td align="center"><input class='ButtonH_25' type='button'  id='cancelBtn' value='取消' onclick='closeWinDialog()'/></td>
    <td>&nbsp;</td>
 </tr>
 </table>
 </form>
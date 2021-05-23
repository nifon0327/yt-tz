<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$tableWidth=400;
$funFrom="item5_8";
$upDataMain="$DataIn.ck3_bcmain";
$updateWebPage=$funFrom . "_ajax.php?ActionId=20&Mid=$Mid";

$OperatorsSTR="";
$MainResult = mysql_query("SELECT T.BillNumber,T.Date,T.Locks,P.Forshort
FROM $upDataMain T
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=T.CompanyId
WHERE T.Id='$Mid' LIMIT 1",$link_id);
if($MainRow = mysql_fetch_array($MainResult)) {
	$BillNumber=$MainRow["BillNumber"];
	$Forshort=$MainRow["Forshort"];
	$Locks=$MainRow["Locks"];
	$Date=$MainRow["Date"];
	}

?>
<iframe name="FormSubmit" id="FormSubmit" width="1" height="1" style="display:none;"></iframe>
<form action="<?php    echo $updateWebPage?>" method="post"  target="FormSubmit" name="saveForm" id="saveForm"  onsubmit="return Validator.Validate(this,3);">
 <table width="<?php    echo $tableWidth?>px" border="0" align="left" cellspacing="5">
 <!--显示表单内容-->
 <tr><td class="A0011">
        <table width="100%" border="0" align="center" cellspacing="5" id="NoteTable">
			<tr>
				<td width="100" align="right" scope="col">供 应 商</td>
				<td scope="col"><?php    echo $Forshort?></td>
			</tr>
			<tr>
				<td align="right" scope="col">送货单号</td><td scope="col"><input name="BillNumber" type="text" id="BillNumber" value="<?php    echo $BillNumber?>" size="25" datatype="Require"  msg="未填写送货单号" ></td>
			</tr>
			<tr>
				<td align="right">补仓日期</td><td><input name="bcDate" type="text" id="bcDate" value="<?php    echo $Date?>" size="25" onfocus="WdatePicker()" title="必选项" datatype="Date" format="ymd" msg="格式不对" readonly></td>
			</tr>
</table>

 <table width="<?php    echo $tableWidth?>" border="0" align="center" cellspacing="5">
 <tr>
    <td>&nbsp;</td>
    <td align="center"><input type='submit' id='updateBtn' value='更新' /></td>
    <td align="center"><input class='ButtonH_25' type='button'  id='cancelBtn' value='取消' onclick='closeWinDialog()'/></td>
    <td>&nbsp;</td>
 </tr>
 </table>
 </form>
<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$tableWidth=320;
$funFrom="item5_12";
$saveWebPage=$funFrom . "_ajax.php";
?>
<iframe name="FormSubmit" id="FormSubmit" width="1" height="1" style="display:none;"></iframe>
<form action="<?php    echo $saveWebPage?>" method="post"  target="FormSubmit" name="saveForm" id="saveForm" onSubmit = "return FileBrowserDialogue.mySubmit()">
<input type="hidden" name="StuffId"  id="StuffId" value="<?php    echo $StuffId?>">
<input type="hidden" name="Qty"  id="Qty" value="<?php    echo $Qty?>">

<table width="<?php    echo $tableWidth?>" border="0" align="center" cellspacing="5">
<TR><TD colspan="4">&nbsp;</TD></TR>
<tr><td>&nbsp;</td>
    <td align="right">报废数量:</td>
    <td colspan="2"><div style='color:#ff0000;'><?php    echo $Qty?></div></td>
 <tr>
    <td>&nbsp;</td>
    <td align="right">报废原因:</td>
	<?php
	if($dReason=="" || $dReason==0){
	?>
    <td align="center"> <select name="Reason" id="Reason" style="width:220px;" onchange="otherCauseClick(this)"  dataType="Require"  msg="未选择不良原因" >
	                    <option value="" selected>请选择</option>
	                    <option value="客户换包装" >客户换包装</option>
						<option value="一年未下单">一年未下单</option>
						<option value="配件名重复/备品转入">配件名重复/备品转入</option>
						<option value="0">其他</option></select>
				<input name="otherCause" type="hidden" id="otherCause" value=""></td>
	<?php
	    }
	else{
	?>
	<td align="left"><?php    echo $dReason?>
	<input name="Reason" type="hidden" id="Reason" value="<?php    echo $dReason?>">
	<input name="otherCause" type="hidden" id="otherCause" value=""></td>
	<?php
	   }
	?>
    <td>&nbsp;</td>
 </tr>
 <TR><TD colspan="4">&nbsp;</TD></TR>
 </table>

 <table width="<?php    echo $tableWidth?>" border="0" align="center" cellspacing="5">
 <tr>
    <td>&nbsp;</td>
    <td align="center"><span class='ButtonH_25' id='sureBtn' onclick='return confirmSave(<?php    echo $StuffId?>,<?php    echo $Qty?>)'>确定</span></td>
    <td align="center"><input class='ButtonH_25' type='button'  id='cancelBtn' value='取消' onclick='closeWinDialog()'/></td>
    <td>&nbsp;</td>
 </tr>
 </table>
</form>

<?
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$tableWidth=320;
$saveWebPage=$funFrom . "_ajax.php"; 
?>
<iframe name="FormSubmit" id="FormSubmit" width="1" height="1" style="display:none;"></iframe> 
<form action="<?=$saveWebPage?>" method="post"  target="FormSubmit" name="saveForm" id="saveForm" onSubmit = "return FileBrowserDialogue.mySubmit()"> 
<table width="<?=$tableWidth?>" border="0" align="center" cellspacing="5">
<tr><td width="100" align="right">width mt.:</td><td align="left"><input id="widthpcs" name="widthpcs" type="text" size="18"</td></tr>
<tr><td  align="right">length mt.:</td><td align="left"><input id="lengthpcs" name="lengthpcs" type="text" size="18"</td></tr>
<tr><td  align="right">height mt.:</td><td align="left"><input id="heightpcs" name="heightpcs" type="text" size="18"</td></tr>
 </table>
 
 <table width="<?=$tableWidth?>" border="0" align="center" cellspacing="5">
 <tr>
    <td>&nbsp;</td>
    <td align="center"><input type='button' id='sureBtn' value='确定' onclick='savedata(<?php echo $ProductId?>,<?php echo $index?>)'/></td>
    <td align="center"><input type='button' id='cancelBtn' value='取消' onclick='closeWinDialog()'/></td>
    <td>&nbsp;</td>
 </tr>
 </table>
</form>

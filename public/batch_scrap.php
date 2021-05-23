<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$tableWidth = 400;
?>

<table width="<?php echo $tableWidth ?>px" border="0" align="left" cellspacing="5">
  <tr>
    　　设置出库对象：<textarea name="scrap" id="scrap" cols="27" rows="10">

     </textarea>
    </td>
  </tr>
</table>

<table width="<?php echo $tableWidth ?>" border="0" align="center" cellspacing="5">
  <tr>
    <td>&nbsp;</td>
    <td align="center">
      <sapn class='ButtonH_25' id='changeBtn' value='变更' onclick='doBatchScrap()'>设置</sapn>
    </td>
    <td align="center">
      <span class='ButtonH_25' id='cancelBtn' value='取消' onclick='closeWinDialog()'>取消</span></td>
    <td>&nbsp;</td>
  </tr>
</table>

<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");


$tableWidth=400;
if($fromWorkShopPage == 1){

	$upSql="SELECT S.Qty AS scQty,SC.Qty, P.cName
	FROM  $DataIn.sc1_cjtj S 
	INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
	INNER JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
	INNER JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
	WHERE S.Id = $Id AND S.sPOrderId=$sPOrderId ";
}else{

	$upSql="SELECT S.Qty AS scQty, SC.Qty, D.StuffCname AS cName
	FROM  $DataIn.sc1_cjtj S 
	INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
	INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId
	INNER JOIN $DataIn.cg1_semifinished M ON M.StockId = SC.StockId
	INNER JOIN $DataIn.stuffdata D ON D.StuffId = M.mStuffId
	WHERE S.Id = $Id AND  S.sPOrderId=$sPOrderId ";
}


$upResult = mysql_query($upSql,$link_id);
if($upData = mysql_fetch_array($upResult)){
	$Qty=$upData["Qty"];
	$scQty=$upData["scQty"];
	$chineseName=$upData["cName"];
}
?>
<table width="<?php echo $tableWidth?>px" border="0" align="left" cellspacing="5">
   <tr><td height="25" align="right" width="120">工单流水号：</td><td><input name="sPOrderId" type="text" id="sPOrderId" value="<?php   echo $sPOrderId?>" style="border:0;background:none;" readonly><input name="Id" type="hidden" id="Id" value="<?php   echo $Id?>"></td></tr>
   <tr><td height="25" align="right">中文名称：</td><td><?php echo $chineseName?></td></tr>
   <tr><td height="25" align="right">生产数量：</td><td><?php echo $scQty?><input type="hidden" name="scQty" id="scQty"  value="<?php echo $scQty?>"></td></tr>
   <tr><td height="25" align="right">减少数量：</td><td><input type=" text" name="delQty" id="delQty" size="16" value="<?php echo $scQty?>"></td>
   </tr>

</table>

 <table width="<?php    echo $tableWidth?>" border="0" align="center" cellspacing="5">
 <tr>
   <td align="center" width="200"><span class='ButtonH_25' id='changeBtn' onclick='updateQty()'>变更</span></td>
    <td align="center" width="100"><input class='ButtonH_25' type='button'  id='changeBtn' value='删除' onclick='deleteQty()'></td>
    <td align="center" width="100"><input class='ButtonH_25' type='button'  id='cancelBtn' value='取消' onclick='closeWinDialog()'/></td>
    <td>&nbsp;</td>
 </tr>
 </table>
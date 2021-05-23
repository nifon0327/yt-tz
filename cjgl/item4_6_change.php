<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$tableWidth=400;
if($fromWorkShopPage == 1){

	$upSql="SELECT SC.Qty,SC.WorkShopId, P.cName
	FROM  $DataIn.yw1_scsheet SC 
	INNER JOIN $DataIn.yw1_ordersheet S ON S.POrderId = SC.POrderId
	INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
	WHERE SC.POrderId=$POrderId ";
}else{

	$upSql="SELECT SC.Qty,SC.WorkShopId, D.StuffCname AS cName
	FROM  $DataIn.yw1_scsheet SC 
	INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId
	INNER JOIN $DataIn.cg1_semifinished M ON M.StockId = SC.StockId
	INNER JOIN $DataIn.stuffdata D ON D.StuffId = M.mStuffId
	WHERE SC.POrderId=$POrderId ";
}


$upResult = mysql_query($upSql,$link_id);
if($upData = mysql_fetch_array($upResult)){
	$Qty=$upData["Qty"];
	$thisWorkShopId=$upData["WorkShopId"];
	$cName=$upData["cName"];
}
?>
 <table width="<?php echo $tableWidth?>px" border="0" align="left" cellspacing="5">
   <tr><td height="25" align="right">PO流水号：</td><td><input name="sPOrderId" type="text" id="sPOrderId" value="<?php   echo $POrderId?>" style="border:0;background:none;" readonly><input name="Id" type="hidden" id="Id" value="<?php   echo $Id?>"></td></tr>
   <tr><td height="25" align="right">中文名称：</td><td><?php echo $cName?></td></tr>
   <tr><td height="25" align="right">生产数量：</td><td><?php echo $Qty?></td></tr>
   <tr><td height="25" align="right">生产单位：</td><td>
   <select  style="width:150px"  name="changeWorkShopId" id = "changeWorkShopId">
	 <option value="0" selected>--请选择--</option>
      <?php
	 $Result2 = mysql_query("SELECT Id,Name FROM $DataIn.workshopdata WHERE Estate=1 order by Id",$link_id);
	 if($myRow2 = mysql_fetch_array($Result2)){
		do{
		    if($myRow2["Id"] == $thisWorkShopId){
			   echo" <option value='$myRow2[Id]' selected>$myRow2[Name]</option>";
		    }else{
			    echo" <option value='$myRow2[Id]'>$myRow2[Name]</option>";
		    }

		  }while($myRow2 = mysql_fetch_array($Result2));
	 }
   ?>
   </select></td>
   </tr>

</table>

 <table width="<?php    echo $tableWidth?>" border="0" align="center" cellspacing="5">
 <tr>
    <td>&nbsp;</td>
   <td align="center"><span class='ButtonH_25' id='changeBtn' onclick='changeWorkshop()'>变更</span></td>
    <td align="center"><input class='ButtonH_25' type='button'  id='cancelBtn' value='取消' onclick='closeWinDialog()'/></td>
    <td>&nbsp;</td>
 </tr>
 </table>
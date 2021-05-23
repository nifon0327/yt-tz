<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$tableWidth=400;
$funFrom="item5_7";
$updateWebPage=$funFrom . "_ajax.php?ActionId=2&Id=$Id";
$delWebPage=$funFrom . "_ajax.php?ActionId=3&Id=$Id";

$OperatorsSTR="";
$upSql=mysql_query("SELECT M.CompanyId,S.Qty,S.StuffId,D.StuffCname,K.tStockQty,P.Forshort 
FROM $DataIn.ck2_thsheet S
LEFT JOIN $DataIn.ck2_thmain M ON M.Id=S.Mid
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
LEFT JOIN $DataIn.stuffdata D ON S.StuffId=D.StuffId 
LEFT JOIN $DataIn.ck9_stocksheet K ON S.StuffId=K.StuffId 
WHERE S.Id=$Id ORDER BY S.Id DESC",$link_id);
if($upData = mysql_fetch_array($upSql)){
	$CompanyId=$upData["CompanyId"];
	$Forshort=$upData["Forshort"];
	$StuffId=$upData["StuffId"];
	$Qty=$upData["Qty"];
	$StuffCname=$upData["StuffCname"];
	$tStockQty=$upData["tStockQty"];
	//该供应商该配件退换总数
	$check_thSql=mysql_query("SELECT SUM(S.Qty) AS thQty FROM $DataIn.ck2_thsheet S LEFT JOIN $DataIn.ck2_thmain M ON M.Id=S.Mid WHERE M.CompanyId='$CompanyId' AND S.StuffId='$StuffId' ORDER BY StuffId",$link_id);
	$thQty=mysql_result($check_thSql,0,"thQty");
	$thQty=$thQty==""?0:$thQty;
	//补仓情况
	$check_bcSql=mysql_query("SELECT SUM(S.Qty) AS bcQty FROM $DataIn.ck3_bcsheet S LEFT JOIN $DataIn.ck3_bcmain M ON M.Id=S.Mid WHERE M.CompanyId='$CompanyId' AND S.StuffId='$StuffId' ORDER BY StuffId",$link_id);
	$bcQty=mysql_result($check_bcSql,0,"bcQty");
	$bcQty=$bcQty==""?0:$bcQty;
	$MantissaQty=$thQty-$bcQty;    //未补仓数量
	if($tStockQty==0){
		$tStockQtyINFO="<br/><span class='redB'>(没有在库,不可做增加退换数量的操作.)</span>";
		}
	else{
		$OperatorsSTR="<option value='1'>增加</option>";
		}
	if($MantissaQty==0){
		$MantissaQtyINFO="<br/><span class='redB'>(已全部补仓,不可减少退换数量.)</span>";
		}
	else{
		$OperatorsSTR.=" <option value='-1'>减少</option>";
		}
}

?>
<iframe name="FormSubmit" id="FormSubmit" width="1" height="1" style="display:none;"></iframe>
<form action="" method="post"  target="FormSubmit" name="saveForm" id="saveForm"  enctype="multipart/form-data" >
 <table width="<?php    echo $tableWidth?>px" border="0" align="left" cellspacing="5">
 <!--显示表单内容 onsubmit="return Validator.Validate(this,3);"-->
		<tr>
	  <td height="30" align="right">配 件 ID：</td><td><input name="StuffId" type="text" id="StuffId" value="<?php    echo $StuffId?>" style="border:0;background:none;" readonly></td>
  </tr>
	<tr>
	  <td height="30" align="right">配件名称：</td><td><?php    echo $StuffCname?></td>
  </tr>
	<tr>
	  <td height="30" align="right">未补数量：</td><td><input name="MantissaQty" type="text" id="MantissaQty" value="<?php    echo $MantissaQty?>" class="I0000L" readonly><?php    echo $MantissaQtyINFO?></td>
  </tr>
	<tr>
	  <td height="30" align="right">在库：</td><td><input name="tStockQty" type="text" id="tStockQty" value="<?php    echo $tStockQty?>" class="I0000L" readonly><?php    echo $tStockQtyINFO?></td>
  </tr>
	<tr>
	  <td height="30" align="right">本次退换：</td><td><input name="oldQty" type="text" id="oldQty" value="<?php    echo $Qty?>" class="I0000L" readonly></td>
  </tr>

		<tr>
		  <td height="30" align="right">图 &nbsp;&nbsp;&nbsp;片</td>
		  <td ><input name="Picture" type="file" id="Picture" size="52" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" ></td>
	    </tr>

	<tr>
	  <td height="30" align="right">退换数量：</td>
	  <td>
	  <?php
	  if($OperatorsSTR==""){
	  	echo"<div class='redB'>条件不足,不能更新.</div>";
		}
	  else{
	  	echo"<select name='Operators' id='Operators'>$OperatorsSTR</select>&nbsp;<input name='changeQty' type='text' class='INPUT0100' id='changeQty' size='8'>";
      	}
		?>
	  </td>
	 </tr>
</table>

 <table width="<?php    echo $tableWidth?>" border="0" align="center" cellspacing="5">
 <tr>
    <td>&nbsp;</td>
   <td align="center"><span class='ButtonH_25' id='updateBtn' onclick="document.saveForm.action='<?php    echo $updateWebPage?>';if (CheckUpdata()) document.saveForm.submit();">更新</span></td>
    <td align="center"><input class='ButtonH_25' type='button'  id='delBtn' value='删除' onclick="document.saveForm.action='<?php    echo $delWebPage?>';if(confirm('你确认要删除该记录吗？')) document.saveForm.submit();"/></td>
    <td align="center"><input class='ButtonH_25' type='button'  id='cancelBtn' value='取消' onclick='closeWinDialog()'/></td>
    <td>&nbsp;</td>
 </tr>
 </table>
 </form>
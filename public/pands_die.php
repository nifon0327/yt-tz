<?php 
/*include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 模具连接");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_die";	
$toWebPage  =$funFrom."_imageload";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Id=$Id==""?$ProductId:$Id;//重置
//$CheckFormURL="thisPage";
$upResult = mysql_query("SELECT P.ProductId,P.cName AS cName FROM $DataIn.productdata P WHERE P.ProductId='$Id' LIMIT 1",$link_id);
if($upData = mysql_fetch_array($upResult)){
    $ProductId=$upData["ProductId"];
	$cName=$upData["cName"];
	}
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,CompanyId,$CompanyId,ProductType,$ProductType,ProductId,$ProductId,ActionId,161";
$tableWidth=850;$tableMenuS=550;$spaceSide=15;
$SelectCode="($ProductId) $cName";
include "../model/subprogram/add_model_t.php";
?>
<table width="<?php  echo $tableWidth?>" height="326" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
    <tr>
      <td width="100" align="right" valign="top" class='A0010'>指定模具:</td>
     <td class='A0001' width="250"><select name="ListId[]" size="18" id="ListId" multiple style="width: 250px;" ondblclick="SearchRecord('nonbom4','<?php  echo $funFrom?>',2,6)" datatype="autoList" readonly>
	<?php 
	$result=mysql_query("SELECT D.GoodsName ,D.GoodsId FROM $DataIn.cut_die  C 
	                     LEFT JOIN $DataPublic.nonbom4_goodsdata D ON D.GoodsId=C.GoodsId
						 WHERE C.ProductId=$ProductId  ORDER BY C.GoodsId",$link_id);
	while ($errorRow= mysql_fetch_array($result)){
		   $GoodsId=$errorRow["GoodsId"];
		   $GoodsName=$errorRow["GoodsName"];
		  echo"<option value='$GoodsId'>$GoodsId  $GoodsName</option>";
		   }
	?>
      </select></td>
	     
    </tr>
	<tr>
	<td align="right" valign="top" class='A0010'>&nbsp;</td>
	<td class='A0001' align="left" width="250"><input type="button"  value="删除选定行"  onClick="delListRow()"> </td>
	
	</tr>
	 <tr>
      <td height="34" align="right"  class='A0010'>操作提示:</td>
      <td class='A0001' width="250">单击列表框可弹出选择模具</td>
  </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script language="JavaScript">
function delListRow(){
   var cList = document.getElementById("ListId");
   for(var i=0; i<cList.length; i++){
      if(cList.options[i].selected){
       cList.options[i]=null;
	   i=i-1;
	  }
   }
}
</script>
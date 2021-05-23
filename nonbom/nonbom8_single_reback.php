<?php 
//EWEN 2013-02-26 OK
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 非bom配件申领后退回仓库");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_reback";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT A.GoodsId,A.Qty,A.WorkAdd,A.Remark,A.Locks,B.GoodsName,B.BarCode,B.Unit,C.wStockQty,C.mStockQty,C.oStockQty,D.TypeName,A.Date ,W.Name AS WorkName
	FROM $DataIn.nonbom8_outsheet A
	LEFT JOIN $DataPublic.nonbom4_goodsdata B ON B.GoodsId=A.GoodsId
	LEFT JOIN $DataPublic.nonbom5_goodsstock C ON C.GoodsId=A.GoodsId	
    LEFT JOIN $DataPublic.staffworkadd  W  ON W.Id=A.WorkAdd
	LEFT JOIN $DataPublic.nonbom2_subtype D ON D.Id=B.TypeId
	WHERE A.Id='$Id' LIMIT 1",$link_id));
$GoodsId=$upData["GoodsId"];
$GoodsName=$upData["GoodsName"];
$Remark=$upData["Remark"];
$TypeName=$upData["TypeName"];
$Attached=$upData["Attached"];
$Date=$upData["Date"];
$Qty=$upData["Qty"];
$Unit=$upData["Unit"];
$Locks=$upData["Locks"];
$wStockQty=$upData["wStockQty"];
$mStockQty=$upData["mStockQty"];
$oStockQty=$upData["oStockQty"];
$WorkName=$upData["WorkName"];
$CheckFormURL="thisPage";
//步骤4：
$tableWidth=950;$tableMenuS=600;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,GoodsId,$GoodsId,oldQty,$Qty,OperatorSign,$OperatorSign,ActionId,130";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="100%" height="250" border="0" align="center" cellspacing="0" id="NoteTable" >
		<tr>
			<td align="right" valign="middle" scope="col" width="150">非BOM配件名称：</td>
			<td valign="middle" scope="col" class="blueB"><?php echo $GoodsName?><input type="hidden" id="SIdList" name="SIdList"><input  id="PropertySign" name="PropertySign" type="hidden" value="<?php echo $PropertySign?>"></td>
		</tr>
        <tr>
		  <td align="right">类型：</td>
		  <td class="blueB"><?php echo $TypeName?></td>
	    </tr>
		<tr>
		  <td align="right">编号：</td>
		  <td class="blueB"><?php echo $GoodsId?></td>
	    </tr>
		<tr>
		  <td align="right">单位：</td>
		  <td class="blueB"><?php echo $Unit;?></td>
	    </tr>
        <tr>
			<td align="right" valign="middle" scope="col">在库：</td>
			<td valign="middle" scope="col" class="blueB"><?php echo $wStockQty;?></td>
		</tr>
        <tr>
			<td align="right" valign="middle" scope="col">采购库存：</td>
			<td valign="middle" scope="col" class="blueB"><?php echo $oStockQty;?></td>
		</tr>
        <tr>
          <td align="right">最低库存：</td>
          <td class="blueB"><?php echo $mStockQty;?></td>
        </tr>
         <tr>
		  <td  align="right">申领日期：</td>
		  <td class="blueB"><?php echo $Date?></td>
	    </tr>
         <tr >
           <td align="right">使用地点：</td>
           <td class="blueB"><?php echo $WorkName?></td>
         </tr>
        <tr>
			<td align="right" valign="middle" scope="col">申领数量：</td>
			<td valign="middle" scope="col" class="blueB"><?php echo $Qty;?><input  type="hidden" id="Qty" name="Qty" value="<?php echo $Qty ?>"></td>
		</tr>
        <tr>
          <td align="right" valign="top">申领备注：</td>
          <td class="blueB"><?php echo $Remark;?></td>
        </tr>
	  </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function CheckForm(){
 }
</script>
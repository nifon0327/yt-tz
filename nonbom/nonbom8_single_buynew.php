<?php   
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 非BOM个人申购信息");//需处理
//步骤3：
$tableWidth=500;$tableMenuS=380;
$nowWebPage="nonbom8_single_buynew";
$CustomFun="<span onClick='javascript:CheckForm();' $onClickCSS>确定</span> ";//自定义功能
$SaveSTR="NO";
$isBack="N";
$Parameter="Q,Q,GoodsId,$GoodsId";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
$checkSql1=mysql_query("SELECT A.GoodsId,A.Price,A.Unit,B.wStockQty,B.oStockQty,B.mStockQty,B.CompanyId, C.mainType,C.BuyerId,A.GoodsName
					   FROM $DataPublic.nonbom4_goodsdata A
						 LEFT JOIN $DataPublic.nonbom5_goodsstock  B ON B.GoodsId=A.GoodsId
						 LEFT JOIN $DataPublic.nonbom2_subtype C ON C.Id=A.TypeId
						 WHERE A.GoodsId='$GoodsId' LIMIT 1",$link_id);	 
if($checkRow1=mysql_fetch_array($checkSql1)){
	$GoodsName=$checkRow1["GoodsName"];
	$Price=$checkRow1["Price"];
	$Unit=$checkRow1["Unit"];
	$CompanyId=$checkRow1["CompanyId"];
	$mainType=$checkRow1["mainType"];
	$BuyerId=$checkRow1["BuyerId"];
}
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr><td align="right" class='A0010' width="120">配件名称：</td><td class="A0001"><?php echo $GoodsName ?></td></tr>
   <tr><td   align="right" class='A0010' >价&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;格：</td><td class="A0001"><?php echo $Price ?><input  id="Price" name="Price" type="hidden" value="<?php echo $Price?>"></td></tr>
   <tr><td   align="right" class='A0010' >单&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;位：</td><td class="A0001"><?php echo $Unit ?><input  id="Unit" name="Unit" type="hidden" value="<?php echo $Unit?>"><input  id="CompanyId" name="CompanyId" type="hidden" value="<?php echo $CompanyId?>"><input  id="BuyerId" name="BuyerId" type="hidden" value="<?php echo $BuyerId?>"><input  id="mainType" name="mainType" type="hidden" value="<?php echo $mainType?>"></td></tr>
    <tr><td   align="right" class='A0010' >申购数量：</td><td class="A0001"><input  type="text" id="Qty" name="Qty" ></td></tr>

        <tr>
          <td align="right" class='A0010'>申购备注：</td>
          <td class="A0001"><textarea name="Remark" rows="3" id="Remark" style="width: 280px;" ></textarea></td>
        </tr>
</table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function CheckForm(){ 
	var Qty= document.form1.Qty.value; 
	var Remark= document.form1.Remark.value; 
	var CompanyId= document.form1.CompanyId.value; 
	var mainType= document.form1.mainType.value; 
	var BuyerId= document.form1.BuyerId.value; 
	var Price= document.form1.Price.value; 
	if(window.opener){
		if (window.opener.document.getElementById("SafariReturnQty")) {  
			window.opener.document.getElementById("SafariReturnQty").value = Qty+"|"+CompanyId+"|"+mainType+"|"+BuyerId+"|"+Price+"|"+Remark; 
		}
	}
 if(Qty>0 && Remark!=""){
	        window.returnValue=Qty+"|"+CompanyId+"|"+mainType+"|"+BuyerId+"|"+Price+"|"+Remark; 
	        window.close(); 
           }
     else{
             alert("请输入申购数量和申购备注!");
           }
	} 
</script> 
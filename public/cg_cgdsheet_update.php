<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//步骤2：
ChangeWtitle("$SubCompany 更新配件需求单");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData=mysql_fetch_array(mysql_query("SELECT S.StockId,S.AddRemark,S.StuffId,S.BuyerId,S.CompanyId,S.POrderId,S.OrderQty,S.Locks,S.StockQty,S.AddQty,S.FactualQty,S.Price,
D.StuffCname,K.oStockQty,T.mainType,D.Price AS defaultPrice,S.cgSign,L.ThisStd
	FROM $DataIn.cg1_stocksheet S 
	LEFT JOIN $DataIn.stuffdata D ON S.StuffId=D.StuffId 
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
	LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId
    LEFT JOIN $DataIn.bom_loss L on D.StuffEname = L.StuffType
	WHERE S.Id=$Id order by S.Id DESC",$link_id));
$StockId=$upData["StockId"];
$StuffCname=$upData["StuffCname"];
$OrderQty=$upData["OrderQty"];
$StockQty=$upData["StockQty"];
$FactualQty=$upData["FactualQty"];
$AddQty=$upData["AddQty"];
$Price=$upData["Price"];
$BuyerId=$upData["BuyerId"];
$CompanyId=$upData["CompanyId"];
$oStockQty=$upData["oStockQty"];
$POrderId=$upData["POrderId"];
$AddRemark=$upData["AddRemark"];
$mainType=$upData["mainType"];
$defaultPrice=$upData["defaultPrice"];
$cgSign=$upData["cgSign"];

//损耗值取得
$ThisStd=$upData["ThisStd"];
$b=preg_match('/[-+]?[0-9]*\.?[0-9]+/',$ThisStd,$arr);
$std = $arr[0];
if (strpos($ThisStd, "%")) {
    $std = $std / 100;
}
if ($std) {
    $AddQty += $FactualQty * $std;
}

if($cgSign==0){
	$leastQty=$AddQty-$oStockQty;
	$leastQty=$leastQty<0?0:$leastQty;
	 //订单资料
	$orderResult = mysql_query("SELECT S.Qty,S.OrderPO,M.OrderDate,P.cName,C.Forshort,A.Relation 
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN  $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
	LEFT JOIN  $DataIn.productdata P ON P.ProductId=S.ProductId 
	LEFT JOIN  $DataIn.pands A ON A.ProductId=P.ProductId
	LEFT JOIN  $DataIn.stuffdata D ON D.StuffId=A.StuffId
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	WHERE S.POrderId='$POrderId' AND D.TypeId='9040' ORDER BY S.Id DESC",$link_id);
	if($orderRow = mysql_fetch_array($orderResult)) {
		$Forshort=$orderRow["Forshort"];
		$OrderPO=$orderRow["OrderPO"];
		$OrderDate=$orderRow["OrderDate"];
		$cName=$orderRow["cName"];
		$Qty=$orderRow["Qty"];	
		$RelationArray=explode("/",$orderRow["Relation"]);
		$Relation=$RelationArray[1];	
		}
	}
else{
	$leastQty=$FactualQty-$oStockQty;
	$leastQty=$leastQty<0?0:$leastQty;
	}
$spaceSide=100;
$semiSign=$APP_CONFIG['SEMI_MAINTYPE']==$mainType?' readonly ':'';
//步骤4：
$tableWidth=850;$tableMenuS=500;
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
 ?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A1111' align="center" colspan="2" <?php  echo $Fun_bgcolor?>>产品订单资料</td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<?php 
	if($cgSign==1){
		$Action=0;
		$FactualQty="<input name='newFactualQty' type='text' id='newFactualQty' style='width: 150px;color: #009900;' value='$FactualQty' title='原特采数量:$FactualQty  改后的特采数量应不少于:$leastQty'>(可用库存:<span class='greenB'>$oStockQty</span>)";
		echo"
		<tr>
			<td width='$spaceSide' class='A0010' height='20'>&nbsp;</td>
			<td class='A0111' width='100' align='right' $Fun_bgcolor>客户</td>
			<td rowspan='5' class='A0101' align='center'>&nbsp;特采单</td>
			<td width='$spaceSide' class='A0001'>&nbsp;</td>
		</tr>
		<tr>
			<td width='$spaceSide' class='A0010' height='20'>&nbsp;</td>
			<td class='A0111' width='100' align='right' $Fun_bgcolor>订单PO</td><td width='$spaceSide' class='A0001'>&nbsp;</td>
		</tr>
		<tr>
			<td width='$spaceSide' class='A0010' height='20'>&nbsp;</td>
			<td class='A0111' width='100' align='right' $Fun_bgcolor>下单日期</td><td width='$spaceSide' class='A0001'>&nbsp;</td>
		</tr>
		<tr>
			<td width='$spaceSide' class='A0010' height='20'>&nbsp;</td>
			<td class='A0111' width='100' align='right' $Fun_bgcolor>产品名称</td><td width='$spaceSide' class='A0001'>&nbsp;</td>
		</tr>
		<tr>
			<td width='$spaceSide' class='A0010' height='20'>&nbsp;</td>
			<td class='A0111' width='100' align='right' $Fun_bgcolor>订单数量</td><td width='$spaceSide' class='A0001'>&nbsp;</td>
		</tr>
		<tr>
			<td width='$spaceSide' class='A0010' height='20'>&nbsp;</td>
			<td class='A0111' width='100' align='right' $Fun_bgcolor>装箱数量</td><td width='$spaceSide' class='A0001'>&nbsp;</td>
		</tr>
			";
		}
	else{
		$Action=1;
			?>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right" <?php  echo $Fun_bgcolor?>>客户</td><td class="A0101">&nbsp;<?php  echo $Forshort?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right" <?php  echo $Fun_bgcolor?>>订单PO</td><td class="A0101">&nbsp;<?php  echo $OrderPO?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right" <?php  echo $Fun_bgcolor?>>下单日期</td><td class="A0101">&nbsp;<?php  echo $OrderDate?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right" <?php  echo $Fun_bgcolor?>>产品名称</td><td class="A0101">&nbsp;<?php  echo $cName?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right" <?php  echo $Fun_bgcolor?>>订单数量</td><td class="A0101">&nbsp;<?php  echo $Qty?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right" <?php  echo $Fun_bgcolor?>>装箱数量</td><td class="A0101">&nbsp;<?php  echo $Relation?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<?php 
	}
	?>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="5">&nbsp;</td>
		<td align="center" colspan="2">&nbsp;</td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A1111' align="center" colspan="2" <?php  echo $Fun_bgcolor?>><input name="Action" type="hidden" id="Action" value="<?php  echo $Action?>">
		  配件需求单资料<input name="leastQty" type="hidden" id="leastQty" value="<?php  echo $leastQty?>"></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right" <?php  echo $Fun_bgcolor?>>采购流水号</td><td class="A0101">&nbsp;<?php  echo $StockId?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right" <?php  echo $Fun_bgcolor?>>配件名称</td><td class="A0101">&nbsp;<?php  echo $StuffCname?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right" <?php  echo $Fun_bgcolor?>>订单需求数量</td><td class="A0101">&nbsp;<?php  echo $OrderQty?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right" <?php  echo $Fun_bgcolor?>>使用库存数量</td><td class="A0101">&nbsp;<?php  echo $StockQty?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right" <?php  echo $Fun_bgcolor?>>需购数量</td><td class="A0101">&nbsp;<?php  echo $FactualQty?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right" <?php  echo $Fun_bgcolor?>>增购数量</td><td class="A0101">
		<?php  if($Action==0){
  		  echo"&nbsp;$AddQty";
		  }
		else{?>
  		  &nbsp;<input name="newAddQty" type="text" id="newAddQty" style="width: 150px;color: #009900;" value="<?php  echo $AddQty?>" title="原增购数量: <?php  echo $AddQty?>  改变后的增购数量应不少于:<?php  echo $leastQty?>"  <?php echo $semiSign?>>(可用库存:<span class="greenB"><?php  echo $oStockQty?></span>)
		  <?php  }?>
		  </td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right" <?php  echo $Fun_bgcolor?>>单价</td><td class="A0101">
		  &nbsp;<input name="newPrice" type="text" id="newPrice" style="width: 150px;color: #009900;" value="<?php  echo $Price?>" title="原单价: <?php  echo $Price?>">(默认单价:<?php  echo $defaultPrice?>)
		  </td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="25">&nbsp;</td>
		<td class='A0111' width="100" align="right" <?php  echo $Fun_bgcolor?>>采购</td>
		<td class="A0101">&nbsp;<select name="BuyerId" id="BuyerId" style="width: 150px;">
            <?php 
			$buyerSql = mysql_query("SELECT P.Number,P.Name FROM $DataIn.staffmain P WHERE 
			 P.Estate=1 AND P.BranchId IN (" . $APP_CONFIG['STUFF_BUYER_BRANCHID'] .") ORDER BY P.BranchId,P.JobId,P.Number",$link_id);
			while($buyerRow = mysql_fetch_array($buyerSql)){
				$Number=$buyerRow["Number"];
				$Name=$buyerRow["Name"];					
				if($Number==$BuyerId){
					echo "<option value='$Number' selected>$Name</option>";
					}
				else{
					echo "<option value='$Number'>$Name</option>";
					}
				} 
			?>
            </select>
		</td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="25">&nbsp;</td>
		<td class='A0111' width="100" align="right" <?php  echo $Fun_bgcolor?>>供应商</td>
		<td class="A0101">&nbsp;<select name="CompanyId" id="CompanyId" style="width: 150px;">
            <?php 
			//供应商
			$ProviderSql = mysql_query("SELECT CompanyId,Forshort,Letter FROM $DataIn.trade_object WHERE (cSign=$Login_cSign OR cSign=0 ) AND Estate=1 ORDER BY Letter",$link_id);
			while($ProviderRow = mysql_fetch_array($ProviderSql)){
				$ProviderId=$ProviderRow["CompanyId"];
				$Forshort=$ProviderRow["Forshort"];
				$Letter=$ProviderRow["Letter"];
				$Forshort=$Letter.'-'.$Forshort;
				if($ProviderId==$CompanyId){
					echo "<option value='$ProviderId' selected>$Forshort</option>";
					}
				else{
					echo "<option value='$ProviderId'>$Forshort</option>";
					}
				} 
			?>
            </select>
		</td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right" <?php  echo $Fun_bgcolor?>>更新备注</td><td class="A0101">
		  &nbsp;<input name="AddRemark" type="text" id="AddRemark" style="width: 534px;color: #009900;" value="<?php  echo $AddRemark?>"></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
   </table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script  type=text/javascript>
function CheckForm(){
	var Message="";
	var Action=Number(document.form1.Action.value);
	var AddRemark=document.form1.AddRemark.value;
	switch(Action){
		case 0://特采
			//检查特采数量
			var newFactualQty=document.form1.newFactualQty.value;
			if(newFactualQty==""){
				document.form1.newFactualQty.value=0;
				newFactualQty=0;
				}
			//检查数量格式是否正确			
			var Result=fucCheckNUM(newFactualQty,'Price');
			if(Result==0){
				Message="输入了不正确的特采数量:"+newFactualQty+",重新输入!";
				}
			else{
				var leastQty=Number(document.form1.leastQty.value);			
				if(newFactualQty<leastQty){
					Message="特采数量不符合要求！特采数量变动的差值不在可用库存允许的范围!";
					}
				}
		break;
		case 1://常单
			//检查增购数量
			var newAddQty=document.form1.newAddQty.value;
			if(newAddQty==""){
				document.form1.newAddQty.value=0;
				}
			//检查数量格式是否正确
			var Result=fucCheckNUM(newAddQty,'Price');
			if(Result==0){
				Message="输入了不正确的增购数量:"+newAddQty+",重新输入!";
				}
			else{
				var leastQty=Number(document.form1.leastQty.value);			
				if(newAddQty<leastQty){
					Message="增购数量不符合要求！增购数量变动的差值不在可用库存允许的范围!";
					}
				}
		break;
		}
	//检查单价
	var newPrice=document.form1.newPrice.value;
	if(newPrice==""){
		Message="未填写单价!";
		}
	var Result=fucCheckNUM(newPrice,'Price');
	if(Result==0){
		Message="输入不正确的售价:"+newPrice+",重新输入!";
		}
	if(AddRemark==""){
		Message="未填写更新备注!";
		}
	if(Message!=""){
		alert(Message);return false;
		}
	else{	
		document.form1.action="cg_cgdsheet_updated.php?Action="+Action;
		document.form1.submit();
		}
	}
</script>
<?php 
//ewen 2013-03-11 OK
include "../model/modelhead.php";
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//步骤2：
ChangeWtitle("$SubCompany 更新申购记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData=mysql_fetch_array(mysql_query("SELECT 
									  A.Id,A.Mid,A.fromMid,A.GoodsId,A.CompanyId,A.Qty,A.Price,A.AddTaxValue,A.Remark,A.Estate,A.Locks,A.Date,
									  B.Date AS cgDate,B.PurchaseID,B.Remark AS mainRemark,
									  C.GoodsName,C.BarCode,C.Attached,C.Unit,
									  D.wStockQty,D.oStockQty,D.mStockQty,E.Name AS Operator,F.Name AS Name
	FROM $DataIn.nonbom6_cgsheet A
	LEFT JOIN $DataIn.nonbom6_cgmain B ON B.Id=A.Mid
	LEFT JOIN $DataPublic.nonbom4_goodsdata C ON C.GoodsId=A.GoodsId 
	LEFT JOIN $DataPublic.nonbom5_goodsstock D ON D.GoodsId=A.GoodsId
	LEFT JOIN $DataPublic.staffmain E ON E.Number=B.Operator
	LEFT JOIN $DataPublic.staffmain F ON F.Number=A.Operator
	WHERE A.Id='$Id' ORDER BY A.Id DESC",$link_id));
$PurchaseID=$upData["PurchaseID"];
$cgDate=$upData["cgDate"];
$mainRemark=$upData["mainRemark"];
$Operator=$upData["Operator"];//采购
$Name=$upData["Name"];
$GoodsId=$upData["GoodsId"];
$GoodsName=$upData["GoodsName"];
$BarCode=$upData["BarCode"];
$Unit=$upData["Unit"];
$Qty=$upData["Qty"];
$Price=$upData["Price"];
$AddTaxValue=$upData["AddTaxValue"];
$CompanyId=$upData["CompanyId"];
$Remark=$upData["Remark"];

$wStockQty=$upData["wStockQty"];
$oStockQty=$upData["oStockQty"];
$mStockQty=$upData["mStockQty"];

$Locks=$upData["Locks"];
if($Locks==0){
	$SaveSTR="NO";
	$ReadOnly="disabled";
	$LockSTR="<span class='redB'>(记录已锁定，不允许修改)</span>";
	}
	
$fromMid=$upData["fromMid"];
if($fromMid>0){  //表示有关联采购单，则重新
	$checkSql2=mysql_fetch_array(mysql_query("SELECT PurchaseID FROM $DataIn.nonbom6_cgmain WHERE Id='$fromMid' ",$link_id));
	$FromPurchaseID=$checkSql2["PurchaseID"];
}	
//收货情况				
$rkTemp=mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.nonbom7_insheet WHERE cgId='$Id'",$link_id);
//echo "SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.nonbom7_insheet WHERE cgId='$Id'";
$rkQty=mysql_result($rkTemp,0,"Qty");
$rkQty=$rkQty==""?0:$rkQty;
//货款情况
$checkFKRow=mysql_fetch_array(mysql_query("SELECT Estate FROM $DataIn.nonbom12_cwsheet WHERE cgId='$Id' AND GoodsId='$GoodsId' AND Mid>'0'",$link_id));
if($checkFKRow){
	$cwEstate=$checkFKRow["Estate"];
	}
else{
	$cwEstate="";
	}
$PriceReadOnly=$QtyReadOnly="";//数量、价格是否可改：如果已经全部收货，则不能更改数量；如果已经结付货款，则不能修改数量也不能更改单价
if($cwEstate!=""){//有请款记录，不能改数量和单价
	$PriceReadOnly=$QtyReadOnly="readonly";
	$LockSTR="<span class='redB'>(记录已请款，不允许修改单价和数量)</span>";
	}
else{//没有请款记录，均可以修改
	if($Qty==$rkQty){//已全部收货
		$QtyReadOnly="readonly";
		$LockSTR="<span class='redB'>(记录已全部收货，不允许修改申购数量)</span>";
		}
	else{//可减少的数量
		$LeastQty=$rkQty<=$Qty-$oStockQty?$rkQty:$Qty-$oStockQty;
		}
	}

$spaceSide=100;
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$BuyerId,CompanyId,$CompanyId,chooseDate,$chooseDate";
//步骤5：//需处理
 ?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A1111' align="center" colspan="2"  bgcolor="#CCCCCC">主采购单资料</td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td width="100" align="right" bgcolor="#CCCCCC" class='A0111' >采购单号</td><td class="A0101">&nbsp;<?php echo $PurchaseID;?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td width="100" align="right" bgcolor="#CCCCCC" class='A0111' >下单日期</td><td class="A0101">&nbsp;<?php echo $cgDate;?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" bgcolor="#CCCCCC" align="right" >采购备注</td><td class="A0101">&nbsp;<?php echo $mainRemark;?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" bgcolor="#CCCCCC" align="right" >采购</td><td class="A0101">&nbsp;<?php echo $Operator;?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="5">&nbsp;</td>
		<td align="center" colspan="2">&nbsp;</td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
	  <td class='A1111' align="center" colspan="2"  bgcolor="#CCCCCC"><input name="Action" type="hidden" id="Action" value="<?php  echo $Action;?>">
		  非BOM配件申购资料
		    <input name="leastQty" type="hidden" id="leastQty" value="<?php  echo $leastQty;?>">
	    <?php  echo $LockSTR?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right"  bgcolor="#CCCCCC">申购人</td><td class="A0101">&nbsp;<?php  echo $Name;?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
    <tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right"  bgcolor="#CCCCCC">采购流水号</td><td class="A0101">&nbsp;<?php  echo $Id;?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right"  bgcolor="#CCCCCC">非BOM配件编号</td><td class="A0101">&nbsp;<?php  echo $GoodsId;?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
    <tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right"  bgcolor="#CCCCCC">非BOM配件名称</td><td class="A0101">&nbsp;<?php  echo $GoodsName;?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
    <tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right"  bgcolor="#CCCCCC">非BOM配件条码</td><td class="A0101">&nbsp;<?php  echo $BarCode;?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
    <tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right"  bgcolor="#CCCCCC">单位</td><td class="A0101">&nbsp;<?php  echo $Unit;?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
    <tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right"  bgcolor="#CCCCCC">在库</td><td class="A0101">&nbsp;<?php  echo $wStockQty;?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
    <tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right"  bgcolor="#CCCCCC">采购库存</td><td class="A0101">&nbsp;<?php  echo $oStockQty;?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
    <tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right"  bgcolor="#CCCCCC">最低库存</td><td class="A0101">&nbsp;<?php  echo $mStockQty;?></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
    <tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right"  bgcolor="#CCCCCC">已入库数量</td><td class="A0101">&nbsp;<input name="rkQty" type="text" id="rkQty"  style="width: 150px; border:0;" value="<?php  echo $rkQty?>" readonly></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right"  bgcolor="#CCCCCC">申购数量</td><td class="A0101">&nbsp;<input name="newQty" type="text" id="newQty" style="width: 150px;color: #009900;" value="<?php  echo $Qty?>" dataType="Range" min="<?php echo $LeastQty;?>" max="100000"  msg="数量不符合条件" <?php  echo $QtyReadOnly;?>></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right"  bgcolor="#CCCCCC">单价</td><td class="A0101">
		  &nbsp;<input name="newPrice" type="text" id="newPrice" style="width: 150px;color: #009900;" datatype='Currency' value="<?php  echo $Price?>"  <?php  echo $PriceReadOnly?> msg="单价不符合条件" ></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	 <tr>
           <td colspan="2" align="right">增值税率</td>
           <td colspan="6"><select name='AddTaxValue' id='AddTaxValue' style="width: 150px;" dataType='Require' msg='未选择'><option value="" selected>--请选择--</option>
				 <?php 
				 
                $checkResult = mysql_query("SELECT A.Id,A.Name FROM $DataPublic.provider_addtax A WHERE A.Estate=1 ",$link_id);
                while($checkRow = mysql_fetch_array($checkResult)){
                    if($checkRow["Id"] == $AddTaxValue)$selectSTR = "selected";
                    else $selectSTR="";
                    echo"<option value='$checkRow[Id]' $selectSTR>$checkRow[Name]</option>";
                    }
                ?>
             </select>
          </td>
         </tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="25">&nbsp;</td>
		<td class='A0111' width="100" align="right"  bgcolor="#CCCCCC">供应商</td>
		<td class="A0101">&nbsp;<select name="newCompanyId" id="newCompanyId" style="width: 150px;" datatype="Require" msg="未选择">
            <?php 
			//供应商
			$ProviderSql = mysql_query("SELECT CompanyId,Forshort FROM $DataPublic.nonbom3_retailermain WHERE Estate=1 ORDER BY Forshort",$link_id);
			while($ProviderRow = mysql_fetch_array($ProviderSql)){
				$theCompanyId=$ProviderRow["CompanyId"];
				$theForshort=$ProviderRow["Forshort"];
				if($theCompanyId==$CompanyId){
					echo "<option value='$theCompanyId' selected>$theForshort</option>";
					}
				else{
					echo "<option value='$theCompanyId'>$theForshort</option>";
					}
				} 
			?>
            </select>
		</td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right"  bgcolor="#CCCCCC">更新备注</td><td class="A0101">
		  &nbsp;<textarea name="Remark" rows="3" id="Remark" style="width: 400px;color: #009900;" datatype="Require" msg="未填写"><?php  echo $Remark;?></textarea></td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>

    <tr>
		<td width="<?php  echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' width="100" align="right"  bgcolor="#CCCCCC">运费关联单号</td><td class="A0101">&nbsp;
           <input name="PurchaseID" type="text" id="PurchaseID" style="width: 350px;" onClick='ViewShipId()'  value="<?php echo $FromPurchaseID;?>" readonly="readonly" />
          <input name="Button" type="button" value="清除关联" onClick="ClearRelation()"/>
          <input name="fromMid" id="fromMid"  type="hidden" value="<?php echo $fromMid;?> "  />        
        </td>
		<td width="<?php  echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
    
    
   </table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>

<script>
function ClearRelation(){
		document.getElementById("PurchaseID").value="";
		document.getElementById("fromMid").value=0;	
}

function ViewShipId(){
	var r=Math.random();  
	var CompanyIdTemp=document.getElementById("CompanyId").value; 
	if(CompanyIdTemp!=""){
		var BackData=window.showModalDialog("nonbom5_s1.php?r="+r+"&tSearchPage=nonbom5&fSearchPage=nonbom5&SearchNum=1&CompanyId="+CompanyIdTemp+"&Action=12","BackData","dialogHeight =500px;dialogWidth=1200px;center=yes;scroll=yes");
		if(BackData){
			var CL=BackData.split("^^");
			document.getElementById("PurchaseID").value=CL[1];
			document.getElementById("fromMid").value=CL[2];
			//alert (CL[2]);
			}
		}
	else{
		alert("请先选择供应商!");
		}
}
</script>	
<?php 
//EWEN 2013-02-18 OK
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新非bom申购记录");//需处理
if($fromWebPage!="nonbom5_m"){
	$fromWebPage=$funFrom."_read";		
}
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT A.fromMid,A.GoodsId,B.GoodsName,A.Remark,B.BarCode,
A.Qty,A.Price,D.Currency,B.Unit,A.CompanyId,C.mStockQty,C.oStockQty,A.Date,A.BuyerId,A.AddTaxValue
	FROM $DataIn.nonbom6_cgsheet A
	LEFT JOIN $DataIn.nonbom4_goodsdata B ON B.GoodsId=A.GoodsId
	LEFT JOIN $DataIn.nonbom5_goodsstock C ON C.GoodsId=A.GoodsId	
	LEFT JOIN $DataIn.nonbom3_retailermain D ON D.CompanyId=A.CompanyId
	WHERE A.Id='$Id' LIMIT 1",$link_id));

$GoodsId=$upData["GoodsId"];
$GoodsName=$upData["GoodsName"];
$Remark=$upData["Remark"];
$TypeId=$upData["TypeId"];
$Attached=$upData["Attached"];
$Qty=$upData["Qty"];
$Price=$upData["Price"];
$Currency=$upData["Currency"];
$Unit=$upData["Unit"];
$sgDate=$upData["Date"];
$BarCode=$upData["BarCode"];
$CompanyId=$upData["CompanyId"];
$mStockQty=$upData["mStockQty"];
$oStockQty=$upData["oStockQty"];
$BuyerId=$upData["BuyerId"];
$thisAddTaxValue=$upData["AddTaxValue"];
//读取申购未审核数量
$checkSql2=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.nonbom6_cgsheet WHERE GoodsId='$GoodsId' AND Estate<>1",$link_id));
$checkQty=$checkSql2["Qty"];

$fromMid=$upData["fromMid"];
if($fromMid>0){  //表示有关联采购单，则重新
	$checkSql2=mysql_fetch_array(mysql_query("SELECT PurchaseID FROM $DataIn.nonbom6_cgmain WHERE Id='$fromMid' ",$link_id));
	$PurchaseID=$checkSql2["PurchaseID"];
}

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,singel,$singel";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="100%" border="0" align="center" cellspacing="0" id="NoteTable" >
		<tr>
			<td align="right" valign="middle" scope="col">非BOM配件名称</td>
			<td valign="middle" scope="col" class="yellowN"><?php echo $GoodsName;?></td>
		</tr>
			<tr>
		  <td align="right">日期</td>
		  <td class="yellowN"><input name="sgDate" type="text" id="sgDate" style="width: 380px;"    onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"  datatype='Require' value="<?php echo $sgDate;?>" msg="没有填写或格式错误" readonly="readonly"/></td>
	    </tr>
		<tr>
		  <td align="right">编号</td>
		  <td class="yellowN"><?php echo $GoodsId?></td>
	    </tr>
		<tr>
		  <td align="right">条码</td>
		  <td class="yellowN"><?php echo $BarCode?></td>
	    </tr>
		<tr>
		  <td align="right">单位</td>
		  <td class="yellowN"><?php echo $Unit;?></td>
	    </tr>
		<tr>
		  <td align="right">申购中未核数量</td>
		  <td class="yellowN"><?php echo $checkQty;?></td>
	    </tr>
        <tr>
			<td align="right" valign="middle" scope="col">采购库存(含已核)</td>
			<td valign="middle" scope="col" class="yellowN"><?php echo $oStockQty;?></td>
		</tr>
        <tr valign="top">
          <td align="right">最低库存</td>
          <td class="yellowN"><?php echo $mStockQty;?></td>
        </tr>
        <tr>
			<td align="right" valign="middle" scope="col" height="30">本次申购数量</td>
			<td valign="middle" scope="col"><input name="Qty" type="text" id="Qty" style="width: 380px;" value="<?php echo $Qty;?>" dataType="Currency" msg="格式不符或不符合条件"/></td>
		</tr>
        <tr>
		  <td align="right" valign="middle" scope="col" height="30">单价</td>
		  <td valign="middle" scope="col"><input name="Price" type="text" id="Price" style="width: 380px;"  title="必填项,输入正整数" datatype='Currency' value="<?php echo $Price;?>" msg="没有填写或格式错误" /></td>
	    </tr>
	    
    <tr>
           <td align="right">增值税率</td>
           <td valign="middle" scope="col"><select name='AddTaxValue' id='AddTaxValue' style='width:380px' dataType='Require' msg='未选择'><option value="" selected>--请选择--</option>
				 <?php 
				 
                $checkResult = mysql_query("SELECT A.Id,A.Name 
                FROM $DataPublic.provider_addtax A WHERE A.Estate=1 ",$link_id);
                while($checkRow = mysql_fetch_array($checkResult)){
                    if($checkRow["Id"] == $thisAddTaxValue)$selectSTR = "selected";
                    else $selectSTR="";
                    echo"<option value='$checkRow[Id]' $selectSTR>$checkRow[Name]</option>";
                    }
                ?>
             </select>
          </td>
         </tr>
         
        <tr>
		  <td align="right" valign="middle" scope="col" height="30">货币</td>
		  <td valign="middle" scope="col">
        <select name='Currency' id='Currency' style='width:380px' dataType='Require' msg='未选择'><option value="" selected>--请选择--</option>
				 <?php 
                $checkResult = mysql_query("SELECT A.Id,A.Name FROM $DataPublic.currencydata A WHERE A.Estate=1 ORDER BY A.Id",$link_id);
                while($checkRow = mysql_fetch_array($checkResult)){
                    $TempId=$checkRow["Id"];
                    $TempName=$checkRow["Name"];
					if( $TempId==$Currency){
						echo"<option value='$TempId' selected>$TempName</option>";
						}
					else{
                    	echo"<option value='$TempId'>$TempName</option>";
						}
                    }
                ?>
             </select>
             </td>
	    </tr>
        <tr>
           <td align="right" height="30">供应商</td>
           <td><select name='CompanyId' id='CompanyId' style='width:380px' dataType='Require' msg='未选择'>
				 <?php 
                $checkResult = mysql_query("SELECT A.Letter,A.CompanyId,A.Forshort FROM $DataPublic.nonbom3_retailermain A WHERE A.Estate=1 ORDER BY A.Letter,A.Forshort",$link_id);
                while($checkRow = mysql_fetch_array($checkResult)){
                    $TempCompanyId=$checkRow["CompanyId"];
                    $TempForshort=$checkRow["Letter"]."-".$checkRow["Forshort"];
					if($TempCompanyId==$CompanyId){
						echo"<option value='$TempCompanyId' selected>$TempForshort</option>";
						}
					else{
						echo"<option value='$TempCompanyId'>$TempForshort</option>";
						}
                    }
                ?>
             </select>
          </td>
         </tr>

        <tr>
           <td align="right" height="30" valign="top">采购</td>
           <td><select name='BuyerId' id='BuyerId' style='width:380px' dataType='Require' msg='未选择'>
				 <?php 
                $checkResult = mysql_query("SELECT A.BuyerId,M.Name FROM $DataPublic.nonbom3_buyer A 
               LEFT JOIN $DataPublic.staffmain M ON M.Number=A.BuyerId
                WHERE A.Estate=1 ",$link_id);
                while($checkRow = mysql_fetch_array($checkResult)){
                    $TempBuyerId=$checkRow["BuyerId"];
                    $TempName=$checkRow["Name"];
					if($TempBuyerId==$BuyerId){
						echo"<option value='$TempBuyerId' selected>$TempName</option>";
						}
					else{
						echo"<option value='$TempBuyerId'>$TempName</option>";
						}
                    }
                ?>
             </select>
          </td>
         </tr>

        <tr>
          <td align="right" valign="top">申购备注</td>
          <td><textarea name="Remark" rows="3" id="Remark" style="width: 380px;" dataType='Require' msg='未填写'><?php echo $Remark;?></textarea></td>
        </tr>
        
        <tr>
          <td align="right" valign="top">运费关联单号</td>
          <td>
          <input name="PurchaseID" type="text" id="PurchaseID" style="width: 300px;" onClick='ViewShipId()'  value="<?php echo $PurchaseID;?>" readonly="readonly" />
          <input name="Button" type="button" value="清除关联" onClick="ClearRelation()"/>
          <input name="fromMid" id="fromMid"  type="hidden" value="<?php echo $fromMid;?>" />          
          </td>
        </tr>        
        
	</table>
</td></tr></table>
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
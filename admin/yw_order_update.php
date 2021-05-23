<?php   
//电信-zxq 2012-08-01

include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新客户订单");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upResult = mysql_query("SELECT 
S.OrderPO,M.OrderDate,M.ClientOrder,M.Id as Mid,M.OrderNumber,
S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.ShipType,S.DeliveryDate,
P.cName,P.eCode,C.Forshort, M.CompanyId
FROM $DataIn.yw1_ordersheet S 
LEFT JOIN $DataIn.yw1_ordermain M on M.OrderNumber=S.OrderNumber 
LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
where S.Id=$Id ORDER BY S.OrderNumber DESC",$link_id);

if($upData = mysql_fetch_array($upResult)){
	$Mid=$upData["Mid"];
	$Forshort=$upData["Forshort"];
	$OrderPO=$upData["OrderPO"];
	$OrderNumber=$upData["OrderNumber"];
	$OrderDate=$upData["OrderDate"];	
	$ClientOrder=$upData["ClientOrder"];
	$ProductId=$upData["ProductId"];
	$POrderId=$upData["POrderId"];

  //获取itf和lotto码--对应companyId = 100024/1004/1059
  $hasProductParameterSql = "Select * From $DataIn.printparameters Where POrderId = '$POrderId' and Estate = 1 Order by Id Limit 1";
  $hasProductParameterResult = mysql_query($hasProductParameterSql);
  $hasProductParameterRow = mysql_fetch_assoc($hasProductParameterResult);
  if($hasProductParameterRow){
      $lotto = $hasProductParameterRow["Lotto"];
      $itf = $hasProductParameterRow["itf"];
  }

  if($lotto != ''){
      $lottoColor = "class='redB'";
  }
  else{
      if($upCompanyId == '100024'){
          $lotto = "ART01";
      }
      else if($CompanyId == '2668'){
          $lotto = "LOP01";
      }
      else{
          $lotto = "ASH01";
      }
  }

	$cName=$upData["cName"];
	$eCode=$upData["eCode"]==""?"&nbsp;":$upData["eCode"];
	$Qty=$upData["Qty"];
	$Price=$upData["Price"];
	$Amount=sprintf("%.2f",$Qty*$Price);
	$PackRemark=$upData["PackRemark"];
	$ShipType=$upData["ShipType"];
	$DeliveryDate=$upData["DeliveryDate"]=="0000-00-00"?"":$upData["DeliveryDate"];
  $CompanyId = $upData["CompanyId"];
	}


$cgSign=0;

//步骤4：
$tableWidth=950;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,Mid,$Mid,OrderNumber,$OrderNumber,ActionId,$ActionId,POrderId,$POrderId,OldQty,$Qty";
//步骤5：//需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td width="10" class="A0010" bgcolor="#FFFFFF" height="30">&nbsp;</td>
    <td colspan="6" class="A0100" valign="bottom">◆主订单信息</td>
    <td width="10" class="A0001" bgcolor="#FFFFFF" height="20"><input name="cgSign" type="hidden" id="cgSign" value="<?php    echo $cgSign?>"></td>
  </tr>
  <tr>
    <td width="10" height="20" class="A0010">&nbsp;</td>
    <td width="145" align="center" class="A0111" bgcolor='<?php    echo $Title_bgcolor?>'>客&nbsp;&nbsp;&nbsp;&nbsp;户</td>
    <td width="145" align="center" class="A0100"><?php    echo $Forshort?></td>
    <td width="145" align="center" class="A0111" bgcolor='<?php    echo $Title_bgcolor?>'>订&nbsp;单&nbsp;PO</td>
    <td width="145" align="center" class="A0100"><input name="OrderPO" type="text" class="INPUT0000" id="OrderPO" value="<?php    echo $OrderPO?>"></td>
    <td width="145" align="center" class="A0111" bgcolor='<?php    echo $Title_bgcolor?>'>订单日期</td>
    <td width="145" align="right" class="A0101"><input name="OrderDate" type="text" class="INPUT0000" id="OrderDate" value="<?php    echo $OrderDate?>" maxlength="10" onfocus="WdatePicker()" readonly></td>
    <td width="10" class="A0001">&nbsp;</td>
  <tr>
	  <td width="10" height="20" class="A0010">&nbsp;</td>
	  <td width="145" align="center" class="A0111" bgcolor='<?php    echo $Title_bgcolor?>'>客户下单资料</td>
  	  <td colspan="5" class="A0101"><input name="ClientOrder" type="file" id="ClientOrder" size="90" title="可选项,可上传rar、zip、xls、doc、pdf、eml、jpg、titf格式" DataType="Filter" Accept="rar,zip,xls,doc,pdf,eml,jpg,titf" Msg="文件格式不对" Row="2" Cel="2"></td>
  	  <td width="10" class="A0001">&nbsp;</td>
	</tr>
     <?php   
	if($ClientOrder!=""){
		echo"
		<tr>
		<td width='10' height='25' class='A0010'>&nbsp;</td>
		<td valign='bottom' class='A0111'>&nbsp;</td>
		<td colspan='5' valign='bottom' class='A0101'><input type='checkbox' name='delFile' id='delFile' value='$ClientOrder'><LABEL for='delFile'>删除已上传客户下单资料</LABEL></td>
		<td width='10' class='A0001'>&nbsp;</td>
		</tr>";
		}
	  	?>
	<tr>
		<td width="10" height="25" class="A0010">&nbsp;</td>
		<td colspan="6" valign="bottom">◆产品订单明细	    </td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
    <td width="10" height="35" class="A0010">&nbsp;</td>
    <td colspan="6" valign="bottom">◆产品订单信息</td>
    <td width="10" class="A0001">&nbsp;</td>
  </tr>
</table>

<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr bgcolor='<?php    echo $Title_bgcolor?>'>
    <td width="10" class="A0010" bgcolor="#FFFFFF" height="20">&nbsp;</td>
    <td class="A1111" width="90" align="center">订单流水号</td>
    <td class="A1101" width="60" align="center">产品ID</td>
    <td class="A1101" width="250" align="center">产品名称</td>
    <td class="A1101" width="250" align="center">Product Code</td>
    <td class="A1101" width="100" align="center">订购数量</td>
    <td class="A1101" width="70" align="center">售价</td>
    <td class="A1101" width="100" align="center">小计<input name="TempValue" type="hidden" value="0"></td>
    <td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  <tr>
    <td class="A0010" bgcolor="#FFFFFF" height="20">&nbsp;</td>
    <td align="center" class="A0111"><?php    echo $POrderId?></td>
    <td class="A0101" align="center"><?php    echo $ProductId?></td>
    <td class="A0101" align="center"><?php    echo $cName?></td>
    <td class="A0101" align="center" ><?php    echo $eCode?></td>
    <td class="A0101" align="center"> <input name="defaultQty" type="hidden" id="defaultQty" size="10" value="<?php    echo $Qty?>" >
      <input name="Qty" type="text" id="Qty" size="10" value="<?php    echo $Qty?>"  class="numINPUTout" onChange="ChangeThis('Qty',0)" onFocus="toTempValue(this.value)" > <!-- readonly -->
    </td>
    <td class="A0101" align="center">
      <input name="Price" type="text" id="Price" size="6" value="<?php    echo $Price?>" class="numINPUTout" onChange="ChangeThis('Price',1)" onFocus="toTempValue(this.value)"><input name="OldPrice" type="hidden" id="OldPrice" size="6" value="<?php    echo $Price?>"
    </td>
    <td class="A0101" align="center">
      <input name="Amount" type="text" id="Amount" size="12" value="<?php    echo $Amount?>" class="totalINPUT" readonly>
    </td>
    <td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  
  
   <?php 
  $checkIdSql="SELECT Id FROM $DataIn.yw1_orderupdate WHERE POrderId=$POrderId ORDER BY CREATED DESC LIMIT 1";
  $checkIdResult = mysql_query($checkIdSql,$link_id);
  $checkIdRow = mysql_fetch_array($checkIdResult);
  $Id = $checkIdRow["Id"];
  if($Id > 0) {
  ?>
  <tr>
    <td class="A0010" bgcolor="#FFFFFF" height="35">&nbsp;</td>
    <td colspan="7" class="A0100" valign="bottom">◆订单历史更新信息</td>
    <td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  <tr>
  <td width="10" class="A0010" bgcolor="#FFFFFF" height="20">&nbsp;</td>
    <td class="A0111" align="center">修改日期</td>
    <td class="A0101" align="center">修改前数量</td>
    <td class="A0101" align="center">修改后数量</td>
    <td class="A0101" align="center">修改前价格</td>
    <td class="A0101" align="center">修改后价格</td>
	<td class="A0101" align="center">操作员</td>
	<td class="A0101" align="center">审核状态</td>
	<td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  <tr>
  <?php
    $mySql="SELECT U.Date,U.OldQty, U.NewQty,U.OldPrice,U.NewPrice,U.Remark,U.Estate,U.Operator FROM $DataIn.yw1_orderupdate U WHERE U.POrderId=$POrderId ORDER BY CREATED DESC";
	//echo $mySql;
	$myResult = mysql_query($mySql,$link_id);
	while ($myRow = mysql_fetch_array($myResult)){
		
		$Date=$myRow["Date"];
		$OldQty=$myRow["OldQty"];
		$NewQty=$myRow["NewQty"];
		$OldPrice=sprintf("%.4f",$myRow["OldPrice"]);
		$NewPrice=sprintf("%.4f",$myRow["NewPrice"]);
		$Remark=$myRow["Remark"];
		$Estate=$myRow["Estate"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$NewQty = $NewQty==$OldQty?$NewQty:"<span class='redB'>$NewQty</span>";
		$NewPrice = "<span class='redB'>$NewPrice</span>";
		$Status = "";
		if($Estate==1)
		{
			$Status = "等待审核";
		}
		if($Estate==2)
		{
			$Status = "<span class='redB'>记录退回</span>";
		}
		if($Estate==0)
		{
			$Status = "<span class='greenB'>审核通过</span>";
		}
  ?>
  <tr>
    <td class="A0010" bgcolor="#FFFFFF" height="20">&nbsp;</td>
    <td align="center" class="A0111"><?php echo $Date?></td>
    <td class="A0101" align="center"><?php echo $OldQty?></td>
    <td class="A0101" align="center"><?php echo $NewQty?></td>
	<td class="A0101" align="center"><?php echo $OldPrice?></td>
	<td class="A0101" align="center"><?php echo $NewPrice?></td>
	<td class="A0101" align="center"><span title="<?php echo $Remark?>"><?php echo $Operator?></span></td>
	<td class="A0101" align="center"><?php echo $Status?></td>
	<td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
   <tr>
  <?php 
        } 
     } 
  ?>

  <tr>
    <td class="A0010" bgcolor="#FFFFFF" height="35">&nbsp;</td>
    <td colspan="7" class="A0100" valign="bottom">◆附加信息</td>
    <td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  
    <tr>
    <td class="A0010" bgcolor="#FFFFFF" height="20">&nbsp;</td>
    <td align="center" class="A0111" bgcolor='<?php    echo $Title_bgcolor?>'>更新备注</td>
    <td colspan="6" align="left" class="A0101"><textarea id="updateRemark" name="updateRemark" rows="3" cols="80"></textarea></td>
    <td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  
 <!-- <tr>
   <tr>
    <td class="A0010" bgcolor="#FFFFFF" height="20">&nbsp;</td>
    <td align="center" class="A0111" bgcolor='<?php    echo $Title_bgcolor?>'>包装说明</td>
    <td colspan="6" align="left" class="A0101"><textarea id="PackRemark" name="PackRemark" rows="3" cols="80"><?php    echo $PackRemark?></textarea></td>
    <td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
    <input name="PackRemark" type="text" class="INPUT0000" id="PackRemark" value="<?php    echo $PackRemark?>" size="110">
    <td class="A0010" bgcolor="#FFFFFF" height="20">&nbsp;</td>
    <td align="center" class="A0111" bgcolor='<?php    echo $Title_bgcolor?>'>出货方式</td>
    <td colspan="6" align="center" class="A0101"><input name="ShipType" type="text" class="INPUT0000" id="ShipType" value="<?php    echo $ShipType?>" size="110"></td>
    <td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  <tr>
    <td class="A0010" bgcolor="#FFFFFF" height="20">&nbsp;</td>
    <td align="center" class="A0111" bgcolor='<?php    echo $Title_bgcolor?>'>出货时间</td>
    <td colspan="6" align="center" class="A0101"> <input name="DeliveryDate" type="text" class="INPUT0000" id="DeliveryDate" value="<?php    echo $DeliveryDate?>" size="110" onfocus="WdatePicker()" readonly></td>
    <td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>-->

  <?php

    if($CompanyId == "1004" || $CompanyId == "1059" || $upCompanyId == '100024' || $upCompanyId == '2668')
    {

        echo "<tr>
                <td class='A0010' bgcolor='#FFFFFF' height='20'>&nbsp;</td>
                <td align='center' class='A0111'>Lotto</td>
                <td colspan='6' align='center' class='A0101'> <input name='lotto' type='text' class='INPUT0000' id='lotto' value = '$lotto' size='110'></td>
                <td class='A0001' bgcolor='#FFFFFF'>&nbsp;</td>
             </tr>";

        echo "<tr>
                <td class='A0010' bgcolor='#FFFFFF' height='20'>&nbsp;</td>
                <td align='center' class='A0111'>itf 14</td>
                <td colspan='6' align='center' class='A0101'> <input name='itf' type='text' class='INPUT0000' id='itf' value = '$itf' size='110'></td>
                <td class='A0001' bgcolor='#FFFFFF'>&nbsp;</td>
             </tr>";

    }

  ?>

  <tr>
    <td class="A0010" bgcolor="#FFFFFF" height="60">&nbsp;</td>
    <td colspan="7"><p>注意：<br>
      1、更新主单信息时，同一批下单的产品订单均使用相同的PO和下单日期<br>
      2、更新产品订单的数量时，将影响
配件需求单    </p>
    </td>
    <td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
</table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script language = "JavaScript"> 
function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}
function ChangeThis(Keywords,KeyFields){
    var  cgSign = document.getElementById("cgSign").value;
    var  defaultQty = document.getElementById("defaultQty").value;
	var oldValue=document.form1.TempValue.value;//改变前的值
	var Qtytemp=document.form1.Qty.value;//改变后的值
	var Pricetemp=document.form1.Price.value;//改变后的值

     if(cgSign==1 && KeyFields==0 && Qtytemp<defaultQty){
               alert("该订单有配件已下采购单，不能做减少订单数量的动作!");
               document.form1.Qty.value = defaultQty;
              return false;
         }
	if(Keywords=="Qty"){		
		//检查是否数字格式
		var Result=fucCheckNUM(Qtytemp,'');
		if(Result==0 || Qtytemp==0){
			alert("输入了不正确的数量:"+Qtytemp+",重新输入!");
			document.form1.Qty.value=oldValue;
			}
		else{
			document.form1.Amount.value=FormatNumber(Qtytemp*Pricetemp,2);//改变数量所增加或减少的值
			}
		}
	else{
		//检查是否价格格式
		var Result=fucCheckNUM(Pricetemp,'Price');
		if(Result==0){
			alert("输入不正确的售价:"+Pricetemp+",重新输入!");
			document.form1.Price.value=oldValue;
			}
		else{
			document.form1.Price.value=FormatNumber(Pricetemp,3);
			document.form1.Amount.value=FormatNumber(Pricetemp*Qtytemp,2);		
			}
		}
	}
</script>
<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 生成扣款单");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_creditnote";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT M.Id,M.Number,M.InvoiceNO,M.ShipType,C.Forshort,C.CompanyId
	FROM $DataIn.ch1_shipmain M,$DataIn.trade_object C WHERE M.CompanyId=C.CompanyId AND M.Id='$Id' LIMIT 1",$link_id));
$Forshort=$upData["Forshort"];
$InvoiceNO=$upData["InvoiceNO"];
$CompanyId=$upData["CompanyId"];
$ShipType=$upData["ShipType"];
if ($ShipType=='replen'){
	echo "<script language='JavaScript'>alert('出货流水号：" . $Id . "的订单类型为补货单,不能生成扣款！');location.href='javascript:history.back()';</script>";
}
//读取该客户的最后一个CREDITNOTE名称
$maxInvoiceNO=mysql_query("SELECT InvoiceNO FROM $DataIn.ch1_shipmain WHERE Sign='-1' AND CompanyId='$CompanyId' ORDER BY Date DESC,InvoiceNO LIMIT 1",$link_id);
if($maxRow=mysql_fetch_array($maxInvoiceNO)){
	$maxNO=$maxRow["InvoiceNO"];
	$formatArray=explode(" ",$maxNO);
	$maxNum=trim(preg_replace("/([^0-9]+)/i","",$formatArray[3]))+1;//提取编号
	$NewInvoiceNO=$Forshort." credit note ".$maxNum;
	}
else{
	$NewInvoiceNO=$Forshort." credit note 001";
	}
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ActionId,59";
//步骤5：//需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="826" border="0" align="center" cellspacing="5">
		<tr>
			<td align="right" scope="col">被扣款Invoice：</td>
            <td scope="col"><?php    echo $InvoiceNO?><input name="Wise" type="hidden" id="Wise" value="<?php    echo $InvoiceNO?>"></td>
		</tr>
		<tr>
		  <td align="right" scope="col">扣款日期：</td>
	    <td scope="col"><input name="Date" type="text" id="Date" size="43" value="<?php    echo date("Y-m-d")?>" onfocus="WdatePicker()" readonly></td>
		</tr>
		<tr>
            <td width="124" align="right" scope="col">退货款给客户：</td>
            <td width="683" scope="col"><select name="CompanyId" id="CompanyId" style="width: 250px;" onchange="ReadInfo()">
			<?php   
			$result = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE cSign=$Login_cSign AND Estate=1 order by Id",$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
					if($CompanyId==$myrow["CompanyId"]){
						echo"<option value='$myrow[CompanyId]' selected>$myrow[Forshort]</option>";
						}
					else{
					echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
					}
					} while ($myrow = mysql_fetch_array($result));
				}
			  ?>			  
            </select>
			</td>
		</tr>
        <tr>
			<td align="right">CreditNote文档名：</td>
            <td><input name="InvoiceNO" type="text" id="InvoiceNO" size="43" value="<?php    echo $NewInvoiceNO?>" dataType="Require"  msg="未填写"></td>
		</tr>
        <tr>
          <td align="right">文档模板：</td>
          <td><select name='ModelId' id='ModelId' style='width:250px' dataType='Require' msg='未选择'><option value='' selected>请选择</option><?php   
		  	$checkBank=mysql_query("SELECT Id,Title FROM $DataIn.ch8_shipmodel WHERE 1 AND CompanyId='$CompanyId' ORDER BY Id",$link_id);
		  	if($BankRow=mysql_fetch_array($checkBank)){
				do{
					$FileId=$BankRow["Id"];
					$Title=$BankRow["Title"];
					echo"<option value='$FileId'>$Title</option>";
					}while($BankRow=mysql_fetch_array($checkBank));
				}
		  ?></select></td>
        </tr>
		<?php   
		/*
        <tr>
          <td align="right">收款帐号：</td>
        <td>
		  $checkBank=mysql_query("SELECT Id,Title FROM $DataPublic.my2_bankinfo ORDER BY Id",$link_id);
		  if($BankRow=mysql_fetch_array($checkBank)){
		  	echo"<select name='BankId' id='BankId' style='width:250px' dataType='Require' msg='未选择'>";
			echo"<option value=''>请选择</option>";
			$i=1;
			do{
				$BankId=$BankRow["Id"];
				$Title=$BankRow["Title"];
				echo"<option value='$BankId'>$Title</option>";
				$i++;
				}while($BankRow=mysql_fetch_array($checkBank));
			echo"</select>";
			}
		else{
			$SubMit="";
			echo"<div class='redB'>系统未设置收款帐号,不能生成出货单.</div>";
			}
		  </td>
        </tr>
		*/
		?>
        <tr>
        	<td align="right">扣款项目：</td>
            <td>&nbsp;</td>
		</tr>
        <tr>
        	<td colspan="2">
            	<table width="806" cellspacing="0">
                	<tr align="center">
                  		<td width="40" height="23" class="A1111">选项</td>
                  		<td width="35" class="A1101">序号</td>
                  		<td width="102" class="A1101">PO#</td>
                  		<td width="417" class="A1101">Product Description </td>
                  		<td width="53" class="A1101">Q'ty</td>
                  		<td width="68" class="A1101">Price</td>
                  		<td width="75" class="A1101">Amount</td>
                	</tr>
					<?php   
					$POrderResult = mysql_query("SELECT YS.OrderPO,YS.POrderId,
					S.Id,S.Price,S.Qty,D.cName,D.eCode
					FROM $DataIn.ch1_shipsheet S
					LEFT JOIN $DataIn.yw1_ordersheet YS ON S.POrderId=YS.POrderId 
					LEFT JOIN $DataIn.productdata D ON YS.ProductId=D.ProductId 
					WHERE S.Mid='$Id' AND S.Type=1 
                                     UNION ALL
                                        SELECT '&nbsp;' AS OrderPO,S.POrderId,S.Id,S.Price,S.Qty,O.SampName AS cName,O.Description AS eCode 
	                                FROM $DataIn.ch1_shipsheet S
                                        LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId
	                                WHERE S.Mid='$Id' AND S.Type=2     
                                      ",$link_id);	
					if ($POrderRows = mysql_fetch_array($POrderResult)) {
						$i=1;
						do{
							$POrderId=$POrderRows["POrderId"];
							$OrderPO=$POrderRows["OrderPO"];
							$eCode=$POrderRows["eCode"];
							$Qty=$POrderRows["Qty"];
							$Price=$POrderRows["Price"];
							$Amount=sprintf("%.2f",$Qty*$Price);
							echo"<tr height='23'>
							<td class='A0111' align='center'><input name='checkid$i' type='checkbox' id='checkid$i' value='$OrderPO|$eCode'  onClick='WriteData(\"$i\")' checked></td>
							<td class='A0101' align='center'>$i</td>
							<td class='A0101' align='center'>$OrderPO</td>
							<td class='A0101'>$eCode</td>
							<td class='A0101'><div align='right'>
							<input type='text' size='8' name='Qty$i' value='$Qty' class='numINPUTout' onchange='ChangeThis(\"Qty\",\"$i\")'  onfocus='toTempValue(this.value)'></div></td>
							<td class='A0101'><input type='text' size='8' name='Price$i' value='$Price' onchange='ChangeThis(\"Price\",\"$i\")' class='numINPUTout'></td>
							<td class='A0101'><input type='text' size='10' name='Amount$i' value='$Amount' class='readonlyINPUT' disabled></td></tr>";
							$i++;
							}while ($POrderRows = mysql_fetch_array($POrderResult));
                                                        
                  
							echo"<input name='IdCount' type='hidden' id='IdCount' value='$i'><input name='TempValue' type='hidden' value='0'>";
						}
					?>
              	</table>
        	</td>
		</tr>
	</table>
</td></tr></table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script  type=text/javascript>
function ReadInfo(){
	var TempCompanyId=document.form1.CompanyId.value;
	var url="ch_shippinglist_creditnote_ajax.php?CompanyId="+TempCompanyId;
　	var ajax=InitAjax(); 
　	ajax.open("GET",url,true);   
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4){
			var BackData=ajax.responseText;
			var DataArray=BackData.split("~");
			document.form1.InvoiceNO.value=DataArray[0];
			document.form1.ModelId.length=1;
			for(var i=1;i<DataArray.length;i++){
				//分解项目
				var ModelArray=DataArray[i].split("|");
				var IdTemp=ModelArray[0];
				var NameTemp=ModelArray[1];
				//写入至选框
				document.form1.ModelId.options[i]=new Option(NameTemp,IdTemp);
				}
			}
		}
	ajax.send(null);	
	}

function WriteData(rowIndex){
	if(eval("document.form1.Qty"+rowIndex).disabled==false){
		eval("document.form1.Qty"+rowIndex).disabled=true;
		eval("document.form1.Price"+rowIndex).disabled=true;
		}
	else{
		eval("document.form1.Qty"+rowIndex).disabled=false;
		eval("document.form1.Price"+rowIndex).disabled=false;
		}
	}
function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}
function ChangeThis(Action,Row){//记录ID/行数/字段
	var oldValue=document.form1.TempValue.value;//改变前的值
	if(Action=="Qty"){
		var Qtytemp=eval("document.form1.Qty"+Row+".value");//改变后的数量
		var Pricetemp=eval("document.form1.Price"+Row+".value");
		if(Qtytemp==""){
			Qtytemp=0;
			alert("数量不能为空,重新输入!" );
			eval("document.form1.Qty"+Row).value=oldValue;
			eval("document.form1.Qty"+Row).select();
			return false;
			}
		//检查数量是否符合格式
		var Result=fucCheckNUM(Qtytemp,'');
		if(Result==0){
			alert("输入不正确的数量:"+Qtytemp+",重新输入!" );
			eval("document.form1.Qty"+Row).value=oldValue;
			eval("document.form1.Qty"+Row).select();
			return false;
			}
		else{
			var Amount=Qtytemp*Pricetemp;
			eval("document.form1.Amount"+Row).value=FormatNumber(Amount,2);
			}
		}
	else{
		var Pricetemp=eval("document.form1.Price"+Row+".value");//改变后的数量
		var Qtytemp=eval("document.form1.Qty"+Row+".value");
		if(Pricetemp==""){
			Pricetemp=0;
			alert("价格不能为空,重新输入!" );
			eval("document.form1.Price"+Row).value=oldValue;
			eval("document.form1.Price"+Row).select();
			return false;
			}
		//检查数量是否符合格式
		var Result=fucCheckNUM(Pricetemp,'Price');
		if(Result==0){
			alert("价格不正确的数量:"+Pricetemp+",重新输入!" );
			eval("document.form1.Price"+Row).value=oldValue;
			eval("document.form1.Price"+Row).select();
			return false;
			}
		else{
			var Amount=Qtytemp*Pricetemp;
			eval("document.form1.Amount"+Row).value=FormatNumber(Amount,2);
			}
		}
	}
</script>
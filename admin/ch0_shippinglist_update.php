<?php   
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 出货资料更新");//需处理
$fromWebPage=$funFrom."_".$From;		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upResult = mysql_query("SELECT M.Sign,M.CompanyId,M.ModelId,M.BankId,M.Number,M.InvoiceNO,M.Wise,M.Notes,M.Terms,M.PaymentTerm,M.PreSymbol,M.Date,C.Forshort
FROM $DataIn.ch0_shipmain M,$DataIn.trade_object C WHERE M.Id='$Id' AND M.CompanyId=C.CompanyId LIMIT 1",$link_id);
if($upData = mysql_fetch_array($upResult)){
	$ShipSign=$upData["Sign"];
	$ShipType=$upData["ShipType"];
	$CompanyId=$upData["CompanyId"];
	$ModelId=$upData["ModelId"];
	$BankId=$upData["BankId"];
	$Number=$upData["Number"];
	$InvoiceNO=$upData["InvoiceNO"];
	$Wise=$upData["Wise"];
	
	$Notes=$upData["Notes"];
	$Terms=$upData["Terms"];
	$PaymentTerm=$upData["PaymentTerm"];
	$PreSymbol=$upData["PreSymbol"];
	$Date=$upData["Date"];
	$Forshort=$upData["Forshort"];
	}
//步骤4：
$tableWidth=870;$tableMenuS=550;$spaceSide=15;
$CheckFormURL="thisPage";
$CustomFun="<span onClick='ViewOrderId(7)' $onClickCSS>追加出货内容</span>&nbsp;";//自定义功能
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,TempValue,,OrderIds,,ShipSign,$ShipSign,ShipType,$ShipType";
//步骤5：//需处理
 ?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td colspan="6" valign="bottom">&nbsp;◆ <?php    echo $Forshort?> 出货单(<?php    echo $Number?>)资料</td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width='<?php    echo $spaceSide?>' class='A0010' height='20'>&nbsp;</td>
		<td class='A1111' width='115' align='center' <?php    echo $Fun_bgcolor?>>文档模板</td>
		<td class='A1101' width='87' align='center' <?php    echo $Fun_bgcolor?>>收款帐号</td>
		<td class='A1101' width='116' align='center' <?php    echo $Fun_bgcolor?>>Invoice NO</td>
		<td class='A1101' width='82' align='center' <?php    echo $Fun_bgcolor?>>出货日期</td>
		<td width='60' align='center' class='A1101' <?php    echo $Fun_bgcolor?>>标签前导符</td>
		<td align='center' class='A1101' <?php    echo $Fun_bgcolor?>>WISE</td>
		<td width='<?php    echo $spaceSide?>' class='A0001'>&nbsp;</td>
	</tr>
	<tr>
		<td width='4' class='A0010' height='20'>&nbsp;</td>
		<td class='A0111' align='center'>
		<select name="ModelId" id="ModelId" style="width:110px">
		<?php   
		$checkModel=mysql_query("SELECT Id,Title FROM $DataIn.ch8_shipmodel WHERE 1 AND CompanyId='$CompanyId' ORDER BY Id",$link_id);
		if($ModelRow=mysql_fetch_array($checkModel)){		  	
			do{
				$mId=$ModelRow["Id"];
				$Title=$ModelRow["Title"];
				if($ModelId==$mId){
					echo"<option value='$mId' selected>$Title</option>";
					}
				else{
					echo"<option value='$mId'>$Title</option>";
					}
				}while($ModelRow=mysql_fetch_array($checkModel));
			}
		?>
		</select>
		</td>
		<td class='A0101' align='center'>
		<?php   
		  $checkBank=mysql_query("SELECT Id,Title FROM $DataPublic.my2_bankinfo WHERE Id='$BankId' ORDER BY Id LIMIT 1",$link_id);
		  if($BankRow=mysql_fetch_array($checkBank)){		  	
			$Title=$BankRow["Title"];
			echo $Title;
			}
		?>
	    </td>
		<td class='A0101' align='center'><input name="InvoiceNO" class="INPUT0000" type="text" Id="InvoiceNO" value="<?php    echo $InvoiceNO?>" size="15"></td>
		<td class='A0101' align='center'><input name="Date" class="INPUT0000" type="text" Id="Date" value="<?php    echo $Date?>" size="10" maxlength="10" onfocus="WdatePicker()" readonly></td>
		<td class='A0101' align='center'><input name="PreSymbol" type="text" class="INPUT0000" Id="PreSymbol" value="<?php    echo $PreSymbol?>" size="3" maxlength="1"></td>
		<td class='A0101' align='center'><input name="Wise" type="text" class="INPUT0000" Id="Wise" value="<?php    echo $Wise?>" size="37" maxlength="59"></td>
		<td width='4' class='A0001'>&nbsp;</td>
	</tr>
    

    
</table>   
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr> 
    <td width='<?php    echo $spaceSide?>' class='A0010' height='20'>&nbsp;</td>
		<td class='A1111' width='300' align='center' >Notes</td>
		<td align='center' class='A1101' >Terms</td>
    <td width='<?php    echo $spaceSide?>' class='A0001'>&nbsp;</td>   
	</tr>    
  	<tr>  
    <td width='<?php    echo $spaceSide?>' class='A0010' height='20'>&nbsp;</td>
		<td class='A1111' width='300' align='center'><textarea name="Notes" cols="47" rows="3" id="Notes"  ><?php    echo $Notes?></textarea></td>
		<td align='center' class='A1101'><textarea name="Terms" cols="47" rows="3" id="Terms"><?php    echo $Terms?></textarea></td>
    <td width='<?php    echo $spaceSide?>' class='A0001'>&nbsp;</td>      
	</tr>   
</table>

<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr> 
    <td width='<?php    echo $spaceSide?>' class='A0010' height='20'>&nbsp;</td>
		<td class='A1111' width='100' align='center' >PaymentTerm</td>
		<td align='left' class='A1101' >&nbsp;&nbsp;<input name="PaymentTerm" type="text" class="INPUT0000" Id="PaymentTerm" value="<?php    echo $PaymentTerm?>" size="100" maxlength="200"></td>
    <td width='<?php    echo $spaceSide?>' class='A0001'>&nbsp;</td>   
	</tr>    

</table>

<?php   
//明细信息
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td colspan="7" valign="bottom">&nbsp;◆ 原出货单明细(即时更新)</td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td width="40" class='A1111' align="center" <?php    echo $Fun_bgcolor?>>操作</td>
		<td width="40" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>序号</td>
		<td width="80" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>PO</td>
		<td width="260" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>产品名称</td>
		<td width="250" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>Product Code</td>
		<td width="70" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>售价</td>
		<td width="90" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>出货数量</td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' colspan="7">
		<div style="width:836px;height:179px;overflow-x:hidden;overflow-y:scroll"> 
		<table border="0" width="820" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" align="left" id="OrderList">
		<?php   
		//明细信息
	 	//产品订单列表
		$sheetResultP = mysql_query("SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,S.Qty,S.Price,S.Type,S.YandN 
				FROM $DataIn.ch0_shipsheet S 
				LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
				LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId WHERE S.Mid='$Id' AND S.Type='1'
			",$link_id);		
		$k=1;
		if($sheetRowP = mysql_fetch_array($sheetResultP)){			
			do{
				$sId=$sheetRowP["Id"];
				$POrderId=$sheetRowP["POrderId"];
				$OrderPO=$sheetRowP["OrderPO"]==""?"&nbsp;":$sheetRowP["OrderPO"];
				$Price=$sheetRowP["Price"];
				$Date=$sheetRowP["Date"];
				$Qty=$sheetRowP["Qty"];
				$Remark=$sheetRowP["Remark"];
				$cName=$sheetRowP["cName"];
				$eCode=$sheetRowP["eCode"];				
				echo"<tr>
				<td width='40' class='A0101' align='center' height='20'><a href='#' onclick='deleteRow(this.parentNode,OrderList,$POrderId,$sId)' title='取消此出货项目'>×</a></td>
				<td width='40' class='A0101' align='center'>$k</td>
				<td width='80' class='A0101' align='center'>$OrderPO</td>
				<td width='260' class='A0101'>$cName</td>
				<td width='250' class='A0101'>$eCode</td>
				<td width='70' class='A0101' align='right'><input name='Price' class='INPUT0000' type='text' Id='Price' value='$Price' size='6' maxlength='10' onblur='changePrice(this,$sId)' onfocus='toTempValue(this.value)'></td>
				<td width='73' class='A0101' align='center'>$Qty</td>
				</tr>";
				$k++;
				}while($sheetRowP = mysql_fetch_array($sheetResultP));
			}		
		?>
		</table>
		</div>
		</td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
</table>
<?php   
//新加出货订单
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td colspan="7" valign="bottom">&nbsp;◆ 新加出货单明细</td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td width="40" class='A1111' align="center" <?php    echo $Fun_bgcolor?>>操作</td>
		<td width="40" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>序号</td>
		<td width="80" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>PO</td>
		<td width="260" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>产品名称</td>
		<td width="250" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>Product Code</td>
		<td width="70" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>售价</td>
		<td width="90" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>出货数量</td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010">&nbsp;</td>
		<td class='A0111' colspan="7" >
		<div style="width:836;height:70;overflow-x:hidden;overflow-y:scroll"> 
		<table border="0" width="820" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" align="left" id="ListTable">
		</table>
		</div>
		</td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
</table>
<?php   
include "../model/subprogram/add_model_b.php";
?>
<script LANGUAGE='JavaScript'>
function CheckForm(){
	var OrderIdsTemp="";
	for(var j=0;j<ListTable.rows.length;j++){		
		if(OrderIdsTemp==""){
			OrderIdsTemp=ListTable.rows[j].cells[0].data+"^^"+ListTable.rows[j].cells[1].data+"^^"+ListTable.rows[j].cells[6].innerHTML;
			}
		else{
			OrderIdsTemp=OrderIdsTemp+"|"+ListTable.rows[j].cells[0].data+"^^"+ListTable.rows[j].cells[1].data+"^^"+ListTable.rows[j].cells[6].innerHTML;
			}
		}
	document.form1.OrderIds.value=OrderIdsTemp;
	document.form1.action="ch0_shippinglist_updated.php";
	document.form1.submit();
	}
	
function ViewOrderId(Action){
	var Message="";
	var num=Math.random();  
	var ClientTemp=document.form1.CompanyId.value;
	var ShipSign=document.form1.ShipSign.value;
	BackData=window.showModalDialog("ch0_shippinglist_s1.php?num="+num+"&Action="+Action+"&Jid="+ClientTemp+"&ShipSign="+ShipSign,"BackData","dialogHeight =550px;dialogWidth=980px;center=yes;scroll=yes");
	//拆分
	if(BackData){
  		//alert(BackData);
		var Rows=BackData.split("``");//分拆记录:
		var Rowslength=Rows.length;//数组长度即订单数
		for(var i=0;i<Rowslength;i++){
			var Message="";
			var FieldTemp=Rows[i];		//拆分后的记录
			var FieldArray=FieldTemp.split("^^");
			//过滤相同的产品订单ID号
			for(var j=0;j<ListTable.rows.length;j++){
				var OrderIdtemp=ListTable.rows[j].cells[1].data;//隐藏ID号存于操作列
				if(FieldArray[1]==OrderIdtemp){//如果流水号存在
					Message="待出项目: "+FieldArray[1]+FieldArray[3]+" 已存在!跳过继续！";
					break;
					}
				}

			if(Message==""){
				oTR=ListTable.insertRow(ListTable.rows.length);
				//表格行数
				tmpNum=oTR.rowIndex+1;
				//第一列:操作
				oTD=oTR.insertCell(0);
				oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode,ListTable)' title='删除当前行'>×</a>";
				oTD.data=""+FieldArray[0]+"";
				oTD.onmousedown=function(){
					window.event.cancelBubble=true;
					};
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="40";
				oTD.height="20";
				
				//第二列:序号
				oTD=oTR.insertCell(1);
				oTD.innerHTML=""+tmpNum+"";
				oTD.data=""+FieldArray[1]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="40";
				//三、PO
				oTD=oTR.insertCell(2);
				oTD.innerHTML=""+FieldArray[2]+"";
				
				oTD.className ="A0101";
				oTD.width="80";
				
				//四：中文名
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[3]+"";
				oTD.className ="A0101";
				oTD.width="260";
				
				//五:Product Code
				oTD=oTR.insertCell(4); 
				oTD.innerHTML=""+FieldArray[4]+"";
				oTD.className ="A0101";
				oTD.width="250";

				//六：信价
				oTD=oTR.insertCell(5);
				oTD.innerHTML=""+FieldArray[5]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";

				//七：数量
				oTD=oTR.insertCell(6);
				oTD.innerHTML=""+FieldArray[6]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="73";
				}//end if(Message=="")
			else{
				alert(Message);
				}
			}//end for
			return true;
		}
	else{
		alert("没有选取待出订单！");
		return false;
		}
	}
function changePrice(thisE,sId){
	var oldValue=document.form1.TempValue.value;
	var Id=document.form1.Id.value;
	var thisValue=thisE.value;
	var CheckSTR=fucCheckNUM(thisValue,"Price");
	if(CheckSTR==0 || thisValue==""){
		alert("格式不对！");
		thisE.value=oldValue;
		return false;
		}
	else{
		//更新价格
		var message=confirm("确定要更改此出贷项目价格吗？");
		if (message==true){
			myurl="ch0_shippinglist_updatedajax.php?sId="+sId+"&ActionId=935&NewPrice="+thisValue+"&Id="+Id;
			//retCode=openUrl(myurl);
			/*if (retCode=="-2"){
				alert("价格更新失败！");
				thisE.value=oldValue;
				return false;
				}*/
　	       var ajax=InitAjax();
　	       ajax.open("GET",myurl,true);
	          ajax.onreadystatechange =function(){
	　　      if(ajax.readyState==4 && ajax.status ==200){
                       }
                 }
　	       ajax.send(null);
			}
		else{
			//价格还原
			thisE.value=oldValue;
			return false;
			}
		}
	}

function ShowSequence(TableTemp){
	for(i=0;i<TableTemp.rows.length;i++){ 
  		var j=i+1
		TableTemp.rows[i].cells[1].innerText=j; 
		}
	}

function deleteRow (RowTemp,TableTemp,OrderIdTemp,sId){
	var rowIndex=RowTemp.parentElement.rowIndex;
	if(TableTemp==ListTable){
		TableTemp.deleteRow(rowIndex);
		ShowSequence(TableTemp);
		}
	else{
		//处理删除，删除成功后再删除行
		var LengthTemp=TableTemp.rows.length;
		if (LengthTemp==1){
			alert("本出货单最后一个订单，不能删除，请使用取消出货的功能！");return false;
			}
		else{
			var message=confirm("确定要删除此出货订单吗？如果删除，则需重新设置装箱并再次生成Invoice!");
			if (message==true){
				var Id=document.form1.Id.value;
				var ReBackId=OrderIdTemp;
				myurl="ch0_shippinglist_updatedajax.php?POrderId="+ReBackId+"&sId="+sId+"&ActionId=934&Id="+Id;
				/*retCode=openUrl(myurl);
				if(retCode!=-2){
					TableTemp.deleteRow(rowIndex);
					ShowSequence(TableTemp);
					//求表格长度，如果是最后一个单，则返回待出订单页面
					}
				else{
					alert("删除失败！");return false;
					}*/
　        	var ajax=InitAjax();
　	       ajax.open("GET",myurl,true);
	          ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
	　　　					TableTemp.deleteRow(rowIndex);
					ShowSequence(TableTemp);
		         	}
		       }
　	       ajax.send(null);

				}
			else{
				return false;
				}
			}
		}	
	}
</script>
<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 需求单拆分");//需处理
$nowWebPage =$funFrom."_analyzes";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="Id,$Id,fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number,CompanyId,$CompanyId,ActionId,22";
//步骤3：
$tableWidth=980;$tableMenuS=500;
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
?>
<table width='<?php  echo $tableWidth?>' border='0' cellspacing='0' bgcolor='#FFFFFF'>
	<tr>
	<td width='10' class='A0010' bgcolor='#FFFFFF' height='35'>&nbsp;</td>
	<td colspan="9">1、拆分前数据</td>
	<td width='10' class='A0001' bgcolor='#FFFFFF'>&nbsp;</td>
	</tr>
	<tr <?php  echo $Fun_bgcolor?>>
	<td width='10' class='A0010' bgcolor='#FFFFFF' height='25'>&nbsp;</td>
	<td width='100' class='A1111' align='center'>客户</td>
	<td width='100' class='A1101' align='center'>需购流水号</td>
	<td width='300' class='A1101' align='center'>配件名称</td>
	<td width='70' class='A1101' align='center'>价格</td>
	<td width='65' class='A1101' align='center'>订单数量</td>
	<td width='65' class='A1101' align='center'>库存数量</td>
	<td width='65' class='A1101' align='center'>需购数量</td>
	<td width='65' class='A1101' align='center'>增购数量</td>
	<td width='130' class='A1101' align='center'>供应商</td>
	<td width='10' class='A0001' bgcolor='#FFFFFF'>&nbsp;</td>
	</tr>
<?php 
$StockResult = mysql_query("SELECT 
	S.Id,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,
	D.StuffCname,P.Forshort,M.Name
	FROM $DataIn.cg1_stocksheet S 
	LEFT JOIN $DataIn.stuffdata D ON S.StuffId=D.StuffId 
	LEFT JOIN $DataIn.trade_object P ON S.CompanyId=P.CompanyId 
	LEFT JOIN $DataPublic.staffmain M ON M.Number=S.BuyerId WHERE S.Id='$Id'",$link_id);
if ($myrow = mysql_fetch_array($StockResult)) {
		$Id=$myrow["Id"];
		$StockId=$myrow["StockId"];		
		$POrderId=$myrow["POrderId"];
		$StuffId=$myrow["StuffId"];
		$StuffCname=$myrow["StuffCname"];
		$Price=$myrow["Price"];
		$OrderQty=$myrow["OrderQty"];
		$StockQty=$myrow["StockQty"];
		$FactualQty=$myrow["FactualQty"];
		$AddQty=$myrow["AddQty"];
		$Forshort=$myrow["Forshort"];
		$Name=$myrow["Name"];
		//客户/产品名称
		
		echo "<tr>";	
		echo "<td class='A0010' height='25'>&nbsp;</td>";
		echo "<td class='A0111'>&nbsp;</td>";
		echo "<td class='A0101' align='center'>$StockId</td>";
		echo "<td class='A0101'>$StuffCname</td>";
		echo "<td class='A0101' align='center'>$Price</td>";
		echo "<td class='A0101' align='center'>$OrderQty</td>";
		echo "<td class='A0101' align='center'>$StockQty</td>";
		echo "<td class='A0101' align='center'>$FactualQty</td>";
		echo "<td class='A0101' align='center'>$AddQty</td>";
		echo "<td class='A0101'>$Forshort</td>";
		echo"<td width='10' class='A0001' bgcolor='#FFFFFF'>&nbsp;</td>";
		echo "</tr>";
		echo"<tr>
		<td width='10' class='A0010' bgcolor='#FFFFFF' height='35'>&nbsp;</td>
		<td colspan='9'>2、拆分数据</td>
		<td width='10' class='A0001' bgcolor='#FFFFFF'>&nbsp;</td>
		</tr>";
	}
$maxSql = mysql_query("SELECT MAX(StockId) AS oldMaxSID FROM $DataIn.cg1_stocksheet WHERE POrderId='$POrderId'",$link_id);
$oldMaxSID=mysql_result($maxSql,0,"oldMaxSID");
?>
</table>
<table width='<?php  echo $tableWidth?>' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
	<tr <?php  echo $Fun_bgcolor?>>
		<td width='10' class='A0010' bgcolor='#FFFFFF' height='25'>&nbsp;</td>
		<td width='100' class='A1111' align='center'>可拆订单数量</td>
		<td width='100' class='A1101' align='center'>可拆库存数量</td>
		<td width='100' class='A1101' align='center'>可拆增购数量</td>
		<td width='100' class='A1101' align='center'>价格</td>
		<td width='200' class='A1101' align='center'>供应商</td>
		<td width='100' class='A1101' align='center'>动作</td>
		<td bgcolor='#FFFFFF'>&nbsp;</td>
		<td width='10' class='A0001' bgcolor='#FFFFFF'>&nbsp;</td>
	</tr>
	<tr>
		<td width='10' class='A0010' bgcolor='#FFFFFF' height='25'>&nbsp;</td>
		<td class='A0111'><input name='OrderQty' type='text' id='OrderQty' size='10' class="INPUT0000" value='<?php  echo $OrderQty?>'></td>
		<td class='A0101'><input name='StockQty' type='text' id='StockQty' size='10' class="INPUT0000" value='<?php  echo $StockQty?>'></td>
		<td class='A0101'><input name='AddQty' type='text' id='AddQty' size='10' class="INPUT0000" value='<?php  echo $AddQty?>'></td>
		<td class='A0101'><input name='Price' type='text' id='Price' size='10' class="INPUT0000" value='<?php  echo $Price?>'></td>
		<td class='A0101'>
			<select name='CompanyId' id='CompanyId'>
			<?php 
			$result = mysql_query("SELECT CompanyId,Forshort,Letter FROM $DataIn.trade_object WHERE (cSign=$Login_cSign OR cSign=0 ) AND Estate=1 ORDER BY Letter",$link_id);
				while ($myrow = mysql_fetch_array($result)){
					$Forshort=$myrow["Forshort"];
					$thisCompanyId=$myrow["CompanyId"];
					$Letter=$myrow["Letter"];
					$ForshortName=$Letter.'-'.$Forshort;
					if ($thisCompanyId==$CompanyId){
						echo"<option value='$thisCompanyId|$Forshort' selected>$ForshortName </option>";}
					else{
						echo"<option value='$thisCompanyId|$Forshort'>$ForshortName</option>";}
					}?>
			</select>
		</td>
		<td align='center' class='A0101'><input type='button' name='Submit' value='确定' onclick='ToResolution()'></td>
		<td bgcolor='#FFFFFF'>&nbsp;</td>
		<td width='10' class='A0001' bgcolor='#FFFFFF'>&nbsp;</td>
	</tr>
	<tr <?php  echo $Fun_bgcolor?>>
		<td width='10' class='A0010' bgcolor='#FFFFFF' height='35'>&nbsp;</td>
		<td bgcolor='#FFFFFF' colspan='7'>3、拆分后数据</td>
		<td width='10' class='A0001' bgcolor='#FFFFFF'>&nbsp;</td>
	</tr>
</table>

<table width='<?php  echo $tableWidth?>' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
	<tr <?php  echo $Fun_bgcolor?>>
		<td width='10' class='A0010' bgcolor='#FFFFFF' height='25'>&nbsp;</td>
		<td width='45' class='A1111' align='center' height='25'>操作</td>
		<td width='45' class='A1101' align='center'>序号</td>
		<td width='100' class='A1101' align='center'>需求流水号</td>
		<td width='280' class='A1101' align='center'>配件名称</td>
		<td width='70' class='A1101' align='center'>价格</td>
		<td width='65' class='A1101' align='center'>订单数量</td>
		<td width='65' class='A1101' align='center'>库存数量</td>
		<td width='65' class='A1101' align='center'>需购数量</td>
		<td width='65' class='A1101' align='center'>增购数量</td>
		<td width='120' class='A1101' align='center'>供应商</td>
		<td width='10' class='A0001' bgcolor='#FFFFFF'>&nbsp;</td>
	</tr>
	<tr>
		<td width="10" class="A0010" height="25" bgcolor="#FFFFFF">&nbsp;</td>
		<td colspan="10" height="143" class="A0111">
			<div style="width:960;height:100%;overflow-x:hidden;overflow-y:scroll">
				<table cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' id='ListTable'>
				</table>
			</div>
		<td width="10" class="A0001"  bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
</table>
<input name='oldMaxSID' type='hidden' id='oldMaxSID' value='<?php  echo $oldMaxSID?>'>
<input name='MaxSID' type='hidden' id='MaxSID' value='<?php  echo $oldMaxSID?>'>
<input name='old_StockId' type='hidden' id='old_StockId' value='<?php  echo $StockId?>'>
<input name='old_StuffId' type='hidden' id='old_StuffId' value='<?php  echo $StuffId?>'>
<input name='old_StuffCname' type='hidden' id='old_StuffCname' value='<?php  echo $StuffCname?>'>
<input name='old_Price' type='hidden' id='old_Price' value='<?php  echo $Price?>'>
<input name='old_OrderQty' type='hidden' id='old_OrderQty' value='<?php  echo $OrderQty?>'>
<input name='old_StockQty' type='hidden' id='old_StockQty' value='<?php  echo $StockQty?>'>
<input name='old_FactualQty' type='hidden' id='old_FactualQty' value='<?php  echo $FactualQty?>'>
<input name='old_AddQty' type='hidden' id='old_AddQty' value='<?php  echo $AddQty?>'>
<input name='old_Forshort' type='hidden' id='old_Forshort' value='<?php  echo $Forshort?>'>
<input name='ListSTR' type='hidden' id='ListSTR' value=''>
<input name='IdCount' type='hidden' id='IdCount' value='0'>
<?php 
include "../model/subprogram/add_model_b.php";
?>
<script language="javascript">
function CheckForm(ALType){
	var ListTemp="";
	var Message="";
	//检查拆分前后的数据是否一致
	if(Number(document.form1.old_OrderQty.value)!=0){
		document.form1.OrderQty.value=document.form1.old_OrderQty.value;
		Message="还有订单数量没拆分!";
		}
	if(Number(document.form1.old_StockQty.value)!=0){
		document.form1.StockQty.value=document.form1.old_StockQty.value;
		Message="还有使用库存的数量没拆分!";
		}
	if(Number(document.form1.old_AddQty.value)!=0){
		document.form1.AddQty.value=document.form1.old_AddQty.value;
		Message="还有增购数量没拆分!";
		}
	if(Message!=""){
		alert(Message);
		return false;
		}
	else{
		for(var m=0; m<ListTable.rows.length; m++){
			if(m==0){
				//4单价|5订单数量|6库存数量|7采购数量|8增购数量|9供应商
				ListTemp=
				ListTable.rows[m].cells[4].innerText+"|"+
				ListTable.rows[m].cells[5].innerText+"|"+
				ListTable.rows[m].cells[6].innerText+"|"+
				ListTable.rows[m].cells[7].innerText+"|"+
				ListTable.rows[m].cells[8].innerText+"|"+
				ListTable.rows[m].cells[9].data;
				}
			else{
				ListTemp=ListTemp+"~"+
				ListTable.rows[m].cells[4].innerText+"|"+
				ListTable.rows[m].cells[5].innerText+"|"+
				ListTable.rows[m].cells[6].innerText+"|"+
				ListTable.rows[m].cells[7].innerText+"|"+
				ListTable.rows[m].cells[8].innerText+"|"+
				ListTable.rows[m].cells[9].data;
				}
			}
		document.form1.ListSTR.value=ListTemp;
		document.form1.action="cg_cgdmain_updated.php";
		document.form1.submit();
		}
	}
	
function ToResolution(){	
	var oldOrderQty=Number(document.form1.old_OrderQty.value);	//可拆分的订单数量
	var oldStockQty=Number(document.form1.old_StockQty.value);	//可拆分的库存数量
	var oldAddQty=Number(document.form1.old_AddQty.value);		//可拆分的增购数量
	
	var newOrderQty=Number(document.form1.OrderQty.value);		//子单的订单数量
	var newStockQty=Number(document.form1.StockQty.value);		//子单的库存数量
	var newAddQty=Number(document.form1.AddQty.value);			//子单的增购数量
	var newFactualQty=newOrderQty-newStockQty;
	if(newFactualQty==0 && newAddQty>0){
		alert("需购数量为0时不能设增购数量!请重设.");
		return false;
		}
	if(
	newOrderQty<=oldOrderQty &&
	newStockQty<=oldStockQty &&
	newAddQty<=oldAddQty &&	
	newOrderQty>0 && 
	newStockQty>=0 && 
	newAddQty>=0 && 	
	newStockQty<=newOrderQty){//条件：子单订单数量<=可拆分订单数量
		
		var oldOrderQty=oldOrderQty-newOrderQty;			//新的可拆分订单数量
		var oldStockQty=oldStockQty-newStockQty;			//新的可拆分库存数量
		var oldAddQty=oldAddQty-newAddQty;					//新的可拆分增购数量
		
		
		//初值
		var tempStuffCname=document.form1.old_StuffCname.value;		//配件名称
		var tempPrice=document.form1.Price.value;					//子单单价
		
		//表格行数
		var oTR = ListTable.insertRow();
		tmpNum=oTR.rowIndex+1;
		if(tmpNum==1){		
			var thisSID=Number(document.form1.old_StockId.value);
			}
		else{
			var thisSID=Number(document.form1.MaxSID.value)+1;		//最大流水号
			document.form1.MaxSID.value=thisSID;
			}

		//1: 操作
		oTD=oTR.insertCell(0);		
		oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode,ListTable)' title='删除当前行'>×</a>";
		oTD.className ="A0100";
		oTD.align="center";
		oTD.width="47";
		oTD.height="20";
		
		//2：序号
		oTD=oTR.insertCell(1);
		oTD.innerHTML=""+tmpNum+"";
		oTD.className ="A0110";
		oTD.align="center";
		oTD.width="48";
		
		//3：流水号
		oTD=oTR.insertCell(2);
		oTD.innerHTML=""+thisSID+"";
		oTD.className ="A0110";
		oTD.align="center";
		oTD.width="102";
		
		//4：配件名称
		oTD=oTR.insertCell(3);
		oTD.innerHTML=""+tempStuffCname+"";
		oTD.className ="A0110";
		oTD.width="282";
		
		//5:价格
		oTD=oTR.insertCell(4);
		oTD.innerHTML=""+tempPrice+"";
		oTD.className ="A0110";
		oTD.align="center";
		oTD.data=tempPrice;
		oTD.width="72";
		
		//6:订单数量
		oTD=oTR.insertCell(5);
		oTD.innerHTML=""+newOrderQty+"";
		oTD.className ="A0110";
		oTD.align="center";
		oTD.data=newOrderQty;
		oTD.width="67";
		
		//7:库存数量
		oTD=oTR.insertCell(6);
		oTD.className ="A0110";
		oTD.align="center";
		oTD.width="67";
		oTD.innerText=newStockQty;
	
		//8：采购数量
		oTD = oTR.insertCell(7);
		oTD.innerText=newFactualQty;
		oTD.data=newFactualQty;
		oTD.className ="A0110";
		oTD.align="center";
		oTD.width="67";

		//9：增购数量
		 oTD = oTR.insertCell(8);	
		oTD.innerText=newAddQty;
		oTD.className ="A0110";
		oTD.align="center";
		oTD.width="67";

		//10：供应商
		oTD =oTR.insertCell(9);
		var Providertemp=document.getElementById('CompanyId').value;
		//拆分
		var CL=Providertemp.split("|");
		var PId=CL[0];					//供应商ID
		var PName=CL[1];				//供应商名称
			oTD.innerText=PName;
			oTD.data=PId;
		oTD.className ="A0110";
		oTD.width="122";
		
		document.form1.old_OrderQty.value=oldOrderQty;		//重置可拆分的订单数量
		document.form1.OrderQty.value=oldOrderQty;			//剩余的订单数量重写
		
		document.form1.old_StockQty.value=oldStockQty;		//重置可拆分的库存数量		
		document.form1.StockQty.value=oldStockQty;			//剩余的库存数量重写
		
		document.form1.old_AddQty.value=oldAddQty;		//重置可拆分的库存数量		
		document.form1.AddQty.value=oldAddQty;			//剩余的库存数量重写
		}
	else{
		alert("拆分的数量不正确");
		document.form1.OrderQty.value=oldOrderQty;
		document.form1.StockQty.value=oldStockQty;
		document.form1.AddQty.value=oldAddQty;
		return false;
		}
	}

//序号重整
function ShowSequence(TableTemp,rowIndex){
	if(TableTemp==ListTable){		
		var SIDTemp=document.form1.oldMaxSID.value;	//原有需求单最大值
		document.form1.MaxSID.value=SIDTemp;		//最大值重新初始化
		}
	for(i=0;i<TableTemp.rows.length;i++){ 
		var j=i+1;
  		TableTemp.rows[i].cells[1].innerText=j; 
		if(TableTemp==ListTable){//新增需求单列表
			if(i==0){
				TableTemp.rows[i].cells[2].innerText=Number(document.form1.old_StockId.value);
				}
			else{
				TableTemp.rows[i].cells[2].innerText=Number(document.form1.MaxSID.value)+1;
				document.form1.MaxSID.value=Number(document.form1.MaxSID.value)+1;
				}
			}
		}
  }   
  
//删除行 
function deleteRow (RowTemp,TableTemp){
	var rowIndex=RowTemp.parentElement.rowIndex;
	var QTY1=Number(TableTemp.rows[rowIndex].cells[5].innerText);//子单订单数量
	var QTY2=Number(TableTemp.rows[rowIndex].cells[6].innerText);//子单库存库存
	var QTY3=Number(TableTemp.rows[rowIndex].cells[7].innerText);//子单需购数量
	var QTY4=Number(TableTemp.rows[rowIndex].cells[8].innerText);//子单增购数量	
	document.form1.old_OrderQty.value=Number(document.form1.old_OrderQty.value)+QTY1;
	document.form1.OrderQty.value=Number(document.form1.OrderQty.value)+QTY1;
	document.form1.old_StockQty.value=Number(document.form1.old_StockQty.value)+QTY2;
	document.form1.StockQty.value=Number(document.form1.StockQty.value)+QTY2;
	document.form1.old_FactualQty.value=Number(document.form1.old_FactualQty.value)+QTY3;
	document.form1.old_AddQty.value=Number(document.form1.old_AddQty.value)+QTY4;
	TableTemp.deleteRow(rowIndex);
	ShowSequence(TableTemp,rowIndex);
	}
</script>
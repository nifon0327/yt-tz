<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cg1_stocksheet
$DataIn.trade_object
$DataIn.cg1_stockmain
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增入库记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=900;$tableMenuS=500;
$CustomFun="<span onclick='SearchRecord()' $onClickCSS>加入需求单</span>&nbsp;";
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr valign="bottom">
	  <td height="25" colspan="6" align="center" class="A0011">入库主单信息</td>
	</tr>
	<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="10" height="25" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>		
		<td align="center" class="A1111">供 应 商</td>
	  	<td align="center" class="A1101">送货单号</td>
		<td align="center" class="A1101">入库日期</td>
		<td width="240" align="center" class="A1101">入库备注</td>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" class="A0010" height="25">&nbsp;</td>
		<td align="center" valign="middle" class="A0111" ><select name="CompanyId" id="CompanyId" onChange="javascript:document.form1.submit();" style="width: 125px;">
          <?php 
			//供应商:有采购且未收完货	
			$GYS_Sql = "SELECT S.CompanyId,P.Forshort,P.Letter 
			FROM $DataIn.cg1_stocksheet S 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 	
        	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId		
			WHERE  1 AND S.rkSign>0 AND S.blsign = 1 AND S.Mid>0  AND S.CompanyId!='".$APP_CONFIG['PT_SUPPLIER']."'  GROUP BY S.CompanyId ORDER BY P.Letter";
			$GYS_Result = mysql_query($GYS_Sql); 
			while ( $GYS_Myrow = mysql_fetch_array($GYS_Result)){
				$ProviderTemp=$GYS_Myrow["CompanyId"];
				$CompanyId=$CompanyId==""?$ProviderTemp:$CompanyId;
				$Forshort=$GYS_Myrow["Forshort"];
				$Letter=$GYS_Myrow["Letter"];
				$Forshort=$Letter.'-'.$Forshort;		
				if ($ProviderTemp==$CompanyId){
					echo "<option value='$ProviderTemp' selected>$Forshort</option>";
					}
				else{
					echo "<option value='$ProviderTemp'>$Forshort</option>";
					}
				} 
			?>
        </select></td>
				<td class="A0101" align="center"><input name="BillNumber" type="text" id="BillNumber" class="INPUT0100" size="15" value=""></td>
		<td align="center" class="A0101"><input name="rkDate" type="text" id="rkDate" value="<?php  echo date("Y-m-d")?>" size="12" maxlength="10"></td>
		<td align="center" class="A0101"><input name="Remark" type="text" id="Remark" size="60" class="INPUT0100"></td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" height="30" class="A0010">&nbsp;</td>
		<td colspan="4" align="center" valign="bottom">入库明细资料
	    <input name="TempValue" type="hidden" id="TempValue"><input name='AddIds' type='hidden' id="AddIds"></td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
</table>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="10" class="A0010" bgcolor="#FFFFFF" height="25">&nbsp;</td>
		<td class="A1111" width="40" align="center">操作</td>
		<td class="A1101" width="40" align="center">序号</td>
		<td class="A1101" width="95" align="center">需求流水号</td>
		<td class="A1101" width="55" align="center">配件ID</td>
		<td class="A1101" width="330" align="center">配件名称</td>
		<td class="A1101" width="60" align="center">需求数量</td>
		<td class="A1101" width="60" align="center">增购数量</td>
		<td class="A1101" width="60" align="center">实购数量</td>
		<td class="A1101" width="60" align="center">未收数量</td>
		<td class="A1101" width="80" align="center">当前入库</td>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" class="A0010" height="216">&nbsp;</td>
		<td colspan="10" align="center" class="A0111">
		<div style="width:880;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='880' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTable">
			<?php 
			//入库明细列表
			?>
			</table>
		</div>		
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function CheckForm(){
	var Message=""
	if(ListTable.rows.length<1){
		Message="没有设置入库的数据!";
		}
	var BillNumber=document.form1.BillNumber.value;
	if(BillNumber==""){
		Message="没有输入送货单号.";
		}
	if(Message!=""){
		alert(Message);return false;
		}
	else{
		var StockValues="";
		//读取加入的数据
		var obj=document.getElementsByName("IndepotQTY[]"); 
		//alert(obj.length);
		for(i=0;i <obj.length;i++){ 
			if(StockValues==""){
				StockValues=ListTable.rows[i].cells[2].innerText+"!"+ListTable.rows[i].cells[3].innerText+"!"+obj[i].value;
				}
			else{
				StockValues=StockValues+"|"+ListTable.rows[i].cells[2].innerText+"!"+ListTable.rows[i].cells[3].innerText+"!"+obj[i].value;
				}
			} 
		document.form1.AddIds.value=StockValues;
		//alert(StockValues);
		document.form1.action="ck_rk_save.php";
		document.form1.submit();
		}
	}

function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}
function Indepot(thisE,SumQty){
	var oldValue=document.form1.TempValue.value;
	var thisValue=thisE.value;
	var CheckSTR=fucCheckNUM(thisValue,"");
	if(CheckSTR==0){
		alert("不是规范的数字！");
		thisE.value=oldValue;
		return false;
		}
	else{
		if((thisValue>SumQty) || thisValue==0){
			alert("不在允许值的范围！");
			thisE.value=oldValue;
			return false;
			}
		}
	}
//删除指定行
function deleteRow(rowIndex){
	ListTable.deleteRow(rowIndex);
	ShowSequence(ListTable);
	}
	
function ShowSequence(TableTemp){
	for(i=0;i<TableTemp.rows.length;i++){ 
  		var j=i+1
		TableTemp.rows[i].cells[1].innerText=j; 
		}
	}   
function SearchRecord(){
	var Jid=document.getElementById('CompanyId').value;
	//var Bid=document.getElementById('BuyerId').value;
	var Bid="";
	var num=Math.random();  
	BackStockId=window.showModalDialog("ck_rk_s1.php?r="+num+"&Jid="+Jid+"&Bid="+Bid+"&tSearchPage=ck_rk&fSearchPage=ck_rk&SearchNum=2&Action=2","BackStockId","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
	if (BackStockId){
  		var Rowstemp=BackStockId.split("``");
		var Rowslength=Rowstemp.length;
		for(var i=0;i<Rowslength;i++){
			var Message="";			
			var FieldArray=Rowstemp[i].split("^^");
			//过滤相同的产品订单ID号
			for(var j=0;j<ListTable.rows.length;j++){
				var StockIdtemp=ListTable.rows[j].cells[2].innerText;//隐藏ID号存于操作列	
				if(FieldArray[0]==StockIdtemp){//如果流水号存在
					Message="需求流水号: "+FieldArray[0]+"的资料已在列表!跳过继续！";
					break;
					}
				}
			if(Message==""){
				oTR=ListTable.insertRow(ListTable.rows.length);
				tmpNum=oTR.rowIndex+1;
				//第一列:操作
				oTD=oTR.insertCell(0);
				oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>";
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
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="40";
				
				//三、需求流水号
				oTD=oTR.insertCell(2);
				oTD.innerHTML=""+FieldArray[0]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="94";
				
				//四：配件ID
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[1]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="54";
				
				//五:配件名称
				oTD=oTR.insertCell(4); 
				oTD.innerHTML=""+FieldArray[2]+"";
				oTD.className ="A0101";
				oTD.width="328";

				//六：配求数量
				oTD=oTR.insertCell(5);
				oTD.innerHTML=""+FieldArray[3]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="59";

				//七：增购数量
				oTD=oTR.insertCell(6);
				oTD.innerHTML=""+FieldArray[4]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="59";
				var SumQty=FieldArray[3]*1+FieldArray[4]*1;
				//八：实购数量
				oTD=oTR.insertCell(7);
				oTD.innerHTML=""+SumQty+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="59";
				
				//第九列:未收数量
				oTD=oTR.insertCell(8);
				oTD.innerHTML=""+FieldArray[6]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="59";
				
				//第十列:本次入库
				oTD=oTR.insertCell(9);
				oTD.innerHTML="<input type='text' name='IndepotQTY[]' id='IndepotQTY' size='4' class='I0000L' value='"+FieldArray[6]+"' onblur='Indepot(this,"+FieldArray[6]+")' onfocus='toTempValue(this.value)'>";
				oTD.className ="A0101";
				oTD.width="79";				
				}
			else{
				alert(Message);
				}//if(Message=="")
			}//for(var i=0;i<Rowslength;i++)
		}//if (BackStockId)
	else{
		alert("没有选取数据!");return true;
		}
	}
</script>

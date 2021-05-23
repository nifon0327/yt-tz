<?php 
//已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增车间生产记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseMonth,$chooseMonth";
//步骤3：
$tableWidth=900;$tableMenuS=500;
$CustomFun="<span onclick='SearchRecord()' $onClickCSS>加入订单</span>&nbsp;";
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr valign="bottom">
    <td colspan="5" align="center" class="A0011">&nbsp;</td>
  </tr>
  <tr bgcolor='<?php  echo $Title_bgcolor?>'>
    <td width="10" height="25" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
    <td width="94" align="center" class="A1111">统计分类</td>
    <td width="94" align="center" class="A1101">生产日期</td>
    <td align="center" class="A1101">生产备注</td>
    <td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  <tr>
    <td width="10" class="A0010" height="25">&nbsp;</td>
    <td align="center" valign="middle" class="A0111"><select name="Tid" id="Tid">
	<?php 
	$typeResult = mysql_query("SELECT Id,Remark  FROM $DataIn.sc1_counttype WHERE Estate=1 ORDER BY Id",$link_id);
	if ($typeRow = mysql_fetch_array($typeResult)){
		do{
			$typeValue=$typeRow["Id"];
			$Remark=$typeRow["Remark"];
			$Tid=$Tid==""?$typeValue:$Tid;
			if($Tid==$typeValue){
				echo"<option value='$typeValue' selected>$Remark</option>";
				}
			else{
				echo"<option value='$typeValue'>$Remark</option>";
				}
			}while($typeRow = mysql_fetch_array($typeResult));
		}
	?>
    </select></td>
    <td align="center" valign="middle" class="A0101"><input name="scDate" type="text" id="scDate" value="<?php  echo date("Y-m-d")?>" size="8" maxlength="10" onfocus="WdatePicker()" readonly></td>
    <td align="center" class="A0101"><input name="Remark" type="text" id="Remark" size="125" class="INPUT0100"></td>
    <td width="10" class="A0001">&nbsp;</td>
  </tr>
  <tr>
    <td width="10" height="25" class="A0010">&nbsp;</td>
    <td colspan="3" align="center" valign="bottom">登记明细资料
        <input name="TempValue" type="hidden" id="TempValue">
    <input name='AddIds' type='hidden' id="AddIds"></td>
    <td width="10" class="A0001">&nbsp;</td>
  </tr>
</table>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="10" class="A0010" bgcolor="#FFFFFF" height="25">&nbsp;</td>
		<td class="A1111" width="40" align="center">操作</td>
		<td class="A1101" width="40" align="center">序号</td>
		<td class="A1101" width="90" align="center">订单流水号</td>
		<td class="A1101" width="100" align="center">PO</td>
		<td class="A1101" width="350" align="center">产品名称</td>
		<td class="A1101" width="60" align="center">工序总数</td>
		<td class="A1101" width="60" align="center">完成数量</td>
		<td class="A1101" width="60" align="center">剩余数量</td>
		<td class="A1101" width="80" align="center">本次登记</td>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" class="A0010" height="270">&nbsp;</td>
		<td colspan="9" align="center" class="A0111">
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
	if(ListTable.rows.length<1){
		alert("没有设置登记的数据!");return false;
		}
	else{
		var StockValues="";
		//读取加入的数据
		var obj=document.getElementsByName("scQty"); 
		for(i=0;i <obj.length;i++){
			if(StockValues==""){
				StockValues=ListTable.rows[i].cells[2].innerText+"!"+obj[i].value;
				}
			else{
				StockValues=StockValues+"|"+ListTable.rows[i].cells[2].innerText+"!"+obj[i].value;
				}
			}
		document.form1.AddIds.value=StockValues;
		document.form1.action="sc_cjtj_save.php";
		document.form1.submit();
		}
	}
function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}

function Outdepot(thisE,UnreceiveQty){
	var oldValue=document.form1.TempValue.value;
	var thisValue=thisE.value;
	var CheckSTR=fucCheckNUM(thisValue,"");
	if(CheckSTR==0){
		alert("不是规范的数字！");
		thisE.value=oldValue;
		return false;
		}
	else{
		if((thisValue>UnreceiveQty) || thisValue==0){
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
//序号重置
function ShowSequence(TableTemp){
	for(i=0;i<TableTemp.rows.length;i++){ 
  		var j=i+1
		TableTemp.rows[i].cells[1].innerText=j; 
		}
	}   
function SearchRecord(){
	var num=Math.random(); 
	//读取分类ID
	var Jid=document.getElementById('Tid').value;
	BackStockId=window.showModalDialog("sc_cjtj_s1.php?r="+num+"&tSearchPage=sc_cjtj&fSearchPage=sc_cjtj&SearchNum=2&Action=2&Jid="+Jid,"BackStockId","dialogHeight =500px;dialogWidth=970px;center=yes;scroll=yes");
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
					Message="订单流水号: "+FieldArray[0]+"的资料已在列表!跳过继续！";
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
				oTD.width="41";
				oTD.height="20";
				
				//第二列:序号
				oTD=oTR.insertCell(1);
				oTD.innerHTML=""+tmpNum+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="40";
				//oTD.data=""+FieldArray[6]+"";
				
				//三、订单流水号
				oTD=oTR.insertCell(2);
				oTD.innerHTML=""+FieldArray[0]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="89";
				
				//四：订单PO
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[1]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="99";
				
				//五:产品名称
				oTD=oTR.insertCell(4); 
				oTD.innerHTML=""+FieldArray[2]+"";
				oTD.className ="A0101";
				oTD.width="349";

				//六：工序数量
				oTD=oTR.insertCell(5);
				oTD.innerHTML=""+FieldArray[3]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="59";
				
				//七：完成数量
				oTD=oTR.insertCell(6);
				oTD.innerHTML=""+FieldArray[4]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="59";
				
				//八：剩余数量
				oTD=oTR.insertCell(7);
				oTD.innerHTML=""+FieldArray[5]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="59";
						
				//第十列:登记数量
				oTD=oTR.insertCell(8);
				oTD.innerHTML="<input type='text' name='scQty[]' id='scQty' size='4' class='I0000L' value='"+FieldArray[5]+"' onblur='Outdepot(this,"+FieldArray[5]+")' onfocus='toTempValue(this.value)'>";
				oTD.className ="A0101";
				oTD.width="78";				
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

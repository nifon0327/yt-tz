<?php 
//电信-zxq 2012-08-01
/*
$DataIn.yw1_ordermain
$DataIn.trade_object
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增领料记录");//需处理
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
    <td colspan="6" align="center" class="A0011">领料主单信息</td>
  </tr>
  <tr bgcolor='<?php  echo $Title_bgcolor?>'>
    <td width="10" height="25" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
    <td width="120" align="center" class="A1111">需求单所属客户</td>
    <td width="80" align="center" class="A1101">领料人</td>
    <td width="100" align="center" class="A1101">领料日期</td>
    <td align="center" class="A1101">领料备注</td>
    <td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  <tr>
    <td width="10" class="A0010" height="25">&nbsp;</td>
    <td align="center" valign="middle" class="A0111"><select name="CompanyId" id="CompanyId" onChange="javascript:document.form1.submit();" style="width: 80px;">
        <?php 
			$clientSql =  mysql_query("SELECT M.CompanyId,C.Forshort
			FROM $DataIn.yw1_ordermain M 
			LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId AND C.cSign=$Login_cSign
			WHERE C.Estate=1 GROUP BY M.CompanyId ORDER BY M.CompanyId",$link_id);
			while($clientRow = mysql_fetch_array($clientSql)){
				$thisCompanyId=$clientRow["CompanyId"];
				$Forshort=$clientRow["Forshort"];
				if($CompanyId==$thisCompanyId){
					echo "<option value='$thisCompanyId' selected>$Forshort</option>";
					}
				else{
					echo "<option value='$thisCompanyId'>$Forshort</option>";
					}
				} 
			?>
    </select></td>
    <td class="A0101" align="center"><select name="Materieler" id="Materieler" style="width: 70px;">
        <?php 
			$buyerSql = mysql_query("SELECT S.Number,S.Name 
			FROM $DataPublic.staffmain S WHERE S.JobId='11' and S.Estate=1 ORDER BY S.Number",$link_id);
			if($buyerRow = mysql_fetch_array($buyerSql)){			
				do{
					$Number=$buyerRow["Number"];
					$Name=$buyerRow["Name"];					
					echo "<option value='$Number'>$Name</option>";
					}while($buyerRow = mysql_fetch_array($buyerSql));
				} 
			?>
    </select></td>
    <td align="center" class="A0101"><input name="llDate" type="text" id="llDate" value="<?php  echo date("Y-m-d")?>" size="8" maxlength="10"></td>
    <td align="center" class="A0101"><input name="Remark" type="text" id="Remark" size="90" class="INPUT0100"></td>
    <td width="10" class="A0001">&nbsp;</td>
  </tr>
  <tr>
    <td width="10" height="25" class="A0010">&nbsp;</td>
    <td colspan="4" align="center" valign="bottom">领料明细资料
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
		<td class="A1101" width="95" align="center">需求流水号</td>
		<td class="A1101" width="55" align="center">配件ID</td>
		<td class="A1101" align="center" width="370">配件名称</td>
		<td class="A1101" width="65" align="center">订单数量</td>
		<td class="A1101" width="65" align="center">未领数量</td>
		<td class="A1101" width="65" align="center">可领数量</td>
		<td class="A1101" width="85" align="center">本次领料</td>
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
		alert("没有设置领料的数据!");return false;
		}
	else{
		var StockValues="";
		//读取加入的数据
		var obj=document.getElementsByName("llQty"); 
		for(i=0;i <obj.length;i++){
			if(StockValues==""){
				StockValues=ListTable.rows[i].cells[2].innerText+"!"+ListTable.rows[i].cells[3].innerText+"!"+obj[i].value;
				}
			else{
				StockValues=StockValues+"|"+ListTable.rows[i].cells[2].innerText+"!"+ListTable.rows[i].cells[3].innerText+"!"+obj[i].value;
				}
			} 
		document.form1.AddIds.value=StockValues;
		document.form1.action="ck_ll_save.php";
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
	var Bid=document.getElementById('CompanyId').value;
	var num=Math.random();  
	BackStockId=window.showModalDialog("ck_ll_s1.php?r="+num+"&Bid="+Bid+"&tSearchPage=ck_ll&fSearchPage=ck_ll&SearchNum=2&Action=2","BackStockId","dialogHeight =500px;dialogWidth=970px;center=yes;scroll=yes");
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
				oTD.width="39";
				oTD.height="20";
				
				//第二列:序号
				oTD=oTR.insertCell(1);
				oTD.innerHTML=""+tmpNum+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="39";
				//oTD.data=""+FieldArray[6]+"";
				
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
				oTD.width="55";
				
				//五:配件名称
				oTD=oTR.insertCell(4); 
				oTD.innerHTML=""+FieldArray[2]+"";
				oTD.className ="A0101";
				oTD.width="366";

				//六：订单数量
				oTD=oTR.insertCell(5);
				oTD.innerHTML=""+FieldArray[3]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="64";

				//七：未领数量
				oTD=oTR.insertCell(6);
				oTD.innerHTML=""+FieldArray[4]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="64";
			
				//八：可领数量
				oTD=oTR.insertCell(7);
				oTD.innerHTML=""+FieldArray[5]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="64";
			
				//第十列:可领数量
				oTD=oTR.insertCell(8);
				oTD.innerHTML="<input type='text' name='llQty[]' id='llQty' size='4' class='I0000L' value='"+FieldArray[5]+"' onblur='Outdepot(this,"+FieldArray[5]+")' onfocus='toTempValue(this.value)'>";
				oTD.className ="A0101";
				oTD.width="83";				
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

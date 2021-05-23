<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增收款记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,cwSign,0";
//步骤3：
$tableWidth=900;$tableMenuS=500;
$CustomFun="<span onclick='SearchRecord()' $onClickCSS>加入Invoice</span>&nbsp;";
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr valign="bottom">
    <td colspan="7" align="center" class="A0011">收款单资料</td>
  </tr>
  <tr bgcolor='<?php  echo $Title_bgcolor?>'>
    <td width="10" height="25" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
    <td width="120" align="center" class="A1111">客户</td>
    <td width="80" align="center" class="A1101">收款日期</td>
    <td width="100" align="center" class="A1101">手续费</td>
  <td width="100" align="center" class="A1101">结付银行</td>
    <td align="center" class="A1101">TT备注</td>
    <td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  <tr>
    <td width="10" class="A0010" height="25">&nbsp;</td>
    <td align="center" valign="middle" class="A0111">
	  <select name="CompanyId" id="CompanyId" onChange="javascript: document.form1.submit();" style="width: 100px;">
        <?php 
			$clientSql =  mysql_query("SELECT M.CompanyId,C.Forshort
			FROM $DataIn.ch1_shipmain M 
			LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
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
    <td class="A0101" align="center"><input name="PayDate" type="text" id="PayDate" class="INPUT0000" value="<?php  echo date("Y-m-d")?>" size="8" maxlength="10" onfocus="WdatePicker()" readonly></td>
    <td align="center" class="A0101"><input name="Handingfee" type="text" class="INPUT0000" id="Handingfee" size="10" maxlength="10" value="0"></td>
    <td align=center class='A0101'> <?php 
       include "../model/selectbank1.php";
      ?></td>
    <td align="center" class="A0101"><input name="Remark" type="text" id="Remark" size="50" class="INPUT0000"></td>
    <td width="10" class="A0001">&nbsp;</td>
  </tr>
  <tr>
    <td width="10" height="25" class="A0010">&nbsp;</td>
    <td colspan="5" align="center" valign="bottom">收款明细
      <input name="TempValue" type="hidden" id="TempValue">
    <input name='AddIds' type='hidden' id="AddIds"></td>
    <td width="10" class="A0001">&nbsp;</td>
  </tr>
</table>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
	<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="10" class="A0010" bgcolor="#FFFFFF" height="25">&nbsp;</td>
		<td class="A1111" width="60" align="center">操作</td>
		<td class="A1101" width="60" align="center">序号</td>
		<td class="A1101" width="100" align="center">出货日期</td>
		<td class="A1101" width="80" align="center">出货流水号</td>
		<td class="A1101" align="center" width="260">Invoice</td>
		<td class="A1101" width="100" align="center">出货总额</td>
		<td class="A1101" width="100" align="center">未收金额</td>
		<td class="A1101" width="100" align="center">本次收款</td>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" class="A0010" height="270">&nbsp;</td>
		<td colspan="8" align="center" class="A0111">
		<div style="width:880;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='880' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTable" style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
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
	var Message="";
	var Handingfee=document.form1.Handingfee.value;
	var CheckSTR=fucCheckNUM(Handingfee,"Price");
	if(ListTable.rows.length<1){
		Message="没有设置领料的数据!";
		}
	if(CheckSTR==0){
		Message="手续费格式不规范!";		
		}
	var PayDate=document.form1.PayDate.value;
	if(PayDate==""){
		Message="收款日期不对!";
		}
	if(Message!=""){
		alert(Message);
		return false;
		}
	else{
		var DataValues="";
		var obj=document.getElementsByName("thisAmount[]"); 
		for(i=0;i <obj.length;i++){
			var unValue=Number(ListTable.rows[i].cells[6].innerText);
			var newValue=Number(obj[i].value);
			if(unValue==newValue){
				var cwSign=0;}
			else{
				var cwSign=2;}			
			if(DataValues==""){
				DataValues=ListTable.rows[i].cells[1].data+"!"+obj[i].value+"!"+cwSign;
				}
			else{
				DataValues=DataValues+"|"+ListTable.rows[i].cells[1].data+"!"+obj[i].value+"!"+cwSign;
				}
			}

		document.form1.AddIds.value=DataValues;
		document.form1.action="cw_orderin_save.php";
		document.form1.submit();
		}
	}

function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}

function Outdepot(thisE,UnreceiveQty){
	var oldValue=document.form1.TempValue.value;
	var thisValue=thisE.value;
	var CheckSTR=fucCheckNUM(thisValue,"Price");
	if(CheckSTR==0){
		alert("格式不规范！"+thisValue);
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
	BackData=window.showModalDialog("cw_orderin_s1.php?r="+num+"&Bid="+Bid+"&tSearchPage=cw_orderin&fSearchPage=cw_orderin&SearchNum=2&Action=2","BackData","dialogHeight =500px;dialogWidth=950px;center=yes;scroll=yes");
	if (BackData){
  		var Rowstemp=BackData.split("``");
		var Rowslength=Rowstemp.length;
		for(var i=0;i<Rowslength;i++){
			var Message="";			
			var FieldArray=Rowstemp[i].split("^^");
			//过滤相同的产品订单ID号
			for(var j=0;j<ListTable.rows.length;j++){
				var StockIdtemp=ListTable.rows[j].cells[1].data;//隐藏ID号存于操作列	
				if(FieldArray[0]==StockIdtemp){//如果流水号存在
					Message="出货单: "+FieldArray[3]+"的资料已在列表!跳过继续！";
					break;
					}
				}
			if(Message==""){
				oTR=ListTable.insertRow(ListTable.rows.length);
				tmpNum=oTR.rowIndex+1;
				//第一列:操作
				oTD=oTR.insertCell(0);
				oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>";
				oTD.onmousedown=function(){
					window.event.cancelBubble=true;
					};
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="59";
				oTD.height="20";
				
				//第二列:序号
				oTD=oTR.insertCell(1);
				oTD.innerHTML=""+tmpNum+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="60";
				oTD.data=""+FieldArray[0]+"";
				
				//三、出货日期
				oTD=oTR.insertCell(2);
				oTD.innerHTML=""+FieldArray[1]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="100";
				
				//四：出货流水号
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[2]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="80";
				
				//五:Invoice
				oTD=oTR.insertCell(4); 
				oTD.innerHTML=""+FieldArray[3]+"";
				oTD.className ="A0101";
				oTD.width="260";

				//六：出货总额
				oTD=oTR.insertCell(5);
				oTD.innerHTML=""+FieldArray[4]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="100";

				//七：未收金额
				oTD=oTR.insertCell(6);
				oTD.innerHTML="<div class='redB'>"+FieldArray[5]+"</div>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="100";
			
				//第八列:本次收款
				oTD=oTR.insertCell(7);
				oTD.innerHTML="<input type='text' name='thisAmount[]' id='thisAmount[]' size='10' class='INPUT0000' value='"+FieldArray[5]+"' onblur='Outdepot(this,"+FieldArray[5]+")' onfocus='toTempValue(this.value)'>";
				oTD.className ="A0101";
				oTD.width="107";				
				}
			else{
				alert(Message);
				}//if(Message=="")
			}//for(var i=0;i<Rowslength;i++)
		}//if (BackData)
	else{
		alert("没有选取数据!");return true;
		}
	}
</script>

<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增供应商备品记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=900;$tableMenuS=500;
$CustomFun="<span onclick='SearchRecord()' $onClickCSS>加入备品配件</span>&nbsp;";
$CheckFormURL="thisPage";

$SelectCode="<select name='CompanyId' id='CompanyId' onChange='javascript:document.form1.submit();' style='width: 150px;'>";
//供应商:有退换且没有补完货
$GYS_Sql ="SELECT P.CompanyId,P.Forshort,P.Letter 
	FROM $DataIn.trade_object P	
	WHERE 1 GROUP BY P.CompanyId ORDER BY P.Letter";
$GYS_Result = mysql_query($GYS_Sql);
if($GYS_Myrow = mysql_fetch_array($GYS_Result)){
	$oldLetter="";
	do{
		$ProviderTemp=$GYS_Myrow["CompanyId"];
		$CompanyId=$CompanyId==""?$ProviderTemp:$CompanyId;
		$Forshort=$GYS_Myrow["Forshort"];
		$Letter=$GYS_Myrow["Letter"];
		if($oldLetter==$Letter){
			$Forshort='&nbsp;&nbsp;&nbsp;&nbsp;'.$Forshort;
			}
		else{
			$Forshort=$Letter.'-'.$Forshort;
			$oldLetter=$Letter;
			}
		if($ProviderTemp==$CompanyId){
			$SelectCode.="<option value='$ProviderTemp' selected>$Forshort</option>";
			}
		else{
			$SelectCode.="<option value='$ProviderTemp'>$Forshort</option>";
			}
		}while ( $GYS_Myrow = mysql_fetch_array($GYS_Result));
	} 
$SelectCode.="</select> 送货单号<input name='BillNumber' type='text' id='BillNumber' size='15'>";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="10" class="A0010" bgcolor="#FFFFFF" height="25">&nbsp;</td>
		<td class="A1111" width="50" align="center">操作</td>
		<td class="A1101" width="50" align="center">序号</td>
		<td class="A1101" width="60" align="center">配件ID</td>
		<td class="A1101" width="330" align="center">配件名称</td>
		<td class="A1101" width="60" align="center">补仓数量</td>
		<td class="A1101" width="330" align="center">备注</td>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" class="A0010" height="432">&nbsp;</td>
		<td colspan="6" align="center" class="A0111">
		<div style="width:880;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='880' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTable">
			<input name="TempValue" type="hidden" id="TempValue">
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
		Message="没有设置补仓的数据!";
		}
	if(Message!=""){
		alert(Message);return false;
		}
	else{
		document.form1.action="ck_gp_save.php";
		document.form1.submit();
		}
	}

function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}
function Indepot(thisE,SumQty){
	var oldValue=document.form1.TempValue.value;
	var thisValue=thisE.value;
	if(thisValue!=""){
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
	var num=Math.random();  
	BackStuffId=window.showModalDialog("ck_gp_s1.php?r="+num+"&Jid="+Jid+"&tSearchPage=ck_gp&fSearchPage=ck_gp&SearchNum=2&Action=2","BackStuffId","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
	if (BackStuffId){
  		var Rowstemp=BackStuffId.split("``");
		var Rowslength=Rowstemp.length;
		for(var i=0;i<Rowslength;i++){
			var Message="";			
			var FieldArray=Rowstemp[i].split("^^");//$StuffId."^^".$StuffCname."^^".$unQty;
			//过滤相同的产品订单ID号
			for(var j=0;j<ListTable.rows.length;j++){
				var StuffIdtemp=ListTable.rows[j].cells[0].data;//隐藏ID号存于操作列	
				if(FieldArray[0]==StuffIdtemp){//如果流水号存在
					Message="配件: "+FieldArray[1]+"的资料已在列表!跳过继续！";
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
				oTD.data=""+FieldArray[0]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="50";
				oTD.height="20";
				
				//第二列:序号
				oTD=oTR.insertCell(1);
				oTD.innerHTML=""+tmpNum+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="50";
				
				//三、配件ID
				oTD=oTR.insertCell(2);
				//oTD.innerHTML=""+FieldArray[0]+"";
				oTD.innerHTML="<input type='text' name='thStuffId[]' id='thStuffId' size='4' class='I0000C' value='"+FieldArray[0]+"' readonly>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="60";
				
				//四：配件名称
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[1]+"";
				oTD.className ="A0101";
				oTD.width="329";
				
				//五:补仓数量
				oTD=oTR.insertCell(4); 
				oTD.innerHTML="<input type='text' name='thQTY[]' id='thQTY' size='4' class='I0000L' value='"+"0"+"' onblur='Indepot(this,"+FieldArray[2]+")' onfocus='toTempValue(this.value)'>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="60";
				
				//六:原因
				oTD=oTR.insertCell(5);
				oTD.innerHTML="<input type='text' name='thRemark[]' id='thRemark' class='I0000L' size='57' value=''>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="326";				
				}
			else{
				alert(Message);
				}//if(Message=="")
			}//for(var i=0;i<Rowslength;i++)
		}//if (BackStuffId)
	else{
		alert("没有选取数据!");return true;
		}
	}
</script>

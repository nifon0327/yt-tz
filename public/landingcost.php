<?php 
//电信-ZX  2012-08-01
//include "../basic/chksession.php";
include "../basic/parameter.inc";
$checkCurrency=mysql_fetch_array(mysql_query("SELECT Rate FROM $DataPublic.currencydata WHERE Id=2 ORDER BY Id LIMIT 1",$link_id));
$USDRate=sprintf("%.4f",$checkCurrency["Rate"]);
$RMBRate=1.0000;
include "../model/subprogram/sys_parameters.php";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>产品售价计算器</title>
<script src='../model/pagefun.js' type=text/javascript></script>
<link rel='stylesheet' href='../model/tl/read_line.css'><link rel='stylesheet' href='../model/css/sharing.css'><style type="text/css">
<style type="text/css">
<!--
.style1 {color: #999999}
td{
	height:25px;
	line-height:25px;}
	.I0000RB{
	text-align: right;
	border: none;
	background-color: #DFF4D5;
	font-size: 12px;
}
.inputText{
	BORDER-TOP-WIDTH: 0px;
	BORDER-BOTTOM-WIDTH: 0px;
	BORDER-LEFT-WIDTH: 0px;
	BORDER-RIGHT-WIDTH: 0px;
	text-indent: 10px;
	COLOR: #669933;
	width:290px;
	BACKGROUND:#FFFFFF url(../images/spacer.gif) no-repeat 0px 0px;
	}
.inputValue{
	BORDER-TOP-WIDTH: 0px;
	BORDER-BOTTOM-WIDTH: 0px;
	BORDER-LEFT-WIDTH: 0px;
	BORDER-RIGHT-WIDTH: 0px;
	text-indent: 10px;
	COLOR: #669933;
	width:120px;
	BACKGROUND:#FFFFFF url(../images/spacer.gif) no-repeat 0px 0px;
	text-align: right;
	}
.readonlyText{
	BORDER-TOP-WIDTH: 0px;
	BORDER-BOTTOM-WIDTH: 0px;
	BORDER-LEFT-WIDTH: 0px;
	BORDER-RIGHT-WIDTH: 0px;
	text-indent: 10px;
	COLOR: #669933;
	width:120px;
	BACKGROUND:#C1FFC1 url(../images/spacer.gif) no-repeat left top;
	text-align: right;
	}
-->
</style>
</head>

<body>
<form name="form1" method="post" action=""><input name="TempValue" type="hidden" id="TempValue">
<input name="USDRate" type="hidden" id="USDRate" value="<?php  echo $USDRate?>">
<input name="hzRate" type="hidden" id="hzRate" value="<?php  echo $HzRate?>">
<table border="0" cellpadding="0" cellspacing="0" style="width:600px;TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
  <tr align="center" bgcolor="#999999">
    <td height="35" class="A1111">Loading Cost</td>
    </tr>
</table>
<table width="600px" border="0" cellpadding="0" cellspacing="0" id="ListTable" style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
  <tr bgcolor="#CCCCCC">
    <td rowspan="51" align="center" class="A0011" style="width:80px;height:25px"><p>A</p><p onClick="AddRow()" style="CURSOR: pointer;">[+]</p></td>
    <td align="center" class="A0101" style="width:80px">Item</td>
    <td align="center" class="A0101" style="width:300px">Product</td>
    <td align="center" class="A0101" style="width:135px">Price</td>
    </tr>
<?php 
for($i=1;$i<11;$i++){
	echo"
	<tr bgcolor='#FFFFFF'>
		<td bgcolor='#CCCCCC' align='center' class='A0101' style='height:25px'>$i</td>
		<td class='A0101'><input name='StuffName' type='text' id='uStuffName' class='inputText'></td>
		<td class='A0101'><input name='Price[]' type='text' id='Price' class='inputValue' onFocus='toTempValue(this.value)' onBlur='Indepot(this)'></td>
	  </tr>";
  }
?>
  
  </table>
<table width="600px" border="0" cellpadding="0" cellspacing="0" style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
  <tr bgcolor="#cccccc">
    <td width="80" height="25" align="center" bgcolor="#cccccc" class="A0111">&nbsp;</td>
  <td width="80" align="center" bgcolor="#cccccc" class="A0101">Total</td>
    <td width="300" align="center" bgcolor="#cccccc" class="A0101">&nbsp;</td>
    <td width="135" align="center" bgcolor="#C1FFC1" class="A0101"><input name="Amount" type="text" id="Amount" class="readonlyText" readonly></td>
  </tr>
</table>
 <br>
 <table width="600px" border="0" cellpadding="0" cellspacing="0" style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
   <tr rowspan="2" bgcolor="#CCCCCC">
     <td rowspan="2" align="center" bgcolor="#CCCCCC" class="A1111" style="height:25px;width:80px">B</td>
     <td bgcolor="#CCCCCC" class="A1101" style="width:380px">Weight</td>
     <td align="center" bgcolor="#FFFFFF" class="A1101"  style="width:135px"><input name="ValueB[]" type="text" id="ValueB" class="inputValue"  onFocus='toTempValue(this.value)' onBlur='Indepot(this)'></td>
   </tr>
   <tr bgcolor="#CCCCCC">
     <td bgcolor="#CCCCCC" class="A0101">Volume</td>
     <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="ValueB[]" type="text" id="ValueB" class="inputValue"  onFocus='toTempValue(this.value)' onBlur='Indepot(this)'></td>
   </tr>
 </table>
 <br>
 <table width="600px" border="0" cellpadding="0" cellspacing="0" style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
   <tr rowspan="2" bgcolor="#CCCCCC">
     <td rowspan="3" align="center" bgcolor="#CCCCCC" class="A1111" style="height:25px;width:80px">C</td>
     <td valign="middle" bgcolor="#CCCCCC" class="A1101" style="width:380px">FOB</td>
     <td align="center" bgcolor="#FFFFFF" class="A1101"  style="width:135px"><input name="ValueC[]" type="text" id="ValueC" class="inputValue"  onFocus='toTempValue(this.value)' onBlur='Indepot(this)'</td>
   </tr>
   <tr bgcolor="#CCCCCC">
     <td valign="middle" bgcolor="#CCCCCC" class="A0101">空运价格=重量与体积较大值者*$0.3</td>
     <td align="center" bgcolor="#C1FFC1" class="A0101"><input name="ValueC[]" type="text" id="ValueC" class="readonlyText" readonly></td>
   </tr>
   <tr bgcolor="#CCCCCC">
     <td valign="middle" bgcolor="#CCCCCC" class="A0101">关税=(产品总额+FOB金额)*6%</td>
     <td align="center" bgcolor="#C1FFC1" class="A0101"><input name="ValueC[]" type="text" id="ValueC" class="readonlyText" readonly></td>
   </tr>
 </table>
 <br>
 <table width="600px" border="0" cellpadding="0" cellspacing="0" style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
   <tr rowspan="2" bgcolor="#999999">
     <td align="center" class="A1111" style="height:25px;width:80px">D</td>
     <td class="A1101" style="width:380px">&nbsp;</td>
     <td align="center" bgcolor="#C1FFC1" class="A1101"  style="width:135px"><input name="ValueD" type="text" id="ValueD" class="readonlyText" readonly></td>
   </tr>
 </table>
</form>
</body>
</html>
<script>
function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}
function AddRow(){
	oTR=ListTable.insertRow(ListTable.rows.length);
	oTR.style.backgroundColor="#FFFFFF";
	tmpNum=oTR.rowIndex+1;
	ItemNum=tmpNum-1;
	//第一列:序号
	oTD=oTR.insertCell(0);
	oTD.innerHTML=ItemNum;
	oTD.className ="A0101";
	oTD.align="center";
	oTD.height="25";
	oTD.style.backgroundColor="#CCCCCC";
				
	//第二列:配件名称
	oTD=oTR.insertCell(1);
	oTD.innerHTML="<input name='StuffName' type='text' id='uStuffName' class='inputText'>";
	oTD.className ="A0101";
	oTD.align="center";
				
	//三、单价
	oTD=oTR.insertCell(2);
	oTD.innerHTML="<input name='Price[]' type='text' id='Price' class='inputValue' onFocus='toTempValue(this.value)' onBlur='Indepot(this)'>";
	oTD.className ="A0101";
	oTD.align="center";
				
	}
function toProfit(thisE){
	var oldValue=document.form1.TempValue.value;
	var thisValue=thisE.value;//售价
	var CheckSTR=fucCheckNUM(thisValue,"Price");
	if(CheckSTR==0 || thisValue==0){
		alert("售价格式不规范！");
		thisE.value=oldValue;
		return false;
		}
	else{
		//计算毛利率=1-(USD成本+RMB成本+行政比率费用)/(售价*汇率)
		var USDRate=Number(document.form1.USDRate.value);
		var UsdAmount=Number(document.form1.UsdAmount.value);
		var RmbAmount=Number(document.form1.RmbAmount.value);
		var SalePrice=1-(UsdAmount+RmbAmount)/(thisValue*USDRate);
		Profit=FormatNumber(SalePrice,2);
		document.form1.Profit.value=Profit;
		}
	}

function toSalePrice(thisE){
	var oldValue=document.form1.TempValue.value;
	var thisValue=thisE.value;//毛利率
	var CheckSTR=fucCheckNUM(thisValue,"Price");
	if(CheckSTR==0 || thisValue==0){
		alert("毛利格式不规范！");
		thisE.value=oldValue;
		return false;
		}
	else{
		//计算售价=(USD成本+RMB成本+行政比率费用)/售价(1-毛利率)
		var USDRate=Number(document.form1.USDRate.value);
		var UsdAmount=Number(document.form1.UsdAmount.value);
		var RmbAmount=Number(document.form1.RmbAmount.value);
		var SalePrice=(UsdAmount+RmbAmount)/((1-thisValue)*USDRate);
		SalePrice=FormatNumber(SalePrice,4);
		document.form1.SalePrice.value=SalePrice;
		}
	}

function Indepot(thisE){
	var oldValue=document.form1.TempValue.value;	//此文本框的原值
	var thisValue=thisE.value;						//此文本框现值
	//重新计算总价
	var P=document.getElementsByName("Price[]");		//产品价格
	var VB=document.getElementsByName("ValueB[]");		//重量
	var VC=document.getElementsByName("ValueC[]");		//重量
	var TempAmount=0;
	var Len=ListTable.rows.length*1-1;
	for(var j=0;j<Len;j++){						
		//alert(j);
		TempAmount+=P[j].value*1;		//对应数量，以/来拆分前后两部分
		}
	document.form1.Amount.value=TempAmount==0?"":TempAmount.toFixed(2); //如果不为0，写入产品总金额
	var VB0=VB[0].value*1;
	var VB1=VB[1].value*1;
	
	var tempVC1=VB0>VB1?VB0*0.3:VB1*0.3;//空运金额
	VC[1].value=tempVC1==0?"":tempVC1.toFixed(2);	//如果不为0，写入
	
	var tempVC2=(TempAmount+VC[0].value*1)*0.06;//关税金额
	VC[2].value=tempVC2==0?"":tempVC2.toFixed(2);	//如果不为0，写入
	
	var tempCost=TempAmount*1+VC[0].value*1+tempVC1*1+tempVC2*1;
	document.form1.ValueD.value=tempCost==0?"":tempCost.toFixed(2);
	//
	/*
		var tempV=document.getElementsByName("Value"+Row);
		var tempValueSum=1;//行的小计初始值
		var Msg="";
			if(tempV[0].value!="" && tempV[1].value!=""){//单价和比率不为空再做计算
			
				var QtyRate=tempV[1].value;
				var QtyRate0=0;
				var QtyRate1="";
				QtyRateArray = QtyRate.split("/");
				if(QtyRateArray.length>0 || QtyRateArray.length<3){
					QtyRate0=QtyRateArray[0];
					var CheckQtyRate0=fucCheckNUM(QtyRate0,"");
					if(CheckQtyRate0==0 || QtyRate0==0){
						Msg="对应数量格式不规范！";
						}
					if(QtyRateArray.length==2){
						QtyRate1=QtyRateArray[1];
						var CheckQtyRate1=fucCheckNUM(QtyRate1,"");
						if(CheckQtyRate1==0 || QtyRate1==0){
							Msg="对应数量格式不规范！";
							}
						}
					}
				var Price=tempV[0].value;
				var CheckPrice=fucCheckNUM(Price,"Price");
				if(CheckPrice==0){
					Msg="单价的格式不规范！";
					}
				if(Msg!=""){
					alert(Msg);
					tempA[Row].value="";
					}
				else{
					tempValueSum=Price*QtyRate0*tempV[2].value;
					if(QtyRate1!=""){
						tempValueSum=tempValueSum/QtyRate1;
						}
					tempA[Row].value=FormatNumber(tempValueSum,2);
					}
				}
		var tempUsdAmount=0;
		var tempRmbAmount=0;
		//////////////USD成本///////////////////
		for(i=0;i<2;i++){
			tempUsdAmount=tempUsdAmount*1+tempA[i].value*1;
			}
		tempUsdAmount=FormatNumber(tempUsdAmount,4);
		if(tempUsdAmount>0){
		document.form1.UsdAmount.value=tempUsdAmount;}
		//////////////RMB成本+0。07行政费用/////
		for(i=2;i<tempA.length;i++){
			if(tempA[i].value!=""){
				tempRmbAmount=tempRmbAmount*1+tempA[i].value*1;
				}
			}
		tempRmbAmount=FormatNumber(tempRmbAmount*(1+hzRate),4);
		if(tempRmbAmount>0){
			document.form1.RmbAmount.value=tempRmbAmount;
			}
		*/
	}
</script>

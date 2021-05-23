<?php   
//电信---yang 20120801
include "../basic/chksession.php";
include "../basic/parameter.inc";
$checkCurrency=mysql_fetch_array(mysql_query("SELECT Rate FROM $DataPublic.currencydata WHERE Id=2 ORDER BY Id LIMIT 1",$link_id));
$USDRate=sprintf("%.4f",$checkCurrency["Rate"]);
$RMBRate=1.0000;
include "../model/subprogram/sys_parameters.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>产品售价计算器</title>
<script src='../model/pagefun.js' type=text/javascript></script>
<?php   
echo"<link rel='stylesheet' href='../model/css/read_line.css'><link rel='stylesheet' href='../model/css/sharing.css'>";
?>
<style type="text/css">
<!--
.style1 {color: #999999}
-->
</style>
</head>

<body>
<form name="form1" method="post" action=""><input name="TempValue" type="hidden" id="TempValue">
<input name="USDRate" type="hidden" id="USDRate" value="<?php    echo $USDRate?>">
<input name="hzRate" type="hidden" id="hzRate" value="<?php    echo $HzRate?>">
<table border="0" cellpadding="0" cellspacing="0" style="width:680px;TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
  <tr align="center" bgcolor="#999999">
    <td height="35" class="A1111">产品售价计算器</td>
    </tr>
</table>
<table id="ListTable" border="0" cellpadding="0" cellspacing="0" style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
  <tr bgcolor="#CCCCCC">
    <td style="width:60px;height:25px" align="center" class="A0111">分类</td>
    <td style="width:60px" align="center" class="A0101">序号</td>
    <td style="width:232px" align="center" class="A0101">配件名称</td>
    <td style="width:80px" align="center" class="A0101">单价</td>
    <td style="width:80px" align="center" class="A0101">对应数量</td>
	<td style="width:80px" align="center" class="A0101">汇率</td>
    <td style="width:80px" align="center" class="A0101">产品成本</td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td rowspan="2" align="center" class="A0111">USD配件</td>
    <td align="center" class="A0101" style="height:25px">1</td>
  <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="StuffName0[]" type="text" id="uStuffName" size="38" cLASS="I0000LF"></td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value0[]" type="text" id="Value0" size="5" cLASS="I0000RF" onFocus='toTempValue(this.value)' onBlur='Indepot(this,0)'></td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value0[]" type="text" id="Value0" size="5" cLASS="I0000RF" onFocus='toTempValue(this.value)' onBlur='Indepot(this,0)'></td>
     <td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Value0[]" type="text" id="Value0" size="8" cLASS="I0000RB" value="<?php    echo $USDRate?>" readonly></td>
	<td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Amount[]" type="text" id="Amount" size="8" class="I0000RB" readonly></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td align="center" class="A0101">2</td>
  <td align="center" bgcolor="#FFFFFF" class="A0101"  style="height:25px"><input name="StuffName0[]" type="text" id="uStuffName" size="38" cLASS="I0000LF"></td>
  <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value1[]" type="text" id="Value1" size="5" cLASS="I0000RF"  onFocus='toTempValue(this.value)' onBlur='Indepot(this,1)'></td>
  <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value1[]" type="text" id="Value1" size="5" cLASS="I0000RF"  onFocus='toTempValue(this.value)' onBlur='Indepot(this,1)'></td>
  <td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Value1[]" type="text" id="Value1" size="8" cLASS="I0000RB" value="<?php    echo $USDRate?>" readonly></td>
  <td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Amount[]" type="text" id="Amount" size="8" class="I0000RB" readonly></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td rowspan="50" align="center" class="A0111"><p>RMB配件</p>
      <p onclick="AddRow()" style="CURSOR: pointer;">[+]</p></td>
  <td align="center" class="A0101" style="height:25px">1</td>
  <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="StuffName0[]" type="text" id="uStuffName" size="38" cLASS="I0000LF"></td>
  <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value2[]" type="text" id="Value2" size="5" cLASS="I0000RF"  onFocus='toTempValue(this.value)' onBlur='Indepot(this,2)'></td>
  <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value2[]" type="text" id="Value2" size="5" cLASS="I0000RF"  onFocus='toTempValue(this.value)' onBlur='Indepot(this,2)'></td>
  <td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Value2[]" type="text" id="Value2" size="8" cLASS="I0000RB" value="<?php    echo $RMBRate?>" readonly></td>
  <td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Amount[]" type="text" id="Amount" size="8" class="I0000RB" readonly></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td align="center" class="A0101" style="height:25px">2</td>
  <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="StuffName0[]" type="text" id="uStuffName" size="38" cLASS="I0000LF"></td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value3[]" type="text" id="Value3" size="5" cLASS="I0000RF"  onFocus='toTempValue(this.value)' onBlur='Indepot(this,3)'></td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value3[]" type="text" id="Value3" size="5" cLASS="I0000RF"  onFocus='toTempValue(this.value)' onBlur='Indepot(this,3)'></td>
	<td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Value3[]" type="text" id="Value3" size="8" cLASS="I0000RB" value="<?php    echo $RMBRate?>" readonly></td>
    <td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Amount[]" type="text" id="Amount" size="8" class="I0000RB" readonly></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td align="center" class="A0101" style="height:25px">3</td>
  <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="StuffName0[]" type="text" id="uStuffName" size="38" cLASS="I0000LF"></td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value4[]" type="text" id="Value4" size="5" cLASS="I0000RF"  onFocus='toTempValue(this.value)' onBlur='Indepot(this,4)'></td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value4[]" type="text" id="Value4" size="5" cLASS="I0000RF"  onFocus='toTempValue(this.value)' onBlur='Indepot(this,4)'></td>
	<td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Value4[]" type="text" id="Value4" size="8" cLASS="I0000RB" value="<?php    echo $RMBRate?>" readonly></td>
    <td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Amount[]" type="text" id="Amount" size="8" class="I0000RB" readonly></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td align="center" class="A0101" style="height:25px">4</td>
  <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="StuffName0[]" type="text" id="uStuffName" size="38" cLASS="I0000LF"></td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value5[]" type="text" id="Value5" size="5" cLASS="I0000RF"  onFocus='toTempValue(this.value)' onBlur='Indepot(this,5)'></td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value5[]" type="text" id="Value5" size="5" cLASS="I0000RF"  onFocus='toTempValue(this.value)' onBlur='Indepot(this,5)'></td>
	<td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Value5[]" type="text" id="Value5" size="8" cLASS="I0000RB" value="<?php    echo $RMBRate?>" readonly></td>
    <td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Amount[]" type="text" id="Amount" size="8" class="I0000RB" readonly></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td align="center" class="A0101" style="height:25px">5</td>
  <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="StuffName0[]" type="text" id="uStuffName" size="38" cLASS="I0000LF"></td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value6[]" type="text" id="Value6" size="5" cLASS="I0000RF"  onFocus='toTempValue(this.value)' onBlur='Indepot(this,6)'></td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value6[]" type="text" id="Value6" size="5" cLASS="I0000RF" onFocus='toTempValue(this.value)' onBlur='Indepot(this,6)'></td>
	<td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Value6[]" type="text" id="Value6" size="8" cLASS="I0000RB" value="<?php    echo $RMBRate?>" readonly></td>
    <td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Amount[]" type="text" id="Amount" size="8" class="I0000RB" readonly></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td align="center" class="A0101" style="height:25px">6</td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="StuffName0[]" type="text" id="uStuffName" size="38" cLASS="I0000LF"></td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value7[]" type="text" id="Value7" size="5" cLASS="I0000RF" onFocus='toTempValue(this.value)' onBlur='Indepot(this,7)'></td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value7[]" type="text" id="Value7" size="5" cLASS="I0000RF" onFocus='toTempValue(this.value)' onBlur='Indepot(this,7)'></td>
	<td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Value7[]" type="text" id="Value7" size="8" cLASS="I0000RB" value="<?php    echo $RMBRate?>" readonly></td>
    <td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Amount[]" type="text" id="Amount" size="8" class="I0000RB" readonly></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td align="center" class="A0101" style="height:25px">7</td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="StuffName0[]" type="text" id="uStuffName" size="38" cLASS="I0000LF"></td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value8[]" type="text" id="Value8" size="5" cLASS="I0000RF" onFocus='toTempValue(this.value)' onBlur='Indepot(this,8)'></td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value8[]" type="text" id="Value8" size="5" cLASS="I0000RF" onFocus='toTempValue(this.value)' onBlur='Indepot(this,8)'></td>
	<td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Value8[]" type="text" id="Value8" size="8" cLASS="I0000RB" value="<?php    echo $RMBRate?>" readonly></td>
    <td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Amount[]" type="text" id="Amount" size="8" class="I0000RB" readonly></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td align="center" class="A0101" style="height:25px">8</td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="StuffName0[]" type="text" id="uStuffName" size="38" cLASS="I0000LF"></td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value9[]" type="text" id="Value9" size="5" cLASS="I0000RF" onFocus='toTempValue(this.value)' onBlur='Indepot(this,9)'></td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value9[]" type="text" id="Value9" size="5" cLASS="I0000RF" onFocus='toTempValue(this.value)' onBlur='Indepot(this,9)'></td>
	<td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Value9[]" type="text" id="Value9" size="8" cLASS="I0000RB" value="<?php    echo $RMBRate?>" readonly></td>
    <td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Amount[]" type="text" id="Amount" size="8" class="I0000RB" readonly></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td align="center" class="A0101" style="height:25px">9</td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="StuffName0[]" type="text" id="uStuffName" size="38" cLASS="I0000LF"></td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value10[]" type="text" id="Value10" size="5" cLASS="I0000RF" onFocus='toTempValue(this.value)' onBlur='Indepot(this,10)'></td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value10[]" type="text" id="Value10" size="5" cLASS="I0000RF" onFocus='toTempValue(this.value)' onBlur='Indepot(this,10)'></td>
	<td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Value10[]" type="text" id="Value10" size="8" cLASS="I0000RB" value="<?php    echo $RMBRate?>" readonly></td>
    <td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Amount[]" type="text" id="Amount" size="8" class="I0000RB" readonly></td>
  </tr> 
  <tr bgcolor="#CCCCCC">
    <td align="center" class="A0101" style="height:25px">10</td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="StuffName0[]" type="text" id="uStuffName" size="38" cLASS="I0000LF"></td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value11[]" type="text" id="Value11" size="5" cLASS="I0000RF" onFocus='toTempValue(this.value)' onBlur='Indepot(this,11)'></td>
    <td align="center" bgcolor="#FFFFFF" class="A0101"><input name="Value11[]" type="text" id="Value11" size="5" cLASS="I0000RF" onFocus='toTempValue(this.value)' onBlur='Indepot(this,11)'></td>
	<td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Value11[]" type="text" id="Value11" size="8" cLASS="I0000RB" value="<?php    echo $RMBRate?>" readonly></td>
    <td align="center" bgcolor="#CCCCCC" class="A0101"><input name="Amount[]" type="text" id="Amount" size="8" class="I0000RB" readonly></td>
  </tr> 
  </table>
 <table border="0" cellpadding="0" cellspacing="0" style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:680px">
  <tr bgcolor="#999999">
    <td style="width:60px" height="25" align="right" class="A0110">USD售价</td>
    <td align="right" class="A0100">&nbsp;</td>
  <td style="width:80px" align="center" class="A0101"><input name="SalePrice" type="text" id="SalePrice" size="8" class="I0000RF" onFocus='toTempValue(this.value)' onchange='toProfit(this)'></td>
  </tr>
  <tr bgcolor="#999999">
    <td height="25" align="right" class="A0110">USD成本</td>
    <td align="right" class="A0100">&nbsp;</td>
    <td align="center" class="A0101"><input name="UsdAmount" type="text" id="UsdAmount" size="8" class="I0000RB" readonly></td>
  </tr>
  <tr bgcolor="#999999">
    <td height="25" align="right" class="A0110">RMB成本</td>
    <td class="A0100">(包括 <?php    echo $HzRate?> 的行政费用)</td>
    <td align="center" class="A0101"><input name="RmbAmount" type="text" id="RmbAmount" size="8" class="I0000RB" readonly></td>
  </tr>
  <tr bgcolor="#999999">
    <td height="25" align="right" class="A0110">毛利率</td>
    <td align="right" class="A0100">&nbsp;</td>
    <td align="center" class="A0101"><input name="Profit" type="text" id="Profit" size="8" class="I0000RF" onFocus='toTempValue(this.value)' onchange='toSalePrice(this)'></td>
  </tr>
</table>
 <table border="0" cellpadding="0" cellspacing="0" style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:680px;height:60px">
  <tr bgcolor="#CCCCCC">
    <td class="A0111"><p>&nbsp;&nbsp;操作说明：<br>
      &nbsp;&nbsp;1、RMB配件行不够时，可点击[+]增加行<br>
      &nbsp;&nbsp;2、改变毛利率参数可以得出相应的USD售价；反之，改变售价的值可以得出相应的毛利率。</p>
      </td>
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
	tmpNum=oTR.rowIndex+1;
	ItemNum=tmpNum-3;
	ItemNum2=tmpNum-2;
	//第一列:序号
	oTD=oTR.insertCell(0);
	oTD.innerHTML=ItemNum;
	oTD.className ="A0101";
	oTD.align="center";
	oTD.height="25";
	oTD.style.backgroundColor="#CCCCCC";
				
	//第二列:配件名称
	oTD=oTR.insertCell(1);
	oTD.innerHTML="<input name='StuffName0[]' type='text' id='uStuffName' size='38' cLASS='I0000LF'>";
	oTD.className ="A0101";
	oTD.align="center";
				
	//三、单价
	oTD=oTR.insertCell(2);
	oTD.innerHTML="<input name='Value"+ItemNum2+"[]' type='text' id='Value"+ItemNum2+"' size='5' cLASS='I0000RF' onFocus='toTempValue(this.value)' onBlur='Indepot(this,"+ItemNum2+")'>";
	oTD.className ="A0101";
	oTD.align="center";
				
	//四：对应数量
	oTD=oTR.insertCell(3);
	oTD.innerHTML="<input name='Value"+ItemNum2+"[]' type='text' id='Value"+ItemNum2+"' size='5' cLASS='I0000RF' onFocus='toTempValue(this.value)' onBlur='Indepot(this,"+ItemNum2+")'>";
	oTD.className ="A0101";
	oTD.align="center";
				
	//五:汇率
	oTD=oTR.insertCell(4); 
	oTD.innerHTML="<input name='Value"+ItemNum2+"[]' type='text' id='Value"+ItemNum2+"' size='8' cLASS='I0000RB' value='1' readonly>";
	oTD.className ="A0101";
	oTD.align="center";
	oTD.style.backgroundColor="#CCCCCC";
	//六：产品成本
	oTD=oTR.insertCell(5);
	oTD.innerHTML="<input name='Amount[]' type='text' id='Amount' size='8' class='I0000RB' readonly>";
	oTD.className ="A0101";
	oTD.align="center";
	oTD.style.backgroundColor="#CCCCCC";
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

function Indepot(thisE,Row){
	var oldValue=document.form1.TempValue.value;
	var hzRate=Number(document.form1.hzRate.value);
	var thisValue=thisE.value;
		var tempA=document.getElementsByName("Amount"); 
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
	}
</script>

<?php
//电信-zxq 2012-08-01
//步骤1 $DataPublic.staffmain
include "../model/modelhead.php";
echo '<link rel="stylesheet" href="../model/tableborder.css">';
//步骤2：
ChangeWtitle("$SubCompany 新增特采单");//需处理
$nowWebPage =$funFrom."_add";
$toWebPage  =$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage; 不在允许值的范围:
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=960;$tableMenuS=500;
$CustomFun="<span onClick='ViewStuffId(5)' $onClickCSS>添加特采配件</span>&nbsp;";//自定义功能
$CheckFormURL="thisPage";
//权限
//采购或最高权限
if($Keys & mLOCK){
	$BuyerSTR="";
	}
else{
	$BuyerSTR=" and M.Number='$Login_P_Number'";
	}
//提取采购名单
$checkBuyer=mysql_query("SELECT  S.Number, S.Name 
	FROM $DataPublic.staffmain S
	WHERE S.BranchId in (4,110) ORDER BY S.Number",$link_id);
if($checkRow = mysql_fetch_array($checkBuyer)){
    $SelectCode="<select name='Number' id='Number' >";
    do{
        $_Number=$checkRow["Number"];
        $_Name=$checkRow["Name"];
        $_SelectedStr=$Number==$_Number?"selected":"";
        $SelectCode.="<option value='$_Number' $_SelectedStr>$_Name</option>";

    }while ($checkRow = mysql_fetch_array($checkBuyer));
    $SelectCode.="</select>";
}


// 项目名称
$ForshortList = '';
$mysql = "select a.Forshort, b.TradeNo, a.CompanyId
            from $DataIn.trade_object a
            INNER join $DataIn.trade_info b on a.id = b.TradeId
            where a.ObjectSign = 2 order by a.Forshort desc";

$tradeSql = mysql_query("$mysql", $link_id);
if ($tradeRow = mysql_fetch_array($tradeSql)) {
    $ForshortList = "<select name='TradeNo1' id='TradeNo1' onchange='document.form1.submit();'>";
    do {
        $Forshort = $tradeRow["Forshort"];
        $ThisTradeNo = $tradeRow["CompanyId"];
        $TradeNo1 = $TradeNo1 == "" ? $ThisTradeNo : $TradeNo1;

            if ($TradeNo1 == $ThisTradeNo) {
                $ForshortList .= "<option value='$ThisTradeNo' selected>$Forshort</option>";
            }
            else {
                $ForshortList .= "<option value='$ThisTradeNo'>$Forshort</option>";
            }
    } while ($tradeRow = mysql_fetch_array($tradeSql));
    $ForshortList .= "</select>&nbsp;";
}

include "../model/subprogram/add_model_t.php";
//步骤4：需处理
//<table border="0" width="<=$tableWidth>" cellpadding="0" cellspacing="0" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor="#F2F3F5">
//<td class="A1101" width="" align="center">&nbsp;</td>
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor="#F2F3F5">
 	<tr <?php  echo $Fun_bgcolor?>>
    <td>
      <table cellpadding="0" width="100%" cellspacing="0" bgcolor="#F2F3F5" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' >
        <tr>
          <td class="A1101" width="30" align="center">操作</td>
          <td class="A1101" width="30" align="center">序号</td>
          <td class="A1101" width="50" align="center">配件ID</td>
          <td class="A1101" width="230" align="center">配件名称</td>
          <td class="A1101" width="60" align="center">单价</td>
          <td class="A1101" width="60" align="center">采购数量</td>
          <td class="A1101" width="90" align="center">小计</td>
          <td class="A1101" width="290" align="center">特采原因</td>
          <td class="A1100" width="" align="center">供应商</td>
        </tr>
      </table>
    </td>
	</tr>
	<tr>
		<td  height="357" class="A0111">
			<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:auto">
				<table cellpadding="0" width="100%" cellspacing="0" bgcolor="#F2F3F5" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' id='ListTable'>
				</table>
			</div>
	</tr>
</table>
<input name="TempValue" id="TempValue" type="hidden" value="0">
<input name="RecordCount" id="RecordCount"  type="hidden" value="0">

<input name="StuffId0" id="StuffId0" type="hidden" value="">
<input name="Price0"  id="Price0" type="hidden" value="">
<input name="FactualQty0" id="FactualQty0" type="hidden" value="">
<input name="Company0" id="Company0" type="hidden" value="">
<input name="AddRemark0" id="AddRemark0"  type="hidden" value="">


<?php
include "../model/subprogram/add_model_b.php";
?><script language = "JavaScript">
function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}
function deleteRow (tt){
	//alert ("Here1");
	//alert(tt.parentElement);
	if(tt.parentElement==null || tt.parentElement=="undefined" ){  // add by zx 2011-05-06 Firfox不支持 parentElement
		var rowIndex=tt.parentNode.rowIndex;
	}
	else{
		var rowIndex=tt.parentElement.rowIndex;
	}
	//alert(rowIndex);
	ListTable.deleteRow(rowIndex);
	ShowSequence();
	}
function ShowSequence(){
	for(i=0;i<ListTable.rows.length;i++){
		var j=i+1;
  		ListTable.rows[i].cells[1].innerText=j;
		}
	}
//窗口打开方式修改为兼容性的模态框 by ckt 2018-01-07
function ViewStuffId(Action){
	if(document.all("Number")!=null){
        var SafariReturnValue = document.getElementById('SafariReturnValue');
        var Number = document.getElementById('Number');
        var index=Number.selectedIndex;
        var buyerId = Number.options[index].value;
        if (!arguments[1]) {
            var num = Math.random();
            SafariReturnValue.value = "";
            SafariReturnValue.callback = 'ViewStuffId("",true)';
            var NumberTemp = document.getElementById('Number').value;
            var TradeNo1 = jQuery("#TradeNo1  option:selected").val();

            var url = "/public/stuffdata_s1.php?r=" + num + "&tSearchPage=stuffdata&fSearchPage=cg_cgdsheet&SearchNum=2&Action=" + Action + "&Bid=" + NumberTemp + "&buyerId=" + buyerId+ "&xmId=" + TradeNo1;
                // BackData = window.open(", "BackData", "dialogHeight =650px;dialogWidth=980px;center=yes;scroll=yes");
            openFrame(url, 1300, 650);//url需为绝对路径
            return false;
        }
		//拆分
		if(SafariReturnValue.value){
			var Rows=SafariReturnValue.value.split("``");//分拆记录:
            SafariReturnValue.value = "";
            SafariReturnValue.callback = "";
			var Rowslength=Rows.length;//数组长度即领料记录数

			if(document.getElementById("TempMaxNumber")){  ////给add by zx 2011-05-05 firfox and  safari不能用javascript生成的元素
				var TempMaxNumber=document.getElementById("TempMaxNumber");
				TempMaxNumber.value=TempMaxNumber.value*1+Rowslength*1;
			}
			  //给add by zx firfox and  safari不能用javascript生成的元素

			for(var i=0;i<Rowslength;i++){
				var Message="";
				var FieldTemp=Rows[i];		//拆分后的记录
				var FieldArray=FieldTemp.split("^^");//分拆记录中的字段：0配件ID|1配件名称|2配件价格|3可用库存|4采购|5供应商
				//过滤相同的配件ID号
				for(var j=0;j<ListTable.rows.length;j++){
					var tempName=ListTable.rows[j].cells[2].innerText;
					if(FieldArray[0]==tempName){//如果ID号存在
						Message="配件: "+FieldArray[1]+" 已存在!跳过继续！";
						break;
						}
					}
				if(Message==""){
					oTR=ListTable.insertRow(ListTable.rows.length);
					tmpNum=oTR.rowIndex+1;

					//第1列:序号
					oTD=oTR.insertCell(0);
					oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode,ListTable)' title='删除当前行'>×</a>";
					oTD.className ="A0111";
					oTD.align="center";
					oTD.width="30";
					oTD.onmousedown=function(){
						window.event.cancelBubble=true;};

					//第2列:序号
					oTD=oTR.insertCell(1);
					oTD.innerHTML=""+tmpNum+"";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="30";

					//第3列:配件ID
					oTD=oTR.insertCell(2);
					oTD.innerHTML="<input name='StuffId["+tmpNum+"]' type='hidden' id='StuffId"+tmpNum+"' size='10' value='"+FieldArray[0]+"'>"+FieldArray[0]+"";

					//oTD.innerHTML="<input name='StuffId["+tmpNum+"]' type='hidden' id='StuffId1' size='10' value='"+FieldArray[0]+"'>"+FieldArray[0]+"";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="50";

					//配件名称
					oTD=oTR.insertCell(3);
					oTD.innerHTML=""+FieldArray[1]+"";
					oTD.className ="A0101";
					oTD.width="230";

					//单价
					oTD=oTR.insertCell(4);
					oTD.innerHTML="<input name='Price["+tmpNum+"]' type='text' id='Price"+tmpNum+"' size='5' value='"+FieldArray[2]+"' class='textINPUT' onChange='ChangeThis("+tmpNum+",\"Price\")' onfocus='toTempValue(this.value)'>";


					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="60";

					//采购数量
					oTD=oTR.insertCell(5);
					oTD.innerHTML="<input name='FactualQty["+tmpNum+"]' type='text' id='FactualQty"+tmpNum+"' size='5' value='100000' class='textINPUT' onChange='ChangeThis("+tmpNum+",\"FactualQty\")' onfocus='toTempValue(this.value)'>";



					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="60";

					//小计
					oTD=oTR.insertCell(6);
					oTD.innerHTML="<input name='Amount["+tmpNum+"]' type='text' id='Amount"+tmpNum+"' size='7' value='0' class='totalINPUT' readonly>";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="90";

					//特采原因
					oTD=oTR.insertCell(7);
					oTD.innerHTML="<input name='AddRemark["+tmpNum+"]' type='text' id='AddRemark"+tmpNum+"' size='35' value='原因:特采' class='textINPUT'>";
					oTD.className ="A0101";
					oTD.width="290";
					//供应商
					oTD=oTR.insertCell(8);
					oTD.innerHTML="<input name='Company["+tmpNum+"]' type='hidden' id='Company"+tmpNum+"' size='10' value='"+FieldArray[3]+"'>"+FieldArray[4]+"";
					oTD.className ="A0101";
					oTD.width="";
					/*
					oTD=oTR.insertCell(9);
					oTD.innerHTML="&nbsp;";
					oTD.className ="A0101";
					oTD.width="";
					*/
					document.form1.RecordCount.value=tmpNum;
					}
				else{
					alert(Message);
					}
				}//end for
			return true;
			}
		else{
			alert("没有选到配件！");
			return false;
			}
		}
	else{
		alert("非采购或最高权限");
		}
	}

function ChangeThis(Row,Keywords){
	var oldValue=document.form1.TempValue.value;//改变前的值
	//var Qtytemp=eval("document.form1.FactualQty"+Row+".value");//改变后的值
	var Qtytemp=document.getElementById("FactualQty"+Row+"").value;
	//var Pricetemp=eval("document.form1.Price"+Row+".value");//改变后的值
	var Pricetemp=document.getElementById("Price"+Row+"").value;
	//alert ("Pricetemp:"+Pricetemp);

	if(Keywords=="FactualQty"){
		//检查是否数字格式
		var Result=fucCheckNUM(Qtytemp,'Price');
		if(Result==0 || Qtytemp==0){
			alert("输入了不正确的数量:"+Qtytemp+",重新输入!");
			//eval("document.form1.FactualQty"+Row).value=oldValue;
			document.getElementById("FactualQty"+Row+"").value=oldValue;
			}
		else{
			var tempAmount=Pricetemp*Qtytemp;
			//eval("document.form1.Amount"+Row).value=tempAmount.toFixed(4);
			document.getElementById("Amount"+Row+"").value=tempAmount.toFixed(4);
			}
		}
	else{
		//检查是否价格格式
		var Result=fucCheckNUMP(Pricetemp,'Price');
		if(Result==-1){
			alert("输入不正确的售价:"+Pricetemp+",重新输入!");
			//eval("document.form1.Price"+Row).value=oldValue;
			document.getElementById("Price"+Row+"").value=oldValue;
			}
		else{
			var tempAmount=Pricetemp*Qtytemp;
			//eval("document.form1.Amount"+Row).value=tempAmount.toFixed(4);
			document.getElementById("Amount"+Row+"").value=tempAmount.toFixed(4);
			}
		}
	}

function CheckForm(){
	var Message="";
	//检查是否有记录，如果没有,重新刷新
	var Rows=ListTable.rows.length;
	if(Rows==0){
		alert("没有设定特采配件资料！");
		return false;
		}
	else{
		//检查单价和数量是否不为0
		for(var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			//if(e.type=="text" && e.value==0){
			if(e.type=="text"  &&  e.value<0 ){
				Message="单价或数量不可以为0，请检查!!!!";
				break;
				}
			}
		if(Message!=""){
			alert(Message);
			return false;
			}
		else{
			passvalue("StuffId|Price|FactualQty|Company|AddRemark");  //add by zx 2011-05-05 必须与上面隐藏传递元素id0号一致,v如StuffId0
			//passvalue("StuffId0|Price0|FactualQty0|Company0|AddRemark0");
			document.form1.action="cg_cgdsheet_save.php";
			document.form1.submit();
			}
		}
	}

function fucCheckNUMP(NUM,Objects)
{
 var i,j,strTemp;
 if (Objects!="Price"){
 strTemp="0123456789";}
 else{
	strTemp=".0123456789";
	 }
 if ( NUM.length== 0)
  return 0
 for (i=0;i<NUM.length;i++)
 {
  j=strTemp.indexOf(NUM.charAt(i));
  if (j==-1)
  {
  //说明有字符不是数字
   return -1;
  }
 }
 //说明是数字
 return 1;
}

/*
function passvalue(passvars){  //专为safari设计 add by zx 2011-05-05  pavlue

	//为safari设计 add by zx 2011-05-05
	if(document.getElementById('Safaripassvars')){ //放在add_model_t.php 存在则
	    document.getElementById('Safaripassvars').value=passvars;  //传递给PHP的变量
		var tmpkeywords = passvars.split("|");
		for (i=0;i<tmpkeywords.length;i++){
			//var	StuffId_Str="^";
			 eval("var "+tmpkeywords[i]+"0='^'");  //给分隔符
			//alert("var "+tmpkeywords[i]+"0='^'");
		}


		var RecordCount=0;
		if(document.getElementById('RecordCount')){
			RecordCount=document.getElementById("RecordCount"+"").value;  //在当前页面的元素
		}

		//alert("RecordCount:"+RecordCount);
		//if(RecordCount>=0){	没作用，一般在保存关已判断是否存在表格数据。
		if(RecordCount>=0){	  //说明有记录，但不知存在多少，故用,document.getElementById('RecordCount') 有些地方没用此变量，>0 为成>=0
			if(document.getElementById('TempMaxNumber')){   //放在add_model_t.php 存在则

				var TempMaxNumber=document.getElementById('TempMaxNumber').value;

				for(i=1;i<=TempMaxNumber;i++){
					//alert("TempMaxNumber:"+TempMaxNumber);
					for (j=0;j<tmpkeywords.length;j++){
						//alert ("document.getElementById('"+tmpkeywords[j]+i+"')");
						if(eval("document.getElementById('"+tmpkeywords[j]+i+"')") ){
							//StuffId_Str=StuffId_Str+document.getElementById("StuffId"+i+"").value+"^";
							 eval(""+tmpkeywords[j]+"0="+tmpkeywords[j]+"0"+"+document.getElementById('"+tmpkeywords[j]+i+"').value+'^'");  //给分隔符
							//alert(""+tmpkeywords[j]+"0="+tmpkeywords[j]+"0"+"+document.getElementById('"+tmpkeywords[j]+i+"').value+'^'");
						}
					}

				}
			}

		}

		 for (i=0;i<tmpkeywords.length;i++){
			 if(eval(""+"document.getElementById('"+tmpkeywords[i]+"0')") ){
			   //document.getElementById("StuffId0"+"").value=StuffId_Str;
			 	eval(""+"document.getElementById('"+tmpkeywords[i]+"0').value="+tmpkeywords[i]+"0");  //给分隔符
			  //alert(""+"document.getElementById('"+tmpkeywords[i]+"0').value="+tmpkeywords[i]+"0");
			 }
		}
		//var ss=document.getElementById("StuffId0"+"").value;
		///alert ("SS:"+ss);

	}
}
*/
</script>

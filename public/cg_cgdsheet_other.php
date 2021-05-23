<?php 
//电信-zxq 2012-08-01
//步骤1 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 采购单-其它功能");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_other";	
$toWebPage  =$funFrom."_other_up";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$tableWidth=1000;$tableMenuS=500;
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
$Parameter="funFrom,$funFrom,From,$From,Orderby,$Orderby,Pagination,$Pagination,Page,$Page";
?>
<STYLE type=text/css>
#wrap {PADDING-RIGHT: 0px; PADDING-LEFT: 0px; PADDING-BOTTOM: 5px; MARGIN: 0px auto; WIDTH: 960px; PADDING-TOP: 5px; TEXT-ALIGN: left}
.T_Menu_05 {
	PADDING-RIGHT: 0px; PADDING-LEFT: 0px; BACKGROUND: url(http://i0.sinaimg.cn/dy/deco/2008/0331/yocc08img/news_mj_001.gif) #fff repeat-x 0px -900px; PADDING-BOTTOM: 0px; MARGIN: 0px 1px; OVERFLOW: hidden; PADDING-TOP: 2px; HEIGHT: 25px
	}
.T_Menu_05 LABEL {
	BORDER-RIGHT: #ccc 1px solid; 
	PADDING-RIGHT: 0px; 
	BORDER-TOP: #ccc 1px solid; 
	PADDING-LEFT: 0px; 
	BACKGROUND: url(http://i0.sinaimg.cn/dy/deco/2008/0331/yocc08img/news_mj_001.gif) repeat-x 0px -650px; 
	FLOAT: left; 
	PADDING-BOTTOM: 0px; 
	MARGIN-LEFT: 2px; 
	BORDER-LEFT: #ccc 1px solid; 
	WIDTH: 46px; 
	CURSOR: pointer; 
	LINE-HEIGHT: 19px; 
	PADDING-TOP: 2px; 
	BORDER-BOTTOM: #ccc 1px solid; 
	TEXT-ALIGN: center
	}
.T_Menu_05 LABEL.selected {
	BACKGROUND: #f7f7f7; HEIGHT: 30px; BORDER-BOTTOM-STYLE: none
	}
.T_Menu_05 A:link {
	COLOR: #000; TEXT-DECORATION: none
	}
.T_Menu_05 A:visited {
	COLOR: #000; TEXT-DECORATION: none
	}
.T_Menu_05 A:hover {
	COLOR: #f00; TEXT-DECORATION: underline
	}

.blk_90 {
	border-top: none;
	border-right: 1px solid #CCCCCC;
	border-bottom: 1px solid #CCCCCC;
	border-left: 1px solid #CCCCCC;
	HEIGHT: 330px;
	background-color: #F7F7F7;
}
.blk_90 .line_01 {
	MARGIN: 0px 14px
}
.blk_90 .f12 {
	PADDING-LEFT: 16px; COLOR: #0449be; LINE-HEIGHT: 30px
}

#SSLabel_21_01 {
	MARGIN-LEFT: 20px; WIDTH: 120px; 
	}
#SSLabel_21_02 {
	WIDTH: 120px; 
	}
</STYLE>
<script language=javascript type=text/javascript>
function SubShowClass(ID,eventType,defaultID,openClassName,closeClassName){
	this.version="1.21";
	this.author="mengjia";
	this.parentObj=SubShowClass.$(ID);
	if(this.parentObj==null&&ID!="none"){throw new Error("SubShowClass(ID)参数错误:ID 对像存在!(value:"+ID+")")};if(!SubShowClass.childs){SubShowClass.childs=[]};this.ID=SubShowClass.childs.length;SubShowClass.childs.push(this);this.lock=false;this.label=[];this.defaultID=defaultID==null?0:defaultID;this.selectedIndex=this.defaultID;this.openClassName=openClassName==null?"selected":openClassName;this.closeClassName=closeClassName==null?"":closeClassName;this.mouseIn=false;var mouseInFunc=Function("SubShowClass.childs["+this.ID+"].mouseIn = true"),mouseOutFunc=Function("SubShowClass.childs["+this.ID+"].mouseIn = false");if(ID!="none"){if(this.parentObj.attachEvent){this.parentObj.attachEvent("onmouseover",mouseInFunc)}else{this.parentObj.addEventListener("mouseover",mouseInFunc,false)}};if(ID!="none"){if(this.parentObj.attachEvent){this.parentObj.attachEvent("onmouseout",mouseOutFunc)}else{this.parentObj.addEventListener("mouseout",mouseOutFunc,false)}};if(typeof(eventType)!="string"){eventType="onmousedown"};eventType=eventType.toLowerCase();switch(eventType){case "onmouseover":this.eventType="mouseover";break;case "onmouseout":this.eventType="mouseout";break;case "onclick":this.eventType="click";break;case "onmouseup":this.eventType="mouseup";break;default:this.eventType="mousedown"};this.addLabel=function(labelID,contID,parentBg,springEvent,blurEvent){if(SubShowClass.$(labelID)==null&&labelID!="none"){throw new Error("addLabel(labelID)参数错误:labelID 对像存在!(value:"+labelID+")")};var TempID=this.label.length;if(parentBg==""){parentBg=null};this.label.push([labelID,contID,parentBg,springEvent,blurEvent]);var tempFunc=Function('SubShowClass.childs['+this.ID+'].select('+TempID+')');if(labelID!="none"){if(SubShowClass.$(labelID).attachEvent){SubShowClass.$(labelID).attachEvent("on"+this.eventType,tempFunc)}else{SubShowClass.$(labelID).addEventListener(this.eventType,tempFunc,false)}};if(TempID==this.defaultID){if(labelID!="none"){SubShowClass.$(labelID).className=this.openClassName};if(SubShowClass.$(contID)){SubShowClass.$(contID).style.display=""};if(ID!="none"){if(parentBg!=null){this.parentObj.style.background=parentBg}};if(springEvent!=null){eval(springEvent)}}else{if(labelID!="none"){SubShowClass.$(labelID).className=this.closeClassName};if(SubShowClass.$(contID)){SubShowClass.$(contID).style.display="none"}};if(SubShowClass.$(contID)){if(SubShowClass.$(contID).attachEvent){SubShowClass.$(contID).attachEvent("onmouseover",mouseInFunc)}else{SubShowClass.$(contID).addEventListener("mouseover",mouseInFunc,false)};if(SubShowClass.$(contID).attachEvent){SubShowClass.$(contID).attachEvent("onmouseout",mouseOutFunc)}else{SubShowClass.$(contID).addEventListener("mouseout",mouseOutFunc,false)}}};this.select=function(num,force){if(typeof(num)!="number"){throw new Error("select(num)参数错误:num 不是 number 类型!(value:"+num+")")};if(force!=true&&this.selectedIndex==num){return};var i;for(i=0;i<this.label.length;i++){if(i==num){if(this.label[i][0]!="none"){SubShowClass.$(this.label[i][0]).className=this.openClassName};if(SubShowClass.$(this.label[i][1])){SubShowClass.$(this.label[i][1]).style.display=""};if(ID!="none"){if(this.label[i][2]!=null){this.parentObj.style.background=this.label[i][2]}};if(this.label[i][3]!=null){eval(this.label[i][3])}}else if(this.selectedIndex==i||force==true){if(this.label[i][0]!="none"){SubShowClass.$(this.label[i][0]).className=this.closeClassName};if(SubShowClass.$(this.label[i][1])){SubShowClass.$(this.label[i][1]).style.display="none"};if(this.label[i][4]!=null){eval(this.label[i][4])}}};this.selectedIndex=num};this.random=function(){if(arguments.length!=this.label.length){throw new Error("random()参数错误:参数数量与标签数量不符!(length:"+arguments.length+")")};var sum=0,i;for(i=0;i<arguments.length;i++){sum+=arguments[i]};var randomNum=Math.random(),percent=0;for(i=0;i<arguments.length;i++){percent+=arguments[i]/sum;if(randomNum<percent){this.select(i);break}}};this.autoPlay=false;var autoPlayTimeObj=null;this.spaceTime=5000;this.play=function(spTime){if(typeof(spTime)=="number"){this.spaceTime=spTime};clearInterval(autoPlayTimeObj);autoPlayTimeObj=setInterval("SubShowClass.childs["+this.ID+"].autoPlayFunc()",this.spaceTime);this.autoPlay=true};this.autoPlayFunc=function(){if(this.autoPlay==false||this.mouseIn==true){return};this.nextLabel()};this.nextLabel=function(){var index=this.selectedIndex;index++;if(index>=this.label.length){index=0};this.select(index);if(this.autoPlay==true){clearInterval(autoPlayTimeObj);autoPlayTimeObj=setInterval("SubShowClass.childs["+this.ID+"].autoPlayFunc()",this.spaceTime)}};this.previousLabel=function(){var index=this.selectedIndex;index--;if(index<0){index=this.label.length-1};this.select(index);if(this.autoPlay==true){clearInterval(autoPlayTimeObj);autoPlayTimeObj=setInterval("SubShowClass.childs["+this.ID+"].autoPlayFunc()",this.spaceTime)}};this.stop=function(){clearInterval(autoPlayTimeObj);this.autoPlay=false}};SubShowClass.$=function(objName){if(document.getElementById){return eval('document.getElementById("'+objName+'")')}else{return eval('document.all.'+objName)}}
</script>
<input name="ActionId" type="hidden" id="ActionId" value="0">
<input name="TempValue" type="hidden" id="TempValue" value="0">
<table border="0" width="<?php  echo $tableWidth?>" height="300" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011" align="center">
     <DIV id=wrap>
	<DIV class=P_Right><!-- Right begin -->
		<DIV class=T_Menu_05 id=SubShow_21>		  	
			<LABEL class=selected id=SSLabel_21_01 onclick="ol(0)">需求单配件置换</LABEL>		
			<LABEL                id=SSLabel_21_02 onclick="ol(1)">清除需求单所用库存</LABEL> 		
		</DIV><!-- 1 begin -->
	
	<DIV class=blk_90 id=SSCont_21_01>
		 <br> <br><table width="940" align="center" cellspacing='0' id="dataTB0" >
		   <tr>
		     <td colspan="3" class="A1111" height="25" align="right">需更新的需求单流水号:</td>
			 <td colspan="9" class="A1101"><input name="StockId0" type="text" id="StockId0" maxlength="14">
			   <input type="button" name="Submit" value="确定" onclick="searchdata(0)"></td>
		     </tr>
		   <tr>
		     <td colspan="3" class="A0111" height="25" align="right">更新原因:</td>
			  <td colspan="9" class="A0101"><input name="AddRemark0" type="text" id="AddRemark0" size="100"></td>
		     </tr>
		   <tr>
		     <td colspan="12" height="40" valign="bottom">需求单数据:</td>
		     </tr>
		   <tr>
		     <td class="A1111" align="center" width="40" height="25">&nbsp;</td>
		     <td class="A1101" align="center" width="40">表ID</td>
		     <td class="A1101" align="center" width="110">流水号</td>
		     <td class="A1101" align="center" width="40">配件ID</td>
		     <td class="A1101" align="center" width="200">配件名</td>
		     <td class="A1101" align="center" width="50">单价</td>
		     <td class="A1101" align="center" width="50">订单数量</td>
		     <td class="A1101" align="center" width="50">已用库存</td>
		     <td class="A1101" align="center" width="50">需购数量</td>
		  	 <td class="A1101" align="center" width="50">增购数量</td>
		   	 <td class="A1101" align="center" width="50">采购</td>
		   <td class="A1101" align="center" width="55">供应商</td>
		   </tr>
		   <tr>
		     <td  height="25" align="center" class="A0111">置换前</td>
		     <td  height="25" align="center" class="A0101">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td align="center" class="A0101">&nbsp;</td>
		     <td class="A0101">&nbsp;</td>
		   </tr>
		   <tr>
		     <td  height="25" align="center" class="A0111">置换后</td>
		     <td  height="25" align="center" class="A0101">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td align="center" class="A0101">&nbsp;</td>
		     <td class="A0101">&nbsp;</td>
		   </tr>
		   <tr>
		     <td  height="60" colspan="12">说明:本操作将把原配件更新为另一配件,可同时更新配件、订单数量(默认与置换前的数量一致)、采购和供应商。置换条件:需求单处于待购状态或全部使用库存</td>
		     </tr>
		 </table>
	</DIV>
	
	<DIV class=blk_90 id=SSCont_21_02 style="DISPLAY:none">
		 <br><br><table width="940" align="center" cellspacing='0' id="dataTB1">
		   <tr>
		     <td colspan="3" class="A1111" height="25" align="right">需更新的需求单流水号:</td>
			 <td colspan="9" class="A1101"><input name="StockId1" type="text" id="StockId1" maxlength="14">
			   <input type="button" name="Submit" value="确定" onclick="searchdata(1)"></td>
		     </tr>
		   <tr>
		     <td colspan="3" class="A0111" height="25" align="right">更新原因:</td>
			  <td colspan="9" class="A0101"><input name="AddRemark1" type="text" id="AddRemark1" size="100"></td>
		     </tr>
		   <tr>
		     <td colspan="12" height="40" valign="bottom">需求单数据:</td>
		     </tr>
		   <tr>
		     <td class="A1111" align="center" width="40" height="25">&nbsp;</td>
		     <td class="A1101" align="center" width="40">表ID</td>
		     <td class="A1101" align="center" width="110">流水号</td>
		     <td class="A1101" align="center" width="40">配件ID</td>
		     <td class="A1101" align="center" width="200">配件名</td>
		     <td class="A1101" align="center" width="50">单价</td>
		     <td class="A1101" align="center" width="50">订单数量</td>
		     <td class="A1101" align="center" width="50">已用库存</td>
		     <td class="A1101" align="center" width="50">需购数量</td>
		  	 <td class="A1101" align="center" width="50">增购数量</td>
		   	 <td class="A1101" align="center" width="50">采购</td>
		   <td class="A1101" align="center" width="55">供应商</td>
		   </tr>
		   <tr>
		     <td  height="25" align="center" class="A0111">清除前</td>
		     <td  height="25" align="center" class="A0101">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td align="center" class="A0101">&nbsp;</td>
		     <td class="A0101">&nbsp;</td>
		   </tr>
		   <tr>
		     <td  height="25" align="center" class="A0111">清除后</td>
		     <td  height="25" align="center" class="A0101">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td class="A0101" align="center">&nbsp;</td>
		     <td align="center" class="A0101">&nbsp;</td>
		     <td class="A0101">&nbsp;</td>
		   </tr>

		   <tr>
		     <td colspan="12" height="60">&nbsp;&nbsp;说明：本操作将把配件需求单的使用库存全部或部分清除(增加可用库存请使用重置)</td>
		     </tr>
		 </table>
	</DIV>
	<SCRIPT language=javascript type=text/javascript>
		var SubShow_21 = new SubShowClass("SubShow_21","onclick",0);//层ID，触发的事件，默认层
		SubShow_21.addLabel("SSLabel_21_01","SSCont_21_01");
		SubShow_21.addLabel("SSLabel_21_02","SSCont_21_02");
		function ol(Action){			
			var ActionTemp=Number(document.form1.ActionId.value);
			if(Action!=ActionTemp){				
				document.form1.ActionId.value=Action;						
				if(Action==0){
					document.form1.StockId0.value="";
					document.form1.AddRemark0.value="";		
					for(i=1;i<12;i++){
						dataTB0.rows[4].cells[i].innerText=" ";
						dataTB0.rows[5].cells[i].innerText=" ";
						dataTB0.rows[5].cells[i].onclick="";
						dataTB0.rows[5].cells[i].style.cursor ="";
						}
					}
				else{
					document.form1.StockId1.value="";
					document.form1.AddRemark1.value="";
					for(i=1;i<12;i++){
						dataTB1.rows[4].cells[i].innerText=" ";
						dataTB1.rows[5].cells[i].innerText=" ";
						}
					}
				}
			}
	</script>
</DIV>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script language = "JavaScript">
function CheckForm(){
	var Message="";	
	var newData="";
	var ActionId=Number(document.form1.ActionId.value);
	switch(ActionId){
		case 0:
			if(funallTrim(dataTB0.rows[5].cells[2].innerText)==""){
				Message="没有需要更新的需求单数据!";break;
				}
			else{
				newData=dataTB0.rows[5].cells[1].innerText;
				}
			if(funallTrim(dataTB0.rows[5].cells[3].innerText)==""){
				Message="没有选取置换的配件0!";break;
				}
			else{
				newData=newData+"|"+dataTB0.rows[5].cells[3].innerText;
				}
			if(document.form1.AddRemark0.value==""){
				Message="没有输入更新原因!";
				}
			//加入采购和供应商ID
			newData=newData+"|"+dataTB0.rows[5].cells[10].data+"|"+dataTB0.rows[5].cells[11].data;
			break;
		case 1:
			if(funallTrim(dataTB1.rows[5].cells[2].innerText)==""){
				Message="没有需要更新的需求单数据!";break;
				}
			else{
				newData=dataTB1.rows[5].cells[1].innerText;
				}
			var changeQty=Number(funallTrim(dataTB1.rows[4].cells[7].innerText))-Number(document.form1.newStockQty.value);
			if(changeQty<=0){
				Message="库存数量没有变化或超出范围!";break;
				}
			else{
				newData=newData+"|"+changeQty
				}
			if(document.form1.AddRemark1.value==""){
				Message="没有输入更新原因!";
				}
			newData=newData+"|"+dataTB1.rows[5].cells[10].data+"|"+dataTB1.rows[5].cells[11].data;
			break;
		}
	if(Message!=""){
		alert(Message);
		}
	else{
		document.form1.action="cg_cgdsheet_other_up.php?newData="+newData;
		document.form1.submit();
		}
	}

function ChangeThis(ActionId){
	var oldValue=document.form1.TempValue.value;
	switch(ActionId){
		case 0:
			var TempFactualQty=document.form1.newOrderQty.value;
			var Result=fucCheckNUM(TempFactualQty,'');
			if(Result==0 || TempFactualQty==0){
				alert("输入了不正确的订单数量:"+TempFactualQty+",重新输入!");
				document.form1.newOrderQty.value=oldValue;
				}
			else{
				dataTB0.rows[5].cells[8].innerText=TempFactualQty;
				}
			break;
		case 1:
			var TempStockQty=document.form1.newStockQty.value;
			//检查库存数量是否符号要求:不能大于原使用库存数，不能为非数字
			var Result=fucCheckNUM(TempStockQty,'');
			if(Result==0){
				alert("输入了不正确的数量:"+TempStockQty+",重新输入!");
				document.form1.newStockQty.value=oldValue;
				}
			else{
				var oldStockQty=Number(dataTB1.rows[4].cells[7].innerText);
				if(Number(TempStockQty)>oldStockQty){
					alert("清除后的库存数量超出许可范围!"+TempStockQty+"-"+oldStockQty);
					document.form1.newStockQty.value=oldValue;
					}
				}
			break;
		case 2:	//单价检查
			var TempPrice=document.form1.newPrice.value;
			var Result=fucCheckNUM(TempPrice,'Price');
			if(Result==0){
				alert("输入了不正确的单价:"+TempPrice+",重新输入!");
				document.form1.newPrice.value=oldValue;
				}
			break;
		}
	}

function searchstuffid(){
	var r=Math.random();
	var BackData=window.showModalDialog("stuffdata_s1.php?r="+r+"&tSearchPage=stuffdata&fSearchPage=cg_cgdsheet&SearchNum=1&Action=4","BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
	//$StuffId."^^".$StuffCname."^^".$Price."^^".$Number."^^".$Buyer."^^".$CompanyId."^^".$Forshort;
	if(BackData){
		var CL=BackData.split("^^");
		for(var i=3;i<6;i++){
			var j=i-3;			
			if(i==5){
				dataTB0.rows[5].cells[i].innerHTML="<input name='newPrice' type='text' id='newPrice' size='4' value='"+CL[j]+"' class='noLine' onChange='ChangeThis(2)' onfocus='toTempValue(this.value)'>";
				}
			else{
				dataTB0.rows[5].cells[i].innerText=CL[j];
				}
			}
		dataTB0.rows[5].cells[10].data=CL[3];
		dataTB0.rows[5].cells[10].innerText=CL[4];
		dataTB0.rows[5].cells[10].onclick=searchBuyerId;
		dataTB0.rows[5].cells[10].style.cursor ="hand";
							
		dataTB0.rows[5].cells[11].data=CL[5];
		dataTB0.rows[5].cells[11].innerText=CL[6];
		dataTB0.rows[5].cells[11].onclick=searchCompanyId;
		dataTB0.rows[5].cells[11].style.cursor ="hand";
		}
	else{
		alert("没有选取配件");
		return false;
		}
	}
function searchBuyerId(){
	var r=Math.random();
	var BackData=window.showModalDialog("staff_s1.php?r="+r+"&tSearchPage=staff&fSearchPage=cg_cgdsheet&SearchNum=1&Action=5&Jid=3","BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
	if(BackData){
		var CL=BackData.split("^^");
		dataTB0.rows[5].cells[10].data=CL[0];
		dataTB0.rows[5].cells[10].innerText=CL[1];
		}
	else{
		alert("没有选取采购，不做更新");
		return false;
		}
	}

function searchCompanyId(){
	var r=Math.random();
	var BackData=window.showModalDialog("providerdata_s1.php?r="+r+"&tSearchPage=staff&fSearchPage=cg_cgdsheet&SearchNum=1&Action=1","BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
	if(BackData){
		var CL=BackData.split("^^");
		dataTB0.rows[5].cells[11].data=CL[0];
		dataTB0.rows[5].cells[11].innerText=CL[1];
		}
	else{
		alert("没有选取供应商，不做更新");
		return false;
		}
	}

function searchdata(ActionTo){
	//读取需求单的数据,然后
	switch(ActionTo){
		case 0:	
			var tempStockId=document.form1.StockId0.value;
			var url="../admin/cg_cgdsheet_ajax.php?StockId="+tempStockId+"&ActionTo="+ActionTo;
			var ajax=InitAjax();
			ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　	if(ajax.readyState==4){// && ajax.status ==200.innerHTML
				var BackData=ajax.responseText;
				var DataArray=BackData.split("`");
				if(DataArray[0]!=""){
					alert(DataArray[0]);
					document.form1.StockId0.value="";
					document.form1.AddRemark0.value="";
					for(var i=1;i<12;i++){
						dataTB0.rows[4].cells[i].innerText=" ";
						dataTB0.rows[5].cells[i].innerText=" ";
						dataTB0.rows[5].cells[i].onclick="";
						dataTB0.rows[5].cells[i].style.cursor ="";
						}
					}
				else{		//6 订单数量默认与之前的一致，7 已用库存=0；8 需购数量与订单数量一致， 9 增购数量=0
					for(var i=1;i<12;i++){
						dataTB0.rows[4].cells[i].innerText=DataArray[i];
						if(i==3 || i==4){
							dataTB0.rows[5].cells[i].onclick=searchstuffid;
							dataTB0.rows[5].cells[i].style.cursor ="hand";
							}
						if(i==6){
							dataTB0.rows[5].cells[i].innerHTML="<input name='newOrderQty' type='text' id='newOrderQty' size='4' value='"+DataArray[i]+"' class='noLine' onChange='ChangeThis(0)' onfocus='toTempValue(this.value)'>";
							dataTB0.rows[5].cells[7].innerText=0;
							dataTB0.rows[5].cells[8].innerText=DataArray[i];
							dataTB0.rows[5].cells[9].innerText=0;
							}						
						}
					for(var j=1;j<3;j++){
						dataTB0.rows[5].cells[j].innerText=DataArray[j];
						}
					
					}
				}
			}
			ajax.send(null); 
		break;	
		case 1:
			var tempStockId=document.form1.StockId1.value;
			var url="../admin/cg_cgdsheet_ajax.php?StockId="+tempStockId+"&ActionTo="+ActionTo;
			var ajax=InitAjax();
			ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　	if(ajax.readyState==4){// && ajax.status ==200.innerHTML
				var BackData=ajax.responseText;
				var DataArray=BackData.split("`");
				if(DataArray[0]!=""){
					document.form1.StockId1.value="";
					document.form1.AddRemark1.value="";
					for(var i=1;i<12;i++){
						dataTB1.rows[4].cells[i].innerText=" ";
						dataTB1.rows[5].cells[i].innerText=" ";
						}
					}
				else{
					for(var i=1;i<12;i++){
						dataTB1.rows[4].cells[i].innerText=DataArray[i];
						if(i==7){
							dataTB1.rows[5].cells[i].innerHTML="<input name='newStockQty' type='text' id='newStockQty' size='4' value='"+DataArray[i]+"' class='noLine' onChange='ChangeThis(1)' onfocus='toTempValue(this.value)'>";
							}
						else{							
							dataTB1.rows[5].cells[i].innerText=DataArray[i];
							if(i>9){
								var j=i+2;
								dataTB1.rows[5].cells[i].data=DataArray[j];
								}
							}
						}
					}
				}
			}
			ajax.send(null); 
		break;
		}
	}
</script>
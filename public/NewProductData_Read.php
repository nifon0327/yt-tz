<style type="text/css">
<!--
.moveLtoR{ filter:revealTrans(Transition=6,Duration=0.3)};
.moveRtoL{ filter:revealTrans(Transition=7,Duration=0.3)};
/* 为 DIV 加阴影 */ 
.out {position:relative;background:#006633;margin:10px auto;width:500px;}
.in {background:#FFFFE6;border:1px solid #555;padding:10px 5px;position:relative;top:-5px;left:-5px;}  
/* 为 图片 加阴影 */ 
.imgShadow {position:relative;     background:#bbb;      margin:10px auto;     width:220px; } 
.imgContainer {position:relative;      top:-5px;     left:-5px;     background:#fff;      border:1px solid #555;     padding:0; } 
.imgContainer img {     display:block; } 
.glow1 { filter:glow(color=#FF0000,strengh=2)}
-->
</style>

<script>
function ViewChart(Pid){
	document.form1.action="NewProductData_chart.php?Pid="+Pid;
	document.form1.target="_blank";
	document.form1.submit();		
	document.form1.target="_self";
	document.form1.action="";
	}
</script>
<?php 
//电信-ZX  2012-08-01
//步骤1
include "../model/modelhead.php";
$tableMenuS=550;
ChangeWtitle("$SubCompany 新产品列表");
$funFrom="NewProductData";
$From=$From==""?"read":$From;
//特殊权限 144
$TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=144 and UserId=$Login_P_Number LIMIT 1",$link_id);
if($TRow = mysql_fetch_array($TResult)){
	$ColsNumber=14;  //18
	$Th_Col="选项|40|序号|35|产品ID|50|中文名|200|类别|100|买价<br>RMB|60|描述|30|高清标准图|100|供应商|100|可用<br>状态|30|操作员|50";
	$myTask=1;
	include "../model/subprogram/sys_parameters.php";
	}
else{
	$ColsNumber=14;  //16
	$Th_Col="选项|40|序号|35|产品ID|50|中文名|200|类别|100|买价<br>RMB|60|描述|30|高清标准图|100|供应商|100|可用<br>状态|30|操作员|50";
	$myTask=0;
	}
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,5,6,7,8";  //$ActioToS="1,2,3,4,5,6,7,8,13,58,43,63";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){
	$SearchRows="";
	
	echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
	echo "<option value='' selected>全部</option>";
	$result = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE cSign>=0 AND Estate=1 ORDER BY Id",$link_id);
	if($myrow = mysql_fetch_array($result)){
		do{
			$theCompanyId=$myrow["CompanyId"];
			$theForshort=$myrow["Forshort"];
			//$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
			if($CompanyId==$theCompanyId){
				echo"<option value='$theCompanyId' selected>$theForshort</option>";
				$SearchRows=" AND P.CompanyId=".$theCompanyId;
				}
			 else{
			 	echo"<option value='$theCompanyId'>$theForshort</option>";
				}
			}while($myrow = mysql_fetch_array($result));
		}
	echo"</select>";
	
	echo "<select name='ProductType' id='ProductType' onchange='ResetPage(this.name)'>";
	$result = mysql_query("SELECT P.TypeId,T.TypeName,T.Letter 
	FROM $DataIn.productdata P
	LEFT JOIN $DataIn.ProductType T ON T.TypeId=P.TypeId
	WHERE T.Estate=1  GROUP BY P.TypeId ORDER BY T.Letter",$link_id);
	echo "<option value='' selected>全部</option>";
	while ($myrow = mysql_fetch_array($result)){
			$TypeId=$myrow["TypeId"];
			if ($ProductType==$TypeId){
				echo "<option value='$TypeId' selected>$myrow[Letter]-$myrow[TypeName]</option>";
				}
			else{
				echo "<option value='$TypeId'>$myrow[Letter]-$myrow[TypeName]</option>";
				}
			} 
		echo"</select>&nbsp;";
	$TypeIdSTR=$ProductType==""?"":" AND P.TypeId=".$ProductType;
	$SearchRows.=$TypeIdSTR;
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
echo"<div id='Jp' style='position:absolute; left:341px; top:229px; width:480px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";


$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql= "SELECT P.Id,P.ProductId,P.cName,P.eCode,P.Price,P.Unit,P.Moq,P.CompanyId,P.Description,P.Remark,P.pRemark,
	P.TestStandard,P.Img_H,P.Img_L,P.Date,P.PackingUnit,P.Estate,P.Locks,P.Code,P.Operator,T.TypeName,C.Forshort,D.Rate,D.Symbol
	FROM $DataIn.newproductdata P
	LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	where 1 $SearchRows order by Estate DESC,Id DESC";   //LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$ProductId=$myRow["ProductId"];
		$Rate=$myRow["Rate"];
		$Symbol="USD";
		$Client=$myRow["Forshort"];
		$cName=$myRow["cName"];
		$eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
		$Remark=trim($myRow["Remark"])==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='18' height='18'>";
		$pRemark=trim($myRow["pRemark"])==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[pRemark]' width='18' height='18'>";
		$Description=$myRow["Description"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Description]' width='18' height='18'>";
		$Price=$myRow["Price"];
		$saleRMB=$Price;
		$Moq=$myRow["Moq"]==0?"&nbsp;":$myRow["Moq"];
		$TestStandard=$myRow["TestStandard"];
		if($TestStandard>=1){	
			include "subprogram/newproductdata.php";
			}
		else{
			if($TestStandard==2){
				$TestStandard="<div class='blueB' title='标准图审核中'>$cName</div>";
				}
			else{
				$TestStandard=$cName;
				}
			}
		$Img_H=$myRow["Img_H"];
		if($Img_H>=1){   //if($Img_H==1){
			$Img_H="T".$ProductId."_H.jpg";
			$Img_H=anmaIn($Img_H,$SinkOrder,$motherSTR);
			$td=anmaIn("download/newproductdata/",$SinkOrder,$motherSTR);
			$Img_H="<img onClick='OpenOrLoad(\"$td\",\"$Img_H\")' src='../images/down.gif' alt='$Gremark' width='18' height='18'>";	
			}
		else{
			if($Img_H==2){
				$Img_H="<div class='blueB' title='高清标准图审核中'>高清图审核中</div>";
				}
			else{
				$Img_H="&nbsp;";
				}
			}
		//图片3：Img_L
		$Img_L=$myRow["Img_L"];
		if($Img_L>=1){   //if($Img_L==1){
			$Img_L="T".$ProductId."_L.jpg";
			$Img_L=anmaIn($Img_L,$SinkOrder,$motherSTR);
			$td=anmaIn("download/newproductdata/",$SinkOrder,$motherSTR);
			$Img_L="<img onClick='OpenOrLoad(\"$td\",\"$Img_L\")' src='../images/down.gif' alt='$Gremark' width='18' height='18'>";	
			}
		else{
			if($Img_L==2){
				$Img_L="<div class='blueB' title='微缩标准图审核中'>微缩图审核中</div>";
				}
			else{
				$Img_L="&nbsp;";
				}
			}	
		$Code=$myRow["Code"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Code]' width='18' height='18'>";
		$Estate=$myRow["Estate"];
		switch($Estate){
			case 1:$Estate="<div class='greenB'>√</div>";	break;
			case 2:$Estate="<div class='yellowB' title='审核中'>√.</div>";	break;
			default:$Estate="<div class='redB'>×</div>";	break;
			}
		
		$PackingUnit=$myRow["PackingUnit"];
		$uResult = mysql_query("SELECT Name FROM $DataPublic.packingunit WHERE Id=$PackingUnit order by Id Limit 1",$link_id);
		if($uRow = mysql_fetch_array($uResult)){
			$PackingUnit=$uRow["Name"];
			}			
		$Unit=$myRow["Unit"];
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];
		//操作员姓名
		$Operator=$myRow["Operator"];
		include"../model/subprogram/staffname.php";
		$thisCId=$myRow["CompanyId"];		
		$TypeName=$myRow["TypeName"];
		if($myTask==1){
			$ValueArray=array(
				array(0=>$ProductId,			1=>"align='center'"),
				array(0=>$TestStandard,			2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$TypeName,				3=>"..."),
				array(0=>$saleRMB,		    1=>"align='center'"),
				array(0=>$Description,		1=>"align='center'"),
				array(0=>$Img_H,	        1=>"align='center'",	2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$Client,				3=>"..."),
				array(0=>$Estate,			1=>"align='center'"),
				array(0=>$Operator,			1=>"align='center'")
				);
			}
		else{
			$ValueArray=array(
				array(0=>$ProductId,			1=>"align='center'"),
				array(0=>$TestStandard,			2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$TypeName,				3=>"..."),				
				array(0=>$saleRMB,		    1=>"align='center'"),
				array(0=>$Description,		1=>"align='center'"),
				array(0=>$Img_H,	        1=>"align='center'",	2=>"onmousedown='window.event.cancelBubble=true;'"),
				array(0=>$Client,				3=>"..."),
				array(0=>$Estate,			1=>"align='center'"),
				array(0=>$Operator,			1=>"align='center'")
				);
			}
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>

<script language="JavaScript" type="text/JavaScript">
<!--
function updateJq(TableId,RowId,runningNum,toObj){//行即表格序号;列，流水号，更新源
	var InfoSTR="";
	var buttonSTR="";
	var theDiv=document.getElementById("Jp");
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';	
	if(theDiv.style.visibility=="hidden" || toObj!=ObjId || TableId!=tempTableId){
		document.form1.ActionTableId.value=TableId;
		document.form1.ActionRowId.value=RowId;
		document.form1.ObjId.value=toObj;
		switch(toObj){
			case 1:	//采购单交货期
				InfoSTR="更新采购流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的采购单交货期:<input name='DeliveryDate' type='text' id='DeliveryDate' size='10' maxlength='10' class='INPUT0100' onFocus='WdatePicker()' readonly>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate()'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'>";
				break;
			case 2:	//订单备注
				InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的<br>订单备注<input name='PackRemark' type='text' id='PackRemark' size='50' class='INPUT0100'><br>";
				break;
			case 3://出货方式
				InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的<br>出货方式<input name='ShipType' type='text' id='ShipType' size='50' class='INPUT0100'><br>";
				break;
			case 4://出货时间
				InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的出货时间<input name='DeliveryDate' type='text' id='DeliveryDate' size='10' maxlength='10' class='INPUT0100' onFocus='WdatePicker()' readonly>";
				break;
			case 5://采购备注
				InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的采购备注<input name='cgRemark' type='text' id='cgRemark' size='85' class='INPUT0100'>";
				break;
			case 6:
				InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的生管备注<input name='sgRemark' type='text' id='sgRemark' size='85' class='INPUT0100'>";
				break;
			/*	
			case 14://新出货时间  //add my zx 20100416
				InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的出货时间<input name='DeliveryDate' type='text' id='DeliveryDate' size='10' maxlength='10' class='INPUT0100' onFocus='WdatePicker()' readonly><br>未能及时出货原因（英文）<input name='UnDeliveryReson' type='text' id='UnDeliveryReson' size='50' class='INPUT0100'>";
				break;
			*/	
			case 14://新出货时间  //add my zx 20100416
				InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的预定出货时间<input name='DeliveryDate' type='text' id='DeliveryDate' size='10' maxlength='10' class='INPUT0100' onFocus='WdatePicker()' readonly>";
				break;
			case 141://新出货时间  //add my zx 20100416
				InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的出货时间<input name='DeliveryDateR' type='text' id='DeliveryDateR' size='10' maxlength='10' class='INPUT0100' onFocus='WdatePicker()' readonly>";
				break;					
			case 15://新出货时间  //add my zx 20100416
				InfoSTR="更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly><br>未能及时出货原因（英文）<input name='UnDeliveryReson' type='text' id='UnDeliveryReson' size='50' class='INPUT0100'>";
				break;	

			case 16://更新价格  //add my zx 20100511
				InfoSTR="更新产品ID号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的参考售价<input name='price' type='text' id='price' size='20' class='INPUT0100'>";
				break;	
			case 17://更新价格  //add my zx 20100628
				InfoSTR="更新产品ID号为<input name='runningNum' type='text' id='runningNum' value='"+runningNum+"' size='12' class='INPUT0000' readonly>的订单下限为：<input name='price' type='text' id='moq' size='20' class='INPUT0100'>";
				break;					
				
			}
		if(toObj>1){
			var buttonSTR="&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate()'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
			}
		infoShow.innerHTML=InfoSTR+buttonSTR;
		theDiv.className="moveRtoL";
		theDiv.filters.revealTrans.apply();//防止错误
		theDiv.filters.revealTrans.play(); //播放
		theDiv.style.visibility = "";
		theDiv.style.display="";
		}
	}

function CloseDiv(){
	var theDiv=document.getElementById("Jp");	
	theDiv.className="moveLtoR";
	theDiv.filters.revealTrans.apply();
	theDiv.style.visibility = "hidden";
	theDiv.filters.revealTrans.play();
	infoShow.innerHTML="";
	}

function aiaxUpdate(){
	var ObjId=document.form1.ObjId.value;
	var tempTableId=document.form1.ActionTableId.value;
	var tempRowId=document.form1.ActionRowId.value;
	var temprunningNum=document.form1.runningNum.value;
	switch(ObjId){
		case "1":		//更新采购单交货期:
			var tempDeliveryDate=document.form1.DeliveryDate.value;
			myurl="purchaseorder_updated.php?StockId="+temprunningNum+"&DeliveryDate="+tempDeliveryDate+"&ActionId=jq";
			retCode=openUrl(myurl);
			if (retCode!=-2){
				//更新成功,隐藏DIV，并且重新写该单元格的内容	或 重新动态更新需求单列表
				if(tempDeliveryDate==""){
					tempDeliveryDate="未确定";
					}
				eval("ListTB"+tempTableId).rows[tempRowId].cells[16].innerHTML="<span onclick='updateJq("+tempTableId+","+tempRowId+","+temprunningNum+",1)' style='CURSOR: pointer;color:#FF9966'>"+tempDeliveryDate+"</sapn>";
				CloseDiv();
				}
			break;
		case "2":		//订单说明 PackRemark
			var tempPackRemark0=document.form1.PackRemark.value;
			var tempPackRemark1=encodeURIComponent(tempPackRemark0);
			myurl="yw_order_updated.php?POrderId="+temprunningNum+"&tempPackRemark="+tempPackRemark1+"&ActionId=PackRemark";
			var ajax=InitAjax(); 
	　		ajax.open("GET",myurl,true);
			ajax.onreadystatechange =function(){
	　			if(ajax.readyState==4){// && ajax.status ==200
					eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempPackRemark0+"</NOBR></DIV>";
					CloseDiv();
					}
				}
			ajax.send(null); 			
			break;
		case "3":		//出货方式
			var tempShipType0=document.form1.ShipType.value;
			var tempShipType1=encodeURIComponent(tempShipType0);
			myurl="yw_order_updated.php?POrderId="+temprunningNum+"&tempShipType="+tempShipType1+"&ActionId=ShipType";
			retCode=openUrl(myurl);
			if (retCode!=-2){
				eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempShipType0+"</NOBR></DIV>";
				CloseDiv();
				}
			break;
		case "4":		//出货时间
			var tempDeliveryDate=document.form1.DeliveryDate.value;
			myurl="yw_order_updated.php?POrderId="+temprunningNum+"&DeliveryDate="+tempDeliveryDate+"&ActionId=DeliveryDate";
			retCode=openUrl(myurl);
			if (retCode!=-2){
				eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempDeliveryDate+"</NOBR></DIV>";
				CloseDiv();
				}
			break;
		case "5":
			var tempcgRemark=document.form1.cgRemark.value;
			var tempcgRemark1=encodeURIComponent(tempcgRemark);//传输中文
			myurl="yw_order_updated.php?POrderId="+temprunningNum+"&cgRemark="+tempcgRemark1+"&ActionId=cgRemark";
			retCode=openUrl(myurl);
			if (retCode!=-2){
				eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempcgRemark+"</NOBR></DIV>";
				CloseDiv();
				}
			break;
		case "6"://更新生管备注
			var tempsgRemark=document.form1.sgRemark.value;
			var tempsgRemark1=encodeURIComponent(tempsgRemark);//传输中文
			myurl="yw_order_updated.php?POrderId="+temprunningNum+"&sgRemark="+tempsgRemark1+"&ActionId=sgRemark";
			retCode=openUrl(myurl);
			if (retCode!=-2){
				eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempsgRemark+"</NOBR></DIV>";
				CloseDiv();
				}
			break;
		/*	
		case "14":		//出货时间 add by zx 20100416
			var tempDeliveryDate=document.form1.DeliveryDate.value;
			//alert(tempDeliveryDate);
			var tempUnDeliveryReson=document.form1.UnDeliveryReson.value;
			//alert(tempUnDeliveryReson);
			var tempStr="";
			var myurl="";
			tempUnDeliveryReson=strtrim(tempUnDeliveryReson);  //除掉空格
			
			if(tempUnDeliveryReson.length==0 && tempDeliveryDate.length>0)
			{
				myurl="yw_order_updated.php?POrderId="+temprunningNum+"&DeliveryDate="+tempDeliveryDate+"&ActionId=DeliveryDate";
				tempStr=tempDeliveryDate;
			}
			else
			{
				if(tempUnDeliveryReson.length>0){
					myurl="yw_order_updated.php?POrderId="+temprunningNum+"&UnDeliveryReson="+tempUnDeliveryReson+"&ActionId=UnDeliveryReson";
					tempStr=tempUnDeliveryReson;
				}
				
			}
			
			//alert(myurl);
			//alert(myurl.length);
			if (myurl.length>0)
			{
				retCode=openUrl(myurl);
				if (retCode!=-2){
				eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempStr+"</NOBR></DIV>";
				CloseDiv();
				}
			}
			
			break;
		*/
		
		case "14":		//出货时间 add by zx 20100416
			var tempDeliveryDate=document.form1.DeliveryDate.value;
			//alert(tempDeliveryDate);
			//var tempUnDeliveryReson=document.form1.UnDeliveryReson.value;
			//alert(tempUnDeliveryReson);
			var tempStr="";
			var myurl="";
			//tempUnDeliveryReson=strtrim(tempUnDeliveryReson);  //除掉空格
			if(tempDeliveryDate.length==0)
			{	
				var message=confirm("确定没有交货日期！？");
				if (message!=true){
					return false;
				}
				else
				{
					tempDeliveryDate="0000-00-00";
				}
			}
			
			if(tempDeliveryDate.length>0)
			{
				myurl="yw_order_updated.php?POrderId="+temprunningNum+"&DeliveryDate="+tempDeliveryDate+"&ActionId=DeliveryDate";
				if(tempDeliveryDate!="0000-00-00")
				{
					tempStr=tempDeliveryDate;
				}
				else
				{
					tempStr="";
				}
			}
			//alert(myurl);
			//alert(myurl.length);
			if (myurl.length>0)
			{
				retCode=openUrl(myurl);
				if (retCode!=-2){
				eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempStr+"</NOBR></DIV>";
				CloseDiv();
				}
			}
			
			break;

		case "141":		//出货时间 add by zx 20100416
			var tempDeliveryDateR=document.form1.DeliveryDateR.value;
			//alert(tempDeliveryDate);
			//var tempUnDeliveryReson=document.form1.UnDeliveryReson.value;
			//alert(tempUnDeliveryReson);
			var tempStr="";
			var myurl="";
			//tempUnDeliveryReson=strtrim(tempUnDeliveryReson);  //除掉空格
			if(tempDeliveryDateR.length==0)
			{	
				var message=confirm("确定没有交货日期！？");
				if (message!=true){
					return false;
				}
				else
				{
					tempDeliveryDateR="0000-00-00";
				}
			}
			
			if(tempDeliveryDateR.length>0)
			{
				myurl="yw_order_updated.php?POrderId="+temprunningNum+"&DeliveryDateR="+tempDeliveryDateR+"&ActionId=DeliveryDateR";
				if(tempDeliveryDateR!="0000-00-00")
				{
					tempStr=tempDeliveryDateR;
				}
				else
				{
					tempStr="";
				}
			}
			//alert(myurl);
			//alert(myurl.length);
			if (myurl.length>0)
			{
				retCode=openUrl(myurl);
				if (retCode!=-2){
				eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempStr+"</NOBR></DIV>";
				CloseDiv();
				}
			}
			
			break;


		case "15":		//未能及进出货原因 add by zx 20100416
			//var tempDeliveryDate=document.form1.DeliveryDate.value;
			//alert(tempDeliveryDate);
			var tempUnDeliveryReson=document.form1.UnDeliveryReson.value;
			tempUnDeliveryReson=strtrim(tempUnDeliveryReson);
			//alert(tempUnDeliveryReson);
			var tempStr="";
			var myurl="";
			//tempUnDeliveryReson=strtrim(tempUnDeliveryReson);  //除掉空格
			if(tempUnDeliveryReson.length>=0){
				myurl="yw_order_updated.php?POrderId="+temprunningNum+"&UnDeliveryReson="+tempUnDeliveryReson+"&ActionId=UnDeliveryReson";
				tempStr=tempUnDeliveryReson;
			}
			if (myurl.length>0)
			{
				retCode=openUrl(myurl);
				if (retCode!=-2){
				eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempStr+"</NOBR></DIV>";
				CloseDiv();
				}
			}
			
			break;
			
		case "16":		//更新价格 add by zx 20100511
			var tempprice=document.form1.price.value;
			tempprice=strtrim(tempprice);
			//alert(tempUnDeliveryReson);
			var tempStr="";
			var myurl="";
			//tempUnDeliveryReson=strtrim(tempUnDeliveryReson);  //除掉空格
			if(IsNum(tempprice)==1){
				myurl="NewProductData_updated.php?ProductId="+temprunningNum+"&price="+tempprice+"&ActionId=Price";
				tempStr=tempprice;
			}
			if (myurl.length>0)
			{
				//alert(myurl);
				retCode=openUrl(myurl);
				if (retCode!=-2){
				eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempStr+"</NOBR></DIV>";
				CloseDiv();
				}
			}
			
			break;		
			
		case "17":		//更新价格 add by zx 20100628
			var tempmoq=document.form1.moq.value;
			tempmoq=strtrim(tempmoq);
			//alert(tempUnDeliveryReson);
			var tempStr="";
			var myurl="";
			//tempUnDeliveryReson=strtrim(tempUnDeliveryReson);  //除掉空格
			if(IsNum(tempmoq)==1){
				myurl="NewProductData_updated.php?ProductId="+temprunningNum+"&Moq="+tempmoq+"&ActionId=Moq";
				tempStr=tempmoq;
			}
			if (myurl.length>0)
			{
				//alert(myurl);
				retCode=openUrl(myurl);
				if (retCode!=-2){
				eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempStr+"</NOBR></DIV>";
				CloseDiv();
				}
			}
			
			break;			
			

		}
	}

function strtrim(str){ //删除左右两端的空格
return str.replace(/(^\s*)|(\s*$)/g, "");
} 

function IsNum(s)
{
    if (s!=null && s!="")
    {
        return !isNaN(s);
    }
    return false;
}
//-->
</script>
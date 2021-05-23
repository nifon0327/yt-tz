<?php   
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 配件需求单更新");//需处理
$nowWebPage =$funFrom."_change";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤3：
//$tableWidth=1050;$tableMenuS=700;
$tableWidth=1130;$tableMenuS=700;
$CustomFun="<span onClick='ViewStuffId(7)' $onClickCSS>添加配件</span>&nbsp;";//自定义功能
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
$upResult = mysql_query("SELECT G.POrderId,G.Level,G.StockId,G.StuffId,(G.AddQty+G.FactualQty) AS Qty,
    D.StuffCname,D.Picture,S.OrderPO,C.Forshort 
FROM  $DataIn.cg1_stocksheet G 
INNER JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId  
LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=G.POrderId 
LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId WHERE G.Id=$Id",$link_id);
if ($upData = mysql_fetch_array($upResult)) {
    $OrderPO=$upData["OrderPO"]==""?"&nbsp;":$upData["OrderPO"];
	$Forshort=$upData["Forshort"]==""?"特采单":$upData["Forshort"];

	$mStockId=$upData["StockId"];
	$Level=$upData["Level"];
	$POrderId=$upData["POrderId"];
	$StuffId=$upData["StuffId"];
	$StuffCname=$upData["StuffCname"];
	$Picture=$upData["Picture"];
	include "../model/subprogram/stuffimg_model.php";
    include"../model/subprogram/stuff_Property.php";//配件属性 
   
	$Qty=$upData["Qty"];
	
	$checkActionResult=mysql_query("SELECT A.Name AS ActionName 
	    FROM $DataIn.cg1_semifinished G 
	    INNER JOIN $DataIn.yw1_scsheet S ON S.StockId=G.StockId 
	    INNER JOIN $DataIn.workorderaction A ON A.ActionId=S.ActionId 
	    WHERE G.mStockId='$mStockId' Limit 1",$link_id);
	if ($checkActionRow = mysql_fetch_array($checkActionResult)) {
	    $ActionName=$checkActionRow['ActionName'];
	}    
		
}
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,Id,$Id,ActionId,$ActionId,POrderId,$POrderId,mStockId,$mStockId,mStuffId,$StuffId,Level,$Level";
//echo $Parameter;
?>
	<input name="SafariReturnQty" id="SafariReturnQty" type="hidden" value="0"> 
    <table width='<?php  echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
		<tr>
			<td width="10" class="A0010">&nbsp;</td>
			<td height="25" colspan="3" valign="bottom"><span class="redB">◆半成品资料</div></td>
			<td height="22" colspan="3" align="right"><span class="redB">本页操作请谨慎</div></td>
			<td width="10" class="A0001">&nbsp;</td>
		</tr>
		<tr class="">
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
			<td width="60" height="22" class="A1111" align="center">客户</td>
			<td width="80" height="22" class="A1101" align="center">PO号</td>
			<td width="100" height="22" class="A1101" align="center">采购流水号</td>
			<td class="A1101" align="center">半成品名称</td>
			<td width="75" class="A1101" align="center">加工类型</td>
			<td width="75" class="A1101" align="center">订单数量</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
		<tr>
			<td width="10" class="A0010">&nbsp;</td>
			<td class="A0111" align="center"><?php    echo $Forshort?></td>
			<td class="A0101" align="center"><?php    echo $OrderPO?></td>
			<td class="A0101" align="center"><?php    echo $mStockId?></td>
			<td class="A0101"><?php    echo $StuffCname?></td>
			<td class="A0101" align="center"><?php    echo $ActionName?></td>
			<td class="A0101" align="center"><?php    echo $Qty?>
		    <input name="POrderQty" type="hidden" id="POrderQty" value="<?php    echo $Qty?>"></td>
			<td width="10" class="A0001">&nbsp;</td>
		</tr>
		<tr>
			<td width="10" class="A0010">&nbsp;</td>
			<td height="25" colspan="6" valign="bottom"><span class="redB">◆已有需求明细</span></td>
			<td width="10" class="A0001">&nbsp;</td>
		</tr>
	</table>
	<table width='<?php    echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
         <tr bgcolor='<?php    echo $Title_bgcolor?>'>
		<td width="10" class="A0010" >&nbsp;</td>
		<td align="center" class="A1111">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:auto">
                    
                <table width='100%' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" >
                  <tr>  
                    <td width="30" class="A0001" align="center">操作</td>
                    <td width="30" class="A0001" align="center">序号</td>
                    <td width="70" class="A0001" align="center">采购日期</td>
                    <td width="90" class="A0001" align="center">待购流水号</td>
                    <td width="350" class="A0001" align="center">配件名称</td>
                    <td width="60" class="A0001" align="center">配件价格</td>
                    <td width="60" class="A0001" align="center">需求数量</td>
                    <td width="60" class="A0001" align="center">使用库存</td>
                    <td width="60" class="A0001" align="center">采购数量</td>
                    <td width="60" class="A0001" align="center">增购数量</td>
                    <td width="50" class="A0001" align="center">采购员</td>
                    <td width="60" class="A0001" align="center">供应商</td>
                    <td width="" class="A0000" align="center">关联配件</td>
                  </tr>         
                </table>
            </div>		
            </td>
            <td width="10" class="A0001">&nbsp;</td>
        </tr>               
		
		<tr>
		<td width="10" class="A0010" >&nbsp;</td>
		<td align="center" class="A0010">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='100%' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id='StuffList'>
		<?php   
		//需求单列表
		$StockResult = mysql_query("SELECT 
			A.Id,A.StuffId,A.StockId,A.Price,A.OrderQty,A.StockQty,A.FactualQty,A.AddQty,A.Estate,B.StuffCname,C.Forshort,D.Name,E.Date,M.blSign  			
			FROM $DataIn.cg1_semifinished G 
			LEFT JOIN  $DataIn.cg1_stocksheet A ON A.StockId=G.StockId 
			LEFT JOIN $DataIn.stuffdata B ON B.StuffId=A.StuffId 
			LEFT JOIN $DataIn.trade_object C ON C.CompanyId=A.CompanyId 
			LEFT JOIN $DataPublic.staffmain D ON D.Number=A.BuyerId 
			LEFT JOIN $DataIn.cg1_stockmain E ON E.Id=A.Mid
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=B.TypeId
			LEFT JOIN $DataIn.stuffmaintype M ON M.Id=T.mainType 
			WHERE G.mStockId='$mStockId' 
			ORDER BY A.StockId",$link_id);
		if($StockRows = mysql_fetch_array($StockResult)){
			$i=1;
			do{
				$StuffId=$StockRows["StuffId"];
				
				//关联配件
				$UniteStuffId="";
				$G_Reslut=mysql_query("SELECT uStuffId FROM $DataIn.cg1_stuffunite WHERE POrderId='$POrderId' AND StuffId='$StuffId' ",$link_id);
				while($G_Row=mysql_fetch_array($G_Reslut)){
					$UniteStuffId.=$UniteStuffId==""?$G_Row["uStuffId"]:",".$G_Row["uStuffId"];
				}
				
				$blSign=$StockRows["blSign"];
				$FactualQty=$StockRows["FactualQty"];
				$StockId=$StockRows["StockId"];
				$StuffCname=$StockRows["StuffCname"];
				$Price=$StockRows["Price"];
				$OrderQty=$StockRows["OrderQty"];
				$StockQty=$StockRows["StockQty"];
				$AddQty=$StockRows["AddQty"];
				$Estate=$StockRows["Estate"];
				$Name=$StockRows["Name"]==""?"&nbsp;":$StockRows["Name"];
				$Forshort=$StockRows["Forshort"]==""?"&nbsp;":$StockRows["Forshort"];
				echo"<tr><td width='30' class='A0101' align='center'>";
				if ($blSign==1){
				   $Date=$StockRows["Date"]==""?$FactualQty==0?"使用库存":"未下采购单":$StockRows["Date"];	
				}
				else{
				   $Date="-";
				}
				 
				 
                //如果是客供配件，因为没有下采购单，有入库，那配件入库后不能删除
               include"../model/subprogram/stuff_Property.php";//配件属性
                /*if($ClientProSign==1){
                    $CheckRkQty  = mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS rkQty FROM $DataIn.ck1_rksheet G WHERE StockId='$StockId'",$link_id));
                    $rkQty=$CheckllQty["rkQty"];
	                if ($rkQty>0){
						     echo"&nbsp;入库";
						}
                 } 
                 */        
                 $CheckllQty=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet G WHERE StockId='$StockId' AND Estate=0",$link_id));
		  	    $llQty=$CheckllQty["llQty"];
                if ($llQty>0){
					     echo"&nbsp;领料";
					}
				else{			
					if($Estate!=4){	
						echo"<a href='#' onclick='deleteRow(this.parentNode,StuffList)' title='删除此配件需求单'>×</a>";
						}
					else{
						echo"<span class='redB'>删</span>";
						}
				}
				echo"</td><td width='30' class='A0101' align='center'>$i</td>";
				echo"<td width='70' class='A0101' align='center'>$Date</td>
					<td width='90' class='A0101' align='center'>$StockId</td>
					<input name='ExStuffId[]' type='hidden' id='ExStuffId$i' value='$StuffId' >
					<td width='350' class='A0101'>$StuffCname</td>
					<td width='60' class='A0101' align='center'>$Price</td>
					<td width='60' class='A0101' align='center'>$OrderQty</td>
					<td width='60' class='A0101' align='center'>$StockQty</td>
					<td width='60' class='A0101' align='center'>$FactualQty</td>
					<td width='60' class='A0101' align='center'>$AddQty</td>
					<td width='50' class='A0101' align='center'>$Name</td>
					<td width='60' class='A0101'>$Forshort</td>
					<td width='' class='A0101'>
					<input name='Unites[]' type='text' id='Unites$i' size='10' value='$UniteStuffId'  onclick='updateJq(this,$i,1,\"StuffList\")'  readonly>
					<input name='oldUnites[]' type='hidden' id='oldUnites$i' value='$UniteStuffId' >	
					</td>
					
					</tr>";
					//
					//<input name='Unites[]' type='text' id='Unites$i' size='10' value='$UniteStuffId'   readonly>
				$i++;
				}while($StockRows = mysql_fetch_array($StockResult));
			}
		?>
			</table>
		</div>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
	</table>
	<input name="hfield" type="hidden" id="hfield" value="0">
	<input name="oldMaxSID" type="hidden" id="oldMaxSID" value="<?php    echo $StockId?>">
	<input name="MaxSID" type="hidden" id="MaxSID" value="<?php    echo $StockId?>">
    
<table width='<?php    echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
        <td width="10" class="A0010" height="25">&nbsp;</td>
        <td valign="bottom" ><span class="redB">◆新增配件列表</span></td>
        <td width="10" class="A0001">&nbsp;</td>
    </tr>    
	<tr bgcolor='<?php    echo $Title_bgcolor?>'>
		<td width="10" class="A0010" >&nbsp;</td>
		<td  align="center" class="A1111">
		<div style="width:100%;height:100%; overflow-x:hidden;overflow-y:auto">
			<table width='100%' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" >
              <tr>  
                <td width="30" class="A0001" align="center">操作</td>
                <td width="30" class="A0001" align="center">序号</td>
                <td width="90"  class="A0001" align="center">新增流水号</td>
                <td width="420" class="A0001" align="center">新增配件</td>
                <td width="60" class="A0001" align="center">配件价格</td>
                <td width="60" class="A0001" align="center">需求数量</td>
                <td width="60" class="A0001" align="center">使用库存</td>
                <td width="60" class="A0001" align="center">采购数量</td>
                <td width="60" class="A0001" align="center">增购数量</td>
                <td width="50" class="A0001" align="center">采购员</td>
                <td width="60" class="A0001" align="center">供应商</td>
                <td width="" class="A0000" align="center">关联配件</td>
             
              </tr>         
			</table>
		</div>		
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
        
	<tr>
		<td width="10" class="A0010" height="300">&nbsp;</td>
		<td align="center" class="A0110">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:scroll; ">
			<table width='100%' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTable">
			</table>
		</div>		
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
</table>

<input name="StuffId0" id="StuffId0" type="hidden" value="">
<input name="pandsQty0"  id="pandsQty0" type="hidden" value="">
<input name="Unite0"  id="Unite0" type="hidden" value="">
    
<?php   
//步骤5：
echo"<div id='Jp' style='position:absolute;width:400px;; height:50px;z-index:1;visibility:hidden; background-color:#FFF' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
			<div class='in' id='infoShow'>
			</div>
	</div>";
	
include "../model/subprogram/add_model_b.php";
?>

<script LANGUAGE='JavaScript'  type="text/JavaScript">



function ViewStuffId(Action){
	var Message="";
	var num=Math.random();  
	BackData=window.showModalDialog("../admin/stuffdata_s1.php?r="+num+"&tSearchPage=stuffdata&fSearchPage=clientorder&SearchNum=1&Action="+Action,"BackData","dialogHeight =650px;dialogWidth=980px;center=yes;scroll=yes");
	
	if(BackData==null || BackData==''){  //专为safari设计的 ,add by zx 2011-05-04
		if(document.getElementById('SafariReturnValue')){
		//alert("return");
		var SafariReturnValue=document.getElementById('SafariReturnValue');
		BackData=SafariReturnValue.value;
		SafariReturnValue.value="";
		}
	}	
	//拆分
	if(BackData){
  		var Rows=BackData.split("``");//分拆记录:	
		var Rowslength=Rows.length;//数组长度即领料记录数
		
					//加入如下的代码****************************************
		if(document.getElementById("TempMaxNumber")){  ////给add by zx 2011-05-05 firfox and  safari不能用javascript生成的元素
			var TempMaxNumber=document.getElementById("TempMaxNumber");
			TempMaxNumber.value=TempMaxNumber.value*1+Rowslength*1;
		}	
			
		for(var i=0;i<Rowslength;i++){
			var Message="";
			var FieldTemp=Rows[i];		//拆分后的记录
			var FieldArray=FieldTemp.split("^^");//分拆记录中的字段：0配件ID|1配件名称|2配件价格|3可用库存|4采购|5供应商
			//过滤相同的配件ID号
			for(var j=0;j<ListTable.rows.length;j++){						
				var tempName=ListTable.rows[j].cells[3].innerText;			
				if(FieldArray[1]==tempName){//如果ID号存在
					Message="配件: "+FieldArray[1]+" 已存在!跳过继续！";
					break;
					}
				}
				
			if(Message==""){
				//要求输入数量对应关系
				var returnValue =window.showModalDialog("../admin/yw_order_relation.php",window,"dialogWidth=400px;dialogHeight=300px");
				//alert ("1");
				//alert(returnValue);
				if(returnValue==null || returnValue=='' || returnValue==0){  //专为safari设计的 ,add by zx 2011-05-04
					if(document.getElementById('SafariReturnQty')){
					//alert("return");
					var SafariReturnQty=document.getElementById('SafariReturnQty');
					returnValue=SafariReturnQty.value;
					//alert ("2");
					SafariReturnQty.value="";
					}
				}	
				
				if (returnValue){
					var qtyvalue=returnValue;
					var POrderQty=document.form1.POrderQty.value;
					var thisQty=POrderQty*eval(qtyvalue);//订单需求数
					thisQty=thisQty.toFixed(1);
					//使用库存数
					if(FieldArray[3]>=thisQty){
						var kqQty=thisQty;						
						}
					else{
						var kqQty=FieldArray[3];	
						}
					//alert("FieldArray[3]:"+FieldArray[3]);
					//alert("thisQty:"+kqQty);
					
					//实际需求数	采购数量				
					var fQty=thisQty-kqQty;
					//流水号
					var thisSID=(document.form1.MaxSID.value)*1+1;
					oTR=ListTable.insertRow(ListTable.rows.length);				
					//表格行数
					tmpNum=oTR.rowIndex+1;
					
					//第1列:序号
					oTD=oTR.insertCell(0);
					oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode,ListTable)' title='删除当前行'>×</a>";
					oTD.className ="A0100";
					oTD.align="center";
					oTD.width="30";
					oTD.onmousedown=function(){
						window.event.cancelBubble=true;};
					
					//第2列:操作
					oTD=oTR.insertCell(1);
					oTD.innerHTML="<input name='StuffId[]' type='hidden' id='StuffId"+tmpNum+"' size='10' value='"+FieldArray[0]+"'><input name='pandsQty[]' type='hidden' id='pandsQty"+tmpNum+"' size='10' value='"+qtyvalue+"'>"+tmpNum+"";
					oTD.className ="A0111";
					oTD.align="center";
					oTD.width="30";
					
					//第3列:流水号
					oTD=oTR.insertCell(2);
					oTD.innerHTML=""+thisSID+"";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="90";
					
					//4:配件名
					oTD=oTR.insertCell(3);
					oTD.innerHTML=""+FieldArray[1]+"";
					oTD.className ="A0101";
					oTD.width="420";
									//5：配件价格
					oTD=oTR.insertCell(4);
					oTD.innerHTML=""+FieldArray[2]+"";
					oTD.className ="A0101";		
					oTD.align="center";			
					oTD.width="60";
					
					//6:需求数量
					oTD=oTR.insertCell(5); 
					oTD.innerHTML=""+thisQty+"";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="60";
					 
					 //7：使用库存
					oTD=oTR.insertCell(6);
					oTD.innerHTML=""+kqQty+"";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="60";
					 
					 //8：采购数量
					oTD=oTR.insertCell(7);
					oTD.innerHTML=""+fQty+"";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="60";
					
					 //9：增购数量
					oTD=oTR.insertCell(8);
					oTD.innerHTML="0";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="60";
					
					 //10：采购
					oTD=oTR.insertCell(9);
					oTD.innerHTML=""+FieldArray[4]+"";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="50";
					
					//11:供应商
					oTD=oTR.insertCell(10);
					oTD.innerHTML=""+FieldArray[5]+"";				
					oTD.className ="A0101";		
					oTD.width="60";

					//第11列:关联
					oTD=oTR.insertCell(11);
					oTD.innerHTML="<input name='Unite[]' type='text'  id='Unite"+tmpNum+"' value=''  size='10' onclick='updateJq(this,"+tmpNum+",1,\"ListTable\")'  readonly/>";
					oTD.className ="A0100";
					oTD.align="center";
					oTD.width="";//100
								
					form1.MaxSID.value=thisSID;
					form1.hfield.value=tmpNum;
					}
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
	
function ShowSequence(TableTemp){
	if(TableTemp==ListTable){		
		var SIDTemp=document.form1.oldMaxSID.value;	//原有需求单最大值
		document.form1.MaxSID.value=SIDTemp;		//最大值重新初始化
		}
	for(i=0;i<TableTemp.rows.length;i++){ 
		var j=i+1;
  		TableTemp.rows[i].cells[1].innerText=j; 
		if(TableTemp==ListTable){//新增需求单列表
			TableTemp.rows[i].cells[2].innerText=(document.form1.MaxSID.value)*1+1;
			document.form1.MaxSID.value=SIDTemp=(document.form1.MaxSID.value)*1+1;
			}
		}
		
  }  

function deleteRow (RowTemp,TableTemp){
	if(RowTemp.parentElement==null || RowTemp.parentElement=="undefined" ){  // add by zx 2011-05-06 Firfox不支持 parentElement
		var rowIndex=RowTemp.parentNode.rowIndex;
	}
	else{
		var rowIndex=RowTemp.parentElement.rowIndex;
	}	
	
	if(TableTemp==ListTable){	//新增需求单列表
		TableTemp.deleteRow(rowIndex);
		ShowSequence(TableTemp);
		}
	else{//处理原需求单删除，删除成功后再删除行		
		var message=confirm("确定此配件需求单要标记删除吗？");
		if (message==true){
			//输入删除原因
			 var delRemark=prompt("请输入删除需求单的原因","");
			if(delRemark!=""){ 
				//var delStockId=TableTemp.rows[rowIndex].cells[3].innerText;
				var delStockId=TableTemp.rows[rowIndex].cells[3].innerHTML;
				delRemark=encodeURIComponent(delRemark);
				myurl="pt_order_updated.php?StockId="+delStockId+"&ActionId=delStuff&StockRemark="+delRemark;
				var delinfo="";
				TableTemp.rows[rowIndex].cells[0].innerHTML="<div style='background-color:#FF0000'  >.删.</div>"; 
				var ajax=InitAjax(); 
				ajax.open("GET",myurl,true);
				ajax.onreadystatechange =function(){
					if(ajax.readyState==4){// && ajax.status ==200		
						    TableTemp.rows[rowIndex].cells[0].innerHTML="&nbsp;";
					    }	
				}
				ajax.send(null); 
			}
			else{
				alert("没有输入删除原因!");return false;
				}
			}
		else{
			return false;
			}			
		}	
	}
function CheckForm(){
	//passvalue("StuffId|pandsQty|Unite");  //add by zx 2011-05-05 必须与上面隐藏传递元素id0号一致,Pid0
	document.form1.action="pt_order_updated.php?ActionId=23";
	document.form1.submit();
}


function updateJq(e,RowId,toObj,tableName){
	var InfoSTR="";
	var buttonSTR="";
	var runningNum="";
	
	var theDiv=document.getElementById("Jp");
	var infoShow=document.getElementById("infoShow");
	
	var ObjId=document.form1.ObjId.value;
	if(theDiv.style.visibility=="hidden" || toObj!=ObjId ){
		document.form1.ObjId.value=toObj;
		switch(toObj){
			case 1:	//选择表中配件
			   var rows=StuffList.rows.length;
			   
			    theDiv.style.width=400;
			    theDiv.style.height=rows*25+20;
			   
			   infoShow.style.width=400;
			   infoShow.style.height=rows*25+20;

			   var eValue=e.value.split(",");
			   for(var j=0;j<StuffList.rows.length;j++){
				     if(tableName=='StuffList'){
						var tmpRowId=RowId; 
					 }
					 else{
						var tmpRowId=0;  
					 }
					 //if (j!=RowId-1){
			         if (j!=tmpRowId-1){
				           //var stuffId=StuffList.rows[j].cells[12].innerText;
						   var  tmpj=j+1;
						   var  stuffId=document.getElementById("ExStuffId"+tmpj).value;
				           var stuffcname=StuffList.rows[j].cells[4].innerText;
				           var checkSign="";
				           for(n=0;n<eValue.length;n++){
					           if (eValue[n]==stuffId) checkSign=" checked ";
				           }
				          InfoSTR+="&nbsp;<input type='checkbox' name='stuffCheckId[]'  id='stuffCheckId' value='"+stuffId+"' "+checkSign+">&nbsp;&nbsp;"+stuffId+"—"+stuffcname+"</br>"; 
			         }
			   } 
			   
			   var rows=ListTable.rows.length;
			   var eValue=e.value.split(",");
			   for(var j=0;j<ListTable.rows.length;j++){
				     if(tableName=='ListTable'){
						var tmpRowId=RowId; 
					 }
					 else{
						var tmpRowId=0;  
					 }				   
			         //if (j!=RowId){
					 if (j!=tmpRowId-1){	 
				           //var stuffId=ListTable.rows[j].cells[3].innerText;
						    var  tmpj=j+1;
						   var  stuffId=document.getElementById("StuffId"+tmpj).value;
				           var stuffcname=ListTable.rows[j].cells[3].innerText;
				           var checkSign="";
				           for(n=0;n<eValue.length;n++){
					           if (eValue[n]==stuffId) checkSign=" checked ";
				           }
				          InfoSTR+="&nbsp;<input type='checkbox' name='stuffCheckId[]'  id='stuffCheckId' value='"+stuffId+"' "+checkSign+">&nbsp;&nbsp;"+stuffId+"—"+stuffcname+"</br>"; 
			         }
			   } 
			   
			   
				break;
			case 2:	//选择工序
				<?PHP 
				   $echoInfo="";$rows=0;
					$TypeResult = mysql_query("SELECT Id,Name FROM $DataIn.pands_process WHERE  Estate=1 ORDER BY Id",$link_id);
		          if($TypeRow = mysql_fetch_array($TypeResult)){
				  do{
				        $rows++;
					    $echoInfo.="&nbsp;<input type='checkbox' name='ProcessCheckId[]'  id='ProcessCheckId' value='$TypeRow[Id]|$TypeRow[Name]'>&nbsp;&nbsp;$TypeRow[Name] </br>";
					  } while($TypeRow = mysql_fetch_array($TypeResult));
			      }
				?>
				infoShow.style.width=100;
				 infoShow.style.height=<?php echo $rows; ?>*25+35;
				 
				 theDiv.style.width=100;
				 theDiv.style.height=<?php echo $rows; ?>*25+35;
				 
				 InfoSTR="<?php echo $echoInfo; ?>"+"<br>";
				break;
				
			}

		var buttonSTR="&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='确  定' onclick=' setValue("+RowId+","+toObj+",\""+tableName+"\")'>&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取  消' onclick='CloseDiv()'>";

		infoShow.innerHTML=InfoSTR+buttonSTR;
		theDiv.style.top=event.clientY + document.body.scrollTop+'px';
	    theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';
       
		//theDiv.className="moveRtoL";
		theDiv.style.visibility = "";
		theDiv.style.display="";
		
		if (toObj==2){
				  var ProcessIdName="ProcessId"+RowId; 
			      var IdValue=document.getElementById(ProcessIdName).value;
			      
			      var eValue=IdValue.split(",");
			      var ProcessCheckId=document.getElementsByName("ProcessCheckId[]");
			       for(var j=0;j<ProcessCheckId.length;j++){
			               var checkValue=ProcessCheckId[j].value.split("|");
				           for(n=0;n<eValue.length;n++){
					             if (eValue[n]==checkValue[0]) ProcessCheckId[j].checked=true ;
				           }
	             }
		    }
		}
}

function CloseDiv(){
	var theDiv=document.getElementById("Jp");	
	//theDiv.className="moveLtoR";
	theDiv.style.visibility = "hidden";
	infoShow.innerHTML="";
	}
	
function setValue(RowId,toObj,tableName)
{
    switch(toObj){
	    case 1:
	       var returnId="";
	       var stuffCheckId=document.getElementsByName("stuffCheckId[]");
	        for(var j=0;j<stuffCheckId.length;j++){
	            if (stuffCheckId[j].checked){
		            returnId+=returnId==""?stuffCheckId[j].value:","+stuffCheckId[j].value;
	            }
	        }
			
		   if(tableName=='StuffList'){
	       		var UniteName="Unites"+RowId; 
		   }
		   else{
			   var UniteName="Unite"+RowId;
		   }
	       document.getElementById(UniteName).value=returnId;
	       break;
	   case 2:
	      var returnId=""; var returnValue="";
	      var ProcessCheckId=document.getElementsByName("ProcessCheckId[]");
	        for(var j=0;j<ProcessCheckId.length;j++){
	            if (ProcessCheckId[j].checked){
	               var checkValue=ProcessCheckId[j].value.split("|");
		            returnId+=returnId==""?checkValue[0]:","+checkValue[0];
		            returnValue+=returnValue==""?checkValue[1]:","+checkValue[1];
	            }
	        }
	        var ProcessName="Process"+RowId; 
	        document.getElementById(ProcessName).value=returnValue;
	        
	         var ProcessIdName="ProcessId"+RowId; 
	         document.getElementById(ProcessIdName).value=returnId;
	      break;
    }
     CloseDiv();
}

</script>

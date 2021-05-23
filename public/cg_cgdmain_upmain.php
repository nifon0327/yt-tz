<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新采购单");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_upmain";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤3：//需处理
$upResult = mysql_query("SELECT M.CompanyId,M.BuyerId,M.PurchaseID,M.DeliveryDate,M.Remark,M.Date,A.Forshort,B.Name
FROM $DataIn.cg1_stockmain M 
LEFT JOIN $DataIn.trade_object A ON A.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.staffmain B ON B.Number=M.BuyerId WHERE M.Id=$Mid LIMIT 1",$link_id);
if($upData = mysql_fetch_array($upResult)){
	$CompanyId=$upData["CompanyId"];
	$BuyerId=$upData["BuyerId"];
	$PurchaseID=$upData["PurchaseID"];	
	$DeliveryDate=$upData["DeliveryDate"]=="0000-00-00"?"":$upData["DeliveryDate"];	
	$Remark=$upData["Remark"]==""?"&nbsp;":$upData["Remark"];
	$Date=$upData["Date"];
	$Forshort=$upData["Forshort"];
	$Name=$upData["Name"];
	}
//步骤4：
$tableWidth=870;$tableMenuS=550;
$CheckFormURL="thisPage";
$CustomFun="<span onClick='ViewStockId(7)' $onClickCSS>添加需求单</span>&nbsp;";//自定义功能
include "../model/subprogram/add_model_t.php";
$Parameter="funFrom,$funFrom,From,$From,CompanyId,$CompanyId,Mid,$Mid,BuyerId,$BuyerId,TempValue,,chooseDate,$chooseDate,PayMode,$PayMode";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
	<td width="10" height="20" class="A0010">&nbsp;</td>
	  <td colspan="6" valign="bottom"><span class="redB">一、主采购单信息</span><input name="Mid" type="hidden" id="Mid" value="<?php  echo $Mid?>">
	  </td>
	  <td width="10" class="A0001">&nbsp;</td>
	</tr>
	<tr class="">
		<td bgcolor="#FFFFFF" width="10" height="20" class="A0010">&nbsp;</td>
		<td width="50" align="center" class="A1111">采购单号</td>
		<td width="50" align="center" class="A1101">采购</td>
		<td width="80" align="center" class="A1101">供 应 商</td>
		<td width="80" align="center" class="A1101">采购日期</td>
		<td width="80" align="center" class="A1101">交货日期</td>
		<td width="490" align="center" class="A1101">采购备注</td>
		<td bgcolor="#FFFFFF" width="10" class="A0001">&nbsp;</td>
	</tr>
		<td width="10" height="20" class="A0010">&nbsp;</td>
		<td class="A0111"><?php  echo $PurchaseID?></td>
		<td class="A0101" align="center"><?php  echo $Name?></td>
		<td class="A0101" align="center"><?php  echo $Forshort?></td>
		<td class="A0101" align="center"><input name="cgDate" class="noLine" type="text" id="cgDate" value="<?php  echo $Date?>" size="7" onfocus="WdatePicker()" readonly></td>
		<td class="A0101" align="center"><input name="DeliveryDate" class="noLine" type="text" id="DeliveryDate" value="<?php  echo $DeliveryDate?>" size="7" onfocus="WdatePicker()" readonly disabled></td>
		<td class="A0101" ><input name="Remark" class="noLine" type="text" id="Remark" value="<?php  echo $Remark?>" size="75"></td>
		<td width="10" class="A0001">&nbsp;</td>
	<tr>
		<td width="10" height="25" class="A0010">&nbsp;</td>
		<td colspan="6" valign="bottom">
		<span class="redB">二、已有需求明细</span>&nbsp;&nbsp;</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
</table>

<table width='<?php  echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
		<!--
        <tr class="">
			<td bgcolor="#FFFFFF" width="10" class="A0010">&nbsp;</td>
			<td width="30" height="22" class="A1111" align="center">操作</td>
			<td width="30" height="22" class="A1101" align="center">序号</td>
			<td width="100" height="22" class="A1101" align="center">需求流水号</td>
			<td width="370" class="A1101" align="center">配件名称</td>
			<td width="70" class="A1101" align="center">配件价格</td>
			<td width="60" class="A1101" align="center">订单数量</td>
			<td width="60" class="A1101" align="center">使用库存</td>
			<td width="60" class="A1101" align="center">需购数量</td>
			<td width="70" class="A1101" align="center">增购数量</td>
			<td bgcolor="#FFFFFF" width="10" class="A0001">&nbsp;</td>			
		</tr>
        -->
		<tr>
		  <td class="A0010">&nbsp;</td>
		  <td  class="A1111">
		  <div style="width:845;overflow-x:hidden;overflow-y:no">
			<table width='843' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
              <tr>
                <td width="30" height="22" class="A0001" align="center">操作</td>
                <td width="30"  class="A0001" align="center">序号</td>
                <td width="100"  class="A0001" align="center">需求流水号</td>
                <td width="370" class="A0001" align="center">配件名称</td>
                <td width="70" class="A0001" align="center">配件价格</td>
                <td width="60" class="A0001" align="center">订单数量</td>
                <td width="60" class="A0001" align="center">使用库存</td>
                <td width="60" class="A0001" align="center">需购数量</td>
                <td width=""  align="center">增购数量</td>    
              </tr>          
			</table>
			</div>
		  </td>
		  <td class="A0001">&nbsp;</td>
 		</tr>                    
		<tr>
		  <td class="A0010">&nbsp;</td>
		  <td height="150" class="A0111">
		  <div style="width:845;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='843' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id='StuffList' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' >
		<?php 
		//需求单列表
		$StockResult = mysql_query("SELECT S.Id,S.StockId,S.POrderId,S.Estate,S.Locks,S.StuffId,S.Price,S.BuyerId,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.DeliveryDate,S.StockRemark,D.StuffCname 
			FROM $DataIn.cg1_stocksheet S 
			LEFT JOIN $DataIn.stuffdata D ON S.StuffId=D.StuffId 			
			WHERE S.Mid=$Mid Order by S.StockId",$link_id);
		if($StockRows = mysql_fetch_array($StockResult)){
			$i=1;
			do{
				$Id=$StockRows["Id"];
				$StockId=$StockRows["StockId"];
				$StuffCname=$StockRows["StuffCname"];
				$Price=$StockRows["Price"];
				$OrderQty=$StockRows["OrderQty"];
				$StockQty=$StockRows["StockQty"];
				$FactualQty=$StockRows["FactualQty"];
				$AddQty=$StockRows["AddQty"];
				$Locks=$StockRows["Locks"];
				//请款检查
				$qkTemp=mysql_query("SELECT Id FROM $DataIn.cw1_fkoutsheet WHERE StockId='$StockId' ORDER BY Id LIMIT 1",$link_id);
				if($qkRow = mysql_fetch_array($qkTemp)){
					$qkEstate=1;
					}
				else{
					$qkEstate=0;
					}
					
				//检查需求单是否有下述情况：已请款成功、已结付、已领料、已收货；如果有，则不能还原
				//收货检查
				$inTemp=mysql_query("SELECT Id FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' ORDER BY Id LIMIT 1",$link_id);
				if($inRow = mysql_fetch_array($inTemp)){
					$inDepot=1;
					}
				else{
					$inDepot=0;
					}
				
				//领料检查
				$outTemp=mysql_query("SELECT Id FROM $DataIn.ck5_llsheet WHERE StockId='$StockId' ORDER BY Id LIMIT 1",$link_id);
				if($outRow = mysql_fetch_array($outTemp)){
					$outDepot=1;
					}
				else{
					$outDepot=0;
					}
					
				//如果记录已解锁，且没有请款，则可以更新单价，重新需审核 
				
				if($Estate==1 || $Locks==0 || $inDepot==1 || $outDepot==1 || $qkEstate==1){//不可还原
						$Cencel="&nbsp;";						
						}
					else{
						$Cencel="<a href='#' onclick='deleteRow(this.parentNode,StuffList,$Id)' title='还原此配件需求单'>×</a>";
						}
				
				//$Cencel="<a href='#' onclick='deleteRow(this.parentNode,StuffList,$Id)' title='还原此配件需求单'>×</a>";
				//是否可以更新单价
				$j=$i-1;
				
				if($Locks==1 && $qkEstate==0){
				//if($Locks>=0){
					$Price="<input name='newStuffPrice$j' type='text' id='newStuffPrice$j' style='width: 50px;color: #009900;' value='$Price' onChange='ChangeThis($j,$Id)' onfocus='toTempValue(this.value)'>";
					}
				echo"<tr><td width='30' class='A0101' align='center'>$Cencel</td>
						<td width='30' class='A0101' align='center'>$i</td>
						<td width='100' class='A0101' align='center'>$StockId</td>
						<td width='370' class='A0101'>$StuffCname</td>
						<td width='70' class='A0101' align='center'>$Price</td>
						<td width='60' class='A0101' align='center'>$OrderQty</td>
						<td width='60' class='A0101' align='center'>$StockQty</td>
						<td width='60' class='A0101' align='center'>$FactualQty</td>
						<td width='' class='A0101' align='center'>$AddQty</td>
					</tr>";
				$i++;
				}while($StockRows = mysql_fetch_array($StockResult));
			}
		?>	</table>
			</div>
		  </td>
		  <td class="A0001">&nbsp;</td>
 		</tr>
</table>

	<table width='<?php  echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td width="10" class="A0010" height="25">&nbsp;</td>
	<td valign="bottom"><span class="redB" >三、新增需求单列表</div></td>
	<td width="10" class="A0001">&nbsp;</td>
	</tr></table>
    <!--
	<table width='<=$tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
		
        <tr class="">
			<td bgcolor="#FFFFFF" width="10" class="A0010">&nbsp;</td>
			<td width="30" height="22" class="A1111" align="center">操作</td>
			<td width="30" height="22" class="A1101" align="center">序号</td>
			<td width="100" height="22" class="A1101" align="center">需求流水号</td>
			<td width="370" class="A1101" align="center">配件名称</td>
			<td width="70" class="A1101" align="center">配件价格</td>
			<td width="60" class="A1101" align="center">订单数量</td>
			<td width="60" class="A1101" align="center">使用库存</td>
			<td width="60" class="A1101" align="center">需购数量</td>
			<td width="70" class="A1101" align="center">增购数量</td>
			<td bgcolor="#FFFFFF" width="10" class="A0001">&nbsp;</td>			
		</tr>
        --->
        <!---
		<tr>
		  <td class="A0010">&nbsp;</td>
		  <td   class="A1011">
			<div style="width:845;overflow-x:hidden;overflow-y:no" >
            
				<table width='840' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' >
                   <tr>
                    <td width="30" height="22" class="A0001" align="center">操作</td>
                    <td width="30" class="A0001" align="center">序号</td>
                    <td width="100" class="A0001" align="center">需求流水号</td>
                    <td width="360" class="A0001" align="center">配件名称</td>
                    <td width="70" class="A0001" align="center">配件价格</td>
                    <td width="60" class="A0001" align="center">订单数量</td>
                    <td width="60" class="A0001" align="center">使用库存</td>
                    <td width="60" class="A0001" align="center">需购数量</td>
                    <td width="" class="A0000" align="center">增购数量</td>
                   </tr> 
				</table>
			</div>	
			</td>
		  <td class="A0001">&nbsp;</td>
	  </tr>        
        -->
 
<table width='<?php  echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">

		<tr>
		  <td class="A0010">&nbsp;</td>
		  <td  class="A1111">
		  <div style="overflow-x:hidden;overflow-y:no">
			<table width='840' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
              <tr>
                <td width="30" height="22" class="A0001" align="center">操作</td>
                <td width="30"  class="A0001" align="center">序号</td>
                <td width="100"  class="A0001" align="center">需求流水号</td>
                <td width="360" class="A0001" align="center">配件名称</td>
                <td width="70" class="A0001" align="center">配件价格</td>
                <td width="60" class="A0001" align="center">订单数量</td>
                <td width="60" class="A0001" align="center">使用库存</td>
                <td width="60" class="A0001" align="center">需购数量</td>
                <td width=""  align="center">增购数量</td>    
              </tr>          
			</table>
			</div>
		  </td>
		 <td  class="A0001">&nbsp;</td>
 		</tr>                    
		<tr>
		  <td class="A0010">&nbsp;</td>
		  <td height="150" class="A0111">
		  <div style="height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='830' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id='ListTable' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' >       

			</table>
			</div>	
			</td>
		 <td  class="A0001">&nbsp;</td> 
	  </tr>
</table>  

<?php 
include "../model/subprogram/add_model_b.php";
?>
<script  src='../model/IE_FOX_MASK.js' type=text/javascript></script>
<script language = "JavaScript">
//窗口打开方式修改为兼容性的模态框 by ckt 2018-01-07
function ViewStockId(Action){
    var SafariReturnValue = document.getElementById('SafariReturnValue');
    if (!arguments[1]) {
        var BuyerId=document.form1.BuyerId.value;
        var CompanyId=document.form1.CompanyId.value;
        var num=Math.random();
        SafariReturnValue.value = "";
        SafariReturnValue.callback = 'ViewStockId("",true)';
        var url = "/public/cg_cgdmain_s1.php?r="+num+"&BuyerId="+BuyerId+"&CompanyId="+CompanyId+"&tSearchPage=cg_cgdmain&fSearchPage=cg_cgdmain&SearchNum=2&Action=2&uType=1";
        openFrame(url, 980, 650);//url需为绝对路径
        return false;
    }
    var Message="";
	//拆分
	if(SafariReturnValue.value){
  		var Rows=SafariReturnValue.value.split("``");//分拆记录:
        SafariReturnValue.value = "";
        SafariReturnValue.callback = "";
		var Rowslength=Rows.length;
		for(var i=0;i<Rowslength;i++){
			var Message="";
			var FieldTemp=Rows[i];		//拆分后的记录
			var FieldArray=FieldTemp.split("^^");//分拆记录中的字段：需求流水号|配件名称|配件价格|需求数量|使用库存|采购数量|增购数量
			//过滤相同的配件ID号
			for(var j=0;j<ListTable.rows.length;j++){						
				var SIdtemp=ListTable.rows[j].cells[2].innerText;				
				if(FieldArray[0]==SIdtemp){//如果流水号存在
					Message="配件需求单: "+FieldArray[0]+" 已存在!跳过继续！";
					break;
					}
				}
			if(Message==""){
				oTR=ListTable.insertRow(ListTable.rows.length);				
				//表格行数
				tmpNum=oTR.rowIndex+1;
				
				//1.操作
				oTD=oTR.insertCell(-1);
				oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode,ListTable)' title='取消此配件需求单'>×</a>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="30";
				
				//2.序号
				oTD=oTR.insertCell(-1);
				oTD.innerHTML=""+tmpNum+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="30";
				
				//3.需求流水号
				oTD=oTR.insertCell(-1);
				oTD.innerHTML=""+FieldArray[0]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="100";
				
				//4.配件名称
				oTD=oTR.insertCell(-1);
				oTD.innerHTML=""+FieldArray[1]+"";
				oTD.className ="A0101";
				oTD.width="360";
					
				//5.配件价格
				oTD=oTR.insertCell(-1);
				oTD.innerHTML=""+FieldArray[2]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";
				
				//6.订单数量
				oTD=oTR.insertCell(-1);
				oTD.innerHTML=""+FieldArray[3]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="60";
				
				//使用库存
				oTD=oTR.insertCell(-1); 
				oTD.innerHTML=""+FieldArray[4]+"";
				oTD.className ="A0101";
				oTD.align="center";	
				oTD.width="60";
				
				//采购数量
				oTD=oTR.insertCell(-1);
				oTD.innerHTML=""+FieldArray[5]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="60";
				
				//增购数量
				oTD=oTR.insertCell(-1);
				oTD.innerHTML=""+FieldArray[6]+"";
				oTD.className ="A0100";
				oTD.align="center";
				oTD.width="";
				}
			else{
				alert(Message);
				}
			}//end for
			return true;
		}
	else{
		alert("没有选取配件需求单！");
		return false;
		}
	}

function ShowSequence(TableTemp){
	for(i=0;i<TableTemp.rows.length;i++){ 
  		var j=i+1;
		//TableTemp.rows[i].cells[1].innerText=j;
		TableTemp.rows[i].cells[1].innerHTML=j;
		}
	}   

function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}

//2.还原需求单
function deleteRow (RowTemp,TableTemp,IdTemp){
	//var rowIndex=RowTemp.parentElement.rowIndex;
	var rowIndex;
	if(RowTemp.parentElement==null || RowTemp.parentElement=="undefined" ){  //add by zx 2011-05-31 Firfox不支持 parentElement，只支持parentNode
		//alert("downMove2")
		rowIndex=RowTemp.parentNode.rowIndex;
	}
	else{
		rowIndex=RowTemp.parentElement.rowIndex;
	}
	
	if(TableTemp==ListTable){
		TableTemp.deleteRow(rowIndex);
		ShowSequence(TableTemp);
		}
	else{
		//判断是否最后一个采购单
		var Rows=TableTemp.rows.length;
		//处理删除，删除成功后再删除行;如果是最后一个需求单，则清除后回转采购列表
		var message=confirm("确定要还原此配件需求单吗?");
		if (message==true){
			myurl="cg_cgdmain_updated.php?Id="+IdTemp+"&ActionId=27";
			/*
			retCode=openUrl(myurl);
			if (retCode!=-2){
				alert("已还原需求单！");
				TableTemp.deleteRow(rowIndex);
				ShowSequence(TableTemp);
				}
			else{
				alert("还原需求单失败！");return false;
				}
			*/
				showMaskBack();  // add by zx 加入庶影   20110323  IE_FOX_MASK.js
				var ajax=InitAjax(); 
				ajax.open("GET",myurl,true);
				//alert(myurl);
				ajax.onreadystatechange =function(){
					if(ajax.readyState==4){// && ajax.status ==200		
						alert("已还原需求单！");
						TableTemp.deleteRow(rowIndex);
						ShowSequence(TableTemp);
						closeMaskBack();    //add by zx 关闭庶影   20110323   add by zx 加入庶影   20110323  IE_FOX_MASK.js
					}	
					else{

						  //alert("标记删除失败！");return false;
						}
				}
				ajax.send(null); 


			}
		else{
			return false;
			}
		}	
	}

//3.更新单价
function ChangeThis(Row,IdTemp){
	
	
	var oldValue=document.form1.TempValue.value;//改变前的值
	var Pricetemp=eval("document.form1.newStuffPrice"+Row).value;//改变后的值
	var Result=fucCheckNUM(Pricetemp,'Price');
	if(Result==0){
		alert("输入不正确的售价:"+Pricetemp+",重新输入!");
		eval("document.form1.newStuffPrice"+Row).value=oldValue;
		}
	else{
		var AddRemark=window.prompt("请输入价格更新备注:"); 
		AddRemark=encodeURIComponent(AddRemark);
		if (AddRemark){
			myurl="cg_cgdmain_updated.php?Id="+IdTemp+"&ActionId=923&Price="+Pricetemp+"&AddRemark="+AddRemark;
			/*
			retCode=openUrl(myurl);
			if(retCode!=-2){
				alert("单价更新成功，需重新审核才能请款!");
				StuffList.rows[Row].cells[0].innerHTML="&nbsp;";
				StuffList.rows[Row].cells[4].innerText=Pricetemp;
				}
			else{
				alert("单价更新失败");
				}  */
				showMaskBack();  // add by zx 加入庶影   20110323  IE_FOX_MASK.js
				var ajax=InitAjax(); 
				ajax.open("GET",myurl,true);
				//alert(myurl);
				ajax.onreadystatechange =function(){
					if(ajax.readyState==4){// && ajax.status ==200		
						alert("单价更新成功，需重新审核才能请款!");
						StuffList.rows[Row].cells[0].innerHTML="&nbsp;";
						StuffList.rows[Row].cells[4].innerHTML=Pricetemp;
						closeMaskBack();    //add by zx 关闭庶影   20110323   add by zx 加入庶影   20110323  IE_FOX_MASK.js
					}	
					else{

						  //alert("标记删除失败！");return false;
						}
				}
				ajax.send(null); 				
				
			}
		else{
			alert("没有输入价格更新备注，不能更新价格！");
			return false;
			}		
		}
	}
	
//1/4:主单信息更新和追加需求单
function CheckForm(ALType){
	var StockIds="";
	var StockTemp="";	
	for(i=0;i<ListTable.rows.length;i++){ 
		//StockTemp=ListTable.rows[i].cells[2].innerText;
		StockTemp=ListTable.rows[i].cells[2].innerHTML;
		if(StockIds==""){
			StockIds=StockTemp;
			}
		else{
			StockIds=StockIds+","+StockTemp;
			}
		}
	document.form1.action="cg_cgdmain_updated.php?StockIds="+StockIds+"&ActionId=933";//主单更新
	document.form1.submit();
	}
</script>

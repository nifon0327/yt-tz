<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cg1_stocksheet
$DataIn.stuffdata	
$DataIn.cw1_fkoutsheet
$DataIn.cg1_stockmain
$DataIn.trade_object
$DataIn.staffmain
$DataIn.ck1_rksheet
$DataIn.ck5_llsheet
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新采购单");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_upmain";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤3：//需处理
/*
$upResult = mysql_query("SELECT M.CompanyId,M.BuyerId,M.PurchaseID,M.DeliveryDate,M.Remark,M.Date,A.Forshort,B.Name
FROM $DataIn.cg1_stockmain M 
LEFT JOIN $DataIn.trade_object A ON A.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.staffmain B ON B.Number=M.BuyerId WHERE M.Id=$Mid LIMIT 1",$link_id);
*/
$upResult = mysql_query("SELECT M.BillNumber,M.Date,M.Remark
FROM $DataIn.gys_shmain M where 
M.Id=$Mid LIMIT 1",$link_id);
/*
echo "SELECT M.BillNumber,M.Date,M.Remark
FROM $DataIn.gys_shmain M where 
M.Id=$Mid LIMIT 1 <br>";
*/
if($upData = mysql_fetch_array($upResult)){
	$BillNumber=$upData["BillNumber"];	
	$Date=$upData["Date"];
	$Remark=$upData["Remark"]==""?"&nbsp;":$upData["Remark"];
	}
//步骤4：
//$tableWidth=870;$tableMenuS=550;
$tableWidth=750;$tableMenuS=550;
$CheckFormURL="thisPage";
//$CustomFun="<span onClick='ViewStockId(7)' $onClickCSS>添加需求单</span>&nbsp;";//自定义功能
include "../Admin/subprogram/add_model_t.php";
$Parameter="funFrom,$funFrom,From,$From,CompanyId,$CompanyId,Mid,$Mid,BuyerId,$BuyerId,TempValue,,chooseDate,$chooseDate,GysPayMode,$GysPayMode";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
	<td width="10" height="20" class="A0010">&nbsp;</td>
	  <td colspan="3" valign="bottom"><span class="redB">一、主送货单信息</span><input name="Mid" type="hidden" id="Mid" value="<?php  echo $Mid?>">
	  </td>
	  <td width="10" class="A0001">&nbsp;</td>
	</tr>
	<tr class="">
		<td bgcolor="#FFFFFF" width="10" height="20" class="A0010">&nbsp;</td>
		<td width="50" align="center" class="A1111">送货单号</td>
		<td width="80" align="center" class="A1101">采购日期</td>
		<td width="600" align="center" class="A1101">送货单备注</td>
		<td bgcolor="#FFFFFF" width="10" class="A0001">&nbsp;</td>
	</tr>
		<td width="10" height="20" class="A0010">&nbsp;</td>
		<td class="A0111"><?php  echo $BillNumber?></td>
		<!--<td class="A0101" align="center"><input name="cgDate" class="noLine" type="text" id="cgDate" value="<=$Date?>" size="7" onfocus="WdatePicker()" readonly></td> -->

        <td class="A0101" align="center"><input name="cgDate" class="noLine" type="text" id="cgDate" value="<?php  echo $Date?>" size="9"  readonly></td>
		<td class="A0101" >&nbsp;<input name="Remark" class="noLine" type="text" id="Remark" value="<?php  echo $Remark?>" style='width:580px'></td>
		<td width="10" class="A0001">&nbsp;</td>
	<tr>
		<td width="10" height="25" class="A0010">&nbsp;</td>
		<td colspan="3" valign="bottom">
		<span class="redB">二、送货明细</span>&nbsp;&nbsp;</td>
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
        <tr class="">
			<td bgcolor="#FFFFFF" width="10" class="A0010">&nbsp;</td>
			<td width="30" height="22" class="A1111" align="center">操作</td>
			<td width="30" height="22" class="A1101" align="center">序号</td>
			<td width="60" class="A1101" align="center">配件ID</td>
			<td width="300" class="A1101" align="center">配件名称</td>
			<td width="70" class="A1101" align="center">采购总数</td>
			<td width="70" class="A1101" align="center">本次送货</td>
			<td width="170" height="22" class="A1101" align="center">需求流水号</td>
			<td bgcolor="#FFFFFF" width="10" class="A0001">&nbsp;</td>			
		</tr>
        
		<tr>
		  <td class="A0010">&nbsp;</td>
		  <td height="150" colspan="7" class="A0111">
		  <div style="width:725;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='730' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id='StuffList'>
		<?php 
		//需求单列表
		$StockResult = mysql_query("SELECT M.BillNumber,M.Date,
		S.Id,S.Mid,S.StockId,S.StuffId,S.Qty,S.Locks,D.StuffCname,D.Picture,G.FactualQty+G.AddQty AS cgQty
		FROM $DataIn.gys_shsheet S
		LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
		LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
		WHERE  S.Mid=$Mid ORDER BY M.Date DESC,M.Id DESC,S.Id			
			 ",$link_id);
		if($StockRows = mysql_fetch_array($StockResult)){
			$i=1;
			do{
				$StuffId=$StockRows["StuffId"];		
				//if($StuffId!=""){
				//$checkidValue=$StockRows["Id"];
				$StuffCname=$StockRows["StuffCname"];
				$Qty=$StockRows["Qty"];            //本次送货 
				$cgQty=$StockRows["cgQty"];       //采购总数
				$StockId=$StockRows["StockId"];  //采购流水号
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
						$Cencel="&nbsp;";	
						//$Cencel="<a href='#' onclick='deleteRow(this.parentNode,StuffList,$Id)' title='还原此配件需求单'>×</a>";
						}
				//是否可以更新单价
				$j=$i-1;
				if($Locks==1 && $qkEstate==0){
					//$Price="<input name='newStuffPrice$j' type='text' id='newStuffPrice$j' style='width: 70px;color: #009900;' value='$Price' onChange='ChangeThis($j,$Id)' onfocus='toTempValue(this.value)'>";
					//<td width='70' class='A0101' align='center'>$Price</td>
					}
				echo"<tr><td width='30' class='A0101' align='center'>$Cencel</td>
						<td width='30' class='A0101' align='center'>$i</td>	
						<td width='60' class='A0101' align='center'>$StuffId</td>
						<td width='300' class='A0101'>$StuffCname</td>
						<td width='70' class='A0101' align='center'>$cgQty</td>
						<td width='70' class='A0101' align='center'>$Qty</td>
						<td width='170' class='A0101' align='center'>$StockId</td>
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
<!--
	<table width='<=$tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td width="10" class="A0010" height="25">&nbsp;</td>
	<td valign="bottom"><span class="redB" >三、新增需求单列表</div></td>
	<td width="10" class="A0001">&nbsp;</td>
	</tr></table>
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
		<tr>
		  <td class="A0010">&nbsp;</td>
		  <td height="75" colspan="9" class="A0111">
			<div style="width:845;height:100%;overflow-x:hidden;overflow-y:scroll">
				<table width='850' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTable">
				</table>
			</div>	
			</td>
		  <td class="A0001">&nbsp;</td>
	  </tr>
		
	</table>
 --> 
<div style="width:845;height:100%;overflow-x:hidden;overflow-y:scroll">
				<table width='850' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTable">
				</table>
			</div>	   
<?php 
include "../Admin/subprogram/add_model_b.php";
?>
<script language = "JavaScript">
function ViewStockId(Action){
	var BuyerId=document.form1.BuyerId.value;
	var CompanyId=document.form1.CompanyId.value;
	var Message="";
	var num=Math.random();  
	BackData=window.showModalDialog("cg_cgdmain_s1.php?r="+num+"&Action="+Action+"&BuyerId="+BuyerId+"&CompanyId="+CompanyId,"BackData","dialogHeight =650px;dialogWidth=980px;center=yes;scroll=yes");
	//拆分
	if(BackData){
  		var Rows=BackData.split("``");//分拆记录:	
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
				oTD=oTR.insertCell(0);
				oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode,ListTable)' title='取消此配件需求单'>×</a>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width=30;
				
				//2.序号
				oTD=oTR.insertCell(1);
				oTD.innerHTML=""+tmpNum+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width=30;
				
				//3.需求流水号
				oTD=oTR.insertCell(2);
				oTD.innerHTML=""+FieldArray[0]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width=100;
				
				//4.配件名称
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[1]+"";
				oTD.className ="A0101";
				oTD.width=360;
					
				//5.配件价格
				oTD=oTR.insertCell(4);
				oTD.innerHTML=""+FieldArray[2]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width=70;
				
				//6.订单数量
				oTD=oTR.insertCell(5);
				oTD.innerHTML=""+FieldArray[3]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width=60;
				
				//使用库存
				oTD=oTR.insertCell(6); 
				oTD.innerHTML=""+FieldArray[4]+"";
				oTD.className ="A0101";
				oTD.align="center";	
				oTD.width=60;
				
				//采购数量
				oTD=oTR.insertCell(7);
				oTD.innerHTML=""+FieldArray[5]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width=60;
				
				//增购数量
				oTD=oTR.insertCell(8);
				oTD.innerHTML=""+FieldArray[6]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width=70;
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
		TableTemp.rows[i].cells[1].innerText=j; 
		}
	}   

function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}

//2.还原需求单
function deleteRow (RowTemp,TableTemp,IdTemp){
	var rowIndex=RowTemp.parentElement.rowIndex;
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
			retCode=openUrl(myurl);
			if (retCode!=-2){
				alert("已还原需求单！");
				TableTemp.deleteRow(rowIndex);
				ShowSequence(TableTemp);
				}
			else{
				alert("还原需求单失败！");return false;
				}
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
		if (AddRemark){
			myurl="cg_cgdmain_updated.php?Id="+IdTemp+"&ActionId=923&Price="+Pricetemp+"&AddRemark="+AddRemark;
			retCode=openUrl(myurl);
			if(retCode!=-2){
				alert("单价更新成功，需重新审核才能请款!");
				StuffList.rows[Row].cells[0].innerHTML="&nbsp;";
				StuffList.rows[Row].cells[4].innerText=Pricetemp;
				}
			else{
				alert("单价更新失败");
				}
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
		StockTemp=ListTable.rows[i].cells[2].innerText;
		if(StockIds==""){
			StockIds=StockTemp;
			}
		else{
			StockIds=StockIds+","+StockTemp;
			}
		}
	document.form1.action="Gys_sh_updated.php?StockIds="+StockIds+"&ActionId=933";//主单更新
	document.form1.submit();
	}
</script>

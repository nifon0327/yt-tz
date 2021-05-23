<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增采购单扣款记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=980;$tableMenuS=500;
$CustomFun="<span onclick='SearchRecord()' $onClickCSS>加入需求单</span>&nbsp;";
$CheckFormURL="thisPage";
$ValidatorUd = true;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
	<tr valign="bottom">
	  <td height="25" colspan="7" align="center" class="A0011">扣款单信息</td>
	</tr>
	<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="10" height="25" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>		
		<td align="center" class="A1111">供 应 商</td>
	  	<td align="center" class="A1101">采&nbsp;&nbsp;&nbsp;&nbsp;购</td>
	  	<td align="center" class="A1101">扣款单号</td>
		<td align="center" class="A1101">扣款日期</td>
		<td width="240" align="center" class="A1101">扣款备注</td>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" class="A0010" height="25">&nbsp;</td>
		<td align="center" valign="middle" class="A0111"><select name="CompanyId" id="CompanyId" onChange="javascript:document.form1.submit();" style="width: 125px;">
          <?php 
			//供应商:有采购且收完货
			/*
			$GYS_Sql = "SELECT S.CompanyId,P.Forshort,P.Letter 
			FROM $DataIn.cg1_stocksheet S 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 			
			WHERE S.rkSign=0 AND S.Mid>0 GROUP BY S.CompanyId ORDER BY P.Letter";
			*/
			$GYS_Sql = "SELECT P.CompanyId,P.Forshort,P.Letter 
			FROM $DataIn.trade_object P 			
			WHERE 1 AND P.ObjectSign IN (1,3)  ORDER BY P.Letter";
			
			$GYS_Result = mysql_query($GYS_Sql); 
			while ( $GYS_Myrow = mysql_fetch_array($GYS_Result)){
				$ProviderTemp=$GYS_Myrow["CompanyId"];
				$CompanyId=$CompanyId==""?$ProviderTemp:$CompanyId;
				$Forshort=$GYS_Myrow["Forshort"];
				$Letter=$GYS_Myrow["Letter"];
				$Forshort=$Letter.'-'.$Forshort;		
				if ($ProviderTemp==$CompanyId){
					echo "<option value='$ProviderTemp' selected>$Forshort</option>";
					}
				else{
					echo "<option value='$ProviderTemp'>$Forshort</option>";
					}
				} 
			?>
        </select></td>
       
		<td class="A0101" align="center"><select name="BuyerId" id="BuyerId" style="width: 80px;">
          <?php 
			$buyerSql = mysql_query("SELECT M.BuyerId,S.Name 
			FROM $DataIn.cg1_stockmain M 
			LEFT JOIN $DataPublic.staffmain S ON M.BuyerId=S.Number 
			WHERE M.CompanyId='$CompanyId'
			GROUP BY M.BuyerId ",$link_id);
			if($buyerRow = mysql_fetch_array($buyerSql)){			
				do{
					$BuyerId=$buyerRow["BuyerId"];
					$Name=$buyerRow["Name"];					
					echo "<option value='$BuyerId'>$Name</option>";
					}while($buyerRow = mysql_fetch_array($buyerSql));
				} 
			?>
        </select></td>
		 <?php 
               $MaxValues=1;
               $CheckMaxBillNumber=mysql_query("SELECT  BillNumber  FROM $DataIn.cw15_gyskkmain WHERE 1  ORDER BY Date DESC LIMIT 0,100",$link_id);
               while($CheckMaxRow=mysql_fetch_array($CheckMaxBillNumber)){
                       $nowBillNumber=$CheckMaxRow["BillNumber"];
                        $nowBillTemp=substr($nowBillNumber,11);
                       if($nowBillTemp>$MaxValues)$MaxValues=$nowBillTemp;
               }
            $MaxValues=$MaxValues+1;
            $MaxBillNumber="Debit note ".$MaxValues;
	  ?>
		<td class="A0101" align="center"><input name="BillNumber" type="text" id="BillNumber" class="INPUT0100" size="15" value="<?php  echo $MaxBillNumber?>"></td>
		<td align="center" class="A0101"><input name="KKDate" type="text" id="KKDate" value="<?php  echo date("Y-m-d")?>" size="10" maxlength="10"></td>
		<td align="center" class="A0101"><input name="Remark" type="text" id="Remark" size="70" class="INPUT0100"></td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" height="30" class="A0010">&nbsp;</td>
		<td align="center" class="A0111">凭&nbsp;&nbsp;&nbsp;&nbsp;证</td>
	   <td colspan="4"  class="A0101"> <input name="Attached" type="file" id="Attached"  DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="3" Cel="2"></td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" height="30" class="A0010">&nbsp;</td>
		<td colspan="5" align="center" valign="bottom">扣款单明细资料
	    <input name="TempValue" type="hidden" id="TempValue"><input name='AddIds' type='hidden' id="AddIds"></td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
</table>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="10" class="A0010" bgcolor="#FFFFFF" height="25">&nbsp;</td>
		<td class="A1111" width="40" align="center">操作</td>
		<td class="A1101" width="40" align="center">序号</td>
		<td class="A1101" width="80" align="center">采购单号</td>
		<td class="A1101" width="95" align="center">需求流水号</td>
		<td class="A1101" width="55" align="center">配件ID</td>
		<td class="A1101" width="300" align="center">配件名称</td>
		<td class="A1101" width="60" align="center">购买数量</td>
		<td class="A1101" width="60" align="center">单价</td>
		<td class="A1101" width="80" align="center">扣款数量</td>
		<td class="A1101" width="150" align="center">扣款原因</td>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" class="A0010" height="216">&nbsp;</td>
		<td colspan="10" align="center" class="A0111" height="216">
		<div style="width:960;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='960' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTable">
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
		Message="没有设置采购扣款单数据!";
		}
	var BillNumber=document.form1.BillNumber.value;
	if(BillNumber==""){
		Message="没有输入扣款单号.";
		}
	if(Message!=""){
		alert(Message);return false;
		}
	else{
		var StockValues="";
		//读取加入的数据
        if(!jQuery(ListTable).find('tr:last').is(':empty')){
		    oTR=ListTable.insertRow(ListTable.rows.length);
        }
		tmpNum=ListTable.rows.length-1;
		
		for(i=1;i <=tmpNum;i++){ 
		   var IndepotQTY="IndepotQTY"+i;
		   var SheetRemark="SheetRemark"+i;
		   var PurchaseID="PurchaseID"+i;
		   var StockId="StockId"+i;
		   var StuffId="StuffId"+i;
		   var Price="Price"+i;
		   var IndepotQTYobj=document.getElementById(IndepotQTY);
		   var Remarkobj =document.getElementById(SheetRemark);
		   var PurchaseIDobj=document.getElementById(PurchaseID);
		   var StockIdobj=document.getElementById(StockId);
		   var StuffIdobj=document.getElementById(StuffId);
		   var Priceobj=document.getElementById(Price);
		   
		  if(IndepotQTYobj.value==""){alert("请输入扣款数量!");return false;}
		  else{
			if(StockValues==""){
				StockValues=PurchaseIDobj.value+"!"
				            +StockIdobj.value+"!"
							+StuffIdobj.value+"!"
							+Priceobj.value+"!"
							+IndepotQTYobj.value+"!"
							+Remarkobj.value;
				}
			else{
				StockValues=StockValues+"|"+PurchaseIDobj.value+"!"
				            +StockIdobj.value+"!"
							+StuffIdobj.value+"!"
							+Priceobj.value+"!"
							+IndepotQTYobj.value+"!"
							+Remarkobj.value;
				}
			  } 
			}
	    //alert(StockValues);
		document.form1.AddIds.value=StockValues;
		document.form1.action="cw_cgkk_save.php";
		document.form1.submit();
		}
	}

function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}
function Indepot(thisE,SumQty){
	var oldValue=document.form1.TempValue.value;
	var thisValue=thisE.value;
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
//窗口打开方式修改为兼容性的模态框 by ckt 2018-01-22
function SearchRecord(){
    var SafariReturnValue = document.getElementById('SafariReturnValue');
    if (!arguments[0]) {
        var Jid = document.getElementById('CompanyId').value;
        var Bid = document.getElementById('BuyerId').value;
        var num = Math.random();
        SafariReturnValue.value = "";
        SafariReturnValue.callback = 'SearchRecord(true)';
        var url = "/public/cw_cgkk_s1.php?r=" + num + "&Jid=" + Jid + "&Bid=" + Bid + "&tSearchPage=cw_cgkk&fSearchPage=cw_cgkk&SearchNum=2&Action=2";
        openFrame(url, 930, 500);//url需为绝对路径
        return false;
            // BackStockId = window.showModalDialog(, "BackStockId", "dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
    }
    if (SafariReturnValue.value){
  		var Rowstemp=SafariReturnValue.value.split("``");
		var Rowslength=Rowstemp.length;
		for(var i=0;i<Rowslength;i++){
			var Message="";			
			var FieldArray=Rowstemp[i].split("^^");
			//过滤相同的产品订单ID号
			for(var j=0;j<ListTable.rows.length;j++){
				var StockIdtemp=ListTable.rows[j].cells[2].innerText;//隐藏ID号存于操作列	
				if(FieldArray[0]==StockIdtemp){//如果流水号存在
					Message="需求流水号: "+FieldArray[0]+"的资料已在列表!跳过继续！";
					break;
					}
				}
			if(Message==""){
				oTR=ListTable.insertRow(ListTable.rows.length);
				tmpNum=oTR.rowIndex+1;
				//第一列:操作
				oTD=oTR.insertCell(0);
				oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>";
				oTD.data=""+FieldArray[0]+"";
				oTD.onmousedown=function(){
					window.event.cancelBubble=true;
					};
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="40";
				oTD.height="20";
				
				//第二列:序号
				oTD=oTR.insertCell(1);
				oTD.innerHTML=""+tmpNum+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="40";
				
				//三、采购单号
				oTD=oTR.insertCell(2);
				oTD.innerHTML=""+FieldArray[7]+"<input name='PurchaseID"+tmpNum+"' type='hidden' id='PurchaseID"+tmpNum+"' value='"+FieldArray[7]+"'>"
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="79";
				
				//四、需求流水号
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[0]+"<input name='StockId"+tmpNum+"' type='hidden' id='StockId"+tmpNum+"' value='"+FieldArray[0]+"'>"
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="94";
				
				//五：配件ID
				oTD=oTR.insertCell(4);
				oTD.innerHTML=""+FieldArray[1]+"<input name='StuffId"+tmpNum+"' type='hidden' id='StuffId"+tmpNum+"' value='"+FieldArray[1]+"'>"
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="54";
				
				//六:配件名称
				oTD=oTR.insertCell(5); 
				oTD.innerHTML=""+FieldArray[2]+"";
				oTD.className ="A0101";
				oTD.width="299";
				
                //七：实购数量
				var SumQty=FieldArray[3]*1+FieldArray[4]*1;
				oTD=oTR.insertCell(6);
				oTD.innerHTML=""+SumQty+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="59";
				
				//第八列:单价
				oTD=oTR.insertCell(7);
				oTD.innerHTML=""+FieldArray[6]+"<input name='Price"+tmpNum+"' type='hidden' id='Price"+tmpNum+"' value='"+FieldArray[6]+"'>"
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="59";
				
				//第九列:扣款数量
				oTD=oTR.insertCell(8);
				oTD.innerHTML="<input type='text' name='IndepotQTY"+tmpNum+"' id='IndepotQTY"+tmpNum+"' size='4' class='I0000L'  onblur='Indepot(this,"+FieldArray[5]+")' onfocus='toTempValue(this.value)'>";
				oTD.className ="A0101";
				oTD.width="79";	
				
				oTD=oTR.insertCell(9);
				oTD.innerHTML="<input type='text' name='IndepotQTY"+tmpNum+"' id='SheetRemark"+tmpNum+"' class='I0000L' size='22'>";
				oTD.className ="A0101";
				oTD.width="147";				
				}
			else{
				alert(Message);
				}//if(Message=="")
			}//for(var i=0;i<Rowslength;i++)
		}//if (BackStockId)
	else{
		alert("没有选取数据!");return true;
		}
	}
</script>

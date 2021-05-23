<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新提货单记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT M.DeliveryNumber,M.Remark,M.DeliveryDate,C.Forshort,M.CompanyId,M.ModelId,M.ForwaderId
FROM $DataIn.ch1_deliverymain M
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
WHERE M.Id=$Id",$link_id));
$DeliveryNumber=$upData["DeliveryNumber"];
$Remark=$upData["Remark"];
$DeliveryDate=$upData["DeliveryDate"];
$Forshort=$upData["Forshort"];
$CompanyId=$upData["CompanyId"];
$thisModelId=$upData["ModelId"];
$thisForwaderId=$upData["ForwaderId"];
//步骤4：
$spaceSide=30;
$tableWidth=890;$tableMenuS=500;
$CheckFormURL="thisPage";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,TempValue,,OrderIds,,ShipSign,$ShipSign";
$CustomFun="<span onClick='AddOrderId()' $onClickCSS>追加提货内容</span>&nbsp;";//自定义功能
include "../model/subprogram/add_model_t.php";
echo "<input type='hidden' id='CompanyId' name='CompanyId' value='$CompanyId'>";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td width="30" class="A0010" height="20">&nbsp;</td>
		<td colspan="8" valign="bottom">&nbsp;◆ <?php    echo $Forshort?> 提货单(<?php    echo $DeliveryNumber?>)资料</td>
		<td width="30" class="A0001">&nbsp;</td>
	</tr>
    <tr>
	  <td width='30' class='A0010' height='25'>&nbsp;</td>
      <td class='A1111' width='120' align='right'>提货单号:</td>
      <td class='A1101' width="120">&nbsp;
	   <input name="DeliveryNumber" type="text" id="DeliveryNumber" value="<?php    echo $DeliveryNumber?>"  size="14" dataType="LimitB" msg="必须在2~50个字符之间" min="3" max="50"></td>
	  <td class='A1101' align='right' width="70">提货日期:</td>
      <td class='A1101' width="110"> &nbsp;<input name="DeliveryDate" type="text" id="DeliveryDate"  size="12" onfocus="WdatePicker()" value="<?php    echo $DeliveryDate?>"></td>
	  <td class='A1101' align='right' width="70">出货模板:</td>
	  <td class='A1101' width="140">
	  <?php   
	   $checkBank=mysql_query("SELECT Id,Title FROM $DataIn.ch8_shipmodel 
	              WHERE 1 AND CompanyId='$CompanyId' ORDER BY Id",$link_id);
		  if($BankRow=mysql_fetch_array($checkBank)){
		  	echo"&nbsp;<select name='ModelId' id='ModelId' style='width:130px' dataType='Require' msg='未选'>";
			echo"<option value='' selected>请选择</option>";
			do{
				$moId=$BankRow["Id"];
				$Title=$BankRow["Title"];
				if($thisModelId==$moId){
				    echo"<option value='$moId' selected>$Title</option>";
				    }
				else{
				    echo"<option value='$moId'>$Title</option>";
				    }
				}while($BankRow=mysql_fetch_array($checkBank));
			echo"</select>";
			}
	  ?>
	  </td>
	  <td class='A1101' align='right' width="70">forward:</td>
	  <td class='A1101' width="140"> 
	  <?php   
	  $checkForwader=mysql_query("SELECT F.CompanyId,F.Forshort FROM $DataPublic.freightdata F
WHERE F.Estate=1",$link_id);
		  if($ForwaderRow=mysql_fetch_array($checkForwader)){
		  	echo"&nbsp;<select name='ForwaderId' id='ForwaderId' style='width:130px' dataType='Require' msg='未选'>";
			echo"<option value='' selected>请选择</option>";
			do{
				$thisCompanyId=$ForwaderRow["CompanyId"];
				$Forshort=$ForwaderRow["Forshort"];
				if($thisForwaderId==$thisCompanyId){
				    echo "<option value='$thisCompanyId' selected>$Forshort</option>";
				      }
				else{
				    echo"<option value='$thisCompanyId'>$Forshort</option>";
					}
				}while($ForwaderRow=mysql_fetch_array($checkForwader));
			echo"</select>";
			}
	  ?>
	  </td>
	  <td width='30' class='A0001'>&nbsp;</td>
    </tr>
    <tr>
	    <td width='30' class='A0010'>&nbsp;</td>
    	<td align="right" class='A0111'>备注:</td>
	    <td class='A0101' colspan="7">&nbsp;
		<textarea name="Remark" cols="54" id="Remark" rows="3"><?php    echo $Remark?></textarea>
		<input type="hidden" id="OrderIds" name="OrderIds" value="" /></td>
		<td width='30' class='A0001'>&nbsp;</td>
    </tr>
    
	
<?php   
//明细信息
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td colspan="8" valign="bottom">&nbsp;◆ 原提货单明细(即时更新)</td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td width="40" class='A1111' align="center">操作</td>
		<td width="40" class='A1101' align="center">序号</td>
		<td width="80" class='A1101' align="center">PO</td>
		<td width="220" class='A1101' align="center">产品名称</td>
		<td width="220" class='A1101' align="center">Product Code</td>
		<td width="60" class='A1101' align="center">售价</td>
		<td width="70" class='A1101' align="center">订单数量</td>
		<td width="100" class='A1101' align="center">提货数量</td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' colspan="8">
		<div style="width:836px;height:179px;overflow-x:hidden;overflow-y:scroll"> 
		<table border="0" width="820" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" align="left" id="OrderList">
		<?php   
		//明细信息
	 	//产品订单列表
		$sheetResultP = mysql_query("SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,S.DeliveryQty ,S.Price,S.Type,O.Qty AS OrderQty
				FROM $DataIn.ch1_deliverysheet S 
				LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
				LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId WHERE S.Mid='$Id' AND S.Type='1'
			",$link_id);
		/*
		$sheetResultP = mysql_query("SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,S.DeliveryQty ,S.Price,S.Type,O.Qty AS OrderQty
				FROM $DataIn.ch1_deliverysheet S 
				LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
				LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId WHERE S.Mid='$Id' AND S.Type='1'
			",$link_id);	
		*/
			/*echo 	"SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,S.DeliveryQty ,S.Price,S.Type,O.Qty AS OrderQty
				FROM $DataIn.ch1_deliverysheet S 
				LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
				LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId WHERE S.Mid='$Id' AND S.Type='1'";*/
		$k=1;
		if($sheetRowP = mysql_fetch_array($sheetResultP)){			
			do{
				$sId=$sheetRowP["Id"];
				$POrderId=$sheetRowP["POrderId"];
				$OrderPO=$sheetRowP["OrderPO"]==""?"&nbsp;":$sheetRowP["OrderPO"];
				$Price=$sheetRowP["Price"];
				$DeliveryQty =$sheetRowP["DeliveryQty"];
				$Remark=$sheetRowP["Remark"];
				$cName=$sheetRowP["cName"];
				$eCode=$sheetRowP["eCode"];
				$OrderQty=$sheetRowP["OrderQty"];				
				echo"<tr>
				<td width='40' class='A0101' align='center' height='20'><a href='#' onclick='deleteRow(this.parentNode,OrderList,$POrderId,$sId)' title='取消此出货项目'>×</a></td>
				<td width='40' class='A0101' align='center'>$k</td>
				<td width='80' class='A0101' align='center'>$OrderPO</td>
				<td width='220' class='A0101'>$cName</td>
				<td width='220' class='A0101'>$eCode</td>
				<td width='60' class='A0101' align='right'>$Price</td>
				<td width='70' class='A0101' align='right'>$OrderQty</td>
				<td width='83' class='A0101' align='center'>$DeliveryQty</td>
				</tr>";
				$k++;
				}while($sheetRowP = mysql_fetch_array($sheetResultP));
			}		
		?>
		</table>
		</div>
		</td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
</table>

<?php   
//新加出货订单
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td colspan="7" valign="bottom">&nbsp;◆ 新加出货单明细</td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td width="40" class='A1111' align="center" >操作</td>
		<td width="40" class='A1101' align="center" >序号</td>
		<td width="80" class='A1101' align="center" >PO</td>
		<td width="220" class='A1101' align="center" >产品名称</td>
		<td width="220" class='A1101' align="center" >Product Code</td>
		<td width="70" class='A1101' align="center" >订单数量</td>
		<td width="70" class='A1101' align="center" >已提数量</td>
		<td width="90" class='A1101' align="center" >现提数量</td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010">&nbsp;</td>
		<td class='A0111' colspan="8" >
		<div style="width:836;height:150;overflow-x:hidden;overflow-y:scroll"> 
		<table border="0" width="820" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" align="left" id="ListTable">
		</table>
		</div>
		</td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
</table>
    
</table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script language="javascript">
function toTempValue(textValue,index){
    var weiQty="weiQty"+index;
	document.getElementById(weiQty).value=textValue;
	}
function checkNum(obj,index,OrderQty,yQty){
	var TempScore=obj.value;
	var reBackSign=0;
	var TempScore=funallTrim(TempScore);
	var firstChar=TempScore.substring(0,1); 
	if(firstChar==0){
		reBackSign=0;
		}
	else{
	   var weiQty="weiQty"+index;
	   weiQty=document.getElementById(weiQty).value;
	   
	   if(weiQty<=OrderQty-yQty){
		var ScoreArray=TempScore.split("/");
		var LengthScore=ScoreArray.length;
		if(LengthScore>2){
			reBackSign=0;
			}
		else{
			if(LengthScore==1){
				//检查数字格式
				var NumTemp=ScoreArray[0];
				var reBackSign=fucCheckNUM(NumTemp,"Price");//1是数字，0不是数字
				}
			else{
				var NumTemp0=ScoreArray[0];
				var reBackSign=fucCheckNUM(NumTemp0,"Price");//1是数字，0不是数字
				if(reBackSign==1){
					var NumTemp1=ScoreArray[1];
					reBackSign=fucCheckNUM(NumTemp1,"Price");//1是数字，0不是数字
					}
				}		
			}
		  }
		 else{
		   alert("提货数量超过范围!");obj.value="";return false;
		    }
		}
	if(reBackSign==0){
		alert("对应数量不正确！");
		obj.value="";
		return false;
		}
	}
function CheckForm(){
    var OrderIdsTemp="";
	for(var j=0;j<ListTable.rows.length;j++){
	    var k=j+1;
	    var weiQty="weiQty"+k;
	    weiQty=document.getElementById(weiQty).value;
		if(weiQty==""){alert("请填写提货数量!");return false;}	
		if(OrderIdsTemp==""){
			OrderIdsTemp=ListTable.rows[j].cells[0].data+"^^"+ListTable.rows[j].cells[2].data+"^^"+weiQty;
			}
		else{
			OrderIdsTemp=OrderIdsTemp+"|"+ListTable.rows[j].cells[0].data+"^^"+ListTable.rows[j].cells[2].data+"^^"+weiQty;
			}
		}
    //alert(OrderIdsTemp);
	document.form1.OrderIds.value=OrderIdsTemp;
	document.form1.action="ch_shipout_updated.php?";
	document.form1.submit();
	}

function AddOrderId(Action){
	var Message="";
	var num=Math.random();  
	var ClientTemp=document.getElementById("CompanyId").value;
	//alert(ClientTemp);
	BackData=window.showModalDialog("ch_shipout_s1.php?num="+num+"&Action="+Action+"&CompanyId="+ClientTemp,"BackData","dialogHeight =650px;dialogWidth=1080px;center=yes;scroll=yes");
	//拆分
	if(BackData){
  		//alert(BackData);
		var Rows=BackData.split("``");//分拆记录:
		var Rowslength=Rows.length;//数组长度即订单数
		for(var i=0;i<Rowslength;i++){
			var Message="";
			var FieldTemp=Rows[i];		//拆分后的记录
			var FieldArray=FieldTemp.split("^^");
			//过滤相同的产品订单ID号
			for(var j=0;j<ListTable.rows.length;j++){
				var OrderIdtemp=ListTable.rows[j].cells[1].data;//隐藏ID号存于操作列
				if(FieldArray[1]==OrderIdtemp){//如果流水号存在
					Message="待出项目: "+FieldArray[1]+FieldArray[3]+" 已存在!跳过继续！";
					break;
					}
				}
	
			if(Message==""){
				oTR=ListTable.insertRow(ListTable.rows.length);
				//表格行数
				tmpNum=oTR.rowIndex+1;
				//第一列:操作
				oTD=oTR.insertCell(0);
				oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode,ListTable)' title='删除当前行'>×</a>";
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
				oTD.data=""+FieldArray[1]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="40";
				//三、PO
				oTD=oTR.insertCell(2);
				oTD.innerHTML=""+FieldArray[2]+"";
				oTD.data=""+FieldArray[7]+"";
				oTD.align="center";
				oTD.className ="A0101";
				oTD.width="80";
				
				//四：中文名
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[3]+"";
				oTD.className ="A0101";
				oTD.width="220";
				
				//五:Product Code
				oTD=oTR.insertCell(4); 
				oTD.innerHTML=""+FieldArray[4]+"";
				oTD.className ="A0101";
				oTD.width="220";

				//六：订单数量
				oTD=oTR.insertCell(5);
				oTD.innerHTML=""+FieldArray[5]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";
				//六：已提数
				oTD=oTR.insertCell(6);
				oTD.innerHTML=""+FieldArray[6]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";

				//七：现提数量
				oTD=oTR.insertCell(7);
				oTD.innerHTML="<input name='weiQty[]' type='text' id='weiQty"+tmpNum+"' size='6' class='noLine' value='' onchange='checkNum(this,"+tmpNum+","+FieldArray[5]+","+FieldArray[6]+")' onfocus='toTempValue(this.value,"+tmpNum+")'>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="73";
				}
			else{
				alert(Message);
				}
			}//end for
			return true;
		}
	else{
		alert("没有选取待出订单！");
		return false;
		}
	}

function ShowSequence(TableTemp){
	for(i=0;i<TableTemp.rows.length;i++){ 
  		var j=i+1
		TableTemp.rows[i].cells[1].innerText=j; 
		}
	}

function deleteRow (RowTemp,TableTemp,OrderIdTemp,Id){
	var rowIndex=RowTemp.parentElement.rowIndex;
	if(TableTemp=="ListTable"){
		TableTemp.deleteRow(rowIndex);
		ShowSequence(TableTemp);
		}
	else{
		//处理删除，删除成功后再删除行
		var LengthTemp=TableTemp.rows.length;
		if (LengthTemp==1){
			alert("本提货单最后一个订单，不能删除，请使用取消提货的功能！");return false;
			}
		else{
			var message=confirm("确定要删除此提货订单吗？如果删除，则需重新设置装箱并再次生成Bill!");
			if (message==true){
				var ReBackId=OrderIdTemp;
				myurl="ch_shipout_updated.php?POrderId="+ReBackId+"&ActionId=934&Id="+Id;
				retCode=openUrl(myurl);
				if(retCode!=-2){
					TableTemp.deleteRow(rowIndex);
					ShowSequence(TableTemp);
					//求表格长度，如果是最后一个单，则返回待出订单页面
					}
				else{
					alert("删除失败！");return false;
					}
				}
			else{
				return false;
				}
			}
		}	
	}
</script>
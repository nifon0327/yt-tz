<?php 
//EWEN 2013-02-26 OK
include "../model/modelhead.php";
include "../model/livesearch/modellivesearch.php";//用于在文本框输入文字显示与之相关的内容
echo "<link rel='stylesheet' href='../model/inputSuggest.css'>
<script type='text/javascript' src='../model/inputSuggest1.0b.js'></script>";
//步骤2：
ChangeWtitle("$SubCompany 非bom配件申领");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,OperatorSign,$OperatorSign";
//步骤3：
$tableWidth=854;$tableMenuS=500;
$SaveSTR="NO";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="850" border="0" align="center" cellspacing="0" id="NoteTable" >
        <tr>
            <td colspan="2" align="right">客户项目</td>
            <td colspan="7">
                <select name='Forshort' id='Forshort' style='width:380px' dataType='Require' msg='未选择'><option value="" selected>--请选择--</option>
                    <?php
                    $comResult = mysql_query("SELECT IFNULL( T.Forshort, '未设置项目' ) AS Forshort,IFNULL(N.TradeId,'empty') AS Id FROM nonbom4_goodsdata N LEFT JOIN trade_object T ON T.Id = N.TradeId GROUP BY 	N.TradeId",$link_id);
                    while($comRow = mysql_fetch_array($comResult)){
                        $TempCompanyId = $comRow["Id"];
                        $TempForshort = $comRow["Forshort"];
                        echo"<option value='$TempCompanyId'>$TempForshort</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
		<tr>
			<td height="22" colspan="2" align="right" valign="middle" scope="col">非BOM配件名称</td>
			<td colspan="5" valign="middle" scope="col"><input name="GoodsName" type="text" id="GoodsName" title="必填项,2-30个字节的范围"  style="width: 340px;" maxlength="100" datatype="LimitB"  min="2" msg="没有填写或超出字节的范围" onkeyup="showResult(this.value,'GoodsName','nonbom4_goodsdata','1','Estate=1')" onblur="LoseFocus()" onchange="clearData()" autocomplete="off">
				<input type="button" name="Search" id="Search" value="查询" onclick="checkGoods()"/></td>
			</tr>
			<tr>
				<td colspan="2" align="right">编号</td>
				<td colspan="5"><input name="checkBack[]" type="text" id="GoodsId" style="width: 380px;"  title="必填项,输入正整数" datatype='Number' value="" msg="没有填写或格式错误" readonly="readonly"/></td>
			</tr>
	<!--	<tr>
		  <td colspan="2" align="right">条码</td>
		  <td colspan="5"><input name="checkBack[]" type="text" id="BarCode" style="width: 380px;"  value="" readonly="readonly"/></td>
		</tr>-->
		<tr>
			<td colspan="2" align="right">单位</td>
			<td colspan="5"><input name="checkBack[]" type="text" id="Unit" style="width: 380px;"  value="" readonly="readonly" /></td>
		</tr>
		<tr>
			<td height="22" colspan="2" align="right" valign="middle" scope="col">在库</td>
			<td colspan="5" valign="middle" scope="col"><input name="checkBack[]" type="text" id="wQty" style="width: 380px;"  title="必填项,输入正整数" datatype='Currency'  msg="没有填写或格式错误" value="" readonly="readonly"/></td>
		</tr>
		<tr>
			<td height="22" colspan="2" align="right" valign="middle" scope="col">采购库存</td>
			<td colspan="5" valign="middle" scope="col"><input name="checkBack[]" type="text" id="oQty" style="width: 380px;"  title="必填项,输入正整数" datatype='Currency'  msg="没有填写或格式错误" value="" readonly="readonly"/></td>
		</tr>
		<tr valign="top">
			<td colspan="2" align="right">最低库存</td>
			<td colspan="5"><input name="checkBack[]" type="text" id="Qty3" style="width: 380px;"  title="必填项,输入正整数" datatype='Number'  msg="没有填写或格式错误" value="" readonly="readonly"/></td>
		</tr>
		<tr>
			<td colspan="2" align="right">使用地点</td>
			<td colspan="7">
                <select name='WorkAdd' id='WorkAdd' style='width:380px' dataType='Require' msg='未选择'><option value="" selected>--请选择--</option>
				<?php 
				$checkResult = mysql_query("SELECT A.Id,A.Name FROM $DataPublic.nonbom0_ck A WHERE  Estate=1 AND  A.TypeId IN (0,2)  ORDER BY A.Name",$link_id);
				while($checkRow = mysql_fetch_array($checkResult)){
					$TempId=$checkRow["Id"];
					$TempName=$checkRow["Name"];
					echo"<option value='$TempId'>$TempName</option>";
				}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td   height="22" colspan="2" align="right" valign="middle" scope="col">申领日期</td>
		<td colspan="5" valign="middle" scope="col"><input name="slDate" type="text" id="slDate" style="width: 380px;"    onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"  datatype='Require' value="<?php echo date("Y-m-d H:m:s");?>" msg="没有填写或格式错误" readonly="readonly"/></td>
	</tr>
	<tr>
		<td height="22" colspan="2" align="right" valign="middle" scope="col">申领数量</td>
		<td colspan="5" valign="middle" scope="col"><input name="Qty" type="text" id="Qty" style="width: 380px;"  title="必填项,输入正整数" datatype='Currency' value="" msg="没有填写或格式错误" /></td>
	</tr>
	<tr >
		<td colspan="2" align="right" valign="top">申领人</td>
		<td colspan="5"> <input name="Name" type="text" id="Name" style="width:380px;"  dataType='Require' msg='未填写'>
			<input name='Number' type='hidden' id='Number' >
		</td>
	</tr>


	<tr>
		<td colspan="2" align="right" valign="top">申领备注</td>
		<td colspan="5"><textarea name="Remark" rows="3" id="Remark" style="width: 380px;" dataType='Require' msg='未填写'></textarea></td>
	</tr>
	<tr>
		<td colspan="2" align="right">&nbsp;</td>
		<td colspan="5" align="center"><label>
			<input name="Button" type="button" value="申领" onClick="Validator.Validate(document.getElementById(document.form1.id),3,'',0)"/>
		</label></td>
	</tr>
	<tr>
		<td colspan="7">申领列表如下：</td>
	</tr>
	<tr bgcolor="#CCCCCC">
		<td width="40" height="25" align="center" class="A1101">序号</td>
		<td width="70" align="center" class="A1101">编号</td>
		<td width="220" align="center" class="A1101">名称</td>
		<td width="40" align="center" class="A1101">单位</td>
		<td width="50" align="center" class="A1101">申领数量</td>
		<td width="100" align="center" class="A1101">领用人</td>
		<td width="330" align="center" class="A1100">备注</td>
	</tr>
	
	<tr>
		<td colspan="7" height="248" class="A0100"><div style="width:100%;height:100%;overflow-x:hidden;overflow-y:scroll; margin-top:-1px;">
			<table width="850" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="RecordTB">
				<tr>
					<td width="41'"></td>
					<td width="71"></td>
					<td width="218"></td>
					<td width="42"></td>
					<td width="52"></td>
					<td width="101"></td>
					<td ></td>
				</tr>
				<?php
				for($i=1;$i<21;$i++){
					echo"
					<tr>
					<td height='25' align='center' class='A0101'>&nbsp;</td>
					<td class='A0101'>&nbsp;</td>
					<td class='A0101'>&nbsp;</td>
					<td class='A0101'>&nbsp;</td>
					<td class='A0101'>&nbsp;</td>
					<td class='A0101'>&nbsp;</td>
					<td class='A0100'>&nbsp;</td>
					</tr>";
				}
				?>
			</table></div>
		</td>
	</tr>
</table>
</td></tr></table>
<?php 
//步骤5：
$mySql="SELECT S.Number,S.Name FROM $DataPublic.staffmain S WHERE 1 and Estate=1 ORDER BY Number ";
$result = mysql_query($mySql,$link_id);
if($myrow = mysql_fetch_array($result)){
	do{
		$thisNumber=$myrow["Number"];
		$thisName=$myrow["Name"];
		$subNumber[]=$thisNumber;
		$subthisName[]=$thisName;
//                   	 "<option value='$thisNumber'>$thisName</option>";
	}while ($myrow = mysql_fetch_array($result));
}
include "../model/subprogram/add_model_b.php";
?>
<script>
	window.onload = function(){
		var subNumber=<?php  echo json_encode($subNumber);?>;
		var subthisName=<?php  echo json_encode($subthisName);?>;
		var sinaSuggestByMan= new InputSuggest({
			input: document.getElementById('Name'),
			poseinput: document.getElementById('Number'),
			data: subthisName,
			id:subNumber,
			width: 290
		});        
	}

	function checkGoods(){
		var TempForshort=document.getElementById("Forshort").value;
		var TempName=document.getElementById("GoodsName").value;
		var url="nonbom5_ajax.php?checkName="+encodeURI(TempName)+"&sid="+Math.random()+"&Forshort="+TempForshort;
		var obj = document.getElementsByName("checkBack[]");
		var ajax=InitAjax(); 
		ajax.open("GET",url,true); 
		ajax.onreadystatechange =function(){
			if(ajax.readyState==4 && ajax.status == 200){
				var BackData=ajax.responseText;
				if(BackData!=""){
					var DataArray=BackData.split(",");
				//填入
				for(var i=0;i<obj.length;i++){
					obj[i].value=DataArray[i];
				}
				var  myselect=document.getElementById("Qty").value="";
				var  myselect=document.getElementById("Remark").value="";
				var  myselect=document.getElementById("WorkAdd").value="";
			}
			else{
				for(var i=0;i<obj.length;i++){
					obj[i].value="";
				}
				var  myselect=document.getElementById("Qty").value="";
				var  myselect=document.getElementById("Remark").value="";
				var  myselect=document.getElementById("WorkAdd").value="";
			}
		}
		else{//清空
			for(var i=0;i<obj.length;i++){
				obj[i].value="";
			}
			var  myselect=document.getElementById("Qty").value="";
			var  myselect=document.getElementById("Remark").value="";
			var  myselect=document.getElementById("WorkAdd").value="";
		}
	}
	//发送空 
	ajax.send(null);  
}
function clearData(){
	var obj = document.getElementsByName("checkBack[]");
	for(var i=0;i<obj.length;i++){
		obj[i].value="";
	}
	var  myselect=document.getElementById("Qty").value="";
	var  myselect=document.getElementById("Remark").value="";
	var  myselect=document.getElementById("WorkAdd").value="";
}
function checkThisPage(){
	//数量检查
	//alert();
	var TempwQty=Number(document.getElementById("wQty").value);
	var TempoQty=Number(document.getElementById("oQty").value);
	var TempQty=Number(document.getElementById("Qty").value);
	var TempNumber=document.getElementById("Number").value;
	if(TempQty<=TempwQty && TempQty<=TempoQty && TempQty>0){
		var TempGoodsId=document.getElementById("GoodsId").value;
		var slDate=document.getElementById("slDate").value;
		var TempRemark=document.getElementById("Remark").value;
		var TempWorkAdd=document.getElementById("WorkAdd").value;
		var url="nonbom8_save.php?GoodsId="+TempGoodsId+"&WorkAdd="+TempWorkAdd+"&Qty="+TempQty+"&slDate="+slDate+"&Remark="+encodeURI(TempRemark)+"&GetNumber="+TempNumber+"&sid="+Math.random(); 
		var ajax=InitAjax(); 
		ajax.open("GET",url,true); 
		ajax.onreadystatechange =function(){
			if(ajax.readyState==4 && ajax.status == 200){
				if(ajax.responseText==1){
					
					//输出至列表％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％
					//A、表格行处理
					var RowNum=(RecordTB.rows[1].cells[0].innerText)*1+1;	//新增行的序号
					var addRow=RecordTB.insertRow(1);						//首行处新增一行
					oTD=addRow.insertCell(0);
					oTD.height="25";	
					oTD.align="center";
					oTD.className ="A0101";
						oTD.innerHTML=RowNum;															//序号			
						oTD=addRow.insertCell(1);
						oTD.align="center";
						oTD.className ="A0101";
						oTD.innerHTML=TempGoodsId;				//编号			
						oTD=addRow.insertCell(2);
						oTD.className ="A0101";
						oTD.innerHTML=document.getElementById("GoodsName").value;		//名称
						
						oTD=addRow.insertCell(3);
						oTD.align="center";
						oTD.className ="A0101";
						oTD.innerHTML=document.getElementById("Unit").value;					//单位		
						oTD=addRow.insertCell(4);
						oTD.align="right";
						oTD.className ="A0101";
						oTD.innerHTML=TempQty;					//数量

						oTD=addRow.insertCell(5);
						oTD.className ="A0101";
						oTD.innerHTML=document.getElementById("Name").value;

						oTD=addRow.insertCell(6);
						oTD.align="center";
						oTD.className ="A0100";
						oTD.innerHTML=TempRemark;				//备注
					//超出100行则删除最后一行
					var LastRow=RecordTB.rows.length;
					if(RecordTB.rows[LastRow-1].cells[0].innerText*1==0){
						RecordTB.deleteRow(LastRow-1);
					}
					//清空数据，防止没有更新库存数量，连续报废
					var obj = document.getElementsByName("checkBack[]");
					for(var i=0;i<obj.length;i++){
						obj[i].value="";
					}
					var  myselect=document.getElementById("Qty").value="";
					var  myselect=document.getElementById("Remark").value="";
					var  myselect=document.getElementById("WorkAdd").value="";
					//清空完毕
				//％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％
			}
		}
	}
		//发送空 
		ajax.send(null); 
	}
	else{
		alert("数据不在允许范围或格式不对");
	}
}
</script>
<?php 
//EWEN 2013-02-26 OK
include "../model/modelhead.php";
include "../model/testsearch/modellivesearch.php";//用于在文本框输入文字显示与之相关的内容
//步骤2：
ChangeWtitle("$SubCompany 非bom配件转入");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=854;$tableMenuS=500;
$SaveSTR="NO";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="850" border="0" align="center" cellspacing="0" id="NoteTable" >
		<tr>
			<td height="22" colspan="2" align="right" valign="middle" scope="col">非BOM配件名称</td>
			<td colspan="5" valign="middle" scope="col"><input name="GoodsName" type="text" id="GoodsName" title="必填项,2-30个字节的范围"  style="width: 340px;" maxlength="30" datatype="LimitB" max="30" min="2" msg="没有填写或超出字节的范围" onkeyup="showResult(this.value,'GoodsName','nonbom4_goodsdata','1','')" onblur="LoseFocus()" onchange="clearData()" autocomplete="off">
			    <input type="button" name="Search" id="Search" value="查询" onclick="checkGoods()"/></td>
		</tr>
		<tr>
		  <td colspan="2" align="right">编号</td>
		  <td colspan="5"><input name="checkBack[]" type="text" id="GoodsId" style="width: 380px;"  title="必填项,输入正整数" datatype='Number' value="" msg="没有填写或格式错误" readonly="readonly"/></td>
	    </tr>
		<tr>
		  <td colspan="2" align="right">条码</td>
		  <td colspan="5"><input name="checkBack[]" type="text" id="BarCode" style="width: 380px;"  value="" readonly="readonly"/></td>
	    </tr>
		<tr>
		  <td colspan="2" align="right">单位</td>
		  <td colspan="5"><input name="checkBack[]" type="text" id="Unit" style="width: 380px;"  value="" readonly="readonly" /></td>
	    </tr>
        <tr>
			<td height="22" colspan="2" align="right" valign="middle" scope="col">在库</td>
			<td colspan="5" valign="middle" scope="col"><input name="checkBack[]" type="text" id="wQty" style="width: 380px;"  title="必填项,输入正整数" datatype='Number'  msg="没有填写或格式错误" value="" readonly="readonly"/></td>
		</tr>
        <tr>
			<td height="22" colspan="2" align="right" valign="middle" scope="col">采购库存</td>
			<td colspan="5" valign="middle" scope="col"><input name="checkBack[]" type="text" id="oQty" style="width: 380px;"  title="必填项,输入正整数" datatype='Number'  msg="没有填写或格式错误" value="" readonly="readonly"/></td>
		</tr>
        <tr valign="top">
          <td colspan="2" align="right">最低库存</td>
          <td colspan="5"><input name="checkBack[]" type="text" id="Qty3" style="width: 380px;"  title="必填项,输入正整数" datatype='Number'  msg="没有填写或格式错误" value="" readonly="readonly"/></td>
        </tr>
        <tr>
			<td height="22" colspan="2" align="right" valign="middle" scope="col">转入数量</td>
			<td colspan="5" valign="middle" scope="col"><input name="Qty" type="text" id="Qty" style="width: 380px;"  title="必填项,输入正整数" datatype='Number' value="" msg="没有填写或格式错误" /></td>
		</tr>
        <tr>
          <td colspan="2" align="right" valign="top">转入备注</td>
          <td colspan="5"><textarea name="Remark" rows="3" id="Remark" style="width: 380px;" dataType='Require' msg='未填写'></textarea></td>
        </tr>
        <tr>
          <td colspan="2" align="right">&nbsp;</td>
          <td colspan="5" align="center"><label>
            <input name="Button" type="button" value="转入" onClick="Validator.Validate(document.getElementById(document.form1.id),3,'',0)"/>
          </label></td>
        </tr>
        <tr>
          <td colspan="7">转入列表如下：</td>
        </tr>
        <tr bgcolor="#CCCCCC">
          <td width="40" height="25" align="center" class="A1101">序号</td>
          <td width="70" align="center" class="A1101">编号</td>
          <td width="220" align="center" class="A1101">名称</td>
          <td width="100" align="center" class="A1101">条码</td>
          <td width="40" align="center" class="A1101">单位</td>
          <td width="50" align="center" class="A1101">数量</td>
          <td width="330" align="center" class="A1100">备注</td>
        </tr>
        
        <tr>
          <td colspan="7" height="248" class="A0100"><div style="width:100%;height:100%;overflow-x:hidden;overflow-y:scroll; margin-top:-1px;">
          <table width="850" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="RecordTB">
          	<tr>
            	<td width="41'"></td>
          		<td width="71"></td>
          		<td width="218"></td>
          		<td width="101"></td>
			 	<td width="42"></td>
			  	<td width="52"></td>
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
include "../model/subprogram/add_model_b.php";
?>
<script>
function checkGoods(){
	var TempName=document.getElementById("GoodsName").value;
	var url="nonbom5_ajax.php?checkName="+TempName+"&sid="+Math.random(); 
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
				}
			else{
				for(var i=0;i<obj.length;i++){
    				obj[i].value="";
 					}
				var  myselect=document.getElementById("Qty").value="";
				var  myselect=document.getElementById("Remark").value="";
				}
			}
		else{//清空
			for(var i=0;i<obj.length;i++){
    			obj[i].value="";
 				}
			var  myselect=document.getElementById("Qty").value="";
			var  myselect=document.getElementById("Remark").value="";
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
	}
function checkThisPage(){
	var TempGoodsId=document.getElementById("GoodsId").value;
	var TempQty=document.getElementById("Qty").value;
	var TempRemark=document.getElementById("Remark").value;
	var url="nonbom9_save.php?GoodsId="+TempGoodsId+"&Qty="+TempQty+"&Remark="+TempRemark+"&sid="+Math.random(); 
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
					oTD.className ="A0101";
					oTD.innerHTML=document.getElementById("BarCode").value;					//条码			
				oTD=addRow.insertCell(4);
					oTD.align="center";
					oTD.className ="A0101";
					oTD.innerHTML=document.getElementById("Unit").value;					//单位		
				oTD=addRow.insertCell(5);
					oTD.align="right";
					oTD.className ="A0101";
					oTD.innerHTML=TempQty;					//数量
				oTD=addRow.insertCell(6);
					oTD.align="center";
					oTD.className ="A0100";
					oTD.innerHTML=TempRemark;				//备注
				//超出100行则删除最后一行
				var LastRow=RecordTB.rows.length;
				if(RecordTB.rows[LastRow-1].cells[0].innerText*1==0){
					RecordTB.deleteRow(LastRow-1);
					}	
			//％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％％
				}
			}
		}
	//发送空 
	ajax.send(null);  
	}
</script>
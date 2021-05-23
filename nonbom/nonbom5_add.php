<?php 
//EWEN 2013-02-20 OK
include "../model/modelhead.php";
include "../model/livesearch/modellivesearch.php";//用于在文本框输入文字显示与之相关的内容
//步骤2：
ChangeWtitle("$SubCompany 非bom配件申购");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,singel,$singel";
//步骤3：
$tableWidth=854;$tableMenuS=500;
$SaveSTR="";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="850" border="0" align="center" cellspacing="0" id="NoteTable" >
	    <tr>
		  <td colspan="2" align="right"  >日期</td>
		  <td colspan="6"><input name="sgDate" type="text" id="sgDate" style="width: 380px;"    onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"  datatype='Require' value="<?php echo date("Y-m-d H:m:s");?>" msg="没有填写或格式错误" readonly="readonly"/></td>
	    </tr>
        <tr>
            <td colspan="2" align="right">客户项目</td>
            <td colspan="6"><select name='Forshort' id='Forshort' style='width:380px' dataType='Require' msg='未选择'><option value="" selected>--请选择--</option>
                    <?php

                    $ProjectResult = mysql_query("SELECT O.Id,O.CompanyId,O.Forshort FROM $DataPublic.trade_info T INNER JOIN $DataPublic.trade_object O ON O.Id = T.TradeId",$link_id);
                    while($ProjectRow = mysql_fetch_array($ProjectResult)){
                        echo"<option value='$ProjectRow[Id]' $selectSTR>$ProjectRow[Forshort]</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
		<tr>
			<td height="22" colspan="2" align="right" valign="middle" scope="col">非BOM配件名称</td>
			<td colspan="6" valign="middle" scope="col"><input name="GoodsName" type="text" id="GoodsName" title="必填项,2-100个字节的范围"  style="width: 350px;" maxlength="30" datatype="LimitB" max="100" min="2" msg="没有填写或超出字节的范围" onkeyup="showResult(this.value,'GoodsName','nonbom4_goodsdata','1','Estate=1')" onblur="LoseFocus()" onchange="clearData()" autocomplete="off">
			    <input type="button" name="Search" id="Search" value="查询" onclick="checkGoods()"/></td>
		</tr>
		<tr>
		  <td colspan="2" align="right">编号</td>
		  <td colspan="6"><input name="checkBack[]" type="text" id="GoodsId" style="width: 380px;"  title="必填项,输入正整数" datatype='Number' value="" msg="没有填写或格式错误" readonly="readonly"/></td>
	    </tr>
		<!--<tr>
		  <td colspan="2" align="right">条码</td>
		  <td colspan="6"><input name="checkBack[]" type="text" id="BarCode" style="width: 380px;"  value="" readonly="readonly"/></td>
	    </tr>-->
		<tr>
		  <td colspan="2" align="right">单位</td>
		  <td colspan="6"><input name="checkBack[]" type="text" id="Unit" style="width: 380px;"  value="" readonly="readonly" /></td>
	    </tr>
        <tr>
			<td height="22" colspan="2" align="right" valign="middle" scope="col">在库</td>
			<td colspan="6" valign="middle" scope="col"><input name="checkBack[]" type="text" id="wQty" style="width: 380px;"  title="必填项,输入正整数" datatype='Currency'  msg="没有填写或格式错误" value="" readonly="readonly"/></td>
		</tr>
        <tr>
			<td height="22" colspan="2" align="right" valign="middle" scope="col">采购库存</td>
			<td colspan="6" valign="middle" scope="col"><input name="checkBack[]" type="text" id="oQty" style="width: 380px;"  title="必填项,输入正整数" datatype='Currency'  msg="没有填写或格式错误" value="" readonly="readonly"/></td>
		</tr>
        <tr valign="top">
          <td colspan="2" align="right">最低库存</td>
          <td colspan="6"><input name="checkBack[]" type="text" id="Qty3" style="width: 380px;" datatype='Currency'  msg="没有填写或格式错误" value="" readonly="readonly"/></td>
        </tr>
        <tr>
		  <td colspan="2" align="right">申购总数</td>
		  <td colspan="6"><input name="checkBack[]" type="text" id="Qty1" style="width: 380px;" datatype='Currency' msg="没有填写或格式错误" value="" readonly="readonly"/></td>
	    </tr>
        <tr>
			<td height="22" colspan="2" align="right" valign="middle" scope="col">本次申购数量</td>
			<td colspan="6" valign="middle" scope="col"><input name="sgQty" type="text" id="sgQty" style="width: 380px;"  title="必填项" datatype='Currency' value="" msg="没有填写或格式错误" /></td>
		</tr>
        <tr>
		  <td height="22" colspan="2" align="right" valign="middle" scope="col">单价</td>
		  <td colspan="6" valign="middle" scope="col"><input name="checkBack[]" type="text" id="Price" style="width: 380px;"  title="必填项" datatype='Currency' value="" msg="没有填写或格式错误" /></td>
	    </tr>
	    
    <tr>
           <td colspan="2" align="right">增值税率</td>
           <td colspan="6"><select name='AddTaxValue' id='AddTaxValue' style='width:380px' dataType='Require' msg='未选择'><option value="" selected>--请选择--</option>
				 <?php 
				 
                $checkResult = mysql_query("SELECT A.Id,A.Name FROM $DataPublic.provider_addtax A WHERE A.Estate=1 ",$link_id);
                while($checkRow = mysql_fetch_array($checkResult)){
                    //if($checkRow["Id"] == 1)$selectSTR = "selected";
                    //else $selectSTR="";
                    echo"<option value='$checkRow[Id]' $selectSTR>$checkRow[Name]</option>";
                    }
                ?>
             </select>
          </td>
         </tr>
	    
        
        <tr>
		  <td height="22" colspan="2" align="right" valign="middle" scope="col">主分类</td>
		  <td colspan="6" valign="middle" scope="col"><input name="checkBack[]" type="text" id="mainType" style="width: 380px;"  title="必填项" datatype='Currency' value="" msg="没有填写或格式错误"  readonly="readonly" /></td>
	    </tr>
        
        <tr>
		  <td height="22" colspan="2" align="right" valign="middle" scope="col">采购</td>
		  <td colspan="6" valign="middle" scope="col"><input name="checkBack[]" type="text" id="BuyerId" style="width: 380px;"  title="必填项"  value="" msg="没有填写或格式错误"  readonly="readonly" /></td>
	    </tr>        
                
        <tr>
           <td colspan="2" align="right">供应商</td>
           <td colspan="6"><select name='CompanyId' id='CompanyId' style='width:380px' dataType='Require' msg='未选择'><option value="" selected>--请选择--</option>
				 <?php 
                $checkResult = mysql_query("SELECT A.Letter,A.CompanyId,A.Forshort FROM $DataPublic.nonbom3_retailermain A WHERE A.Estate=1 ORDER BY A.Letter,A.Forshort",$link_id);
                while($checkRow = mysql_fetch_array($checkResult)){
                    echo"<option value='$checkRow[CompanyId]'>$checkRow[Letter] - $checkRow[Forshort]</option>";
                    }
                ?>
             </select>
          </td>
         </tr>
        <tr>
          <td colspan="2" align="right" valign="top">申购备注</td>
          <td colspan="6"><textarea name="Remark" rows="3" id="Remark" style="width: 380px;" dataType='Require' msg='未填写'></textarea></td>
        </tr>
 
       <!-- <tr>
          <td colspan="2" align="right" valign="top">运费关联单号</td>
          <td colspan="6">
          <input name="PurchaseID" type="text" id="PurchaseID" style="width: 300px;" onClick='ViewShipId()'   readonly="readonly" />
          <input name="Button" type="button" value="清除关联" onClick="ClearRelation()"/>
          <input name="fromMid" id="fromMid"  type="hidden" value="0" />
          </td>
        </tr>-->
        
        <tr>
          <td colspan="2" align="right">&nbsp;</td>
          <td colspan="6" align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input name="Button" type="button" value="确定申购" onClick="Validator.Validate(document.getElementById(document.form1.id),3,'',0)"/></td>
        </tr>
        <tr>
          <td colspan="8">申购列表如下：</td>
        </tr>
        <tr bgcolor="#CCCCCC">
          <td width="40" height="25" align="center" class="A1101">序号</td>
          <td width="60" align="center" class="A1101">编号</td>
          <td width="230" align="center" class="A1101">名称</td>
          <td width="40" align="center" class="A1101">单位</td>
          <td width="50" align="center" class="A1101">单价</td>
          <td width="50" align="center" class="A1101">增值税率</td>
          <td width="50" align="center" class="A1101">数量</td>
          <td width="100" align="center" class="A1101">供应商</td>
          <td width="230" align="center" class="A1100">备注</td>
        </tr>
        
        <tr>
          <td colspan="10" height="250" class="A0100"><div style="width:100%;height:100%;overflow-x:hidden;overflow-y:scroll; margin-top:-1px;">
          <table width="850" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="RecordTB">
          	<tr>
            	<td width="40'"></td>
          		<td width="61">
          		</td><td width="226">
			 	<td width="41"></td>
                <td width="51"></td>
                <td width="51"></td>
			  	<td width="52"></td>
			  	<td width="101"></td>
			  	<td ></td>
			  </tr>
          <?php
          for($i=1;$i<21;$i++){
			  echo"
			  <tr><td height='25' align='center' class='A0101'>&nbsp;</td>
			  <td class='A0101'>&nbsp;</td>
			  <td class='A0101'>&nbsp;</td>
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

	var url="nonbom5_ajax.php?checkName="+encodeURIComponent(TempName)+"&sid="+Math.random(); 
	//alert(url);
	var obj = document.getElementsByName("checkBack[]");
	var ajax=InitAjax(); 
	ajax.open("GET",url,true); 
	var k = 0 ;
	ajax.onreadystatechange =function(){
		if(ajax.readyState==4 && ajax.status == 200){
			var BackData=ajax.responseText;
			//alert(BackData);
			if(BackData!=""){
				var DataArray=BackData.split(",");
				//填入
				for(var i=0;i<obj.length;i++){
					obj[i].value=DataArray[i];
					}
				//默认供应商处理
				var  myselect=document.getElementById("CompanyId").value=DataArray[i];
				//默认增值税率
				k = i+1;
				var  myselect=document.getElementById("AddTaxValue").value=DataArray[k];
				}
			else{
				for(var i=0;i<obj.length;i++){
    				obj[i].value="";
 					}
				var  myselect=document.getElementById("CompanyId").value="";
				var  myselect=document.getElementById("AddTaxValue").value="";
				var  myselect=document.getElementById("sgQty").value="";
				var  myselect=document.getElementById("Remark").value="";
				}
			}
		else{//清空
			for(var i=0;i<obj.length;i++){
    			obj[i].value="";
 				}
			var  myselect=document.getElementById("CompanyId").value="";
			var  myselect=document.getElementById("AddTaxValue").value="";
			var  myselect=document.getElementById("sgQty").value="";
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
	var  myselect=document.getElementById("CompanyId").value="";
	var  myselect=document.getElementById("AddTaxValue").value="";
	var  myselect=document.getElementById("sgQty").value="";
	var  myselect=document.getElementById("Remark").value="";
	}
	
	
function checkThisPage(){
	var Forshort=document.getElementById("Forshort").value;
	var TempGoodsId=document.getElementById("GoodsId").value;
	var TempsgQty=document.getElementById("sgQty").value;
	var sgDate=document.getElementById("sgDate").value;
	var TempPrice=document.getElementById("Price").value;
	var TempRemark=document.getElementById("Remark").value;
	var TempCompanyId=document.getElementById("CompanyId").value;
    var TempmainType=document.getElementById("mainType").value;
	var TempBuyerId=document.getElementById("BuyerId").value;
	
	var AddTaxValue = document.getElementById("AddTaxValue");
	
	var TempAddTaxValue=AddTaxValue.options[AddTaxValue.options.selectedIndex].value;
	
	var TempAddTaxValueText = AddTaxValue.options[AddTaxValue.options.selectedIndex].text;
	
	
	var url="nonbom5_save.php?GoodsId="+TempGoodsId+"&Forshort="+Forshort+"&Qty="+TempsgQty+"&Price="+TempPrice+"&CompanyId="+TempCompanyId+"&BuyerId="+TempBuyerId+"&mainType="+TempmainType+"&sgDate="+sgDate+"&Remark="+encodeURI(TempRemark)+"&AddTaxValue="+TempAddTaxValue+"&sid="+Math.random();
	
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
	                oTD.innerHTML=RowNum;					//序号			
				oTD=addRow.insertCell(1);
					oTD.align="center";
					oTD.className ="A0101";
					oTD.innerHTML=TempGoodsId;				//编号			
				oTD=addRow.insertCell(2);
					oTD.className ="A0101";
					oTD.innerHTML=document.getElementById("GoodsName").value;	//名称
				oTD=addRow.insertCell(3);
					oTD.className ="A0101";
		
					oTD.innerHTML=document.getElementById("Unit").value;		//单位		
				oTD=addRow.insertCell(4);
					oTD.align="center";
					oTD.className ="A0101";
					oTD.innerHTML=TempPrice;					//单价
				oTD=addRow.insertCell(5);
					oTD.align="center";
					oTD.className ="A0101";
					oTD.innerHTML=TempAddTaxValueText;					//增值税率
					
				oTD=addRow.insertCell(6);
					oTD.align="center";
					oTD.className ="A0101";
					oTD.innerHTML=TempsgQty;					//数量
				oTD=addRow.insertCell(7);
					oTD.className ="A0101";
					oTD.align="center";
					oTD.innerHTML=document.getElementById("CompanyId").options[document.getElementById("CompanyId").selectedIndex].text;			//供应商
				oTD=addRow.insertCell(8);
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
<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
echo"<SCRIPT src='../model/addtblist.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 新增免抵税收益明细列表");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=900;$tableMenuS=500;
$CustomFun="<span onClick='ViewProductId(2)' $onClickCSS>加入报关费用</span>&nbsp;";//自定义功能
$OtherFun="<span onClick='ViewOtherfee(2)' $onClickCSS>加入行政费用</span>&nbsp;";
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_tt.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
	<tr>
	<td width="10" class="A0010" bgcolor="#FFFFFF" height="25">&nbsp;</td>
	<td colspan="8" class="A0100" valign="bottom">◆免抵税信息</td>
	<td width="10" class="A0001" bgcolor="#FFFFFF" height="20">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" height="20" class="A0010">&nbsp;</td>
		<td width="103" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>国税时间</td>
	  	<td width="170" align="left" class="A0100">
		&nbsp;&nbsp;
         <input name="Taxdate" type="text" class="INPUT0000" id="Taxdate" value="<?php  echo date("Y-m-d")?>" maxlength="10" onfocus="WdatePicker()" readonly>
         </td>
      	<td width="80" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>免抵税金额</td>
		<td width="162" align="left" class="A0100">
            &nbsp;&nbsp;
        	<input name="Taxamount" type="text" id="Taxamount" value="" size="20" maxlength="20" dataType="Require" Msg="未填写">
         </td>
		<td width="93" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>结付银行</td>
		<td width="124" align="left" class="A0101"><?php  include "../model/selectbank1.php";?></td>
		<td width="72" align="center" class="A0101">收款日期</td>
		<td width="103" align="left" class="A0101"><input name="Taxgetdate" type="text" class="INPUT0000" id="Taxgetdate" onfocus="WdatePicker()" value="<?php  echo date("Y-m-d")?>" size="12" maxlength="10" readonly /></td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
    
	<tr>
		<td width="10" height="25" class="A0010">&nbsp;</td>
		<td width="103" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>免抵退税发票号</td>
	  	<td width="170" align="left" class="A0100">
            &nbsp;&nbsp;
        	<input name="TaxNo" type="text" id="TaxNo" value="" size="20" maxlength="20" dataType="Require" Msg="未填写">
       </td>
      
      <!--	<td width="80" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>税款状态</td>
		<td width="162" align="left" class="A0100" colspan="1">
            &nbsp;&nbsp;
              <?php     
			    echo "<select name='Estate'  id='Estate' style='width:135px' dataType='Require' msg='未选'  > ";           
		        echo "<option value='0' selected='selected' >税款已收到</option>";
			    echo "<option value='3'  >税款未收到</option>";
				echo "</select>";
		       ?>
         </td>-->
		<td width="93" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>期末留抵税额</td>
		<td colspan="5" align="left" class="A0101">
            &nbsp;&nbsp;
			<input name="endTax" type="text" id="endTax" value="" size="20" maxlength="20" >       
			</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr> 
	<tr>
	<td width="10" height="25" class="A0010">&nbsp;</td>
	<td width="103" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>扫描附件</td>
	<td colspan="2" align="left" class="A0101"><input name="Attached" type="file" id="Attached" size="20" title="可选项,可上传rar、zip、xls、doc、pdf、eml、jpg格式" Row="2" Cel="2">  
     </td>
	 <td width="162" align="center" class="A0101">结付凭证</td>
	 <td colspan="4" align="left" class="A0101">
            &nbsp;&nbsp;
        	<input name="proof" type="file" id="proof" size="20" title="可选项,可上传rar、zip、xls、doc、pdf、eml、jpg格式" Row="2" Cel="2">  
     </td>
	 <td width="10" class="A0001">&nbsp;</td>
	</tr>   
    <tr>
		<td width="10" height="20" class="A0010">&nbsp;</td>
		<td width="103" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>备&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注</td>
	  	<td colspan="7" class="A0101" align="left">
         &nbsp;&nbsp;
            <textarea name="Remark" cols="60" rows="3" id="Remark"></textarea>

	  	<td width="10" class="A0001">&nbsp;</td>
    </tr>    
	<tr>
		<td width="10" height="25" class="A0010">&nbsp;</td>
		<td colspan="8" valign="bottom" style="color:#990033">◆报关费用明细	   </td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
	
</table>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="10" class="A0010" bgcolor="#FFFFFF" height="20">&nbsp;</td>
		<td class="A1111" width="40" align="center">操作</td>
		<td class="A1101" width="40" align="center">序号</td>
		<td class="A1101" width="100" align="center">出货流水号</td>
		<td class="A1101" width="170" align="center">客户</td>
		<td class="A1101" width="249" align="center">Invoice名称</td>
		<td class="A1101" width="100" align="center">出货金额</td>
		<td class="A1101" width="70" align="center">出货日期</td>
		<td class="A1101" width="112" align="center">报关方式</td>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
	<tr>
		<td width="10" class="A0010" height="200">&nbsp;</td>
		<td colspan="8" align="center" class="A0111">
		<div style="width:881;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='870' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTable">
			</table>
		</div>		
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
		<tr>
		<td width="10" height="25" class="A0010">&nbsp;</td>
		<td colspan="6" valign="bottom" style="color:#990033">◆行政费用明细	   </td>

	   </tr>
</table>


<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="10" class="A0010" bgcolor="#FFFFFF" height="20">&nbsp;</td>
		<td class="A1111" width="40" align="center">操作</td>
		<td class="A1101" width="40" align="center">序号</td>
		<td class="A1101" width="50" align="center">Id</td>
		<td class="A1101" width="60" align="center">请款人</td>
		<td class="A1101" width="90" align="center">请款日期</td>
		<td class="A1101" width="60" align="center">金额</td>
		<td class="A1101" width="429" align="center">说明</td>
		<td class="A1101" width="112" align="center">分类</td>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
	<tr>
		<td width="10" class="A0010" height="250">&nbsp;</td>
		<td colspan="8" align="center" class="A0111">
		<div style="width:881;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='881' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTablefee">
			</table>
		</div>		
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
	
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_bb.php";
?>
<script language = "JavaScript"> 
function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}
function deleteAllRow(){	
	for(i=ListTable.rows.length-1;i>=0;i--){ 
  		ListTable.deleteRow(i); 
		}
	}
function deleteRows (tt){
	var rowIndex=tt.parentElement.rowIndex;
	ListTable.deleteRow(rowIndex);
	ShowSequence(ListTable);
	}
function deletefeeRows(ss){
     var rowIndex=ss.parentElement.rowIndex;
	 ListTablefee.deleteRow(rowIndex);
	 ShowSequence(ListTablefee);
	}
	 
//序号重整
function ShowSequence(TableTemp){
	for(i=0;i<TableTemp.rows.length;i++){ 
  		var j=i+1
		TableTemp.rows[i].cells[1].innerText=j; 
		}
	}  
//==============================================================================================================加入报关费用 
function ViewProductId(Action){
	var r=Math.random();  

	var Bid="oK";
	if(Bid!=""){
		var BackData=window.showModalDialog("Invoice_s3.php?r="+r+"&tSearchPage=Invoice&fSearchPage=customs&SearchNum=2&Action="+Action,"BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
	if(!BackData){  //专为safari设计的 add by 2011-05-04
			if(document.getElementById('SafariReturnValue')){
				//alert("return");
				var SafariReturnValue=document.getElementById('SafariReturnValue');
				BackData=SafariReturnValue.value;
				SafariReturnValue.value="";
			 }
		   }
		if(BackData){
			//锁定客户选项
			//document.checkFrom.CompanyId.disabled=true;
			var Rows=BackData.split("``");//分拆记录
			var Rowslength=Rows.length;//数组长度
			for(var i=0;i<Rowslength;i++){
				var Message="";
				var FieldTemp=Rows[i];		//拆分后的记录
				var FieldArray=FieldTemp.split("^^");//分拆记录中的字段
							
				if(Message==""){
					oTR=ListTable.insertRow(ListTable.rows.length);
					tmpNum=oTR.rowIndex+1;
					
					oTD=oTR.insertCell(0);
					oTD.innerHTML="<a href='#' onclick='deleteRows(this.parentNode)' title='删除当前行'>×</a>";
					oTD.align="center";
					oTD.className ="A0101";
					oTD.width="40";
					oTD.height="20";
					oTD.onmousedown=function(){
						window.event.cancelBubble=true;
						};
						
					oTD=oTR.insertCell(1);
					oTD.innerHTML=""+tmpNum+"";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="40";
					
					oTD=oTR.insertCell(2);
					oTD.innerHTML="<input style='border:1px;border-bottom-style:none;border-top-style:none;border-left-style:none;border-right-style:none;' name='shipmainNumber["+tmpNum+"]' type='text' id='shipmainNumber"+tmpNum+"' size='4' value='"+FieldArray[0]+"' class='noLine' readonly>";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="100";
					
					
					oTD=oTR.insertCell(3);
					oTD.innerHTML=""+FieldArray[1];
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="170";
					
					oTD=oTR.insertCell(4);
					oTD.innerHTML="<input style='border:1px;border-bottom-style:none;border-top-style:none;border-left-style:none;border-right-style:none;' name='InvoiceNumber["+tmpNum+"]' type='text' id='InvoiceNumber"+tmpNum+"' size='15' value='"+FieldArray[2]+"' class='noLine' readonly>";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="250";
					
					oTD=oTR.insertCell(5);
					oTD.innerHTML=FieldArray[3]+"";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="100";
					
					oTD=oTR.insertCell(6); 
					oTD.innerHTML=FieldArray[4]+"";
					oTD.align="center";
					oTD.className ="A0101";
					oTD.width="70";
					 
					oTD=oTR.insertCell(7);
					oTD.innerHTML=FieldArray[5]+"";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="100";
					}
				else{
					alert(Message);
					}
				}//end for
				return true;
			}
		else{
			alert("没有选！");
			return false;
			}
		}
	else{
		alert("没有选择客户!");
		return false;
		}
	}	
	
//====================================================================================================加入行政费用
function ViewOtherfee(Action){
	var r=Math.random();  
	
	var Bid="oK";
	if(Bid!=""){
		var BackData=window.showModalDialog("cw_mdotherfee_s1.php?r="+r+"&tSearchPage=cw_mdotherfee&fSearchPage=adminicost&SearchNum=2&Action="+Action,"BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
			
			
			if(!BackData){  //专为safari设计的 add by 2011-05-04
			if(document.getElementById('SafariReturnValue')){
				//alert("return");
				var SafariReturnValue=document.getElementById('SafariReturnValue');
				BackData=SafariReturnValue.value;
				SafariReturnValue.value="";
			 }
		   }
		
		
		if(BackData){
			var Rows=BackData.split("``");//分拆记录
			//document.write(Rows);
			var Rowslength=Rows.length;//数组长度
			for(var i=0;i<Rowslength;i++){
				var Message="";
				var FieldTemp=Rows[i];		//拆分后的记录
				var FieldArray=FieldTemp.split("^^");//分拆记录中的字段
							
				if(Message==""){
					oTR=ListTablefee.insertRow(ListTablefee.rows.length);
					tmpNum=oTR.rowIndex+1;
					
					oTD=oTR.insertCell(0);
					oTD.innerHTML="<a href='#' onclick='deletefeeRows(this.parentNode)' title='删除当前行'>×</a>";
					oTD.align="center";
					oTD.className ="A0101";
					oTD.width="40";
					oTD.height="20";
					oTD.onmousedown=function(){
						window.event.cancelBubble=true;
						};
						
					oTD=oTR.insertCell(1);
					oTD.innerHTML=""+tmpNum+"";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="40";
					
					oTD=oTR.insertCell(2);
					oTD.innerHTML="<input style='border:1px;border-bottom-style:none;border-top-style:none;border-left-style:none;border-right-style:none;' name='otherfeeNumber["+tmpNum+"]' type='text' id='otherfeeNumber"+tmpNum+"' size='4' value='"+FieldArray[0]+"' class='noLine' readonly>";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="50";
					
					
					oTD=oTR.insertCell(3);
					oTD.innerHTML=""+FieldArray[1]+"";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="60";
					
					oTD=oTR.insertCell(4);
					oTD.innerHTML=""+FieldArray[2]+"";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="90";
					
					oTD=oTR.insertCell(5);
					oTD.innerHTML=FieldArray[3]+"";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="60";
					
					oTD=oTR.insertCell(6);
					oTD.innerHTML=FieldArray[4]+"";
					oTD.className ="A0101";
					oTD.width="431";
					
					
					oTD=oTR.insertCell(7);
					oTD.innerHTML=FieldArray[5]+"";
					oTD.className ="A0101";
					oTD.width="110";
					
						
					}
				else{
					alert(Message);
					}
				}//end for
				return true;
			}
		else{
			alert("没有选！");
			return false;
			}
		}
	else{
		alert("没有选择客户!");
		return false;
		}
	}	

//====================================================================================================================================
function CheckForm(){
	var Message="";
	var DeclarationNoTmp=document.form1.Taxdate.value;//改变前的值
	
	var CertificateNoTmp=document.form1.Taxamount.value;//改变前的值
	
	var DeclarationDateTmp=document.form1.Taxgetdate.value;//改变前的值 DeclarationAmount
	

	
	if(Message){
		alert(Message);
		return false;
		}
	else{
		var message=confirm("保存之前请确认相关数据是否正确，点取消可以返回修改。");
		if (message==true){
			//document.form1.CompanyId.disabled=false;
			document.form1.action="cw_mdtax_save.php";
			document.form1.submit();
			}
		else{
			return false;
			}
		}
	}
	
	
	
</script>

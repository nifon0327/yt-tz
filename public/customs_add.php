<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
echo"<SCRIPT src='../model/addtblist.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 新增报关出口明细列表");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=900;$tableMenuS=500;
$CustomFun="<span onClick='ViewProductId(2)' $onClickCSS>加入Invoice</span>&nbsp;";//自定义功能
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
	<tr>
	<td width="10" class="A0010" bgcolor="#FFFFFF" height="20">&nbsp;</td>
	<td colspan="6" class="A0100" valign="bottom">◆报关单信息</td>
	<td width="10" class="A0001" bgcolor="#FFFFFF" height="20">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" height="20" class="A0010">&nbsp;</td>
		<td width="103" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>报关单号</td>
	  	<td width="167" align="left" class="A0100">
            &nbsp;&nbsp;
        	<input name="DeclarationNo" type="text" id="DeclarationNo" value="" size="20" maxlength="20" dataType="Require" Msg="未填写">
         </td>
      	<td width="102" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>核销单号</td>
		<td width="177" align="left" class="A0100">
            &nbsp;&nbsp;
        	<input name="CertificateNo" type="text" id="CertificateNo" value="" size="20" maxlength="20" dataType="Require" Msg="未填写">
         </td>
		<td width="112" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>报关日期</td>
		<td width="243" align="left" class="A0101">
            &nbsp;&nbsp;
            <input name="DeclarationDate" type="text" class="INPUT0000" id="DeclarationDate" value="<?php  echo date("Y-m-d")?>" maxlength="10" onfocus="WdatePicker()" readonly>
        </td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
    
	<tr>
		<td width="10" height="20" class="A0010">&nbsp;</td>
		<td width="103" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>报关金额</td>
	  	<td width="167" align="left" class="A0100">
        	&nbsp;&nbsp;
            <input name="DeclarationAmount" type="text" id="DeclarationAmount" value="" size="20" maxlength="20" dataType="Require" Msg="未填写">
      </td>
      
      
      	<td width="102" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>申报状态</td>
		<td width="177" align="left" class="A0100">
            &nbsp;&nbsp;
        <?php     

			  echo "<select name='DeclarationEstate'  id='DeclarationEstate' style='width:135px' dataType='Require' msg='未选'  > ";           

							echo "<option value='1' selected='selected' >未申报</option>";
							echo "<option value='0'  >已申报</option>";

				echo "</select>";
	
		  ?>	
         </td>
		<td width="112" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>报关资料</td>
		<td width="243" align="left" class="A0101">
            &nbsp;&nbsp;
            <input name="DeclarationFile" type="file" id="DeclarationFile" size="20" title="可选项,可上传rar、zip、xls、doc、pdf、eml、jpg格式" Row="2" Cel="2">       </td>
              

		<td width="10" class="A0001">&nbsp;</td>
	</tr>    
    <tr>
		<td width="10" height="20" class="A0010">&nbsp;</td>
		<td width="103" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>备&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注</td>
	  	<td colspan="5" class="A0101" align="left">
         &nbsp;&nbsp;
            <textarea name="Remark" cols="60" rows="6" id="Contant"></textarea>

	  	<td width="10" class="A0001">&nbsp;</td>
    </tr>    
	<tr>
		<td width="10" height="25" class="A0010">&nbsp;</td>
		<td colspan="6" valign="bottom">◆报关Invoice明细	    </td>
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
		<td class="A1101" width="112" align="center">货运信息</td>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
	<tr>
		<td width="10" class="A0010" height="300">&nbsp;</td>
		<td colspan="8" align="center" class="A0111">
		<div style="width:880;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='870' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTable">
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
//序号重整
function ShowSequence(TableTemp){
	for(i=0;i<TableTemp.rows.length;i++){ 
  		var j=i+1
		TableTemp.rows[i].cells[1].innerText=j; 
		}
	}   
function ViewProductId(Action){
	var r=Math.random();  
	//var Bid=document.getElementById('CompanyId').value;	
	var Bid="oK";
	if(Bid!=""){
		var BackData=window.showModalDialog("Invoice_s1.php?r="+r+"&tSearchPage=Invoice&fSearchPage=customs&SearchNum=2&Action="+Action,"BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
		
		if(BackData==null || BackData==''){  //专为safari设计的 ,add by zx 2011-05-04
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
				//过滤相同的ID号??不过滤：有时会产品ID一样，但下单数量不一样				
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
					oTD.width="170";
					
					oTD=oTR.insertCell(4);
					oTD.innerHTML=""+FieldArray[2]+"";
					oTD.className ="A0101";
					oTD.width="250";
					
					oTD=oTR.insertCell(5);
					oTD.innerHTML=FieldArray[3]+"";
					oTD.className ="A0101";
					oTD.width="100";
					
					oTD=oTR.insertCell(6); 
					oTD.innerHTML=FieldArray[4]+"";
					oTD.align="center";
					oTD.className ="A0101";
					oTD.width="70";
					 
					oTD=oTR.insertCell(7);
					oTD.innerHTML=FieldArray[5]+"";
					oTD.className ="A0101";
					oTD.width="100";
					}
				else{
					alert(Message);
					}
				}//end for
				return true;
			}
		else{
			alert("没有选产品！");
			return false;
			}
		}
	else{
		alert("没有选择客户!");
		return false;
		}
	}
/*	
function ChangeThis(Row,Keywords){
	var oldValue=document.form1.TempValue.value;//改变前的值
	var Qtytemp=eval("document.form1.Qty"+Row+".value");//改变后的值
	var Pricetemp=eval("document.form1.ProductPrice"+Row+".value");//改变后的值
	if(Keywords=="Qty"){		
		//检查是否数字格式
		var Result=fucCheckNUM(Qtytemp,'');
		if(Result==0 || Qtytemp==0){
			alert("输入了不正确的数量:"+Qtytemp+",重新输入!");
			eval("document.form1.Qty"+Row).value=oldValue;
			}
		else{
			eval("document.form1.Amount"+Row).value=FormatNumber(Qtytemp*Pricetemp,4);//改变数量所增加或减少的值
			}
		}
	else{
		//检查是否价格格式
		var Result=fucCheckNUM(Pricetemp,'Price');
		if(Result==0){
			alert("输入不正确的售价:"+Pricetemp+",重新输入!");
			eval("document.form1.ProductPrice"+Row).value=oldValue;
			}
		else{
			eval("document.form1.ProductPrice"+Row).value=FormatNumber(Pricetemp,4);
			eval("document.form1.Amount"+Row).value=FormatNumber(Pricetemp*Qtytemp,4);		
			}
		}
	}
*/
function CheckForm(){
	var Message="";
	var DeclarationNoTmp=document.form1.DeclarationNo.value;//改变前的值
	
	var CertificateNoTmp=document.form1.CertificateNo.value;//改变前的值
	
	var DeclarationDateTmp=document.form1.DeclarationDate.value;//改变前的值 DeclarationAmount
	
	var DeclarationAmountTmp=document.form1.DeclarationAmount.value;//改变前的值 DeclarationAmount
	

	/*	
	var Rowslength=ListTable.rows.length;//数组长度即领料记录数
	if(Rowslength==0){
		Message="没有Invoice，不能保存！";
	}
	*/
	var Result=fucCheckNUM(DeclarationAmountTmp,'Price');
		if(Result==0){
			Message="输入不正确的报关金额";
		}	
	if(DeclarationDateTmp==""){
		Message="未填写日期！";
		}
	if(CertificateNoTmp==""){
		Message="未填写核销单号！";
		}	
	if(DeclarationNoTmp==""){
		Message="未填写报关单号！";
		}			
		
	//检查上传的文件
	UploadStr=document.form1.DeclarationFile.value;
	if(UploadStr!=""){
		UploadEx=UploadStr.substring(UploadStr.lastIndexOf("."));   
		if(UploadEx!=".rar" && UploadEx!=".zip" && UploadEx!=".xls" && UploadEx!=".doc" && UploadEx!=".eml" && UploadEx!=".jpg" &&  UploadEx!=".pdf"){
			Message="不允许的文件格式";
			//NoteTable.rows[2].cells[2].innerHTML=" <input name='ClientOrder' type='file' id='ClientOrder' size='78' Row='2' Cel='2'>";//重写元素
			}
		}
	if(Message){
		alert(Message);
		return false;
		}
	else{
		var message=confirm("保存之前请确认相关数据是否正确，点取消可以返回修改。");
		if (message==true){
			//document.form1.CompanyId.disabled=false;
			document.form1.action="customs_save.php";
			document.form1.submit();
			}
		else{
			return false;
			}
		}
	}
</script>

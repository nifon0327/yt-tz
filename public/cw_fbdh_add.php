<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
echo"<SCRIPT src='../model/addtblist.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 添加货币汇兑记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=900;$tableMenuS=500;
$CustomFun="<span onClick='ViewProductId(2)' $onClickCSS>加入核销单号</span>&nbsp;";//自定义功能
//$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
	<tr>
	<td width="10" class="A0010" bgcolor="#FFFFFF" height="20">&nbsp;</td>
	<td colspan="10" class="A0100" valign="bottom">◆货币汇兑记录信息</td>
	<td width="10" class="A0001" bgcolor="#FFFFFF" height="20">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" height="20" class="A0010">&nbsp;</td>
		<td width="81" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>结汇凭证</td>
	  	<td width="221" align="left" class="A0100">
            &nbsp;
        	<input name="BillNumber" type="text" id="BillNumber" size="20" DataType="Require" Msg="结汇凭证不能为空" >
         </td>
      	<td width="46" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>兑换<br />
   	    日期</td>
		<td width="108" align="left" class="A0100">
            &nbsp;
        	<input name="PayDate" type="text" id="PayDate" size="12" dataType="Date" format="ymd" msg="格式不对或未填写" onfocus="WdatePicker()" readonly>
         </td>
		<td width="56" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>转出<br />
	    货币</td>
		<td width="111" align="left" class="A0101">
            &nbsp;
                <select name="OutCurrency" id="OutCurrency" style="width:70px" dataType="Require" msg="未选择">
                <option value="" selected>请选择</option>
				<?php 
				$checkCurrency=mysql_query("SELECT Id,Symbol FROM $DataPublic.currencydata WHERE Estate=1 ORDER BY Id",$link_id);
				if($checkCurrencyRow=mysql_fetch_array($checkCurrency)){
					do{
						$cSymbol=$checkCurrencyRow["Symbol"];
						$cId=$checkCurrencyRow["Id"];
						echo"<option value='$cId'>$cSymbol</option>";
						}while ($checkCurrencyRow=mysql_fetch_array($checkCurrency));
					}
				?>
              </select>
        </td>
		
        <td width="51" align="center" class="A0101" bgcolor='<?php  echo $Title_bgcolor?>'>转出<br />
        余额</td>
	  	<td width="112" align="left" class="A0101">
        	&nbsp;
            <input name="OutAmount" type="text" id="OutAmount" size="10" onchange="InAmountValue()" dataType="Currency" msg="未填写或格式不对">
    </td>
	  	<td width="53" align="center" class="A0101"><p>转出<br />
  	    银行</p></td>
	  	<td width="191" align="left" class="A0101"><?php  
		$BankFromName="OutBankId";
		include "../model/selectbank1.php";?></td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
    
    
	<tr>
		<td width="10" height="20" class="A0010">&nbsp;</td>
		<td width="81" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>凭证上传</td>
	  	<td width="221" align="left" class="A0100">
        	&nbsp;
            <input name="Attached" type="file" id="Attached" size="20"  DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="2" Cel="2">
      </td>
      	<td width="46" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>汇率</td>
		<td width="108" align="left" class="A0100">
            &nbsp;
             <input name="Rate" type="text" id="Rate" size="12" maxlength="12" onchange="InAmountValue()" dataType="Currency" msg="未填写或格式不对">
         </td>
		<td width="56" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>转入<br />
	    货币</td>
		<td width="111" align="left" class="A0101">
            &nbsp;
            <select name="InCurrency" id="InCurrency" style="width:70px" dataType="Require" msg="未选择">
              <option value="" selected>请选择</option>
				<?php 
				$checkCurrency=mysql_query("SELECT Id,Symbol FROM $DataPublic.currencydata WHERE Estate=1 ORDER BY Id",$link_id);
				if($checkCurrencyRow=mysql_fetch_array($checkCurrency)){
					do{
						$cSymbol=$checkCurrencyRow["Symbol"];
						$cId=$checkCurrencyRow["Id"];
						echo"<option value='$cId'>$cSymbol</option>";
						}while ($checkCurrencyRow=mysql_fetch_array($checkCurrency));
					}
				?>
              </select>       </td>
        <td width="51" align="center" class="A0101" bgcolor='<?php  echo $Title_bgcolor?>'>转入<br />
        金额</td>
  	  <td width="112" align="left" class="A0101">
        	&nbsp;
            <input name="InAmount" type="text" id="InAmount" size="10" readonly title="自动计算">
    </td>
  	  <td width="53" align="center" class="A0101">转入<br />
      银行</td>
  	  <td width="191" align="left" class="A0101"><?php  
	  $BankFromName="InBankId";
	  include "../model/selectbank1.php";?></td>
              

		<td width="10" class="A0001">&nbsp;</td>
	</tr>    



	<tr>
		<td width="10" height="20" class="A0010">&nbsp;</td>
		<td width="81" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
	  	<td colspan="9" align="left" class="A0100">
        &nbsp;
        <textarea name="Remark" cols="48" rows="5" id="Remark" dataType="Require"  msg="未填写"></textarea>
        </td>
   	  <td width="10" class="A0001">&nbsp;</td>
	</tr>   
    
  
	<tr>
		<td width="10" height="25" class="A0010">&nbsp;</td>
		<td colspan="10" valign="bottom">◆核销单明细	    </td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
	
</table>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="10" class="A0010" bgcolor="#FFFFFF" height="20">&nbsp;</td>
		<td class="A1111" width="40" align="center">操作</td>
		<td class="A1101" width="40" align="center">序号</td>
		<td class="A1101" width="100" align="center">报关日期</td>
		<td class="A1101" width="177" align="center">报关单号</td>
		<td class="A1101" width="177" align="center">核销单号</td>
		<td class="A1101" width="100" align="center">报关金额</td>
		<td class="A1101" width="100" align="center">发票日期</td>
		<td class="A1101" width="150" align="center">出口发票</td>
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
		var BackData=window.showModalDialog("customs_s1.php?r="+r+"&tSearchPage=customs&fSearchPage=cw_fbdh&SearchNum=2&Action="+Action,"BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
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
					oTD.innerHTML=FieldArray[0]+"";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="100";
					
					
					oTD=oTR.insertCell(3);
					oTD.innerHTML=""+FieldArray[1];
					oTD.className ="A0101";
					oTD.width="177";
					
					oTD=oTR.insertCell(4);
					oTD.innerHTML="<input style='border:1px;border-bottom-style:none;border-top-style:none;border-left-style:none;border-right-style:none;' name='CertificateNo["+tmpNum+"]' type='text' id='CertificateNo"+tmpNum+"' size='4' value='"+FieldArray[2]+"' class='noLine' readonly>";
					oTD.className ="A0101";
					oTD.width="177";
					
					oTD=oTR.insertCell(5);
					oTD.innerHTML=FieldArray[3]+"";
					oTD.className ="A0101";
					oTD.width="100";
					
					oTD=oTR.insertCell(6); 
					oTD.innerHTML=FieldArray[4]+"";
					oTD.align="center";
					oTD.className ="A0101";
					oTD.width="100";
					 
					oTD=oTR.insertCell(7);
					oTD.innerHTML=FieldArray[5]+"";
					oTD.className ="A0101";
					oTD.width="137";
					}
				else{
					alert(Message);
					}
				}//end for
				return true;
			}
		else{
			alert("没有选核销单！");
			return false;
			}
		}
	else{
		alert("没有选择客户!");
		return false;
		}
	}
	
/*	


function CheckForm(){
	var Message="";
	var DeclarationNoTmp=document.form1.DeclarationNo.value;//改变前的值
	
	var CertificateNoTmp=document.form1.CertificateNo.value;//改变前的值
	
	var DeclarationDateTmp=document.form1.DeclarationDate.value;//改变前的值 DeclarationAmount
	
	var DeclarationAmountTmp=document.form1.DeclarationAmount.value;//改变前的值 DeclarationAmount
	

		
	var Rowslength=ListTable.rows.length;//数组长度即领料记录数
	if(Rowslength==0){
		Message="没有Invoice，不能保存！";
	}
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
*/	
</script>
<script>
function InAmountValue(){
	var OutAmount=document.form1.OutAmount.value;
	var Rate=document.form1.Rate.value;
	if(OutAmount!="" && Rate!=""){
		var InAmount=(OutAmount*1)*(Rate*1);
		document.form1.InAmount.value=InAmount;
		}

	}
</script>
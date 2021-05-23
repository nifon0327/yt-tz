<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
echo"<SCRIPT src='../model/addtblist.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 更新免抵退税收益明细列表");//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="Id,$Id,fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=900;$tableMenuS=500;
$CustomFun="<span onClick='ViewProductId(2)' $onClickCSS>加入报关费用</span>&nbsp;";//自定义功能
$OtherFun="<span onClick='ViewOtherfee(2)' $onClickCSS>加入行政费用</span>&nbsp;";
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_tt.php";

$upResult = mysql_query("SELECT M.Id,M.TaxNo,M.Taxdate,M.Taxamount,M.BankId,M.Taxgetdate,M.Attached,M.Estate,M.Remark,M.Operator,M.endTax,M.Proof FROM $DataIn.cw14_mdtaxmain M WHERE M.Id=$Id",$link_id);
if ($upData = mysql_fetch_array($upResult)) {
	$Id=$upData["Id"];
	$Taxdate=$upData["Taxdate"];
	$TaxNo=$upData["TaxNo"];
	$Taxamount=$upData["Taxamount"];
	$BankId=$upData["BankId"];
	$Taxgetdate=$upData["Taxgetdate"];
	$Attached=$upData["Attached"];
	$Estate=$upData["Estate"];
	$Remark=$upData["Remark"];
	$Operator=$upData["Operator"];
	$endTax=$upData["endTax"];
	$Proof=$upData["Proof"];
	}

//步骤4：需处理
?>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
	<tr>
	<td width="10" class="A0010" bgcolor="#FFFFFF" height="15">&nbsp;</td>
	<td colspan="8" class="A0100" valign="bottom">◆免抵税信息</td>
	<td width="10" class="A0001" bgcolor="#FFFFFF" height="15">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" height="25" class="A0010">&nbsp;</td>
		<td width="112" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>国税时间</td>
	  	<td width="140" align="left" class="A0100">
            &nbsp;&nbsp;
       <input name="Taxdate" type="text" class="INPUT0000" id="Taxdate" value="<?php  echo $Taxdate?>" maxlength="10" onfocus="WdatePicker()" readonly>
         </td>
      	<td width="100" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>免抵税金额</td>
		<td width="166" align="left" class="A0100">
            &nbsp;&nbsp;
        	<input name="Taxamount" type="text" id="Taxamount" value="<?php  echo $Taxamount?>" size="20" maxlength="20" dataType="Require" Msg="未填写">
         </td>
		<td width="100" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>结付银行</td>
		<td width="149" align="left" class="A0101"><?php  include "../model/selectbank2.php";?></td>
	  <td width="68" align="center" class="A0101">收款日期</td>
	  <td width="120" align="left" class="A0101"><input name="Taxgetdate" type="text" class="INPUT0000" id="Taxgetdate" onfocus="WdatePicker()" value="<?php  echo $Taxgetdate?>" size="12" maxlength="10" readonly /></td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
    
	<tr>
		<td width="10" height="25" class="A0010">&nbsp;</td>
		<td width="112" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>免抵退税发票号</td>
	  	<td width="170" align="left" class="A0100">
            &nbsp;&nbsp;
        	<input name="TaxNo" type="text" id="TaxNo" value="<?php  echo $TaxNo?>" size="20" maxlength="20" dataType="Require" Msg="未填写">
         </td>
      
      <!--	<td width="70" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>税款状态</td>
		<td width="166" align="left" class="A0100">
            &nbsp;&nbsp;
            <?php     
			        echo "<select name='Estate'  id='Estate' style='width:135px' dataType='Require' msg='未选'  > "; 
					if($Estate==0){          
                        echo "<option value='0' selected='selected' >税款已收到</option>";
					    echo "<option value='3'  >税款未收到</option>";
					}
					else
					{
					    echo "<option value='0' >税款已收到</option>";
					    echo "<option value='3' selected='selected' >税款未收到</option>";
					}
					
                    echo "</select>";
		    ?>	
         </td>-->
		<td width="100" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>期末留抵税额</td>
		<td colspan="5" align="left" class="A0101">
            &nbsp;&nbsp;
			<input name="endTax" type="text" id="endTax" value="<?php  echo $endTax?>" size="20" maxlength="20" >       
			</td>
		 <td width="10" class="A0001">&nbsp;</td>
	</tr>
	<tr>
	    <td width="10" height="20" class="A0010">&nbsp;</td>
		<td width="112" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>扫描附件</td>
		<td align="left" class="A0101" colspan="3">
            &nbsp;&nbsp;
            <input name="Attachedfile" type="file" id="Attachedfile"   size="20" title="可选项,上传jpg格式" Row="2" Cel="2">
			  <?php 
             if($Attached!="")
			         {
			  			 echo"<input type='checkbox' name='delFile' id='delFile' value='$Attached'><LABEL for='delFile'>删除已上传扫描资料</LABEL> ";
			         }
            ?>       
		</td>
		<td width="100" align="center" class="A0101">结付凭证</td>
		<td colspan="3" align="left" class="A0101">
            &nbsp;&nbsp;
        	<input name="proof" type="file" id="proof" size="20" title="可选项,上传jpg格式" Row="2" Cel="2">  
     </td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>    

    <tr>
		<td width="10" height="20" class="A0010">&nbsp;</td>
		<td width="112" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>备&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注</td>
	  	<td colspan="7" class="A0101" align="left">
         &nbsp;&nbsp;
            <textarea name="Remark" cols="60" rows="3" id="Remark" ><?php  echo $Remark?></textarea>

	  	<td width="10" class="A0001">&nbsp;</td>
    </tr>    

	<tr>
		<td width="10" height="25" class="A0010">&nbsp;</td>
		<td colspan="8" valign="bottom"><span class="redB">◆已有报关费用明细	<span>    </td>
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
		<td width="10" class="A0010" height="120">&nbsp;</td>
		<td colspan="8" align="center" class="A0111">
		<div style="width:880;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='870' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="StuffList">
 		<?php 
		//需求单列表

		$StockResult = mysql_query("SELECT 
				M.Id,M.CompanyId,M.BankId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.Remark,M.Ship,M.Operator,T.Type as incomeType,C.Forshort 
				FROM $DataIn.cw14_mdtaxsheet K  
				LEFT JOIN  $DataIn.ch1_shipmain M  ON M.Number=K.shipmainNumber 
				LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
				LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
                WHERE  K.TaxNo='$TaxNo'
			",$link_id);
		/*
		echo "SELECT 
				M.Id,M.CompanyId,M.BankId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.Remark,M.Ship,M.Operator,T.Type as incomeType,C.Forshort 
				FROM $DataIn.cw13_customssheet K  
				LEFT JOIN  $DataIn.ch1_shipmain M  ON M.Number=K.shipmainNumber 
				LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
				LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
                WHERE  K.DeclarationNo='$DeclarationNo'";
		*/		
		if($StockRows = mysql_fetch_array($StockResult)){
			$i=1;
			do{
			    $Ids=$StockRows["Id"];
				$CompanyId=$StockRows["CompanyId"];
				$Number=$StockRows["Number"];
				$Forshort=$StockRows["Forshort"];
				$InvoiceNO=$StockRows["InvoiceNO"];
				$InvoiceFile=$StockRows["InvoiceFile"];
				$Wise=$StockRows["Wise"]==""?"&nbsp;":$StockRows["Wise"];
				$Date=$StockRows["Date"];	
				$Sign=$StockRows["Sign"];
				$checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$Ids'",$link_id));
				$Amount=sprintf("%.2f",$checkAmount["Amount"])*$Sign;				
				
			    //echo "$Number.^^.$Forshort.^^.$InvoiceNO.^^.$Amount.^^.$Date.^^.$Wise";
				echo"<tr><td width='40' class='A0101' align='center'>";
				echo"<a href='#' onclick='deleteRow(this.parentNode,StuffList)' title='删除此Invoice'>×</a>";
				echo"<td width='40' class='A0101' align='center'>$i</td>";
				echo"</td><td width='100' class='A0101' align='center'>$Number</td>
					<td width='170' class='A0101' align='center'>$Forshort</td>
					<td width='248' class='A0101' align='center'><DIV STYLE='width:203 px;overflow: hidden; text-overflow:ellipsis' title='$StuffCname'><NOBR>$InvoiceNO</NOBR></DIV></td>
					<td width='100' class='A0101' align='center'>$Amount</td>
					<td width='70' class='A0101' align='center'>$Date</td>
					<td width='100' class='A0101' align='center'>报关</td>
					</tr>";
				$i++;
				}while($StockRows = mysql_fetch_array($StockResult));
			}
		?>           
            
            
			</table>
		</div>		
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
</table>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
	<tr>
		<td width="10" height="15" class="A0010">&nbsp;</td>
		<td   valign="bottom">&nbsp;	    </td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" height="15" class="A0010">&nbsp;</td>
		<td   valign="bottom">◆新增报关费用明细	    </td>
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
		<td width="10" class="A0010" height="80">&nbsp;</td>
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
include "cw_mdtax_other.php";
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


//序号重整
function ShowSequence(TableTemp){
	for(i=0;i<TableTemp.rows.length;i++){ 
  		var j=i+1
		TableTemp.rows[i].cells[1].innerText=j; 
		}
	}  
	
	
//======================================================================================================更新报关费用	

function deleteRows (tt){
	var rowIndex=tt.parentElement.rowIndex;
	ListTable.deleteRow(rowIndex);
	ShowSequence(ListTable);
	}
	

function deleteRow (RowTemp,TableTemp){
	var rowIndex=RowTemp.parentElement.rowIndex;
	if(TableTemp==ListTable){	//新增需求单列表
		TableTemp.deleteRow(rowIndex);
		ShowSequence(TableTemp);
		}
	else{//处理原需求单删除，删除成功后再删除行		
		var message=confirm("确定删除此Invoice吗？");
		if (message==true){
			//输入删除原因
			 //var delRemark=prompt("请输入删除需求单的原因","");
			//if(delRemark!=""){ 
				var delshipmainNumber=TableTemp.rows[rowIndex].cells[2].innerText;
				//delRemark=encodeURIComponent(delRemark);
				alert(delshipmainNumber);
				myurl="cw_mdtax_updated.php?shipmainNumber="+delshipmainNumber+"&ActionId=delshipmainNumber";
				retCode=openUrl(myurl);
				if (retCode!=-2){//标记删除成功，不直接删除需求单，而是做标记
					//TableTemp.deleteRow(rowIndex);
					//ShowSequence(TableTemp);
					TableTemp.rows[rowIndex].cells[0].innerHTML="&nbsp;";
					}
				else{
					alert("标记删除失败！");return false;
				}
			/*		
				}
			else{
				alert("没有输入删除原因!");return false;
				}
			*/	
			}
		else{
			return false;
			}			
		}	
	}
 
function ViewProductId(Action){
	var r=Math.random();  
	//var Bid=document.getElementById('CompanyId').value;	
	var Bid="oK";
	if(Bid!=""){
		var BackData=window.showModalDialog("Invoice_s3.php?r="+r+"&tSearchPage=Invoice&fSearchPage=customs&SearchNum=2&Action="+Action,"BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
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
					//oTD.innerHTML="<a href='#' onclick='deleteRows(this.parentNode)' title='删除当前行'>×</a>";
					oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode,ListTable)' title='删除当前行'>×</a>";
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
					oTD.innerHTML="<input style='border:1px;border-bottom-style:none;border-top-style:none;border-left-style:none;border-right-style:none;' name='InvoiceNumber["+tmpNum+"]' type='text' id='InvoiceNumber"+tmpNum+"' size='15' value='"+FieldArray[2]+"' class='noLine' readonly>";
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
					oTD.innerHTML="报关"+"";
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
			alert("没有选！");
			return false;
			}
		}
	else{
		alert("没有选择客户!");
		return false;
		}
	}

//=============================================================================================================更新行政费用


function deletefeeRows(ss){
     var rowIndex=ss.parentElement.rowIndex;
	 ListTablefee.deleteRow(rowIndex);
	 ShowSequence(ListTablefee);
	}

function deleteRowfee (RowTemp,TableTemp){
	var rowIndex=RowTemp.parentElement.rowIndex;
	if(TableTemp==ListTablefee){	//新增需求单列表
		TableTemp.deleteRowfee(rowIndex);
		ShowSequence(TableTemp);
		}
	else{//处理原需求单删除，删除成功后再删除行		
		var message=confirm("确定删除此行政费用吗？");
		if (message==true){
			//输入删除原因
			 //var delRemark=prompt("请输入删除需求单的原因","");
			//if(delRemark!=""){ 
				var delotherfeeNumber=TableTemp.rows[rowIndex].cells[2].innerText;
				//delRemark=encodeURIComponent(delRemark);
				alert(delotherfeeNumber);
				myurl="cw_mdtax_updated.php?otherfeeNumber="+delotherfeeNumber+"&ActionId=delotherfeeNumber";
				retCode=openUrl(myurl);
				if (retCode!=-2){//标记删除成功，不直接删除需求单，而是做标记
					//TableTemp.deleteRow(rowIndex);
					//ShowSequence(TableTemp);
					TableTemp.rows[rowIndex].cells[0].innerHTML="&nbsp;";
					}
				else{
					alert("标记删除失败！");return false;
				}
			/*		
				}
			else{
				alert("没有输入删除原因!");return false;
				}
			*/	
			}
		else{
			return false;
			}			
		}	
	}
	
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
//==================================================================


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
			document.form1.action="cw_mdtax_updated.php";
			document.form1.submit();
			}
		else{
			return false;
			}
		}
	}
</script>


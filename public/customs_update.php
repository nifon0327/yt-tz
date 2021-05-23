<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
echo"<SCRIPT src='../model/addtblist.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 更新报关出口明细列表");//需处理
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="Id,$Id,fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=900;$tableMenuS=500;
$CustomFun="<span onClick='ViewProductId(2)' $onClickCSS>加入Invoice</span>&nbsp;";//自定义功能
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";

$upResult = mysql_query("SELECT M.Id,M.DeclarationNo,M.DeclarationDate,M.CertificateNo,M.CertificateEstate,M.DeclarationAmount,M.exportinvoiceNo,M.exportinvoiceDate,M.DeclarationFile,M.Remark,M.Estate,M.Locks,M.Date,M.Operator,D.BillNumber  
FROM $DataIn.cw13_customsmain M
	LEFT JOIN $DataIn.cw5_customsfbdh K  ON K.CertificateNo=M.CertificateNo
	LEFT JOIN $DataIn.cw5_fbdh D  ON D.BillNumber=K.BillNumber
WHERE M.Id=$Id",$link_id);
/*
echo "SELECT S.Id,S.DeclarationNo,S.DeclarationDate,S.CertificateNo,S.CertificateEstate,C.Symbol,S.DeclarationAmount,S.exportinvoiceNo,S.exportinvoiceDate,S.BillNumber,s.DeclarationFile,S.Remark,S.Estate,S.Locks,S.Date,S.Operator  
FROM $DataIn.cw13_customsmain S	
WHERE S.Id=$Id";
*/
if ($upData = mysql_fetch_array($upResult)) {
	$DeclarationNo=$upData["DeclarationNo"];
	$DeclarationDate=$upData["DeclarationDate"];
	$CertificateNo=$upData["CertificateNo"];
	$DeclarationAmount=$upData["DeclarationAmount"];
	$exportinvoiceNo=$upData["exportinvoiceNo"];
	$exportinvoiceDate=$upData["exportinvoiceDate"]==""?date("Y-m-d"):$upData["exportinvoiceDate"];
	$DeclarationFile=$upData["DeclarationFile"];
    $CertificateEstate=$upData["CertificateEstate"];
    $BillNumber=$upData["BillNumber"];
	if ($BillNumber!=""){  //表示已出结汇凭证，不能修改 核实单号、核实状态，金额
	   $readonlyLock="readonly='readonly'";
	}
	$Remark=$upData["Remark"];
	}
	
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
		<td width="123" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>报关单号</td>
	  	<td width="167" align="left" class="A0100">
            &nbsp;&nbsp;
        	<input name="DeclarationNo" type="text" id="DeclarationNo" value="<?php  echo $DeclarationNo?>" size="20" maxlength="20"   readonly="readonly">
         </td>
      	<td width="110" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>核销单号</td>
		<td width="180" align="left" class="A0100">
            &nbsp;&nbsp;
        	<input name="CertificateNo" type="text" id="CertificateNo" value="<?php  echo $CertificateNo?>" size="20" maxlength="20" dataType="Require" Msg="未填写" <?php  echo $readonlyLock?> >
         </td>
		<td width="117" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>报关日期</td>
		<td width="196" align="left" class="A0101">
            &nbsp;&nbsp;
            <input name="DeclarationDate" type="text" class="INPUT0000" id="DeclarationDate" value="<?php  echo $DeclarationDate?>" maxlength="10" onfocus="WdatePicker()" readonly>
        </td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
    
	<tr>
		<td width="10" height="20" class="A0010">&nbsp;</td>
		<td width="123" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>报关金额</td>
	  	<td width="167" align="left" class="A0100">
        	&nbsp;&nbsp;
            <input name="DeclarationAmount" type="text" id="DeclarationAmount" value="<?php  echo $DeclarationAmount?>" size="20" maxlength="20" dataType="Require" Msg="未填写" <?php  echo $readonlyLock?> >
      </td>
      	<td width="110" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>报关资料</td>
		<td colspan="3" align="left" class="A0111">
            &nbsp;&nbsp;
            <input name="DeclarationFile" type="file" id="DeclarationFile" size="40" title="可选项,可上传rar、zip、xls、doc、pdf、eml、jpg格式" Row="2" Cel="2">
		 <?php 
        if($DeclarationFile!=""){
            echo"
<input type='checkbox' name='delFile' id='delFile' value='$DeclarationFile'><LABEL for='delFile'>删除已上传报关资料</LABEL> ";
            }
            ?>
        </td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>   
    
	<tr>
		<td width="10" height="20" class="A0010">&nbsp;</td>
		<td width="123" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>出口发票</td>
	  	<td width="167" align="left" class="A0100">
        	&nbsp;&nbsp;
            <input name="exportinvoiceNo" type="text" id="exportinvoiceNo"  value="<?php  echo $exportinvoiceNo?>" size="20" maxlength="20" dataType="Require" Msg="未填写">
      </td>
      	<td width="110" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>发票日期</td>

		<td width="180" align="left" class="A0100">
          &nbsp;&nbsp;
                    <input name="exportinvoiceDate" type="text" class="INPUT0000" id="exportinvoiceDate" value="<?php  echo $exportinvoiceDate?>" maxlength="10" onfocus="WdatePicker()" readonly>         </td>
		<td width="117" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>申报状态</td>
		<td width="196" align="left" class="A0101">
            &nbsp;&nbsp;
        <?php     
          
		  if($readonlyLock!=""){
			echo " <input name='DeclarationEstate' type='hidden' id='DeclarationEstate' value='$DeclarationEstate' >";
			echo "<span align='center' class='redB' title='已核实'>已结汇!!</span>";
		  }
		  else{
			  echo "<select name='DeclarationEstate'  id='DeclarationEstate' style='width:90px' dataType='Require' msg='未选'  > ";           
			   
						if ($DeclarationEstate==1){
							echo "<option value='1' selected='selected' >未申报</option>";
							echo "<option value='0'  >已申报</option>";
						}
						else{
							echo "<option value='1'  >未申报</option>";
							echo "<option value='0' selected='selected' >已申报</option>";
	
						}
					
			   
				echo "</select>";
		  }
		  ?>	
        </td>             
		<td width="10" class="A0001">&nbsp;</td>
	</tr>    
    
     
    <tr>
		<td width="10" height="20" class="A0010">&nbsp;</td>
		<td width="123" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>备&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注</td>

 	  	<td colspan="3" align="left" class="A0100">
            &nbsp;&nbsp;
       	 <textarea name="Remark" cols="49" rows="6" id="Contant"><?php  echo $Description?></textarea>                </td>
       	<td width="117" align="center" class="A0111" bgcolor='<?php  echo $Title_bgcolor?>'>核实状态</td>
		<td width="196" align="left" class="A0101">
            &nbsp;&nbsp;
        <?php     
          
		  if($readonlyLock!=""){
			echo " <input name='CertificateEstate' type='hidden' id='CertificateEstate' value='$CertificateEstate' >";
			echo "<span align='center' class='redB' title='已核实'>已结汇!!</span>";
		  }
		  else{
			  echo "<select name='CertificateEstate'  id='CertificateEstate' style='width:90px' dataType='Require' msg='未选'  > ";           
			   
						if ($CertificateEstate==1){
							echo "<option value='1' selected='selected' >未核实</option>";
							echo "<option value='0'  >已核实</option>";
						}
						else{
							echo "<option value='1'  >未核实</option>";
							echo "<option value='0' selected='selected' >已核实</option>";
	
						}
					
			   
				echo "</select>";
		  }
		  ?>	
        </td>
 
 
         
	  	<td width="10" class="A0001">&nbsp;</td>
    </tr>    
	<tr>
		<td width="10" height="25" class="A0010">&nbsp;</td>
		<td colspan="6" valign="bottom"><span class="redB">◆已有报关Invoice明细	<span>    </td>
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
		<td width="10" class="A0010" height="150">&nbsp;</td>
		<td colspan="8" align="center" class="A0111">
		<div style="width:880;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='870' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="StuffList">
 		<?php 
		//需求单列表
		$StockResult = mysql_query("SELECT 
				M.Id,M.CompanyId,M.BankId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.Remark,M.Ship,M.Operator,T.Type as incomeType,C.Forshort 
				FROM $DataIn.cw13_customssheet K  
				LEFT JOIN  $DataIn.ch1_shipmain M  ON M.Number=K.shipmainNumber 
				LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
				LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
                WHERE  K.DeclarationNo='$DeclarationNo'
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
				$CompanyId=$StockRows["CompanyId"];
				$Number=$StockRows["Number"];
				$Forshort=$StockRows["Forshort"];
				$InvoiceNO=$StockRows["InvoiceNO"];
				$InvoiceFile=$StockRows["InvoiceFile"];
				$Wise=$StockRows["Wise"]==""?"&nbsp;":$StockRows["Wise"];
				$Date=$StockRows["Date"];	
				$Sign=$StockRows["Sign"];//收支标记
				$checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$Id'",$link_id));
				$Amount=sprintf("%.2f",$checkAmount["Amount"])*$Sign;				
				
			    //echo "$Number.^^.$Forshort.^^.$InvoiceNO.^^.$Amount.^^.$Date.^^.$Wise";
				echo"<tr><td width='40' class='A0101' align='center'>";
				echo"<a href='#' onclick='deleteRow(this.parentNode,StuffList)' title='删除此Invoice'>×</a>";
				echo"<td width='40' class='A0101' align='center'>$i</td>";
				echo"</td><td width='100' class='A0101' align='center'>$Number</td>
					<td width='170' class='A0101' align='center'>$Forshort</td>
					<td width='248' class='A0101'><DIV STYLE='width:203 px;overflow: hidden; text-overflow:ellipsis' title='$StuffCname'><NOBR>$InvoiceNO</NOBR></DIV></td>
					<td width='100' class='A0101' align='center'>$Amount</td>
					<td width='70' class='A0101' align='center'>$Date</td>
					<td width='100' class='A0101' align='center'>$Wise</td>
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
		<td width="10" height="25" class="A0010">&nbsp;</td>
		<td   valign="bottom">&nbsp;	    </td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" height="25" class="A0010">&nbsp;</td>
		<td   valign="bottom">◆新增报关Invoice明细	    </td>
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
		<td width="10" class="A0010" height="150">&nbsp;</td>
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
/*	
function deleteRows (tt){
	var rowIndex=tt.parentElement.rowIndex;
	ListTable.deleteRow(rowIndex);
	ShowSequence(ListTable);
	}
*/
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
				myurl="customs_updated.php?shipmainNumber="+delshipmainNumber+"&ActionId=delshipmainNumber";
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
	/*
	if(DeclarationNoTmp==""){
		Message="未填写报关单号！";
		}
	*/	
		
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
			document.form1.action="customs_updated.php";
			document.form1.submit();
			}
		else{
			return false;
			}
		}
	}
</script>

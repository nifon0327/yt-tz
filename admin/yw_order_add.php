<?php   
include "../model/modelhead.php";
include "../model/sweetalert.php";
echo"<SCRIPT src='../model/addtblist.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 新增客户订单");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=1000;$tableMenuS=500;
$CustomFun="<span onClick='ViewProductId(2)' $onClickCSS>加入产品</span>&nbsp;";//自定义功能
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
	<tr>
	<td width="10" class="A0010" bgcolor="#FFFFFF" height="20">&nbsp;</td>
	<td colspan="6" class="A0100" valign="bottom">◆主订单信息</td>
	<td width="10" class="A0001" bgcolor="#FFFFFF" height="20">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" height="30" class="A0010">&nbsp;</td>
		<td width="145" align="center" class="A0111" bgcolor='<?php    echo $Title_bgcolor?>'>客&nbsp;&nbsp;&nbsp;&nbsp;户</td>
	  	<td width="145" align="center" class="A0100"><select name="CompanyId" id="CompanyId" style="width:150px" onchange="deleteAllRow()">
        <?php    
		echo"<option value='' selected>请选择</option>";
		$result = mysql_query("SELECT C.CompanyId,C.Forshort FROM $DataIn.trade_object C 
		  WHERE C.Estate=1 AND  C.ObjectSign IN (1,2) AND  EXISTS( 
		  SELECT D.CompanyId FROM $DataIn.Productdata D WHERE D.CompanyId=C.CompanyId ) ORDER BY C.Id",$link_id);
		if($myrow = mysql_fetch_array($result)){
			do{
              if($CompanyId==$myrow["CompanyId"]){
                  $Forshort=$myrow["Forshort"];
                   echo"<option value='$myrow[CompanyId]' selected>$myrow[Forshort]</option>";
                 }
			else	echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
				} while ($myrow = mysql_fetch_array($result));
			}
		?>
      	</select></td>
      	<td width="145" align="center" class="A0101" bgcolor='<?php    echo $Title_bgcolor?>'>订&nbsp;单&nbsp;PO</td>
		<td width="145" align="center" class="A0101"><input name="OrderPO" type="text" class="INPUT0000" id="OrderPO" maxlength="20" ></td>
		<td width="145" align="center" class="A0101" bgcolor='<?php    echo $Title_bgcolor?>'>订单日期</td>
		<td width="145" align="center" class="A0101"><input name="OrderDate" type="text" class="INPUT0000" id="OrderDate" value="<?php  
		if(date("Y")<2016){
			echo "2016-01-01";
		}else{
		   echo date("Y-m-d");
		}
		?>" maxlength="10" onfocus="WdatePicker()" readonly></td>
		<td width="10" class="A0001">&nbsp;</td>
	<tr>
		<td width="10"  class="A0010" height="30">&nbsp;</td>
		<td width="145" align="center" class="A0111" bgcolor='<?php    echo $Title_bgcolor?>'>客户下单资料</td>
	  	<td colspan="5" class="A0101"><input name="ClientOrder" type="file" id="ClientOrder" size="78" title="可选项,可上传rar、zip、xls、doc、pdf、eml、jpg、titf格式" Row="2" Cel="2"></td>
	  	<td width="10" class="A0001">&nbsp;</td>
	<tr>
		<td width="10" height="25" class="A0010">&nbsp;</td>
		<td colspan="6" valign="bottom">◆产品订单明细	    </td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
	</tr>
</table>

<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr bgcolor='<?php    echo $Title_bgcolor?>'>
		<td width="10" class="A0010" height="25">&nbsp;</td>
		<td height="25" align="center" class="A0111">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:auto">
			<table width='100%' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" >
              <tr>  
                <td class="A1101" width="40" align="center">操作</td>
                <td class="A1101" width="40" align="center">序号</td>
                <td class="A1101" width="60" align="center">产品ID</td>
                <td class="A1101" width="250" align="center">产品名称</td>
                <td class="A1101" width="250" align="center">Product Code</td>
                <td class="A1101" width="100" align="center">订购数量</td>
                <td class="A1101" width="70" align="center">售价</td>
                <td class="A1100" width="" align="center">小计</td>
              </tr>         
			</table>
		</div>		
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
        
	<tr>
		<td width="10" class="A0010" height="300">&nbsp;</td>
		<td align="center" class="A0110" height="300">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='100%' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTable">
			</table>
		</div>		
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
</table>




<input name="TempValue" id="TempValue"  type="hidden" value="0">

<input name="Pid0" id="Pid0" type="hidden" value="">
<input name="PCName0"  id="PCName0" type="hidden" value="">
<input name="Qty0" id="Qty0" type="hidden" value="">
<input name="ProductPrice0" id="ProductPrice0" type="hidden" value="">
<input name="Amount0" id="Amount0"  type="hidden" value="">

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
		document.form1.submit();
	}
function deleteRows (tt){
	//var rowIndex=tt.parentElement.rowIndex;
	if(tt.parentElement==null || tt.parentElement=="undefined" ){  // add by zx 2011-05-06 Firfox不支持 parentElement
		var rowIndex=tt.parentNode.rowIndex;
	}
	else{
		var rowIndex=tt.parentElement.rowIndex; 
	}	
	
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
//窗口打开方式修改为兼容性的模态框 by ckt 2017-12-23
function ViewProductId(Action) {
    var Bid = document.getElementById('CompanyId').value;
    /*判断是否填写po  by.lwh*/
    var Bpo = document.getElementById('OrderPO').value;
    if (Bpo != ""){
    if (Bid != "") {
        var SafariReturnValue = document.getElementById('SafariReturnValue');
        if (!arguments[1]) {
            var r = Math.random();
            SafariReturnValue.value = "";
            SafariReturnValue.callback = 'ViewProductId("",true)';
            var url = "/admin/productdata_s1.php?r=" + r + "&tSearchPage=productdata&fSearchPage=clientorder&SearchNum=2&Action=" + Action + "&CompanyId=" + Bid;
            openFrame(url, 980, 650);//url需为绝对路径
            return false;
        }
        if (SafariReturnValue.value) {
            //锁定客户选项
            //document.checkFrom.CompanyId.disabled=true;
            var checkFrom = document.getElementById('CompanyId');
            checkFrom.disabled = "disabled";

            var Rows = SafariReturnValue.value.split("``");//分拆记录
            SafariReturnValue.value = "";
            SafariReturnValue.callback = "";
            var Rowslength = Rows.length;//数组长度
            //加入如下的代码****************************************
            if (document.getElementById("TempMaxNumber")) {  ////给add by zx 2011-05-05 firfox and  safari不能用javascript生成的元素
                var TempMaxNumber = document.getElementById("TempMaxNumber");
                TempMaxNumber.value = TempMaxNumber.value * 1 + Rowslength * 1;
            }

            for (var i = 0; i < Rowslength; i++) {
                var Message = "";
                var FieldTemp = Rows[i];		//拆分后的记录
                var FieldArray = FieldTemp.split("^^");//分拆记录中的字段
                //过滤相同的ID号??不过滤：有时会产品ID一样，但下单数量不一样
                if (Message == "") {
                    oTR = ListTable.insertRow(ListTable.rows.length);
                    tmpNum = oTR.rowIndex + 1;

                    oTD = oTR.insertCell(0);
                    oTD.innerHTML = "<a href='#' onclick='deleteRows(this.parentNode)' title='删除当前行'>×</a>";
                    oTD.align = "center";
                    oTD.className = "A0111";
                    oTD.width = "40";
                    oTD.height = "20";
                    oTD.onmousedown = function () {
                        window.event.cancelBubble = true;
                    };

                    oTD = oTR.insertCell(1);
                    oTD.innerHTML = "" + tmpNum + "";
                    oTD.className = "A0101";
                    oTD.align = "center";
                    oTD.width = "40";

                    oTD = oTR.insertCell(2);
                    oTD.innerHTML = "<input name='Pid[" + tmpNum + "]' type='text' id='Pid" + tmpNum + "' size='6' value='" + FieldArray[0] + "' class='noLine' readonly>";
                    oTD.className = "A0101";
                    oTD.align = "center";
                    oTD.width = "60";


                    oTD = oTR.insertCell(3);
                    oTD.innerHTML = "" + FieldArray[1] + "<input name='PCName[" + tmpNum + "]' type='hidden' id='PCName" + tmpNum + "' value='" + FieldArray[1] + "'>";
                    oTD.className = "A0101";
                    oTD.width = "250";

                    oTD = oTR.insertCell(4);
                    oTD.innerHTML = "" + FieldArray[2] + "";
                    oTD.className = "A0101";
                    oTD.width = "250";

                    oTD = oTR.insertCell(5);
                    oTD.innerHTML = "<input name='Qty[" + tmpNum + "]' type='text' id='Qty" + tmpNum + "' size='10' value='1' class='numINPUTout' onChange='ChangeThis(" + tmpNum + ",\"Qty\")' onfocus='toTempValue(this.value)'>";
                    oTD.className = "A0101";
                    oTD.width = "100";

                    oTD = oTR.insertCell(6);
                    oTD.innerHTML = "<input name='ProductPrice[" + tmpNum + "]' type='text' id='ProductPrice" + tmpNum + "' size='5' value='" + FieldArray[3] + "' class='textINPUT' onChange='ChangeThis(" + tmpNum + ",\"ProductPrice\")' onfocus='toTempValue(this.value)'>";
                    oTD.align = "center";
                    oTD.className = "A0101";
                    oTD.width = "70";

                    oTD = oTR.insertCell(7);
                    oTD.innerHTML = "<input name='Amount[" + tmpNum + "]' type='text' id='Amount" + tmpNum + "' size='10' value='" + FieldArray[3] + "' class='totalINPUT' readonly>";
                    oTD.className = "A0101";
                    oTD.width = "";
                }
                else {
                    alert(Message);
                }
            }//end for
            return true;
        }
        else {
            //alert("没有选产品！");
            swal("没有选产品！");
            return false;
        }
    }
    else {
        //alert("没有选择客户!");
        swal("没有选择客户!");
        return false;
    }
}else {
        swal("没有填写PO！");
        return false;
    }
	}
function ChangeThis(Row,Keywords){
	//alert("Here1!!");
	var oldValue=document.form1.TempValue.value;//改变前的值
	//var Qtytemp=eval("document.form1.Qty"+Row+".value");//改变后的值
	var Qtytemp=document.getElementById("Qty"+Row+"").value*1;
	//var Pricetemp=eval("document.form1.ProductPrice"+Row+".value");//改变后的值
	var Pricetemp=document.getElementById("ProductPrice"+Row+"").value*1;
	
	//alert("Here2!!");
	if(Keywords=="Qty"){		
		//检查是否数字格式
		var Result=fucCheckNUM(Qtytemp,'');
		if(Result==0 || Qtytemp==0){
			//alert("输入了不正确的数量:"+Qtytemp+",重新输入!");
			swal("输入了不正确的数量:"+Qtytemp+",重新输入!");
			document.getElementById("Qty"+Row+"").value=oldValue;
			}
		else{
			document.getElementById("Amount"+Row+"").value=(Qtytemp*Pricetemp).toFixed(4);
			}
		}
	else{
		//检查是否价格格式
		var Result=fucCheckNUM(Pricetemp,'Price');
		if(Result==0){
			swal("输入不正确的售价:"+Pricetemp+",重新输入!");
			document.getElementById("ProductPrice"+Row+"").value=oldValue;
			}
		else{
			document.getElementById("ProductPrice"+Row+"").value=FormatNumber(Pricetemp,4);
			document.getElementById("Amount"+Row+"").value=(Pricetemp*Qtytemp).toFixed(4);	
			}
		}
	}

function CheckForm(){
	var Message="";
	var DateTemp=document.form1.OrderDate.value;//改变前的值
	var Clienttemp=	document.getElementById('CompanyId').value;
	if(Clienttemp==""){
		Message="没有选择客户！";
		}		
	if(DateTemp==""){
		Message="未填写日期！";
		}
	else{
		//检查是否存在产品订单明细，如果没有则提示，如果有则转保存存
		var Rowslength=ListTable.rows.length;//数组长度即领料记录数
		if(Rowslength==0){
			Message="没有产品订单明细，不能保存！";
			}
		}
	//检查上传的文件
	UploadStr=document.form1.ClientOrder.value;
	if(UploadStr!=""){
		UploadEx=UploadStr.substring(UploadStr.lastIndexOf("."));   
		if(UploadEx!=".rar" && UploadEx!=".zip" && UploadEx!=".xls" && UploadEx!=".doc" && UploadEx!=".eml" && UploadEx!=".jpg" && UploadEx!=".titf" && UploadEx!=".pdf"){
			Message="不允许的文件格式";
			NoteTable.rows[2].cells[2].innerHTML=" <input name='ClientOrder' type='file' id='ClientOrder' size='78' Row='2' Cel='2'>";//重写元素
			}
		}
	if(Message){
		swal(Message);
		return false;
		}
	else{
		/*var message=confirm("保存之前请确认订单数量是否正确，点取消可以返回修改。");
		if (message==true){
			document.form1.CompanyId.disabled=false;
			passvalue("Pid|PCName|Qty|ProductPrice|Amount");  //必须与上面隐藏传递元素id0号一致,Pid0
			document.form1.action="yw_order_save.php";
			document.form1.submit();
			}
		else{
			return false;
			}*/
			
			
			swal({   
		        title: "下客户订单",   
		        text: "保存之前请确认订单数量是否正确，点取消可以返回修改。？",   
		        type: "warning",   
		        showCancelButton: true,   
		        confirmButtonColor: "#DD6B55",   
		        confirmButtonText: "YES",   
		        cancelButtonText: "NO",   
		        closeOnConfirm: false
		    }, function(isConfirm){   
		          if (isConfirm) {     
		               document.form1.CompanyId.disabled=false;
						passvalue("Pid|PCName|Qty|ProductPrice|Amount");  //必须与上面隐藏传递元素id0号一致,Pid0
						document.form1.action="yw_order_save.php";
						document.form1.submit();
		           } 
		          
		    });
		}
	}
</script>

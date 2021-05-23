<style type="text/css">
<!--
.list{position:relative;color:#FF0000;}
.list span img{ /*CSS for enlarged image*/
border-width: 0;
padding: 2px; width:300px;
}
.list span{ 
position: absolute;
padding: 3px;
border: 1px solid gray;
visibility: hidden;
background-color:#FFFFFF;
}
.list:hover{
background-color:transparent;
}
.list:hover span{
visibility: visible;
top:0; left:35px;
}
-->
</style>
<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 上传发票更新");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_invoiceupdate";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 


$CheckRow = mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.nonbom6_invoice  WHERE cgMid ='$Mid'",$link_id));
$InvoiceFile = $CheckRow["InvoiceFile"];
$InvoiceNo= $CheckRow["InvoiceNo"];
$InvoiceRemark= $CheckRow["Remark"];
$InvoiceAmount= $CheckRow["InvoiceAmount"];
$InvoiceDate= $CheckRow["InvoiceDate"];

$InvoiceFilePath="../download/nonbom_cginvoice/" .$InvoiceFile;
if($InvoiceFile!=""){
     $noStatue="onMouseOver=\"window.status='none';return true\"";
     $InvoiceFileSTR="<span class='list' >已上传<span><img src='$InvoiceFilePath' $noStatue/></span></span>";
}
else{
    $InvoiceFileSTR="";
}


$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="ActionId,179,funFrom,$funFrom,From,$From,Estate,3,Pagination,$Pagination,Page,$Page,fromWebPage,$fromWebPage,chooseDate,$chooseDate,BuyerId,$BuyerId,uType,$CompanyId,Mid,$Mid";
//步骤4：//需处理

?>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
        
          <tr>
            <td width="100" align="right" scope="col">发票号码</td>
            <td scope="col"><input name="InvoiceNo" type="text" id="InvoiceNo" style="width:380px" value="<?php echo $InvoiceNo?>" dataType="Require"  msg="没有输入发票编号"></td>
          </tr>
          <tr>
            <td width="100" align="right" scope="col">发票金额</td>
            <td scope="col"><input name="InvoiceAmount" type="text" id="InvoiceAmount" style="width:380px" value="<?php echo $InvoiceAmount?>" dataType="Currency" msg="没有输入金额或金额格式不正确"></td>
          </tr>
           <tr>
           <td align="right">开票日期</td>
        <td><input name="InvoiceDate" type="text" id="InvoiceDate" style="width:380px" value="<?php echo $InvoiceDate?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd'})" dataType="Require"  msg="未填写" readonly>
         </td>
           </tr>
          
			<tr>
            <td align="right">发票文件</td>
            <td>
			<input name="InvoiceFile" type="file" id="InvoiceFile" size="74" title="可选项,pdf格式" DataType="Filter" Accept="pdf" Msg="文件格式不对,请重选" Row="2" Cel="1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $InvoiceFileSTR?>
			</td>
		</tr>
		<tr>
		<td width="100" align="right" scope="col">采购单<br><br>
			<input type='button' name='resetList' value='重选' onclick="removeLists()"/><br><br>
			<input type='button' name='deleteList' value='删除' onclick="deleteLists()"/>
		</td>
		<td valign="middle" scope="col">
		 	<select name="ListId[]" size="10" id="ListId" multiple style="width:380px"  ondblclick="NewSearchRecord('nonbom6_cg','<?php  echo $funFrom?>',2,4);" dataType="PreTerm" Msg="没有采购单" readonly  >
		 <?php 
		$result=mysql_query("SELECT V.cgMid,M.PurchaseID FROM $DataIn.nonbom6_invoice V 
		LEFT JOIN $DataIn.nonbom6_cgmain M ON M.Id = V.cgMid
		WHERE V.InvoiceFile='$InvoiceFile'  ",$link_id);
		$fkAmount = 0 ;					 
		 while ($cgRow= mysql_fetch_array($result)){
		   $cgMid=$cgRow["cgMid"];
		   $PurchaseID=$cgRow["PurchaseID"];
		   $InvoiceAmount=$cgRow["InvoiceAmount"];
		   $fkAmount+=$InvoiceAmount;
		   echo"<option value='$cgMid'>$cgMid  $PurchaseID</option>";
		 }
	     ?>
		 	</select>
		</td>
		<tr>
            <td width="100" align="right" scope="col">所选货款金额</td>
            <td scope="col"><input name="fkAmount" type="text" id="fkAmount" style="width:380px" value="<?php echo $fkAmount; ?>" readonly></td>
          </tr>
          
	  </tr>
		<tr>
            <td align="right" valign="top">备注</td>
            <td ><textarea name="Remark" id="Remark" cols="56" rows="5" title="可选项"><?php echo $InvoiceRemark; ?></textarea></td>
       </tr>
       
       <?php  if ($InvoiceFile!=""){ ?>
       <tr>
         <td colspan="2"><input name='DeleteSign' type='checkbox' id='DeleteSign' value='1'><LABEL for='Locks'>删除发票信息</LABEL></td>
         <input name="OldInvoiceFile" type="hidden" id="OldInvoiceFile" value="<?php echo $InvoiceFile?>">
          </tr>
        <?php  } ?>
        
</table>
	</td></tr></table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>

<script>

function listChanged(el)
{
	var length=el.options.length;
	var ids='';
	for (var i=0;i<length;i++){
		ids=ids==''?el.options[i].value:ids+','+el.options[i].value;
    }	
    var url="nonbom6_cg_ajax.php?Ids="+ids;
    var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
	        //alert(ajax.responseText);
	　　　	document.getElementById("fkAmount").value=ajax.responseText;
			}
		}
　	ajax.send(null);	
}

function removeLists(){
	var el=document.getElementById("ListId");
	var length=el.options.length;
	for (var i=length-1;i>=0;i--){
		el.options[i].remove();
    }  
    document.getElementById("fkAmount").value=0;
}

function deleteLists(){
	
   var el = document.getElementById("ListId");
   for(var i=0; i<el.options.length; i++){
      if(el.options[i].selected){
       el.options[i]=null;
	   i=i-1;
	  }
   }
	
}

function NewSearchRecord(tSearchPage,fSearchPage,SearchNum,Action){
	var r=Math.random();
        evt = event.srcElement ? event.srcElement : event.target;
	var theType=evt.type;
	var theName=evt.name;
	//var theType=event.srcElement.getAttribute('type');
	//var theName=event.srcElement.getAttribute('name');	
	switch(theType){
		
		case "select-multiple"://多选列表
		case "button"://按钮
			//其它参数：主要是类型限制
			var uTypeT=JobIdT=BranchIdT=KqSignT=MonthT=theYearT=ItemNameT="";		
			if(document.all("uType")!=null){
				uTypeT=document.getElementById('uType').value;
				uTypeT="&uType="+uTypeT;
			}
			
			if(document.all("Month")!=null){
				MonthT=document.getElementById('Month').value;
				MonthT="&Month="+MonthT;
			}

			var BackData=window.showModalDialog(tSearchPage+"_s1.php?r="+r+"&tSearchPage="+tSearchPage+"&fSearchPage="+fSearchPage+"&SearchNum="+SearchNum+"&Action="+Action+uTypeT+MonthT,"BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
			if(!BackData){  //专为safari设计的 ,add by zx 2011-05-04
		       if(document.getElementById('SafariReturnValue')){
			    //alert("return");
			   var SafariReturnValue=document.getElementById('SafariReturnValue');
			   BackData=SafariReturnValue.value;
			   SafariReturnValue.value="";
			  }
			 }
			//拆分BackData
			if(BackData){
				var The_Selectd = window.document.form1.ListId;
				var BL=BackData.split("``");
				var AddLength=The_Selectd.options.length;
				var fkAmount =document.getElementById('fkAmount').value*1;
				
				for(var i=0;i<BL.length;i++){
					var oldNum=0;
					var CL=BL[i].split("^^");
					for (loop=0;loop<AddLength;loop++){
						var oldTemp=The_Selectd.options[loop].value;
						if(CL[0]==oldTemp){
							oldNum=1;
							break;
							}
						}
					if(oldNum==1){
						alert("记录"+CL[1]+"已在列表,跳过继续！");
						}
					else{
						window.document.form1.ListId.options[document.form1.ListId.options.length]=new Option(CL[0]+' '+CL[1] ,CL[0]);
						fkAmount = fkAmount + (CL[2]*1);
					
						}
					}
					document.getElementById('fkAmount').value=fkAmount.toFixed(2);
				}
				
			break;
		}//switch(theType)
	}
</script>
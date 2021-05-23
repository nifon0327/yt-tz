<?php 
include "../model/modelhead.php";
$upDataMain="$DataIn.cw1_fkoutinvoice";
ChangeWtitle("$SubCompany 发票上传");
//include "subprogram/upmain_model.php";
$fromWebPage=$funFrom. '_' . $From;		
$nowWebPage =$funFrom."_upinvoice";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤3：
$fkAmount=0;$InvoiceNo='';$InvoiceAmount='';$optionSTR='';$InvoiceFileSTR='';
if ($Mid!=''){
	$strMid=explode('|', $Mid);
	$Mid = $strMid[0];
	$InvoiceId = $strMid[1];
	$InvoiceId=$InvoiceId==""?0:$InvoiceId;
}else{
	 $checkMid = mysql_query("SELECT S.Mid,S.InvoiceId   FROM $DataIn.cw1_fkoutsheet S WHERE S.Id='$Id' LIMIT 1",$link_id); 
	 if($midRows = mysql_fetch_array($checkMid)){
	     $Mid = $midRows['Mid'];
	     $InvoiceId = $midRows['InvoiceId'];
	 }
}

if ($Mid>0){//来自已结付
	$checkSheet=mysql_query("SELECT S.Id,S.StockId,S.Amount,D.StuffCname,S.CompanyId,S.Month,
	                          I.InvoiceNo,I.InvoiceDate,I.InvoiceAmount,I.InvoiceFile,I.Remark,I.Estate    
			                  FROM $DataIn.cw1_fkoutsheet S 
			                  LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			                  LEFT JOIN $DataIn.cw1_fkoutinvoice I ON I.Id=S.InvoiceId 
			                  WHERE S.Mid='$Mid' AND S.InvoiceId='$InvoiceId'",$link_id); 	                  
    while($checkRow = mysql_fetch_array($checkSheet)){
        $sId        = $checkRow['Id'];
        $iStockId   = $checkRow['StockId'];
        $iStuffCname= $checkRow['StuffCname'];
        $iAmount    = $checkRow['Amount'];
        $CompanyId  = $checkRow['CompanyId'];
	    $Month      = $checkRow['Month'];
	    $Remark    = $checkRow['Remark'];
	    
	    if ($InvoiceId>0 && $InvoiceNo==''){
	        $InvoiceNo     = $checkRow['InvoiceNo'];
		    $InvoiceAmount = $checkRow['InvoiceAmount'];
		    $InvoiceFile   = $checkRow['InvoiceFile'];
		    $InvoiceDate   = $checkRow['InvoiceDate'];
		    $InvoiceEstate = $upRow['Estate'];
		    
		    if (strlen($InvoiceFile)>4){
			    $InvoiceFileDir=anmaIn("download/fkinvoice/",$SinkOrder,$motherSTR);
				$InvoiceFile=anmaIn($InvoiceFile,$SinkOrder,$motherSTR);
				$InvoiceFileSTR="<a href=\"../admin/openorload.php?d=$InvoiceFileDir&f=$InvoiceFile&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>已上传</a>"; 
			}
	    }
	    
        $fkAmount+=$iAmount;
        $optionSTR.="<option value='$sId'>$iStockId $iStuffCname</option>";
    }          
}
else{
		$upResult = mysql_query("SELECT S.Id,S.StockId,S.Amount,S.CompanyId,IF(I.Id IS NULL,0,S.InvoiceId) AS InvoiceId,S.Month,D.StuffCname,
										I.InvoiceNo,I.InvoiceDate,I.InvoiceAmount,I.InvoiceFile,I.Remark,I.Estate    
		                           FROM $DataIn.cw1_fkoutsheet S
		                           LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
		                           LEFT JOIN $DataIn.cw1_fkoutinvoice I ON I.Id=S.InvoiceId 
		                           WHERE S.Id='$Id' GROUP BY S.InvoiceId",$link_id);                                              
		if($upRow = mysql_fetch_array($upResult)) {
		    $StockId   = $upRow['StockId'];
		    $sId       = $upRow['Id'];
		    $StuffCname= $upRow['StuffCname'];
		    $Amount    = $upRow['Amount'];
		    $InvoiceId = $upRow['InvoiceId'];
		    $Remark    = $upRow['Remark'];
		    $CompanyId = $upRow['CompanyId'];
		    $Month     = $upRow['Month'];
		    
		    $fkAmount+=$Amount;
			$optionSTR.="<option value='$sId'>$StockId $StuffCname</option>";
			
		    if ($InvoiceId>0 && strlen($upRow['InvoiceFile'])>4){
			    $InvoiceNo = $upRow['InvoiceNo'];
			    $InvoiceAmount = $upRow['InvoiceAmount'];
			    $InvoiceFile = $upRow['InvoiceFile'];
			    $InvoiceDate = $upRow['InvoiceDate'];
			    $InvoiceEstate = $upRow['Estate'];
			    
			    $InvoiceFileDir=anmaIn("download/fkinvoice/",$SinkOrder,$motherSTR);
			    $InvoiceFile=anmaIn($InvoiceFile,$SinkOrder,$motherSTR);
			    $InvoiceFileSTR="<a href=\"../admin/openorload.php?d=$InvoiceFileDir&f=$InvoiceFile&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>已上传</a>"; 
			    
			    $checkSheet=mysql_query("SELECT S.Id,S.StockId,S.Amount,D.StuffCname 
			                  FROM $DataIn.cw1_fkoutsheet S 
			                  LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			                  WHERE S.InvoiceId='$InvoiceId' AND S.StockId!='$StockId' ",$link_id); 
			                  
			    while($checkRow = mysql_fetch_array($checkSheet)){
			        $sId        = $checkRow['Id'];
			        $iStockId   = $checkRow['StockId'];
			        $iStuffCname= $checkRow['StuffCname'];
			        $iAmount    = $checkRow['Amount'];
			        $fkAmount+=$iAmount;
			        $optionSTR.="<option value='$sId'>$iStockId $iStuffCname</option>";
			    }            
			                  
		    }
		    
		}
}
$tableWidth=850;$tableMenuS=500;
$ActionId=$ActionId==""?178:$ActionId;

$SaveSTR=($From=='read' && $InvoiceEstate==1)?"NO":"";
include "../model/subprogram/add_model_t.php";
$Parameter="ActionId,$ActionId,Mid,$Mid,oldPayDate,$PayDate,cashSymbol,$cashSymbol,funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page,fromWebPage,$fromWebPage,Id,$Id,InvoiceId,$InvoiceId,uType,$CompanyId,CompanyId,$CompanyId,Month,$Month,chooseMonth,$chooseMonth";
//步骤4：//需处理
?>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
        
          <tr>
            <td width="100" align="right" scope="col">发票编号</td>
            <td scope="col"><input name="InvoiceNo" type="text" id="InvoiceNo" size="52" value="<?php echo $InvoiceNo; ?>" dataType="Require"  msg="没有输入发票编号"></td>
          </tr>
          <tr>
            <td width="100" align="right" scope="col">发票金额</td>
            <td scope="col"><input name="InvoiceAmount" type="text" id="InvoiceAmount" size="52" value="<?php echo $InvoiceAmount; ?>" dataType="Currency" msg="没有输入金额或金额格式不正确"></td>
          </tr>
           <tr>
           <td align="right">开票日期</td>
        <td><input name="InvoiceDate" type="text" id="InvoiceDate" style="width:380px" value="<?php  echo $InvoiceDate;?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd'})" dataType="Require"  msg="未填写" readonly>
         </td>
           </tr>
          
			<tr>
            <td align="right">发票文件</td>
            <td>
			<input name="InvoiceFile" type="file" id="InvoiceFile" size="74" title="可选项,pdf格式" DataType="Filter" Accept="pdf" Msg="文件格式不对,请重选" Row="2" Cel="1"> <?php echo $InvoiceFileSTR; ?>
			</td>
		</tr>
		<tr>
		<td width="100" align="right" scope="col">采购配件<br><br>
			<input type='button' name='resetList' value='重选' onclick="removeLists()"/><br><br>
			<input type='button' name='addList'   value='添加' onclick="NewSearchRecord('cw_fkout','<?php  echo $funFrom?>',2,4);"/><br>
		</td>
		<td valign="middle" scope="col">
		 	<select name="ListId[]" size="10" id="ListId" multiple style="width: 415px;" onclick="NewSearchRecord('cw_fkout','<?php  echo $funFrom?>',2,4);" dataType="PreTerm" Msg="没有配件名称" readonly   ><?php echo $optionSTR; ?>
		 	</select>
		</td>
	  </tr>
	  <tr>
            <td width="100" align="right" scope="col">所选货款金额</td>
            <td scope="col"><input name="fkAmount" type="text" id="fkAmount" size="52" value="<?php echo $fkAmount; ?>" readonly></td>
          </tr>
		<tr>
            <td align="right" valign="top">备注</td>
            <td ><textarea name="Remark" id="Remark" cols="56" rows="5" title="可选项"><?php echo $Remark; ?></textarea></td>
       </tr>
       <?php  if ($From!='read' || $InvoiceEstate==2){ ?>
       <tr>
         <td colspan="2"><input name='DeleteSign' type='checkbox' id='DeleteSign' value='1'><LABEL for='Locks'>删除发票信息</LABEL></td>
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
    //alert(ids);	
    var url="cw_fkout_ajax.php?Ids="+ids;
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
	for (var i=length-1;i>0;i--){
		el.options[i].remove();
    }
    listChanged(el);
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
			var sumAmount=document.getElementById("fkAmount").value*1.00;		
			if(document.all("uType")!=null){
				uTypeT=document.getElementById('uType').value;
				uTypeT="&uType="+uTypeT;
			}
			
			if(document.all("Month")!=null){
				MonthT=document.getElementById('Month').value;
				MonthT="&chooseMonth="+MonthT;
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
				for(var i=0;i<BL.length;i++){
					var oldNum=0;
					var CL=BL[i].split("^^");
					for (loop=0;loop<AddLength;loop++){
						var oldTemp=The_Selectd.options[loop].value;
						if(CL[3]==oldTemp){
							oldNum=1;
							break;
							}
						}
					if(oldNum==1){
						alert("记录"+CL[1]+"已在列表,跳过继续！");
						}
					else{
						window.document.form1.ListId.options[document.form1.ListId.options.length]=new Option(CL[0]+' '+CL[1] ,CL[3]);
						sumAmount=sumAmount+CL[2]*1.00;
						}
					}
				}
			document.getElementById("fkAmount").value=sumAmount.toFixed(2);
			break;
		}//switch(theType)
	}
</script>
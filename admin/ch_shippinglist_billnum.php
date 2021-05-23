<?php   
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 上传发票");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_billnum";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$MainResult = mysql_query("SELECT M.Id,M.CompanyId,M.InvoiceNO,O.Forshort
FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.trade_object  O ON O.CompanyId = M.CompanyId
WHERE M.Id='$Mid' LIMIT 1",$link_id);
if($MainRow = mysql_fetch_array($MainResult)) {
	$InvoiceNO=$MainRow["InvoiceNO"];
	$CompanyId=$MainRow["CompanyId"];
	$Forshort =$MainRow["Forshort"];
 }

$CheckMidResult = mysql_fetch_array(mysql_query("SELECT BillNum FROM $DataIn.ch1_shipfile WHERE ShipId=$Mid",$link_id));
$BillNum = $CheckMidResult["BillNum"];
$d1=anmaIn("download/billback/",$SinkOrder,$motherSTR);
$f1=anmaIn($CompanyId."_".$BillNum,$SinkOrder,$motherSTR);

if($BillNum!=""){
	$BillNumDownLoad="&nbsp;&nbsp;<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">$BillNum</a>&nbsp;&nbsp;&nbsp;&nbsp;<a style='color:#FF0000;' onclick='delfile(\"$CompanyId\",\"$BillNum\")'>删除</a>";
}



$BillResult = mysql_query("SELECT  M.Id,M.InvoiceNO
FROM $DataIn.ch1_shipfile F 
LEFT JOIN $DataIn.ch1_shipmain M ON M.Id = F.ShipId 
WHERE F.BillNum ='$BillNum' AND F.BillNum!=''",$link_id);

$Mids = $Mid;
$InvoiceNOs = $InvoiceNO;
while($BillRow = mysql_fetch_array($BillResult)){
    $tempMid = $BillRow["Id"];
    $tempInvoiceNO = $BillRow["InvoiceNO"];
	$Mids = $Mids==""?$tempMid:$Mids."^^".$tempMid;
	$InvoiceNOs = $InvoiceNOs ==""?$tempInvoiceNO:$InvoiceNOs."^^".$tempInvoiceNO;
	
}

$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="ActionId,803,Mid,$Mid,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseDate,$chooseDate,CompanyId,$CompanyId";

//步骤4：//需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
        <tr>
            <td  height="25"  align="right" scope="col">客户:</td>
            <td scope="col"><?php    echo $Forshort?></td>
		</tr>
		<tr>
            <td  height="25"  align="right" scope="col">Invoice名称:</td>
            <td scope="col"><textarea  id="InvoiceNOs" name="InvoiceNOs" cols="52" rows="3" onclick="getMoreInvoice(<?php echo $Mid?>)"  readonly><?php echo $InvoiceNOs?></textarea>
            <input id="Mids" name="Mids" type="hidden" value="<?php echo $Mids?>">
            </td>
		</tr>
       
         <tr>
		  <td  align="right"  >发票号:</td>
		  <td > <input  type="text" id="BillNum" name="BillNum" value="<?php echo $BillNum?>" style="width:380px"  dataType="Require"></td>
  		</tr>   
	 <tr>
		  <td align="right" >发票单:</td>
		  <td scope="col"><input name="Attached" type="file" id="Attached" size="52" DataType="Filter" Accept="pdf" Msg="文件格式不对,请重选"><?php echo $BillNumDownLoad?></td>
	    </tr>                 
          
</table>
	</td></tr></table>
<?php   
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>
<script>
function getMoreInvoice(Mid){
	var Bid=document.getElementById('CompanyId').value;
	var InvoiceNOs = document.getElementById("InvoiceNOs").value;
	var Mids = document.getElementById("Mids").value;
	var NewInvoiceNOs ="";
	var NewMids = "";
	var num=Math.random();  
	var BackData=window.showModalDialog("../public/cw_orderin_s1.php?r="+num+"&Bid="+Bid+"&Jid="+Mids+"&tSearchPage=cw_orderin&fSearchPage=ch_shippinglist&SearchNum=2&Action=7","BackData","dialogHeight =500px;dialogWidth=950px;center=yes;scroll=yes");
	if (BackData){
  		var Rowstemp=BackData.split("``");
		var Rowslength=Rowstemp.length;
		for(var i=0;i<Rowslength;i++){
			var Message="";			
			var FieldArray=Rowstemp[i].split("^^");
			   if(NewMids==""){
				   NewMids = Mids +"^^"+FieldArray[0];
			   }else{
				   NewMids = NewMids+"^^"+FieldArray[0];
			   }
			   
			   if(NewInvoiceNOs==""){
				   NewInvoiceNOs = InvoiceNOs +"^^"+FieldArray[1];
			   }else{
				   NewInvoiceNOs = NewInvoiceNOs+"^^"+FieldArray[1];
			   }
			
			}//for(var i=0;i<Rowslength;i++)
			if(NewMids!=""){
			   
				document.getElementById("Mids").value = NewMids;
			    document.getElementById("InvoiceNOs").value = NewInvoiceNOs;
			}
			
		}//if (BackData)
	else{
		alert("没有选取数据!");return true;
		}
	}
	
function  delfile (CompanyId,BillNum){
	if(confirm("确定要删除发票么?")){
	    var url="ch_shippinglist_delbill_ajax.php?CompanyId="+CompanyId+"&BillNum="+BillNum+"&ActionId=billnum";
		var ajax=InitAjax();
	　	ajax.open("GET",url,true);
		ajax.onreadystatechange =function(){
		　　if(ajax.readyState==4 && ajax.status ==200){
		           if(ajax.responseText =="Y"){
		               alert("删除成功!");
		           }
				}
			}
	　	ajax.send(null);
	}
}
</script>
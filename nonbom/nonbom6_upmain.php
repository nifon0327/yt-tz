<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 更新采购主单");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_upmain";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤3：//需处理
$upData=mysql_fetch_array(mysql_query("SELECT A.mainType,A.CompanyId,A.BuyerId,A.Date,A.PurchaseID,A.Remark,A.Operator,B.Name,SUM(C.Qty*C.Price) AS hkAmount,D.yqHK,A.Attached 
	FROM $DataIn.nonbom6_cgmain A
	LEFT JOIN $DataPublic.staffmain B ON B.Number=A.BuyerId
	LEFT JOIN $DataIn.nonbom6_cgsheet C ON C.Mid=A.Id
	LEFT JOIN (SELECT cgMid,IFNULL(SUM(hkAmount),0) AS yqHK FROM $DataIn.nonbom11_qksheet WHERE cgMid='$Mid') D ON D.cgMid=A.Id
	WHERE A.Id='$Mid' GROUP BY A.Id ORDER BY A.Id DESC",$link_id));
$mainType=$upData["mainType"];
$CompanyId=$upData["CompanyId"];
$PurchaseID=$upData["PurchaseID"];
$Date=$upData["Date"];
$hkAmount=$upData["hkAmount"];
$Remark=$upData["Remark"];
$Name=$upData["Name"];
$BuyerId=$upData["BuyerId"];
$yqHK=$upData["yqHK"];
$Attached=$upData["Attached"];
$wqHK=$hkAmount-$yqHK;

$contractFileSTR="";
if($Attached==1){
            $contractFile = $Mid . '.pdf';
			$Dir=anmaIn("download/nonbom_contract/",$SinkOrder,$motherSTR);
			$contractFile=anmaIn($contractFile,$SinkOrder,$motherSTR);
			$contractFileSTR="<span onClick='OpenOrLoad(\"$Dir\",\"$contractFile\")' style='CURSOR: pointer;color:#FF6633'>已上传</span>";
 }


if($wqHK==0 ){
	$SaveSTR="NO";
	$LockSTR="<span class='redB'>(已全部请款，不允许更新操作)</span>";
	}
	
$CheckFormURL="thisPage";
$CustomFun="<span onclick='AddGoods(\"$CompanyId\")' $onClickCSS>新加</span>&nbsp;";
//步骤4：
$tableWidth=930;$tableMenuS=600;
include "../model/subprogram/add_model_t.php";
$Parameter="Mid,$Mid,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ActionId,99,CompanyId,$CompanyId,mainType,$mainType,chooseDate,$chooseDate";
//步骤5：//需处理
 ?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
	  <td class="A0011">
      <table width="850" height="140" border="0" align="center" cellspacing="5"  id="NoteTable">

       <tr>
           <td width="152" height="22" align="right">采购单号</td>
           <td><?php echo $PurchaseID;?></td>
         </tr>
           <tr>
			<td width="152" height="22" valign="middle" scope="col" align="right">默认采购</td>
			<td valign="middle" scope="col"><?php echo $Name;?></td>
		</tr>
        <tr>
			<td width="152" height="22" valign="middle" scope="col" align="right">下单日期</td>
			<td valign="middle" scope="col"><INPUT name="Date" class=textfield id="Date" value="<?php echo $Date?>" style="width: 400px;" onfocus="WdatePicker()" datatype="Require" msg="未填写" readonly></td>
		</tr>
		<tr>
          <td height="22" valign="top" scope="col" align="right">备注</td>
          <td valign="middle" scope="col"><textarea name="Remark" rows="3" id="Remark" style="width: 400px;color: #009900;"><?php  echo $Remark;?></textarea></td>
        </tr>
        <tr>
          <td height="22" valign="top" scope="col" align="right">购买合同</td>
          <td valign="middle" scope="col">
          <input name="Attached" type="file" id="Attached" style="width: 400px;" title="可选项,pdf格式" DataType="Filter" Accept="pdf" Msg="文件格式不对,请重选" Row="5" Cel="1">
           <?php echo $contractFileSTR; if ($SaveSTR=='NO'){ ?>
	             <input type='button' value='上传合同' onclick='uploadFile()'></td>
           <?php } ?>
          </td>
        </tr>
        <tr>
          <td height="22" valign="top" scope="col" align="right">&nbsp;</td>
          <td valign="middle" scope="col">&nbsp;<?php echo $LockSTR;?></td>
        </tr>
      </table>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
   <tr><td width="30"  bgcolor="#FFFFFF" >&nbsp;</td><td colspan="7" valign="bottom"><span class='redB'>1.已采购配件明细</span></td><td width="30"  bgcolor="#FFFFFF">&nbsp;</td></tr>
<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="30"  bgcolor="#FFFFFF" height="25">&nbsp;</td>
		 <td class="A1111" width="40" align="center">序号</td>
          <td class="A1101" width="330" align="center">配件名称</td>
          <td class="A1101" width="60" align="center">单价</td>
          <td class="A1101" width="60" align="center">申购数量</td>
          <td class="A1101" width="40" align="center">单位</td>
          <td class="A1101" width="60" align="center">金额</td>
          <td class="A1101" width="280" align="center">申购备注</td>
		<td width="30"  bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td width="30"  height="120">&nbsp;</td>
		<td colspan="7" align="center" class="A0111" id="ShowInfo">	
                    <div style='width:870;height:100%;overflow-x:hidden;overflow-y:scroll'>
                   <table width='100%' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' id='NoteTable'>
                   <?php
                     $sListResult = mysql_query("SELECT   F.Id,F.GoodsId,F.Qty,F.Price,F.Remark,D.GoodsName,D.Unit
                      FROM $DataIn.nonbom6_cgsheet F 
		              LEFT JOIN $DataPublic.nonbom4_goodsdata D ON D.GoodsId=F.GoodsId 
                     WHERE  F.Mid='$Mid'",$link_id);
                        $i=1;
                        while($ListRows= mysql_fetch_array($sListResult)){
                                               $GoodsName=$ListRows["GoodsName"];
                                               $GoodsId=$ListRows["GoodsId"];
                                               $Qty=$ListRows["Qty"];
                                               $Price=$ListRows["Price"];
                                               $Remark=$ListRows["Remark"];
                                               $Unit=$ListRows["Unit"];$Amount=$Price*$Qty;
                                               echo"<tr bgcolor='$theDefaultColor'>";
                                               echo"<td  align='center' width='38' class='A0101' height='30'>$i</td>";
                                               echo"<td  width='330' class='A0101'>$GoodsName</td>";
                                               echo"<td  align='right' width='60' class='A0101'>$Price</td>";
                                               echo"<td   align='right' width='60' class='A0101'>$Qty</td>";
                                               echo"<td  align='center' width='40' class='A0101'>$Unit</td>";
                                               echo"<td align='right'  width='60' class='A0101'>$Amount</td>";
                                               echo"<td  width='277' class='A0100'>$Remark</td>";
                                               echo"</tr>";
                                       $i++;
                                  }
                     ?>
                 </table></div>
		</td>
		<td width="30" >&nbsp;</td>
	</tr>
</table>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
   <tr><td width="30"  bgcolor="#FFFFFF" height="30">&nbsp;</td><td colspan="7" valign="bottom"><span class='redB'>2.新加采购配件明细</span></td><td width="30"  bgcolor="#FFFFFF">&nbsp;</td></tr>
<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="30"  bgcolor="#FFFFFF" height="25">&nbsp;</td>
		 <td class="A1111" width="40" align="center">操作</td>
		 <td class="A1101" width="30" align="center">序号</td>
          <td class="A1101" width="60" align="center">采购Id</td>
          <td class="A1101" width="290" align="center">配件名称</td>
          <td class="A1101" width="70" align="center">单价</td>
          <td class="A1101" width="70" align="center">申购数量</td>
          <td class="A1101" width="40" align="center">单位</td>
          <td class="A1101" width="80" align="center">金额</td>
          <td class="A1101" width="180" align="center">申购备注</td>
		<td width="30"  bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td width="30"  height="180">&nbsp;</td>
		<td colspan="9" align="center" class="A0111">	
                    <div style='width:870;height:100%;overflow-x:hidden;overflow-y:scroll'>
                   <table width='100%' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' id='ListTable'>
                 </table></div>
		</td>
		<td width="30" >&nbsp;</td>
	</tr>
</table>
</td></tr></table>
<input type="hidden" id="SIdList" name="SIdList">
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function uploadFile(){
    var fnUpload=document.getElementById('Attached');
    var filename = fnUpload.value; 
	var mime = filename.toLowerCase().substr(filename.lastIndexOf(".")); 
	if(mime!=".pdf") 
	{ 
	    alert("请选择pdf格式的文件上传"); 
	    fnUpload.value=""; 
	    return;
	}

    document.getElementById('ActionId').value='upFiles';
	document.form1.action="nonbom6_updated.php";
	document.form1.submit();
}

/*function chkAmount(AmountFrom){
	var Message="";
	switch(AmountFrom){
		case 1://货款
			var wqHK_TEMP=document.form1.wqHK.value*1;	//可请款最大值
			var qkHK_TEMP=document.form1.qkHK.value*1;	//当前请款值
			//先检查
			var CheckSTR=fucCheckNUM(qkHK_TEMP,"Price");
			//alert(CheckSTR+"-*-"+qkHK_TEMP+"-*-"+wqHK_TEMP);
			if(CheckSTR==0 || qkHK_TEMP>wqHK_TEMP){
				Message="不是规范或不允许的值！"+qkHK_TEMP+" "+wqHK_TEMP;
				}
			if(Message!=""){
				alert(Message);
				document.form1.qkHK.value=wqHK_TEMP;
				return false;
				}
		break;
		
		case 2://增值税
			var wqTax_TEMP=document.form1.wqTax.value;	//可请款最大值
			var qkTax_TEMP=document.form1.qkTax.value;	//当前请款值
			//先检查
			var CheckSTR=fucCheckNUM(qkTax_TEMP,"Price");
			if(CheckSTR==0 || qkTax_TEMP>wqTax_TEMP){
				Message="不是规范或不允许的值！";
				}
			if(Message!=""){
				alert(Message);
				document.form1.qkTax.value=wqTax_TEMP;
				return false;
				}
		break;
		case 3://运费
			var wqShip_TEMP=document.form1.wqShip.value;	//可请款最大值
			var qkShip_TEMP=document.form1.qkShip.value;	//当前请款值
			//先检查
			var CheckSTR=fucCheckNUM(qkShip_TEMP,"Price");
			if(CheckSTR==0 || qkShip_TEMP>wqShip_TEMP){
				Message="不是规范或不允许的值！";
				}
			if(Message!=""){
				alert(Message);
				document.form1.qkShip.value=wqShip_TEMP;
				return false;
				}
		break;
		
		}
		
	}*/


function  CheckForm(){
           var DataSTR="";
	    	for(i=0;i<ListTable.rows.length;i++){
		    	if(DataSTR==""){
			    	    DataSTR=ListTable.rows[i].cells[2].innerText;
			    	}
		    	else{
			          	DataSTR=DataSTR+"|"+ListTable.rows[i].cells[2].innerText;
			    	   }
		        	}
		   document.form1.SIdList.value=DataSTR;
		   document.form1.action="nonbom6_updated.php";
		   document.form1.submit();

}

function AddGoods(CompanyId){
   document.getElementById('SafariReturnValue').value="";
	var num=Math.random();  
	BackData=window.showModalDialog("nonbom6_s1.php?r="+num+"&tSearchPage=nonbom6&fSearchPage=nonbom6&SearchNum=2&Action=1"+"&Jid="+CompanyId,"BackData","dialogHeight =550px;dialogWidth=980px;center=yes;scroll=yes");
	
		if(!BackData){  //专为safari设计的 ,add by zx 2011-05-04
		if(document.getElementById('SafariReturnValue')){
			//alert("return");
			var SafariReturnValue=document.getElementById('SafariReturnValue');
			BackData=SafariReturnValue.value;
			SafariReturnValue.value="";
			}
		}	
	//拆分
	if(BackData){
  		var Rows=BackData.split("``");//分拆记录
		var Rowslength=Rows.length;//数组长度
		
		if(document.getElementById("TempMaxNumber")){  
			var TempMaxNumber=document.getElementById("TempMaxNumber");
			TempMaxNumber.value=TempMaxNumber.value*1+Rowslength*1;
		}
			  

		for(var i=0;i<Rowslength;i++){
			var Message="";
			var FieldTemp=Rows[i];		//拆分后的记录
			var FieldArray=FieldTemp.split("^^");//分拆记录中的字段
			//过滤相同的配件ID号
			for(var j=0;j<ListTable.rows.length;j++){						
				var SIdtemp=ListTable.rows[j].cells[2].innerText;				
				if(FieldArray[0]==SIdtemp){//如果流水号存在
					Message="采购配件Id: "+FieldArray[0]+" 已存在!跳过继续！";
					break;
					}
				}			
			if(Message==""){
				oTR=ListTable.insertRow(ListTable.rows.length);
				
				//表格行数
				tmpNumQty=oTR.rowIndex;
				tmpNum=oTR.rowIndex+1;
				
				//第1列:序号
				oTD=oTR.insertCell(0);
				oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="40";
				oTD.height="25";

				//第2列:
				oTD=oTR.insertCell(1);
				oTD.innerHTML=""+tmpNum+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="30";

				//第2列:
				oTD=oTR.insertCell(2);
				oTD.innerHTML=""+FieldArray[0]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="60";
				//第3列
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[1]+"";
				oTD.className ="A0101";
				oTD.width="290";
 


				oTD=oTR.insertCell(4); 
				oTD.innerHTML=""+FieldArray[2]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="69";	
				//第5列
				oTD=oTR.insertCell(5); 
				oTD.innerHTML=""+FieldArray[3]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";	

				oTD=oTR.insertCell(6); 
				oTD.innerHTML=""+FieldArray[4]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="40";	

				oTD=oTR.insertCell(7); 
				oTD.innerHTML=""+FieldArray[5]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="80";	

				oTD=oTR.insertCell(8); 
				oTD.innerHTML=""+FieldArray[6]+"";
				oTD.className ="A0100";
				oTD.width="179";	


				}
			else{
				alert(Message);
				}
			}//end for
			return true;
		}
	}


function deleteRow(rowIndex){
	            ListTable.deleteRow(rowIndex);
	            ShowSequence(ListTable);
	}

function ShowSequence(TableTemp){
	for(i=0;i<TableTemp.rows.length;i++){ 
        var k=i+1;
		TableTemp.rows[i].cells[1].innerText=k;//
		var j=i-1;
		}
	}  

</script>

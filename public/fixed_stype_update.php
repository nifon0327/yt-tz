<?php 
//电信-joseph
//代码、数据共享-EWEN 2012-08-15
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新固定资产分类资料");//需处理
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤3：
$tableWidth=600;$tableMenuS=300;
$CustomFun="<span onClick='ViewStuffId(7)' $onClickCSS>新的新子分类</span>&nbsp;";//自定义功能
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
$upResult = mysql_query("SELECT * FROM $DataPublic.oa1_fixedmaintype WHERE Id='$Id'",$link_id);
if ($upData = mysql_fetch_array($upResult)) {
	$Name=$upData["Name"];
	}
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,Id,$Id,ActionId,$ActionId,POrderId,$POrderId";
?>
	<input name="SafariReturnQty" id="SafariReturnQty" type="hidden" value="0"> 
    <table width='<?php  echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
		<tr>
			<td width="10" class="A0010">&nbsp;</td>
			<td height="25"  valign="bottom"><span class="redB">◆主分类资料</div></td>
			<td height="22"  align="right"><span class="redB">本页操作请谨慎</div></td>
			<td width="10" class="A0001">&nbsp;</td>
		</tr>
		<tr class="">
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
            <td scope="col" align="right">分类名称</td>
            <td scope="col">
              <input name="Name" type="text" id="Name" size="60" maxlength="20" value="<?php  echo $Name?>" title="可输入1-20个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="20" Min="1" Msg="没有填写或字符超出20字节"> 
			</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>

		<tr>
			<td width="10" class="A0010">&nbsp;</td>
			<td height="25" colspan="2" valign="bottom"><span class="redB">◆已有子分类明细</span></td>
			<td width="10" class="A0001">&nbsp;</td>
		</tr>
	</table>
	<table width='<?php  echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
         <tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="10" class="A0010" >&nbsp;</td>
		<td align="center" class="A1111">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:auto">
                    
                <table width='100%' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" >
                  <tr>  
                    <td width="30" class="A0001" align="center">操作</td>
                    <td width="30" class="A0001" align="center">序号</td>
                    <td width="70" class="A0001" align="center">子分类ID</td>
                    <td width="170" class="A0001" align="center">子分类名称</td>
                    <td width="90" class="A0001" align="center">日期</td>
                    <td width="60" class="A0001" align="center">状态</td>
                    <td width="" class="A0000" align="center">操作者</td>
                  </tr>         
                </table>
            </div>		
            </td>
            <td width="10" class="A0001">&nbsp;</td>
        </tr>               
		
		<tr>
		<td width="10" class="A0010" >&nbsp;</td>
		<td align="center" class="A0010">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='100%' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id='StuffList'>
		<?php 
		//子分类列表
		$StockResult = mysql_query("SELECT * FROM $DataPublic.oa2_fixedsubtype
where 1 AND MainTypeId='$Id'",$link_id);
		if($StockRows = mysql_fetch_array($StockResult)){
			$i=1;
			do{
				
				$SubId=$StockRows["Id"];
				$SubName=$StockRows["Name"];
				$SubNameondblclick="ondblclick=UpdateRow(this,StuffList,'SubName')";  //
				$Date=$StockRows["Date"];
				$Estate=$StockRows["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
				$Operator=$StockRows["Operator"];
				include "../model/subprogram/staffname.php";
				$Locks=$StockRows["Locks"];	
				

				echo"<tr><td width='30' class='A0101' align='center'>";
				//不能删除需求单的情况：有领料记录
				$outResult = mysql_query("SELECT Id FROM $DataPublic.fixed_assetsdata WHERE TypeId='$SubId'",$link_id);
				//$outResult = mysql_query("SELECT Id FROM $DataPublic.fixed_assetsdata WHERE TypeId='9999'",$link_id);
				if($outRows = mysql_fetch_array($outResult)){
					echo"&nbsp;";//有固定资产记录
					}
				else{			
					if($Estate!=4){	
						echo"<a href='#' onclick='deleteRow(this.parentNode,StuffList)' title='删除此子分类'>×</a>";
						}
					else{
						echo"&nbsp;$Estate";
						}
					}
				echo"<td width='30' class='A0101' align='center'>$i</td>";
				echo"</td><td width='70' class='A0101' align='center'>$SubId</td>
					<td width='170' class='A0101' $SubNameondblclick title='双击更改'>$SubName</td>
					<td width='90' class='A0101' align='center'>$Date</td>
					<td width='60' class='A0101' align='center'>$Estate</td>
					<td width='' class='A0100'>$Operator</td>
					</tr>";
				$i++;
				}while($StockRows = mysql_fetch_array($StockResult));
			}
		?>
			</table>
		</div>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
	</table>
	<input name="hfield" type="hidden" id="hfield" value="0">
<table width='<?php  echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
        <td width="10" class="A0010" height="25">&nbsp;</td>
        <td valign="bottom" ><span class="redB">◆新增子分类</span></td>
        <td width="10" class="A0001">&nbsp;</td>
    </tr>    
	<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="10" class="A0010" >&nbsp;</td>
		<td  align="center" class="A1111">
		<div style="width:100%;height:100%; overflow-x:hidden;overflow-y:auto">
			<table width='100%' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" >
              <tr>  
                    <td width="30" class="A0001" align="center">操作</td>
                    <td width="30" class="A0001" align="center">序号</td>
                    <td width="" class="A0000" align="center">子分类名称</td>

              </tr>         
			</table>
		</div>		
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" class="A0010" height="300">&nbsp;</td>
		<td align="center" class="A0110">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='100%' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTable">
			</table>
		</div>		
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
</table>
<input name="SubName0" id="SubName0" type="hidden" value="">
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script LANGUAGE='JavaScript'  type="text/JavaScript">
function ViewStuffId(Action){
	var Message="";
	var num=Math.random(); 
	 var BackData=prompt("请输入新的子分类名称","");
	 if(BackData==""){ 
	 	return false;		
	}
	if(BackData==null || BackData==''){  //专为safari设计的 ,add by zx 2011-05-04
		if(document.getElementById('SafariReturnValue')){
		var SafariReturnValue=document.getElementById('SafariReturnValue');
		BackData=SafariReturnValue.value;
		SafariReturnValue.value="";
		}
	}	
	//拆分
	if(BackData){
  		var Rows=BackData.split("``");//分拆记录:	
		var Rowslength=Rows.length;//数组长度即领料记录数
		
					//加入如下的代码****************************************
		if(document.getElementById("TempMaxNumber")){  ////给add by zx 2011-05-05 firfox and  safari不能用javascript生成的元素
			var TempMaxNumber=document.getElementById("TempMaxNumber");
			TempMaxNumber.value=TempMaxNumber.value*1+Rowslength*1;
		}	
			
		for(var i=0;i<Rowslength;i++){
			var Message="";
			var FieldTemp=Rows[i];		//拆分后的记录
			var FieldArray=FieldTemp.split("^^");//分拆记录中的字段：0配件ID|1配件名称|2配件价格|3可用库存|4采购|5供应商
			//过滤相同的配件ID号
			for(var j=0;j<ListTable.rows.length;j++){						
				var tempName=ListTable.rows[j].cells[2].innerHTML;		
				//alert (tempName);
				if(FieldArray[0]==tempName){//如果ID号存在
					Message="分类: "+FieldArray[0]+" 已存在!跳过继续！";
					break;
					}
			}
			
			for(var j=0;j<StuffList.rows.length;j++){						
				var tempName=StuffList.rows[j].cells[3].innerHTML;		
				//alert (tempName);
				if(FieldArray[0]==tempName){//如果ID号存在
					Message="分类: "+FieldArray[0]+" 已存在!跳过继续！";
					break;
					}
			}	
			
			if(Message==""){
				var returnValue=1;
				if (returnValue){
					oTR=ListTable.insertRow(ListTable.rows.length);				
					//表格行数
					tmpNum=oTR.rowIndex+1;
					
					//第1列:序号
					oTD=oTR.insertCell(0);
					oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode,ListTable)' title='删除当前行'>×</a>";
					oTD.className ="A0100";
					oTD.align="center";
					oTD.width="30";
					oTD.onmousedown=function(){
						window.event.cancelBubble=true;};
					
					//第2列:操作
					oTD=oTR.insertCell(1);
					oTD.innerHTML="<input name='SubName["+tmpNum+"]' type='hidden' id='SubName"+tmpNum+"' size='10' value='"+FieldArray[0]+"'>"+tmpNum+"";
					oTD.className ="A0111";
					oTD.align="center";
					oTD.width="30";
					
					//第3列:流水号
					oTD=oTR.insertCell(2);
					oTD.innerHTML=""+FieldArray[0]+"";
					oTD.className ="A0100";
					oTD.align="center";
					oTD.width="";
					

					form1.hfield.value=tmpNum;
					}
				}
			else{
				alert(Message);
				}
			}//end for
			return true;
		}
	else{
		alert("没有输入子分类！");
		return false;
		}
	}
	
function ShowSequence(TableTemp){
	if(TableTemp==ListTable){		
		var SIDTemp=document.form1.oldMaxSID.value;	//原有需求单最大值
		document.form1.MaxSID.value=SIDTemp;		//最大值重新初始化
		}
	for(i=0;i<TableTemp.rows.length;i++){ 
		var j=i+1;
  		TableTemp.rows[i].cells[1].innerText=j; 
		if(TableTemp==ListTable){//新增需求单列表
			TableTemp.rows[i].cells[2].innerText=(document.form1.MaxSID.value)*1+1;
			document.form1.MaxSID.value=SIDTemp=(document.form1.MaxSID.value)*1+1;
			}
		}
		
  }  

function deleteRow (RowTemp,TableTemp){
	//var rowIndex=RowTemp.parentElement.rowIndex;
	
	if(RowTemp.parentElement==null || RowTemp.parentElement=="undefined" ){  // add by zx 2011-05-06 Firfox不支持 parentElement
		var rowIndex=RowTemp.parentNode.rowIndex;
	}
	else{
		var rowIndex=RowTemp.parentElement.rowIndex;
	}	
	
	if(TableTemp==ListTable){	//新增需求单列表
		TableTemp.deleteRow(rowIndex);
		ShowSequence(TableTemp);
		}
	else{//处理原需求单删除，删除成功后再删除行		
		var delSubName=TableTemp.rows[rowIndex].cells[3].innerHTML;
		var message=confirm("确定要删除："+delSubName+"吗？");
		if (message==true){
			//输入删除原因
	
				//var delStockId=TableTemp.rows[rowIndex].cells[3].innerText;
				//var delStockId=TableTemp.rows[rowIndex].cells[3].innerHTML;
				delSubName=encodeURIComponent(delSubName);
				myurl="fixed_stype_updated.php?SubName="+delSubName+"&ActionId=delSubName";
				/*
				retCode=openUrl(myurl);
				if (retCode!=-2){//标记删除成功，不直接删除需求单，而是做标记
					//TableTemp.deleteRow(rowIndex);
					//ShowSequence(TableTemp);
					TableTemp.rows[rowIndex].cells[0].innerHTML="&nbsp;";
					}
					
					else{
						alert("标记删除失败！");return false;
						}
					}					
				*/
				var delinfo="";
				TableTemp.rows[rowIndex].cells[0].innerHTML="<div style='background-color:#FF0000'  >.删.</div>"; 
				var ajax=InitAjax(); 
				ajax.open("GET",myurl,true);
				//alert(myurl);
				ajax.onreadystatechange =function(){
					if(ajax.readyState==4){// && ajax.status ==200		
						TableTemp.rows[rowIndex].cells[0].innerHTML="&nbsp;";
					}	
					else{

						  //alert("标记删除失败！");return false;
						}
				}
				ajax.send(null); 

			}
		else{
			return false;
			}			
		}	
	}
	
function UpdateRow(RowTemp,TableTemp,SType){  //SType暂时没用，如果更新不同字段就用它来区别
	//var rowIndex=RowTemp.parentElement.rowIndex;
	//alert(RowTemp);
	if(RowTemp.parentElement==null || RowTemp.parentElement=="undefined" ){  // add by zx 2011-05-06 Firfox不支持 parentElement
		var rowIndex=RowTemp.parentNode.rowIndex;
	}
	else{
		var rowIndex=RowTemp.parentElement.rowIndex;
	}	
	
	if(TableTemp==ListTable){	//新增需求单列表
		//TableTemp.deleteRow(rowIndex);
		//ShowSequence(TableTemp);
		}
	else{//处理原需求单删除，删除成功后再删除行
		//alert (rowIndex);
		var SubName=TableTemp.rows[rowIndex].cells[3].innerHTML;
		var SubId=TableTemp.rows[rowIndex].cells[2].innerHTML;
		var BackData=prompt("请输需要更新子分类名称",SubName);
		//if ((BackData!=null) &&  (BackData!="")  && (BackData!=SubName)){
		if ((BackData!=null) &&  (BackData!="") ){
			//输入删除原因
	
				//var delStockId=TableTemp.rows[rowIndex].cells[3].innerText;
				//var delStockId=TableTemp.rows[rowIndex].cells[3].innerHTML;
				var tempBackData=BackData;
				BackData=encodeURIComponent(BackData);
				myurl="fixed_stype_updated.php?SubName="+BackData+"&Id="+SubId+"&ActionId=UpdateSubName";
				var delinfo="";
				//TableTemp.rows[rowIndex].cells[3].innerHTML=; 
				var ajax=InitAjax(); 
				ajax.open("GET",myurl,true);
				//alert(myurl);
				ajax.onreadystatechange =function(){
					if(ajax.readyState==4){// && ajax.status ==200		
						TableTemp.rows[rowIndex].cells[3].innerHTML=tempBackData;
					}	
					else{

						  //alert("标记删除失败！");return false;
						}
				}
				ajax.send(null); 

			}
		else{
			return false;
			}			
		}	
	}
	
function CheckForm(){
	passvalue("SubName");  //add by zx 2011-05-05 必须与上面隐藏传递元素id0号一致,Pid0
	document.form1.action="fixed_stype_updated.php";
	document.form1.submit();
}
</script>

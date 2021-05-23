<?php 
//已更新
//电信-joseph
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新设备维护资料项目");//需处理
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤3：
$tableWidth=700;$tableMenuS=300;
//$CustomFun="<span onClick='ViewStuffId(7)' $onClickCSS>添加设备维护资料项目</span>&nbsp;";//自定义功能
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
$upResult = mysql_query("SELECT * FROM $DataPublic.oa2_fixedsubtype WHERE Id='$Id'",$link_id);
if ($upData = mysql_fetch_array($upResult)) {
	$Name=$upData["Name"];
	}
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,Id,$Id,ActionId,$ActionId,POrderId,$POrderId";
?>
	<input name="SafariReturnQty" id="SafariReturnQty" type="hidden" value="0"> 

	  

<table width='<?php  echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
<tr>
			<td width="10" class="A0010">&nbsp;</td>
			<td height="25"  valign="bottom"><span class="redB">◆设备分类资料</div></td>
			<td height="22"  align="right"><span class="redB">本页操作请谨慎</div></td>
			<td width="10" class="A0001">&nbsp;</td>
		</tr>
		<tr class="">
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
            <td scope="col" align="right">设备分类名称</td>
            <td scope="col">
              <input name="Name" type="text" id="Name" size="60" maxlength="20" value="<?php  echo $Name?>" title="可输入1-20个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="20" Min="1" Msg="没有填写或字符超出20字节" readonly="readonly"> 
			</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>

		<tr>
			<td width="10" class="A0010">&nbsp;</td>
			<td height="25" colspan="2" valign="bottom"><span class="redB">◆已有维护项目明细</span></td>
			<td width="10" class="A0001">&nbsp;</td>
		</tr>
	</table>
    <!--
	<table width='<=$tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
         <tr bgcolor='<=$Title_bgcolor?>'>
       
            <td width="10" class="A0010" >&nbsp;</td>
            <td  align="center" class="A0111">
            <div style="width:100%;height:100%;overflow-x:hidden;overflow-y:auto">
                <table width='100%' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" >
                  <tr>  
                    <td width="30" class="A1111" align="center">操作</td>
                    <td width="30" class="A1101" align="center">序号</td>
                    <td width="70" class="A1101" align="center">采购日期</td>
                    <td width="90" class="A1101" align="center">待购流水号</td>
                    <td width="180" class="A1101" align="center">配件名称</td>
                    <td width="60" class="A1101" align="center">配件价格</td>
                    <td width="60" class="A1101" align="center">需求数量</td>
                    <td width="60" class="A1101" align="center">使用库存</td>
                    <td width="60" class="A1101" align="center">采购数量</td>
                    <td width="60" class="A1101" align="center">增购数量</td>
                    <td width="50" class="A1101" align="center">采购员</td>
                    <td width="" class="A1101" align="center">供应商</td>
                  </tr>         
                </table>
            </div>		
            </td>
            <td width="10" class="A0001">&nbsp;</td>
        </tr>   
       -->
	<table width='<?php  echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
         <tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="10" class="A0010" >&nbsp;</td>
		<td align="center" class="A1111">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:auto">
                    
                <table width='100%' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" >
                  <tr>  
                    <td width="30" class="A0001" align="center">操作</td>
                    <td width="30" class="A0001" align="center">序号</td>
                    <td width="30" class="A0001" align="center">项目ID</td>
                    <td width="250" class="A0001" align="center">维护项目名称</td>
                    <td width="50" class="A0001" align="center">类型</td>
                    <td width="50" class="A0001" align="center">天数</td>
                    <td width="70" class="A0001" align="center">日期</td>
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
		$StockResult = mysql_query("SELECT  O.Id,O.Name,O.Days,O.Date,O.Estate,O.Operator,O.Locks,M.CName FROM $DataPublic.oa3_maitaintype  O 
								    left join $DataPublic.oa3_maitaindays  M  ON M.ID=O.DaysID 
									where 1 AND O.TypeId='$Id'",$link_id);

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
				$SubCName=$StockRows["CName"];	
				$SubDays=$StockRows["Days"];

				echo"<tr><td width='30' class='A0101' align='center'>";
				//这句要改，这里借用下。。
				$outResult = mysql_query("SELECT Id FROM $DataPublic.fixed_assetsdata WHERE TypeId='$SubId'",$link_id);
				//$outResult = mysql_query("SELECT Id FROM $DataPublic.fixed_assetsdata WHERE TypeId='9999'",$link_id);
				if($outRows = mysql_fetch_array($outResult)){
					echo"&nbsp;";//有固定资产记录
					}
				else{			
					if($Estate!=4){	
						echo"<a href='#' onclick='deleteRow(this.parentNode,StuffList)' title='删除此维护项目'>×</a>";
						}
					else{
						echo"&nbsp;$Estate";
						}
					}
				echo"<td width='30' class='A0101' align='center'>$i</td>";
				echo"</td><td width='30' class='A0101' align='center'>$SubId</td>
					<td width='250' class='A0101' $SubNameondblclick title='双击更改'>$SubName</td>
					<td width='50' class='A0101' align='center'>$SubCName</td>
					<td width='50' class='A0101' align='center'>$SubDays</td>
					<td width='70' class='A0101' align='center'>$Date</td>
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

    
  <?php  
  /*
	<table width='<=$tableWidth>' border="0" cellspacing="0" bgcolor="#FFFFFF">
		<tr>
			<td width="10" class="A0010" height="25">&nbsp;</td>
			<td valign="bottom" colspan="11"><span class="redB">◆新增配件列表</span></td>
			<td width="10" class="A0001">&nbsp;</td>
		</tr>
		<tr class="">
			<td width="10" class="A0010" bgcolor="#FFFFFF" height="22" >&nbsp;</td>
			<td width="30" class="A1111" align="center">操作</td>
			<td width="30" class="A1101" align="center">序号</td>
			<td width="90"  class="A1101" align="center">新增流水号</td>
			<td width="280" class="A1101" align="center">新增配件</td>
			<td width="60" class="A1101" align="center">配件价格</td>
			<td width="60" class="A1101" align="center">需求数量</td>
			<td width="60" class="A1101" align="center">使用库存</td>
			<td width="60" class="A1101" align="center">采购数量</td>
			<td width="60" class="A1101" align="center">增购数量</td>
			<td width="50" class="A1101" align="center">采购员</td>
			<td width="70" class="A1101" align="center">供应商</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>			
		</tr>
		<tr>
		  <td class="A0010">&nbsp;</td>
		  <td height="75" colspan="11" class="A0111">
			<div style="width:845;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='850' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTable">
			</table>
			</div>	
		  </td>
		  <td class="A0001">&nbsp;</td>
	  </tr>
	</table>
	*/
 ?>   
<table width='<?php  echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
	<?php 
	/*
    <tr bgcolor='<=$Title_bgcolor>'>
		<td width="10" class="A0010" bgcolor="#FFFFFF" height="20">&nbsp;</td>
		<td class="A1111" width="40" align="center">操作</td>
		<td class="A1101" width="40" align="center">序号</td>
		<td class="A1101" width="60" align="center">产品ID</td>
		<td class="A1101" width="250" align="center">产品名称</td>
		<td class="A1101" width="250" align="center">Product Code</td>
		<td class="A1101" width="60" align="center">订购数量</td>
		<td class="A1101" width="70" align="center">售价</td>
		<td class="A1101" width="110" align="center">小计</td>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	*/
    ?> 
    <tr>
        <td width="10" class="A0010" height="45">&nbsp;</td>
        <td valign="bottom" ><span class="redB">◆新增维护项目</span>
        <?php 
			echo "&nbsp;&nbsp; 新增维护项目名称";
			echo "<input name='DayName' type='text' id='DayName' style='width:200px' maxlength='100' title='必选项,在100个汉字内.' dataType='LimitB' min='1' max='40' msg='没有填写或超出许可范围'>"; 
			
			echo "&nbsp;&nbsp; ";
			echo "<select name='DayID' id='DayID' style='width:100px' dataType='Require'  msg='维护周期' onchange='zhtj(this.name)'>";
			echo "<option value='' selected>维护周期选择</option>";
		   	$cResult = mysql_query("SELECT Id,CName,Days FROM $DataPublic.oa3_maitaindays WHERE Estate=1 order by Id",$link_id);
		    if($cRow = mysql_fetch_array($cResult)){
				do{
					$O3ID=$cRow[Id];
					$O3CName=$cRow[CName];
					$O3Days=$cRow[Days];
					echo"<option value='$O3ID-$O3Days'>$O3CName</option>";
					}while ($cRow = mysql_fetch_array($cResult));
				}
          	
		  	echo '</select>';	
			echo '&nbsp;';
			echo "<input name='DayDays' type='text' id='DayDays' style='width:50px' dataType='Number'  msg='格式不对' value='' readonly='readonly'>天"; 
			echo '&nbsp;&nbsp;';
			echo "<input type='button'  name='AddNew' id='AddNew' value='加入'   onclick='ViewStuffId(this.name)' />";
		?>
</td>
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
                    <td width="280" class="A0001" align="center">维护项目名称</td>
                    <td width="50" class="A0001" align="center">类型</td>
                    <td width="50" class="A0001" align="center">天数</td>
                    <td width="" class="A0000" align="center">&nbsp;</td>

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
<input name="DaysID0" id="DaysID0" type="hidden" value="">
<input name="Days0" id="Days0" type="hidden" value="">
    
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>

<script LANGUAGE='JavaScript'  type="text/JavaScript">



function ViewStuffId(Action){
	var Message="";
	var num=Math.random(); 
	/*
	BackData=window.showModalDialog("stuffdata_s1.php?r="+num+"&tSearchPage=stuffdata&fSearchPage=clientorder&SearchNum=1&Action="+Action,"BackData","dialogHeight =650px;dialogWidth=980px;center=yes;scroll=yes");
	*/
	var DayName=document.getElementById("DayName").value;
	DayName=trimStr(DayName);
	if(DayName==null || DayName=="") {
		alert("请输入维护项目的名称!");
		return false;
	}
	
	var Days=document.getElementById("DayDays").value;
	var Result=fucCheckNUM(Days,'');
	if(Result==0 || Days==0){
		alert("输入了不正确的天数:"+Days+",重新输入!");
		return false;
	}
	
	var DayID= document.getElementById("DayID").value;
	
	if(DayID!=null && DayID!=''){
		var DayIDarray=DayID.split('-');
		//alert(DayIDarray[1]);
	}
	else{
		alert("请选择维护周期");
		return false;
	}	
	var index=document.getElementById("DayID").selectedIndex;
	var DayText=document.getElementById("DayID").options[index].text;
	
	
	var BackData=DayName+"^^"+DayIDarray[0]+"^^"+DayText+"^^"+Days;
	alert (BackData);
	//return false;
	
	
	if(BackData==null || BackData==''){  //专为safari设计的 ,add by zx 2011-05-04
		if(document.getElementById('SafariReturnValue')){
		//alert("return");
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
				//要求输入数量对应关系
				/*
				var returnValue =window.showModalDialog("yw_order_relation.php",window,"dialogWidth=400px;dialogHeight=300px");
				//alert ("1");
				//alert(returnValue);
				if(returnValue==null || returnValue=='' || returnValue==0){  //专为safari设计的 ,add by zx 2011-05-04
					if(document.getElementById('SafariReturnQty')){
					//alert("return");
					var SafariReturnQty=document.getElementById('SafariReturnQty');
					returnValue=SafariReturnQty.value;
					//alert ("2");
					SafariReturnQty.value="";
					}
				}	
				*/
				if (returnValue){
					/*
					var qtyvalue=returnValue;
					var POrderQty=document.form1.POrderQty.value;
					var thisQty=POrderQty*qtyvalue;//订单需求数
					//使用库存数
					if(FieldArray[3]>=thisQty){
						var kqQty=thisQty;						
						}
					else{
						var kqQty=FieldArray[3];	
						}
					//alert("FieldArray[3]:"+FieldArray[3]);
					//alert("thisQty:"+kqQty);
					
					//实际需求数	采购数量				
					var fQty=thisQty-kqQty;
					//流水号
					var thisSID=(document.form1.MaxSID.value)*1+1;
					*/
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
					oTD.className ="A0101";
					oTD.align="left";
					oTD.width="280";
					
					oTD=oTR.insertCell(3);
					oTD.innerHTML=""+FieldArray[2]+"<input name='DaysID["+tmpNum+"]' type='hidden' id='DaysID"+tmpNum+"' value='"+FieldArray[1]+"'>";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="50";
					
					oTD=oTR.insertCell(4);
					oTD.innerHTML=""+FieldArray[3]+"<input name='Days["+tmpNum+"]' type='hidden' id='Days"+tmpNum+"' value='"+FieldArray[3]+"'>";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="50";	
			
					oTD=oTR.insertCell(5);
					oTD.innerHTML="&nbsp;";
					oTD.className ="A0101";
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
		alert("没有输入维护项目！");
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
				myurl="fixed_maintain_updated.php?SubName="+delSubName+"&ActionId=delSubName";
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
		var BackData=prompt("请输需要更新维护项目名称",SubName);
		//if ((BackData!=null) &&  (BackData!="")  && (BackData!=SubName)){
		if ((BackData!=null) &&  (BackData!="") ){
			//输入删除原因
	
				//var delStockId=TableTemp.rows[rowIndex].cells[3].innerText;
				//var delStockId=TableTemp.rows[rowIndex].cells[3].innerHTML;
				var tempBackData=BackData;
				BackData=encodeURIComponent(BackData);
				myurl="fixed_maintain_updated.php?SubName="+BackData+"&Id="+SubId+"&ActionId=UpdateSubName";
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
	passvalue("SubName|DaysID|Days");  //add by zx 2011-05-05 必须与上面隐藏传递元素id0号一致,Pid0
	document.form1.action="fixed_maintain_updated.php";
	document.form1.submit();
}

function zhtj(obj){
	switch(obj){
		case "DayID"://改变维护周期
			//document.forms["form1"].elements["PayMode"].value="";

			var DayID= document.getElementById("DayID").value;
			if(DayID!=null && DayID!=''){
				var DayIDarray=DayID.split('-');
				//alert(DayIDarray[1]);
				if	(DayIDarray[1]==0) {
					document.getElementById("DayDays").readOnly=false;
				}
				else{
					document.getElementById("DayDays").readOnly=true;
				}
					document.getElementById("DayDays").value=DayIDarray[1];
			}
			else{
				document.getElementById("DayDays").readOnly=true;
				document.getElementById("DayDays").value='';
			}
       
		break;

		}
	//document.form1.action="fixed_assets_read.php";
	//document.form1.submit();


}	

function trimStr(stringToTrim) 
{return stringToTrim.replace(/^\s+|\s+$/g,"");}

</script>

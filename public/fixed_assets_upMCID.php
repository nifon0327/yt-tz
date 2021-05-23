<?php 
//已更新
//电信-joseph
include "../model/modelhead.php";
echo "<link rel='stylesheet' href='../model/inputSuggest.css'>
      <script type='text/javascript' src='../model/inputSuggest1.0c.js'></script>";
//步骤2：
ChangeWtitle("$SubCompany 更新固定资产使用公司资料");//需处理
$nowWebPage =$funFrom."_upMCID";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤3：
$tableWidth=600;$tableMenuS=300;
//$CustomFun="<span onClick='ViewStuffId(7)' $onClickCSS>新的使用者</span>&nbsp;";//自定义功能
//$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理

$Id=$Mid;
$upResult = mysql_query("SELECT * FROM $DataPublic.fixed_assetsdata WHERE Id='$Id'",$link_id);
if ($upData = mysql_fetch_array($upResult)) {
	$CpName=$upData["CpName"];
	$Model=$upData["Model"];
	$tempCM=$Model."(".$CpName.")";
	$temMCID=$upData["cSign"];
	}
$ActionId="UpMCID";	 ////////////////////

$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,Id,$Id,ActionId,$ActionId,POrderId,$POrderId,OMTypeId,$OMTypeId,TypeId,$TypeId,BranchId,$BranchId,UserMCID,$UserMCID";
//<td height="22" width='' align="right"><span class="redB">本页操作请谨慎</span></td>
?>
	<input name="SafariReturnQty" id="SafariReturnQty" type="hidden" value="0"> 
    <input name="Mid" id="Mid" type="hidden" value="<?php  echo $Id?>">
    <table width='<?php  echo $tableWidth?>' border="0" cellspacing="0" bgcolor="#FFFFFF">
		<tr>
			<td width="10" class="A0010">&nbsp;</td>
			<td height="25" colspan="2"   valign="bottom"><span class="redB">◆固定资产资料:<?php  echo $tempCM?></span></td>
			
			<td width="10" class="A0001">&nbsp;</td>
		</tr>
		<tr class="">
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
            <td scope="col" align="right">更换公司：</td>
            <td scope="col">
             <select name='cSign' id='cSign' onchange='cSignChanged(this)' style='width:200'>
             <option value='' selected>--请选择--</option>
            <?php 
				$cSignResult = mysql_query("SELECT cSign,CShortName,Db FROM $DataPublic.companys_group WHERE Estate IN (1,0)  AND cSign>0 AND cSign!=$temMCID ORDER BY Id",$link_id);
if($cSignRow = mysql_fetch_array($cSignResult)){
        do{
                $theId=$cSignRow["cSign"];
                $theName=$cSignRow["CShortName"];
                $dbName=$cSignRow["Db"];
                if ($theId==$cSign){
                       $DataIn=$dbName;
                        echo "<option value='$theId' selected>$theName</option>";
                        }
                else{
                        echo "<option value='$theId'>$theName</option>";
                        }
         }while ($cSignRow = mysql_fetch_array($cSignResult));
      }
        ?>
        </select>
			</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
    
		<tr class="">
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
            <td scope="col" align="right">日期：</td>
            <td scope="col">
  
               <input name="UserDate" type="text" id="UserDate" style="width:200px;" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="日期不正确" readonly>  
			</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>   
        
 		<tr class="">
			<td width="10" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
            <td scope="col" align="right">备注：</td>
            <td scope="col">
               <textarea name="Remark" cols="20" rows="6" id="Remark" style="width:200px;"></textarea>
			</td>
			<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>              

		<tr>
			<td width="10" class="A0010">&nbsp;</td>
			<td height="25" colspan="2" valign="bottom"><span class="redB">◆公司使用明细</span></td>
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
                    <td width="80" class="A0001" align="center">使用公司</td>
                    <td width="90" class="A0001" align="center">日期</td>
                     <td width="220" class="A0001" align="center">备注</td>
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
		$StockResult = mysql_query("SELECT F.ID,S.MCId,S.CShortName as Name,F.User,F.SDate as Date,F.Remark,F.Operator FROM $DataPublic.fixed_userdata F
								    left join $DataPublic.companys_group S ON S.MCId=F.User
								   	where F.Mid='$Id' AND F.UserType=3 order by F.SDate ",$link_id);
		echo "";
		if($StockRows = mysql_fetch_array($StockResult)){
			$i=1;
			do{
				
				$SubId=$StockRows["Id"];
				$SubName=$StockRows["Name"];
				//$SubNameondblclick="ondblclick=UpdateRow(this,StuffList,'SubName')";  //
				$Date=$StockRows["Date"];
				$Remark=$StockRows["Remark"]==""?"&nbsp;":$StockRows["Remark"];
				//$Estate=$StockRows["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
				
				$SubMCId=$StockRows["MCId"];
				$SubUser=$StockRows["User"];
				//echo "$SubUser==$MCIDtmp";
					$Operator=$StockRows["Operator"];
					include "../model/subprogram/staffname.php";
				//$Locks=$StockRows["Locks"];	
				
				echo"<tr><td width='30' class='A0101' align='center'>";
				//echo"<a href='#' onclick='deleteRow(this.parentNode,StuffList)' title='删除领用人'>×</a>";
				echo "&nbsp;";  //删除的就暂时不添加，看需要时再说
				/*
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
				*/	
				echo"<td width='30' class='A0101' align='center'>$i</td>";
				echo"
					<td width='80' class='A0101' align='left'>$SubName</td>
					<td width='90' class='A0101' align='center'>$Date</td>
					<td width='220' class='A0101' align='left'>$Remark</td>
					<td width='' class='A0101'>$Operator</td>
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

    


<input name="SubName0" id="SubName0" type="hidden" value="">

    
<?php 
//步骤5：

include "../model/subprogram/add_model_b.php";
/*
  $StaffSql = mysql_query("SELECT M.Id,M.Number,M.Name, B.Name AS Branch
	FROM $DataPublic.staffmain M 
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId	
	WHERE 1 AND M.Estate='1' ORDER BY M.BranchId,M.JobId,M.ComeIn,M.Number",$link_id);

	while ($StaffRow = mysql_fetch_array($StaffSql)){
		$sNumber=$StaffRow["Number"];
                $sName=$StaffRow["Name"];
		$sBranch=$StaffRow["Branch"];

                $subName[]=array($sNumber,$sName,$sBranch);
	};
	*/
?>
<script LANGUAGE='JavaScript'  type="text/JavaScript">
 /*   
 window.onload = function(){
                var subName=< ? echo json_encode($subName);?>;
                
		var sinaSuggest = new InputSuggest({
		        input: document.getElementById('UserName'),
			poseinput: document.getElementById('User'),
			data: subName,
			width: 200
		});
				
	}

*/


function ViewStuffId(Action){
	var Message="";
	var num=Math.random(); 
	/*
	BackData=window.showModalDialog("stuffdata_s1.php?r="+num+"&tSearchPage=stuffdata&fSearchPage=clientorder&SearchNum=1&Action="+Action,"BackData","dialogHeight =650px;dialogWidth=980px;center=yes;scroll=yes");
	*/
	 var BackData=prompt("请输入新的子分类名称","");
	 if(BackData==""){ 
	 	return false;		
	}
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
				myurl="fixed_assets_updated.php?SubName="+delSubName+"&ActionId=delMCID";
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
				myurl="fixed_assets_updated.php?SubName="+BackData+"&Id="+SubId+"&ActionId=UpdateSubName";
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
	//passvalue("SubName");  //add by zx 2011-05-05 必须与上面隐藏传递元素id0号一致,Pid0
	document.form1.action="fixed_assets_updated.php"+"&ActionId=UpMCID";  
	document.form1.submit();
}


</script>

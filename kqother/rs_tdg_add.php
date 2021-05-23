<?php 
//电信-ZX  2012-08-01
//步骤1 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增员工等级调动记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=700;$tableMenuS=400;
$CustomFun="<span onClick='javascript:ChooseAddDate();' $onClickCSS>选取员工</span>&nbsp;";
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
    	<td colspan="11" class='A0011'>&nbsp;</td>
	</tr>
	<tr>
    	<td class='A0011' height="25">&nbsp;</td>
		<td colspan="2" align="center" class='A1001'>起效月份</td>
		<td colspan="7" class='A1001'><input name="Month" type="text" id="Month" maxlength="7" size="90" class="noLine" title="这里填写月份,格式YYYY-MM" dataType="Date" format="ymd" Msg="未填写或日期格式不对"></td>
	    <td class='A0001'>&nbsp;</td>
	</tr>
	<tr>
    	<td class='A0011' height="25">&nbsp;</td>
		<td colspan="2" align="center" class='A1001'>调动原因</td>
		<td colspan="7" class='A1001'><input name="Remark" type="text" id="Remark" size="90" class="noLine" title="这里填写调动原因"></td>
	    <td class='A0001'>&nbsp;</td>
	</tr>
    <tr <?php  echo $Fun_bgcolor?>>
    	<td width="10" height="25" class='A0010' bgcolor="#FFFFFF">&nbsp;</td>
	    <td width="50" class='A1111' align="center">操作<input name="TempValue" type="hidden" id="TempValue"><input name="AddIds" type="hidden" id="AddIds"></td>
	    <td width="50" class='A1101' align="center">序号</td>
        <td width="70" class='A1101' align="center">员工ID</td>
        <td width="70" class='A1101' align="center">员工姓名</td>
        <td width="70" class='A1101' align="center">部门</td>
        <td width="70" class='A1101' align="center">职位</td>
		<td width="70" class='A1101' align="center">原等级</td>
        <td width="70" class='A1101' align="center">新等级</td>
        <td width="160" class='A1101' align="center">等级范围</td>
        <td width="10" class='A0001' bgcolor="#FFFFFF">&nbsp;</td>
    </tr>
    <tr>
    	<td width="10" class='A0010'>&nbsp;</td>
		<td height="300" colspan="9" align="right" class='A0111'>
		<div style='width:680;height:300;overflow-x:hidden;overflow-y:scroll'> 
		<table border='0' width='680' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' align='left' id='newTable'>
		</table>
		</div>
		</td>
		<td width="10" class='A0001'>&nbsp;</td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script LANGUAGE='JavaScript'>
function CheckForm(){
	var Message="";
	var lengthTemp=newTable.rows.length;
	if(lengthTemp==0){
		Message="没有加入资料！";
		}
	//月份检查
	var MonthTemp=document.form1.Month.value;
	if(!yyyymmCheck(MonthTemp)){
		Message="月份格式不对";
		}
	var AddIdsTemp="";
	//读取加入的数据
	var tempGrade="";
	var obj=document.getElementsByTagName("input");
	for (var i=0;i<obj.length;i++){
		  var e=obj[i];
		  var NameTemp=e.name;
		  var Name=NameTemp.search("Grade") ;
		  if(obj[i].value=="" && Name!=-1){
			Message+="入库数量不能为空!";
			break;
			} 
		 if ( Name!=-1){
		      if (tempGrade==""){tempGrade=e.value;} else {tempGrade=tempGrade + "|" + e.value;}
		 }
	}

	if(Message!=""){
		alert(Message);return false;
		}
	else{
	   var arrGrade=tempGrade.split("|"); 
		for(i=0;i <arrGrade.length;i++){ 
		  if(AddIdsTemp==""){
				AddIdsTemp=newTable.rows[i].cells[2].innerHTML+"!"+arrGrade[i];
				}
			else{
				AddIdsTemp=AddIdsTemp+"|"+newTable.rows[i].cells[2].innerHTML+"!"+arrGrade[i];
				}

		}

	 // alert(AddIdsTemp);
	
		document.form1.Remark.value=toGB(document.form1.Remark.value);//转简体		
		document.form1.AddIds.value=AddIdsTemp;
		document.form1.action="rs_tdg_save.php";
		document.form1.submit();
		
		}
	}

//序号重整
function ShowSequence(TableTemp){
	for(i=0;i<TableTemp.rows.length;i++){ 
  		var j=i+1
		TableTemp.rows[i].cells[1].innerHTML=j; 
		}
	}
	   
//删除指定行:参数，删除行的索引和所在表格
function deleteRow (rowIndex,TableTemp){
	if(TableTemp==newTable){
		TableTemp.deleteRow(rowIndex);
		ShowSequence(TableTemp);
		}
	}

function toTempValue(textValue){//OK
	document.form1.TempValue.value=textValue;
	}
function InNumber(thisE,Low,Hight){//OK
	var oldValue=document.form1.TempValue.value;
	var thisValue=thisE.value;
	var CheckSTR=fucCheckNUM(thisValue,"");
	if(CheckSTR==0){
		alert("不是规范的数字！");
		thisE.value=oldValue;
		return false;
		}
	else{
		if((thisValue<Low) || thisValue>Hight){
			alert("不在允许值的范围！");
			thisE.value=oldValue;
			return false;
			}
		}
	}
//新加员工明细行OK
function ChooseAddDate(){
	var r=Math.random();
	BackStockId=window.showModalDialog("staff_s1.php?r="+r+"&tSearchPage=staff&fSearchPage=staffgrade&SearchNum=2&Action=2","BackStockId","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
//alert(BackStockId);
	if (BackStockId){
  		var Rowstemp=BackStockId.split("``");
		var Rowslength=Rowstemp.length;
		for(var i=0;i<Rowslength;i++){
			var Message="";			
			var FieldArray=Rowstemp[i].split("^^");
			//过滤相同的员工ID号
			for(var j=0;j<newTable.rows.length;j++){
				var IdTemp=newTable.rows[j].cells[2].innerHTML;//隐藏ID号存于操作列	
				if(FieldArray[0]==IdTemp){//如果存在
					Message="员工: "+FieldArray[1]+"的资料已在列表!跳过继续！";
					break;
					}
				}
			if(Message==""){
				oTR=newTable.insertRow(newTable.rows.length);
				tmpNum=oTR.rowIndex+1;
				//第一列:操作
				oTD=oTR.insertCell(0);
				oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex,newTable)' title='删除当前行'>×</a>";
				oTD.onmousedown=function(){
					window.event.cancelBubble=true;
					};
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="48";
				oTD.height="20";
				
				//第二列:序号
				oTD=oTR.insertCell(1);
				oTD.innerHTML=""+tmpNum+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="49";
				
				//三、员工ID
				oTD=oTR.insertCell(2);
				oTD.innerHTML=""+FieldArray[0]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";
				
				//四：员工姓名
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[1]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";
				
				//五:部门
				oTD=oTR.insertCell(4); 
				oTD.innerHTML=""+FieldArray[2]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";

				//六：职位
				oTD=oTR.insertCell(5);
				oTD.innerHTML=""+FieldArray[3]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";
				
				//七：原等级
				oTD=oTR.insertCell(6);
				oTD.innerHTML=""+FieldArray[6]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";

				//八：新等级
				oTD8=oTR.insertCell(7);
				oTD8.className ="A0101";
				oTD8.align="center";
				oTD8.width="69";
				
				//八：等级范围
				oTD=oTR.insertCell(8);				
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="163";
				oTD8.innerHTML="<input type='text' name='Grade[]' id='Grade' size='3' class='INPUT0000' value='"+FieldArray[4]+"' onchange='InNumber(this,"+FieldArray[4]+","+FieldArray[5]+")' onfocus='toTempValue(this.value)'>";
				oTD.innerHTML="从 "+FieldArray[4]+" 至 "+FieldArray[5]+"";
				}
			else{
				alert(Message);
				}//if(Message=="")
			}//for(var i=0;i<Rowslength;i++)
		}//if (BackStockId)
	else{
		alert("没有选取数据!");return true;
		}
	}
</script>
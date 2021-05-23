<?php 
//MC专用、DP没有加入毛利即时计算
//步骤1
include "../model/modelhead.php";
//步骤2：
echo"<SCRIPT src='../model/addtblist.js' type=text/javascript></script>";
ChangeWtitle("$SubCompany 配件需求单更新");//需处理
$nowWebPage =$funFrom."_change";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"] = $nowWebPage; 
//$Id=$Id==""?$ProductId:$Id;//重置
//步骤3：
$tableWidth=1000;$tableMenuS=500;$ColsNumber=10;
$CustomFun="<span onClick='CPandsViewStuffId(3)' $onClickCSS>加入配件</span>&nbsp;";//自定义功能
$CheckFormURL="thisPage";
include "../model/subprogram/sys_parameters.php";
$upResult = mysql_query(" SELECT D.Id,D.ItemId,D.Attached,D.ItemName,D.Content,D.StartDate,D.Operator,D.EndDate,C.Forshort,P.Name,D.Qty,D.Developer
FROM $DataIn.development D 
LEFT JOIN $DataIn.trade_object C ON D.CompanyId=C.CompanyId
LEFT JOIN $DataPublic.staffmain P ON P.Number=D.Developer 
WHERE D.Id=$Id",$link_id);
if ($upData = mysql_fetch_array($upResult)) {
	$ItemId=$upData["ItemId"];
	$ItemName=$upData["ItemName"];
	$Forshort=$upData["Forshort"];
	$Qty=$upData["Qty"];
	$Developer=$upData["Developer"];
	$Name=$upData["Name"];
	$StartDate=$upData["StartDate"];
	$EndDate=$upData["EndDate"];
	}
$SelectCode="项目 $ItemName 的配件清单<input name='HZ' type='hidden' id='HZ' value='$HzRate'>";
include "../model/subprogram/add_model_t.php";
//include "../model/subprogram/add_model_pt.php";
//步骤4：需处理
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,
Page,$Page,ActionId,$ActionId,Id,$Id,ItemId,$ItemId";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="10" class="A0010" bgcolor="#FFFFFF" height="25">&nbsp;</td>
        <td width="70" height="25" class="A1111" align="center">操作</td>
        <td width="50" class="A1101" align="center">序号</td>
        <td width="90" class="A1101" align="center">类别</td>
		<td width="50" class="A1101" align="center">配件ID</td>
        <td width="270" class="A1101" align="center">配件名称</td>
        <td width="70" class="A1101" align="center">对应数量</td>
        <td width="70" class="A1101" align="center">刀模</td>
        <td width="70" class="A1101" align="center">切割关系</td>
		<td width="40" class="A1101" align="center">单价</td>
        <td width="70" class="A1101" align="center">采购</td>
        <td width="120" class="A1101" align="center">供应商</td>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" class="A0010" height="336">&nbsp;</td>
		<td height="25" colspan="11" class="A0111">
		<div style="width:975;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='975' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTable">
			<?php 
		$S_Result=mysql_query("SELECT D.Id,D.ItemId,D.StuffCname,D.TypeId,D.Price,
		D.CompanyId,P.Forshort,S.TypeName,D.Relation,D.Diecut,D.Cutrelation,M.Name,D.StuffId
            FROM $DataIn.developsheet D
            LEFT JOIN $DataIn.stufftype S ON S.TypeId=D.TypeId
            LEFT JOIN $DataIn.trade_object P ON P.CompanyId=D.CompanyId
			LEFT JOIN $DataPublic.staffmain M  ON M.Number=D.BuyerId
            WHERE ItemId IN (SELECT ItemId FROM $DataIn.development WHERE Id='$Id')",$link_id);
		if($S_Row=mysql_fetch_array($S_Result)) {//如果设定了产品配件关系
			$i=1;
			do{
				$StuffId=$S_Row["StuffId"];
				$Relation=$S_Row["Relation"];
				$Diecut=$S_Row["Diecut"];
				$Cutrelation=$S_Row["Cutrelation"];
				//$Diecut=$Diecut==""?"&nbsp;":$Diecut;
				$Cutrelation=$Cutrelation==0?"":$Cutrelation;
				$StuffCname=$S_Row["StuffCname"];
				$TypeName=$S_Row["TypeName"];
				$Name=$S_Row["Name"];
				$Forshort=$S_Row["Forshort"];
				$Currency=$S_Row["Currency"];
				$Price=$S_Row["Price"];
				$Rate=$S_Row["Rate"];
				$theAmount=sprintf("%.4f",$Price*$Rate);
				echo"<tr>
				<td align='center' class='A0101' width='70' onmousedown='window.event.cancelBubble=true;'>
				 <a href='#' onclick='deleteRow(this.parentNode)' title='删除当前行'>×</a>&nbsp;
				 <a href='#' onclick='upMove(this.parentNode)' title='当前行上移'>∧</a>&nbsp;
				 <a href='#' onclick='downMove(this.parentNode)' title='当前行下移'>∨</a>
				</td>
		   		   <td align='center' class='A0101' width='50'>$i</td>
		   		   <td class='A0101' width='90'>$TypeName</td>
				   <td class='A0101' width='50'>$StuffId</td>
				   <td class='A0101' width='270'>$StuffCname</td>
				   <td class='A0101' align='center' width='70'>
				   <input name='Qty[]' type='text' id='Qty$i' size='4' value='$Relation' onchange='checkNum(this)' onfocus='toTempValue(this.value)'>
				   </td>
				   <td class='A0101' align='center' width='70'>
				    <input name='Diecut[]' type='text'  id='Diecut$i' size='6' value='$Diecut'>
					</td>
				    <td class='A0101' align='center' width='70'>
					<input name='Cutrelation[]' type='text' id='Cutrelation$i' size='4' value='$Cutrelation' onchange='checkNum(this)'>
				   </td>
				   <td class='A0101' align='center' width='40'>$Price</td>
				   <td class='A0101' align='center' width='70'>$Name</td>
				   <td class='A0101' width='115'>$Forshort</td>
				  </tr>";
		  		$i++;
				}while ($S_Row=mysql_fetch_array($S_Result));
			}
			$Rows=$i-1;
			?>
			</table>
		</div>
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
</table>
<input name="TempValue" type="hidden" id="TempValue"><input name="SIdList" type="hidden" id="SIdList">
<?php 
//步骤5：
include "subprogram/add_model_p.php";
?>
<script language = "JavaScript"> 

function CPandsViewStuffId(Action){
	var num=Math.random();  
	BackData=window.showModalDialog("stuffdata_s1.php?r="+num+"&tSearchPage=stuffdata&fSearchPage=pands&SearchNum=2&Action="+Action,"BackData","dialogHeight =650px;dialogWidth=980px;center=yes;scroll=yes");
	
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
		var Rowslength=Rows.length;//数组长度即领料记录数
		
		if(document.getElementById("TempMaxNumber")){  ////给add by zx 2011-05-05 firfox and  safari不能用javascript生成的元素
			var TempMaxNumber=document.getElementById("TempMaxNumber");
			TempMaxNumber.value=TempMaxNumber.value*1+Rowslength*1;
		}
		  //给add by zx firfox and  safari不能用javascript生成的元素
			  
		for(var i=0;i<Rowslength;i++){
			var Message="";
			var FieldTemp=Rows[i];		//拆分后的记录
			var FieldArray=FieldTemp.split("^^");//分拆记录中的字段
			//过滤相同的配件ID号
			for(var j=0;j<ListTable.rows.length;j++){						
				var SIdtemp=ListTable.rows[j].cells[3].innerText;				
				if(FieldArray[1]==SIdtemp){//如果流水号存在
					Message="配件: "+FieldArray[2]+" 已存在!跳过继续！";
					break;
					}
				}			
			if(Message==""){
				oTR=ListTable.insertRow(ListTable.rows.length);
				
				//表格行数
				tmpNumQty=oTR.rowIndex;
				tmpNum=oTR.rowIndex+1;
				
				//第1列:隐藏的配件ID
				oTD=oTR.insertCell(0);
				oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode)' title='删除当前行'>×</a>&nbsp;&nbsp;<a href='#' onclick='upMove(this.parentNode)' title='当前行上移'>∧</a>&nbsp;&nbsp;<a href='#' onclick='downMove(this.parentNode)' title='当前行下移'>∨</a>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";
				oTD.height="20";
				//第2列:序号
				oTD=oTR.insertCell(1);
				oTD.innerHTML=""+tmpNum+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="50";
				//第3列:类别
				oTD=oTR.insertCell(2);
				oTD.innerHTML=""+FieldArray[0]+"";
				oTD.className ="A0101";
				oTD.width="90";
				//第4列:配件ID
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[1]+"";
				oTD.className ="A0101";
				oTD.width="50";
				
				//第5列:配件名称
				oTD=oTR.insertCell(4);
				oTD.innerHTML=""+FieldArray[2]+"";
				oTD.className ="A0101";
				oTD.width="270";
				//第6列:对应数量
				oTD=oTR.insertCell(5); 
				oTD.innerHTML="<input name='Qty[]' type='text' id='Qty"+tmpNumQty+"' size='4' class='noLine' value='1' onchange='checkNum(this)' onfocus='toTempValue(this.value)'><input name='Fb[]' type='hidden' id='Fb"+tmpNumQty+"' value='"+FieldArray[6]+"'><input name='sPrice[]' type='hidden' id='sPrice"+tmpNumQty+"' value='"+FieldArray[5]+"'>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";
				//第7列:刀模
				oTD=oTR.insertCell(6);
				oTD.innerHTML="<input name='Diecut[]' type='text'  id='Diecut"+tmpNumQty+"' value=''  size='6'/>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";
				//第8列:切割关系
				oTD=oTR.insertCell(7);
				oTD.innerHTML="<input name='Cutrelation[]' type='text' id='Cutrelation"+tmpNumQty+"' size='4' value='' onchange='checkNum(this)'/>";				
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";
				//form1.hfield.value=tmpNum;
				//第9列:单价
				oTD=oTR.insertCell(8);
				oTD.innerHTML=""+FieldArray[7]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="40";
				 //第10列:采购
				oTD=oTR.insertCell(9);
				oTD.innerHTML=""+FieldArray[3]+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="70";
				//第11列:供应商
				oTD=oTR.insertCell(10);
				oTD.innerHTML=""+FieldArray[4]+"";				
				oTD.className ="A0101";
				oTD.width="116";
				//form1.hfield.value=tmpNum;
				}
			else{
				alert(Message);
				}
			}//end for
			return true;
		}
	else{
		alert("没有选到配件！");
		return false;
		}
	}
	
function CheckForm(){
	//检查对应数量是否正确
	var Message="";
	var QtySTR="";
	if(Message!=""){
		alert(Message);
		return false;
		}
	else{
		var DataSTR="";
		for(i=0;i<ListTable.rows.length;i++){
  			var thisData=ListTable.rows[i].cells[3].innerText;
			if(DataSTR==""){
				DataSTR=thisData;
				}
			else{
				DataSTR=DataSTR+"|"+thisData;
				}
			}
		if(DataSTR!=""){
			document.form1.SIdList.value=DataSTR;
			document.form1.action="developtask_updated.php";
			document.form1.submit();
			}
		else{
			alert("没有加入任何配件！请先加入配件！");
			return false;
			}
		}
	}
function checkNum(obj){
	var oldScore=document.form1.TempValue.value;
	var TempScore=obj.value;
	var reBackSign=0;
	var TempScore=funallTrim(TempScore);
	var firstChar=TempScore.substring(0,1); 
	if(firstChar==0){
		reBackSign=0;
		}
	else{
		var ScoreArray=TempScore.split("/");
		var LengthScore=ScoreArray.length;
		if(LengthScore>2){
			reBackSign=0;
			}
		else{
			if(LengthScore==1){
				//检查数字格式
				var NumTemp=ScoreArray[0];
				var reBackSign=fucCheckNUM(NumTemp,"");//1是数字，0不是数字
				}
			else{
				var NumTemp0=ScoreArray[0];
				var reBackSign=fucCheckNUM(NumTemp0,"");//1是数字，0不是数字
				if(reBackSign==1){
					var NumTemp1=ScoreArray[1];
					reBackSign=fucCheckNUM(NumTemp1,"");//1是数字，0不是数字
					}
				}		
			}
		}
	if(reBackSign==0){
		alert("对应数量不正确！");
		obj.value=oldScore;
		return false;
		}
	}



</script>
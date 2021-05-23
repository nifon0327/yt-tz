<?php 
//代码、数据库共享-zx
//电信-ZX  2012-08-01
//代码共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：
$funFrom="ipad_modulenexus";
ChangeWtitle("$SubCompany 更新系统功能的权限");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_set";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$cSign=$_SESSION["Login_cSign"] ;
//步骤4：
$tableWidth=850;$tableMenuS=500;
$checkModelName= mysql_fetch_array(mysql_query("SELECT ModuleName FROM $DataPublic.sc4_funmodule WHERE ModuleId='$ModuleId' LIMIT 1",$link_id));
$ModuleName=$checkModelName["ModuleName"];
$SelectCode="ipad系统功能名称：$ModuleName($ModuleId)";

include "../model/subprogram/add_model_t.php";
$Parameter="ModuleId,$ModuleId,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="800" height="42" border="0" align="center" cellspacing="0">
      <tr class=''>
        <td width="45" rowspan="2" align="center" class="A1111">序号</td>
        <td width="65" rowspan="2" align="center" class="A1101">部门</td>
		<td width="68" rowspan="2" align="center" class="A1101">职位</td>
        <td width="100" height="21" align="center" class="A1101">用户姓名</td>
        <td colspan="5" align="center" class="A1101">权限</td>
      </tr>
      <tr class=''>
        <td width="100" height="21" class="A0101">&nbsp;<input name="ChooseCol1" type="checkbox" id="ChooseCol1" value="1" onClick="ChooseTheCol(this)"><LABEL for="ChooseCol1">全选用户列</LABEL></td>
        <td width="100" class="A0101">&nbsp;<input name="ChooseCol2" type="checkbox" id="ChooseCol2" value="2" onClick="ChooseTheCol(this)"><LABEL for="ChooseCol2">全选浏览列</LABEL></td>
      	<td width="100" class="A0101">&nbsp;<input name="ChooseCol3" type="checkbox" id="ChooseCol3" value="3" onClick="ChooseTheCol(this)"><LABEL for="ChooseCol3">全选新增列</LABEL></td>
        <td width="100" class="A0101">&nbsp;<input name="ChooseCol4" type="checkbox" id="ChooseCol4" value="4" onClick="ChooseTheCol(this)"><LABEL for="ChooseCol4">全选更新列</LABEL></td>
        <td width="100" class="A0101">&nbsp;<input name="ChooseCol5" type="checkbox" id="ChooseCol5" value="5" onClick="ChooseTheCol(this)"><LABEL for="ChooseCol5">全选删除列</LABEL></td>
		<td width="106" class="A0101">&nbsp;<input name="ChooseCol6" type="checkbox" id="ChooseCol6" value="6" onClick="ChooseTheCol(this)"><LABEL for="ChooseCol6">全选锁定列</LABEL></td>
      </tr>
<?php 
//只给内部员工权限
$Result =mysql_query("SELECT A.Id,B.Name,B.Number,C.Name AS Branch,D.Name AS Job 
	FROM $DataIn.usertable A 
	LEFT JOIN $DataPublic.staffmain B ON B.Number=A.Number 
	LEFT JOIN $DataPublic.branchdata C ON C.Id=B.BranchId
	LEFT JOIN $DataPublic.jobdata D ON D.Id=B.JobId 
	WHERE A.Estate=1 AND B.cSign='$Login_cSign' AND A.uType='1' ORDER BY B.JobId,B.Number",$link_id);
$i=1;
$j=1;//复选框序号
$cols2=1;
$cols3=1;
$cols4=1;
$cols5=1;
$cols6=1;
if ($myRow = mysql_fetch_array($Result)){
	do {
		$UserId=$myRow["Id"];
		$Name=$myRow["Name"];
		$Number=$myRow["Number"];
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		//////////////////////////
		//步骤3：//需处理
		$actionResult =mysql_query("SELECT A.Action FROM $DataIn.sc4_upopedom A 
								   LEFT JOIN $DataIn.usertable B ON B.Id=A.UserId 
								   WHERE A.ModuleId='$ModuleId' AND B.Number='$Number' LIMIT 1",$link_id);
		if($chexkRow3 = mysql_fetch_array($actionResult)){
			$ActionSTR3="checked";
			$Action=$chexkRow3["Action"];
			$cols2++;
			if($Action & mADD){$ActionSTRa="checked";$cols3++;}else{$ActionSTRa="";}//2
			if($Action & mUPDATE){$ActionSTRb="checked";$cols4++;}else{$ActionSTRb="";}//4
			if($Action & mDELETE){$ActionSTRc="checked";$cols5++;}else{$ActionSTRc="";}//8
			if($Action & mLOCK){$ActionSTRd="checked";$cols6++;}else{$ActionSTRd="";}//16
			}
		else{
			$ActionSTR3="";
			$ActionSTRa="";
			$ActionSTRb="";
			$ActionSTRc="";
			$ActionSTRd="";
			}
							
		echo"<tr><td align='center' height='20' class='A0111'>$i</td>";
		echo"<td class='A0101' align='center'>$Branch</td>";
		echo"<td class='A0101' align='center'>$Job</td>";
		echo"<td class='A0101'>&nbsp;
			<input name='checkid$j' type='checkbox' id='$j' value='$UserId' onclick='Chooserow($j)' $ActionSTR3>
			<LABEL for='$j'>$Name</LABEL></td>";
			$j++;
		echo"<td class='A0101'>&nbsp;
			<input name='checkid$j' type='checkbox' id='$j' value='1' onclick='Chooserow($j)' $ActionSTR3>
			<LABEL for='$j'>浏览</LABEL>&nbsp;</td>";
			$j++;
		echo"<td class='A0101'>&nbsp;
			<input name='checkid$j' type='checkbox' id='$j' value='2' onclick='Chooserow($j)' $ActionSTRa>
			<LABEL for='$j'>新增</LABEL>&nbsp;</td>";
			$j++;
		echo"<td class='A0101'>&nbsp;
			<input name='checkid$j' type='checkbox' id='$j' value='4' onclick='Chooserow($j)' $ActionSTRb>
			<LABEL for='$j'>更新</LABEL>&nbsp;</td>";
			$j++;
		echo"<td class='A0101'>&nbsp;
			<input name='checkid$j' type='checkbox' id='$j' value='8' onclick='Chooserow($j)' $ActionSTRc>
			<LABEL for='$j'>删除</LABEL>&nbsp;</td>";
			$j++;
		echo"<td class='A0101'>&nbsp;
			<input name='checkid$j' type='checkbox' id='$j' value='16' onclick='Chooserow($j)' $ActionSTRd>
			<LABEL for='$j'>锁定</LABEL></td>";
			echo"</tr>";
		//////////////////////////
		$i++;$j++;
		} while ($myRow = mysql_fetch_array($Result));
  	}
//改变列选状态
$Sign2=$cols2==$i?"true":"false";
$Sign3=$cols3==$i?"true":"false";
$Sign4=$cols4==$i?"true":"false";
$Sign5=$cols5==$i?"true":"false";
$Sign6=$cols6==$i?"true":"false";
$Sign1=$Sign2;
echo"<script>
document.form1.ChooseCol1.checked=$Sign1;
document.form1.ChooseCol2.checked=$Sign2;
document.form1.ChooseCol3.checked=$Sign3;
document.form1.ChooseCol4.checked=$Sign4;
document.form1.ChooseCol5.checked=$Sign5;
document.form1.ChooseCol5.checked=$Sign6;
</script>";
?>
</table></td></tr></table>
<?php 
echo"<input name='IdCount' type='hidden' id='IdCount' value='$j'>";
//步骤5：
include "../model/subprogram/add_model_b.php";
echo "<br>";
?>
<script  type=text/javascript>
function upDateValue(){
	document.form1.action="popedom_updated.php";
	document.form1.submit();
	}
function ChooseTheCol(thisValue){
	var thisVALUE=Number(thisValue.value);	//确定所选取的列
	var CountId=Number(document.form1.IdCount.value);
	
	if(thisValue.checked==true){	//选取操作
		switch(thisVALUE){
			case 1:	//选取用户列，自动选取浏览列
				document.form1.ChooseCol2.checked=true;break;
			case 2://选取浏览列，自动选取用户列
				document.form1.ChooseCol1.checked=true;break;
			default:
				document.form1.ChooseCol1.checked=true;document.form1.ChooseCol2.checked=true;break;
			}
		for(var i=thisVALUE;i<CountId;i=i+6){eval("document.form1.checkid"+i).checked=true;
			switch(thisVALUE){
				case 1:	//选取用户列，自动选取浏览列
					var j=i+1;eval("document.form1.checkid"+j).checked=true;break;
				case 2://选取浏览列，自动选取用户列
					var j=i-thisVALUE+1;eval("document.form1.checkid"+j).checked=true;break;
				default:
					var j=i-thisVALUE+1;eval("document.form1.checkid"+j).checked=true;var j=i-thisVALUE+2;eval("document.form1.checkid"+j).checked=true;break;
				}
			}
		}
	else{//							//取消操作
		if(thisVALUE<3){//全部取消
			for (var i=0;i<form1.elements.length;i++){
				var e=form1.elements[i];
				if (e.type=="checkbox"){
					e.checked=false;
					} 
				}
			}
		else{			//取消当前列
			for(var i=thisVALUE;i<CountId;i=i+6){
				eval("document.form1.checkid"+i).checked=false;
				}
			}
		}
	}

function Chooserow(thisValue){
	var CountId=Number(document.form1.IdCount.value);
	var TempValue=thisValue%6;if(TempValue==0)TempValue=6;
	if(eval("document.form1.checkid"+thisValue).checked==true){//选取
		switch(TempValue){
			case 1:		var i=thisValue-TempValue+2;eval("document.form1.checkid"+i).checked=true;break;
			case 2:		var i=thisValue-TempValue+1;eval("document.form1.checkid"+i).checked=true;break;
			default:	var i=thisValue-TempValue+1;eval("document.form1.checkid"+i).checked=true;i++;eval("document.form1.checkid"+i).checked=true;break;
			}
		//判断列是否为全选
		//判断用户列是否全选	1
		var Col1=true;
		for(var i=1;i<CountId;i=i+6){
			if(eval("document.form1.checkid"+i).checked==false){
				Col1=false;
				break;
				}
			}
		eval("document.form1.ChooseCol"+1).checked=Col1;
		//判断浏览列是否全选	2
		var Col2=true;
		for(var i=2;i<CountId;i=i+6){
			if(eval("document.form1.checkid"+i).checked==false){
				Col2=false;
				break;
				}
			}
		eval("document.form1.ChooseCol"+2).checked=Col2;
		//判断当前列是否全选
		var ColTemp=true;
		for(var i=TempValue;i<CountId;i=i+6){
			if(eval("document.form1.checkid"+i).checked==false){
				ColTemp=false;
				break;
				}
			}
		eval("document.form1.ChooseCol"+TempValue).checked=ColTemp;
		//////////////////
		}
	else{														//取消
		if(TempValue==1 || TempValue==2){//全部取消
			for(var i=Number(thisValue-TempValue+1);i<Number(thisValue-TempValue+7);i++){
				eval("document.form1.checkid"+i).checked=false;
				}
			for(var j=1;j<7;j++){
				eval("document.form1.ChooseCol"+j).checked=false;
				}
			}
		//当前列取消全选
		eval("document.form1.ChooseCol"+TempValue).checked=false;
		}
	}
</script>
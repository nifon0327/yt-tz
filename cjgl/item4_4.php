<?php
//电信-zxq 2012-08-01
$Th_Col="序号|40|小组分类|60|小组班长|60|状态|40|小组编号|60|权限|40|成员|40|人数|40";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$nowInfo="当前:默认小组设置";
//步骤5：
echo"<table width='100%' border='0' cellspacing='0' cellpadding='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
	<td height='40px' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td>
	</tr></table>";
echo"<table width='100%' border='0' cellspacing='0' cellpadding='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td class='' width='380' valign='top'>";

echo"<table id='ListGroup' border='0' cellspacing='0' cellpadding='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr>";

	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A0111":"A0101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";
	$SumNums=0;
	$checkNums= mysql_query("SELECT * FROM $DataPublic.staffmain WHERE Estate=1 AND GroupId='0' AND BranchId=8",$link_id);
	$Nums=@mysql_num_rows($checkNums);
	$SumNums+=$Nums;
	echo"<tr align='center' id='Row1'>
	<td class='A0101' height='25' >1</td>";
	echo"<td class='A0101' colspan='5'>没有分配至小组的车间员工</td>";
	echo"<td class='A0101' onclick='SetAction(1,2,0)'><div class='yellowB'>查看</div></td><td class='A0101'>$Nums</td>";
$i=2;
$mySql="SELECT G.GroupId,G.GroupLeader,G.GroupName,G.Estate,M.Name 
FROM $DataIn.staffgroup G 
LEFT JOIN $DataPublic.staffmain M ON M.Number=G.GroupLeader
LEFT JOIN $DataPublic.branchdata B ON B.Id=G.BranchId 
WHERE 1 AND  B.TypeId=2  AND G.Estate=1 AND M.cSign='$Login_cSign' ORDER BY G.GroupId";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$GroupId=$myRow["GroupId"];
		$GroupLeader=$myRow["GroupLeader"];
		$GroupName=$myRow["GroupName"];
		$Name=$myRow["Name"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Date=$myRow["Date"];
		$Leader=$myRow["Leader"];
		//检查当天的员工数
		$checkNums= mysql_query("SELECT * FROM $DataPublic.staffmain WHERE Estate=1 AND GroupId='$GroupId' and cSign='$Login_cSign'",$link_id);
		$Nums=@mysql_num_rows($checkNums);
		$SumNums+=$Nums;
		echo"<tr align='center' id='Row$i'>
			<td class='A0101' height='25' >$i</td>";
		echo"<td class='A0101'>$GroupName</td>";
		echo"<td class='A0101'>$Name</td>";
		echo"<td class='A0101'>$Estate</td>";
		echo"<td class='A0101'>$GroupId</td>";
		//如果已经有登录帐号：
		$checkUserSql=mysql_query("SELECT Id FROM $DataIn.usertable WHERE Number='$GroupLeader'",$link_id);
		if($checkUserRow=mysql_fetch_array($checkUserSql)){
			echo"<td class='A0101' onclick='SetAction($i,1,$GroupLeader)'><div class='yellowB'>设置</div></td>";
			}
		else{//没有登录帐户
			echo"<td class='A0101'>无帐号</td>";
			}
		echo"<td class='A0101' onclick='SetAction($i,2,$GroupId)'><div class='yellowB'>查看</div></td>";
		echo"<td class='A0101'>$Nums</td>";
		echo"</tr>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	echo"<tr><td colspan='8' align='center' height='30' class='A1011'><div class='redB'>没有设置小组</div></td></tr>";
	}
echo"<tr align='center'><td class='A0101' height='25' colspan='7'>总人数</td><td class='A0101'>$SumNums</td></tr>";
echo "</table></td>
		<td class='A1101' id='SheetInfo' align='center' valign='top'>&nbsp;</td>
	</tr>
</table>";
	?>
<input name="TempValue" type="hidden" id="TempValue">
</form>

<iframe id="saveIframe" name="saveIframe" style="position:absolute;display:none;width:0px;height:0px"></iframe>
</body>
</html>
<script>
SetAction(1,2,0);
function MoveGroup(e,Num){
	var GroupId=e.value;
	var OldValue=document.form1.TempValue.value;
	//检查GroupId的合法性
	var CheckSTR=fucCheckNUM(GroupId,"");
	if(CheckSTR==0){
		alert("不是合法的小组编号!");
		e.value=OldValue;
		return false;
		}
	else{
		var url="item4_4_move.php?Num="+Num+"&GroupId="+GroupId;
		var ajax=InitAjax();
	　	ajax.open("GET",url,true);
		ajax.onreadystatechange =function(){
		　　if(ajax.readyState==4 && ajax.status ==200){
				if(ajax.responseText=="Y"){
					e.disabled=true;
					alert("小组调动成功!");
					}
				else{
					alert("小组调动不成功!");
					e.value=OldValue;

					}
				}
			}
	　	ajax.send(null);
		}
	}
function SetAction(theRow,Action,GroupLeader){
	//消除行背景色
	for (var i=1; i<ListGroup.rows.length; i++){   //遍历行
		if(i==theRow){
			ListGroup.rows[i].bgColor="#D9D9D9";
			SheetInfo.bgColor="#D9D9D9";
			}
		else{
			ListGroup.rows[i].bgColor="";
			}
		}
	//动态读取数据
	if(Action==1){//权限设置
		var url="item4_4_ajax.php?GroupLeader="+GroupLeader+"&fromModuleId=121";
		}
	else{//默认小组成员
		var url="item3_3_ajax.php?GroupId="+GroupLeader+"&Action="+Action+"&fromModuleId=121";
		}
	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
			var BackData=ajax.responseText;
			SheetInfo.innerHTML=BackData;
			}
		}
　	ajax.send(null);
	}


function Chooserow(thisV){
	//拆分参数nowIndex,NextCount,Grade,ModuleId
	var thisVALUE=thisV.value;
	var valueArray=thisVALUE.split(",");
	var nowIndex=valueArray[0]*1;		//当前元素ID
	var Grade=valueArray[1]*1;			//子功能权限:1-浏览	31-操作
	var ModuleId=valueArray[2]*1;		//子功能ID
	var PreIndex=nowIndex-1;		//前一个元素ID
        var LaterIndex=nowIndex+1;
	switch(Grade){
		case 1://OK 点击浏览
			if(!form1.elements[nowIndex].checked){
				//nowIndex++;
				form1.elements[LaterIndex].checked=false;
				}
		break;
		case 2://OK 点击：新增、更新、删除、锁定
			if(form1.elements[nowIndex].checked){
				form1.elements[PreIndex].checked=true;
				}
		break;
		}
	}


function ChooseCell(cellIndex){
	var thisVALUE=form1.elements[cellIndex].value*1;
	var IdCount=form1.IdCount.value*1;
	//如果选取操作
	switch(thisVALUE){
	case 1://全选/或取消浏览
		 if(form1.elements[cellIndex].checked){//选取，只选浏览列
			for(var j=9;j<=IdCount;j++){
				if(j%2!=0){
					form1.elements[j].checked=true;
					}
				}
			}
		else{//取消，则全部全消
			for(var j=9;j<=IdCount;j++){
				form1.elements[j].checked=false;
				}
			}
			break;
		case 2://全选或取消操作列
			 if(form1.elements[cellIndex].checked){//选取，只选浏览列
			for(var j=8;j<=IdCount;j++){
				form1.elements[j].checked=true;
				}
			}
		else{//取消，则只取消该列
			for(var j=9;j<=IdCount;j++){
				if(j%2!=1){
					form1.elements[j].checked=false;
					}
				}
			}
		break;
		}
	}

function SavePower(){
	//处理参数值
	document.form1.action="item4_4_updated.php";
	document.form1.target="saveIframe";
	document.form1.submit();
	alert("已更新");
	}
</script>
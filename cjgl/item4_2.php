<?php
//电信-zxq 2012-08-01
$Th_Col="序号|40|小组|60|小组班长|60|小组编号|60|成员|40|人数|40";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$nowInfo="当前:当天组员设置";
$checkDay=$checkDay==""?date("Y-m-d"):$checkDay;
//步骤5：
echo"<table width='100%' border='0' cellspacing='0' cellpadding='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
	<td width='560' class='' align='right'><input name='checkDay' type='text' id='checkDay' size='10' maxlength='10' value='$checkDay' onchange='document.form1.submit()'  onFocus='WdatePicker()' readonly />&nbsp;的小组成员</td>
	<td height='40px' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td>
	</tr></table>";
echo"<table width='100%' border='0' cellspacing='0' cellpadding='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td class='' width='300' valign='top'>";

echo"<table id='ListGroup' border='0' cellspacing='0' cellpadding='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr>";

	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A0101":"A0101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";
$i=1;
$mySql="SELECT G.GroupId,G.GroupLeader,G.GroupName,G.Estate,M.Name 
FROM $DataIn.staffgroup G 
LEFT JOIN $DataPublic.staffmain M ON M.Number=G.GroupLeader
LEFT JOIN $DataPublic.branchdata B ON B.Id=G.BranchId 
WHERE 1 AND  B.TypeId=2 AND G.Estate=1  ORDER BY G.GroupId";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$dGroupId=$myRow["GroupId"];
	$SumNums=0;
	do{
		$m=1;
		$GroupId=$myRow["GroupId"];
		$GroupName=$myRow["GroupName"];
		$Name=$myRow["Name"];
		$Date=$myRow["Date"];
		//检查当天的员工数
		$checkNums= mysql_query("SELECT * FROM $DataIn.sc1_memberset  S 
		LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
		WHERE S.Date='$checkDay' AND S.GroupId='$GroupId' AND M.cSign='$Login_cSign'",$link_id);
		$Nums=@mysql_num_rows($checkNums);
		$SumNums+=$Nums;
		echo"<tr align='center' id='Row$i'>
			<td class='A0101' height='25' >$i</td>";
		echo"<td class='A0101'>$GroupName</td>";
		echo"<td class='A0101'>$Name</td>";
		echo"<td class='A0101'>$GroupId</td>";
		echo"<td class='A0101' onclick='SetAction($i,2,$GroupId)'><div class='yellowB'>查看</div></td>";
		echo"<td class='A0101'>$Nums</td>";
		echo"</tr>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	echo"<tr align='center'><td class='A0101' height='25' colspan='5'>总人数</td><td class='A0101'>$SumNums</td></tr>";
	}
else{
	echo"<tr><td colspan='7' align='center' height='30' class='A1011'><div class='redB'>没有小组资料!</div></td></tr>";
	}

echo "</table></td>
		<td class='A1101' id='SheetInfo' align='center' valign='top'>&nbsp;</td>
	</tr>
</table>";
	?>
<input name="TempValue" type="hidden" id="TempValue">
</form>
</body>
</html>
<script>
SetAction(1,2,<?php    echo $dGroupId?>);
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
		var checkDay=document.form1.checkDay.value;
		var url="item4_2_move.php?Num="+Num+"&GroupId="+GroupId+"&checkDay="+checkDay;
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
function SetAction(theRow,Action,GroupId){
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
	var checkDay=document.form1.checkDay.value;
	var url="item4_2_ajax.php?GroupId="+GroupId+"&fromModuleId=121&checkDay="+checkDay+"&fromModuleId=119";
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
</script>
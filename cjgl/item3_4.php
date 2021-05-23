<?php
//电信-zxq 2012-08-01
$Th_Col="序号|30|分类|50|班长|50|排名|30|图表|30";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$nowInfo="当前:效率查询";
//步骤5：
echo"<table width='100%' border='0' cellspacing='0' cellpadding='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
	<td height='40px' class='' align='right'><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td>
	</tr></table>";
echo"<table width='100%' border='0' cellspacing='0' cellpadding='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td class='' width='190' valign='top'>";

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
$mySql="SELECT G.Id,G.Leader,G.Remark,G.Estate,M.Name FROM $DataIn.sc1_grouping G 
LEFT JOIN $DataPublic.staffmain M ON M.Number=G.Leader
WHERE 1 ORDER BY G.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Remark=$myRow["Remark"];
		$Name=$myRow["Name"];
		$Leader=$myRow["Leader"];
		echo"<tr align='center' id='Row$i'>
			<td class='A0101' height='25' >$i</td>";
		echo"<td class='A0101'>$Remark</td>";
		echo"<td class='A0101'>$Name</td>";
		echo"<td class='A0101'>&nbsp;</td>";
		echo"<td class='A0101' onclick='SetAction($i,$Leader)'><div class='yellowB'>查看</div></td>";
		echo"</tr>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	echo"<tr><td colspan='5' align='center' height='30' class='A1011'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
	}

echo "</table></td>
		<td class='A1101' id='SheetInfo' align='center' valign='middle'>&nbsp;</td>
	</tr>
</table>";?>
</form>
</body>
</html>
<script>
function SetAction(theRow,Leader){
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
	SheetInfo.innerHTML="<img src='item3_4_img.php?Leader="+Leader+"'>";
	//写入新的图表
	/*动态读取数据
	var url="item3_3_ajax.php?GroupId="+GroupId;
	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
			var BackData=ajax.responseText;
			SheetInfo.innerHTML=BackData;
			}
		}
　	ajax.send(null);*/
	}
</script>
<?php
/*
echo"<table width='100%' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#D9D9D9'>
	<td height='40px' align='right' class='A1011'><input name='NowInfo' type='text' id='NowInfo' value='当前:效率查询' class='text' disabled></td></tr>";
echo"<tr><td align='center' height='30' class='A0111'>
<img src='item3_4_img.php'>
</td></tr></table>";
*/
?>
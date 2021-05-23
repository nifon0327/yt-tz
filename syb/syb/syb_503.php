<?php 
//电信
//代码共享-EWEN 2014-05-19
//就学补助
$MonthSTR=$Month==""?"":" AND A.Month='$Month'";
switch($DataT){
	case "Y":$DataTSTR="已结付记录"; $EstateSTR=" AND A.Estate=0"; break;
	case "W":$DataTSTR="未结付记录"; $EstateSTR=" AND A.Estate=3";break;
	case "A":$DataTSTR="全部记录"; $EstateSTR=" AND (A.Estate=0 OR A.Estate=3)";break;
	}
$ShowInfo=$ItemName." ".$Month.$DataTSTR."：".$Remark;
$Parameters=$Parameters==""?"":" AND A.TypeID IN($Parameters)";
echo"
<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:480px' align='center'>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td width='50' height='20' class='A1011'>序号</td>
<td width='60' class='A1001'>员工姓名</td>
<td width='60' class='A1001'>小孩姓名</td>
<td width='40' class='A1001'>性别</td>
<td width='40' class='A1001'>凭证</td>
<td width='150' class='A1001'>备注</td>
<td width='150' class='A1001'>目前就读学校</td>
<td width='40' class='A1001'>状态</td>
<td width='80' class='A1001'>请款月份</td>
<td width='100' class='A1001'>请款金额</td>
</tr>
<tr>
<td colspan='10' height='450px'>
<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px' align='center'>
";
//读取记录
$checkSql=mysql_query("
					SELECT   D.Name,B.ChildName,B.Sex,A.Attached,A.Remark,C.Name AS ClassName,A.Estate,A.Month,A.Amount,A.Id
					FROM  $DataIn.cw19_studyfeesheet   A 
					LEFT JOIN  $DataPublic.childinfo B  ON B.Id=A.cId
					LEFT JOIN $DataPublic.childclass C ON C.Id=A.NowSchool
					LEFT JOIN $DataPublic.staffmain D ON D.Number=B.Number
					WHERE 1 AND A.Month>='2008-07' $MonthSTR
					$EstateSTR",$link_id);
$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	 $Dir=anmaIn("download/childinfo/",$SinkOrder,$motherSTR);
	do{
		//add by cabbage 20141212 app採集單月紀錄
		$detailList[$i - 1] = $checkRow;

		$Id=$checkRow["Id"];
		$Name=$checkRow["Name"];
		$ChildName=$checkRow["ChildName"];
		$Sex=$checkRow["Sex"]==0?"女":"男";
		if($checkRow["Attached"]!=""){
		
			//add by cabbage 20141212 app紀錄附件的檔案路徑
			$detailList[$i - 1]["FilePath"] = "/download/childinfo/".$checkRow["Attached"];
		
			$Attached=anmaIn($checkRow["Attached"],$SinkOrder,$motherSTR);
		   	$Attached="<a href=\"../../public/openorload.php?d=$Dir&f=$Attached&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
            }
		$Remark=$checkRow["Remark"];
		$ClassName=$checkRow["ClassName"];
		$Estate=$checkRow["Estate"]==3?"<span class=\"redB\">未付</span>":"<span class=\"greenB\">已付</span>";
		$Month=$checkRow["Month"];
		$Amount=sprintf("%.2f",$checkRow["Amount"]);
		$SumAmount+=$Amount;
		$Amount=number_format($Amount);
		echo"
			<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td width='50' height='20' class='A0111' align='center'>$i</td>
			<td width='60'  class='A0101'>$Name</td>
			<td width='60' class='A0101'>$ChildName</td>
			<td width='40' class='A0101' align='center'>$Sex</td>
			<td width='40' class='A0101' align='center'>$Attached</td>
			<td width='150' class='A0101' align='center'>$Remark</td>
			<td width='150' class='A0101' align='center'>$ClassName</td>
			<td width='40' class='A0101'  align='center'>$Estate</td>
			<td width='80' class='A0101' align='center'>$Month</td>
			<td width='100' class='A0100' align='right'>$Amount</td>
			</tr>";
		$i++;
		}while($checkRow=mysql_fetch_array($checkSql));
	}
for($j=$i;$j<27;$j++){//补空行
	echo"
	<tr bgcolor='#ECEAED' align='center' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
	<td width='50' height='20' class='A0111'>$j</td>
	<td width='60'  class='A0101'>&nbsp;</td>
	<td width='60'  class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='150' class='A0101'>&nbsp;</td>
	<td width='150' class='A0100'>&nbsp;</td>
	<td width='40' class='A0101'>&nbsp;</td>
	<td width='80' class='A0101'>&nbsp;</td>
	<td width='100' class='A0100'>&nbsp;</td>
	</tr>";
	}
$SumAmount=number_format(sprintf("%.0f",$SumAmount));
echo"</table>
</div>
</td>
</tr>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td height='20' class='A0111' colspan='2'>合计</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0101' align='right'>¥$SumAmount</td>
</tr>
</table>
";
?>
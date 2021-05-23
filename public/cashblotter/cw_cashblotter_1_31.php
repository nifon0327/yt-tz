<?php
//车辆费用			OK
//ewen 2013-09-04 OK
//读取记录
echo"<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;' align='center'>
<tr bgcolor='#CCCCCC' align='center' >
<td width='50' height='20' class='A1111'>序号</td>
<td width='60' class='A1101'>员工姓名</td>
<td width='60' class='A1101'>小孩姓名</td>
<td width='40' class='A1101'>性别</td>
<td width='40' class='A1101'>凭证</td>
<td width='150' class='A1101'>备注</td>
<td width='150' class='A1101'>目前就读学校</td>
<td width='40' class='A1101'>状态</td>
<td width='80' class='A1101'>请款月份</td>
<td width='100' class='A1101'>请款金额</td>
</tr>
";
//读取记录
$checkSql=mysql_query("
					SELECT   D.Name,B.ChildName,B.Sex,A.Attached,A.Remark,C.Name AS ClassName,A.Estate,A.Month,A.Amount,A.Id
					FROM  $DataIn.cw19_studyfeesheet   A 
					LEFT JOIN  $DataPublic.childinfo B  ON B.Id=A.cId
					LEFT JOIN $DataPublic.childclass C ON C.Id=A.NowSchool
					LEFT JOIN $DataPublic.staffmain D ON D.Number=B.Number
					WHERE 1 AND A.Mid='$Id_Remark' ",$link_id);
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
	else{
	
	}
echo "</table>";
echo "</div>";
?>
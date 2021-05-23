<?php   
//电信-zxq 2012-08-01
include "cj_chksession.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
include "../basic/parameter.inc";
$Th_Col="序号|30|编号|40|员工姓名|60|入职日期|70|短号|50|移动电话|80|当前小组编号|90|默认小组|100|考勤|40";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$Cells=$Count/2;
//提示行
echo"<table border='0' cellspacing='0' cellpadding='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
<tr >";
//输出表格标题
for($i=0;$i<$Count;$i=$i+2){
	$Class_Temp=$i==0?"A0111":"A0101";
	$j=$i;
	$k=$j+1;
	echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
	}
echo"</tr>";
$i=1;

$mySql="SELECT M.Number,M.Name,M.ComeIn,S.Dh,S.Mobile,M.GroupId,M.KqSign,G.GroupName  
FROM $DataIn.sc1_memberset C
LEFT JOIN $DataPublic.staffmain M ON M.Number=C.Number
LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
WHERE 1 AND C.GroupId='$GroupId' AND C.Date='$checkDay' ORDER BY M.KqSign DESC,M.GroupId,M.ComeIn";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$ComeIn=$myRow["ComeIn"];
		$Dh=$myRow["Dh"];
		$Mobile=$myRow["Mobile"];
		$GroupName=$myRow["GroupName"];
		$KqSign=$myRow["KqSign"]==1?"<span class='greenB'>是</span>":"<span class='redB'>否</span>";
		echo"<tr align='center' id='Row$i'>";
		echo"<td height='25' class='A0111'>$i</td>";
		echo"<td class='A0101'>$Number</td>";
		echo"<td class='A0101'>$Name</td>";
		echo"<td class='A0101'>$ComeIn</td>";
		echo"<td class='A0101'>$Dh</td>";
		echo"<td class='A0101'>$Mobile</td>";

		//权限
		$checkPower=mysql_fetch_array(mysql_query("SELECT Action FROM $DataIn.sc4_upopedom WHERE UserId='$Login_Id' AND ModuleId='$fromModuleId'",$link_id));
		$SubAction=$checkPower["Action"];
		$Disabled=$SubAction==31?"":"disabled";
		echo"<td class='A0101'><input name='Move$i' type='text' id='Move$i' value='$GroupId' size='5' onChange=MoveGroup(this,$Number) onFocus=toTempValue(this.value) $Disabled></td>";
		echo"<td class='A0101'>$GroupName</td>";
		echo"<td class='A0101' align='center'>$KqSign</td>";		
		echo"</tr>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	echo"<tr><td colspan='$Cells' align='center' height='30' class='A0111'><div class='redB'>该小组没有设置".$checkDay."的小组成员.</div></td></tr>";
	}
	
echo "</table>";
	?>

<?php   
//电信-zxq 2012-08-01
include "cj_chksession.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
include "../basic/parameter.inc";
if($Action==2){
	$Th_Col="序号|40|员工编号|60|员工姓名|60|入职日期|80|短号|60|移动电话|100|小组编号|70";
	}
else{
	$Th_Col="序号|40|员工编号|60|员工姓名|60|入职日期|80|短号|60|移动电话|100";
	}
$Field=explode("|",$Th_Col);
$Count=count($Field);
$Cells=$Count/2;
//提示行
echo"<table border='0' cellspacing='0' cellpadding='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#D9D9D9'>
<tr >";
//输出表格标题
for($i=0;$i<$Count;$i=$i+2){
	$Class_Temp=$i==0?"A0111":"A0101";
	$j=$i;
	$k=$j+1;
	echo"<td width='$Field[$k]' class='' height='25px'><div align='center'>$Field[$j]</div></td>";
	}
echo"</tr>";
$i=1;
$mySql="SELECT M.Number,M.Name,M.ComeIn,S.Dh,S.Mobile,M.GroupId 
FROM $DataPublic.staffmain M
LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number 
LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId 
WHERE 1 AND M.GroupId='$GroupId' AND M.Estate=1 AND  B.TypeId=2 AND M.cSign='$Login_cSign' ORDER BY M.ComeIn";//部门ID要大于4
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$ComeIn=$myRow["ComeIn"];
		$Dh=$myRow["Dh"];
		$Mobile=$myRow["Mobile"];
		echo"<tr align='center' id='Row$i'>";
		echo"<td height='25' class='A0111'>$i</td>";
		echo"<td class='A0101'>$Number</td>";
		echo"<td class='A0101'>$Name</td>";
		echo"<td class='A0101'>$ComeIn</td>";
		echo"<td class='A0101'>$Dh</td>";
		echo"<td class='A0101'>$Mobile</td>";
		if($Action==2){
			//权限
			$checkPower=mysql_fetch_array(mysql_query("SELECT Action FROM $DataIn.sc4_upopedom WHERE UserId='$Login_Id' AND ModuleId='$fromModuleId'",$link_id));
			$SubAction=$checkPower["Action"];
			$Disabled=$SubAction==31?"":"disabled";
			echo"<td class='A0101'><input name='Move$i' type='text' id='Move$i' value='$GroupId' size='5' onChange=MoveGroup(this,$Number) onFocus=toTempValue(this.value) $Disabled></td>";
			}
		echo"</tr>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	echo"<tr><td colspan='$Cells' align='center' height='30' class='A0111'><div class='redB'>该小组没有设置小组成员.</div></td></tr>";
	}
	
echo "</table>";
	?>

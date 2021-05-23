<?php 
//代码、数据库共享-EWEN 2012-09-19
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=8;
$tableMenuS=550;
$tableWidth=680;
ChangeWtitle("$SubCompany 门禁权限");
$funFrom="mj_powertype";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|门禁权限类型|100|状态|40|更新日期|80|操作员|60";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,5,6,7,8";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
$SearchRows="";
echo "<select name='PowerType' id='PowerType' onchange='document.form1.submit();'>";
$checkPTSql=mysql_query("SELECT * FROM $DataPublic.accessguard_powertype WHERE Id>0 AND Estate='1' ORDER BY Id",$link_id);
$TempParameters="";
if($checkPTRow=mysql_fetch_array($checkPTSql)){
	do{
		$PTID=$checkPTRow["Id"];
		$PTName=$checkPTRow["TypeName"];
		$PowerType=$PowerType==""?$PTID:$PowerType;
		if($PowerType==$PTID){
			echo"<option value='$PTID' selected>$PTName</option>";
			$TempParameters=$checkPTRow["Parameters"];
			$SearchRows="AND PowerType='$PowerType' ";
			}
		else{
			echo"<option value='$PTID'>$PTName</option>";
			}
		}while($checkPTRow=mysql_fetch_array($checkPTSql));
	}
echo"</select>";
$PowerData="accessguard_powerfixed";
//如果是部门或职位
if($TempParameters!=""){//如果部门或职位不为空
	echo "&nbsp;&nbsp;<select name='Parameters' id='Parameters' onchange='document.form1.submit();'>";
	if($TempParameters=="accessguard_user"){
		$checkSql=mysql_query("SELECT A.Number AS Id,B.Name FROM $DataPublic.$TempParameters A LEFT JOIN $DataPublic.staffmain B ON B.Number=A.Number WHERE A.Estate=1 AND A.PowerType='1' AND B.Estate='1' ORDER BY B.Name",$link_id);
		$PowerData="accessguard_powermyself";
		}
	else{
		$checkSql=mysql_query("SELECT Id,Name FROM $DataPublic.$TempParameters WHERE Estate=1 ORDER BY Id",$link_id);
		}
	if($checkRow=mysql_fetch_array($checkSql)){
		do{
			$TempId=$checkRow["Id"];
			$TempName=$checkRow["Name"];
			$Parameters=$Parameters==""?$TempId:$Parameters;
			if($Parameters==$TempId){
				echo"<option value='$TempId' selected>$TempName</option>";
				if($TempParameters=="accessguard_user"){
					$SearchRows=" AND Number='$TempId'";
					}
				else{
					$SearchRows.=" AND Parameters='$TempId'";
					}
				}
			else{
				echo"<option value='$TempId'>$TempName</option>";
				}
			}while($checkRow=mysql_fetch_array($checkSql));
		}
	echo "</select>";
	}
else{
	echo"<input name='Parameters' type='hidden' id='Parameters' value='0' />";
	}
//步骤5：
include "../model/subprogram/read_model_5.php";

//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
echo"
<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
<tr align='center' bgcolor='#CCCCCC'>
<td class='A1111' rowspan='2' width='80'>门禁名称</td>
 <td class='A1101' colspan='2' height='20'>星期一</td>
 <td class='A1101' colspan='2'>星期二</td>
 <td class='A1101' colspan='2'>星期三</td>
 <td class='A1101' colspan='2'>星期四</td>
 <td class='A1101' colspan='2'>星期五</td>
 <td class='A1101' colspan='2'>星期六</td>
 <td class='A1101' colspan='2'>星期日</td>
</tr>
<tr align='center' bgcolor='#CCCCCC'>
<td class='A0101' height='20'>起始时间</td><td class='A0101'>终止时间</td>
<td class='A0101'>起始时间</td><td class='A0101'>终止时间</td>
<td class='A0101'>起始时间</td><td class='A0101'>终止时间</td>
<td class='A0101'>起始时间</td><td class='A0101'>终止时间</td>
<td class='A0101'>起始时间</td><td class='A0101'>终止时间</td>
<td class='A0101'>起始时间</td><td class='A0101'>终止时间</td>
<td class='A0101'>起始时间</td><td class='A0101'>终止时间</td>
</tr>
";
//门禁检查
$checkMJSql=mysql_query("SELECT * FROM $DataPublic.accessguard_door WHERE Estate='1' ORDER BY Id",$link_id);
if($checkMJRow=mysql_fetch_array($checkMJSql)){
	do{
		$DoorId=$checkMJRow["Id"];
		$DoorName=$checkMJRow["DoorName"];
		//检查此门禁的权限
		echo "<tr align='center'><td class='A0111' height='25' bgcolor='#CCCCCC'>$DoorName</td>";
		for($j=1;$j<8;$j++){
			$checkPowerSql=mysql_query("SELECT DATE_FORMAT(TimeS,'%H:%i') AS TimeS,DATE_FORMAT(TimeE,'%H:%i') AS TimeE FROM $DataPublic.$PowerData WHERE DoorId='$DoorId' AND WeekDay='$j' $SearchRows",$link_id);
			if($checkPowerRow=mysql_fetch_array($checkPowerSql)){
				echo"<td class='A0101' ondblclick='ToTextbox(this,$DoorId,$j,1)'>$checkPowerRow[TimeS]</td><td class='A0101' ondblclick='ToTextbox(this,$DoorId,$j,2)'>$checkPowerRow[TimeE]</td>";
				}
			else{
				echo"<td class='A0101' ondblclick='ToTextbox(this,$DoorId,$j,1)'>0:00</td><td class='A0101' ondblclick='ToTextbox(this,$DoorId,$j,2)'>0:00</td>";
				}
			}
		echo "</tr>";
		}while($checkMJRow=mysql_fetch_array($checkMJSql));
	}
//步骤7：
echo '</div>';
echo "
<tr>
<td class='A0111' height='25' bgcolor='#CCCCCC' align='center'>说明</td>
<td class='A0101' colspan='14'>起始时间与终止时间一致时，没有权限。</td>
</tr>
</table>";
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
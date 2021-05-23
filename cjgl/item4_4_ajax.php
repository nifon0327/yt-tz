<?php   
//电信-zxq 2012-08-01
include "cj_chksession.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
include "../basic/parameter.inc";
//步骤2：
$checkYg=mysql_fetch_array(mysql_query("SELECT M.Name,U.Id AS User 
FROM $DataPublic.staffmain M
LEFT JOIN $DataIn.usertable U ON U.Number=M.Number
WHERE M.Number='$GroupLeader' AND U.uType='0' LIMIT 1",$link_id));
$Name=$checkYg["Name"];
$User=$checkYg["User"];
echo"
<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' align='center'>
<tr>
<td class='A0110' height='35' width='150' align='right'>$GroupLeader $Name</td>
<td class='A0101' width='150'>&nbsp;权限表</td>
<td class='A0101' width='120' align='center'>浏览权限</td>
<td class='A0101' width='100' align='center'>操作权限</td>
</tr>
<tr>
<td class='A0111' height='25' width='130' align='center'>主项目</td>
<td class='A0101' width='130' align='center'>子项目</td>
<td align='center' width='100' class='A0101' onclick='ChooseCell(9)'><input name='checkid1' type='checkbox' id='checkid1' value='1'>全选浏览</td>
<td align='center' width='100' class='A0101' onclick='ChooseCell(10)'><input name='checkid2' type='checkbox' id='checkid2' value='2'>全选操作</td>
</tr>
";
$Result = mysql_query("SELECT ModuleId,ModuleName FROM $DataPublic.sc4_funmodule WHERE 1 and Place=1 order by Place,OrderId",$link_id);
$i=1;
$j=11;//复选框起始序号
if ($myrow = mysql_fetch_array($Result)) {
	do {
		$m=1;
		$RowsA=1;
		$Id=$myrow["Id"];
		$ModuleId=$myrow["ModuleId"];
		$ModuleName=$myrow["ModuleName"];
		
		$Row2="";
		//子项目////////////////////////////////////////////////////////////////////////////////////////////
		$Result2 = mysql_query("SELECT M.dModuleId,F.ModuleName FROM $DataPublic.sc4_modulenexus M LEFT JOIN $DataPublic.sc4_funmodule F ON F.ModuleId=M.dModuleId WHERE 1 and M.ModuleId=$ModuleId and F.Estate=1 order by M.ModuleId,M.OrderId",$link_id);		
		if($myrow2 = mysql_fetch_array($Result2)){
			$RowsB=0;
			do{
				$n=$m;
				$ModuleId2=$myrow2["dModuleId"];
				$ModuleName2=$myrow2["ModuleName"];
				$Parameter=$myrow2["Parameter"];
				if($RowsA!=1){//非首行时，要开新行
					$Row2.="<tr>";
					}
				//权限检查
				$ActionSTR1="";$ActionSTR31="";
				$checkResult = mysql_query("SELECT P.Id,P.Action FROM $DataIn.sc4_upopedom P
				LEFT JOIN $DataIn.usertable U ON U.Id=P.UserId
				WHERE 1 AND U.Number=$GroupLeader AND U.uType=0 AND P.ModuleId=$ModuleId2 AND P.Action>0 ORDER BY P.Id LIMIT 1",$link_id);
				if($chexkRow = mysql_fetch_array($checkResult)){
					$Action=$chexkRow["Action"];
					$ActionSTR1="checked";
					$ActionSTR31=$Action==31?"checked":"";
					}
				//输出子项目
				$Row2.="<td class='A0101' width='130' height='20'>&nbsp;$ModuleName2($ModuleId2)</td>";//子项目名称
				$Row2.="<td class='A0101' width='80' align='center'><input name='checkid[$j]' type='checkbox' id='checkid$j' value='$j,1,1,$ModuleId2' onchange='Chooserow(this)' $ActionSTR1>允许浏览</td>";
				$Pre4=$j;$j++;
				$Row2.="<td class='A0101' width='80' align='center'><input name='checkid[$j]' type='checkbox' id='checkid$j' value='$j,2,31,$ModuleId2' onchange='Chooserow(this)' $ActionSTR31>允许操作";
				$j++;
				$Row2.="</td></tr>";
				$RowsA++;$RowsB++;
				}while ($myrow2 = mysql_fetch_array($Result2));
				$Row1="<td class='A0111' width='130' height='20' rowspan='$RowsB'>&nbsp; $i 、$ModuleName($ModuleId)</td>";//主项目内容ID
			echo $Row1.$Row2;
		//子项目////////////////////////////////////////////////////////////////////////////////////////////
			}
		else{//没有子项目
			echo"<td class='A0101' width='130'>&nbsp;未设定</td><td class='A0101' width='80'>&nbsp;</td><td class='A0101' width='80'>&nbsp;</td></tr>";//子项目
			}
		/*if($RowsB>1){//重写首行的并行数
			//echo"<script>ListTable$i.rows[0].cells[0].rowSpan=$RowsB;</script>";//}*/
		
		$i++;
		} while ($myrow = mysql_fetch_array($Result));
  	}
//权限判断
$checkPower=mysql_fetch_array(mysql_query("SELECT Action FROM $DataIn.sc4_upopedom WHERE UserId='$Login_Id' AND ModuleId='$fromModuleId'",$link_id));
$SubAction=$checkPower["Action"];
$UpdateButton=$SubAction==31?"<input class='ButtonH_25' type='button'  name='Submit' value='更新' onclick='SavePower()'>":"&nbsp;";
			
echo"<tr>
<td class='A0110' height='35' width='130'>&nbsp;</td>
<td class='A0100' width='130'>&nbsp;</td>
<td width='100' class='A0100'>&nbsp;</td>
<td align='center' width='100' class='A0101'>$UpdateButton</td>
</tr>
</table>";
$j++;
echo"<input name='IdCount' type='hidden' id='IdCount' value='$j'>";
echo"<input name='User' type='hidden' id='User' value='$User'>";
?>

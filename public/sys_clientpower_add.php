<?php 
//$DataIn.电信---yang 20120801
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增客户用户权限");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";//需处理
//步骤3：
$tableWidth=909;$tableMenuS=700;$ColsNumber=6;
$CheckFormURL="thisPage";
//全部客户用户
$CheckSql = mysql_query("SELECT A.Id,A.uName,B.Nickname,C.Forshort 
FROM $DataIn.usertable A
LEFT JOIN $DataIn.linkmandata B ON B.Id=A.Number
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=B.CompanyId
WHERE 1 AND A.uType=2 AND C.Estate=1 AND A.Estate=1 
GROUP BY A.Id ORDER BY C.Forshort,A.uName,A.Id DESC
",$link_id);
$i=1;
if($CheckRow = mysql_fetch_array($CheckSql)){
	$SelectCode="<select name='UserId' id='UserId' style='width: 250px;' onchange='document.form1.submit();'>";
	do{
		$Id=$CheckRow["Id"];
		$UserId=$UserId==""?$Id:$UserId;
		$uName=$CheckRow["uName"];
		$Forshort=$CheckRow["Forshort"];
		$Nickname=$CheckRow["Nickname"];				
		if ($UserId==$Id){
			$SelectCode.="<option value='$Id' selected>$i $Forshort - $uName/$Nickname</option>";
			$SearchRows=" AND A.UserId=$UserId";
			}
		else{
			$SelectCode.="<option value='$Id'>$i $Forshort - $uName/$Nickname</option>";
			}
		$i++;
		}while($CheckRow = mysql_fetch_array($CheckSql));
	echo"</select>";
	}
else{
	$SaveSTR="NO";
	}
include "../model/subprogram/add_model_t.php";
$MergeRows=$MergeRows==""?0:$MergeRows;
$sumCols=$sumCols==""?"":$sumCols;
echo"<input name='sumCols' type='hidden' id='sumCols' value='$sumCols'><input name='MergeRows' type='hidden' id='MergeRows' value='$MergeRows'>";
//步骤4：需处理
?>
<table border="0" width="909" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' >
    <tr>
    	<td width="100" rowspan="2" valign="top" class='A0010' align="right">可选功能列表：</td>
      <td class='A1111'width="50" height="25"  align="center">选项</td>
      <td class='A1101' width="50" align="center">序号</td>
      <td class='A1101' width="70" align="center">功能ID</td>
      <td class='A1101' width="150" align="center">功能</td>
	  <td class='A1101' width="460" align="center">备注</td>
      <td class='A0001' rowspan="2"  width="20">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="5" class='A0001'>
	  <div style='width:790;height:380;overflow-x:hidden;overflow-y:scroll'>
	  <?php 
	$Result = mysql_query("SELECT * FROM $DataIn.sys_clientfunmodule WHERE Estate=1 ORDER BY Oby,Id",$link_id);
	if($myrow = mysql_fetch_array($Result)) {
		$i=1;
		do{
			$Id=$myrow["Id"];
			$ModuleId=$myrow["ModuleId"];
			$ModuleName=$myrow["ModuleName"];
			$Remark=$myrow["Remark"];
			$Result2 = mysql_query("SELECT * FROM $DataIn.sys_clientfunpower WHERE ModuleId=$ModuleId and UserId=$UserId",$link_id);
			if($myrow2 = mysql_fetch_array($Result2)){
				echo"
				<table border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>
				<tr onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>
				<td class='A0111' height='30' width='48' align='center'><input name='checkid[]' type='checkbox' id='checkid$i' value='$Id' checked disabled></td>
				<td class='A0101' width='48' align='center'>$i</td>
				<td class='A0101' width='68' align='center'>$ModuleId</td>
				<td class='A0101' width='148'>$ModuleName</td>
				<td class='A0101' width='458'>$Remark</td>
				</tr></table>";
				echo"<script> 
				 chooseRow(ListTable$i,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber)
				 </script>";
				}
			else{
				echo"
				<table border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>
				<tr onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>
				<td class='A0111' height='30' width='48' align='center'><input name='checkid[]' type='checkbox' id='checkid$i' value='$Id' disabled></td>
				<td class='A0101' width='48' align='center'>$i</td>
				<td class='A0101' width='68' align='center'>$ModuleId</td>
				<td class='A0101' width='148'>$ModuleName</td>
				<td class='A0101' width='458'>$Remark</td>
				</tr></table>";
				}
			$i++;
			}while ($myrow = mysql_fetch_array($Result));
		}
	  ?>
	  </div>
	  </td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script  type=text/javascript>
function CheckForm(){
	//解除
	for (var i=0;i<form1.elements.length;i++){
		var e=form1.elements[i];
		var NameTemp=e.name;
		var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
		if (e.type=="checkbox" && Name!=-1){
			e.disabled=false;
			} 
		}
	document.form1.action="sys_clientpower_save.php";
	document.form1.submit();
	}
</script>

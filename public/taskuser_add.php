<?php 
//电信-joseph
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增特殊功能权限");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";//需处理
//步骤3：
$tableWidth=850;$tableMenuS=500;$ColsNumber=5;
$CheckFormURL="thisPage";

//全部有效的用户员工资料表
$checkSql = "
SELECT * FROM (
SELECT B.Number,C.Name,C.cSign,C.BranchId,C.JobId,D.CShortName,E.Name AS Branch 
	FROM $DataIn.usertable B
	LEFT JOIN $DataPublic.staffmain C ON C.Number=B.Number 
	LEFT JOIN $DataPublic.companys_group D ON D.cSign=C.cSign
	LEFT JOIN $DataPublic.branchdata E ON E.Id=C.BranchId
	WHERE B.Estate='1' AND C.Estate='1' AND B.uType=1
UNION
SELECT B.Number,C.Name,'0' AS cSign,'0' AS BranchId,'0' AS JobId,'外部人员' AS CShortName,'其他' AS Branch
	FROM $DataIn.usertable B
	LEFT JOIN $DataIn.ot_staff C ON C.Number=B.Number 
	WHERE B.Estate='1' AND  C.Estate>0 AND B.uType=4
)Z ORDER BY cSign DESC,BranchId,JobId,convert(Name using gbk) ASC
";
$checkResult = mysql_query($checkSql); 
$i=1;
while ( $checkRow = mysql_fetch_array($checkResult)){
	$theUserId=$checkRow["Number"];
	$UserId=$UserId==""?$theUserId:$UserId;
	$CShortName=$i." ".$checkRow["CShortName"]."-".$checkRow["Branch"]."-".$checkRow["Name"];
	if ($UserId==$theUserId){
		$OptionStr.="<option value='$theUserId' selected>$CShortName</option>";
		}
	else{
		$OptionStr.="<option value='$theUserId'>$CShortName</option>";
		}
	$i++;
	}
//下拉选框处理
$SelectCode="<select name='UserId' id='UserId'  onchange='document.form1.submit();'>$OptionStr</select>";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<input name="MergeRows" type="hidden" id="MergeRows">
<input name="sumCols" type="hidden" id="sumCols">
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
    	<td colspan="6" class='A0011'>&nbsp;</td>
	</tr>
    <tr>
    	<td width="150" rowspan="2" valign="top" class='A0010'><p align="right">可选功能列表：<br> 
      </td>
      <td class='A1111' height="25" width="40" align="center">选项</td>
      <td class='A1101' width="40" align="center">序号</td>
      <td class='A1101' width="60" align="center">功能ID</td>
      <td class='A1101' width="550" align="center">功能描述</td>
      <td class='A0001'width="10">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="5" class='A0001'>
	  <div style='width:696;height:300;overflow-x:hidden;overflow-y:scroll'>
	  <?php 
	$Result = mysql_query("SELECT * FROM $DataPublic.tasklistdata WHERE Estate=1 ORDER BY Id",$link_id);
	if($myrow = mysql_fetch_array($Result)) {
		$i=1;
		do{
			$ItemId=$myrow["ItemId"];
			$Title=$myrow["Title"];
			$Result2 = mysql_query("SELECT * FROM $DataIn.taskuserdata WHERE ItemId=$ItemId and UserId=$UserId",$link_id);
			if($myrow2 = mysql_fetch_array($Result2)){
				echo"
				<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>
				<tr onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>
				<td class='A0111' height='25' width='42' align='center'><input name='checkid[]' type='checkbox' id='checkid$i' value='$ItemId' checked disabled></td>
				<td class='A0101' width='41' align='center'>$i</td>
				<td class='A0101' width='61' align='center'>$ItemId</td>
				<td class='A0101' width='550'>$Title</td>
				<td >&nbsp;</td></tr></table>";
				echo"<script> 
				 chooseRow(ListTable$i,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber)
				 </script>";
				}
			else{
				echo"
				<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>
				<tr onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>
				<td class='A0111' height='25' width='42' align='center'><input name='checkid[]' type='checkbox' id='checkid$i' value='$ItemId' disabled></td>
				<td class='A0101' width='41' align='center'>$i</td>
				<td class='A0101' width='61' align='center'>$ItemId</td>
				<td class='A0101' width='550'>$Title</td>
				<td >&nbsp;</td></tr></table>";
				}
			$i++;
			}while ($myrow = mysql_fetch_array($Result));
		}
		echo"<input name='IdCount' type='hidden' id='IdCount' value='$i'>";
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
	document.form1.action="taskuser_save.php";
	document.form1.submit();
	}
</script>

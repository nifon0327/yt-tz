<?php 
//电信-joseph
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：
$funFrom="taskdata";
ChangeWtitle("$SubCompany 更新特殊功能的权限");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_set";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤4:
$ColsNumber=5;
$tableWidth=850;$tableMenuS=500;
$checkRow=mysql_fetch_array(mysql_query("SELECT Title FROM $DataPublic.tasklistdata WHERE 1 AND ItemId=$ItemId order by Id DESC",$link_id));
$Title=$checkRow["Title"];
$SelectCode="特殊功能权限设置: $Title $ItemId";
$CustomFun="<span onClick='All_elects(\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",$ColsNumber)' $onClickCSS>全选</span>&nbsp;<span onClick='Instead_elects(\"$theDefaultColor\",\"$theDefaultColor\",\"$theMarkColor\",$ColsNumber)' $onClickCSS>反选</span>&nbsp;";
$CheckFormURL="thisPage";
include "../model/subprogram/add_model_t.php";
$Parameter="ItemId,$ItemId,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ActionId,9";
?>
<input name="MergeRows" type="hidden" id="MergeRows">
<input name="sumCols" type="hidden" id="sumCols">
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>
      <tr class=''>
       <td align="center" class="A1111" height="25">权限</td>
	   <td align="center" class="A1101">序号</td>
        <td align="center" class="A1101">公司</td>
        <td align="center" class="A1101">部门</td>
		<td align="center" class="A1101">职位</td>
        <td align="center" class="A1101">用户姓名</td>
      </tr>
</table>
<?php 
//步骤3：//需处理
$Result =mysql_query("SELECT * FROM (
SELECT A.Id,B.cSign,B.Name,B.Number,C.Name AS Branch,D.Name AS Job,B.BranchId,B.JobId 
	FROM $DataIn.usertable A 
	LEFT JOIN $DataPublic.staffmain B ON B.Number=A.Number 
	LEFT JOIN $DataPublic.branchdata C ON C.Id=B.BranchId
	LEFT JOIN $DataPublic.jobdata D ON D.Id=B.JobId
	WHERE B.Estate=1 
UNION
SELECT A.Id,'0' AS cSign,B.Name,B.Number,'其他' AS Branch,'其他' AS Job,'0' AS BranchId,'0' AS JobId
	FROM $DataIn.usertable A
	LEFT JOIN $DataIn.ot_staff B ON A.Number=B.Number 
	WHERE A.uType=4 AND B.Estate>0
)Z ORDER BY cSign DESC,BranchId,JobId,Name
",$link_id);
$i=1;
if ($myRow = mysql_fetch_array($Result)){
	do {
		$UserId=$myRow["Id"];
		$Name=$myRow["Name"];
		$Number=$myRow["Number"];
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		$cSignFrom=$myRow["cSign"];
		if($cSignFrom==0){
			$cSign="<span style='color:#FF00FF'>外部人员</span>";
			}
		else{
			include "../model/subselect/cSign.php";
			}
		//////////////////////////
		//步骤3：//需处理
		$actionResult =mysql_query("SELECT ItemId FROM $DataIn.taskuserdata WHERE ItemId='$ItemId' AND UserId ='$Number' LIMIT 1",$link_id);
		if($chexkRow = mysql_fetch_array($actionResult)){
			$ActionSTR="checked";
			}
		else{
			$ActionSTR="";
			}
		echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
		//$ColsNumber着色列数/$sumCols求和项目
		echo"<tr bgcolor='$theDefaultColor'
			onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);'
			onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
			onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
		echo"<td class='A0111' align='center' height='20'>&nbsp;<input name='checkid[]' type='checkbox' id='checkid$i' value='$Number' $ActionSTR disabled></td>";
		echo"<td class='A0101' align='center'>$i</td>";
		echo"<td class='A0101' align='center'>$cSign</td>";
		echo"<td class='A0101' align='center'>$Branch</td>";
		echo"<td class='A0101' align='center'>$Job</td>";
		echo"<td class='A0101' align='center'>$Name</td>";
		echo"</tr></table>";
		if($ActionSTR=="checked"){
			echo"<script> chooseRow(ListTable$i,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber)</script>";
			}
		//////////////////////////
		$i++;
		} while ($myRow = mysql_fetch_array($Result));
  	}
$i=$i-1;
echo"<input name='IdCount' type='hidden' id='IdCount' value='$i'>";
//步骤5：
include "../model/subprogram/add_model_b.php";
echo "<br>";
?>
<script>
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
	document.form1.action="taskdata_updated.php";
	document.form1.submit();
	}
</script>
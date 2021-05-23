<?php 
include "../model/modelhead.php";
$ColsNumber=7;				
$tableMenuS=600;
$From=$From==""?"online":$From;
$funFrom="aqsc00";
$nowWebPage=$funFrom."_online";
ChangeWtitle("$SubCompany 员工安全生产培训记录");
$checkStaff=mysql_fetch_array(mysql_query("SELECT A.Name,A.ComeIn,B.Name AS Branch,C.Name AS Job
	FROM $DataPublic.staffmain A
	LEFT JOIN $DataPublic.branchdata B ON B.Id=A.BranchId
	LEFT JOIN $DataPublic.jobdata C ON C.Id=A.JobId
	WHERE A.Number='$Number' LIMIT 1",$link_id));
?>
<style type="text/css"> 
.Title{
	font-size:20px}
</style>
<table border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td colspan="7" align="center" class="Title">员工安全生产培训记录</td></tr>
	<tr><td colspan="7" height="17">姓名：<?php echo $checkStaff["Name"];?></td></tr>
    <tr><td colspan="7" height="17">部门：<?php echo $checkStaff["Branch"];?></td></tr>
    <tr><td colspan="7" height="17">职位：<?php echo $checkStaff["Job"];?></td></tr>
    <tr><td colspan="7" height="17">入职：<?php echo $checkStaff["ComeIn"];?></td></tr>
	<tr bgcolor="#CCCCCC" align="center">
		<td width="40" height="25" class="A1111">序号</td>
        <td width="70" class="A1101">日期</td>
        <td width="80" class="A1101">分类</td>
        <td width="350" class="A1101">内容</td>
        <td width="60" class="A1101">附档</td>
        <td width="40" class="A1101">签到</td>
        <td width="50" class="A1101">审核人</td>
	</tr>
<?php
$myResult=mysql_query("SELECT Type,Id,Date,Content,Reviewer,List,theType,Attached FROM(
SELECT '2' AS Type,A.Id,B.DefaultDate AS Date,B.ItemName AS Content,B.Reviewer,B.List,C.Name AS theType,D.Attached
FROM  $DataPublic.aqsc08 A LEFT JOIN $DataPublic.aqsc07 B ON B.Id=A.ItemId LEFT JOIN $DataPublic.aqsc07_type C ON C.Id=B.TypeId LEFT JOIN $DataPublic.aqsc04 D ON D.Id=B.Tutorial
WHERE A.Number='$Number'
UNION ALL
SELECT  A.TypeId AS Type,A.Id,A.ExamDate AS Date,B.Caption AS Content,A.Checker AS Reviewer,'' AS List,'' AS theType,A.Attached
FROM $DataPublic.aqsc09 A 
LEFT JOIN $DataPublic.aqsc06 B ON B.Id=A.ExamContent
WHERE A.Number='$Number'
UNION ALL
SELECT '3' AS Type,A.Id,A.TrainDate AS Date,A.TrainContent AS Content,'-' AS Reviewer,'' AS List,'' AS theType,A.Attached
FROM $DataPublic.aqsc10 A 
) Z ORDER BY Date
",$link_id);
if($myRow=mysql_fetch_array($myResult)){
	$i=1;
	$d=anmaIn("download/aqsc/",$SinkOrder,$motherSTR);	
	do{
		$FileTitle="";
		$Id=$myRow["Id"];
		$Date=$myRow["Date"];
		$Attached=$myRow["Attached"];
		$FileType=substr($Attached, -3, 3);
		
		$Type=$myRow["Type"];
		switch($Type){
			case 0:
			$TypeName="考核";
			$Content="电子考核-安全生产知识在线考核";
			$Attached="<a href=\"aqsc09_view.php?Id=$Id\" target=\"_blank\"><img src='../images/icon_mailbody.gif' title='答卷'/></a>";
			break;
			case 1:
			$TypeName="考核";
			$Content="笔试考核-".$myRow["Content"];
			$FileTitle="答卷";
			break;
			case 2:
			$TypeName="培训";
			$Content=$myRow["theType"]."-".$myRow["Content"];
			$FileTitle="教程";
			break;
			case 3:
			$TypeName="责任人培训";
			$Content=$myRow["Content"];
			$FileTitle="凭证";
			break;
			}
		if($Attached!=""){
			if($Type!=0){
				$f=anmaIn($Attached,$SinkOrder,$motherSTR);
				$Attached="<a href=\"openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\"><img src='../images/$FileType.gif' title='$FileTitle'/></a>";
				}
			}
		else{
			$Attached="-";
			}
		$Reviewer=$myRow["Reviewer"]==""?"<span class='redB'>未审核</span>":$myRow["Reviewer"];
		$List=$myRow["List"];
		
		$List=$myRow["List"]==1?$myRow["List"]:"-";
		if($List==1){
			$List="aqsc07_list_".$ItemId.".pdf";
			$List=anmaIn($List,$SinkOrder,$motherSTR);
			$List="<a href=\"openorload.php?d=$d&f=$List&Type=&Action=6\" target=\"download\"><img src='../images/pdf.gif'  title='签到文件'/></a>";
			}
		echo"
		<tr align='center'>
		<td height='25' class='A0111'>$i</td>
        <td class='A0101'>$Date</td>
        <td class='A0101'>$TypeName</td>
        <td align='left' class='A0101'>$Content</td>
        <td class='A0101'>$Attached</td>
        <td class='A0101'>$List</td>
        <td class='A0101'>$Reviewer</td>
		</tr>";
		$i++;
		}while($myRow=mysql_fetch_array($myResult));
	}
?>	
</table>
<br />
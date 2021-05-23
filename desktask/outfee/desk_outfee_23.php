<?php  
//其他收入 
include "../../basic/chksession.php" ;
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$tableWidth=1140;
$TempArray=explode("|",$TempId);
$MonthTemp=$TempArray[0];
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='70' align='center'>体检类型</td>
		<td width='60' align='center'>员工姓名</td>
		<td width='60' align='center'>部门</td>
		<td width='60' align='center'>职位</td>
		<td width='70' align='center'>入职日期</td>
		<td width='60' align='center'>金额</td>
		<td width='200' align='center'>备注</td>
		<td width='60' align='center'>凭证</td>
		<td width='60' align='center'>结付</td>
		<td width='80' align='center'>登记日期</td>
		<td width='60' align='center'>操作员</td>
	</tr></table>";
$SearchRows=" AND S.Estate='3' AND S.Month='$MonthTemp'";
$mySql="SELECT S.Id,S.Number,S.Month,S.Amount,S.Locks,S.Date,S.Operator,S.Estate,S.Mid,P.Name,B.Name AS BranchName,J.Name AS JobName,S.Remark,S.Attached ,S.tjType,P.ComeIn,S.CheckT
FROM $DataIn.cw17_tjsheet S
LEFT JOIN $DataPublic.staffmain P ON S.Number=P.Number
LEFT JOIN $DataPublic.branchdata B ON B.Id=P.BranchId
LEFT JOIN $DataPublic.jobdata J ON J.Id=P.JobId
WHERE 1 $SearchRows";
//echo $mySql;
$i=1;
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
$GDnumber= array("①","①","②","③","④","⑤","⑥","⑦","⑧","⑨","⑩");
	do{
	        $m=1;
			$Id=$myRow["Id"];			
			$Number=$myRow["Number"];		
			$Name=$myRow["Name"];
			$Month =$myRow["Month"];
			$Amount =$myRow["Amount"];
			$Locks=$myRow["Locks"];
			$Date=$myRow["Date"];
			$BranchName=$myRow["BranchName"];				
			$JobName=$myRow["JobName"];
			$Mid=$myRow["Mid"];
            $ComeIn=$myRow["ComeIn"];
			$Operator=$myRow["Operator"];
			include "../../model/subprogram/staffname.php";
			$Estate="<span class='yellowB'>未结付</span>";
            $Remark=$myRow["Remark"];
            $tjType=$myRow["tjType"];
            $CheckT=$myRow["CheckT"];
            $CheckTime=$GDnumber[$CheckT];
            switch($tjType){
                case "1":  $tjType="岗前体检".$CheckTime;  break;
                case "2":  $tjType="岗中体检".$CheckTime;  break;
                case "3":  $tjType="离职体检".$CheckTime;  break;
                }
            $Attached=$myRow["Attached"];
        if($Attached!="" && $Attached!=0){
		     $f1=anmaIn($Attached,$SinkOrder,$motherSTR);
		     $d1=anmaIn("download/tjfile/",$SinkOrder,$motherSTR);		
             $Attached="<a href=\"../public/openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
          }
		   echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='70' align='center'>$tjType</td>
				<td width='60' align='center'>$Name</td>
				<td width='60' align='center'>$BranchName</td>
				<td width='60' align='center'>$JobName</td>
                <td width='70' align='center'> $ComeIn</td>
				<td width='60' align='center'>$Amount</td>
				<td width='200' >$Remark</td>
				<td width='60' align='center' >$Attached</td>
				<td width='60' align='center' >$Estate</td>
				<td width='80' align='center' >$Date</td>
				<td width='60' align='center' >$Operator</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>
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
$tableWidth=1000;
$TempArray=explode("|",$TempId);
$MonthTemp=$TempArray[0];
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='280' align='center'>模具项目</td>
		<td width='80' align='center'>退回金额</td>
		<td width='50' align='center'>货币</td>
		<td width='100' align='center'>供应商</td>
		<td width='250' align='center'>备注</td>
		<td width='40' align='center'>凭证</td>
		<td width='40' align='center'>状态</td>
		<td width='70' align='center'>更新日期</td>
		<td width='50' align='center'>请款人</td>
	</tr></table>";
$SearchRows=" AND M.Estate='3' AND DATE_FORMAT(M.Date,'%Y-%m')='$MonthTemp'";
$mySql="SELECT M.Id,M.Mid,M.Moq,M.ItemName ,M.OutAmount ,M.Remark,M.Operator,M.Date,M.Locks,M.Estate,S.Provider  
FROM $DataIn.cw16_modelfee  M 
LEFT JOIN cwdyfsheet S  ON M.Mid=S.Id
WHERE 1 $SearchRows";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Mid=$myRow["Mid"];
		$Moq=$myRow["Moq"];
		$ItemName=$myRow["ItemName"];
		$OutAmount=$myRow["OutAmount"];
		//$Remark=$myRow["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$myRow[Remark]' width='16' height='16'>";
		$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];
		$Operator=$myRow["Operator"];
        $Provider =$myRow["Provider"];	
		include"../model/subprogram/staffname.php";
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];		
		$Estate=$myRow["Estate"];		
		$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/modelfee/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="M".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="&nbsp;";
			}
         	echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='280' align='center'>$ItemName</td>
				<td width='80' align='center'>$Amount</td>
				<td width='50' align='center'>$Symbol</td>
                <td width='100' > $Provider</td>
				<td width='250' >$Remark</td>
				<td width='40' align='center'>$Bill</td>
				<td width='40' align='center' >$Estate</td>
				<td width='70' align='center' >$Date</td>
				<td width='50' align='center' >$Operator</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>
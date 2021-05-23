<?php   
//开发费用OK
include "../../basic/chksession.php" ;
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$tableWidth=1100;
$TempArray=explode("|",$TempId);
$MonthTemp=$TempArray[0];
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='60' align='center'>项目ID</td>
		<td width='80' align='center'>费用分类</td>
		<td width='70' align='center'>请款日期</td>
		<td width='60' align='center'>请款金额</td>
		<td width='60' align='center'>货币类型</td>
		<td width='450' align='center'>请款说明</td>
		<td width='40' align='center'>凭证</td>
		<td width='50' align='center'>请款人</td>
		<td width='40' align='center'>状态</td>
		<td width='140' align='center'>供应商</td>
		<td width='40' align='center'>备注</td>
	</tr></table>";
$SearchRows=" AND S.Estate='3' AND DATE_FORMAT(S.Date,'%Y-%m')='$MonthTemp'";
$mySql="SELECT S.Id,S.ItemId,K.Name as KName,S.Date,S.Amount,C.Symbol as CName,S.ModelDetail,S.Description,S.Remark,S.Provider,S.Bill,S.Estate,S.Locks,S.Operator
 	FROM $DataIn.cwdyfsheet S 
	LEFT JOIN $DataPublic.kftypedata K ON K.ID=S.TypeID
	LEFT JOIN $DataPublic.currencydata C ON C.ID=S.Currency
	WHERE 1 $SearchRows order by S.Date DESC";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
$m=1;
		$Id=$myRow["Id"];
		$ItemId=$myRow["ItemId"];
		$KName=$myRow["KName"];
		$Description=$myRow["Description"]==""?"&nbsp":$myRow["Description"];
		$Amount=$myRow["Amount"];
		$CName=$myRow["CName"];
		$ModelDetail=$myRow["ModelDetail"]==""?"&nbsp":$myRow["ModelDetail"];		
		$Remark=$myRow["Remark"];
        $Remark=$Remark==""?"&nbsp":"<img src='../images/remark.gif' title='$Remark' width='18' height='18'>";
		$Operator=$myRow["Operator"];
		include "../../model/subprogram/staffname.php";
		$Provider=$myRow["Provider"];
		$Date=$myRow["Date"];	
		$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/dyf/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="DYF".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="&nbsp;";
			}
         	echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='60' align='center'>$ItemId</td>
				<td width='80' >$KName</td>
				<td width='70' align='center'>$Date</td>
				<td width='60' align='right'>$Amount</td>
				<td width='60' align='center'>$CName</td>
                <td width='450' > $Description</td>
				<td width='40' align='center'>$Bill</td>
				<td width='50' align='center'>$Operator</td>
				<td width='40' align='center' >$Estate</td>
				<td width='140' >$Provider</td>
				<td width='40' align='center'>$Remark</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>
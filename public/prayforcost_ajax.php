<?php 
/*
$DataIn.cwdyfsheet电信---yang 20120801
分开已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$tableWidth=1060;
$theMonth=$_GET["Month"];
$theTypeId=$_GET["TypeId"];

echo"<br><table width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr bgcolor='#0099FF'>
		<td width='30' align='center' height='20'>序号</td>
		<td width='60' align='center'>客户</td>
		<td width='130' align='center'>开发项目内容</td>
		<td width='30' align='center'>图片</td>
		<td width='320' align='center'>费用说明</td>
		<td width='90' align='center'>供应商</td>
		<td width='50' align='center'>请款人</td>
		<td width='55' align='center'>请款日期</td>
		<td width='30' align='center'>凭证</td>
		<td width='50' align='center'>金额</td>
		</tr>";
		
$mySql="SELECT S.Id,S.ItemId,S.Description,S.Amount,S.Remark,S.Date,S.Provider,S.Bill,S.Estate,S.Locks,S.Operator,D.ItemName,C.Forshort
 	FROM $DataIn.cwdyfsheet S 
	LEFT JOIN $DataIn.development D ON D.ItemId=S.ItemId
	LEFT JOIN $DataIn.trade_object C ON D.CompanyId=C.CompanyId
	WHERE 1 AND S.TypeId='$theTypeId' AND DATE_FORMAT(S.Date,'%Y-%m')='$theMonth'  and (S.Estate=0 OR S.Estate=3) ORDER BY S.Date DESC";

$myResult = mysql_query($mySql." $PageSTR",$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
                $Id=$myRow["Id"];
		$Forshort=$myRow["Forshort"];
		$ItemName=$myRow["ItemName"];
		$Description=$myRow["Description"];
		$Amount=$myRow["Amount"];
		$Bill=$myRow["Bill"];
		$Provider=$myRow["Provider"];
		$Date=$myRow["Date"];
        $Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
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
		echo"<tr bgcolor='#66CCFF'>
		<td align='center'>$i</td>";
		echo"<td>$Forshort</td>";
		echo"<td>$ItemName</td>";
		echo"<td align='center'>&nbsp;</td>";
		echo"<td>$Description</td>";
		echo"<td>$Provider</td>";
		echo"<td align='center'>$Operator</td>";
		echo"<td align='center'>$Date</td>";
		echo"<td align='center'>$Bill</td>";
		echo"<td align='right'>$Amount</td>";
		echo"</tr>";
		$i++;
		}while($myRow = mysql_fetch_array($myResult));
	echo"</table><br>";
	}
else{
	echo"<tr><td height='30' colspan='9'  bgcolor=#D0FFD0>没有资料,请检查.</td></tr></table>";
	}
?>
<?php   
/*
已更新电信---yang 20120801
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
//参数拆分
$TempArray=explode("|",$TempId);
$Operator=$TempArray[0];
$theMonth=$TempArray[1];
$tableWidth=1010;
$TableId=$predivNum;
echo"<br><table width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr bgcolor='#99FF99'>
		<td width='30' align='center' height='20'>序号</td>
		<td width='60' align='center'>客户</td>
		<td width='130' align='center'>开发项目内容</td>
		<td width='30' align='center'>图片</td>
		<td width='320' align='center'>费用说明</td>
		<td width='90' align='center'>供应商</td>
		<td width='55' align='center'>请款日期</td>
		<td width='30' align='center'>凭证</td>
		<td width='50' align='center'>金额</td>
		</tr>";
//订单列表
$mySql="SELECT S.ItemId,S.Description,S.Amount,S.Remark,S.Date,S.Provider,S.Bill,S.Estate,S.Locks,D.ItemName,C.Forshort
 	FROM $DataIn.cwdyfsheet S 
	LEFT JOIN $DataIn.development D ON D.ItemId=S.ItemId
	LEFT JOIN $DataIn.trade_object C ON D.CompanyId=C.CompanyId
	WHERE 1 AND S.Operator='$Operator' AND DATE_FORMAT(S.Date,'%Y-%m')='$theMonth' ORDER BY S.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Forshort=$myRow["Forshort"];
		$ItemName=$myRow["ItemName"];
		$Description=$myRow["Description"];
		$Amount=$myRow["Amount"];
		$Bill=$myRow["Bill"];
		$Provider=$myRow["Provider"];
		$Date=$myRow["Date"];

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
		echo"<tr bgcolor=#D0FFD0>
		<td align='center'>$i</td>";
		echo"<td>$Forshort</td>";
		echo"<td>$ItemName</td>";
		echo"<td align='center'>&nbsp;</td>";
		echo"<td>$Description</td>";
		echo"<td>$Provider</td>";
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

<?php   
/*电信---yang 20120801
$DataIn.yw1_ordersheet
$DataIn.productdata
$DataIn.producttype
二合一已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TempArray=explode("|",$TempId);
$TempId=$TempArray[0];
$tableWidth=835;
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1'><tr bgcolor='#CCCCCC'>
		<td width='30' align='center'>序号</td>
		<td width='420' align='center'>费用说明</td>
		<td width='40' align='center'>备注</td>
		<td width='40' align='center'>图档</td>
		<td width='202' align='center'>供应商</td>
		<td width='68' align='center'>请款日期</td>
		<td width='75' align='center'>费用</td>		
		</tr>";
$i=1;$sumAmount=0;
$myResult=mysql_query("SELECT S.Id,S.Description,S.Bill,S.Amount,S.Remark,S.Provider,S.Date 
FROM $DataIn.cwdyfsheet S WHERE S.ItemId='$TempId' ORDER BY S.Date",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Id=$myRow["Id"];
		$Description=$myRow["Description"];
		$Amount=$myRow["Amount"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='16' height='16'>";
		
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
		
		$Provider=$myRow["Provider"];
		$Date=$myRow["Date"];
		$sumAmount=sprintf("%.2f",$sumAmount+$Amount);
		
		
		echo"<tr bgcolor=#EAEAEA><td align='center'>$i</td>";	//序号
		echo"<td >$Description</td>";		
		echo"<td align='center'>$Remark</td>";
		echo"<td >$Bill</td>";
		echo"<td >$Provider</td>";
		echo"<td align='center'>$Date</td>";
		echo"<td align='right'>$Amount</td>";
		echo"</tr>";
		$i++;
 		}while($myRow = mysql_fetch_array($myResult));
	//合计
		echo"<tr bgcolor=#EAEAEA><td align='center' colspan='6'>合 计</td>";
		echo"<td align='right'>$sumAmount</td>";
		echo"</tr>";
	}
else{
		echo"<tr bgcolor=#EAEAEA><td align='center' colspan='7'>无费用记录</td>";
		echo"</tr>";
	}
echo"</table>";
?>
<?php 
/*电信---yang 20120801
$DataIn.pands
$DataIn.stuffdata
$DataIn.stufftype
二合一已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$myResult = mysql_query("
	SELECT A.Id,A.StuffId,A.Relation,D.StuffCname,T.TypeName
	FROM $DataIn.pands A,$DataIn.stuffdata D,$DataIn.stufftype T WHERE A.ProductId='$TempId' and D.StuffId=A.StuffId AND T.TypeId=D.TypeId
	ORDER BY A.Id
	",$link_id);
$i=1;
echo"<table id='$TableId' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>
	<tr bgcolor='#cccccc'>
		<td width='80' height='20' align='center'>序号</td>
		<td width='130' align='center'>配件分类</td>
		<td width='89' align='center'>配件ID</td>
		<td width='89' align='center'>对应数量</td>
		<td width='406' align='center'>配件名称</td>
	</tr>";
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Id=$myRow["Id"];
		$StuffId=$myRow["StuffId"];
		$Relation=$myRow["Relation"];
		$StuffCname=$myRow["StuffCname"];
		$TypeName=$myRow["TypeName"];
		echo"<tr bgcolor='#cccccc'><td height='20' align='center'>$i</td>
			<td>$TypeName</td>
			<td align='center'>$StuffId</td>
			<td align='center'>$Relation</td>
			<td>$StuffCname</td>
			</tr>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
echo"</table>";
?>
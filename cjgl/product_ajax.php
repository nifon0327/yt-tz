<?php   
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$subTableWidth=1350;
//来自于iPad产品查询页面
echo"<table id='$TableId'  cellspacing='0' border='0' align='center' bgcolor='#FFFFFF'><tr>
	<td width='50' align='center' class='A1101'>序号</td>
	<td width='80' align='center' class='A1101'>配件ID</td>
	<td width='400' align='center' class='A1101'>配件名称</td>
	<td width='30' align='center' class='A1101'>单位</td>
	<td width='60' align='center' class='A1101'>对应数量</td>
	<td width='50' align='center' class='A1101'>采购</td>
	<td width='120' align='center' class='A1101'>供应商</td>
	</tr>";
$ordercolor=3;
$sListResult = mysql_query("SELECT D.StuffCname,D.StuffId,D.TypeId,A.Relation,A.Id,M.TypeColor,U.Name AS UnitName 
	FROM $DataIn.pands A
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId 
	LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
	LEFT JOIN $DataPublic.stuffmaintype M ON M.Id=T.mainType
	where A.ProductId='$ProductId' 
	ORDER BY A.Id",$link_id);
$i=1;
$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
if($StockRows = mysql_fetch_array($sListResult)) {
	do{
		$StuffCname=$StockRows["StuffCname"];
		$StuffId=$StockRows["StuffId"];
		$Relation=$StockRows["Relation"];
		$UnitName =$StockRows["UnitName"];
		$TypeId=$StockRows["TypeId"];
		$TypeColor=$StockRows["TypeColor"];
		$bps = mysql_query("SELECT M.Name,P.Forshort 
			FROM $DataIn.bps B
			LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId 
			LEFT JOIN $DataPublic.staffmain M ON M.Number=B.BuyerId
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
			WHERE B.StuffId='$StuffId'",$link_id);
		if($SSMMyrow=mysql_fetch_array($bps)){
			$Name=$SSMMyrow["Name"];
			$Forshort=$SSMMyrow["Forshort"];
			}
		$theDefaultColor=$TypeColor;
		//检查是否有图片
		include "../model/subprogram/stuffimg_model.php";
		echo"<tr bgcolor='$theDefaultColor'>
			<td align='center' height='20' class='A0111'>$i</td>";
		echo"<td align='center' class='A0101'>$StuffId</td>";
		echo"<td class='A0101'>$StuffCname</td>";
		echo"<td align='center' class='A0101'>$UnitName</td>";
		echo"<td align='center' class='A0101'>$Relation</td>";
		echo"<td align='center' class='A0101'>$Name</td>";
		echo"<td class='A0101'>$Forshort</td>";
		echo"</tr>";
		$i++;
		}while ($StockRows = mysql_fetch_array($sListResult));	
	}
echo"</table>";
?>
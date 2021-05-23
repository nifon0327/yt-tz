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
$TypeId=$TempArray[0];
$BuyerId=$TempArray[1];
$predivNum=$TempArray[2];
$JobId=$TempArray[3];
switch($BuyerId){
  case "N":
    $SearchRows=""; 
    break;
   case "-2":
     $SearchRows="AND M.JobId!='$JobId'";
	 break;
   default:
     $SearchRows="AND B.BuyerId='$BuyerId'";
}
/*if ($BuyerId=="N"){
	 $SearchRows=""; 
    }
   else{
	 $SearchRows="AND B.BuyerId='$BuyerId'";
}*/

$tableWidth=910;
$TableId=$predivNum;
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1'><tr bgcolor='#99FF99'>
		<td width='30' align='center'>序号</td>
		<td width='90' align='center'>配件ID</td>
		<td width='310' align='center'>配件名称</td>
		<td width='40' align='center'>图档</td>
		<td width='60' align='center'>单价</td>
		<td width='45' align='center'>单位</td>
		<td width='50' align='center'>规格</td>
		<td width='50' align='center'>备注</td>
		<td width='100' align='center'>默认供应商</td>
		</tr>";
//订单列表
$sList_Sql="SELECT 
	D.StuffId,D.StuffCname,D.Price,U.Name AS UnitName,D.Spec,D.Picture,D.Gfile,D.Gstate,D.Remark,P.Forshort 
	FROM $DataIn.stuffdata D 
	LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
	LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
	LEFT JOIN  $DataPublic.staffmain M ON M.Number=B.BuyerId 
	WHERE 1 AND D.Picture in (0,4,7) AND D.JobId='$JobId' AND D.Estate>0 AND D.TypeId='$TypeId'  $SearchRows AND T.mainType<2 ORDER BY D.Id DESC
	";
//echo $sList_Sql;
$sListResult = mysql_query($sList_Sql,$link_id);
$i=1;
$sumQty=0;
$sumAmount=0;
if ($StockRows = mysql_fetch_array($sListResult)) {	
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	do{
		$StuffId=$StockRows["StuffId"];
		$StuffCname=$StockRows["StuffCname"];
		$Price=$StockRows["Price"];
		$UnitName=$StockRows["UnitName"]==""?"&nbsp;":$StockRows["UnitName"];
		$Spec=$StockRows["Spec"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$StockRows[Spec]' width='18' height='18'>";
		$Remark=$StockRows["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' alt='$StockRows[Remark]' width='18' height='18'>";
		$Forshort=$StockRows["Forshort"];
		$Gfile=$StockRows["Gfile"];
		$Gstate=$StockRows["Gstate"];		
		$Picture=$StockRows["Picture"];
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示
		include "../model/subprogram/stuffimg_model.php";
		
		echo"<tr bgcolor=#D0FFD0><td align='center'>$i</td>";
		echo"<td  align='center'>$StuffId</td>";
		echo"<td>$StuffCname</td>";
		echo"<td  align='center'>$Gfile</td>";
		echo"<td  align='right'>$Price</td>";
		echo"<td align='center'>$UnitName</td>";
		echo"<td align='center'>$Spec</td>";
		echo"<td align='center'>$Remark</td>";
		echo"<td>$Forshort</td>";
		echo"</tr>";
		$i++;
 		}while ($StockRows = mysql_fetch_array($sListResult));
	}
else{
	echo"<tr><td height='30'>没有资料,请检查.</td></tr>";
	}
echo"</table>";
?>

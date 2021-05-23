<?php 
include "../model/modelhead.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
//参数拆分
$TempArray=explode("|",$TempId);
$Month=$TempArray[0];
$TypeId=$TempArray[1];

switch($TypeId){
	case 2:
	    $SearchRow=" AND KS.Estate ='0'  ";
	     $TitleStr="已付";
	  break;
	case 3:
	  $SearchRow=" AND KS.Estate ='3'  ";
	  $TitleStr="未付";
	 break;
   default:
      $SearchRow=" AND (KS.Estate ='3' OR KS.Estate ='0') ";
      $TitleStr="应付";
     break;
}
//echo "$TempId";
echo "<span style='font-size:15px;font-weight:bold;'>$Month  $TitleStr" . "货款明细</span>";
$tableWidth=1020;
$TableId=$predivNum;
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1'><tr bgcolor='#99FF99'>
		<td width='30' align='center'>序号</td>
		<td width='50' align='center'>结付日期</td>
		<td width='50' align='center'>请款月份</td>
		<td width='70' align='center'>采购单号</td>
		<td width='90' align='center'>需求单号</td>
		<td width='50' align='center'>配件ID</td>
		<td width='333' align='center'>配件名称</td>				
		<td width='40' align='center'>图档</td>
		<td width='55' align='center'>单价</td>
		<td width='30' align='center'>单位</td>
		<td width='60' align='center'>采购数量</td>
		<td width='60' align='center'>未收数量</td>
		<td width='72' align='center'>采购金额</td>
		</tr>";
//订单列表
/*
$sListResult = mysql_query("
	SELECT M.PurchaseID,S.StockId,S.StuffId,S.Price,S.OrderQty,S.StockQty,(S.AddQty+S.FactualQty) AS Qty,D.StuffCname
	FROM $DataIn.cg1_stockmain M
	LEFT JOIN $DataIn.cg1_stocksheet S ON M.Id=S.Mid
	LEFT JOIN $DataIn.cw1_fkoutsheet K ON K.StockId=S.StockId
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	WHERE 1 AND M.CompanyId='$CompanyId' AND K.Estate=3 AND K.Month='$Month' ORDER BY M.Id
	",$link_id);*/
$ListSql="
	SELECT M.PurchaseID,KS.StockId,KS.StuffId,KS.Price,KS.OrderQty,KS.StockQty,(KS.AddQty+KS.FactualQty) AS Qty,KS.Month,D.StuffCname,D.Gfile,D.Gstate,DATE_FORMAT(KM.PayDate,'%d') AS jfDay,U.Name AS UnitName,ROUND((KS.AddQty+KS.FactualQty)*KS.Price,2) AS Amount
	FROM $DataIn.cw1_fkoutsheet KS 
	LEFT JOIN $DataIn.cw1_fkoutmain KM ON KM.Id=KS.Mid 
	LEFT JOIN $DataIn.cg1_stocksheet S ON KS.StockId=S.StockId
	LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
	WHERE 1 AND M.CompanyId='$myCompanyId' $SearchRow AND KS.Month='$Month' ORDER BY KM.Id";
//echo $ListSql;
$sListResult = mysql_query($ListSql,$link_id);
	
$i=1;
$sumAmount=0;$sumQty=0;
if ($StockRows = mysql_fetch_array($sListResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	do{
		$PurchaseID=$StockRows["PurchaseID"];
		$jfDay=$StockRows["jfDay"];
		$rkDay=$StockRows["rkDay"];
		$StockId=$StockRows["StockId"];
		$StuffId=$StockRows["StuffId"];
		$StuffCname=$StockRows["StuffCname"];
		$UnitName=$StockRows["UnitName"];
		$Month=$StockRows["Month"];
		$Price=$StockRows["Price"];
		$Qty=$StockRows["Qty"];
		$sumQty+=$Qty;
		$Amount= $StockRows["Amount"];
		$SumAmount+=$Amount;
		//总收货数量
		$rkTemp=mysql_query("SELECT ifnull(SUM(Qty),0) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$wsQty=$Qty-$rkQty;
		$wsQty=$wsQty==0?"&nbsp;":"<div class='redB'>".$wsQty."</div>";
		$Gfile=$StockRows["Gfile"];
		$Gstate=$StockRows["Gstate"];
		if($Gfile!="" && $Gstate==1){
				$f=anmaIn($Gfile,$SinkOrder,$motherSTR);
			
				$Gfile="<a href=\"../admin/openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\" ><img src='../images/down.gif' alt='$Gremark' width='18' height='18'></a>";
				}
			else{
				$Gfile="&nbsp;";
				}
	
				
		echo"<tr bgcolor=#D0FFD0>
		<td align='center'>$i</td>";
		echo"<td  align='center'>$jfDay 日</td>";	//结付日期
		echo"<td  align='center'>$Month</td>";	//请款日期
		echo"<td  align='center'>$PurchaseID</td>";	//采购单
		echo"<td  align='center'>$StockId</td>";	//需求单
		echo"<td  align='center'>$StuffId</td>";	//配件ID
		echo"<td>$StuffCname</td>";					//配件名称
		echo"<td  align='right'>$Gfile</td>";		//图档
		echo"<td  align='right'>$Price</td>";		//价格
		echo"<td align='center'>$UnitName</td>";
		echo"<td align='right'>$Qty</td>";		//采购数量
		echo"<td align='right'>$wsQty</td>";
		echo"<td align='right'>$Amount</td>";		//金额
		echo"</tr>";
		$i++;
 		}while ($StockRows = mysql_fetch_array($sListResult));
	//合计
	$SumAmount=sprintf("%.2f",$SumAmount);
	$SumAmount=number_format($SumAmount,2);
	$sumQty=number_format($sumQty,1);
	echo"<tr  bgcolor=#99FF99><td colspan='10' align='center'>合计</td>";
	echo"<td align='right'>$sumQty</td>";
	echo"<td align='right'>&nbsp;</td>";
	echo"<td align='right'>$SumAmount</td>";
	echo"</tr>";
	}
else{
	echo"<tr><td height='30' colspan='13'  bgcolor=#D0FFD0>没有资料,请检查.</td></tr>";
	}
echo"</table>";
?>

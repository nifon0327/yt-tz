<?php   
/*电信---yang 20120801
已更新
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
$DeliveryDate=$TempArray[0];//交期
$BuyerId=$TempArray[1]; //采购
$predivNum=$TempArray[2];//aXbY

$DateTime=date("Y-m-d");   //从现在开始，到上一年的。采购的，其它就不显示了  add mby zx 2010-12-30
$StartDate=date("Y-m-d",strtotime("$DateTime-1 years"));
$SearchRows=" AND S.DeliveryDate>='$StartDate' ";
$SearchCk=" AND M.Date>='$StartDate' ";
/*
$mySql="SELECT S.CompanyId,G.Forshort
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.trade_object G ON G.CompanyId =S.CompanyId
WHERE 1 AND S.Mid>0 AND S.rkSign>0 AND S.DeliveryDate='$DeliveryDate' AND S.BuyerId='$BuyerId' GROUP BY S.CompanyId ORDER BY G.Letter";
//echo "$mySql <br>";
*/
$mySql="SELECT S.CompanyId,S.Forshort,S.Letter from 
		(SELECT S.CompanyId,(S.AddQty+S.FactualQty) AS cgQty,S.StockId,G.Forshort,G.Letter 
		 FROM $DataIn.cg1_stocksheet S
		 LEFT JOIN $DataIn.trade_object G ON G.CompanyId =S.CompanyId
		 WHERE 1  AND S.rkSign>0 AND BuyerId='$BuyerId' AND S.Mid>0 AND S.DeliveryDate='$DeliveryDate') S
		LEFT JOIN 
		(SELECT SUM(C.Qty) AS Qty,C.StockId  
		FROM $DataIn.ck1_rksheet C
		LEFT JOIN $DataIn.ck1_rkmain M ON M.Id=C.Mid
		WHERE 1 $SearchCk  group by C.StockID) C  ON C.StockID=S.StockID
		where (S.cgQty>C.Qty) OR C.Qty IS NULL  group by S.CompanyId ORDER BY S.Letter";

//echo "$mySql2";

$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=930;
$subTableWidth=910;
if($myRow = mysql_fetch_array($myResult)){
	$FK_1=0;$FK_2=0;$FK_3=0;
	do{		
		/*
		$cgQty=$myRow["cgQty"];
		$StockId=$myRow["StockId"];
		$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$rkQty=$rkQty==""?0:$rkQty;
		$unQty=$cgQty-$rkQty;
		if($unQty>0){ */		
			$CompanyId=$myRow["CompanyId"];
			$Forshort=$myRow["Forshort"];
	
			$DivNum=$predivNum."c".$i;
			
			$TempId="$DeliveryDate|$BuyerId|$CompanyId|$DivNum";//交期|采购|供应商
			$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"cg_deliverydate_c\");' 
			id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
			echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
				<tr bgcolor='#99FF99'>
					</td><td width='600'>&nbsp;$showPurchaseorder $Forshort</td>
				</tr></table>";
			echo"<table width='$tableWidth' cellspacing='1' border='0' id='HideTable_$DivNum$i' style='display:none'>
				<tr bgcolor='#FFFFFF'>
					<td height='30'>
					<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div><br>
					</td>
				</tr></table>
				";
			$i++;
		// }
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>
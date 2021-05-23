<?php   
/*电信---yang 20120801
$DataIn.ck9_stocksheet
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

$TempArray=explode("|",$TempId);
$TypeId=$TempArray[0];	//供应商
//$predivNum=$TempArray[1];	//a
$SearchRows=$TempArray[1];	//a

//参数拆分
//$TypeId=$TempId;
$mySql="
SELECT K.tStockQty,K.oStockQty,D.StuffId,D.StuffCname,U.Name AS UnitName,D.Estate,D.Gfile,D.Gstate,D.Picture,D.Gremark,P.Forshort
FROM $DataIn.ck9_stocksheet K
LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId
LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
WHERE 1 $SearchRows and K.oStockQty>0  AND D.TypeId='$TypeId' AND D.Estate=1 ORDER BY D.Estate DESC,D.StuffCname,D.StuffId";  // 
//WHERE K.oStockQty>0 AND D.TypeId='$TypeId' ORDER BY D.Estate DESC,D.StuffCname,D.StuffId";

$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=880;
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
if($myRow = mysql_fetch_array($myResult)){
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#CCCCCC'>
				<td width='40' height='20' align='center'>序号</td>
				<td width='80' align='center'>配件ID</td>
				<td align='center'>配件名称</td>
				<td width='60' align='center'>历史订单</td>
				<td width='45' align='center'>单位</td>
				<td width='60' align='center'>状态</td>
				<td width='100' align='center'>在库</td>
				<td width='100' align='center'>可用库存</td>
				<td width='100' align='center'>供应商</td>
			</tr></table>";
	do{
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$tStockQty=$myRow["tStockQty"];
		$oStockQty=$myRow["oStockQty"];
		
		$Picture=$myRow["Picture"];
		$Gfile=$myRow["Gfile"];
		$Gstate=$myRow["Gstate"]; 				
		$Gremark=$myRow["Gremark"];	
		$Forshort=$myRow["Forshort"];
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示	
		include "../model/subprogram/stuffimg_model.php";
		/*
		$Dir="stufffile";
		$Dir=anmaIn($Dir,$SinkOrder,$motherSTR);		
			//检查是否有图片
			include "../model/subprogram/stuffimg_model.php";
		*/	
                //历史订单
         $OrderQtyInfo="<a href='../public/cg_historyorder.php?StuffId=$StuffId&Id=' target='_blank'>查看</a>";
		$DivNum=$predivNum."b".$i;
		$TempId="$StuffId|$DivNum";
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_ostockqty_b\");' 
		id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		$htmlstr="<table width='$tableWidth' cellspacing='0' border='0' id='HideTable_$DivNum$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
				<td height='30'>
				<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
				</td>
			</tr></table>";
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#CCCCCC'>
				<td width='40' height='20' align='center'>$showPurchaseorder $i</td>
				<td width='80' align='center'>$StuffId</td>
				<td >$StuffCname</td>
				<td width='60' align='center'>$OrderQtyInfo</td>
				<td width='45' align='center'>$UnitName</td>
				<td width='60' align='center'>$Estate</td>
				<td width='100' align='right'>$tStockQty&nbsp;</td>
				<td width='100' align='right'>$oStockQty&nbsp;</td>
				<td width='100' align='left'>$Forshort</td>
			</tr></table>";
         echo $htmlstr;
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>
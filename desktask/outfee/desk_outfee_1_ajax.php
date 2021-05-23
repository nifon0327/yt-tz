<?php   
//供应商货款OK
include "../../basic/chksession.php" ;
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$tableWidth=910;
$TempArray=explode("|",$TempId);
$CompanyId=$TempArray[0];
$MonthTemp=$TempArray[1];
$predivNum=$TempArray[2];
$TableId="ListTB".$preDivNum.$RowId;
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='100' align='center'>采购流水号</td>
		<td width='230' align='center'>配件名称</td>
		<td width='50' align='center'>采购员</td>
		<td width='50' align='center'>订单数</td>
		<td width='50' align='center'>使用库存</td>
		<td width='50' align='center'>需求数</td>
		<td width='50' align='center'>增购数</td>
		<td width='50' align='center'>实购数</td>
		<td width='50' align='center'>单价</td>
		<td width='45' align='center'>单位</td>
		<td width='60' align='center'>金额</td>
	</tr></table>";
$SearchRows=" AND S.Estate='3' AND S.Month='$MonthTemp' AND P.CompanyId=$CompanyId";
$mySql="SELECT 
	S.Id,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.BuyerId,P.Forshort,M.Name,D.StuffCname,U.Name AS UnitName,H.Date as OutDate,D.TypeId,D.Picture 
 	FROM $DataIn.cw1_fkoutsheet S 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	LEFT JOIN $DataPublic.staffmain M ON M.Number=S.BuyerId	
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
    Left Join $DataIn.ch1_shipsheet C ON C.PorderId=S.PorderId
    Left Join $DataIn.ch1_shipmain H ON H.Id=C.Mid	
	WHERE 1 $SearchRows ORDER BY S.Month DESC";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
     $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);	
	do{
         	$m=1;
		$Id=$myRow["Id"];
		$StockId=$myRow["StockId"];//采购流水号
		$OutStockId=$StockId;
		$StuffCname=$myRow["StuffCname"];//配件名称
		$Buyer=$myRow["Name"];//采购		
		$Forshort=$myRow["ForshortName"];//供应商
		$OrderQty=$myRow["OrderQty"];		//订单数量		
		$StockQty=$myRow["StockQty"];	//需求数量
		$FactualQty=$myRow["FactualQty"];	//需求数量
		$AddQty=$myRow["AddQty"];			//增购数量	
		$Qty=$FactualQty+$AddQty;	//采购总数
		$TypeId=$myRow["TypeId"];
		$AmountQty=$FactualQty+$AddQty;	//采购总数
		$Price=$myRow["Price"];	//采购价格
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$OutDate=$myRow["OutDate"]==""?"&nbsp":$myRow["OutDate"];    

        $StuffId=$myRow["StuffId"];
        $Picture=$myRow["Picture"];
        include "../../model/subprogram/stuffimg_model.php";	//检查是否有图片
		$Estate="<div class='redB'>未付</div>";
		//统计
		$Amount=sprintf("%.2f",$AmountQty*$Price);//本记录金额合计	
		//收货情况				
		$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$rkQty=$rkQty==""?0:$rkQty;
			//领料情况
			$llTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck5_llsheet WHERE StockId='$StockId' order by Id",$link_id); 
			$llQty=mysql_result($llTemp,0,"Qty");
			$llQty=$llQty==""?0:$llQty;
			$llBgColor="";
			if($tdBGCOLOR==""){
				if($llQty==$OrderQty){
					$llBgColor="class='greenB'";
					}
				else{
					$llBgColor="class='yellowB'";
					}
				}
			else{
				$llBgColor="class='greenB'";
				}
		
		//行标色
		$ordercolor="bgcolor='FFFFFF'";$Fontcolor="class='redB'";
		if($Mantissa<$Qty){//如果尾数《采购数：黄色
			$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
			$StockId="<a href='../../public/ck_rk_list.php?Sid=$Sid' target='_blank' title='点击查看收货记录'>$StockId</a>";
			$Mantissa="<div class='yellowB'>$Mantissa</div>";
			if($Mantissa==0){//如果尾数=0：绿色
				$Mantissa="&nbsp;";
				}
			}
		else{
			$Mantissa="<div class='redB'>$Mantissa</div>";
			}
			
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='100' align='center'>$StockId</td>
				<td width='230' >$StuffCname</td>
				<td width='50' align='center'>$Buyer</td>
				<td width='50' align='right'>$OrderQty</td>
				<td width='50' align='right'>$StockQty</td>
				<td width='50' align='right'>$FactualQty</td>
                <td width='50' align='right'> $AddQty</td>
				<td width='50' align='right'>$Qty</td>
				<td width='50' align='center'>$Price</td>
				<td width='50' align='center' >$UnitName</td>
				<td width='60' align='right'>$Amount</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>
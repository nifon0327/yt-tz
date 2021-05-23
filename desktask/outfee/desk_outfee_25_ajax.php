<?php   
//非BOM未付订金 ewen 2013-03-28  OK
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
		<td width='300' align='center'>配件名称</td>
		<td width='80' align='center'>入库数量</td>
		<td width='80' align='center'>采购数量</td>
		<td width='80' align='center'>单价</td>
		<td width='60' align='center'>单位</td>
		<td width='80' align='center'>金额</td>
	</tr>";
$SearchRows=" AND S.Estate='3' AND S.Month='$MonthTemp' AND P.CompanyId=$CompanyId";
$mySql="SELECT 
	S.cgId,S.GoodsId,S.Price,S.Qty,S.Amount,
	P.Forshort,D.GoodsName,D.Unit,D.Attached 
 	FROM $DataIn.nonbom12_cwsheet S 
	LEFT JOIN $DataPublic.nonbom3_retailermain P ON P.CompanyId=S.CompanyId
	LEFT JOIN $DataPublic.nonbom4_goodsdata D ON D.GoodsId=S.GoodsId
	WHERE 1 $SearchRows ORDER BY S.Month DESC";
$myResult = mysql_query($mySql,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
     $Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
	do{
         $m=1;
		$cgId=$myRow["cgId"];
		$GoodsId=$myRow["GoodsId"];
		$GoodsName=$myRow["GoodsName"];//配件名称
		$Forshort=$myRow["Forshort"];//供应商
		$Qty=$myRow["Qty"];		//数量		
		$Price=$myRow["Price"];	//采购价格
		$Unit=$myRow["Unit"];
		$Attached=$myRow["Attached"];    
		$Attached=$myRow["Attached"];
		if($Attached==1){
			$Attached=$GoodsId.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
			}
		$Estate="<div class='redB'>未付</div>";
		//统计
		$Amount=$myRow["Amount"];   //本记录金额合计	
		//入库数量
			$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.nonbom7_insheet WHERE GoodsId='$GoodsId' AND cgId='$cgId'",$link_id);
			$rkQty=mysql_result($rkTemp,0,"Qty");
			$rkQty=$rkQty==""?0:$rkQty;
			if($rkQty==$Qty){
				$rkBgColor="class='greenB'";
				$rkQty="<a href='../public/nonbom7_list.php?cgId=$cgId' target='_blank' style='color:#093'>$rkQty</a>";
				}
			else{
				$rkBgColor="class='redB'";
				if($rkQty==0){
					$rkQty="&nbsp;";
					}
				else{
					$rkQty="<a href='../public/nonbom7_list.php?cgId=$cgId' target='_blank' style='color:#F00'>$rkQty</a>";
					}
				}
		//历史单价
		$Price="<a href='nonbom4_history.php?GoodsId=$GoodsId' target='_blank'>$Price</a>";
		//配件分析
		$GoodsId="<a href='nonbom4_report.php?GoodsId=$GoodsId' target='_blank'>$GoodsId</a>";
		$Locks=$myRow["Locks"];			
		echo"
			<tr bgcolor='#FFFFFF'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='100' align='center'>$cgId</td>
				<td width='230' >$GoodsName</td>
				<td width='50' align='right'><div $rkBgColor>$rkQty</div></td>
				<td width='50' align='right'>$Qty</td>
				<td width='50' align='center'>$Price</td>
				<td width='50' align='center' >$Unit</td>
				<td width='60' align='right'>$Amount</td>
			</tr>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
echo "</table>";
?>
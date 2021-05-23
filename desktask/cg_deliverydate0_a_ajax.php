<?php   
//电信-zxq 2012-08-01
/*
$DataIn.ch1_shipmain
$DataIn.ch1_shipsheet
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

//取得文件所在目录
$FromDir=get_currentDir(1);

//参数拆分
$TempArray=explode("|",$TempId);
$BuyerId=$TempArray[0];	//采购
$predivNum=$TempArray[1];	//a

$mySql="SELECT S.DeliveryDate FROM $DataIn.cg1_stocksheet S
		WHERE 1  AND S.rkSign>0 AND S.Mid>0 AND S.DeliveryDate!='0000-00-00' AND BuyerId='$BuyerId'
		AND (S.AddQty+S.FactualQty)>(SELECT IFNULL(SUM(C.Qty),0) AS Qty  FROM $DataIn.ck1_rksheet C
		WHERE 1 AND C.StockId=S.StockId) GROUP BY S.DeliveryDate ORDER BY S.DeliveryDate";

$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=950;
$subTableWidth=930;
if($myRow = mysql_fetch_array($myResult)){
	do{
		
		/*
		$cgQty=$myRow["cgQty"];
		$StockId=$myRow["StockId"];
		$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$rkQty=$rkQty==""?0:$rkQty;
		$unQty=$cgQty-$rkQty;
		*/
		//if($unQty>0){
			$DeliveryDate=$myRow["DeliveryDate"];
			$DivNum=$predivNum."b".$i;
			$TempId="$DeliveryDate|$BuyerId|$DivNum";
			//交货日期颜色
			$SetDate=DateDiff($DeliveryDate);
			if($SetDate<2){		//离交期不大于一天，为红色
				switch($SetDate){
				case 0:
					$DeliveryDate="<span class='redB'>".$DeliveryDate." :已到预定交期</span>";break;
				case 1:
					$DeliveryDate="<span class='redB'>".$DeliveryDate." :离预定交期还有 $SetDate 天</span>";break;
				default:
					$SetDate=-$SetDate;
					$DeliveryDate="<span class='redB'>".$DeliveryDate." :已超过预定交期 $SetDate 天</span>";break;
					}
				}
			else{
				if($SetDate<5){
					$DeliveryDate="<span class='yellowB'>".$DeliveryDate." :离预定交期还有 $SetDate 天</span>";
					}
				else{
					$DeliveryDate="<span class='greenB'>".$DeliveryDate." :离预定交期还有 $SetDate 天</span>";
					}
				}
	
			
			$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"cg_deliverydate_b\",\"$FromDir\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
			echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr bgcolor='#CCCCCC'><td>&nbsp;$showPurchaseorder $DeliveryDate</td></tr></table>";
			echo"<table width='$tableWidth' cellspacing='0' border='0' id='HideTable_$DivNum$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
					<td height='30'>
					<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
					</td>
				</tr></table>
				";
			$i++;
		// }
		}while ($myRow = mysql_fetch_array($myResult));
	}
	$DeliveryDate='0000-00-00';
	$DivNum=$predivNum."b".$i;
	$TempId="$DeliveryDate|$BuyerId|$DivNum";
	$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"cg_deliverydate0_b\",\"$FromDir\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' alt='显示或隐藏配件需求明细资料. ' width='13' height='13' style='CURSOR: pointer'>";
			$HideTableHTML="
			<table width='$tableWidth' border='0' cellspacing='0' id='HideTable_$DivNum$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
					<td class='A0111' height='30'>
						<br>
							<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
						<br>
					</td>
				</tr>
			</table>";
?>
	<table width="<?php    echo $tableWidth?>" border="0" cellspacing="0" id="ListTable<?php    echo $i?>" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		<tr id='A'>
			<td class="A0111" height="25">&nbsp;<?php    echo $showPurchaseorder?>&nbsp;未设置交货日期的采购单</td>
		</tr>
	</table>
<?php    echo $HideTableHTML?>

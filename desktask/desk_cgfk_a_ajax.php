<?php   
/*电信-yang 20120801
配件分类页面
已更新
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
echo"<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$TempArray=explode("|",$TempId);
$CompanyId=$TempArray[0];	//供应商
$predivNum=$TempArray[1];	//a
$i=1;
$tableWidth=1050;
$subTableWidth=1030;
for($i=1;$i<5;$i++){
	$AmountTemp=0;
	switch($i){
		case 1:
			$TypeName="未请款货款 (以送货日期为索引)";
			$NextPage="desk_cgfk_b1";
			//检查是否有内容
			$checkAmountRow=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty*G.Price) AS Amount
				FROM $DataIn.ck1_rkmain M
				LEFT JOIN $DataIn.ck1_rksheet S ON M.Id=S.Mid
				LEFT JOIN $DataIn.cw1_fkoutsheet K ON K.StockId=S.StockId
				LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
				WHERE 1 AND M.CompanyId='$CompanyId' AND K.StockId IS NULL
				",$link_id));
			$AmountTemp =sprintf("%.2f",$checkAmountRow["Amount"]);
			break;
		case 2:
			$TypeName="请款中货款 (以请款月份为索引)";
			$NextPage="desk_cgfk_b2";
			$checkAmountRow=mysql_fetch_array(mysql_query("SELECT SUM(K.Qty*K.Price) AS Amount
				FROM $DataIn.cw1_fkoutsheet K
				WHERE 1 AND K.CompanyId='$CompanyId' AND K.Estate=2
				",$link_id));
			$AmountTemp =sprintf("%.2f",$checkAmountRow["Amount"]);			
			break;
		case 3:
			$TypeName="待结付货款 (以请款月份为索引)";
			$NextPage="desk_cgfk_b3";
			$checkAmountRow=mysql_fetch_array(mysql_query("SELECT SUM(K.Qty*K.Price) AS Amount
				FROM $DataIn.cw1_fkoutsheet K
				WHERE 1 AND K.CompanyId='$CompanyId' AND K.Estate=3
				",$link_id));
			$AmountTemp =sprintf("%.2f",$checkAmountRow["Amount"]);			
			break;
		case 4:
			$TypeName="已结付货款 (以结付月份为索引)";
			$NextPage="desk_cgfk_b4";
			$checkAmountRow=mysql_fetch_array(mysql_query("SELECT SUM(K.Qty*K.Price) AS Amount
				FROM $DataIn.cw1_fkoutsheet K
				WHERE 1 AND K.CompanyId='$CompanyId' AND K.Estate=0
				",$link_id));
			$AmountTemp =sprintf("%.2f",$checkAmountRow["Amount"]);			
			break;
		}
		$DivNum=$predivNum."b".$i;
		$TempId="$i|$CompanyId|$DivNum";
		if($AmountTemp>0){
			$AmountTemp=number_format($AmountTemp,2);
			$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"$NextPage\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
			echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr bgcolor='#CCCCCC'><td>&nbsp;$showPurchaseorder $TypeName</td><td width='85' align='right'>$AmountTemp</td><td width='80' align='right'>&nbsp;</td><td width='75' align='right'>&nbsp;</td></tr></table>";
			echo"<table width='$tableWidth' cellspacing='0' border='0' id='HideTable_$DivNum$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
					<td height='30'>
					<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
					</td>
				</tr></table>
				";
		}
	}
?>
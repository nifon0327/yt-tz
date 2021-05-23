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
$SearchRows=$TempArray[2];	//a
//echo "????????????SearchRows:$SearchRows";
$SearchRows=str_replace("''", "'", $SearchRows);
$mySql="
SELECT thQty,bcQty,wbQty,StuffId,StuffCname,Gfile,Gstate FROM (
	SELECT A.thQty,ifnull(B.bcQty,0) AS bcQty,(A.thQty-ifnull(B.bcQty,0)) AS wbQty,D.StuffId,D.StuffCname,D.Gfile,D.Gstate	
	FROM (
	SELECT ifnull(SUM(S.Qty),0) AS thQty,S.StuffId FROM $DataIn.ck2_thmain M
	LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid=M.Id
	WHERE M.CompanyId='$CompanyId' GROUP BY S.StuffId) A
	LEFT JOIN 
	$DataIn.stuffdata D ON A.StuffId=D.StuffId
	LEFT JOIN 
	(SELECT ifnull(SUM(S.Qty),0) AS bcQty,S.StuffId 
	FROM $DataIn.ck3_bcmain M
	LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid=M.Id
	WHERE M.CompanyId='$CompanyId' GROUP BY S.StuffId) B ON B.StuffId=D.StuffId
	WHERE 1 $SearchRows ORDER BY D.StuffCname
	) C ORDER BY wbQty DESC,bcQty,StuffCname
	";
$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=1050;
$subTableWidth=1030;
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	do{
		$StuffId=$myRow["StuffId"];
		$thQty=$myRow["thQty"];
		$bcQty=$myRow["bcQty"];
		$wbQty=$myRow["wbQty"];
		if($thQty==$wbQty){
			$wbQty="<div align='right' style='color: #FF0000;font-weight: bold;'>".$wbQty."</div>";
			}
		else{
			if($wbQty>0){
				$wbQty="<div align='right' style='color: #0000FF;font-weight: bold;'>".$wbQty."</div>";
				}
			else{
				if($wbQty==0){
					$wbQty="<div align='center' style='color: #009900;font-weight: bold;'>OK</div>";
					}
				else{
					$wbQty="<div align='right' style='color: #FF0000;font-weight: bold;'>".$wbQty."(异常)</div>";
					}
				}
			}
		$bcQty=$bcQty==0?"&nbsp;":$bcQty;
		$StuffCname=$myRow["StuffCname"];
		$Gfile=$myRow["Gfile"];
		$Gstate=$myRow["Gstate"];
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示	
		$DivNum=$predivNum."b".$i;
		$TempId="$CompanyId|$StuffId";
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_cgth_b1\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
		<tr bgcolor='#cccccc'>
		<td>&nbsp;$showPurchaseorder $StuffId $StuffCname</td>
		<td width='60' align='center'>$Gfile</td>
		<td width='110' align='right'>$thQty</td>
		<td width='110' align='right'>$bcQty</td>
		<td width='104'>$wbQty</td>
		</tr></table>";
		echo"<table width='$tableWidth' cellspacing='0' border='0' id='HideTable_$DivNum$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
				<td height='30'>
				<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
				</td>
			</tr></table>
			";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>
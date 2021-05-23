<?php   
/*
配件分类页面电信---yang 20120801
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
$BuyerId=$TempArray[0];	//采购
$predivNum=$TempArray[1];	//a
$JobId=$TempArray[2];
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

$mySql="SELECT T.TypeId,T.TypeName,count(*) as Nums 
	FROM  $DataIn.stuffdata D 
	LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
	LEFT JOIN  $DataPublic.staffmain M ON M.Number=B.BuyerId 
	WHERE 1 AND D.Picture in (0,4,7) AND D.JobId='$JobId' AND D.Estate>0  $SearchRows  AND T.mainType<2 GROUP BY D.TypeId ORDER BY T.Id
	";
$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=930;
$subTableWidth=910;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$TypeId=$myRow["TypeId"];
		$TypeName=$myRow["TypeName"];
		$DivNum=$predivNum."b".$i;
		$TempId="$TypeId|$BuyerId|$DivNum|$JobId";
/*		$checkNums=mysql_query("SELECT count(*)
			FROM$DataIn.stuffdata D ON G.StuffId=D.StuffId
			LEFT JOIN $DataIn.bps B ON B.StuffId=G.StuffId
			WHERE 1 AND (D.Picture in (0,4,7) AND D.JobId='$JobId') AND D.Estate>0 AND D.TypeId='$TypeId' AND D.TypeId NOT IN(9074,9082,9093) $SearchRows GROUP BY G.StuffId",$link_id);
		$Nums=@mysql_num_rows($checkNums);*/
		$Nums=$myRow["Nums"];
		if($Nums>0){
			$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_unuploadimg_b\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
			echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr bgcolor='#CCCCCC'><td>&nbsp;$showPurchaseorder $TypeName ($Nums)</td></tr></table>";
			echo"<table width='$tableWidth' cellspacing='0' border='0' id='HideTable_$DivNum$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
					<td height='30'>
					<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
					</td>
				</tr></table>
				";
			$i++;
		}
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>
<?php   
//电信---yang 20120801
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
$predivNum=$TempArray[0];	//a
$theMonth=$TempArray[1];	//月份

//有未请款的年份
$mySql="
	SELECT Name,Estate,OrderByKey,Operator,Amount FROM (
		SELECT P.Name,P.Estate,P.BranchId AS OrderByKey,M.Operator,SUM(M.Amount) AS Amount
		FROM $DataIn.cwdyfsheet M
		LEFT JOIN $DataPublic.staffmain P ON P.Number=M.Operator
		WHERE 1 AND P.BranchId<5 AND P.Estate<2 AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth' GROUP BY M.Operator
	UNION ALL
	SELECT P.Name,'0' AS Estate,P.BranchId AS OrderByKey,M.Operator,SUM(M.Amount) AS Amount
		FROM $DataIn.cwdyfsheet M
		LEFT JOIN $DataPublic.staffmain P ON P.Number=M.Operator
		WHERE 1 AND P.BranchId<5 AND P.Estate=2 AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth' GROUP BY M.Operator
	UNION ALL
		SELECT P.Name,P.Estate,'0' AS OrderByKey,M.Operator,SUM(M.Amount) AS Amount
		FROM $DataIn.cwdyfsheet M
		LEFT JOIN $DataPublic.staffmain P ON P.Number=M.Operator
		WHERE 1 AND P.BranchId=5 AND DATE_FORMAT(M.Date,'%Y-%m')='$theMonth' GROUP BY M.Operator
	) A ORDER BY Estate DESC,OrderByKey DESC,Operator DESC
	";
$myResult = mysql_query($mySql,$link_id);
$i=1;
$tableWidth=1030;
$subTableWidth=1010;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$Name=$myRow["Name"];
		$Operator=$myRow["Operator"];
		$Amount=$myRow["Amount"];
		$Estate=$myRow["Estate"];
		$OrderByKey=$myRow["OrderByKey"];
		//现职并且开发
		if($Estate==1 && $OrderByKey==4){
			$Name="<span class='greenB'>".$Name."</span>";
			}
		else{
			if($Estate==0){
				$Name="<span style='color:#99CC99'>".$Name."</span>";
				}
			}
		$DivNum=$predivNum."c".$i;
		$TempId="$theMonth|$Operator";
		$showPurchaseorder="<img onClick='SandH(\"$DivNum\",$i,this,\"$TempId\",\"desk_development1_c1\");' id='ThisImg_$DivNum$i' name='ThisImg_$DivNum$i' src='../images/showtable.gif' width='13' height='13' style='CURSOR: pointer'>";
		echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr bgcolor='#ffffff'><td>&nbsp;$showPurchaseorder $Name</td><td width='63' align='right'>$Amount</td></tr></table>";
		echo"<table width='$tableWidth' cellspacing='0' border='0' id='HideTable_$DivNum$i' style='display:none'>
			<tr bgcolor='#ffffff'>
				<td height='30'>
				<div id='HideDiv_$DivNum$i' width='$subTableWidth' align='right'>&nbsp;</div>
				</td>
			</tr></table>
			";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>
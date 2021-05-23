<?php 
//电信-EWEN
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="QCTB".$RowId;
echo"<table id='$TableId' cellspacing='1' border='1' align='center'><tr bgcolor='#CCCCCC'>
		<td width='30' align='center'>序号</td>
		<td width='80' align='center'>客户</td>
		<td width='60' align='center'>产品ID</td>				
		<td width='200' align='center'>中文名称</td>
		<td width='200' align='center'>Product Code</td>
		<td width='60' align='center'>配件Id</td>
		<td width='200' align='center'>配件名称</td>
		<td width='' align='center'>配件Qc名称</td>
		</tr>";
//订单列表//LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=O.OrderNumber 
$ArrayQCId=explode("|",$QCId);
$QCId=$ArrayQCId[0];
$TypeId=$ArrayQCId[1];
$IsType=$ArrayQCId[2];

if ($IsType==1){//去掉剔除的那一部分
	$QCResult = mysql_query("
	SELECT P.ProductId,P.cName,P.eCode,C.Forshort FROM  $DataIn.productdata P 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
	WHERE p.Estate=1 AND p.TypeId='$TypeId'  AND P.ProductId NOT IN (SELECT  ProductId FROM $DataIn.qcstandardless WHERE QcId='$QCId')
    ORDER BY C.CompanyId
	",$link_id);
        }
else {
	$QCResult = mysql_query("
	SELECT Q.ProductId,P.cName,P.eCode,C.Forshort FROM $DataIn.qcstandardimg Q
	LEFT JOIN $DataIn.productdata P ON P.ProductId=Q.ProductId
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
	WHERE  Q.QcId='$QCId' AND p.Estate=1 ORDER BY C.CompanyId
	",$link_id);
}
$i=1;
$tempi=1;
$tempProductId="";
if ($QCRows = mysql_fetch_array($QCResult)) {
	do{
		$Forshort=$QCRows["Forshort"];
		$cName=$QCRows["cName"];
		$eCode=$QCRows["eCode"];
		$ProductId=$QCRows["ProductId"];
		
			
			//if($IsType==1) {

			$StuffResult = mysql_query("
				SELECT D.StuffId,D.StuffCname,Q.Title
				FROM $DataIn.pands A
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
				LEFT JOIN $DataIn.stuffqcstandard Q ON Q.TypeId=D.TypeId
				WHERE A.ProductId='$ProductId' AND Q.IsType=1  AND D.Estate=1 AND A.ProductId NOT IN (SELECT  ProductId FROM $DataIn.qcstandardless ) AND  D.StuffId IS NOT NULL
				UNION ALL 
				SELECT D.StuffId,D.StuffCname,Q.Title
				FROM $DataIn.pands A
				LEFT JOIN $DataIn.stuffqcimg G ON G.StuffId=A.StuffID
				LEFT JOIN $DataIn.stuffqcstandard Q ON Q.Id=G.QcId
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				WHERE A.ProductId='$ProductId' AND Q.IsType=0 AND D.Estate=1 AND D.StuffId IS NOT NULL
				
				
				",$link_id);
           /*
			}
			else {
			$StuffResult = mysql_query("
				SELECT D.StuffId,D.StuffCname,Q.Title
				FROM $DataIn.pands A
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
				LEFT JOIN $DataIn.stuffqcstandard Q ON Q.TypeId=D.TypeId
				WHERE A.ProductId='$ProductId' AND Q.IsType=1  AND D.Estate=1 AND  D.StuffId IS NOT NULL
				UNION ALL 
				SELECT D.StuffId,D.StuffCname,Q.Title
				FROM $DataIn.pands A
				LEFT JOIN $DataIn.stuffqcimg G ON G.StuffId=A.StuffID
				LEFT JOIN $DataIn.stuffqcstandard Q ON Q.Id=G.QcId
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				WHERE A.ProductId='$ProductId' AND Q.IsType=0 AND D.Estate=1 AND D.StuffId IS NOT NULL
				",$link_id);
			}
			*/

			$StuffId="&nbsp;";
			$StuffCname="&nbsp;";
			$Title="&nbsp;";
			$bgcolor='#EAEAEA';					
			$style="";
			if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
			  $tempi=1;
				do{
					
					$StuffId=$StuffMyrow["StuffId"];
					$StuffCname=$StuffMyrow["StuffCname"];
					$Title=$StuffMyrow["Title"]==""?"&nbsp;":$StuffMyrow["Title"];
					
					//$bgcolor='#EAEAEA';	
					//echo "$temp: $tempi";
					$style="";
					if($tempi>1){
						
						$style="color:#09F";
		
					}
					
					$tempi= $tempi+1;					
					echo"<tr bgcolor='$bgcolor' style='$style'><td align='center'>$i</td>";	//序号
					echo"<td  align='center'>$Forshort </td>";				//客户				
					echo"<td  align='center'>$ProductId</td>";				//产品Id
					echo"<td>$cName</td>";					//产品名称
					echo"<td>$eCode</td>";					//Code
					echo"<td>$StuffId</td>";	
					echo"<td>$StuffCname</td>";	
					echo"<td>$Title</td>";	
					echo"</tr>";
					
					$i++;
					
					
				} while ($StuffMyrow = mysql_fetch_array($StuffResult));
			}
			else { 
		                
				echo"<tr bgcolor='$bgcolor'><td align='center'>$i</td>";	//序号
				echo"<td  align='center'>$Forshort</td>";				//客户				
				echo"<td  align='center'>$ProductId</td>";				//产品Id
				echo"<td>$cName</td>";					//产品名称
				echo"<td>$eCode</td>";					//Code
				echo"<td>$StuffId</td>";	
				echo"<td>$StuffCname</td>";	
				echo"<td>$Title</td>";	
				echo"</tr>";
				$i++;
			}
 		}while ($QCRows = mysql_fetch_array($QCResult));
	}
else{
	echo"<tr><td height='30'>Nothing</td></tr>";
	}
echo"</table>";
?>
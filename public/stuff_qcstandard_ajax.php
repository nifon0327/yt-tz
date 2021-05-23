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
		<td width='80' align='center'>供应商</td>
		<td width='60' align='center'>配件Id</td>
		<td width='200' align='center'>配件名称</td>
		<td width='80' align='center'>客户</td>
		<td width='60' align='center'>产品ID</td>				
		<td width='200' align='center'>中文名称</td>
		<td width='200' align='center'>Product Code</td>		
		<td width='' align='center'>产品Qc名称</td>
		</tr>";
//订单列表//LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=O.OrderNumber 
$ArrayQCId=explode("|",$QCId);
$QCId=$ArrayQCId[0];
$TypeId=$ArrayQCId[1];
$IsType=$ArrayQCId[2];

if ($IsType==1){
	$QCResult = mysql_query("
	SELECT D.StuffId,D.StuffCname,E.Forshort FROM  $DataIn.stuffdata D
	LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
	LEFT JOIN $DataIn.trade_object E ON E.CompanyId=B.CompanyId 
	WHERE D.Estate=1 AND D.TypeId='$TypeId' AND D.StuffId NOT IN (SELECT StuffId FROM $DataIn.stuffqcless WHERE QcId=$QCId)
     ORDER BY E.CompanyId",$link_id);

}
else {
	$QCResult = mysql_query("
	SELECT Q.StuffId,D.StuffCname,E.Forshort FROM $DataIn.stuffqcimg Q
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=Q.StuffId
	LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
	LEFT JOIN $DataIn.trade_object E ON E.CompanyId=B.CompanyId 
	WHERE  Q.QcId='$QCId' AND D.Estate=1 ORDER BY E.CompanyId",$link_id);
}
$i=1;
$tempi=1;
$tempProductId="";
if ($QCRows = mysql_fetch_array($QCResult)) {
	do{
		$Forshort=$QCRows["Forshort"];
		$StuffCname=$QCRows["StuffCname"];
		$StuffId=$QCRows["StuffId"];
		$StuffResult = mysql_query("
				SELECT P.ProductId,P.cName,P.eCode,C.Forshort as CForshort,Q.Title
				FROM $DataIn.pands A
				LEFT JOIN $DataIn.productdata P ON P.ProductId=A.ProductId
				LEFT JOIN $DataIn.qcstandarddata Q ON Q.TypeId=P.TypeId
				LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
				WHERE A.StuffId='$StuffId' AND Q.IsType=1 AND P.Estate=1  AND A.StuffId NOT IN (SELECT StuffId FROM $DataIn.stuffqcless )
				UNION ALL
				SELECT P.ProductId,P.cName,P.eCode,C.Forshort as CForshort,Q.Title
				FROM $DataIn.pands A
				LEFT JOIN $DataIn.qcstandardimg G ON G.ProductId=A.ProductId
				LEFT JOIN $DataIn.qcstandarddata Q ON Q.Id=G.QcId
				LEFT JOIN $DataIn.productdata P ON P.ProductId=G.ProductId
				LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
				WHERE A.StuffId='$StuffId' AND Q.IsType=0 AND P.Estate=1  ",$link_id);
 
			/*
			}
			else {
			$StuffResult = mysql_query("
				SELECT P.ProductId,P.cName,P.eCode,C.Forshort as CForshort,Q.Title
				FROM $DataIn.pands A
				LEFT JOIN $DataIn.qcstandardimg G ON G.ProductId=A.ProductId
				LEFT JOIN $DataIn.qcstandarddata Q ON Q.Id=G.QcId
				LEFT JOIN $DataIn.productdata P ON P.ProductId=G.ProductId
				LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
				WHERE A.StuffId='$StuffId' AND Q.IsType=0 AND P.Estate=1 
				",$link_id);
	
			
			}
	         */
			$ProductId="&nbsp;";
			$cName="&nbsp;";
			$eCode="&nbsp;";
			$CForshort="&nbsp;";
			$Title="&nbsp;";
			$bgcolor='#EAEAEA';					
			$style="";
			if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
			  $tempi=1;
				do{
					
					$ProductId=$StuffMyrow["ProductId"];
					$cName=$StuffMyrow["cName"];
					$eCode=$StuffMyrow["eCode"];
					$CForshort=$StuffMyrow["CForshort"];					
					
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
					echo"<td  align='center'>$StuffId</td>";				//产品Id
					echo"<td>$StuffCname</td>";					//产品名称
					echo"<td  align='center'>$CForshort</td>";				//客户				
					echo"<td  align='center'>$ProductId</td>";				//产品Id
					echo"<td>$cName</td>";					//产品名称
					echo"<td>$eCode</td>";					//C;	
					echo"<td>$Title</td>";	
					echo"</tr>";
					
					$i++;
					
					
				} while ($StuffMyrow = mysql_fetch_array($StuffResult));
			}
			else { 
		                
					echo"<tr bgcolor='$bgcolor' style='$style'><td align='center'>$i</td>";	//序号
					echo"<td  align='center'>$Forshort </td>";				//客户				
					echo"<td  align='center'>$StuffId</td>";				//产品Id
					echo"<td>$StuffCname</td>";					//产品名称
					echo"<td  align='center'>$CForshort</td>";				//客户				
					echo"<td  align='center'>$ProductId</td>";				//产品Id
					echo"<td>$cName</td>";					//产品名称
					echo"<td>$eCode</td>";					//C;	
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
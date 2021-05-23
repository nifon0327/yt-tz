<?php 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$subTableWidth=980;

$dataArray=explode("|",$args);
$StuffId=$StuffId==""?$dataArray[0]:$StuffId;

echo"<table id='$TableId' width='$subTableWidth'  cellspacing='1' border='1' style='margin-left:60px;'><tr bgcolor='#CCCCCC'>
		<td width='30' height='20'></td>
		<td width='100' align='center'>客户</td>
		<td width='50' align='center'>产品ID</td>
		<td width='250' align='center'>中文名</td>		
		<td width='150' align='center'>Product Code</td>
		<td width='180' align='center'>条码</td></tr>";

$sListSql = "SELECT C.Forshort,A.ProductId,P.cName,P.eCode,P.TestStandard,P.Code,P.Estate
FROM $DataIn.pands A
LEFT JOIN $DataIn.productdata P ON A.ProductId=P.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.stuffdata D ON  D.StuffId=A.StuffId
WHERE  1 AND D.StuffId='$StuffId' order by  P.Estate DESC";
//echo $sListSql;
$sListResult = mysql_query($sListSql,$link_id);
$i=1;
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{
        $Sbgcolor="";
		$Forshort=$StockRows["Forshort"];
		$ProductId=$StockRows["ProductId"];
		$cName=$StockRows["cName"];
	
		$eCode=$StockRows["eCode"];
		$Code=$StockRows["Code"];

		$TestStandard=$StockRows["TestStandard"];
		include "../admin/Productimage/getProductImage.php";
		$Estate=$StockRows["Estate"];
        switch($Estate){
                    case "0": $Sbgcolor ="#FF0000";break;
                    case "2": $Sbgcolor ="#EE9A00";break;
            }
    	echo"<tr bgcolor='$theDefaultColor'>
		<td bgcolor='$Sbgcolor' align='right' height='20'>$i</td>";//
		echo"<td  align='Left' >$Forshort</td>";	
		echo"<td  align='center'>$ProductId</td>";//
		echo"<td  align='Left' >$TestStandard</td>";		
		echo"<td  align='Left'>$eCode</td>";
		echo"<td  align='Left'>$Code</td>";
		echo"</tr>";
		$i=$i+1;
		
		//echo "<td width='55' align='center'>$Date</td>";
	}while ($StockRows = mysql_fetch_array($sListResult));
}
$IdList="";
$depth=0;
get_semifinished($StuffId,$DataIn,$link_id,$IdList,$depth);
//echo $IdList;
if ($IdList!=""){
	$sListResult = mysql_query("SELECT C.Forshort,A.ProductId,P.cName,P.eCode,P.TestStandard,P.Code,P.Estate
	FROM $DataIn.pands A
	LEFT JOIN $DataIn.productdata P ON A.ProductId=P.ProductId
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
	LEFT JOIN $DataIn.stuffdata D ON  D.StuffId=A.StuffId
	WHERE  D.StuffId IN ($IdList) AND NOT EXISTS ( SELECT B.ProductId FROM $DataIn.pands B WHERE B.StuffId='$StuffId' AND B.ProductId=A.ProductId)
		GROUP BY A.ProductId  order by  P.Estate DESC",$link_id);
	if ($StockRows = mysql_fetch_array($sListResult)) {
		do{
	        $Sbgcolor="";
			$Forshort=$StockRows["Forshort"];
			$ProductId=$StockRows["ProductId"];
			$cName=$StockRows["cName"];
		
			$eCode=$StockRows["eCode"];
			$Code=$StockRows["Code"];
	
			$TestStandard=$StockRows["TestStandard"];
			include "../admin/Productimage/getProductImage.php";
			$Estate=$StockRows["Estate"];
	        switch($Estate){
	                    case "0": $Sbgcolor ="#FF0000";break;
	                    case "2": $Sbgcolor ="#EE9A00";break;
	            }
	    	echo"<tr bgcolor='$theDefaultColor'>
			<td bgcolor='$Sbgcolor' align='right' height='20'>$i</td>";//
			echo"<td  align='Left' >$Forshort</td>";	
			echo"<td  align='center'>$ProductId</td>";//
			echo"<td  align='Left' >$TestStandard</td>";		
			echo"<td  align='Left'>$eCode</td>";
			echo"<td  align='Left'>$Code</td>";
			echo"</tr>";
			$i=$i+1;
			
			//echo "<td width='55' align='center'>$Date</td>";
		}while ($StockRows = mysql_fetch_array($sListResult));
	}
}

if ($i==1){
	echo"<tr><td height='30' colspan='6'>无相关的产品.</td></tr>";
}

echo"</table>"."";

function get_semifinished($StuffId,$DataIn,$link_id,&$IdList,&$depth){

	$checkResult=mysql_query("SELECT mStuffId AS mStuffId 
	             FROM $DataIn.semifinished_bom WHERE StuffId IN ($StuffId) GROUP BY mStuffId",$link_id);
	while($checkRow=mysql_fetch_array($checkResult)) {       
      $mStuffId = $checkRow['mStuffId'];
	  $IdList.=$IdList==""?$mStuffId:',' . $mStuffId;  
	  if ($depth<1000){
	        $depth++;
		  get_semifinished($checkRow['mStuffId'],$DataIn,$link_id,$IdList,$depth);
	  }
    }
  
}
?>
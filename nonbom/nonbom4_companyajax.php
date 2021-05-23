<?php 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$i=1;
$checkResult=mysql_query("SELECT   M.CompanyId,M.Forshort
 FROM $DataPublic.nonbom3_link L  
LEFT JOIN $DataPublic.nonbom3_retailermain M ON M.CompanyId=L.CompanyId
WHERE L.TypeId=$TypeId ORDER BY  M.Letter",$link_id);
$echoInfo1="";
$echoInfo2="";
if( $checkResult && $checkRow=mysql_fetch_array($checkResult)){
	$echoInfo1= "<table id='TableId'  cellspacing='0' border='0'>";
	do{ 
		$CompanyId=$checkRow["CompanyId"];
		$Forshort=$checkRow["Forshort"];
	    $echoInfo1.= "<tr><td id='TempName$i' align='left' height='15' onmousemove='ChangeColor($i)' onmouseout='unChangeColor($i)' onmousedown='ChooseCompanyName($i)'>$CompanyId-$Forshort</td></tr>";
	   $echoInfo2.="&nbsp;<input type='checkbox' name='CompanyCheckId[]'  id='CompanyCheckId' value='$CompanyId|$Forshort'>&nbsp;&nbsp;$CompanyId-$Forshort</br>";
	    $i++;
	    }while($checkRow=mysql_fetch_array($checkResult));
	$echoInfo1.= "</table>";
    }
if($ActionId==1)echo $echoInfo1;
else {
          echo $echoInfo2."@".$i;
         }
?>
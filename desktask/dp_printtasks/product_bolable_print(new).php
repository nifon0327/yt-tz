<?php   
include "../../basic/parameter.inc";
$checkRow=mysql_fetch_array(mysql_query("SELECT cName,eCode,Code,TypeId 
FROM  $DataIn.productdata WHERE ProductId='$ProductId' ORDER BY Id LIMIT 1",$link_id));
$cName=$checkRow["cName"];
//$cName=iconv("UTF-8","GB2312",$cName);
$eCode=$checkRow["eCode"];
$BoxCode=$checkRow["Code"];
$TypeId=$checkRow["TypeId"];
$Code=substr($BoxCode,-4);


$PrintRow=mysql_fetch_array(mysql_query("SELECT S.Date,Y.Qty AS OrderQty,Y.POrderId,T.Forshort,SC.Qty AS scQty,
IFNULL(Y.OrderPO ,M.OrderPO) AS OrderPO
FROM $DataIn.sc3_printtasks S 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
LEFT JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId 
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.trade_object T ON T.CompanyId = M.CompanyId
WHERE S.Id = $Id",$link_id));	
$PrintDate = $PrintRow["Date"];
$OrderQty= $PrintRow["OrderQty"];
$POrderId= $PrintRow["POrderId"];
$Forshort =$PrintRow["Forshort"];
$OrderPO=$PrintRow["OrderPO"];
$scQty= sprintf("%.0f", $PrintRow["scQty"]);
$cName = $Forshort."-".$cName;

$Relation="";
$StuffTypeSTR="and T.TypeId='9040'";
$BoxResult = mysql_query("SELECT P.Relation 
FROM $DataIn.pands P 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId 
LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
WHERE 1 and P.ProductId='$ProductId' AND P.ProductId>0 $StuffTypeSTR",$link_id);
if($BoxRows = mysql_fetch_array($BoxResult)){
	$Relation=$BoxRows["Relation"];
	
	}

$rSql = mysql_query("SELECT Relation FROM  
$DataIn.sc1_newrelation WHERE POrderId='$POrderId' order by ID DESC Limit 1 ",$link_id);
if ($rRows = mysql_fetch_array($rSql)){
	$Relation="1/".$rRows["Relation"];
	}										

if ($Relation!=""){
   $RelationArray=explode("/",$Relation);
   $Relation=$RelationArray[1];
   }
 $BoxPcs=ceil($scQty/$Relation);  
 
 //外箱条码
 
$boxcodeResult=mysql_query("SELECT D.StuffCname
FROM $DataIn.yw1_ordersheet Y 
LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId = Y.POrderId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId = G.StuffId
WHERE Y.POrderId = '$POrderId' AND D.TypeId = 9124",$link_id);
while($boxcodeRow = mysql_fetch_array($boxcodeResult)){
	$StuffCname = $boxcodeRow["StuffCname"];
	if(strpos($StuffCname,"外箱")){
		$tempArray = explode("-", $StuffCname);
		$boxCode = end($tempArray);
		break;
	}
	$tempArray = explode("-", $StuffCname);
	$boxCode = end($tempArray);
}

if($TypeId == 8061 || $TypeId == 8051){
	$boxCode ="";
}
?>

<style type="text/css">
<!--
body,td,th {
	font-family: Tahoma,Helvetica,Arial;
	font-size: 10pt;
	/*font-weight: bold;*/
}
body {
	margin-left: 4px;
	margin-top: 2px;
}
#BackD{
	width:472px;
	margin:0;
	padding:0;
	}
#BackD #backL{
	width:236px;
	height:177px;
	margin:0;
	padding:0;
	
	float:left;
	}
#backL #Num1{
	line-height: 21px;
	margin-left:30px;
	margin-top: 2px;
	}

#backL #Num2{
    line-height: 21px;
	margin-left: 30px;
	margin-top: 4px;
	}


-->
</style>
<script type="text/javascript" language="javascript">
//<![CDATA[
// Do print the page
window.onload = function()
{
    if (typeof(window.print) != 'undefined') {
        window.print();
    }
}
//]]>
</script>

<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head>


<div id="BackD">
	<div id="backL">
	    <div id="Num1" ><?php echo $OrderPO?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $PrintDate?></div>
		<div id="Num2"><?php  echo $cName?></div>
		<div id="Num2"><?php  echo $eCode?></div>
		<div id="Num2">数量:<?php echo $scQty?>&nbsp;&nbsp;&nbsp;&nbsp;箱数:<?php    echo $BoxPcs."(".$Relation.")"?></div>
		<div id="Num2"><?php   echo $boxCode?></div>
	</div>
		<div id="backL">
	    <div id="Num1" ><?php echo $OrderPO?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $PrintDate?></div>
		<div id="Num2"><?php    echo $cName?></div>
		<div id="Num2"><?php    echo $eCode?></div>
		<div id="Num2">数量:<?php    echo $scQty?>&nbsp;&nbsp;&nbsp;&nbsp;箱数:<?php    echo $BoxPcs."(".$Relation.")"?></div>
		<div id="Num2"><?php     echo $boxCode?></div>
	</div>
	
	
</div>

<div id="BackD">
	<div id="backL">
	    <div id="Num1" ><?php echo $OrderPO?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $PrintDate?></div>
		<div id="Num2"><?php     echo $cName?></div>
		<div id="Num2"><?php     echo $eCode?></div>
		<div id="Num2">数量:<?php echo $scQty?>&nbsp;&nbsp;&nbsp;&nbsp;箱数:<?php    echo $BoxPcs."(".$Relation.")"?></div>
		<div id="Num2"><?php     echo $boxCode?></div>
	</div>
		<div id="backL">
	    <div id="Num1" ><?php echo $OrderPO?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $PrintDate?></div>
		<div id="Num2"><?php  echo $cName?></div>
		<div id="Num2"><?php  echo $eCode?></div>
		<div id="Num2">数量:<?php echo $scQty?>&nbsp;&nbsp;&nbsp;&nbsp;箱数:<?php    echo $BoxPcs."(".$Relation.")"?></div>
		<div id="Num2"><?php     echo $boxCode?></div>
	</div>
	
	
</div>
</head>
</html>

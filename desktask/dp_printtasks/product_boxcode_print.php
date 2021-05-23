<?php   
//电信-ZX  2012-08-01
/*
已更新
*/
include "../../basic/parameter.inc";
if($Type==4){//白盒坑盒
      $TypeId=" AND S.TypeId='9103'";
     }
else{//PE袋标签
      $TypeId=" AND S.TypeId=9002";  
    }

$checkStuff=mysql_fetch_array(mysql_query("SELECT A.Relation From $DataIn.pands  A
          LEFT JOIN $DataIn.stuffdata S ON S.StuffId=A.StuffId
          LEFT JOIN $DataIn.productdata P ON P.ProductId=A.ProductId
          WHERE P.ProductId = '$p' $TypeId",$link_id));
$Relation=$checkStuff["Relation"];
/*echo "SELECT A.Relation From $DataIn.pands  A
          LEFT JOIN $DataIn.stuffdata S ON S.StuffId=A.StuffId
          LEFT JOIN $DataIn.productdata P ON P.ProductId=A.ProductId
          WHERE P.ProductId = '$p' $TypeId";*/

$checkRow=mysql_fetch_array(mysql_query("SELECT P.cName,P.eCode,P.Code,C.Forshort,P.Description,C.CompanyId
FROM $DataIn.productdata P
LEFT JOIN $DataIn.yw1_ordersheet S ON S.ProductId = P.ProductId
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = S.OrderNumber
LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
WHERE P.ProductId = '$p' ORDER BY P.Id LIMIT 1 ",$link_id));

$cName=$checkRow["cName"];
$cName=iconv("UTF-8","GB2312",$cName);
$eCode=$checkRow["eCode"];
$CompanyId=$checkRow["CompanyId"];
$Forshort=$CompanyId=="1077"?"ISY":$checkRow["Forshort"];
if(strlen($Forshort)<=5){
       $Forshort=$Forshort."&nbsp;".$Forshort."&nbsp;".$Forshort."&nbsp;".$Forshort."&nbsp;".$Forshort."&nbsp;".$Forshort."&nbsp;".$Forshort;
	   }
 else if(strlen($Forshort)<10){
       $Forshort=$Forshort."&nbsp;".$Forshort."&nbsp;".$Forshort."&nbsp;".$Forshort;
	   }
 else{
       $Forshort=$Forshort."&nbsp;".$Forshort."&nbsp;";
       }
$Description=$checkRow["Description"];
$BoxCode=$checkRow["Code"];
$Field=explode("|",$BoxCode);
$BoxCode0=$Field[0];
$BoxCode1=$Field[1];
$Relation=explode("/",$Relation);
if($Relation[1]!=""){
$Qty="QTY:".$Relation[1]."PCS";}
else{$Qty="QTY:".$Relation[0]."PCS";}
$s=$s==""?7:$s;
$BoxCodeNum1=substr($BoxCode1,0,1);
$BoxCodeNum2=substr($BoxCode1,1,6);
$BoxCodeNum3=substr($BoxCode1,-6);
//echo $BoxCode1;
?>
<style type="text/css">
<!--
body,td,th {
    font-family:Arial;
	font-size: <?php    echo $s?>pt;
	margin-top: 10px;
	margin_left:20px;
}
#BackD{
	width:400px;
	margin:0;
	padding:0;
	}
#BackD #backL{
	width:180px;
	height:120px;
	margin:0;
	padding:0;
	MARGIN-RIGHT: 2px;
	float:left;
	}
#backL #CodeTop{
    font-size:9px;
	MARGIN-LEFT:10px;
	MARGIN-BOTTOM:2px;
	}
#backL #CodeText{
    font-size:9px;
	MARGIN-LEFT:10px;
	MARGIN-BOTTOM:2px;
	}
#backL #CodeBottom{
    font-size:9px;
	MARGIN-LEFT:10px;
	MARGIN-BOTTOM:5px;
	}

#backL #CodeImg{
    MARGIN-LEFT:10px;
	MARGIN-BOTTOM:2px;
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
<div id="BackD">
	<div id="backL">
	    <div id="CodeTop" ><?php    echo $Forshort?></div>
		<div id="CodeText"><?php    echo $BoxCode0?><BR><?php    echo $Description?><BR><?php    echo $Qty?></div>
		<div id="CodeImg"><img width='180' height='56'  src='Print_AutoCode1.php?Code=<?php    echo $BoxCode1?>&amp;lw=1&amp;hi=25'></img></div>
		<div id="CodeBottom"><?php    echo $Forshort?></div>
	</div>
	<div id="backL">
	    <div id="CodeTop" ><?php    echo $Forshort?></div>
		<div id="CodeText"><?php    echo $BoxCode0?><BR><?php    echo $Description?><BR><?php    echo $Qty?></div>
		<div id="CodeImg"><img width='180' height='56' src='Print_AutoCode1.php?Code=<?php    echo $BoxCode1?>&amp;lw=1&amp;hi=25'></img></div>
		<div id="CodeBottom"><?php    echo $Forshort?></div>
	</div>
</div>

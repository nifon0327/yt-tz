<?php 
//代码共享-EWEN 2012-09-18
include "../../basic/chksession.php" ;
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$i=1;
$Condition=$Condition==""?"":" AND ".$Condition;
switch($Action){
   case 1://共享数据表
       $checkResult=mysql_query("SELECT $FieldName FROM $DataPublic.$Table WHERE $FieldName LIKE '%$qcName%' $Condition LIMIT 100",$link_id);
	 break;
	case 2://内部数据表
	    $checkResult=mysql_query("SELECT $FieldName FROM $DataIn.$Table WHERE $FieldName LIKE '%$qcName%' $Condition LIMIT 100",$link_id);
	 break;
	 case 3://外部数据表
	    $checkResult=mysql_query("SELECT $FieldName FROM $DataOut.$Table WHERE $FieldName LIKE '%$qcName%' $Condition LIMIT 100",$link_id);
	 break;
	}
if( $checkResult && $checkRow=mysql_fetch_array($checkResult)){
	echo "<table id='TableId'  cellspacing='0' border='0'>";
	do{ 
		$cName=$checkRow["$FieldName"];
	    echo "<tr><td id='TempName$i' align='left' height='15' onmousemove='ChangeColor($i)' onmouseout='unChangeColor($i)' onmousedown='ChooseName($i,\"$FieldName\")'>$cName</td></tr>";
	    $i++;
	    }while($checkRow=mysql_fetch_array($checkResult));
	echo "</table>";
    }
?>
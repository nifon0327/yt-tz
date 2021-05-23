<?php 
//电信-ZX  2012-08-01
//步骤1
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

switch($Action){
   case 1://来自产品名称查询
       $i=1;
       $cNameResult=mysql_query("SELECT $FieldName FROM $DataIn.$Table WHERE $FieldName LIKE '%$qcName%'",$link_id);
       if($cNameRow=mysql_fetch_array($cNameResult)){
	   echo "<table id='TableId'  cellspacing='0' border='0'>";
	       do{ 
	           $cName=$cNameRow["$FieldName"];
	           echo "<tr><td id='TempName$i' align='left' height='15' onmousemove='ChangeColor($i)' onmouseout='unChangeColor($i)' onmousedown='ChooseName($i,\"$FieldName\")'>$cName</td></tr>";
	        $i++;
	       }while($cNameRow=mysql_fetch_array($cNameResult));
		 echo "</table>";
        }
	 break;
	/* case 2://来自配件名称查询
	    $i=1;
		 $cNameResult=mysql_query("SELECT StuffCname FROM $DataIn.stuffdata WHERE StuffCname LIKE '%$qcName%'",$link_id);
       if($cNameRow=mysql_fetch_array($cNameResult)){
	   echo "<table id='$TableId'  cellspacing='0' border='0'>";
	       do{ 
	           $cName=$cNameRow["StuffCname"];
	           echo "<tr><td id='productName$i' align='left' height='15' onmousemove='ChangeColor($i)' onmouseout='unChangeColor($i)' onmousedown='ChooseName($i)'>$cName</td></tr>";
	        $i++;
	       }while($cNameRow=mysql_fetch_array($cNameResult));
		 echo "</table>";
        }
	 break;*/
}
?>
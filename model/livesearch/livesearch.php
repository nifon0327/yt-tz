<?php 
//代码共享-EWEN 2013-03-06 加入条件，以便过滤数据
include "../../basic/chksession.php" ;
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$i=1;
$Condition=$Condition==""?"":" AND ".$Condition;//条件
switch($Action){
     case 1://共享数据表 
	 case 2://内部数据表    
	 case 3://外部数据表
	    $checkResult=mysql_query("SELECT 0 AS Id, $FieldName FROM $DataIn.$Table 
	                 WHERE $FieldName LIKE '%$qcName%' $Condition LIMIT 100",$link_id);
	 break;
	 case 12://共享数据表-内部数据表
	    $ATable=explode('|',$Table);		
	    $checkResult=mysql_query("SELECT 0 AS Id, $FieldName FROM $DataPublic.$ATable[0] 
	                              WHERE $FieldName LIKE '%$qcName%' $Condition LIMIT 100
								  UNION 
								  SELECT 0 AS Id, $FieldName FROM $DataIn.$ATable[1] 
								  WHERE $FieldName LIKE '%$qcName%' $Condition LIMIT 100",$link_id);
	 break;
	 case 4://特殊：安全生产分类
	 $checkResult=mysql_query("SELECT 0 AS Id, A.Name FROM $DataPublic.$Table A
							  LEFT JOIN $DataPublic.$Table B ON B.PreItem=A.Id
							  WHERE A.Name LIKE '%$qcName%' AND A.Estate='1' AND B.Id IS NULL LIMIT 100",$link_id);
    break;	
    
    case 5://特殊，取Id
        if ($FieldName == "SeatId"){//备品转入
            $checkResult=mysql_query("SELECT Id, SeatId, WareHouse, ZoneName FROM $DataIn.$Table 
	                 WHERE $FieldName LIKE '%$qcName%' $Condition LIMIT 100",$link_id);
        }else{
            $checkResult=mysql_query("SELECT Id, $FieldName FROM $DataIn.$Table 
	                 WHERE $FieldName LIKE '%$qcName%' $Condition LIMIT 100",$link_id);
        }
	 break;	
	 
	case 6://特殊，取Id
	    $checkResult=mysql_query("SELECT 0 AS Id, $FieldName FROM $DataIn.$Table 
	    WHERE $FieldName LIKE '%$qcName%' $Condition GROUP BY $FieldName LIMIT 100",$link_id);
	 break;					  
	}
if( $checkResult && $checkRow=mysql_fetch_array($checkResult)){
	echo "<table id='TableId'  cellspacing='0' border='0'>";
	do{ 
		$cName=$checkRow["$FieldName"];
		$Id=$checkRow["Id"];

	    echo "<tr><td id='TempName$i' align='left' height='15' onmousemove='ChangeColor($i)' onmouseout='unChangeColor($i)' onmousedown='ChooseName($i,\"$FieldName\",\"$Id\")'>$cName</td></tr>";
	    $i++;
	    }while($checkRow=mysql_fetch_array($checkResult));
	echo "</table>";
    }
?>
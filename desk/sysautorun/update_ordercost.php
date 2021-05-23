<?php
header("Content-Type: text/html; charset=utf-8");
$MyPDOEnabled=1;
include "../../basic/parameter.inc";
include "../../model/systemfunction.php";

if ($doAction==1){
			  ob_end_clean();     //在循环输出前，要关闭输出缓冲区   
			  echo str_pad('',256);   
			  
			  $i=0;
      
			 $checkIdSql = " SELECT A.POrderId  FROM  yw1_ordersheet  A   LEFT JOIN yw1_ordercost S ON S.POrderId=A.POrderId WHERE  S.Incomplete=1 OR   S.POrderId IS NULL"; 
			// $checkIdSql = " SELECT A.POrderId  FROM  yw1_ordersheet  A WHERE  A.Estate>0"; 
			 
			 $checkIdResult = $myPDO->query($checkIdSql);     
			 while($checkIdRow =$checkIdResult->fetch(PDO::FETCH_ASSOC)) {
			            $POrderId= $checkIdRow['POrderId'];
			            
			             
			             $myPDO->query(" START TRANSACTION;");
			             $myResult=$myPDO->query("SELECT setOrderCostPrice($POrderId) AS Counts");
			             $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
						$Counts = $myRow['Counts'];   
			             $myPDO->query(" COMMIT;");
			             
			             $i++;
			             if ($Counts>0) {
				              echo "<div style='color:#00BB00'>$i - " . $POrderId . "更新成功;</div>";
			             }else{
				              echo "<div style='color:#FF0000'>$i - " . $POrderId . "更新失败;</div>";
			             }
			                    
			             
			             flush();    //刷新输出缓冲   
			 }
			  $myPDO=null;
			  ob_end_flush();
			  
			  echo "--End--更新数量:$i";
}
?>
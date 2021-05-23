<?php
header("Content-Type: text/html; charset=utf-8");
$MyPDOEnabled=1;
include "../../basic/parameter.inc";
include "../../model/systemfunction.php";

if ($doAction==1){
		  ob_end_clean();     //在循环输出前，要关闭输出缓冲区   
		  echo str_pad('',256);   
		  do {
					  $n=1;
					  $Counts = 0;
					  $OrderBySTR =$n%2==0?"ORDER BY A.mStuffId DESC ":" ORDER BY A.mStuffId ";
					  
					 $checkIdSql = "
					             SELECT A.mStuffId,D.StuffCname FROM  semifinished_bom A 
					             LEFT JOIN stuffdata D  ON D.StuffId=A.mStuffId 
							     LEFT JOIN stufftype T ON T.TypeId=D.TypeId
							     LEFT JOIN stuffmaintype M ON M.Id=T.mainType 
					             WHERE  1  GROUP BY A.mStuffId    $OrderBySTR
					 "; 
					 
					 $checkIdResult = $myPDO->query($checkIdSql);     
					 while($checkIdRow =$checkIdResult->fetch(PDO::FETCH_ASSOC)) {
					            $StuffId = $checkIdRow['mStuffId'];
					            
					             
					             $myPDO->query(" START TRANSACTION;");
					            // $myResult=$myPDO->query("CALL proc_stuffdata_costprice('$StuffId');");
					             $myResult=$myPDO->query("SELECT setNewStuffCostPrice($StuffId) AS Counts");
					             $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
								$upCounts = $myRow['Counts'];   
					             $myPDO->query(" COMMIT;");
					             
					             if ($upCounts>0) {
					                 $Counts++;
						              echo "<div style='color:#FF0000'>" . $StuffId . ' - ' . $checkIdRow['StuffCname'] ."</div>";
					             }		                    
					             
					             flush();    //刷新输出缓冲   
					 }
					 $n++;
		 }while($Counts >0);
		 
		$myPDO=null;
		ob_end_flush();
		  
       echo "--End--";
}
?>
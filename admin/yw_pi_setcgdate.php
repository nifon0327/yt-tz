<?php   
 //按设置的交货周期更新交货日期  //1、订单采购单
$MyPDOEnabled=1;
if ($myPDO==null){
	include "../basic/parameter.inc";
}
//echo "Ids:$Ids";
$CheckSql="SELECT S.POrderId,P.Leadtime,IFNULL(E.ReduceWeeks,-1) AS ReduceWeeks  
            FROM $DataIn.yw1_ordersheet S
			LEFT JOIN $DataIn.yw3_pisheet P ON S.Id=P.oId
			LEFT JOIN $DataIn.yw2_cgdeliverydate E ON E.POrderId=S.POrderId 
			WHERE S.Id IN ($Ids)  AND P.Leadtime IS NOT NULL";
			
$CheckResult=$myPDO->query($CheckSql);	        		        			
while($CheckRow = $CheckResult->fetch(PDO::FETCH_ASSOC)){
     $sPOrderId=$CheckRow["POrderId"]; 
     $ReduceWeeks=$CheckRow["ReduceWeeks"]; 
      
     $Leadtime=explode("*",$CheckRow["Leadtime"]);
     $PIDate=$Leadtime[0];
    
     
     $dateResult =$myPDO->query("SELECT YEARWEEK('$PIDate',1)  AS PIWeek,YEARWEEK(CURDATE(),1) AS CurWeek");
     $dateRow = $dateResult->fetch(PDO::FETCH_ASSOC);	
     $PIWeek=$dateRow["PIWeek"];
     $dateRow=null;$dateResult=null;
     
     if ($PIWeek>0){
	     $setResult=$myPDO->query("CALL proc_yw1_ordersheet_setdeliverydate('$sPOrderId','$PIDate',$ReduceWeeks,'0',$Operator);");
		 $setRow = $setResult->fetch(PDO::FETCH_ASSOC);
		 $Log.=$setRow['OperationLog'] . "<br>";
	     $setResult=null;$setRow=null;
     } 
}

?>

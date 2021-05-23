<?php
$ProcessStr ="";
$CheckGxQtyResult=mysql_query("SELECT B.ProcessId,IFNULL(SUM(SC.Qty*B.Relation),0) AS gxQty,
PT.Color,PD.ProcessName ,PD.BassLoss
FROM $DataIn.cg1_processsheet B 
LEFT JOIN $DataIn.yw1_scsheet SC ON SC.StockId=B.StockId 
LEFT JOIN $DataIn.process_data PD ON PD.ProcessId=B.ProcessId
LEFT JOIN $DataIn.process_type PT ON PT.gxTypeId=PD.gxTypeId
WHERE B.StockId='$StockId'  AND SC.sPOrderId =$sPOrderId GROUP BY B.ProcessId ORDER BY PT.SortId,B.Id ",$link_id);
$ProcessStr="";    $LowMinQty=0;       $thisLowMinQty="";
$ProcessNameArray=array();
$ProcessIdArray=array();
$SumGXQtyArray=array();
if($checkGxRow = mysql_fetch_array($CheckGxQtyResult)){
     $ProcessStr="<div class='divmain'><ul style='background:#FFFFFF'>";
  do{
       $tempProcessId=$checkGxRow["ProcessId"];
       $tempProcessName=$checkGxRow["ProcessName"];
       $SumGXQty=$checkGxRow["gxQty"];
       $ProcessIdArray[]=$tempProcessId;
       $ProcessNameArray[]=$tempProcessName;
       $SumGXQtyArray[]=$SumGXQty;
       $BassLoss=$checkGxRow["BassLoss"];
       $thisColor=$checkGxRow["Color"];
       
       $BassQty=floor($BassLoss*$MaxQty);
       if($LowMinQty==0){
                    $LowMinQty=$MaxQty-$BassQty;
               }
       else{
             $LowMinQty=$LowMinQty-$BassQty;
        }

      if($thisProcessId==$tempProcessId){
              $thisLowMinQty=$LowMinQty;
       }
      $tempGxQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS gxQty FROM $DataIn.sc1_gxtj C WHERE  C.StockId='$StockId' AND C.ProcessId='$tempProcessId' AND C.sPOrderId = '$sPOrderId'",$link_id));
      $tempthisGxQty=$tempGxQty["gxQty"];

      if($tempthisGxQty<$thisLowMinQty && $tempthisGxQty>0)$tempthisGxQty="<span class='redB'>$tempthisGxQty</span>";

      $ProcessStr.="<li class='liwidth' style='border:none;background:$thisColor;' title='本道工序最低登记:$LowMinQty,允许损耗的数量:$BassQty'><span style='font-size:80%'>$tempProcessName</span><br>$tempthisGxQty</li>";     
     }while($checkGxRow = mysql_fetch_array($CheckGxQtyResult));
}
?>
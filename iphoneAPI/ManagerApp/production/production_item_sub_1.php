<?php 
//供应商送货信息
    
     $FromPageName="sh";
     $CheckResult=mysql_fetch_array(mysql_query("SELECT  COUNT(*) AS Nums,SUM(S.Qty) AS shQty,SUM(IF(YEARWEEK(G.DeliveryDate,1)<YEARWEEK(CURDATE(),1),S.Qty,0)) AS OverQty  FROM $DataIn.gys_shsheet S 
     LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
     LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
     WHERE S.Estate=1  AND S.SendSign IN(0,1) AND M.Floor='$Floor' ",$link_id));
     
    $Nums=$CheckResult["Nums"]==""?0:$CheckResult["Nums"];
    $TotalQty=$CheckResult["shQty"]==""?0:number_format($CheckResult["shQty"]);
   $OverQty=$CheckResult["OverQty"]==0?"":number_format($CheckResult["OverQty"]);  
   
   //逾期
   /*
   $OverResult= mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty  FROM $DataIn.gys_shsheet S 
           LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
            WHERE  S.Estate=1 AND M.Floor=6 AND TIMESTAMPDIFF(minute,M.Date,Now())>360",$link_id));
     $OverQty=$OverResult["Qty"]==0?"":number_format($OverResult["Qty"]);    
    */ 
     //差最后一个配件
     $LastQty=0;
     $LastBlResult=mysql_query("SELECT S.StuffId,S.StockId,S.Qty  FROM $DataIn.gys_shsheet S 
     LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
     WHERE  S.Estate=1  AND M.Floor='$Floor' AND S.SendSign=0",$link_id);
     while ($LastBlRow = mysql_fetch_array($LastBlResult)){
            $StuffId=$LastBlRow["StuffId"];
            $StockId=$LastBlRow["StockId"];
            $POrderId=substr($StockId,0,12);
            $Qty=$LastBlRow["Qty"];
            include "../../model/subprogram/stuff_blcheck.php";
            if ($LastBlSign==1) $LastQty+=$Qty;
    }
    
   $AddCols=array();
   if ($LastQty>0){
        $LastQty=number_format($LastQty); 
        $AddCols[]=array("Title"=>"$LastQty","Frame"=>"55, 2, 60,40","Align"=>"R","Color"=>"#00BB00");
   }
?>
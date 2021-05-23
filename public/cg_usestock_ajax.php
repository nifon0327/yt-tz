<?php   
//电信-zxq 2012-08-01
session_start();

if($ActionId==1){
	include "../model/modelhead.php";
	echo "<link rel='stylesheet' href='../model/input.css'>";
}
else{
	
	include "../basic/chksession.php" ;
	include "../basic/parameter.inc";
	include "../model/modelfunction.php";
	header("Content-Type: text/html; charset=utf-8");
	header("expires:mon,26jul199705:00:00gmt");
	header("cache-control:no-cache,must-revalidate");
	header("pragma:no-cache");
}

$OperationResult="Y";
$tableWidth = "800px";
//步骤2：
switch($ActionId*1){
     

   case 1:
   
     $curDate=date("Y-m-d");
	 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$curDate',1) AS CurWeek",$link_id));
	 $curWeek=$dateResult["CurWeek"];
     $checkResult = mysql_query("SELECT D.StuffCname,D.Picture,K.oStockQty 
     FROM $DataIn.stuffdata D 
     LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId = D.StuffId 
     WHERE D.StuffId=$StuffId ",$link_id);

     if($checkRow = mysql_fetch_array($checkResult)){

        $StuffCname = $checkRow["StuffCname"];
        $Picture = $checkRow["Picture"];
        $oStockQty= $checkRow["oStockQty"];
        include "../model/subprogram/stuffimg_model.php"; ////检查是否有图片
        include"../model/subprogram/stuff_Property.php";//配件属性	
        


        $CheckGRow=mysql_fetch_array(mysql_query("SELECT SUM(IFNULL(A.OrderQty,0)) AS orderQty,
        SUM(IFNULL(A.cgQty,0)) AS cgQty,
		SUM(IFNULL(A.bpQty,0)) AS bpQty,
		SUM(IFNULL(A.bfQty,0)) AS bfQty
		FROM (
	        SELECT SUM(OrderQty) AS orderQty,0 AS cgQty,0 AS bpQty,0 AS bfQty
	        FROM $DataIn.cg1_stocksheet WHERE StuffId='$StuffId' 
	     UNION ALL 
	        SELECT 0 AS orderQty,SUM(FactualQty+AddQty) AS cgQty,0 AS bpQty,0 AS bfQty
	        FROM  $DataIn.cg1_stocksheet WHERE StuffId='$StuffId'         
	     UNION ALL
			SELECT 0 AS orderQty,0 AS cgQty,SUM(Qty) AS bpQty,0 AS bfQty
            FROM $DataIn.ck1_rksheet WHERE StuffId='$StuffId'  AND Type=2  
		 UNION ALL
			SELECT 0 AS orderQty,0 AS cgQty,0 AS bpQty,SUM(IF(Type=6,-Qty,Qty)) AS bfQty
 			FROM $DataIn.ck5_llsheet WHERE StuffId='$StuffId'   AND  Type IN (2,6)
		UNION ALL 
			SELECT 0 AS orderQty,0 AS cgQty,0 AS bpQty,0 AS bfQty  
			FROM $DataIn.ck1_rksheet WHERE StuffId='$StuffId' AND Type=5				
	   )A",$link_id));
	      
       
	   $orderQty=$CheckGRow["orderQty"];
       $cgQty=$CheckGRow["cgQty"];
       $bpQty=$CheckGRow["bpQty"];
       $bfQty=$CheckGRow["bfQty"];
       
	   $oValue=round($cgQty+$bpQty-$orderQty-$bfQty,1); 
	   
	   if($oStockQty>0 && $oValue<0){
	         $UpdateSql="Update $DataIn.ck9_stocksheet SET  oStockQty = 0 WHERE StuffId='$StuffId'";
		     $UpdateResult = mysql_query($UpdateSql);
		     
       }
   
       echo "</br><div style='width:$tableWidth;text-align:left;'><b>配件名称：</b>$StuffCname</div></br>";
       echo "<div style='width:$tableWidth;text-align:left;'><b>库存表的可用库存：</b>$oStockQty</div></br>";
       echo "<div style='width:$tableWidth;text-align:left;'><b>分析到的可用库存：</b>$oValue</div></br>";
       
       if($oValue<0){
       
        
       
		echo"<table cellspacing='0' border='0' width='$tableWidth' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>";
		echo"<tr bgcolor='timeTop'>
		<td align='center' class='A1111' width='30' height='30'>序号</td>
		<td align='center' class='A1101' width='110'>需求日期</td>
		<td align='center' class='A1101' width='120'>流水号</td>
		<td align='center' class='A1101' width='70'>订单数量</td>
		<td align='center' class='A1101' width='70'>使用库存</td>
		<td align='center' class='A1101' width='70'>增购数量</td>
		<td align='center' class='A1101' width='70'>实购数量</td>
		<td align='center' class='A1101' width='80'>供应商</td>
		<td align='center' class='A1101' width='80'>交期</td>
		<td align='center' class='A1101' width='100'>更新</td>
		</tr>";
		$mySql="SELECT S.StockId,S.POrderId,S.OrderQty,S.StockQty,S.AddQty,
		S.FactualQty,S.DeliveryWeek,S.DeliveryDate,S.ywOrderDTime,T.Forshort 
		FROM $DataIn.cg1_stocksheet S 
		LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = S.POrderId
		LEFT JOIN $DataIn.trade_object  T ON T.CompanyId = S.CompanyId
		WHERE S.StuffId = '$StuffId'  AND S.StockQty>0 AND S.Mid=0 AND S.DeliveryWeek>'$curWeek'
		ORDER BY S.DeliveryWeek DESC"; //AND S.DeliveryWeek>'$curWeek' AND S.Mid=0
		$myResult = mysql_query($mySql."",$link_id);
		if($myRow = mysql_fetch_array($myResult)){
			$j=1;
			do{
				$StockId=$myRow["StockId"];
				$POrderId=$myRow["POrderId"];
				$OrderQty=$myRow["OrderQty"];			
				$StockQty=$myRow["StockQty"];			
				$AddQty=$myRow["AddQty"];				
				$FactualQty=$myRow["FactualQty"];		
				$Forshort=$myRow["Forshort"];
				$DeliveryDate=$myRow["DeliveryDate"];
				$ywOrderDTime=$myRow["ywOrderDTime"];
				$DeliveryWeek=$myRow["DeliveryWeek"];
				include "../model/subprogram/deliveryweek_toweek.php"; 
   
                $updateStr = "<input class='inputTxt2' type='text' id='useStockQty$j' size='10' onblur='UpdateStockQty(this,$StockId,$StockQty,$oValue)' >";


				echo"<tr $bgcolor>
				<td align='center' class='A0111' height='25'>$j</td>
				<td align='center' class='A0101'>$ywOrderDTime</td>
				<td align='center' class='A0101'>$StockId</td>
				<td align='right' class='A0101'>$OrderQty</td>
				<td align='right' class='A0101'>$StockQty</td>
				<td align='right' class='A0101'>$AddQty</td>
				<td align='right' class='A0101'>$FactualQty</td>
				<td align='right' class='A0101'>$Forshort</td>
				<td align='center' class='A0101'>$DeliveryWeek</td>
				<td align='center' class='A0101'>$updateStr</td>
				</tr>";
				$j++;
				}while ($myRow = mysql_fetch_array($myResult));
			}
		   echo"<table>";
        
	    }else{
	    
	      if($oValue==0){
		      $UpdateSql="Update $DataIn.ck9_stocksheet SET  oStockQty = 0 WHERE StuffId='$StuffId'";
		      $UpdateResult = mysql_query($UpdateSql);
	      }
	       
		   echo "<p><span style='color:#FF0000'>分析的订单库存为整数，不用更新</span></p>";    
	    }
   }
  else{
       echo "<p><span style='color:#FF0000'>未有ID为: $StuffId 的配件信息！请检查配件ID是否正确？</span></p>";
   } 
   break;
   
   case 2:
   
       $UpdateSql="Update $DataIn.cg1_stocksheet SET  StockQty = StockQty-$thisQty,FactualQty=FactualQty+$thisQty
        WHERE StockId='$StockId'";
       $UpdateResult = mysql_query($UpdateSql);
       if($UpdateResult){
	        
            $OperationResult="Y";
	      }
	   else{
	   
	       $OperationResult="N";
	     }
        echo $OperationResult;
   break;
}


?>

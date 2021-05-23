<?php   
/*
在系统计划任务中增加任务列表运行
功能：每5分钟运行一次
*/
header("Content-Type: text/html; charset=utf-8");
header("cache-control:no-cache,must-revalidate");
include "d:/website/mc/basic/parameter.inc";

/*生成可备料/已备料订单时间表*/
$DateTime=date("Y-m-d H:i:s");
$ProductArray=array();

  $mySql="SELECT S.POrderId,S.ProductId,S.Qty,IFNULL(E.Type,0) AS Type   
FROM $DataIn.yw1_ordersheet S 
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId AND E.Type=2  
WHERE 1 and S.scFrom>0 AND S.Estate=1  GROUP BY S.POrderId ORDER BY M.OrderDate,S.ProductId,S.Id";

$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_assoc($myResult))
{
   do{
          $POrderId=$myRow["POrderId"];
          $ProductId=$myRow["ProductId"];
        
         //检查订单备料情况
         $CheckblState=mysql_fetch_array(mysql_query("
						SELECT SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1, SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2, SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty,L.llEstate,SUM(IF(GL.Id>0,1,0)) AS Locks 
						FROM $DataIn.cg1_stocksheet G 
						LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
						LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId 
						LEFT JOIN $DataIn.cg1_lockstock GL ON G.StockId=GL.StockId  AND GL.Locks=0 
						LEFT JOIN ( 
						    SELECT L.StockId,SUM(L.Qty) AS Qty,SUM(IF(L.Estate=1,1,0)) AS llEstate 
						    FROM  $DataIn.cg1_stocksheet G 
						    LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
						    WHERE  G.POrderId='$POrderId'  GROUP BY L.StockId 
						  )L ON L.StockId=G.StockId 
						WHERE G.POrderId='$POrderId' AND ST.mainType<2 
						AND NOT EXISTS(SELECT T.StuffId FROM $DataIn.stuffproperty T WHERE T.StuffId=G.StuffId AND T.Property='8')",$link_id));
						
		  $blQty=$CheckblState["blQty"];
          $llQty=$CheckblState["llQty"];
          $K1=$CheckblState["K1"];
          $K2=$CheckblState["K2"];
          $llEstate=$CheckblState["llEstate"];     
          $Locks=$CheckblState["Locks"]; 
         
           if (in_array($ProductId, $ProductArray)) {
	               if ($K1>=$K2 && $blQty!=$llQty){
                            $Locks=1;
                    }
          }
          else{
	            $Locks=$myRow["Type"]==2?1:$Locks;
           }
           
           $ProductArray[]=$ProductId; 
           /*
            if ($Locks==0 && $K1>=$K2 && $blQty!=$llQty){
                $ProductArray[]=$ProductId; 
            }
          */
          $ck_ableResult=mysql_query("SELECT * FROM $DataIn.ck_bldatetime WHERE POrderId='$POrderId'",$link_id);
          if($ck_albeRow=mysql_fetch_array($ck_ableResult)){
                 $Estate=$ck_albeRow["Estate"]; 
                 if ($Locks==0 && $K1>=$K2){
                       $setSTR=$Estate==2?",ableDate='$DateTime' ":"";
                       if ($blQty==$llQty && $Estate>0 && $llEstate==0){
	                       $setSTR.=",Estate=0";
                       }
                       else{
                           if (($blQty!=$llQty && $Estate!=1) || $llEstate>0) $setSTR.=",Estate=1"; 
                       }
	                   if ($setSTR!=""){
	                     $UpdateSql="UPDATE  $DataIn.ck_bldatetime  SET  unableDate='0000-00-00 00:00:00' $setSTR   WHERE POrderId='$POrderId'";
			             $UpdateResult=mysql_query($UpdateSql); 
			             }
                 }
                 else{//不可备料 
                             if ($K1<$K2 || $blQty!=$llQty){
	                                 $setSTR=",ableDate='0000-00-00 00:00:00',unableDate='$DateTime'";
                             }
                            if ($setSTR!="" || $Estate!=2){
			                  $UpdateSql="UPDATE  $DataIn.ck_bldatetime  SET  Estate=2 $setSTR WHERE POrderId='$POrderId'";
		                      $UpdateResult=mysql_query($UpdateSql); 
		                   }
                 }
          }
	      else{
	          if ($K1>=$K2){
		           $Estate=$blQty==$llQty?"0":"1";
			       $In_bldateSql="INSERT INTO $DataIn.ck_bldatetime(Id, POrderId, ableDate, unableDate,Estate, Locks) VALUES(NULL,'$POrderId','$DateTime','0000-00-00 00:00:00',$Estate,0)";
	               $In_Result=mysql_query($In_bldateSql);
	            }
	      }    
       }while($myRow = mysql_fetch_assoc($myResult));
}   
                                           
$base_dir=dirname(__FILE__);                       
$OutputInfo=$DateTime . "备料数据统计已自动生成！\r\n";
$fp = fopen($base_dir . "/subtask/system_blautorun.log", "a");
fwrite($fp, $OutputInfo);
fclose($fp);
?>
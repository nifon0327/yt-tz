<?
 $Log_Item="配件报废审核"; 
 $curDate=date("Y-m-d");
 $DateTime=date("Y-m-d H:i:s");
 $OperationResult="N";
 $Operator=$LoginNumber;
 
 $AuditStr="";
 /*
 switch($ActionId){
     
    case "PASS":
            $updateSql = "UPDATE $DataIn.ck8_bfsheet F LEFT JOIN $DataIn.ck9_stocksheet K ON F.StuffId=K.StuffId
			SET F.Estate='3',F.Locks='0',K.tStockQty=K.tStockQty-F.Qty,K.oStockQty=K.oStockQty-F.Qty
			WHERE F.Id='$Id' AND K.tStockQty>=F.Qty AND (K.oStockQty-K.mStockQty)>=F.Qty";
            $UpdateResult = mysql_query($updateSql,$link_id);
            if($UpdateResult)
            {
	                $Log="<div class=greenB>物料报废($Id)审核成功!</div><br>";
	                $OperationResult="Y";
                } 
            else{
                $Log="<div class=redB>物料报废($Id)审核失败! </div><br>$updateSql</br>";   
                }
              break;
   }
   */
?>
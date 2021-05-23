<?
     $Log_Item="配件库存更正"; 
     $curDate=date("Y-m-d");
	 $DateTime=date("Y-m-d H:i:s");
	 $OperationResult="N";
	 $Operator=$LoginNumber;
 
   switch($ActionId){
   case "CORRECT": {
			$Sql = "UPDATE $DataIn.ck9_stocksheet SET tStockQty='$tStockQty',oStockQty='$oStockQty',Date='$curDate' WHERE StuffId='$StuffId'";
			$UpdateResult = mysql_query($Sql);
            if($UpdateResult)
            {
				  $Log="配件库存更正($StuffId)成功!";
				  $OperationResult="Y";
            } else {
                $Log="配件库存更正($StuffId)失败!";   
            }
   }break;
   
   default: break;

	}
 
 $IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
$jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$infoSTR");
?>
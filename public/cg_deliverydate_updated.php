<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cg1_stockmain
$DataIn.cg1_stocksheet
$DataIn.trade_object
*/ 
include "../basic/parameter.inc";
//步骤2：
$Log_Item="更新采购单交货日期";			//需处理
$funFrom="cg_cgdmain";
$_SESSION["nowWebPage"]=$nowWebPage;
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$DateTemp=date("Y");          
      
//按设置的交货周期更新交货日期
$CheckSql=mysql_query("SELECT M.Date,G.Id,S.jhDays,T.jhDays AS TypeJhDays  
            FROM $DataIn.cg1_stocksheet G 
            LEFT JOIN $DataIn.cg1_stockmain M ON G.Mid=M.Id 
			LEFT JOIN $DataIn.stuffdata S ON S.StuffId=G.StuffId 
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId 
			WHERE G.rkSign>0 AND  (G.FactualQty>0 OR G.AddQty>0) AND G.Mid>0  AND (G.AddQty+G.FactualQty)>(SELECT IFNULL(SUM(C.Qty),0) AS Qty  FROM $DataIn.ck1_rksheet C WHERE 1 AND C.StockId=G.StockId) ",$link_id);
while($CheckRow = mysql_fetch_array($CheckSql)){
       $sId=$CheckRow["Id"];
       $Date=$CheckRow["Date"];
       $jhDays=$CheckRow["jhDays"]==0?$CheckRow["TypeJhDays"]:$CheckRow["jhDays"];
       
       $DeliveryDate=date("Y-m-d",strtotime("$Date  +$jhDays  day"));
       $DeliveryDateSql = "UPDATE $DataIn.cg1_stocksheet SET DeliveryDate=' $DeliveryDate' WHERE Id='$sId' AND Estate='0'";
       $DeliveryDateResult = mysql_query($DeliveryDateSql);
       if ($DeliveryDateResult  && mysql_affected_rows()>0){
	       echo "需求单明细 ($sId) 更新采购交期 $DeliveryDate 成功!<br>";
       }
}
               
//步骤4：
//$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
//$IN_res=@mysql_query($IN_recode);
//include "../model/logpage.php";
?>

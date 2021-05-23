<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw1_fkoutsheet 
*/
include "../model/modelhead.php";
//步骤2：
$Log_Item="需求单";		//需处理
$Log_Funtion="请款";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$Log_Funtion="采购请款";
$i=1;
//将记录复制到请款明细表(客户退款类配件只按订单数计算)
$cgStockResult=mysql_query("SELECT G.StockId,G.POrderId,G.StuffId,G.Price,G.OrderQty,
G.StockQty,G.AddQty,G.FactualQty,G.CompanyId,G.BuyerId,S.TypeId
FROM $DataIn.cg1_stocksheet G 
LEFT JOIN $DataIn.cg1_stockmain M ON G.Mid=M.Id 
LEFT JOIN $DataIn.stuffdata S ON S.StuffId=G.StuffId 
WHERE M.CompanyId='2270' AND M.Date>='2013-03-01' AND NOT EXISTS( SELECT W.StockId From $DataIn.cw1_fkoutsheet W WHERE  W.StockId=G.StockId)",$link_id);
if($cgStockRow=mysql_fetch_array($cgStockResult)){
 do{
    $Log="";
     $StockId=$cgStockRow["StockId"];
     $OrderQty=$cgStockRow["OrderQty"];
	$StockQty=$cgStockRow["StockQty"];
	$AddQty=$cgStockRow["AddQty"];
	$FactualQty=$cgStockRow["FactualQty"];
	
	$cgQty=$AddQty+$FactualQty;
     //收货情况				
	$rkTemp=mysql_query("SELECT SUM(S.Qty) AS Qty,Max(M.Date) AS rkDate FROM $DataIn.ck1_rksheet S 
	LEFT JOIN  $DataIn.ck1_rkmain M ON M.Id=S.Mid  
	 WHERE S.StockId='$StockId' ",$link_id);
	$rkQty=mysql_result($rkTemp,0,"Qty");
	$rkDate=mysql_result($rkTemp,0,"rkDate");
	$rkQty=$rkQty==""?0:$rkQty;
			
   if ($cgQty<>$rkQty) continue;
   $Month=substr($rkDate, 0, 7);
	$POrderId=$cgStockRow["POrderId"];
	$StuffId=$cgStockRow["StuffId"];
	$Price=$cgStockRow["Price"];
	$CompanyId=$cgStockRow["CompanyId"];
	$BuyerId=$cgStockRow["BuyerId"];
	$TypeId=$cgStockRow["TypeId"];
	if($TypeId=='9104'){//配件为客户退款的，金额为订单数*单价。否则为实际购买数*单价
	    $Qty=$OrderQty;
		$Amount=$Qty*$Price;
		}
	else{
	     $Qty=$FactualQty+$AddQty;
		 $Amount=$Qty*$Price;
	     }
    $inRecode="INSERT INTO $DataIn.cw1_fkoutsheet(Id, Mid, StockId, POrderId, StuffId, Qty, Price, OrderQty, StockQty, AddQty, FactualQty, CompanyId, BuyerId, Amount, Month, Estate, Locks)VALUES(NULL,'0','$StockId','$POrderId','$StuffId','$Qty','$Price','$OrderQty','$StockQty','$AddQty','$FactualQty','$CompanyId','$BuyerId','$Amount','$Month','2','1')";
	/*
    $inAction=@mysql_query($inRecode);
      if($inAction){ 
           $Log.="&nbsp;&nbsp;Id号在(".$Ids.")的".$TitleSTR."成功!<br>";
          } 
      else{ 
          $Log.="<div class=redB>&nbsp;&nbsp;Id号在(".$Ids.")的".$TitleSTR."失败!</div><br>";
          $OperationResult="N";
		  }
	echo $Log;
	*/
   echo "$i --" . $inRecode . "<br>";
	$i++;
   }while($cgStockRow=mysql_fetch_array($cgStockResult));
}
//返回参数
/*
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
*/
?>
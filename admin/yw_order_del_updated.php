<?php   
//电信-EWEN
/*
更新:加入清除生产记录动作 2010.12.08
*/
$MyPDOEnabled=1;  //启用PDO连接数据库
include "../model/modelhead.php";

$fromWebPage=$funFrom."_verify";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="产品订单";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$SaveOperationLog=true;

ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;
$Id=$checkid[0];
switch ($ActionId){
    case 17:
        foreach ($checkid as $Id){
            // include "subprogram/yw_order_del.php";
            $sql = "SELECT POrderId FROM $DataIn.yw1_orderdeleted  WHERE Id='".$Id."' ";
            $myResult = $myPDO->query($sql);
            if ($myRow = $myResult->fetch()){
               $POrderId=$myRow["POrderId"];

                //删除订单前,将数据copy到删除表,以便查询
                $saveSql = "INSERT INTO cg1_stocksheet_del  SELECT Id,Mid,Level,StockId, POrderId,StuffId,Relation,Price,CostPrice, OrderQty,StockQty,AddQty,FactualQty,CompanyId,BuyerId,DeliveryDate,DeliveryWeek,StockRemark,AddRemark,cgSign,scSign,rkSign, blSign,llSign,Estate,Locks,ywOrderDTime,PLocks,creator,created, modifier,modified,Date,Operator FROM cg1_stocksheet WHERE POrderId='$POrderId'";
                //echo $saveSql."<br/>";
                $saveResult = $myPDO->exec($saveSql);
                $saveResult = null;

                $saveSql = "INSERT INTO cg1_semifinished_del SELECT  Id,sId,POrderId,mStockId,mStuffId,StockId,StuffId,Relation,OrderQty, OldQty,Date,Operator,Estate,Locks,PLocks,creator,created,modifier,modified FROM cg1_semifinished WHERE POrderId='$POrderId'";
                $saveResult = $myPDO->exec($saveSql);
                $saveResult = null;


                $delResult=$myPDO->query("CALL proc_yw1_ordersheet_delete($POrderId,$Operator);");

                while($operRow = $delResult->fetch(PDO::FETCH_ASSOC)) {
                    $OperationResult = $operRow['OperationResult'];
                    $Log .= $operRow['OperationLog'];
                }
                $SaveOperationLog=false;
            }
            else{
                $Log.="<div class='redB'>删除订单退回失败!<div>";
                $OperationResult="N";
            }
            $myResult->closeCursor();
        }
	   break;
	case 34:
	   $sql="DELETE FROM $DataIn.yw1_orderdeleted WHERE Id='$Id'";
	   $res = $myPDO->exec($sql);

	   if($res>0){
	         $Log.="删除订单退回成功!";
	         }
		else{
		      $Log.="<div class='redB'>删除订单退回失败!<div>";
			  $OperationResult="N";
		    }
		
	   break;
}

$ALType="From=$From&CompanyId=$CompanyId";

include "../model/logpage.php";
?>
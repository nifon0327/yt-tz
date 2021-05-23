<?php 
/*
更新:加入清除生产记录动作 电信-yang 20120801
*/
include "../model/modelhead.php";
$funFrom='del_llqty';
$fromWebPage=$funFrom."_manual";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="	订单领料记录";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
if($POrderId!="" && strlen($POrderId)==12){
		//读取订单信息
		$sheetResult = mysql_query("SELECT * FROM $DataIn.yw1_ordersheet WHERE POrderId='$POrderId' ORDER BY Id DESC",$link_id);
		if($sheetRow = mysql_fetch_array($sheetResult)){
             $Log.="<div class='redB'>$x - &nbsp;&nbsp;订单流水号为 $POrderId 的 订单已存在,不能删除.</div><br>";
			 $OperationResult="N";  	
		}
		else{
			 $llResult = mysql_query("SELECT StockId FROM $DataIn.ck5_llsheet WHERE  StockId like '$POrderId%'",$link_id);
			 if($llRow = mysql_fetch_array($llResult)){
				 do{
					$StockId=$llRow["StockId"];
			      	include "../admin/subprogram/del_model_llqty.php";	 
				 }while($llRow = mysql_fetch_array($llResult));
			 }else{
				 $Log.="<div class='redB'>$x - &nbsp;&nbsp;订单流水号为 $POrderId 的 订单未有领料记录.</div><br>";
			      $OperationResult="N";  
			 }
			 
		}
	
}else{
	 $Log.="<div class='redB'>？？？订单流水号:$POrderId </div><br>";
}

//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.ck5_llsheet");

//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
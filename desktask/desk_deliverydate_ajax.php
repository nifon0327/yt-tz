<?php   
//电信-zxq 2012-08-01
include "../basic/chksession.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
include "../basic/parameter.inc";
//步骤2：
$Log_Item="采购单交期";			//需处理
$Log_Funtion="备注信息";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
switch($ActionId){
	 case "AddRemark"://添加备注信息
	  /*
	  //检查是否存在
     $checkResult = mysql_query("SELECT Id FROM $DataIn.cg_remark WHERE StockId='$StockId'  LIMIT 1",$link_id);
	 if($checkRow = mysql_fetch_array($checkResult)){
		 //更新生产日期
	    $UpdateSql="Update $DataIn.cg_remark SET Remark='$Remark' WHERE StockId='$StockId'";
        $UpdateResult = mysql_query($UpdateSql);
        if($UpdateResult){
	          $Log="<div class=greenB>采购单:" . $StockId . "备注更新成功!</div><br>";
	         } 
         else{
  	           $Log="<div class=redB>采购单:" . $StockId . "备注更新失败!</div><br>";
	           $OperationResult="N";
	          }
	     }
	 else{
	 */
		//新增备注信息
	   $inRecode="INSERT INTO $DataIn.cg_remark(Id,StockId,Remark,Date,Operator) VALUES (NULL,'$StockId','$Remark','$Date','$Operator')";
		$inResult=@mysql_query($inRecode);
		if($inResult){
			$Log.="&nbsp;&nbsp采购单:" . $StockId . "备注添加成功.</br>";
			}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp;采购单:" . $StockId . "备注添加失败.$inRecode </div></br>";
			$OperationResult="N";
			}
	  // }

		break;
}
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
echo $OperationResult;
?>

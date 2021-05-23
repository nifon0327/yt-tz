<?php 
//步骤1：

$Log_Item="配件资料";			
$Log_Funtion="删除";


$DateTime=date("Y-m-d H:i:s");
$Operator=$LoginNumber;
$OperationResult="N";
$stuffId = $info[0];

$Log = "";
if ($stuffId>0) {
	
	 
            //get PandS related
			 $pandscount = 0;
            $sqlLim2 = mysql_query("SELECT COUNT(*) AS PandSCount FROM $DataIn.pands  WHERE StuffId='$stuffId' ");
            if ($rowLim2 = mysql_fetch_assoc($sqlLim2)) {
					$pandscount = $rowLim2["PandSCount"];
			}
			
			if ($pandscount <= 0) {
				mysql_query("delete from $DataIn.bps where StuffId=$stuffId");
				mysql_query("delete from $DataIn.cut_die where StuffId=$stuffId");
				 mysql_query("delete from $DataIn.stuffproperty where StuffId=$stuffId");
				  mysql_query("delete from $DataIn.ck9_stocksheet where StuffId=$stuffId");
				   mysql_query("delete from $DataIn.stuffdevelop where StuffId=$stuffId");
				 
				$sql = "delete from $DataIn.stuffdata where StuffId=$stuffId";
				$in_sql = @mysql_query($sql);
				if ($in_sql) {
					$Log = "配件 $stuffId 删除成功";
					$OperationResult="Y";
				}
			}
			
			
			

else {
	$Log = "配件 $stuffId 删除失败";
}
}
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

$jsonArray = array("Result"=>"$OperationResult");
?>

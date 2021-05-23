<?php 
//删除订单为重新上传标准图的标记$DataIn.电信---yang 20120801

$delsql = "delete  A from $DataIn.yw2_orderteststandard A
		LEFT JOIN  $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId
		LEFT JOIN  $DataIn.Productdata B ON  B.ProductId=S.ProductId
		WHERE B.ProductId=$ProductId AND A.Type=9";
//echo "$delsql";		
$delresult = mysql_query($delsql);
if($delresult){
	$Log.="删除产品Id号 $ProductId 重新上传图档标志的记录成功 $Log_Funtion.</br>";
	}
else{
	$Log.="删除产品Id号 $ProductId 重新上传图档标志失败! $delsql</br>";
	$OperationResult="N";
	}

?>
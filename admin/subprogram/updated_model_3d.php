<?php   
//多记录操作 二合一已更新
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}
$sql = "UPDATE $upDataSheet SET $SetStr WHERE Id IN ($Ids) $EstateStr";
//echo $sql;
$result = mysql_query($sql);
if($result){
	$Log="ID号在 $Ids 的记录成功 $Log_Funtion. $sql</br>";
	}
else{
	$Log="ID号为 $Ids 的记录$Log_Funtion 失败! $sql</br>";
	$OperationResult="N";
	}
?>
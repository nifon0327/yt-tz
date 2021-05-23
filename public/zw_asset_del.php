<?php 
//电信-joseph
include "../model/modelhead.php";
$fromWebPage="zw_asset";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="物品资料";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=0;
$delIds="";
for($i=1;$i<=$IdCount;$i++){
	$IdTemp=$checkid[$i];
	if ($IdTemp!=""){
		$delIds=$delIds==""?$IdTemp:($delIds.",".$IdTemp);
		}
	}
$delSql = "DELETE $DataIn.zw1_assetrecord,$DataIn.zw1_assetuse FROM $DataIn.zw1_assetrecord  LEFT JOIN $DataIn.zw1_assetuse ON $DataIn.zw1_assetrecord.Id=$DataIn.zw1_assetuse.AssetId WHERE $DataIn.zw1_assetrecord.Id IN ($delIds)"; 
$delRresult = mysql_query($delSql);
if($delRresult && mysql_affected_rows()>0){
	$Log="&nbsp;&nbsp;ID号在( $delIds ) 的物品资料删除成功。 <br>";
	}
else{
	$Log="<div class='redB'>&nbsp;&nbsp;ID号在( $delIds ) 的物品资料删除失败。$delSql </div><br>";
	$OperationResult="N";
	}

//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.zw1_assetrecord,$DataIn.zw1_assetuse");
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
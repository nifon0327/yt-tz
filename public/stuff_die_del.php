<?php   
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="配件模具关系";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$y=0;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$PIdTemp=$checkid[$i];
	if ($PIdTemp!=""){
		$PIds=$PIds==""?$PIdTemp:($PIds.",".$PIdTemp);
		}
	}

$DelSql = "DELETE FROM $DataIn.cut_die WHERE StuffId IN ($PIds)"; 
$DelResult = mysql_query($DelSql);
if($DelResult){
	$Log.="配件ID为 $PIds 的模具关系解除成功<br>";
	}
else{
	$Log.="<div class=redB>配件ID为 $PIds 的模具关系解除失败 $DelSql</div><br>";	
	$OperationResult="N";
	}
//操作日志
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&StuffType=$StuffType&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
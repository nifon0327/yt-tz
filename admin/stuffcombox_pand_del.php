<?php
include "../model/modelhead.php";
ChangeWtitle("$SubCompany  子母配件BOM删除");
$fromWebPage="stuffcombox_pand_read";
$nowWebPage="stuffcombox_pand_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Log_Item=" 字母配件BOM";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$PIdTemp=$checkid[$i];
	if ($PIdTemp!=""){
		$PIds=$PIds==""?$PIdTemp:($PIds.",".$PIdTemp);
		}
}
$DelSql = "DELETE FROM $DataIn.stuffcombox_bom WHERE mStuffId IN ($PIds)"; 
$DelResult = mysql_query($DelSql);
if($DelResult){
	$Log.="母配件ID：$PIds 的BOM关系解除成功<br>";
	}
else{
	$Log.="<div class=redB>母配件ID：$PIds 的BOM关系解除失败 $DelSql</div><br>";	
	$OperationResult="N";
	}
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
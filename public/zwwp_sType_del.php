<?php 
//电信-EWEN 2012-11-23
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="总务用品分类";//需处理
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
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);$y++;
		}
	}
$DelSql = "DELETE A FROM $DataPublic.zwwp2_subtype A LEFT JOIN  $DataPublic.zwwp3_data B ON B.TypeId=A.Id WHERE A.Id IN ($Ids) AND B.TypeId IS NULL";
$DelResult = mysql_query($DelSql);
if($DelResult && mysql_affected_rows()>0){
	$Log.="ID号在 $Ids 的 $Log_Item 删除操作成功(如果记录仍在，则类别已使用不能删除).<br>";
	}
else{
	$Log.="<div class='redB'>ID号在 $Ids 的 $Log_Item 删除操作失败,分类已使用!</div><br>";
	$OperationResult="N";
	}
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataPublic.zwwp2_subtype");
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
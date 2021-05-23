<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-17
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Log_Item="员工津贴扣款";
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		$y++;
		}
	}
$Del = "DELETE A FROM $DataIn.staff_lateearly A LEFT JOIN $DataIn.cwxzsheet B ON A.Month=B.Month AND A.Number=B.Number WHERE A.Id IN($Ids) AND B.Month IS NULL";   //如果生成工资则不能删除!
//echo "$Del";
$Del_result = mysql_query($Del);
if ($Del_result){
	$Log.="ID号在 $Ids 的 $Log_Item 删除操作成功(生成工资则不能删除). $Del<br>";
	}
else{
	$Log.="<div class='redB'>ID号在 $Ids 的 $Log_Item 删除操作失败(已生成工资？). $Del </div><br>";
	$OperationResult="N";
	}
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.StuffType");
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

<?php 
//电信-zxq 2012-08-01
/*
$DataIn.zw3_purchases二合一已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="每月报关金额";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=0;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		$x++;
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)	
	
//删除数据库记录
$DelSql = "DELETE FROM $DataIn.ch11_shipamount WHERE Id IN ($Ids) "; //未请款且未购回状态
$DelResult = mysql_query($DelSql);
if($DelResult ){
	$Log="&nbsp;&nbsp; ID号在( $Ids )的 $Log_Item 删除操作成功.<br>";
	}
else{
	$OperationResult="N";
	$Log="<div class='redB'>ID号在( $Ids )的 $Log_Item 删除操作失败. $DelSql </div><br>";
	}//end if ($Del_result)
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.zw3_purchases");
$Page=$IdCount==$x?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

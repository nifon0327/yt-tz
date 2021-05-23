<?php 
//电信-ZX  2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="社保参数设定";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:$Ids.",".$Id;
		}
	}
	
//删除数据库记录
$delSql = "DELETE A FROM $DataIn.rs_sbtype A 
LEFT JOIN (
		   SELECT Type FROM $DataIn.sbdata GROUP BY Type 
		   ) B ON B.Type=A.Id 
WHERE Id IN ($Ids) AND B.Type IS NULL"; 
$delRresult = mysql_query($delSql);
if($delRresult && mysql_affected_rows()>0){
	$Log="&nbsp;&nbsp; $x - ID在( $Ids )的 $TitleSTR 成功.<br>";
	}
else{
	$OperationResult="N";
	$Log.="<div class='redB'>$x - &nbsp;&nbsp;ID在( $Ids )的 $TitleSTR 失败.</div><br>";
	}
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataPublic.rs_sbtype");
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
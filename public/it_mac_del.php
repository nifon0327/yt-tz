<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="上网设备MAC地址资料";//需处理
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
//在两边员工资料、薪资资料、考勤资料、社保资料不存在记录方可删除
$DelSql = "DELETE   FROM $DataPublic.it_mac  WHERE Id IN ($Ids) "; 
$Del_result = mysql_query($DelSql);
if($Del_result && mysql_affected_rows()>0){
	$Log="ID号在 $Ids 的 $Log_Item 删除操作成功";
	}
else{
	$Log="<div class='redB'>ID号在 $Ids 的 $Log_Item 删除失败 $DelSql</div><br>";
	$OperationResult="N";
	}
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataPublic.jobdata");
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
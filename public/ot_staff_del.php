<?php 
//电信-joseph
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="外部人员资料";//需处理
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
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}
//同时删除外部人员资料、用户数据、权限数据、在线记录
$DelSql = "DELETE A,B,C,D 
	FROM $DataIn.ot_staff A
	LEFT JOIN $DataIn.usertable B ON B.Number=A.Number
	LEFT JOIN $DataIn.upopedom C ON C.UserId=B.Id
	LEFT JOIN $DataIn.online D ON D.uId=B.Id
	WHERE A.Id IN ($Ids)"; 
$DelResult= mysql_query($DelSql);
if($DelResult && mysql_affected_rows()>0){
	$Log.="ID号在 $Ids 的 $Log_Item 删除操作成功.<br>";
	}
else{
	$OperationResult="N";
	$Log.="<div class='redB'>ID号在 $Ids 的 $Log_Item 删除操作失败. $DelSql</div><br>";
	}
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.online,$DataIn.ot_staff,$DataIn.usertable,$DataIn.upopedom");
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
<?php 
//电信-EWEN
//代码、数据共享-EWEN 2012-08-15
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="开发费用分类";//需处理
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

//删除数据库记录
$DelSql = "DELETE A FROM $DataPublic.kftypedata A 
LEFT JOIN (
		   SELECT TypeID FROM $DataIn.cwdyfsheet GROUP BY TypeID
		   ) B ON B.TypeID=A.Id
WHERE A.Id IN ($Ids) ANDB.TypeID IS NULL"; 
$DelResult = mysql_query($DelSql);
if ($DelResult){
	$Log.="ID号在 $Ids 的 $Log_Item 删除操作成功(如果记录仍在，则已有使用不能删除).<br>";
	}
else{
	$OperationResult="N";
	$Log.="<div class='redB'>ID号在 $Ids 的 $Log_Item 删除操作失败. $DelSql </div><br>";
	}
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

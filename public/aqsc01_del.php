<?php 
//2013-10-14 ewen
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="安全管理制度汇编记录";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$y=0;
for($i=0;$i<count($checkid);$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);$y++;
		}
	}
//其下无下级分类和文档资料方可删除
$delSql = "DELETE A FROM $DataPublic.aqsc01 A 
LEFT JOIN $DataPublic.aqsc02 B ON B.TypeId=A.Id 
WHERE A.Id='$Id' AND B.TypeId IS NULL"; 
$delRresult = mysql_query($delSql);
if ($delRresult && mysql_affected_rows()>0){
	$Log.="ID号在(".$Ids.")的".$TitleSTR."成功.<br>";
	}
else{
	$Log.="<div class='redB'>ID号在(".$Ids.")的".$TitleSTR."失败.</div>DELETE A FROM $DataPublic.aqsc01 A 
LEFT JOIN $DataPublic.aqsc02 B ON B.TypeId=A.Id 
LEFT JOIN $DataPublic.aqsc01 C ON C.Id=A.PreItem
WHERE A.Id='$Id' AND B.TypeId IS NULL AND C.Id IS NULL<br>";
	$OperationResult="N";
	}//end if ($Del_result)
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
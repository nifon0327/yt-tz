<?php 
//ewen 2013-03-18 OK
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非BOM采购预付订金";//需处理
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
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)	

//删除数据库记录
$delSql = "DELETE FROM $DataIn.nonbom11_djsheet WHERE Id IN ($Ids) AND (Estate='1' OR Estate='4') AND Mid='0' AND Did='0'"; 
$delRresult = mysql_query($delSql);
if ($delRresult && mysql_affected_rows()>0){
	$Log.="&nbsp;&nbsp; ID号在($Ids) 的 $TitleSTR 成功.<br>";
	}
else{
	$OperationResult="N";
	$Log.="<div class='redB'>&nbsp;&nbsp;ID号在($Ids) 的 $TitleSTR 失败. $delSql </div><br>";
	}//end if ($Del_result)

$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
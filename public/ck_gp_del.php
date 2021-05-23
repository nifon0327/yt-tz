<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="供应商备品";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=0;
//$sql=" LOCK TABLES $DataIn.ck11_bpmain M WRITE,$DataIn.ck11_bpsheet S WRITE";$res=@mysql_query($sql);
$Lens=count($checkid);
$Ids="";
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:$Ids.",".$Id;
		}//end if($Id!="")	
	}//end for($i=1;$i<$IdCount;$i++)		
$upSql="UPDATE $DataIn.ck11_bpsheet S 
		SET S.StuffId='0' WHERE S.Id IN ($Ids) ";
$upResult = mysql_query($upSql);		
if($upResult ){
	//删除此补仓记录
	$delSql = "DELETE  FROM  $DataIn.ck11_bpsheet   WHERE StuffId=0"; 
	//echo "$delSql";
	$delRresult = mysql_query($delSql);
	//删除全部没有明细的主单
	$delMainSql = "DELETE $DataIn.ck11_bpmain  FROM $DataIn.ck11_bpmain 
		LEFT JOIN $DataIn.ck11_bpsheet  ON $DataIn.ck11_bpmain.Id=$DataIn.ck11_bpsheet.Mid
		WHERE $DataIn.ck11_bpsheet.Id IS NULL"; 
	$delMianRresult = mysql_query($delMainSql);
	//echo "$delMainSql";
	$Log.="&nbsp;&nbsp;记录 $Ids 供应商备品资料删除操作成功!<br>";
	}
else{//不能删除，原因有补仓记录
	$Log.="<div class='redB'>&nbsp;&nbsp;记录 $Ids 供应商备品资料删除操作失败!</div><br>";
	$OperationResult="N";
	}

//$sql="UNLOCK TABLES";$res=@mysql_query($sql);
//操作日志
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.ck11_bpmain");
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.ck11_bpsheet");
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
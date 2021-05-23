<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="采购单扣款";//需处理
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
	    $UpdateSql="UPDATE $DataIn.cw15_gyskkmain M
			          LEFT JOIN $DataIn.cw15_gyskksheet S ON S.Mid=M.Id
			          SET M.TotalAmount=M.TotalAmount-S.Amount
					  WHERE S.Id='$Id'";
	    $UpdateResult=mysql_query($UpdateSql);
		$DelSql="DELETE FROM $DataIn.cw15_gyskksheet WHERE Id='$Id'";
		$DelResult=mysql_query($DelSql);
		if($DelResult && mysql_affected_rows()>0){
		    $Log.="&nbsp;&nbsp;$x ----采购单扣款记录删除成功!<br>";
			$OperationResult="N";
		    }
		$x++;
		}//end if($Id!="")	
	}//end for($i=1;$i<$IdCount;$i++)

/*
if ($OperationResult=="Y"){
	$DelSql="DELETE FROM $DataIn.cw15_gyskkmain M WHERE not exists (SELECT  S.Mid FROM $DataIn.cw15_gyskksheet S WHERE S.Mid=M.Id)";
    $DelResult=mysql_query($DelSql);
}	
*/	
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
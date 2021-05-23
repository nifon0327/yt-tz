<?php   
//电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
$Log_Item="工序分类资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
$Log_Funtion="新增记录";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");

$maxSql = mysql_query("SELECT MAX(gxTypeId) AS Mid FROM $DataIn.process_type",$link_id);
$gxTypeId=mysql_result($maxSql,0,"Mid");
if($gxTypeId){
	$gxTypeId=$gxTypeId+1;
	}
else{
	$gxTypeId=1;
     }

$Remark=FormatSTR($Remark);
$inRecode="INSERT INTO $DataIn.process_type (Id,gxTypeId, gxTypeName,SortId,Color,Remark,Estate,Locks,Date, Operator)values(NULL,'$gxTypeId','$gxTypeName','$SortId','$Color','$Remark','1','0','$Date','$Operator')";
        $inAction=@mysql_query($inRecode);
        if($inAction){ 
	        $Log="$TitleSTR 成功!<br>";
	        } 
        else{
	        $Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
	        $OperationResult="N";
	       } 

//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
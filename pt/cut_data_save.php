<?php 
/*
 * 鼠宝皮套专用 zhongxq-2012-08-17
 */
include "../model/modelhead.php";
//步骤2：
$Log_Item="刀模资料新增记录";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d H:i:s");
$FilePath="../download/cut_data/";

        $inRecode="INSERT INTO $DataIn.pt_cut_data (Id, CutName,CutSize, Picture, Date, cutSign,Operator,Estate,Locks)values(NULL,'$CutName','$CutSize','0','$Date','$cutSign','$Operator','0','0')";
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
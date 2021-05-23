<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
$Log_Item="摄像头新增记录";			//需处理
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
//步骤3：需处理
/*
if($cFrom=="dp"){
	         $fromDp="select max(C.Order) as dpOrder from $DataPublic.ot2_cam C where C.From='$cFrom'";
			 $dpResult=mysql_query($fromDp);
			 if($fromRow=mysql_fetch_array($dpResult)){
			 $Order=$fromRow["dpOrder"]+1;
             }
             }
	    else{
	         $fromMc="select max(C.Order) as mcOrder from $DataPublic.ot2_cam C where C.From='$cFrom'";
			 $mcResult=mysql_query($fromMc);
			 if($fromRow=mysql_fetch_array($mcResult)){
	         $Order=$fromRow["mcOrder"]+1;
			 }
			}	
*/	
 $fromMc="select max(C.Order) as mcOrder from $DataPublic.ot2_cam C where C.From='$cFrom'";
 $mcResult=mysql_query($fromMc);
if($fromRow=mysql_fetch_array($mcResult)){
     $Order=$fromRow["mcOrder"]+1;
}         
$inRecode="INSERT INTO $DataPublic.ot2_cam (Id, Floor, Info, Name, IP,OutIP,Port,Params, `Order`, `From`) VALUES (NULL,'$Floor','$Info','$Name','$IP','$OutIP','$Port','$Params','$Order','$cFrom')";
//echo $inRecode;
$inAction=@mysql_query($inRecode);
if ($inAction){ 
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
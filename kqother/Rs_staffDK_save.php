<?php 
include "../model/modelhead.php";
//步骤2：
$Log_Item="员工抵扣日期";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Date=date("Y-m-d");
$Month=date("Y-m");
if($_POST['ListId']){//如果指定了操作对象
	$Counts=count($_POST['ListId']);
	//$Ids="";


	for($i=0;$i<$Counts;$i++){
		 $thisId=$_POST[ListId][$i];
         $inRecode ="INSERT INTO $DataPublic.staff_dkdate(Id,  Number,Remark,dkDate,dkHour,RemainHour,Date,Estate,Locks,Operator)VALUES(NULL,'$thisId','$Remark','$dkDate','$dkHour','$dkHour','$Date',1,0,'$Operator')";
         $inResult=@mysql_query($inRecode);
         if($inResult){
		           $Log.="&nbsp;&nbsp; 员工号 $thisId 抵扣工时添加成功.</br>";
		         }
         else{
		          $Log.="<div class='redB'>&nbsp;&nbsp;&nbsp;&nbsp; 员工号 $thisId 抵扣工时添加 失败! $inRecode </div></br>";
		          $OperationResult="N";
		       }
		 }
 }

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

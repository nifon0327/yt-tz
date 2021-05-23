<?php 
include "../model/modelhead.php";
header("Content-Type: text/html; charset=gb2312");
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_imageload";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Log_Item="相关模具图档上传";	 //需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");

//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
/*case 161:
$Log_Funtion="模具连接";
$delSql=mysql_query("DELETE FROM $DataIn.cut_die WHERE ProductId='$ProductId'",$link_id);
if($_POST['ListId']){//如果指定了操作对象
$Counts=count($_POST['ListId']);
$X=1;
for($i=0;$i<$Counts;$i++){
$thisId=$_POST[ListId][$i];
       $inRecode="INSERT INTO $DataIn.cut_die(Id, ProductId,GoodsId)VALUES(NULL,$ProductId,$thisId)";
       $inResult=mysql_query($inRecode);
   if($inResult){
            $Log.="$X ----模具连接成功! </br>";
            }
           else{
            $Log.="<div class='redB'>$X ----模具连接失败! $inRecode</div></br>";
            $OperationResult="N";
           }
$X++;
  }
}
   break;*/
}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
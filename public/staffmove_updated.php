<?php 
/*
已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="员工调动资料";		//需处理
$upDataSheet="$DataPublic.staffmain";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	 case 50:
         $Log_Funtion="取消调动";
         $Lens=count($checkid);
         for($i=0;$i<$Lens;$i++){
	            $Id=$checkid[$i];
                $OResult=mysql_query("SELECT M.Number,M.OcSign,M.OBranchId,M.OJobId,M.OGroupId,M.Date FROM $DataPublic.staff_move M WHERE M.Id='$Id'",$link_id);
               if($ORow=mysql_fetch_array($OResult)){
                      $Number=$ORow["Number"];
                      $OcSign=$ORow["OcSign"];
                      $OBranchId=$ORow["OBranchId"];
                      $OJobId=$ORow["OJobId"];
                      $OGroupId=$ORow["OGroupId"];
                    $UpdateSql="UPDATE $DataPublic.staffmain SET cSign='$OcSign',BranchId='$OBranchId',JobId='$OJobId',GroupId='$OGroupId' WHERE Number='$Number'";
                   $UpdateResult=mysql_query($UpdateSql);
                          if( $UpdateResult && mysql_affected_rows()>0){
                                 $DelSql="DELETE FROM $DataPublic.staff_move WHERE  Id='$Id'";
                                 $DelResult=mysql_query($DelSql);
                                 $Log.="Number 为 $Number 的员工取消调动成功 </br>";
                                 }
                          else{
                                 $Log.="<div class='redB'>Number 为 $Number 的员工取消调动失败</div></br>";
                                 }
                         }
                   }
         break;
	}
/*$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);*/
include "../model/logpage.php";
?>
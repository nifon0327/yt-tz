<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非BOM配件领用记录转出";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
switch($ActionId){
        case "156"://确认
                  $Lens=count($checkid);
                  for($i=0;$i<$Lens;$i++){
                  	$Id=$checkid[$i];
                  	if ($Id!=""){
	                  	$Ids=$Ids==""?$Id:($Ids.",".$Id);$y++;
	                  	}
                  	}
                  $UpdateSql="UPDATE $DataIn.nonbom8_bf SET  Estate=0 WHERE Id IN ($Ids)";
                  $UpdateResult=@mysql_query($UpdateSql);
                   if($UpdateResult && mysql_affected_rows()>0){
                              $Log.="&nbsp;&nbsp;&nbsp;ID 为$Id 的记录确认接收成功!<br>"; 

                          $UpdateSql3="UPDATE  $DataIn.nonbom7_code  C   
                           LEFT JOIN  $DataIn.nonbom8_turnfixed  B  ON B.BarCode=C.BarCode
                           LEFT JOIN $DataIn.nonbom8_turn  R  ON R.Id=B.TurnId
                            SET C.Estate=2,  C.Number=R.InNumber  WHERE R.Id IN ($Ids)"; 
                            $UpdateResult3=@mysql_query($UpdateSql3);
                           if($UpdateResult3){
                                         $Log.="固定资产最新领用人更新成功!<br>"; 
                                  }
                         else{
                                        $Log.="<div class='redB'>固定资产最新领用人更新失败! $UpdateSql3</div><br>"; 
                                     }
                       }
               else{
                           $Log.="&nbsp;&nbsp;&nbsp;<div class='redB'>ID 为$Id 的确认接收失败! $UpdateSql</div><br>"; 
                           $OperationResult="N";
                   }
           break;
       default://转出更新
                  $UpdateSql="UPDATE $DataIn.nonbom8_turn SET  Qty=$turnQty,InNumber='$InNumber',Remark='$Remark'  WHERE Id=$Id";
                  $UpdateResult=@mysql_query($UpdateSql);
                   if($UpdateResult && mysql_affected_rows()>0){
                        $Log.="配件ID$GoodsId 转出数量  $thisthQty 给接收人 $InName 更新成功!<br>";           
                       }
               else{
                           $Log.="<div class='redB'>配件ID$GoodsId 转出数量  $thisthQty 给接收人 $InName 更新失败! $UpdateSql</div><br>"; 
                           $OperationResult="N";
                   }
                         $DelSql="DELETE  FROM $DataIn.nonbom8_turnfixed WHERE TurnId=$Id";  $DelResult=@mysql_query($DelSql);           
                         $turnCount=count($turnCode);
                         for($k=0;$k<$turnCount;$k++){
                                  $BarCode=$turnCode[$k];
                                  $IN_Sql1="INSERT INTO $DataIn.nonbom8_turnfixed(Id,TurnId,GoodsId,BarCode)VALUES(NULL,'$Id','$GoodsId','$BarCode')";     
                                  $IN_recode1=@mysql_query($IN_Sql1);
                                  if($IN_recode1){
                                          $Log.="&nbsp;&nbsp;固定条码为 $BarCode 的配件转出成功!<br>"; 
                                         }
                                 else $Log.="<div class=redB>&nbsp;&nbsp;固定条码为 $BarCode 的配件转出失败!$IN_Sql1<div class=redB><br>"; 
                           }
          break;
}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
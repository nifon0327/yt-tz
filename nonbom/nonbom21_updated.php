<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非BOM配件领用记录退回仓库";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
switch($ActionId){
        case "17":
                  $Lens=count($checkid);
                  for($i=0;$i<$Lens;$i++){
                  	$Id=$checkid[$i];
                  	if ($Id!=""){
	                  	$Ids=$Ids==""?$Id:($Ids.",".$Id);$y++;
	                  	}
                  	}
                  $UpdateSql="UPDATE $DataIn.nonbom8_reback SET  Estate=0 WHERE Id IN ($Ids)";
                  $UpdateResult=@mysql_query($UpdateSql);
                   if($UpdateResult && mysql_affected_rows()>0){
                              $Log.="ID 为$Id 的记录审核成功!<br>"; 
                           $UpdateSql2=" UPDATE  $DataPublic.nonbom5_goodsstock   K 
                           LEFT JOIN $DataIn.nonbom8_reback R ON R.GoodsId=K.GoodsId
                         SET K.oStockQty=K.oStockQty+R.Qty,K.wStockQty=K.wStockQty+R.Qty,K.lStockQty=K.lStockQty+R.Qty WHERE R.Id IN ($Ids)";
                             $UpdateResult2=@mysql_query($UpdateSql2);
                            if($UpdateResult2){
                                         $Log.="配件实物仓库和领用库存退回成功!<br>"; 
                                  }
                         else{
                                         $Log.="<div class='redB'>配件实物库存和领用库存退回失败! $UpdateSql2</div><br>"; 
                                     }
                           $UpdateSql3="UPDATE  $DataIn.nonbom7_code  C   
                           LEFT JOIN  $DataIn.nonbom8_rebackfixed B  ON B.BarCode=C.BarCode
                           LEFT JOIN $DataIn.nonbom8_reback R  ON R.Id=B.BackId
                            SET C.Estate=1,C.Number='0'  WHERE R.Id IN ($Ids)"; 
                            $UpdateResult3=@mysql_query($UpdateSql3);
                           if($UpdateResult3){
                                         $Log.="固定资产状态和最新领用人更新成功!<br>"; 
                                  }
                         else{
                                         $Log.="<div class='redB'>固定资产状态和最新领用人更新失败! $UpdateSql3</div><br>"; 
                                     }
                       }
               else{
                           $Log.="<div class='redB'>ID 为$Id 的记录审核失败! $UpdateSql</div><br>"; 
                           $OperationResult="N";
                   }
                          $fromWebPage=$funFrom."_m";
           break;
       default://退回仓库
                          if($Attached!=""){//有上传文件
	                                 	$FileType=".jpg";
	                                 	$OldFile=$Attached;
	                                 	$FilePath="../download/nonbom21/";
		                                 if(!file_exists($FilePath)){
		                                 	makedir($FilePath);
		                                 	}
	                                 	$PreFileName=$Id.$FileType;
	                                 	$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
	                                 	if($Attached){
	                                        $PictureStr=",Picture='$Attached'";
		                                 	}
                                       else  $PictureStr="";
	                           	}
                  $UpdateSql="UPDATE $DataIn.nonbom8_reback SET  Qty=$thisthQty,Remark='$Remark' $PictureStr WHERE Id=$Id";
                  $UpdateResult=@mysql_query($UpdateSql);
                   if($UpdateResult && mysql_affected_rows()>0){
                        $Log.="配件ID$GoodsId 退回仓库数量 为 $thisthQty 更新成功!<br>"; 
                                  }
               else{
                           $Log.="<div class='redB'>配件ID$GoodsId 退回仓库数量 为 $thisthQty 更新失败! $UpdateSql</div><br>"; 
                           $OperationResult="N";
                   }

 $DelSql="DELETE  FROM $DataIn.nonbom8_rebackfixed WHERE BackId=$Id";  $DelResult=@mysql_query($DelSql);
                         $backCount=count($backCode);
                         for($k=0;$k<$backCount;$k++){
                                    $BarCode=$backCode[$k];
                                    $IN_Sql1="INSERT INTO $DataIn.nonbom8_rebackfixed(Id,BackId,GoodsId,BarCode)
                                    VALUES(NULL,'$Id','$GoodsId','$BarCode')"; $IN_recode1=@mysql_query($IN_Sql1);
                                   if($IN_recode1){
                                            $Log.="&nbsp;&nbsp;固定条码为 $BarCode 的配件退回成功!<br>"; 
                                           $UpdateSql="UPDATE  $DataIn.nonbom7_code SET Estate=1 WHERE BarCode=$BarCode"; $UpdateResult=@mysql_query($UpdateSql);
                                      }
                                   else $Log.="<div class=redB>&nbsp;&nbsp;固定条码为 $BarCode 的配件退回失败!$IN_Sql1<div class=redB><br>"; 
                              }

          break;
}


$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非BOM配件领用记录报废";		//需处理
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
                  $UpdateSql="UPDATE $DataIn.nonbom8_bf SET  Estate=0 WHERE Id IN ($Ids)";
                  $UpdateResult=@mysql_query($UpdateSql);
                   if($UpdateResult && mysql_affected_rows()>0){
                              $Log.="&nbsp;&nbsp;&nbsp;ID 为$Id 的记录审核成功!<br>"; 
                           $UpdateSql2=" UPDATE  $DataPublic.nonbom5_goodsstock   K 
                           LEFT JOIN $DataIn.nonbom8_bf R ON R.GoodsId=K.GoodsId
                           SET K.lStockQty=K.lStockQty-R.Qty WHERE R.Id IN ($Ids) AND K.lStockQty>=R.Qty";
                             $UpdateResult2=@mysql_query($UpdateSql2);
                            if($UpdateResult2){
                                         $Log.="&nbsp;&nbsp;&nbsp;配件领用库存退回成功!<br>"; 
                                 }
                         else{
                                         $Log.="&nbsp;&nbsp;&nbsp;<div class='redB'>配件领用库存退回失败! $UpdateSql2</div><br>"; 
                                     }

                           $UpdateSql3="UPDATE  $DataIn.nonbom7_code  C   
                           LEFT JOIN  $DataIn.nonbom8_bffixed B  ON B.BarCode=C.BarCode
                           LEFT JOIN $DataIn.nonbom8_bf  R  ON R.Id=B.BfId
                            SET C.Estate=0,C.Number='0'  WHERE R.Id IN ($Ids)"; 
                            $UpdateResult3=@mysql_query($UpdateSql3);
                           if($UpdateResult3){
                                         $Log.="固定资产状态和最新领用人更新成功!<br>"; 
                                  }
                         else{
                                         $Log.="<div class='redB'>固定资产状态和最新领用人更新失败! $UpdateSql3</div><br>"; 
                                     }

                       }
               else{
                           $Log.="&nbsp;&nbsp;&nbsp;<div class='redB'>ID 为$Id 的记录审核失败! $UpdateSql</div><br>"; 
                           $OperationResult="N";
                   }
             $fromWebPage=$funFrom."_m";
           break;
       default://报废更新
                        if($Attached!=""){//有上传文件
	                                 	$FileType=".jpg";
	                                 	$OldFile=$Attached;
	                                 	$FilePath="../download/nonbom22/";
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
                  $UpdateSql="UPDATE $DataIn.nonbom8_bf SET  Qty=$thisbfQty,Remark='$Remark' $PictureStr WHERE Id=$Id";
                  $UpdateResult=@mysql_query($UpdateSql);
                   if($UpdateResult && mysql_affected_rows()>0){
                        $Log.="配件ID$GoodsId 报废数量 为 $thisthQty 更新成功!<br>"; 
                             }
                   else{
                           $Log.="<div class='redB'>配件ID$GoodsId 报废数量 为 $thisthQty 更新失败! $UpdateSql</div><br>"; 
                           $OperationResult="N";
                        }

                       $UpdateSql1="UPDATE  $DataIn.nonbom7_code  C  
                        LEFT JOIN $DataIn.nonbom8_bffixed B ON B.BarCode=C.BarCode  
                        SET  Estate=2 WHERE  B.BfId=$Id ";
                        $UpdateResult=@mysql_query($UpdateSql1);                         
                         $DelSql="DELETE  FROM $DataIn.nonbom8_bffixed WHERE BfId=$Id";  $DelResult=@mysql_query($DelSql);           
                         $bfCount=count($bfCode);
                         $bfBarCode="";
                         for($k=0;$k<$bfCount;$k++){
                                  $BarCode=$bfCode[$k];
                                  $IN_Sql1="INSERT INTO $DataIn.nonbom8_bffixed(Id,BfId,GoodsId,BarCode)VALUES(NULL,'$Id','$GoodsId','$BarCode')"; 
                                  $IN_recode1=@mysql_query($IN_Sql1);
                                  if($IN_recode1){
                                             $Log.="&nbsp;&nbsp;固定条码为 $BarCode 的配件报废成功!<br>"; 
                                           $UpdateSql="UPDATE  $DataIn.nonbom7_code SET Estate=0 WHERE BarCode=$BarCode"; $UpdateResult=@mysql_query($UpdateSql);
                                         }
                                 else $Log.="<div class=redB>&nbsp;&nbsp;固定条码为 $BarCode 的配件报废失败!$IN_Sql1<div class=redB><br>"; 
                           }


          break;
}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
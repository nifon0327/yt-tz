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
$TempDate=date("Ym");
switch($ActionId){
       case "157"://退回仓库
               $CheckMaxResult=mysql_fetch_array(mysql_query("SELECT Max(BillNumber)  AS MaxBillNumber  FROM  $DataIn.nonbom8_reback WHERE BillNumber LIKE '$TempDate%'",$link_id));
               $MaxBillNumber=$CheckMaxResult["MaxBillNumber"];
               if($MaxBillNumber=="")$MaxBillNumber=$TempDate."0001";
               else $MaxBillNumber=$MaxBillNumber+1;
                $IN_Sql="INSERT INTO $DataIn.nonbom8_reback(Id,BillNumber,GoodsId,Qty,Remark,BackNumber,Picture,Estate,Locks,Date,Operator)
                VALUES(NULL,'$MaxBillNumber','$GoodsId','$thisthQty','$Remark','$Operator','','1','0','$DateTime','$Operator')";
                  $IN_recode=@mysql_query($IN_Sql);
                 $Id=mysql_insert_id();
                   if($IN_recode && mysql_affected_rows()>0){
                            $Log.="&nbsp;&nbsp;配件ID$GoodsId 退回仓库数量 为 $thisthQty 成功!<br>"; 
                         $backCount=count($backCode);
                         for($k=0;$k<$backCount;$k++){
                                     $BarCode=$backCode[$k];
                                    $IN_Sql1="INSERT INTO $DataIn.nonbom8_rebackfixed(Id,BackId,GoodsId,BarCode)
                                    VALUES(NULL,'$Id','$GoodsId','$BarCode')"; 
                                   $IN_recode1=@mysql_query($IN_Sql1);
                                   if($IN_recode1){
                                            $Log.="&nbsp;&nbsp;固定条码为 $BarCode 的配件退回成功!<br>"; 
                                      }
                                   else $Log.="<div class=redB>&nbsp;&nbsp;固定条码为 $BarCode 的配件退回失败!$IN_Sql1<div class=redB><br>"; 
                           }
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
		                                    	$Log.="&nbsp;&nbsp;退回仓库图片上传成功！$inRecode <br>";
	                                 	  		$sql = "UPDATE $DataIn.nonbom8_reback SET Picture='$Attached' WHERE Id=$Id";
		                                   		$result = mysql_query($sql);
		                                 	}
	                                 	else{
		                                 	  	$Log.="<div class=redB>&nbsp;&nbsp;退回仓库图片上传失败！$inRecode </div><br>";
		                                  	 	$OperationResult="N";			
		                                 	}
	                           	}
                     }
               else{
                           $Log.="&nbsp;&nbsp;<div class='redB'>配件ID$GoodsId 退回仓库数量 为 $thisthQty 失败! $IN_Sql</div><br>"; 
                           $OperationResult="N";
                   }
                    $fromWebPage="nonbom21_read";
          break;

      case "158"://领用报废
               $CheckMaxResult=mysql_fetch_array(mysql_query("SELECT Max(BillNumber)  AS MaxBillNumber  FROM  $DataIn.nonbom8_bf WHERE BillNumber LIKE '$TempDate%'",$link_id));
               $MaxBillNumber=$CheckMaxResult["MaxBillNumber"];
               if($MaxBillNumber=="")$MaxBillNumber=$TempDate."0001";
               else $MaxBillNumber=$MaxBillNumber+1;
                  $IN_Sql="INSERT INTO $DataIn.nonbom8_bf(Id,BillNumber,GoodsId,Qty,Remark,bfNumber,Picture,Estate,Locks,Date,Operator)
                VALUES(NULL,'$MaxBillNumber','$GoodsId','$thisbfQty','$Remark','$Operator','','1','0','$DateTime','$Operator')";
                  $IN_recode=@mysql_query($IN_Sql);
                 $Id=mysql_insert_id();
                   if($IN_recode && mysql_affected_rows()>0){
                              $Log.="配件ID$GoodsId 报废数量 为 $thisthQty 成功!<br>"; 
                             $UpdateSql=" UPDATE  $DataPublic.nonbom5_goodsstock  SET lStockQty=lStockQty-$thisbfQty WHERE GoodsId=$GoodsId AND lStockQty>=$thisbfQty";
                               $UpdateResult=@mysql_query($UpdateSql);
                               if($UpdateResult){
                                         $Log.="配件ID$GoodsId 的领用库存更新成功!<br>"; 
                                 }
                             else{
                                         $Log.="<div class='redB'>配件ID$GoodsId 的领用库存更新失败! $UpdateSql</div><br>"; 
                                     }

                         $bfCount=count($bfCode);
                         for($k=0;$k<$bfCount;$k++){
                                  $BarCode=$bfCode[$k];
                                  $IN_Sql1="INSERT INTO $DataIn.nonbom8_bffixed(Id,BfId,GoodsId,BarCode)VALUES(NULL,'$Id','$GoodsId','$BarCode')"; 
                                  $IN_recode1=@mysql_query($IN_Sql1);
                                  if($IN_recode1){
                                             $Log.="&nbsp;&nbsp;固定条码为 $BarCode 的配件报废成功!<br>"; 
                                         }
                                 else $Log.="<div class=redB>&nbsp;&nbsp;固定条码为 $BarCode 的配件报废失败!$IN_Sql1<div class=redB><br>"; 
                           }
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
		                                 	$Log.="&nbsp;&nbsp;报废图片上传成功！$inRecode <br>";
	                                 	  		$sql = "UPDATE  $DataIn.nonbom8_bf SET Picture='$Attached' WHERE Id=$Id";
		                                   		$result = mysql_query($sql);
		                                 	}
	                                 	else{
		                                 	  	$Log.="<div class=redB>&nbsp;&nbsp;报废图片上传失败！$inRecode </div><br>";
		                                  	 	$OperationResult="N";			
		                                 	}
	                           	}
                     }
               else{
                           $Log.="<div class='redB'>配件ID$GoodsId 报废数量 为 $thisthQty 失败! $IN_Sql</div><br>"; 
                           $OperationResult="N";
                      }
                    $fromWebPage="nonbom22_read";
          break;
       case "159": //盘点
               $CheckMaxResult=mysql_fetch_array(mysql_query("SELECT Max(BillNumber)  AS MaxBillNumber  FROM  $DataIn.nonbom8_pd WHERE BillNumber LIKE '$TempDate%'",$link_id));
               $MaxBillNumber=$CheckMaxResult["MaxBillNumber"];
               if($MaxBillNumber=="")$MaxBillNumber=$TempDate."0001";
               else $MaxBillNumber=$MaxBillNumber+1;
                  $IN_Sql="INSERT INTO $DataIn.nonbom8_pd(Id,BillNumber,GoodsId,TotalQty,Qty,Remark,PdNumber, Estate, Locks, Date, Operator)
                   VALUES(NULL,'$MaxBillNumber','$GoodsId','$MaxQty','$pdQty','$Remark','$Operator','2','0','$DateTime','$Operator')";
                  $IN_recode=@mysql_query($IN_Sql);
                   $Id=mysql_insert_id();
                   if($IN_recode && mysql_affected_rows()>0){
                                $Log.="配件ID$GoodsId 盘点,总共数量:$MaxQty , 盘点后数量:$pdQty 保存成功!<br>"; 
                                $pdCount=count($pdCode);
                                 for($k=0;$k<$pdCount;$k++){
                                          $BarCode=$pdCode[$k];
                                          $IN_Sql1="INSERT INTO $DataIn.nonbom8_pdfixed(Id,PdId,GoodsId,BarCode)VALUES(NULL,'$Id','$GoodsId','$BarCode')";                                                                                                                                                                         
                                          $IN_recode1=@mysql_query($IN_Sql1);
                                          if($IN_recode1){
                                                     $Log.="&nbsp;&nbsp;固定条码为 $BarCode 的配件盘点保存成功!<br>"; 
                                                 }
                                         else $Log.="<div class=redB>&nbsp;&nbsp;固定条码为 $BarCode 的配件盘点保存失败!$IN_Sql1<div class=redB><br>"; 
                                   }
                          }
                   else{
                           $Log.="<div class='redB'>配件ID$GoodsId 盘点,总共数量:$MaxQty , 盘点后数量:$pdQty 保存失败! $IN_Sql</div><br>"; 
                           $OperationResult="N";
                        }

                    $fromWebPage="nonbom24_read";
               break;
          case "160"://转出
               $CheckMaxResult=mysql_fetch_array(mysql_query("SELECT Max(BillNumber)  AS MaxBillNumber  FROM  $DataIn.nonbom8_turn WHERE BillNumber LIKE '$TempDate%'",$link_id));
               $MaxBillNumber=$CheckMaxResult["MaxBillNumber"];
               if($MaxBillNumber=="")$MaxBillNumber=$TempDate."0001";
               else $MaxBillNumber=$MaxBillNumber+1;
                  $IN_Sql="INSERT INTO $DataIn.nonbom8_turn(Id,BillNumber,GoodsId,Qty,Remark,OutNumber,InNumber, Estate, Locks, Date, Operator)
                   VALUES(NULL,'$MaxBillNumber','$GoodsId','$turnQty','$Remark','$Operator','$InNumber','2','0','$DateTime','$Operator')";
                  $IN_recode=@mysql_query($IN_Sql);
                   $Id=mysql_insert_id();
                   if($IN_recode && mysql_affected_rows()>0){
                                $Log.="配件ID$GoodsId 转出数量: $turnQty  给 $InName 成功!<br>"; 
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
                          }
                   else{
                           $Log.="<div class='redB'>配件ID$GoodsId 转出数量: $turnQty  给 $InName 失败! $IN_Sql</div><br>"; 
                           $OperationResult="N";
                        }
                    $fromWebPage="nonbom23_read";
             break;
}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
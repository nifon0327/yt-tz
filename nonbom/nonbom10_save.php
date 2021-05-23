<?php 
include "../model/modelhead.php";
$Log_Item="非BOM配件报废数量";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
$sheetInSql="INSERT INTO $DataIn.nonbom10_outsheet (Id,GoodsId,Qty,Remark,ReturnReasons,Estate,Locks,Date,Operator) VALUES (NULL,'$GoodsId','$Qty','$Remark','','2','0','$DateTime','$Operator')";
$sheetInAction=@mysql_query($sheetInSql);
$Id=mysql_insert_id();
if($sheetInAction && mysql_affected_rows()>0){
        $Log.="&nbsp;&nbsp;非BOM配件$GoodsId 转入 $Qty 成功  <br>";	
	     //库存增加
	$sql = "UPDATE $DataPublic.nonbom5_goodsstock SET wStockQty=wStockQty-'$Qty',oStockQty=oStockQty-'$Qty' WHERE GoodsId='$GoodsId' AND wStockQty>='$Qty' AND oStockQty>='$Qty' ";
	$result = mysql_query($sql);
          if($result && mysql_affected_rows()>0){
                    $Log.="&nbsp;&nbsp;非BOM配件$GoodsId 库存更新成功!  <br>";	   
             }
           $tempCount=count($BarCode);
           for($k=0;$k<$tempCount;$k++){
					$thisBarCode=$BarCode[$k];	
        			$thisbfRemark=$bfRemark[$k];	
     	   		    $thisPicture=$Picture[$k];	
                              $IN_Sql="INSERT INTO $DataIn.nonbom10_bffixed(Id,BfId,GoodsId,BarCode,Picture,Estate,Date,Remark,Operator)
                               VALUES(NULL,'$Id','$GoodsId','$thisBarCode','','1','$DateTime','$thisbfRemark','$Operator')";
                              $IN_res=mysql_query($IN_Sql);
                              if($IN_res && mysql_affected_rows()>0){
           		                          $Log.="&nbsp;&nbsp;配件(固定资产)编号信息 $thisBarCode 报废成功!<br>";
                                         $UpdateSql="UPDATE  $DataIn.nonbom7_code SET Estate=0 WHERE BarCode=$thisBarCode"; $UpdateResult=@mysql_query($UpdateSql);
                                         $thisMid=mysql_insert_id();
                                          if($thisPicture!=""){
	                                        	$FileType=".jpg";
	                                      	   $OldFile=$thisPicture;
	                                      	   $FilePath="../download/nonbombf/";
	                                         	if(!file_exists($FilePath)){
			                                          makedir($FilePath);
		                                      	}
		                                      $PreFileName="B".$thisMid.$FileType;
               		                          $Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
	                                        	if($Attached){
		                                                 $Log.="&nbsp;&nbsp;报废图片上传成功！$inRecode <br>";
		               	                                 $sql = "UPDATE $DataIn.nonbom10_bffixed SET Picture='$Attached' WHERE Id=$thisMid";
			                                             $result = mysql_query($sql);
			                                        }
		                                      else{
		                                            	  $Log.="<div class=redB>&nbsp;&nbsp;报废图片上传失败! </div><br>";	
		                                           	}
                                              }
                                     }
                              else{
		                     	  $Log.="<div class=redB>&nbsp;&nbsp;配件(固定资产)编号信息 $thisBarCode 报废失败 $IN_Sql </div><br>";	
                                     }
                          }
               //    }
	     }
else{
        $Log.="<span>&nbsp;&nbsp;非BOM配件$GoodsId 报废 $Qty 失败  $sheetInSql</span><br>";	
        $OperationResult="N";
}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

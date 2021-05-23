<?php 
include "../model/modelhead.php";
$Log_Item="非BOM配件转入数量";			//需处理
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
$sheetInSql="INSERT INTO $DataIn.nonbom9_insheet (Id,GoodsId,Qty,Remark,Locks,Date,Operator) VALUES (NULL,'$GoodsId','$Qty','$Remark','0','$DateTime','$Operator') ";
$sheetInAction=@mysql_query($sheetInSql);
$Id=mysql_insert_id();
if($sheetInAction && mysql_affected_rows()>0){
        $Log.="&nbsp;&nbsp;非BOM配件$GoodsId 转入 $Qty 成功  <br>";	
	     //库存增加
	     $sql = "UPDATE $DataPublic.nonbom5_goodsstock SET wStockQty=wStockQty+'$Qty',oStockQty=oStockQty+'$Qty' WHERE GoodsId='$GoodsId'";
	     $result = mysql_query($sql);
          if($result && mysql_affected_rows()>0){
                    $Log.="&nbsp;&nbsp;非BOM配件$GoodsId 库存更新成功!  <br>";	   
             }
           $tempCount=count($CkId);
           for($k=0;$k<$tempCount;$k++){
					$thisGoodsNum=$GoodsNum[$k];	
        			$thisCkId=$CkId[$k];	
     	   		    $thisPicture=$Picture[$k];	
                            $MaxResult=mysql_fetch_array(mysql_query("SELECT  MAX(BarCode) AS MaxBarCode  FROM $DataIn.nonbom7_code",$link_id));
                             $MaxBarCode=$MaxResult["MaxBarCode"];
                             if($MaxBarCode=="")$MaxBarCode="8000000000001";
                             else $MaxBarCode=$MaxBarCode+1;
                              $IN_Sql="INSERT INTO $DataIn.nonbom7_code(Id,rkId,GoodsId,BarCode,GoodsNum,CkId,TypeSign,Picture,Estate,Date,Operator)
                               VALUES(NULL,'$Id','$GoodsId','$MaxBarCode','$thisGoodsNum','$thisCkId','2','','1','$DateTime','$Operator')";
                              $IN_res=mysql_query($IN_Sql);
                              if($IN_res && mysql_affected_rows()>0){
                                         $thisMid=mysql_insert_id();
           		                          $Log.="&nbsp;&nbsp;配件(固定资产)编号信息 $MaxBarCode 新增成功!<br>";
                                          if($thisPicture!=""){
	                                        	$FileType=".jpg";
	                                      	   $OldFile=$thisPicture;
	                                      	   $FilePath="../download/nonbomCode/";
	                                       	if(!file_exists($FilePath)){
			                                          makedir($FilePath);
		                                      	}
		                                      $PreFileName="C".$thisMid.$FileType;
               		                          $Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
	                                        	if($Attached){
		                                                 $Log.="&nbsp;&nbsp;图片上传成功！$inRecode <br>";
		               	                                 $sql = "UPDATE $DataIn.nonbom7_code SET Picture='$Attached' WHERE Id=$thisMid";
			                                             $result = mysql_query($sql);
			                                        }
		                                      else{
		                                            	  $Log.="<div class=redB>&nbsp;&nbsp;图片上传失败! </div><br>";	
		                                           	}
                                              }
                                     }
                              else{
		                     	  $Log.="<div class=redB>&nbsp;&nbsp;配件(固定资产)编号信息 $MaxBarCode 新增失败 $IN_Sql </div><br>";	
                                     }
                          }
                  // }
	     }
else{
        $Log.="<span>&nbsp;&nbsp;非BOM配件$GoodsId 转入 $Qty 失败  $sheetInSql</span><br>";	
        $OperationResult="N";
}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 配件条码保存");
$fromWebPage="nonbom14_code";
$nowWebPage="nonbom14_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$DateTime=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Log_Item="配件条码";
$Log_Funtion="设置配件条码";
$ALType="From=$From";
$Count=count($BarCode);
for($k=0;$k<$Count;$k++){
       $tempBarCode=$BarCode[$k];
       $tempGoodsNum=$GoodsNum[$k];
       $tempPicture=$Picture[$k];
       $tempCkId=$CkId[$k];
        $CheckResult=mysql_fetch_array(mysql_query("SELECT  * FROM $DataIn.nonbom7_code WHERE BarCode='$tempBarCode' AND rkId=$rkId",$link_id));
         $thisMid= $CheckResult["Id"];   
         if($thisMid>0){
               $UpdateSql = "UPDATE $DataIn.nonbom7_code SET GoodsNum='$tempGoodsNum',CkId='$tempCkId',Date='$DateTime',Operator=$Operator WHERE Id=$thisMid";
			  $UpdateResult = mysql_query($UpdateSql);  
              if($UpdateResult && mysql_affected_rows()>0){
		               $Log.="&nbsp;&nbsp;固定资产配件条码 $tempBarCode 更新成功  <br>";	
                   if($tempPicture!=""){
	                 	$FileType=".jpg";
	               	   $OldFile=$tempPicture;
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
		                     	  $Log.="<div class=redB>&nbsp;&nbsp;图片上传失败！$inRecode </div><br>";	
		                    	}
                             }
                      }
                }
          else{
                     if($tempGoodsNum!=""){
                            $MaxResult=mysql_fetch_array(mysql_query("SELECT  MAX(BarCode) AS MaxBarCode  FROM $DataIn.nonbom7_code",$link_id));
                             $MaxBarCode=$MaxResult["MaxBarCode"];
                             if($MaxBarCode=="")$MaxBarCode="8000000000001";
                             else $MaxBarCode=$MaxBarCode+1;
                              $IN_Sql="INSERT INTO $DataIn.nonbom7_code(Id,rkId,GoodsId,BarCode,GoodsNum,CkId,Picture,Estate,Date,Operator)
VALUES(NULL,'$rkId','$GoodsId','$MaxBarCode','$tempGoodsNum','$tempCkId','','1','$DateTime','$Operator')";
                              $IN_res=mysql_query($IN_Sql);
                              if($IN_res && mysql_affected_rows()>0){
                                         $thisMid=mysql_insert_id();
           		                          $Log.="&nbsp;&nbsp;配件条码 $MaxBarCode 新增成功!<br>";
                                          if($tempPicture!=""){
	                                        	$FileType=".jpg";
	                                      	   $OldFile=$tempPicture;
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
		                                            	  $Log.="<div class=redB>&nbsp;&nbsp;图片上传失败！$inRecode </div><br>";	
		                                           	}
                                              }
                                     }
                              else{
		                     	  $Log.="<div class=redB>&nbsp;&nbsp;配件条码 $MaxBarCode 新增失败 $IN_Sql </div><br>";	
                                     }
                         }
                 }
}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
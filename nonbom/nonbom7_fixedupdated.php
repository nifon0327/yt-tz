<?php
//$tempArray=explode("|",$TempFixed);
$tempCount=count($BarCode);
for($k=0;$k<$tempCount;$k++){
           // $Tempvalue=explode("@",$tempArray[$k]);
		    $thisBarCode=$BarCode[$k];	
			$thisGoodsNum=$GoodsNum[$k];	
        	$thisCkId=$CkId[$k];	
     	    $thisPicture=$Picture[$k];	
           if($thisBarCode!=0){
                   $CheckResult=mysql_fetch_array(mysql_query("SELECT  * FROM $DataIn.nonbom7_code WHERE BarCode='$thisBarCode' AND rkId=$Id",$link_id));
                   $thisMid= $CheckResult["Id"];   
               	   $UpdateSql = "UPDATE $DataIn.nonbom7_code SET GoodsNum='$thisGoodsNum',CkId='$thisCkId',Date='$DateTime',Operator=$Operator WHERE Id=$thisMid";
			       $UpdateResult = mysql_query($UpdateSql);  
                  if($UpdateResult && mysql_affected_rows()>0){
                                  $Log.="&nbsp;&nbsp;固定资产信息资料 $thisBarCode 更新成功!<br>";
                        }
                 else{
                                  $Log.="&nbsp;&nbsp;<span class='redB'>固定资产信息资料 $thisBarCode 更新失败! $UpdateSql</span><br>";
                        }
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
		                     	  $Log.="<div class=redB>&nbsp;&nbsp;图片上传失败！$inRecode </div><br>";	
		                    	}
                       }
            }
         else{
                     if($thisGoodsNum!=""){
                            $MaxResult=mysql_fetch_array(mysql_query("SELECT  MAX(BarCode) AS MaxBarCode  FROM $DataIn.nonbom7_code",$link_id));
                             $MaxBarCode=$MaxResult["MaxBarCode"];
                             if($MaxBarCode=="")$MaxBarCode="8000000000001";
                             else $MaxBarCode=$MaxBarCode+1;
                              $IN_Sql="INSERT INTO $DataIn.nonbom7_code(Id,rkId,GoodsId,BarCode,GoodsNum,CkId,TypeSign,Picture,Estate,Date,Operator)
                           VALUES(NULL,'$Id','$GoodsId','$MaxBarCode','$thisGoodsNum','$thisCkId','$TypeSign','','1','$DateTime','$Operator')";
                              $IN_res=mysql_query($IN_Sql);
                              if($IN_res && mysql_affected_rows()>0){
                                         $thisMid=mysql_insert_id();
           		                          $Log.="&nbsp;&nbsp;固定资产信息资料 $MaxBarCode 新增成功!<br>";
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
		                                                 $Log.="&nbsp;&nbsp;固定资产图片上传成功！$inRecode <br>";
		               	                                 $sql = "UPDATE $DataIn.nonbom7_code SET Picture='$Attached' WHERE Id=$thisMid";
			                                             $result = mysql_query($sql);
			                                        }
		                                      else{
		                                            	  $Log.="<div class=redB>&nbsp;&nbsp;固定资产图片上传失败！$inRecode </div><br>";	
		                                           	}
                                              }
                                     }
                              else{
		                     	  $Log.="<div class=redB>&nbsp;&nbsp;固定资产信息资料 $MaxBarCode 新增失败 $IN_Sql </div><br>";	
                                     }
                         }
               }
}
?>
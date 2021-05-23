<?php
//$tempArray=explode("|",$TempFixed);
$rkIdArray=explode(",", $rkGoodIds);

$tempCount=count($tempGoodsId);
for($k=0;$k<$tempCount;$k++){
      //   $Tempvalue=explode("@",$tempArray[$k]);
		    $thisGoodsId=$tempGoodsId[$k];	
			$thisGoodsNum=$GoodsNum[$k];	
        	$thisCkId=$CkId[$k];	
     	    $thisPicture=$Picture[$k];	
     	    if  (in_array($thisGoodsId, $rkIdArray)){
                            $MaxResult=mysql_fetch_array(mysql_query("SELECT  MAX(BarCode) AS MaxBarCode  FROM $DataIn.nonbom7_code",$link_id));
                             $MaxBarCode=$MaxResult["MaxBarCode"];
                             if($MaxBarCode=="")$MaxBarCode="8000000000001";
                             else $MaxBarCode=$MaxBarCode+1;
                              $IN_Sql="INSERT INTO $DataIn.nonbom7_code(Id,rkId,GoodsId,BarCode,GoodsNum,CkId,TypeSign,Picture,Estate,Date,Operator)
                               VALUES(NULL,'$rkId','$thisGoodsId','$MaxBarCode','$thisGoodsNum','$thisCkId','1','','1','$DateTime','$Operator')";
                              $IN_res=mysql_query($IN_Sql);
                              if($IN_res && mysql_affected_rows()>0){
                                         $thisMid=mysql_insert_id();
           		                          $Log.="&nbsp;&nbsp;配件编号信息 $MaxBarCode 新增成功!<br>";
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
		                     	  $Log.="<div class=redB>&nbsp;&nbsp;配件编号信息 $MaxBarCode 新增失败 $IN_Sql </div><br>";	
                             }
          }                     
}
?>
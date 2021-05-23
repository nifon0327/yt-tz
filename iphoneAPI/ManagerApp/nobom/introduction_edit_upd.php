<?php 

/**
 * 图片缩放函数（可设置高度固定，宽度固定或者最大宽高，支持gif/jpg/png三种类型）
 * Author : Specs
 * Homepage: http://9iphp.com
 *
 * @param string $source_path 源图片
 * @param int $target_width 目标宽度
 * @param int $target_height 目标高度
 * @param string $fixed_orig 锁定宽高（可选参数 width、height或者空值）
 * @return string
 */

$Log_Funtion="修改";
$GoodsName = $info[1];
$GoodsType = $info[0];
$edit_id = $info[2];
 	 $Log_Item="公司培训"; 
     $curDate=date("Y-m-d");
	 $DateTime=date("Y-m-d H:i:s");
	
	 $OperationResult="N";
	 $Operator=$LoginNumber;
	 $Log = "";
$ModifyTimes = 0;
$oldFile = $oldIcon = '';
	 $checkHasName = mysql_query("select ModifyTimes,Name,Icon,File from $DataIn.studysheet where Id='$edit_id'");
	 if ($checkHasNameRow = mysql_fetch_array($checkHasName))
 {
	 $ModifyTimes = $checkHasNameRow['ModifyTimes'];
	 $oldIcon = $checkHasNameRow['Icon'];
	 	 $oldFile = $checkHasNameRow['File'];

	 
 }	
 
 $ModifyTimes ++;
 
 
   
	 
	  mysql_query("update $DataIn.studysheet set Name='$GoodsName',TypeId='$GoodsType',modified='$DateTime',modifier='$Operator',ModifyTimes=$ModifyTimes  where Id=$edit_id");
	 
  $insertedId =$edit_id;

	 
	 $imgCount = $_POST["FileCount"];
	
	 if ($imgCount>0 && mysql_affected_rows()>0) {
		 $OperationResult = "Y";
		 for ($i=0; $i<$imgCount;$i++) {
			  $upFile=$_POST["Data$i"];
			  $fileUpName = 'upFile'."$i";
			  $fixed = $i==0?"_F_$ModifyTimes":"_C_$ModifyTimes";
			    $fileName="".$insertedId.$fixed.".png";
				
				$path = "../../download/nobom_intro/".$fileName;
				if(move_uploaded_file($_FILES[$fileUpName]['tmp_name'],$path))
				{
					if ($i==0) {
					    $in_sql= @mysql_query("update $DataIn.studysheet set  File='$fileName'  where Id='$insertedId'");
						
								 $OperationResult = "Y";
								 $todel = "../../download/nobom_intro/".$oldFile;
								 @unlink($todel);
								 
					} else {
					//	$fileName2="".$insertedId.$fixed."_s.jpg";
				
			//	$target_path = "../../download/nobom_intro/".$fileName2;
						
						// myImageResize($path,$target_path, 160, 160,'width');
						$in_sql= @mysql_query("update $DataIn.studysheet set  Icon='$fileName'  where Id='$insertedId'");
						$todel = "../../download/nobom_intro/".$oldIcon;
								 @unlink($todel);
						 
						
					}
					$infoSTR.= "Id'$insertedId'上传文件成功！";	
					
						
					
				} else {
						 $OperationResult = "N";
					$infoSTR.= "Id'$insertedId'上传文件失败！";		
				}
		 }
	 }

 
		
	

	$jsonArray = array(
				"ActionId" => "$ActionId",
				"Result" => "$OperationResult",
				"Info"=>"$infoSTR"
			);
			
			 
 $IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log $infoSTR','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

?>
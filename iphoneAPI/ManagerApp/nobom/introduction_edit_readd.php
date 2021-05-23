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
function myImageResize($source_path,$target_path, $target_width = 200, $target_height = 200, $fixed_orig = ''){
    $source_info = getimagesize($source_path);
    $source_width = $source_info[0];
    $source_height = $source_info[1];
    $source_mime = $source_info['mime'];
    $ratio_orig = $source_width / $source_height;
    if ($fixed_orig == 'width'){
        //宽度固定
        $target_height = $target_width / $ratio_orig;
    }elseif ($fixed_orig == 'height'){
        //高度固定
        $target_width = $target_height * $ratio_orig;
    }else{
        //最大宽或最大高
        if ($target_width / $target_height > $ratio_orig){
            $target_width = $target_height * $ratio_orig;
        }else{
            $target_height = $target_width / $ratio_orig;
        }
    }
	
    switch ($source_mime){
        case 'image/gif':
            $source_image = imagecreatefromgif($source_path);
            break;
        
        case 'image/jpeg':
            $source_image = imagecreatefromjpeg($source_path);
            break;
        
        case 'image/png':
            $source_image = imagecreatefrompng($source_path);
            break;
        
        default:
            return false;
            break;
    }
    $target_image = imagecreatetruecolor($target_width, $target_height);
    imagecopyresampled($target_image, $source_image, 0, 0, 0, 0, $target_width, $target_height, $source_width, $source_height);
    //header('Content-type: image/jpeg');
    
  
    imagejpeg($target_image, $target_path,100);
	//imagedestroy() ;
	//(move_uploaded_file($target_image,$target_path));
}

$Log_Funtion="保存";
$GoodsName = $info[1];
$GoodsType = $info[0];

 	 $Log_Item="公司培训"; 
     $curDate=date("Y-m-d");
	 $DateTime=date("Y-m-d H:i:s");
	
	 $OperationResult="N";
	 $Operator=$LoginNumber;
	 $Log = "";

	 $checkHasName = mysql_query("select Id from $DataIn.studysheet where Name='$GoodsName'");
	 if ($checkHasNameRow = mysql_fetch_array($checkHasName))
 {
	$infoSTR = $Log = "已有同名培训文件记录";
	 
 }	 else {
	 
	  mysql_query("insert into $DataIn.studysheet (Name,Icon,File,creator,Date,TypeId,created) values 
		 
		 ('$GoodsName','－','－','$Operator','$curDate','$GoodsType','$DateTime')");
		 
		 
		 
		 
		 
	 
  $insertedId = mysql_insert_id();
  



	 
	 $imgCount = $_POST["FileCount"];
	
	 if ($imgCount>0 && $insertedId>0) {
		 
		 for ($i=0; $i<$imgCount;$i++) {
			  $upFile=$_POST["Data$i"];
			  $fileUpName = 'upFile'."$i";
			  $fixed = $i==0?"_F":"_C";
			    $fileName="".$insertedId.$fixed.".png";
				
				$path = "../../download/nobom_intro/".$fileName;
				if(move_uploaded_file($_FILES[$fileUpName]['tmp_name'],$path))
				{
					if ($i==0) {
					    $in_sql= @mysql_query("update $DataIn.studysheet set File='$fileName'  where Id='$insertedId'");
						
								 $OperationResult = "Y";
								 
								 
								   $qr = mysql_query("select GoodsId from $DataIn.nonbom4_goodsdata
						where GoodsName ='$GoodsName' limit 1");
						if ($ro=mysql_fetch_array($qr)) {

							$goodsId = $ro["GoodsId"];
							$did = $insertedId;
							mysql_query("update $DataIn.studysheet
						set GoodsId ='$goodsId' where Id=$did");
							mysql_query("update $DataIn.nonbom4_goodsdata
						set Introduction ='$fileName' ,Attached=1,Operator='$Operator' where GoodsId=$goodsId");
						
						}
					} else {
						//$fileName2="".$insertedId.$fixed."_s.jpg";
				
			//	$target_path = "../../download/nobom_intro/".$fileName2;
			//			
			//			 myImageResize($path,$target_path, 160, 160,'width');
						$in_sql= @mysql_query("update $DataIn.studysheet set Icon='$fileName'  where Id='$insertedId'");
						
					}
					$infoSTR.= "Id'$insertedId'上传文件成功！";	
					
				} else {
						 $OperationResult = "N";
					$infoSTR.= "Id'$insertedId'上传文件失败！";		
				}
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
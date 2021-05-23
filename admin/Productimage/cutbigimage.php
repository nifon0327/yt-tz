<?php   

    include "../../basic/parameter.inc";
    //$ProductId=86081;
    //$ImageFile="../../download/teststandard/T86081.jpg";
    include "../../model/modelfunction.php";
    
    $img= $ImageFile;
    $savePath="../../download/teststandard/T".$ProductId."/";
 
    $width = 1500;
    $height = 1500; 
	
    $source = @imagecreatefromjpeg( $img );
    $source_width = imagesx( $source );
    $source_height = imagesy( $source );

    $colnum=ceil($source_width / $width);
    $rownum=ceil($source_height / $height);
    $imgArray=array();
    
    $cut_sign=0;
	if(!file_exists($savePath)){
	      makedir($savePath);
	      $cut_sign=1;
	  }
	  else{
		   $m_time=filemtime ($img);
		  $cut_time=filemtime ($savePath ."img00_00.png");
		  if ($m_time>$cut_time) $cut_sign=1;
		//  echo "$m_time---$cut_time";
	  }
	  
  if ($cut_sign==1){
      for( $col = 0; $col < $colnum; $col++){  
          
        $last_width=$source_width-($col+1)*$width;
        if ($last_width<=0) {
            $cut_width=$source_width-$col*$width;
        }else{
            $cut_width=$width;
        }
       
        for( $row = 0; $row < $rownum; $row++)
        {
            $last_height=$source_height-($row+1)*$height;
            if ($last_height<=0){
                 $cut_height=$source_height-$row*$height;;
            }else{
                 $cut_height=$height;
            }
           
            $fn = sprintf( "img%02d_%02d.png", $col, $row );
            
            $fn=$savePath . $fn;
            
            $imgArray[$row][$col]=$fn;
 
            $im = @imagecreatetruecolor( $cut_width, $cut_height);
            imagecopyresized( $im, $source, 0, 0,
                $col * $width, $row * $height, $cut_width, $cut_height,
                $cut_width, $cut_height);
            imagejpeg( $im, $fn );
            imagedestroy( $im );
            }
         }
   }
     else{  
        //取得切割文件名
        for( $col = 0; $col < $colnum; $col++){ 
            for( $row = 0; $row < $rownum; $row++){
               $fn = sprintf( "img%02d_%02d.png", $col, $row );
               $fn=$savePath . $fn;
               $imgArray[$row][$col]=$fn; 
            }
        } 
  }

    //生成图片
    $imgList="<table  border='0' cellspacing='0' cellpadding='0' width='$source_width' height='$source_height'>";
     for( $row = 0; $row < $rownum; $row++){ 
          $imgList.="<tr>";
          for( $col = 0; $col < $colnum; $col++){  
             $imgList.="<td><image src='" . $imgArray[$row][$col] . "' width='100%' height='100%'/></td>"; 
          }
          $imgList.="</tr>";
      }
       $imgList.="</table>";
    
      // echo $imgList;
 ?>
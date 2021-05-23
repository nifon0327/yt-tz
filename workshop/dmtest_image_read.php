<?php
 $ImagePath="../download/dmtest/test.jpg";
 $img_info = getimagesize($ImagePath);
 $newsize=filesize($ImagePath);
 if ($filesize==0){
    $filesize=$newsize;
    
	 $img_width=$img_info[0];
	 $img_height=$img_info[1];
	 $wScale=$img_width/1080;
	 $hScale=$img_height/1920;
	
	if($wScale>=$hScale){
	   $SizeStr="width='1080' ";
	}
	else{
	   $SizeStr="height='1920' ";
	}
    echo "<center><img src='$ImagePath' $SizeStr></center>"; 
}
else{
    if ($newsize!=$filesize){
		echo "reload"; 
	}
}
?>

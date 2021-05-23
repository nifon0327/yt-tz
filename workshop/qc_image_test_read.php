<?php
	   
	   $ImagePath="http://192.168.20.31/download/upload/test/test.jpg";
	   $img_info = getimagesize($ImagePath); 
	   $img_width=$img_info[0];
	   $img_height=$img_info[1];
	   $wScale=$img_width/1080;
	   $hScale=$img_height/1660;
	   
	   if($wScale>=$hScale){
	       $SizeStr="width='1080' ";
	   }
	   else{
	       $SizeStr="height='1660' ";
	   }
   
   $ListSTR="<center><img src='$ImagePath' $SizeStr></center>";
   $upTime=date("H:i:s");

?>
 <input type='hidden' id='StuffId' name='StuffId' value='<?php echo $StuffId; ?>'>
<div id='headdiv' style='height:260px;'>
   <div class='cName'> <?php echo "<span class='blue_color'>配件Id-</span>配件名称";?></div>
</div>
<div id='listdiv' style='overflow: hidden;height:1660px;width:1080px;'>
<?php echo $ListSTR;?>
</div>

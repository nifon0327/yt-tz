<?php
	
	$staffNumber = $_GET["staffNumber"];
	
	$stuffFilePath="../../download/staffPhoto/";
	$stuffFile = $stuffFilePath."P".$staffNumber.".jpg";
	
	$smallIconStuffFile = $stuffFilePath."Profile_small/"."P".$staffNumber.".jpg";
	
	$size = getimagesize($stuffFile);
	$width = $size[0];
	$height = $size[1];
	
	$limitWidth = 300;
	$limitHeight = 100;
	//$smallIpadjpgFile=$stuffFilePath . "P". $productId . "_ipad_s.jpg";
	$limitHeight = $height * ($limitWidth / $width);
	
	$isConvert = "no";
	exec("convert -resize $limitWidth x $limitHeight -colorspace sRGB -transparent white -trim $stuffFile $smallIconStuffFile");
	
	if(file_exists($smallIconStuffFile))
	{
		$isConvert = "yes";
	}
	
	echo $isConvert;

	
?>
<?php
	
	$productId = $_GET["productId"];
	
	$stuffFilePath="../../download/teststandard/";
	$stuffFile = $stuffFilePath."T".$productId.".jpg";
	
	$smallIconStuffFile = $stuffFilePath."T".$productId."_ipad_s.jpg";
	
	$size = getimagesize($stuffFile);
	$width = $size[0];
	$height = $size[1];
	
	$limitWidth = 300;
	$limitHeight = 100;
	$smallIpadjpgFile=$stuffFilePath . "T". $productId . "_ipad_s.jpg";
	$limitHeight = $height * ($limitWidth / $width);
	
	$isConvert = "no";
	exec("convert -resize $limitWidth x $limitHeight -colorspace sRGB -transparent white -trim $stuffFile $smallIpadjpgFile");
	
	if(file_exists($smallIpadjpgFile))
	{
		$isConvert = "yes";
	}
	
	echo $isConvert;
	
?>
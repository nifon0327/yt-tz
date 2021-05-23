<?php
	
	$pdfFile = "120987.pdf";
	$jpgFileIpad = "120987_ipad.jpg";
	
	exec("convert -colorspace RGB -quality 80 -density 300 -trim $pdfFile $jpgFileIpad");
	
	$size = getimagesize($jpgFileIpad);
	$width = $size[0];
	$height = $size[1];
	
	$limitWidth = 5000;
	$limitHeight = 0;
	
	if($width > $limitWidth)
	{
		$limitHeight = $height * ($limitWidth / $width);
	}
	else
	{
		$limitWidth = $width;
		$limitHeight = $height;
	}
	
	exec("convert -resize $limitWidth x $limitHeight -trim $jpgFileIpad $jpgFileIpad");
			
?>
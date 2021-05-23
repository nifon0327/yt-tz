<?php
//$Code='48018080235122';
if(strlen($Code) == 14 ){
	$Code=substr($Code,0,13);
}
else{
	if ( strlen($Code) == 13 ) $Code=substr($Code,0,12);
	$Code='4'.$Code;
}

$ncode ='0'.$Code;
$even = 0; $odd = 0;
for ($x=0;$x<14;$x++)
{
if ($x % 2) { $odd += $ncode[$x]; } else { $even += $ncode[$x]; }
}
$sumValue=$odd*3 +$even;
$lastCode=substr($sumValue, -1,1);
if($lastCode==0)$lastCode=substr($sumValue, -2,1);//add yang (???)为0的话多出一位
$lastCode= $lastCode==0?0:10-$lastCode;
$text=$Code.$lastCode;

require_once('class/BCGFontFile.php');
require_once('class/BCGColor.php');
require_once('class/BCGDrawing.php');

// Including the barcode technology
require_once('class/BCGi25.barcode.php');

// Loading Font
$font = new BCGFontFile('font/Arial.ttf', 12);

// Don't forget to sanitize user inputs
//$text = isset($_GET['text']) ? $_GET['text'] : 'HELLO';

// The arguments are R, G, B for color.
$color_black = new BCGColor(0, 0, 0);
$color_white = new BCGColor(255, 255, 255);

$drawException = null;
try {
	$code = new BCGi25();
	$code->setScale(2); // Resolution
	$code->setThickness(30); // Thickness
	$code->setForegroundColor($color_black); // Color of bars
	$code->setBackgroundColor($color_white); // Color of spaces
	$code->setFont($font); // Font (or 0)
	$code->parse($text); // Text
} catch(Exception $exception) {
	$drawException = $exception;
}

/* Here is the list of the arguments
1 - Filename (empty : display on screen)
2 - Background color */
$drawing = new BCGDrawing('', $color_white);
if($drawException) {
	$drawing->drawException($drawException);
} else {
	$drawing->setBarcode($code);
	$drawing->draw();
}

// Header that says it is an image (remove it if you save the barcode to a file)
header('Content-Type: image/png');
header('Content-Disposition: inline; filename="barcode.png"');

// Draw (or save) the image into PNG format.
$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
?>
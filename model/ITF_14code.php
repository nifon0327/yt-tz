<?php 
//二合一已更新
/*

ITF14Code($Code,$lw,$hi);
*/
$classpath= "/../plugins/barcodegen/class/";
 require(dirname(__FILE__).$classpath);
$text='8018080202230';
require_once('BCGFontFile.php');
require_once('BCGColor.php');
require_once('BCGDrawing.php');

// Including the barcode technology
require_once('BCGi25.barcode.php');

// Loading Font
$font = new BCGFontFile('font/Arial.ttf', 18);

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

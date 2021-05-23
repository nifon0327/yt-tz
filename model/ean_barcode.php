<?php
// Including all required classes
$barcodePath='../plugins/barcodegen/';
require_once($barcodePath .'class/BCGFontFile.php');
require_once($barcodePath .'class/BCGColor.php');
require_once($barcodePath .'class/BCGDrawing.php');

//$Codebar = $_GET['Codebar'];

// Including the barcode technology
require_once($barcodePath .'class/'. $Codebar .'.barcode.php');

// Loading Font
$font = new BCGFontFile($barcodePath .'/font/Arial.ttf', 16);

// Don't forget to sanitize user inputs
//$text = isset($_GET['Code']) ? $_GET['Code'] : 'HELLO';

// The arguments are R, G, B for color.
$color_black = new BCGColor(0, 0, 0);
$color_white = new BCGColor(255, 255, 255);

$drawException = null;
try {
    $code = new $Codebar();
	$code->setScale(2); // Resolution
	$code->setThickness(25); // Thickness
	$code->setForegroundColor($color_black); // Color of bars
	$code->setBackgroundColor($color_white); // Color of spaces
	$code->setFont($font); // Font (or 0)
	$code->parse($Codetext); 
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
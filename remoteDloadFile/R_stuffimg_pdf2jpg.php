<?php 

//$Log_Funtion="PDF图片上传";
//$Date=date("Y-m-d");
$returnstr="";  //(远程更新)
switch ($doAction) {
	case "stuffpdf2jpg";
		$stuffFilePath="../download/stufffile/";
		$pdfFile=$stuffFilePath . $StuffId .".pdf";
		$jpgFile=$stuffFilePath . $StuffId . "_s.jpg";
		if (!file_exists($jpgFile)){
			$returnstr.="NoFind|-1|";
		   if (file_exists($pdfFile)){
			/*  $nmw =NewMagickWand();
			  MagickReadImage($nmw,$pdfFile);
			  MagickWriteImage($nmw,$jpgFile);
			  DestroyMagickWand($nmw);*/
		   
			  exec("$execImageMagick -colorspace sRGB -transparent white -trim $pdfFile $jpgFile");
			 /* if (file_exists($jpgFile)){
				echo $jpgFile . " File convert jpg success!";
			  }*/
		   }
		}
		else {
			$returnstr.="Find|1|";	
		}
	 break;	
}
	
echo "^$returnstr"; 


?>
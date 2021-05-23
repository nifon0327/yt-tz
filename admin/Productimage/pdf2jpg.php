<?php
if ($donwloadFileIP!="") {

			$url=$donwloadFileIP."/remoteDloadFile/R_stuffimg_pdf2jpg.php?Login_P_Number=$Login_P_Number&StuffId=$StuffId&doAction=stuffpdf2jpg";
			$str=file_get_contents(iconv("UTF-8","GB2312",$url));				//ע�⣺Ҫ����ַתΪGB2312�������ȡʧ��
			$content=$str;
			$start="^";
			$strP=strpos($content,$start);
			$tempStr=substr($content,$strP+1);
			if ( $tempStr!="") {
				$Field=explode("|",$tempStr);
				$Fisexists=$Field[1];
			}
			$tempS=$Field[1];


}
else {
	$FilePath="../../download/stufffile/";
	$pdfFile=$FilePath . $StuffId .".pdf";
	$jpgFile=$FilePath . $StuffId . "_s.jpg";
	if (!file_exists($jpgFile)){
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
}

?>
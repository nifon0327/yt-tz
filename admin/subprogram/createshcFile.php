<?php    
//创建切割SHC格式文件
$shcFileName=$ProductId . ".shc";
$newshcFile=$entPath . $shcFileName;
$newFile= fopen($newshcFile,'a+');//创建文件
if ($newFile){
	$content="[ARTICLE]\r\n";
	$content.="Name=" . $ProductId ."\r\n\r\n";
	$content.="[SHAPES]\r\n";
	foreach ($FileNames as $fname) {
	   $content.="Shape0=" .$fname ."\r\n";
	}
	foreach ($FileNames as $fname) {
	   $content.="[" .$fname ."]\r\n";
	   $content.="QuantityLeft=1\r\nQuantityRight=1\r\nSize=*\r\nMaterial=*\r\nDivisore=1\r\nFitting=*\r\n";
	   $content.="FullName=" . $fname ."\r\n\r\n";
	}
	//打开base.shc源文件
	$baseFile=$FilePath . "base.shc";
	$shcFile=fopen($baseFile,'r+');
	if ($shcFile){
	    while(!feof($shcFile)){
            $content.=fgets($shcFile);
		}
	    fclose($shcFile);	
	}else{
	  $Log.="读入切割图档base.shc文件失败！<br>";	
	}
	fwrite($newFile,$content);
    fclose($newFile);
	$Log.="$shcFileName 文件创建成功.<br>";
  }
else{
    $Log.="$shcFileName 文件创建失败.<br>";	
}
?>

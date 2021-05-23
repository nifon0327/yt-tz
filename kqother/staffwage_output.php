<?php 
//二合一已更新
$datetime=date("YmdHis"); 
$file="../download/cwxz/".$datetime.".gbpt"; 
if(!file_exists($file)){   
	$fp_write = fopen($file, "w");
	fputs($fp_write, $sheetList);
	fclose($fp_write); 
	}
?>
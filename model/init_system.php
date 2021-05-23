<?php
header("Content-Type: text/html; charset=utf-8");

if ($doAction!=1)  exit;

ob_end_clean();     //在循环输出前，要关闭输出缓冲区   
echo str_pad('',256);   
			  
include "../model/modelfunction.php";
//建立下载文件目录：
$downDirArray=array(   'orderflow',
										'bomflow',
										'ptflow',
										'stufffile',
										'tmp_stuffpdf',
										'upload',
										'tmp_standarddrawing',
										'standarddrawing',
										'PurchasePDF',
										'pipdf'
								);

foreach($downDirArray as $dirName)
{
      $FilePath="../download/$dirName/";
      if(!file_exists($FilePath)){
	         makedir($FilePath);
	         echo '创建下载文件目录: ' . $FilePath . '  成功!<br>';
	         flush();    //刷新输出缓冲   
     }
}

ob_end_flush();
?>
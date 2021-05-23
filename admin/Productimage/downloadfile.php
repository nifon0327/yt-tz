<?php    
switch($Type){
	case 1:
	   $file_dir='../../download/teststandard';
	 break;
	 case 2:
	   $file_dir='../../download/QCstandard';
	 break;
	 case 3:
	   $file_dir='../../download/errorcase';
	 break;
}
downloadfile($file_dir,$file_name);
function downloadfile($file_dir,$file_name){        //下载FTP中的一个文件     （已测试成功） 
        if (!file_exists($file_dir."/".$file_name)){ //检查文件是否存在
         return false;
         exit;
        }else{
         $file = fopen($file_dir."/".$file_name,"r"); // 打开文件
         // 输入文件标签
         header('Content-Encoding: none');
         header("Content-type: application/octet-stream");
         header("Accept-Ranges: bytes");
         header("Accept-Length: ".filesize($file_dir."/".$file_name));
         header( 'Content-Transfer-Encoding: binary' );
         header("Content-Disposition: attachment; filename=" .$file_name); //以真实文件名提供给浏览器下载 
         header('Pragma: no-cache');
         header('Expires: 0');
         //输出文件内容
         echo fread($file,filesize($file_dir."/".$file_name));
         fclose($file);
         exit;
        }
      } 

?>
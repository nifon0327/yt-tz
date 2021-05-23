<?php   
function deldir($dir) {
    $dh=opendir($dir);
    while ($file=readdir($dh)) {
       if($file!="." && $file!="..") {
           $fullpath=$dir."/".$file;
           if(!is_dir($fullpath)) {
                unlink($fullpath);
            } 
	       else{
                 deldir($fullpath);
           }
       }
   }
  closedir($dh);
  if(rmdir($dir)) {return true;} else {return false;} 
}

function finddirfile($dir,$ext,$type){
	$files = array();
	$dh=opendir($dir);
	while ($file=readdir($dh)) {
       if($file == '.') continue;
       if($file == '..') continue;
	   $filename=basename($file);
       $fileinfo = explode('.',$filename);
	   if ($fileinfo[1]==$ext){
		   if ($type==1){
		      $files[] =$fileinfo[0];
			 }
		   else{
			 $files[] =$filename;  
		   } 
	    } 
    }
    closedir($dh);
	if (count($files)==0) {return false;} else {return $files;}
}
?>

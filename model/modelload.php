<?php 
include "loadtype.php";
$Dir=anmaDecrypt($d,$k);
$File=anmaDecrypt($f,$k);
$FileName ="../download/".$Dir."/".$File;
$Download=new download('php,exe,html',false); 
if(!$Download->downloadfile($FileName)){ 
	echo $Download->geterrormsg();
 }
?>
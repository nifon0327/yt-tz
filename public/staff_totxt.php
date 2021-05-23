<?php 
//已更新电信---yang 20120801

include "../model/modelhead.php";
 
$sheetResult = mysql_query("SELECT  Mail FROM $DataPublic.staffmain WHERE Estate='1' AND cSign='$Login_cSign' AND Mail!=''",$link_id);
if($sheetRows = mysql_fetch_array($sheetResult)){
    $i=1;
	$Path="../download/email/";
	if(!file_exists($Path)){
			    makedir($Path);
				}
	$txtPath=$Path."email.txt";
	if(file_exists($txtPath)){
	  unlink($txtPath);
	}
    do{
		$Mail=$sheetRows["Mail"];
		$Mail=$Mail.";";
		$handle=fopen($txtPath,"a"); 
		fwrite($handle,strip_tags($Mail)); 
		fclose($handle);
        }while($sheetRows = mysql_fetch_array($sheetResult));   
		$Log="员工邮件导出成功";   
}
else{
$Log="<div class='redB'>员工邮件导出成功</div>";
}

if (file_exists($txtPath)) {     
header('Content-Description: File Transfer');     
header('Content-Type: application/octet-stream');     
header('Content-Disposition: attachment; filename='.basename($txtPath));     
header('Content-Transfer-Encoding: binary');     
header('Expires: 0');    
 header('Cache-Control: must-revalidate, post-check=0, pre-check=0');     
 header('Pragma: public');     
 header('Content-Length: ' . filesize($txtPath));     
 ob_clean();     
 flush();     
 readfile($txtPath);     
 exit; } 


$fromWebPage=$funFrom."_read";
include "../model/logpage.php";

?>

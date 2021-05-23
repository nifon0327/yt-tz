<?php 
include "../model/modelhead.php";
$FilePath="../download/qcbadpicture/".$ImgName;
$delSql="update $DataIn.qc_badrecordsheet  set Picture=0  WHERE Id='$Bid'";
$result1 = mysql_query($delSql);
if($result1){
      echo "Y";
      if(file_exists($FilePath)){
	    unlink($FilePath);
	   }
}
else echo "N";
?>
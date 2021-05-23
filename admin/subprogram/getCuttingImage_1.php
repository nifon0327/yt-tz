<?php      //输出切割样版图
    $checkResult=mysql_query("SELECT Id,Type,Estate,Picture  FROM $DataIn.diecutimg WHERE ProductId='$ProductId' AND StuffId='$StuffId' AND Type='1'",$link_id);  
   if($checkRow = mysql_fetch_array($checkResult)){
	 $imgEstate=$checkRow["Estate"];
	 $ImageName=$checkRow["Picture"];
	 if ($imgEstate==1){
          $ImageFile="../download/diecutimg/" . $ImageName; 
		  $d1=anmaIn("download/diecutimg/",$SinkOrder,$motherSTR);
		  $f1=anmaIn($ImageName,$SinkOrder,$motherSTR);
          $StuffCname="<div><a  href='openorload.php?d=$d1&f=$f1&Type=cut' target='_blank' title='点击下载样板图'>$StuffCname</a></div>";
	      }
      }
?>
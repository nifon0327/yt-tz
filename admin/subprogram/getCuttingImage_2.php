<?php     	//输出切割图档
   $cutimgFile="&nbsp;";$cutPicture="&nbsp;";
   $checkResult=mysql_query("SELECT Id,Type,Estate,Picture FROM $DataIn.diecutimg WHERE ProductId='$ProductId'",$link_id);
   if($checkRow = mysql_fetch_array($checkResult)){
	  do{
	     $imgEstate=$checkRow["Estate"];
		 $imgType=$checkRow["Type"];
		 $imgFile=$checkRow["Picture"];
	     if ($imgEstate==1){
		   switch($imgType){
			case 1:
			  //$ImageName="../download/diecutimg/" . $imgFile; 
		      $d1=anmaIn("download/diecutimg/",$SinkOrder,$motherSTR);
		      $f1=anmaIn($imgFile,$SinkOrder,$motherSTR);
			  $cutPicture="<div><a  href='openorload.php?d=$d1&f=$f1&Type=cut' target='_blank'>查  看</a></div>"; 
			  //$cutPicture="<div><a  href='openorload.php?d=$d1&f=$f1&Type=cut' target='_blank'><img src='../images/Acrobat.png' width='30px' style='border:none;' title='查看样图'/></a></div>"; 
			  break;
			case 2:
	           $ServerIp=$_SERVER["SERVER_ADDR"];
               $cutimgFile="<a href='\\\\$ServerIp\\diecutfile\\$ProductId' target='_blank'>网络目录</a><br>";
		       $cutimgFile.="<a href='../download/diecutfile/$imgFile'>下 载</a>";
			   break;
		   }
	     } 
	   }while($checkRow = mysql_fetch_array($checkResult));
   }

?>
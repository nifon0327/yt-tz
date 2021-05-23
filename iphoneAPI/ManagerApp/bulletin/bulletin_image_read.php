<?php 
//读取通知图片
   $mySql = "SELECT P.Picture FROM  $DataPublic.msg1_picture P  WHERE P.Mid='$Id' ";
  $myResult = mysql_query($mySql);
  while($myRow = mysql_fetch_array($myResult))
	{
	        $Picture=$myRow["Picture"]==""?"0":"../../download/msgfile/" . $myRow["Picture"];
	         echo "<img src='$Picture' width='100%'/><br>";
    }
?>
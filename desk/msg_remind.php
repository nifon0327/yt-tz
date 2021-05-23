<?php   
//µçÐÅ-zxq 2012-08-01
include "../basic/chksession.php";
include "../basic/parameter.inc";
$mySql="SELECT O.Id,O.Content FROM $DataPublic.msg4_remind O WHERE  O.Estate=1 ORDER BY O.Date DESC";
$myResult = mysql_query($mySql,$link_id);
$msgContent="";
if($myRow = mysql_fetch_array($myResult)){
      $i=1;
    do{
       $Content=nl2br($myRow["Content"]);
       if ($i==1){
          $msgContent=$Content;
          $i++;
       }else{
          $msgContent.="|" . $Content;
       }
    }while ($myRow = mysql_fetch_array($myResult));
}
echo $msgContent;
?>

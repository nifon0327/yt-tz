<?php   //REACH法规图电信---yang 20120801
$ReachImage="";
$Reach_result = mysql_query("SELECT 0 as Id,D.Picture FROM $DataIn.stuffreachlink Q  
                   LEFT JOIN $DataIn.stuffreach D ON Q.QcId=D.Id WHERE Q.StuffId='$StuffId'  
               UNION
                   SELECT 1 as Id,Picture FROM   $DataIn.stuffreach WHERE   TypeId='$TypeId' AND IsType=1 
                   order by Id LIMIT 1",$link_id);
if ($ReachimgRow= mysql_fetch_array($Reach_result)){
       $ReachImage=$ReachimgRow["Picture"];
       $ReachImage=anmaIn($ReachImage,$SinkOrder,$motherSTR);
       $Dir="download/stuffreach/";
       $Dir=anmaIn($Dir,$SinkOrder,$motherSTR);			
       $ReachImage="<a href=\"../admin/openorload.php?d=$Dir&f=$ReachImage&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>view</a>"; 
}
$ReachImage=$ReachImage==""?"&nbsp;":$ReachImage;
?>
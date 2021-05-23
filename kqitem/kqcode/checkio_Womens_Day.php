<?php 
//三八妇女节 女性员工放半天假
//电信-EWEN
switch($WomensDay){
    case 1:
        if (date("m-d",strtotime($CheckDate))=="03-08"){ 
          $womenResult = mysql_query("SELECT Id FROM $DataPublic.staffsheet WHERE Number=$Number AND Sex=0 LIMIT 1",$link_id);
          if($womenRow=mysql_fetch_array($womenResult)){
              $qjHours=$qjHours-4; 
            //  $WomensTime-=$qjHours;
              $qjHours=$qjHours<0?0:$qjHours;
            }
       }
     break;       
     case 2:
      if (date("m-d",strtotime($CheckDate))=="03-08"){ 
          $womenResult = mysql_query("SELECT Id FROM $DataPublic.staffsheet WHERE Number=$Number AND Sex=0 LIMIT 1",$link_id);
          if($womenRow=mysql_fetch_array($womenResult)){
            $GTime=4;
            $QQTime=$QQTime-4;
            $QQTime=$QQTime<0?0:$QQTime;
            $InLates=$WorkTime==4?0:$InLates;
         }   
      }
     break;
}  
?>
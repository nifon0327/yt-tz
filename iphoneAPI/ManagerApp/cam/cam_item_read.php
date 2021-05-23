<?php 
//读取公司监控信息
     $postion=array(
           array( "48","48"),
           array( "47","47")
          // array( "bsd","Bostar")
         );//array( "mc","伟信达"), array( "cf","BHS"),
    
    $SearchRows=" AND (C.Floor>1 OR C.Id IN(50,51,69,70,87)) "; 
    for ($i=0;$i<count($postion);$i++){
          $f=$postion[$i][0];
          $p=$postion[$i][1];
          $n=0;
          $mySql ="SELECT Id,Floor,Info FROM $DataPublic.ot2_cam C WHERE C.From='$f' $SearchRows AND C.Estate=1 AND C.Name<>'NULL'  ORDER BY C.Order,C.Id "; 
          $myResult = mysql_query($mySql);
            while($myRow =mysql_fetch_array($myResult))
            {
               $Id=$myRow["Id"];
               $Floor=$myRow["Floor"];
               $Info=$myRow["Info"];
 
               //$Info=strlen($Info)==1?"$Info" . "门":$Info ;
                $jsonArray[]=array("Id"=>"$Id","Title"=>"$p-$Floor $Info");
            }
     }
     
    $SearchRows=" AND C.Floor=1 AND C.Id IN(52,53,64,71,72) "; 
    for ($i=0;$i<count($postion);$i++){
          $f=$postion[$i][0];
          $p=$postion[$i][1];
          $n=0;
          $mySql ="SELECT Id,Floor,Info FROM $DataPublic.ot2_cam C WHERE C.From='$f' $SearchRows AND C.Estate=1 AND C.Name<>'NULL'  ORDER BY C.Order,C.Id "; 
          $myResult = mysql_query($mySql);
            while($myRow =mysql_fetch_array($myResult))
            {
               $Id=$myRow["Id"];
               $Floor=$myRow["Floor"];
               $Info=$myRow["Info"];
 
               //$Info=strlen($Info)==1?"$Info" . "门":$Info ;
                $jsonArray[]=array("Id"=>"$Id","Title"=>"$p-$Floor $Info");
            }
     }
     
        $SearchRows=" AND C.Floor=1  AND  C.Id NOT IN(50,51,69,70,52,53,64,71,72,87) "; 
    for ($i=0;$i<count($postion);$i++){
          $f=$postion[$i][0];
          $p=$postion[$i][1];
          $n=0;
          $mySql ="SELECT Id,Floor,Info FROM $DataPublic.ot2_cam C WHERE C.From='$f' $SearchRows AND C.Estate=1 AND C.Name<>'NULL'  ORDER BY C.Order,C.Id "; 
          $myResult = mysql_query($mySql);
            while($myRow =mysql_fetch_array($myResult))
            {
               $Id=$myRow["Id"];
               $Floor=$myRow["Floor"];
               $Info=$myRow["Info"];
 
               //$Info=strlen($Info)==1?"$Info" . "门":$Info ;
                $jsonArray[]=array("Id"=>"$Id","Title"=>"$p-$Floor $Info");
            }
     }
     
?>
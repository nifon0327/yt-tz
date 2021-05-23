<?php 
//读取公司监控信息
     $postion=array(
           array( "48","48"),
           array( "47","47")
          // array( "bsd","Bostar")
         );//array( "mc","伟信达"), array( "cf","BHS"),
     
    for ($i=0;$i<count($postion);$i++){
          $f=$postion[$i][0];
          $p=$postion[$i][1];
          $n=0;
          $mySql ="SELECT Id,Floor,Info FROM $DataPublic.ot2_cam C WHERE C.From='$f' AND C.Estate=1 ORDER BY C.Order,C.Id "; 
          $myResult = mysql_query($mySql);
            while($myRow =mysql_fetch_array($myResult))
            {
               $Id=$myRow["Id"];
               $Floor=$myRow["Floor"];
               $Info=$myRow["Info"];
               $Floor.=strlen($Info)>1?" $Info":"";
                $jsonArray[]=array("$f","$p-$Floor","$Id");
                $n++;
            }
            if ($n%2==1)$jsonArray[]=array("","","");
     }
?>
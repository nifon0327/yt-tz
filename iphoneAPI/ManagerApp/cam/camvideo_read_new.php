<?php 
//读取公司监控信息
   $mySql ="SELECT * FROM $DataPublic.ot2_cam C WHERE  C.Id='$checkId'  AND C.Estate=1";
    //echo $mySql;
    $myResult = mysql_query($mySql,$link_id);
    echo"<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><meta name='viewport' content='minimum-scale=0.4; maximum-scale=5;  initial-scale=0.50; user-scaleable=yes;'>
      </head><body bgcolor='#DDDDDD'>";
    //echo "<center><div style='height:6px;'>&nbsp;</div>";
    while($myRow = mysql_fetch_assoc($myResult))
    {
        $f=$myRow["From"];
  
       $IP=$myRow["OutIP"];

        $Port=$myRow["Port"];
        $Name=$myRow["Name"]==""?$myRow["Floor"] . $myRow["Info"]:$myRow["Name"];
        $Name= $Name=="NULL"?"": $Name;
        $Params=$myRow["Params"];
			if (stripos($Params, "subcam/")===false){
				$src="http://$IP:$Port/$Params";
			}
			else{
				$src="http://www.ashcloud.com/desk/".$Params;
			}

     echo "<iframe frameborder=0 width=636 height=400 marginheight=0 marginwidth=0 scrolling=no src='$src' style='margin-left:-6px;margin-top:-5px;'></iframe>";
     // echo "<center><div style='height:6px;'>&nbsp;</div>";
    }
    echo "</center></body></html>";
?>
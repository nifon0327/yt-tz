<?php 
//读取公司监控信息
    $f=$idArray[0];
    switch(count($idArray)){
	    case 3:
	         $SearchRows="AND (C.Id='$idArray[1]' OR  C.Id='$idArray[2]') ";
	      break;
	   default:
	         $SearchRows=" AND C.Id='$idArray[1]'";
	      break;
    }

$SearchRows.=" AND C.Estate=1";

    $mySql ="SELECT * FROM $DataPublic.ot2_cam C WHERE 1 $SearchRows";
    //echo $mySql;
    $myResult = mysql_query($mySql,$link_id);
    echo"<html><head>  <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><meta name='viewport' content='minimum-scale=0.4; maximum-scale=5;  initial-scale=0.48; user-scaleable=yes;'>
      </head><body bgcolor='#DDDDDD'>";
    echo "<center><div style='height:6px;'>&nbsp;</div>";
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
				$src="http://www.middlecloud.com/desk/".$Params;
			}

     echo "<iframe frameborder=0 width=640 height=400 marginheight=0 marginwidth=0 scrolling=no src='$src'></iframe>";
     echo "<div style='height:16px;'>$Name</div>";
    }
    echo "</center></body></html>";
?>
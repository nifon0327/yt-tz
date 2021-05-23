<?php 
//更新App版本号
	include "../basic/parameter.inc";
        
                $postInfo="DailyManagement";
                $mySql="UPDATE $DataPublic.app_sheet SET  version='1.0.8'  WHERE appname='$postInfo'";
	$myResult = mysql_query($mySql,$link_id);
                if ($myResult && mysql_affected_rows()>0)
                {
                    echo "update success!";
                }
	
?>
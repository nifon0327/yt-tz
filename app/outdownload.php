<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php   
    include "../basic/parameter.inc";

    $today=date("Ymd");

    $mySql="SELECT A.appname,A.link  FROM $DataPublic.app_sheet A WHERE A.cer='$Id' limit 1";
    $myResult = mysql_query($mySql,$link_id);
    if($myRow = mysql_fetch_assoc($myResult))
    {
        $appname=$myRow["appname"];
        if ($appname=='AshCloudApp'){
             $newCer=md5($appname . $today . $DomainCerKey);
        }
        else{
	         $newCer=md5($appname . $DomainCerKey);
        }
        
       if ($Id==$newCer){
	        $link=$myRow["link"];
   ?>
    <html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<link rel="stylesheet" href="app.css" />
		<title>download APP</title>
	</head>
    <script language="javascript">
             location.href='itms-services://?action=download-manifest&url=<?php    echo $link?>';
    </script>
          <body>

         </body>
</html>
 <?php 
    }
    else {
?>	
       <html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<link rel="stylesheet" href="app.css" />
		<title>download APP</title>
	</head>
        <body>
           <p>下载地址失效！</p>
        </body>
     </html>
<?php      
     }
  }
?>

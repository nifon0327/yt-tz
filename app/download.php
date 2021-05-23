<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php   
     include "../basic/parameter.inc";
    $mySql="SELECT A.link  FROM $DataPublic.app_sheet A WHERE A.appname='$name' limit 1";
    $myResult = mysql_query($mySql);
    if($myRow = mysql_fetch_assoc($myResult))
    {
        $link=$myRow["link"];
    }
?>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<link rel="stylesheet" href="app.css" />
		<title>App应用下载</title>
	</head>
    <script language="javascript">
             location.href='itms-services://?action=download-manifest&url=<?php    echo $link?>';
    </script>
          <body>

         </body>
</html>
<?php   
//电信-zxq 2012-08-01
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<?php   
include "../model/characterset.php";//二合一已更新
?>
<title><?php    echo $SubCompany?> CLIENT DATA</title>
</head>

<frameset rows="80,*" frameborder="NO" border="0" framespacing="0">
  <frame src="clientdatatop.php" name="topFrame" scrolling="NO" noresize>
  <frame src="clientdatamain.php" name="mainFrame">
</frameset>
<noframes><body>
</body></noframes>
</html>

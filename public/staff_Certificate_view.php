<?php   	
include "../basic/chksession.php";
include "../basic/parameter.inc";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>证书查看</title>
</head>
<body>
<form >
<?php   
$CertificateResult=mysql_query("SELECT Picture FROM $DataPublic.staff_Certificate WHERE Number=$Number",$link_id);
while($CertificateRow=mysql_fetch_array($CertificateResult)){
       $Picture=$CertificateRow["Picture"];
        //echo"<img src='../download/Certificate/$Picture' width='860px' height='500px'><p>&nbsp;</p>";
		echo"<img src='../download/Certificate/$Picture' ><p>&nbsp;</p>";
    }
?>
</form>
</body>
</html>
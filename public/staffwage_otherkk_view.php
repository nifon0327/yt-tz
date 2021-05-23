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
$CertificateResult=mysql_query("SELECT Picture FROM $DataPublic.staffwage_otherkk WHERE Number=$Number AND Month='$Month'",$link_id);
while($CertificateRow=mysql_fetch_array($CertificateResult)){
       $Picture=$CertificateRow["Picture"];
		echo"<img src='../download/otherkk/$Picture' ><p>&nbsp;</p>";
    }
?>
</form>
</body>
</html>
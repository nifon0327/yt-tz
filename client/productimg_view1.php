<html>
<head>
<?php   
//二合一已更新
include "../model/characterset.php";
$img="../download/productfile/" .$Filename;
$img_info = getimagesize($img); 
	    $imgWidth=$img_info[0];
		$imgHeight=$img_info[1];
?>
<title></title>
</head>

<body>
<img src="../download/productfile/<?php    echo $Filename?>" name="img" width="<?php    echo $imgWidth?>" height="1200" border="0" id="img"></td>
</body>
</html>

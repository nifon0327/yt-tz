<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";

$productimg_Result = mysql_query("SELECT * FROM $DataIn.productimg WHERE ProductId='$ProductId'",$link_id);
?>
<style type="text/css">
<!--
body {background-color: #E3E3E3;}
-->
</style></head>
<body>
<form name="form1" method="post" action="">

<table width="1800" height="1389" border="1" align="center" bgcolor="#FFFFFF">
 
  <tr>
    <td height="20" colspan="2">Page:
	<?php   
	$i=1;
	if ($productimg_Row = mysql_fetch_array($productimg_Result)) {
		do{
			echo "<a href='productimg_view1.php?Filename=$productimg_Row[Picture]' target='picture'>[".$i."]</a>&nbsp;&nbsp;";
			if($i==1){
				$Fristname=$productimg_Row["Picture"];
				}
			$i++;
			}while ($productimg_Row = mysql_fetch_array($productimg_Result));		
		}
	?>
	</td>
  </tr>
  <tr valign="top">
    <td height="1317" colspan="2">
	<iframe name="picture" frameborder=0 marginheight=0 marginwidth=0 scrolling=no width='1650' height='1200'  src='productimg_view1.php?Filename=<?php    echo $Fristname?>'></iframe>
  </tr>
</table>
</form>
</body>
</html>
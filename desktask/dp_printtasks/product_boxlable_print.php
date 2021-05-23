<?php   
//µçÐÅ-ZX  2012-08-01
include "../../basic/parameter.inc";

$checkRow=mysql_fetch_array(mysql_query("SELECT cName,eCode,Code FROM  $DataIn.productdata WHERE ProductId='$p' ORDER BY Id LIMIT 1",$link_id));
$cName=$checkRow["cName"];
$cName=iconv("UTF-8","GB2312",$cName);
$eCode=$checkRow["eCode"];
$BoxCode=$checkRow["Code"];
$Code=substr($BoxCode,-4);

if($Type==1){
	$Field=explode("|",$BoxCode);
	$eCodeArray=explode("(",str_replace(")","",$eCode));
	$BoxCodeText_S=$eCodeArray[1];$BoxCodeText_L=$eCodeArray[0];
	$BoxCodeNum=$Field[1];
	$BoxCodeText=$BoxCodeText_S==""?$BoxCodeText_L:$BoxCodeText_S.$BoxCodeText_L;
	$s=$s==""?7:$s;
	$BoxCodeNum1=substr($BoxCodeNum,0,1);
	$BoxCodeNum2=substr($BoxCodeNum,1,6);
	$BoxCodeNum3=substr($BoxCodeNum,-6);
?><style type="text/css">
<!--
body,td,th {
	font-family: Arial;
	font-size: <?php    echo $s?>pt;
	line-height: 10px;
	font-weight: bold;
}
body {
	margin-left: 4px;
	margin-top: 2px;
}
#BackD{
	width:280px;
	margin:0;
	padding:0;
	}
#BackD #backL{
	width:130px;
	height:60px;
	margin:0;
	padding:0;
	MARGIN-RIGHT: 2px; 
	float:left;
	}
#backL #CodeText{
	line-height: 11px;
	MARGIN-LEFT:10px;
	MARGIN-BOTTOM:2px;
	}
#backL #CodeImg{
	}
#backL #CodeNum1{
	margin-left: 1px;
	float:left;
	margin-top: -18px;
	font-size: 9pt;
	}
#backL #CodeNum2{
	margin-left: 10px;
	float:left;
	margin-top: -13px;
	font-size: 9pt;
	}
#backL #CodeNum3{
	margin-left: 70px;
	float:left;
	margin-top: -13px;
	font-size: 9pt;
	}

-->
</style>
<script type="text/javascript" language="javascript">
//<![CDATA[
// Do print the page
window.onload = function()
{
    if (typeof(window.print) != 'undefined') {
        window.print();
    }
}
//]]>
</script>
<div id="BackD">
	<div id="backL">
		<div id="CodeText"><?php    echo $BoxCodeText_S?><br><?php    echo $BoxCodeText_L?></div>
		<div id="CodeImg"><img width='130' height='36'  src='Print_AutoCode.php?Code=<?php    echo $BoxCodeNum?>&lw=1&hi=25'></img></div>
		<div id="CodeNum1"><?php    echo $BoxCodeNum1?></div><div id="CodeNum2"><?php    echo $BoxCodeNum2?></div><div id="CodeNum3"><?php    echo $BoxCodeNum3?></div>
	</div>
	<div id="backL">
		<div id="CodeText"><?php    echo $BoxCodeText_S?><br><?php    echo $BoxCodeText_L?></div>
		<div id="CodeImg"><img width='130' height='36'  src='Print_AutoCode.php?Code=<?php    echo $BoxCodeNum?>&lw=1&hi=25'></img></div>
		<div id="CodeNum1"><?php    echo $BoxCodeNum1?></div><div id="CodeNum2"><?php    echo $BoxCodeNum2?></div><div id="CodeNum3"><?php    echo $BoxCodeNum3?></div>
	</div>
	<div id="backL">
		<div id="CodeText"><?php    echo $BoxCodeText_S?><br><?php    echo $BoxCodeText_L?></div>
		<div id="CodeImg"><img width='130' height='36'  src='Print_AutoCode.php?Code=<?php    echo $BoxCodeNum?>&lw=1&hi=25'></img></div>
		<div id="CodeNum1"><?php    echo $BoxCodeNum1?></div><div id="CodeNum2"><?php    echo $BoxCodeNum2?></div><div id="CodeNum3"><?php    echo $BoxCodeNum3?></div>
	</div>
		<div id="backL">
		<div id="CodeText"><?php    echo $BoxCodeText_S?><br><?php    echo $BoxCodeText_L?></div>
		<div id="CodeImg"><img width='130' height='36'  src='Print_AutoCode.php?Code=<?php    echo $BoxCodeNum?>&lw=1&hi=25'></img></div>
		<div id="CodeNum1"><?php    echo $BoxCodeNum1?></div><div id="CodeNum2"><?php    echo $BoxCodeNum2?></div><div id="CodeNum3"><?php    echo $BoxCodeNum3?></div>
	</div>
</div>
<?php   
}
else{
	
?>

<style type="text/css">
<!--
body,td,th {
	font-family: "ËÎÌå";
	font-size: 7pt;
	font-weight: bold;
	
}
body {
	margin-left: 4px;
}
-->
</style>
<script type="text/javascript" language="javascript">
//<![CDATA[
// Do print the page
window.onload = function()
{
    if (typeof(window.print) != 'undefined') {
        window.print();
    }
}
//]]>
</script>

<table border="0" cellpadding="0" cellspacing="2" valign="middle" style="height:60px;TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
	<tr>
    	<td style="width:130px;"><?php    echo $cName?><br><?php    echo $eCode?>
	  		<div align="center"><?php    echo $Code?></div></td>
		<td valign="middle" style="width:130px"><?php    echo $cName?><br><?php    echo $eCode?>
		  <div align="center"><?php    echo $Code?></div>
	  </td>
	</tr>
</table>
<table border="0" cellpadding="0" cellspacing="2" valign="middle" style="height:60px;TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
	<tr>
    	<td  style="width:130px;"><?php    echo $cName?><br><?php    echo $eCode?>
	  		<div align="center"><?php    echo $Code?></div></td>
		<td valign="middle" style="width:130px"><?php    echo $cName?><br><?php    echo $eCode?>
		  <div align="center"><?php    echo $Code?></div>
	  </td>
	</tr>
</table>


<?php   
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv=refresh content="1800">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<TITLE>公司摄像头监控</TITLE>
</head>
<link rel='stylesheet' href='../model/tl/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>

<style type="text/css">
<!--
.style1 {
	font-size: 150px;
	color: #0000FF;
	font-family: Verdana, Arial, Helvetica, sans-serif;
}
.style2 {
	font-size: 50px;
	color: #0000FF;
	font-family: "黑体";
}
-->
</style>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>
<BODY style="background:#000000;scroll:no;">
<div id="Layer1" style="position:absolute; left:47px; top:0px; z-index:200;">
 <table  align="center" cellspacing="0" border="0" style='margin-top:110px;'>

<?php   
    include "../basic/parameter.inc";			//数据库连接
    $f=48;$Cles=2;$IP="127.0.0.1";$Width=1080;
    
    switch($Floor){
	   case "4A": $Id="41,50";  break;
	   case "4B": $Id="42,51";  break;
	   case "3A": $Id="50,59";  break;
	   case "3B": $Id="51,60";  break;
	   //default: $Id="41,50";  break;
   }
   
  $checkCamSql=mysql_query("SELECT * FROM $DataPublic.ot2_cam C WHERE  C.From='$f' AND C.Id IN ($Id) ORDER BY C.Order,C.Id",$link_id);
   if($checkCamRow=mysql_fetch_array($checkCamSql)){
  	$i=1;echo"<tr>";
	do{
		$Floor=$checkCamRow["Floor"]==0?"&nbsp;":$checkCamRow["Floor"];
		$Info=$checkCamRow["Info"];
		$wt=$i==1?450:510;
		if ($checkCamRow["Name"]!="NULL"){
		   echo"<td align='right' valign='top' style='width:$wt" . "px;height:400px;'><span class='style1'>$Floor</span> <span class='style2'>$Info</span></td>";
		  }
		$i++;
		if($i>$Cles){
			echo"</tr>";$i=1;
			}
		}while($checkCamRow=mysql_fetch_array($checkCamSql));
	}
	?>
 </table>
</div>

<table border="0" align="center"  cellspacing="0" style='margin-top:130px;'>
<?php   
  $i=1;
  $checkCamSql=mysql_query("SELECT * FROM $DataPublic.ot2_cam C WHERE C.From='$f' AND C.Id IN ($Id) ORDER BY C.Order,C.Id",$link_id);
   if($checkCamRow=mysql_fetch_array($checkCamSql)){
  	$i=1;
	do{ 
          $Port=$checkCamRow["Port"];
          $IP=$checkCamRow["IP"];

		if($i==1){
			echo"<tr>";
			}
		else{
			echo"<td width='3'>&nbsp;</td>";
			}
			$Params=$checkCamRow["Params"];
			if (stripos($Params, "subcam/")===false){
				$src="http://$IP:$Port/$Params";
			}
			else{
				$src=$Params;
			}
			
			 echo"<td class='A1111' style='width:500px;height:480px'><iframe frameborder=0 width=500 height=480 marginheight=0 marginwidth=0 scrolling=no src='$src'></iframe></td>"; 
		$i++;
		if($i>$Cles){
			echo"</tr>";$i=1;
			}
		}while($checkCamRow=mysql_fetch_array($checkCamSql));
	}
//if($i<$LenNum-1){echo"<tr><td height='3' colspan='5'></td></tr>";}
?></table>
</BODY>
</HTML>

<?php   
//电信-zxq 2012-08-01
include "../basic/chksession.php";
include "../basic/parameter.inc";			//数据库连接
//$Cles=$f=="mc"?4:2;
//$IP=$f=="mc"?"113.105.87.188":"113.105.87.57";
//$Width=$f=="mc"?2560:1500;

switch($f){
      case "mc":$Cles=4;$IP="113.105.87.188";$Width=2560;break;
      case "dp":$Cles=2;$IP="113.105.87.57";$Width=1500;break;
      case "cf":$Cles=2;$IP="183.62.224.154";$Width=1500;break;
      case "48":$Cles=2;$IP="113.105.87.58";$Width=1500;break;
      case "47":$Cles=2;$IP="113.105.87.58";$Width=1500;break;
      case "bsd":$Cles=2;$IP="113.108.240.162";$Width=1500;break;
        }
 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN">
<HTML>
<HEAD>
<TITLE>公司摄像头监控</TITLE>
<?php   
echo "<link rel='stylesheet' href='model/tl/read_line.css'><link rel='stylesheet' href='model/sharing.css'>";
?>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
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
</HEAD>
<BODY>

<div id="Layer1" style="position:absolute; left:0px; top:14px; z-index:1;">
  <table width="<?php    echo $Width?>" border="0" align="center" cellspacing="0">
  <?php   
  $checkCamSql=mysql_query("SELECT * FROM $DataPublic.ot2_cam C WHERE C.From='$f' AND C.Estate=1  ORDER BY C.Order,C.Id",$link_id);
  if($checkCamRow=mysql_fetch_array($checkCamSql)){
  	$i=1;
	do{
		$Floor=$checkCamRow["Floor"]==0?"&nbsp;":$checkCamRow["Floor"];
		$Info=iconv("UTF-8","GB2312",$checkCamRow["Info"]);
		if($i==1){
			echo"<tr>";
			}
		if ($checkCamRow["Name"]!="NULL"){
		   echo"<td align='center' valign='bottom' style='weight:640px;height:480px'><span class='style1'>$Floor</span><span class='style2'>$Info</span></td>";
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
<table border="0" align="center" cellspacing="0">
<?php   
  $checkCamSql=mysql_query("SELECT * FROM $DataPublic.ot2_cam C WHERE C.From='$f' AND C.Estate=1  ORDER BY C.Order,C.Id",$link_id);
  if($checkCamRow=mysql_fetch_array($checkCamSql)){
  	$i=1;
	do{ 
          $Port=$checkCamRow["Port"];
	    if(preg_match('/^192\.168/',$Login_IP)){
                   $IP=$checkCamRow["IP"];
              }
		else{
		     $IP=$checkCamRow["OutIP"];
			}
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
			 echo"<td class='A1111' style='width:640px;height:480px'><iframe  frameborder=0 width=640 height=480 marginheight=0 marginwidth=0 scrolling=no src='$src'></iframe></td>"; 
			 //echo $src;
	/*
	   if ($f==48 && ($Port==2430 || $Port==2440 || $Port==2420 || $Port==2410 || $Port==2400)){
	      $IP_Info=$IP . ":" . $Port;
                   echo"<td class='A1111' style='weight:640px;height:480px'><iframe frameborder=0 width=640 height=480 marginheight=0 marginwidth=0 scrolling=no src='subcam/cam_sony.php?IP=$IP_Info'></iframe></td>";  
       }else{
	     // echo "http://$IP:$Port/ImageViewer?Mode=Motion&Resolution=640x400&Quality=Standard&Interval=10'";
		   echo"<td class='A1111' style='weight:640px;height:480px'><iframe frameborder=0 width=640 height=480 marginheight=0 marginwidth=0 scrolling=no src='http://$IP:$Port/ImageViewer?Mode=Motion&Resolution=640x400&Quality=Standard&Interval=10'></iframe></td>";
	   }
	*/	
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
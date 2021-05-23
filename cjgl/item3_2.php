
<table border="0" align="center" cellspacing="0">
<?php   
//电信-zxq 2012-08-01
$Cles=4;
 $checkCamSql=mysql_query("SELECT * FROM $DataPublic.ot2_cam C WHERE C.From='mc' ORDER BY C.Order,C.Id",$link_id);
  if($checkCamRow=mysql_fetch_array($checkCamSql)){
  	$i=1;
	do{ 
	    if(preg_match('/^192\.168/',$Login_IP)){
		$IP=$checkCamRow["IP"];}
		else{$IP=$f=="mc"?"113.105.87.188":"113.105.87.57";}
		$Port=$checkCamRow["Port"];
		if($i==1){
			echo"<tr>";
			}
		else{
			echo"<td width='3'>&nbsp;</td>";
			}
		echo"<td class='A1111' style='weight:640px;height:480px'><iframe frameborder=0 width=640 height=480 marginheight=0 marginwidth=0 scrolling=no src='http://$IP:$Port/ImageViewer?Mode=Motion&Resolution=640x400&Quality=Standard&Interval=10'></iframe></td>";
		$i++;
		if($i>$Cles){
			echo"</tr>";$i=1;
			}
		}while($checkCamRow=mysql_fetch_array($checkCamSql));
	}
?></table>
</BODY>
</HTML>

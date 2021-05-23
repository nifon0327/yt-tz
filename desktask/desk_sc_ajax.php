<?php   
//电信---yang 20120801
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$i=1;
$tableWidth=825;
$subTableWidth=810;
echo"<table id='$TableId' width='$tableWidth' align='center' cellspacing='1' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#CCCCCC'><td width='30' height='30' align='center'>&nbsp;</td><td width='75'>&nbsp;</td><td width='388'>&nbsp;</td></tr>";
for($i=1;$i<5;$i++){
	switch($i){
		case 1:$TypeName="背卡条码";
		break;
		case 2:$TypeName="PE袋标签";
		break;
		case 3:$TypeName="外箱标签";
		break;
		case 4:$TypeName="白盒/坑盒";
		break;
		
		}
	$checkFileSql=mysql_query("SELECT Estate FROM $DataIn.file_codeandlable WHERE ProductId='$ProductId' AND CodeType='$i'",$link_id);
	if($checkFileRow=mysql_fetch_array($checkFileSql)){
			//已经存在文件
			echo"<tr bgcolor='#CCCCCC'>
    			<td align='center'>$i</td>
  				<td align='center'>$TypeName <span class='greenB'>已上传</span></td>
  				<td>
					<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0' width='420' height='30'>
  					<param name='movie' value='upload.swf?maxsize=2048&amp;bgcolor=FF00FF&amp;limit=qdf&amp;savefile=desk_sc_save.php&amp;ProductId=$ProductId&amp;CodeType=$i'>
  					<param name='quality' value='high'>
  					<embed src='upload.swf?maxsize=2048&amp;bgcolor=FF00FF&amp;limit=qdf&amp;savefile=desk_sc_save.php&amp;ProductId=$ProductId&amp;CodeType=$i' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' width='420' height='30'></embed>
					</object>
				</td>
 	 			</tr>";
			}
		else{//没有文件
			echo"<tr bgcolor='#CCCCCC'>
    			<td align='center'>$i</td>
  				<td align='center'>$TypeName <span class='redB'>未上传</span></td>
  				<td>
					<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0' width='420' height='30'>
  					<param name='movie' value='upload.swf?maxsize=2048&amp;bgcolor=FF00FF&amp;limit=qdf&amp;savefile=desk_sc_save.php&amp;ProductId=$ProductId&amp;CodeType=$i'>
  					<param name='quality' value='high'>
  					<embed src='upload.swf?maxsize=2048&amp;bgcolor=FF00FF&amp;limit=qdf&amp;savefile=desk_sc_save.php&amp;ProductId=$ProductId&amp;CodeType=$i' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' width='420' height='30'></embed>
					</object>
				</td>
 	 			</tr>";
			}
		}
	echo"</table>";
?>

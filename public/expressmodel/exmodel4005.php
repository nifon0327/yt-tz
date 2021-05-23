<?php 
//二合一已更新
?>
<script src="js/divfuntion.js" type="text/javascript"></script>
<style type="text/css">
<!--
@charset "UTF-8";
BODY {
	BACKGROUND-POSITION: center 50%;
	SCROLLBAR-FACE-COLOR: #9BCFE3;
	FONT-SIZE: 12px;
	SCROLLBAR-HIGHLIGHT-COLOR: #308BAD;
	SCROLLBAR-SHADOW-COLOR: #308BAD;
	COLOR: #0000FF;
	SCROLLBAR-3DLIGHT-COLOR: #FFFFFF;
	SCROLLBAR-ARROW-COLOR: #CC0000;
	SCROLLBAR-TRACK-COLOR: #FFFFFF;
	FONT-FAMILY: Verdana, Arial, Helvetica, sans-serif;
	SCROLLBAR-DARKSHADOW-COLOR: #FFFFFF;
	border: thin none #000000;
	margin-top: 0px;
	margin-left: 0px;
	background-color: #F5F5F5;
	}
TD{
	FONT-SIZE: 12px;
	FONT-FAMILY: Verdana, Arial, Helvetica, sans-serif;
	border: none;
	font-weight: bold;
	}
-->
</style>
</head>
<body>
<!-- 原单参考 -->
<table width="785"  border="0" cellpadding="0" cellspacing="0" background="expressmodel/4005.gif" style="width:820px;height:506px">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

<!-- 1-发件公司 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:67px; left:23px; width:321; z-index:1; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $E_Company?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 2-发件人 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:39px; left:19px; width:147; z-index:2; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $Nickname?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 3-发件地址 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:100px; left:15px; width:338; z-index:3; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $E_Address?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>
<!-- 国家-->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:147px; left:218px; width:125; z-index:3; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)>CHINA</td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>
<!-- 邮政编码-->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:147px; left:80px; width:125; z-index:3; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo E_ZIP?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 4-发件人电话\手机 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:40px; left:174px; width:171; z-index:4; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $E_Tel?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 5-收件人公司 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:240px; left:16px; width:329; z-index:5; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $Company?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>


<!-- 6-收件人地址 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:271px; left:16px; width:329; z-index:6; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $Address?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>
<!-- 收件人国家-->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:318px; left:212px; width:133; z-index:6; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $Country?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>
<!-- 收件邮政编码-->

<!-- 7-收件人 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:212px; left:13px; width:159; z-index:7; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $Receiver?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 8-收件人电话 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:212px; left:184px; width:160; z-index:8; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $Tel?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 9-托寄物内容 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:308px; left:355px; width:183; z-index:9; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this) align="center"><?php  echo $Contents?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 11-托寄件数 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:237px; left:354px; width:60; z-index:11; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this) align="center"><?php  echo $Pieces?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 14-经手人 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:449px; left:689px; width:86; z-index:14; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this) align="center">ZengYong</td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 15-寄件日期 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:449px; left:597px; width:96; z-index:15; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $Month?>&nbsp;&nbsp;&nbsp;<?php  echo $Day?>&nbsp;&nbsp;&nbsp;<?php  echo $Year?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

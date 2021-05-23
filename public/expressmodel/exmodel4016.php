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
	FONT-FAMILY: "思源黑体";
	SCROLLBAR-DARKSHADOW-COLOR: #FFFFFF;
	border: thin none #000000;
	margin-top: 0px;
	margin-left: 20px;
	background-color: #F5F5F5;
	}
TD{
	FONT-SIZE: 15px;
	FONT-FAMILY: "思源黑体";
	border: none;
	font-weight: bold;
	}
-->
</style>
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" background="expressmodel/4016.gif" style="width:841px;height:692px">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

<!-- 付款方式 106 164-->
<?php 
if($PayType==1){
	$PayX=52;
	}
else{
	if($PayType==0){
		$PayX=106;
		}
	else{
		$PayX=164;
		}
	}
?>
<table cellpadding='4' cellspacing='0' style='position:absolute; top:62px; left:441px; width:24; z-index:3; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)>√</td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<table cellpadding='4' cellspacing='0' style='position:absolute; top:143px; left:537px; width:24; z-index:3; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)>√</td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<table cellpadding='4' cellspacing='0' style='position:absolute; top:239px; left:537px; width:24; z-index:3; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)>√</td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<table cellpadding='4' cellspacing='0' style='position:absolute; top:266px; left:490px; width:205; z-index:3; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this) align="center"><?php  echo $PayerNo?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 1-发件公司 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:120px; left:82px; width:340; z-index:1; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this) align="center"><?php  echo $E_Company?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 2-发件人 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:89px; left:82px; width:188; z-index:2; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this) align="center"><?php  echo $Nickname?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 3-发件地址 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:155px; left:84px; width:337; z-index:3; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $E_Address?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>
<!-- 邮政编码-->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:250px; left:316px; width:110; z-index:3; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this) align="center"><?php  echo $E_ZIP?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 4-发件人电话\手机 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:85px; left:280px; width:161; z-index:4; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this) align="center"><?php  echo $E_Tel?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 5-收件人公司 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:314px; left:78px; width:345; z-index:5; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this) align="center"><?php  echo $Company?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>


<!-- 6-收件人地址 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:346px; left:79px; width:333; z-index:6; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $Address?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>
<!-- 收件人国家-->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:441px; left:75px; width:197; z-index:6; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this) align="center"><?php  echo $Country?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>
<!-- 收件邮政编码-->

<!-- 7-收件人 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:284px; left:82px; width:190; z-index:7; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this) align="center"><?php  echo $Receiver?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 8-收件人电话 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:280px; left:288px; width:148; z-index:8; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $Tel?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 9-托寄物内容 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:563px; left:29px; width:212; z-index:9; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this) align="center"><?php  echo $Contents?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 11-托寄件数 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:504px; left:98px; width:54; z-index:11; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this) align="center"><?php  echo $Pieces?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 14-经手人 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:471px; left:504px; width:240; z-index:14; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this) align="center">ZengYong<?php  echo $Month?>/<?php  echo $Day?>/<?php  echo $Year?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 15-寄件日期 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:57px; left:75px; width:86; z-index:15; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $Month?>/<?php  echo $Day?>/<?php  echo $Year?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

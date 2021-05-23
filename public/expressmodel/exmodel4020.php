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
	margin-left: 0px;
	background-color: #F5F5F5;
	}
TD{
	FONT-SIZE: 16px;
	FONT-FAMILY: "思源黑体";
	border: none;
	font-weight: bold;
	}
-->
</style>
</head>
<body>
<!-- 原单参考expressmodel/4020.png -->
<table style="width:767px; height:503px"  border="0" cellpadding="0" cellspacing="0" background="expressmodel/4020.png">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

<!-- 1-发件公司 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:167px; left:117px; width:160; z-index:1; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $S_Company?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 2-发件人 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:130px; left:55px; width:70; z-index:2; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $Shipper?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 3-发件地址 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:201px; left:88px; width:263; z-index:3; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $S_Address.$S_ZIP?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 4-发件人电话\手机 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:130px; left:178px; width:171; z-index:4; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $S_Tel?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 5-收件人公司 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:170px; left:461px; width:262px; z-index:5; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $Company?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>


<!-- 6-收件人地址 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:207px; left:430px; width:268; z-index:6; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $Address?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 7-收件人 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:130px; left:433px; width:70; z-index:7; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $Receiver?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 8-收件人电话 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:131px; left:570px; width:163px; z-index:8; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $Tel?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 9-托寄物内容 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:346px; left:5px; width:89px; z-index:9; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 41px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td height="2" style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this) align="left"><?php  echo $Contents?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 10-托寄物数量 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:401px; left:251px; width:65; z-index:10; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this) align="center"></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 11-托寄件数 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:349px; left:103px; width:44; z-index:11; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this) align="center"><?php  echo $Pieces?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 12-付款方式：518寄方付；收方付573；第三方付640-->
<?php 
if($PayType==1){
	$LeftX=518;
	}
else{
	if($PayType==0){
		$LeftX=573;
		}
	else{
		$LeftX=640;
		}
	}
?>
<table cellpadding='4' cellspacing='0' style='position:absolute; top:306px; left:<?php  echo $LeftX?>px; width:27; z-index:12; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px; left: 424px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)>√</td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>
<?php 
if($PayType==1){?>
<!-- 13-月结帐号 -->
<!--
<table cellpadding='4' cellspacing='0' style='position:absolute; top:270px; left:804px; width:190; z-index:13; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)>7 5 5 2 2 5 9 5 5 0</td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>
-->
<?php  }?>
<!-- 14-经手人 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:447px; left:349px; width:92; z-index:14; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $HandledBy?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

<!-- 15-寄件日期 -->
<table cellpadding='4' cellspacing='0' style='position:absolute; top:471px; left:336px; width:160; z-index:15; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体; height: 30px;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr><td style='cursor:s-resize;'></td><td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td></tr>
	<tr>
    	<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)><?php  echo $Month?>&nbsp;&nbsp;&nbsp;<?php  echo $Day?></td>
   		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
	<tr><td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td><td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td></tr>
</table>

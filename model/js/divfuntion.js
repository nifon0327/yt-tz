/*
<table cellpadding='4' cellspacing='0' 
style='position:absolute; top:98px; left:356px; width:160; z-index:1; TABLE-LAYOUT:fixed; WORD-BREAK:break-all; border:0 solid #BED9EB; FONT-SIZE:12; FONT-FAMILY:思源黑体;' onmouseover=sANDhBorder(this,1) onmouseout=sANDhBorder(this,0)>
	<tr>
		<td style='cursor:s-resize;'></td>
		<td style='width:2px;height:2px;cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
	</tr>
  　<tr>
		<td style='cursor:move;' onmousedown=Down(this) onmousemove=Remove(this) onmouseup=Up(this)>测试qqqqqqqqqqqqqqqqqqqgjdkshj	DLHSJALhdsah</td>
		<td style='cursor:w-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"W") onmouseup=Up(this)></td>
    </tr>
	<tr>
		<td style='cursor:s-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"H") onmouseup=Up(this)></td>
		<td style='width:2px;height:2px;cursor:nw-resize;' onmousedown=Down(this) onmousemove=ResizeRB(this,"") onmouseup=Up(this)></td>
	</tr>
</table>
*/
move = null; //移动标记
wmin = 10; //最小的窗口为100x100
zmax = 10000; //刻录当前的最高层
ssize = 4; //阴影宽度
function sANDhBorder(obj,Action){
	obj.style.border=Action+" solid #BED9EB";
	}

function Down(obj){
	move = 1;
	obj.x = event.x; //鼠标起始位置
　　obj.y = event.y;
	obj.l = obj.offsetParent.offsetLeft; //父元素当前位置
	obj.t = obj.offsetParent.offsetTop;
	obj.w = obj.offsetParent.offsetWidth;
	obj.h = obj.offsetParent.offsetHeight;
	obj.setCapture(); //得到鼠标
	}

function Remove(obj){
	if(move != null){
		obj.offsetParent.style.left = event.x - obj.x + obj.l; // 鼠标移过的位置 + 父元素当前位置
		obj.offsetParent.style.top = event.y - obj.y + obj.t;
　　 	}
	//在状态栏显示层的左、上坐标以及屋的高度、宽度
	sL=obj.offsetParent.style.left;
	sT=obj.offsetParent.style.top;
	sH=obj.offsetParent.style.height;
	sW=obj.offsetParent.style.width;
	window.status="层的顶坐标:"+sT+" 层的左坐标:"+sL+" 层的宽度:"+sW+" 屋的高度:"+sH;
　　 }

function ResizeRB(obj,Direction){
	if(move != null){
		if(Direction!="H"){
			obj.offsetParent.style.width = Math.max(wmin, event.x - obj.x + obj.w); //窗口的width, height不能为负数
			}
		if(Direction!="W"){ 	
　　 		obj.offsetParent.style.height = Math.max(wmin, event.y - obj.y + obj.h);
　　 		}
　　 	}
	}

function Up(obj){
	move = null;
　　obj.releaseCapture(); //释放鼠标
　　}


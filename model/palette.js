function h(obj,url){
obj.style.behavior='url(#default#homepage)';
obj.setHomePage(url);
}
function $(id){
    obj=document.getElementById(id);
	if (obj==null) obj=document.all.id;
	return obj;
}

//检查颜色值-Begin
	function isNum16(ch)
	{
		if (ch >= '0' && ch <= '9')return true;
		if (ch >= 'A' && ch <= 'F')return true;
		if (ch >= 'a' && ch <= 'f')return true;
		return false;
	}
	function isAllNum16(str1)
	{//判断颜色值。除第一个字符#外的任一个值是否大于等a,A,0,小于等于f,F,9，否则报错。
		for (i=1; i<str1.length; i++) {
			if (!isNum16(str1.charAt(i)))
			{
				return false;
			}
		}
		return true;
	}

function checkCol(myColor)
{   //made by jiarry,input color value to change background
if(myColor!="")
 {
  if(myColor.length !=7 || myColor.charAt(0)!="#")
   {
   alert("颜色值加#至少7位，请检查！");
   $("SelColor").value="";
   }
  else if(!isAllNum16(myColor))
  {
  alert("颜色代码错误，请检查\n 颜色代码示例:#ff6600");
  $("SelColor").value="";
  }
  else{
   return myColor;
   }
  }
}

//检查颜色值-END
var SelRGB = '#808080';
var DrRGB = "";
var SelGRAY = '120';
var SelCol="";
var baseCol="#808080";
var light="120";
var RGB=$("RGB");
var hexch = new Array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
//add innerText to FireFox Begin
if(!document.all){
HTMLElement.prototype.__defineGetter__ 
( 
"innerText", 
function () 
{ 
var anyString = ""; 
var childS = this.childNodes; 
for(var i=0; i<childS.length; i++) 
{ 
if(childS[i].nodeType==1) 
anyString += childS[i].tagName=="BR" ? '\n' : childS[i].innerText; 
else if(childS[i].nodeType==3) 
anyString += childS[i].nodeValue; 
} 
return anyString; 
} 
); 
}

////add innerText to FireFox End

function ToHex(n){	var h, l;
	n = Math.round(n);
	l = n % 16;
	h = Math.floor((n / 16)) % 16;
	return (hexch[h] + hexch[l]);
	}

function DoColor(c, l){ 
	var r, g, b;
  	r = '0x' + c.substring(1, 3);
  	g = '0x' + c.substring(3, 5);
  	b = '0x' + c.substring(5, 7);
  	if(l > 120){
		l = l - 120;
		r = (r * (120 - l) + 255 * l) / 120;
		g = (g * (120 - l) + 255 * l) / 120;
		b = (b * (120 - l) + 255 * l) / 120;
		}
	else{
    	r = (r * l) / 120;
    	g = (g * l) / 120;
    	b = (b * l) / 120;
  		}
  	return '#' + ToHex(r) + ToHex(g) + ToHex(b);
	}

function SetupColor(uRGB){
    var i;
    var GrayTable=$("GrayTable");
    if (uRGB!="") {SelRGB=uRGB;baseCol=uRGB};
    for(i = 0; i <= 30; i ++)
		GrayTable.rows[i].bgColor = DoColor(SelRGB, 240 - i * 8);
    var SelColor=$("SelColor");
    var RGB=baseCol;
    var GRAY=light;
    var ShowColor=$("ShowColor");
    SelColor.value = DoColor(baseCol, light);
    ShowColor.bgColor = SelColor.value;
}

function EndColor(){ 
	var i;
	var GrayTable=$("GrayTable");
  	if(DrRGB != SelRGB){
		DrRGB = SelRGB;
   		for(i = 0; i <= 30; i ++)
		GrayTable.rows[i].bgColor = DoColor(SelRGB, 240 - i * 8);
		}
	var SelColor=$("SelColor");
  	var RGB=baseCol;
  	var GRAY=light;
  	var ShowColor=$("ShowColor");
  	SelColor.value = DoColor(baseCol, light);
  	ShowColor.bgColor = SelColor.value;
  	//document.getElementById('copytip').innerHTML='';
	}

function ctOut(e) {
	baseCol=SelRGB;
  	EndColor();
	}

function ctClick(e) {
  	SelRGB = e.bgColor;
  	EndColor();
	}

function ctOver(e){
  	baseCol = e.bgColor.toUpperCase();
  	EndColor();
	}

function gtOver(e){
	light = e.title;
  	EndColor();
 	}

function gtOut() {
	light = SelGRAY;
	EndColor();
  	}

function gtClick(e){
	SelGRAY = e.title;
  	EndColor();
 	}

function okClick(){
	var SelColor=$("SelColor");
 	self.parent.setColor(SelColor.value);
 	}

function inpCol(o){
	var l=o.value;
	if (l.length==7){
                SetupColor(o.value);
		//$('ShowColor').bgColor=checkCol(o.value);
		}
	else if(l.length>7){
		o.value=l.substring(0,7);
 		alert("颜色代码加#不能超过7位");
 		}
	}
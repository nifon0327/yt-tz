function id(obj){
return document.getElementById(obj);
}
var page;
var mx;
var md=false;
var sh=0;
var en=false;
window.onload=function(){
page=id("menu").getElementsByTagName("div");
if(page.length>0){
page[0].style.zIndex=2;
}
for(i=0;i<page.length;i++){
page[i].innerHTML+="<span class=\"tip\">"+(i+1)+"/"+page.length+"页 拖拽翻页</span>";
page[i].id="page"+i;
page[i].i=i;
page[i].onmousedown=function(e){
if(!en){
if(!e){e=e||window.event;}
ex=e.pageX?e.pageX:e.x;
mx=20-ex;
this.style.cursor="move";
md=true;
if(document.all){
this.setCapture();
}else{
window.captureEvents(Event.MOUSEMOVE|Event.MOUSEUP);
}
}
}
page[i].onmousemove=function(e){
if(md){
en=true;
if(!e){e=e||window.event;}
ex=e.pageX?e.pageX:e.x;
this.style.left=ex+mx+"px";
if(this.offsetLeft<150){
var cu=(this.i==0)?page.length-1:this.i-1;
page[sh].style.zIndex=0;
page[cu].style.zIndex=1;
this.style.zIndex=2;
sh=cu;
}
if(this.offsetLeft>150){
var cu=(this.i==page.length-1)?0:this.i+1;
page[sh].style.zIndex=0;
page[cu].style.zIndex=1;
this.style.zIndex=2;
sh=cu;
}
}
}
page[i].onmouseup=function(){
this.style.cursor="default";
md=false;
if(this.offsetLeft==20){
en=false;
}
if(document.all){
this.releaseCapture();
}else{
window.releaseEvents(Event.MOUSEMOVE|Event.MOUSEUP);
}
flyout(this);
}
}
}
function flyout(obj){
if(obj.offsetLeft < 20){
if( (obj.offsetLeft - 10) > -60 ){
obj.style.left=obj.offsetLeft - 10 + "px";
window.setTimeout("flyout(id('"+obj.id+"'));",0);
}else{
obj.style.left= 20+"px";
obj.style.zIndex=0;
flyin(id(obj.id));
}
}
if(obj.offsetLeft > 20){
if((obj.offsetLeft + 10) < 80){
obj.style.left=obj.offsetLeft + 10 + "px";
window.setTimeout("flyout(id('"+obj.id+"'));",0);
}else{
obj.style.left= 20 + "px";
obj.style.zIndex=0;
flyin(id(obj.id));
}
}
}
function flyin(obj){
if(obj.offsetLeft<20){
if((obj.offsetLeft + 10) < 20){
obj.style.left=obj.offsetLeft + 10+"px";
window.setTimeout("flyin(id('"+obj.id+"'));",0);
}else{
obj.style.left= 20 +"px";
en=false;
}
}
if(obj.offsetLeft>20){
if((obj.offsetLeft - 10) > 20){
obj.style.left=obj.offsetLeft - 10 +"px";
window.setTimeout("flyin(id('"+obj.id+"'));",0);
}else{
obj.style.left=20+"px";
en=false;
}
}
}
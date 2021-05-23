function InitAjax(){ 
	var ajax=false;
	try{   
　　	ajax=new ActiveXObject("Msxml2.XMLHTTP");
		}
	catch(e){   
　　	try{   
　　　		ajax=new ActiveXObject("Microsoft.XMLHTTP");
			}
		catch(E){   
　　　		ajax=false;
			}   
　		} 
　	if(!ajax && typeof XMLHttpRequest!='undefined'){
		ajax=new XMLHttpRequest();
		}   
　	return ajax;
	}
//签卡状态设定
var NowTime=new Date().toLocaleString().substring(10,13);
document.getElementById("CheckType").value=NowTime>12?"O":"I";

function ReBackDefault(){
	document.getElementById("ReBack").style.display="none";
	//状态判断：如果是12点前，为签到状态，否则为签退状态
	}

function ReBackType(){//返回默认签卡状态
	var NowTime=new Date().toLocaleString().substring(10,13);
	document.getElementById("CheckType").value=NowTime>12?"O":"I";
	}
function ToActionS(ActionTemp){
	if(ActionTemp==2){
		alert(document.getElementById("CheckType").value);
		}
	else{
		document.getElementById("CheckType").value=ActionTemp==1?"I":"O";
		//状态判断：如果是12点前，为签到状态，否则为签退状态
		if(iType)clearTimeout(iType);
			iType=setTimeout("ReBackType()",10000);//5秒后自动恢复
		}
	}

marqueesHeight=50; //内容区高度
stopscroll=false; //这个变量控制是否停止滚动
with(jbr){
	noWrap=true; //这句表内容区不自动换行
	style.width=0; //于是我们可以将它的宽度设为0，因为它会被撑大
	style.height=marqueesHeight;
	style.overflowY="hidden"; //滚动条不可见
	}

//这时候，内容区的高度是无法读取了。下面输出一个不可见的层"templayer"，稍后将内容复制到里面：
document.write('<div id="templayer" style="position:absolute;z-index:100;visibility:hidden"></div>');
function init(){ //初始化滚动内容
	init2();
	//多次复制原内容到"templayer"，直到"templayer"的高度大于内容区高度：
	templayer.innerHTML+=jbr.innerHTML;
	if(templayer.offsetHeight>marqueesHeight){
		while(templayer.offsetHeight<marqueesHeight){
			templayer.innerHTML+=jbr.innerHTML;
			}
		jbr.innerHTML=templayer.innerHTML+templayer.innerHTML;
		setInterval("scrollUp()",100);
		}
	}
document.body.onload=init;
preTop=0;
function scrollUp(){
	if(stopscroll==true) return;
	preTop=jbr.scrollTop;
	jbr.scrollTop+=1;
	if(preTop==jbr.scrollTop){
		jbr.scrollTop=templayer.offsetHeight-marqueesHeight+1;
	}
}

stopscroll2=false; //这个变量控制是否停止滚动
with(rsr){
	noWrap=true; //这句表内容区不自动换行
	style.width=0; //于是我们可以将它的宽度设为0，因为它会被撑大
	style.height=marqueesHeight;
	style.overflowY="hidden"; //滚动条不可见
	}

document.write('<div id="templayer2" style="position:absolute;z-index:100;visibility:hidden"></div>');
function init2(){ //初始化滚动内容
	//多次复制原内容到"templayer"，直到"templayer"的高度大于内容区高度：
	templayer2.innerHTML+=rsr.innerHTML;
	if(templayer2.offsetHeight>marqueesHeight){
		while(templayer2.offsetHeight<marqueesHeight){
			templayer2.innerHTML+=rsr.innerHTML;
			}
		rsr.innerHTML=templayer2.innerHTML+templayer2.innerHTML;
		setInterval("scrollUp2()",100);
		}
	}
//document.body.onload=init2;
preTop2=0;
function scrollUp2(){
	if(stopscroll2==true) return;
	preTop2=rsr.scrollTop;
	rsr.scrollTop+=1;
	if(preTop2==rsr.scrollTop){
		rsr.scrollTop=templayer2.offsetHeight-marqueesHeight+1;
	}
}
/* add by zx IE fireFox safari 通用  begin  */
//注意1、 要把要把<！-- 的去掉，否则safari 有时在多个脚本时出问题
//注意2、不能使用  objxml=new ActiveXObject("Microsoft.XMLHttp") ; 这是IE专用的


function isIe(){
	thisisIe=(document.all)?true:false;
	return thisisIe;
}
//设置select的可见状态,IE下会庶不住下拉框，只能把它不显示
function setSelectState(state)
{
	var objl=document.getElementsByTagName('select');
	for(var i=0;i<objl.length;i++)
	{
		//objl[i].style.visibility=state;
		objl[i].disabled = state
	}
}

function CreateMaskBack()
{
	//要是做的连frame都遮住就最好！！！
	var bWidth = Math.max(document.documentElement.scrollWidth, document.body.scrollWidth); //parseInt(document.documentElement.scrollWidth);
	var bHeight= Math.max(document.documentElement.scrollHeight, document.body.scrollHeight); //parseInt(document.documentElement.scrollHeight);
	
	if(isIe()){
	 //setSelectState('hidden');
	  setSelectState('false');
	}
	var thisback=document.createElement("div");
	thisback.id="TheMask_back";
	var styleStr="top:0px;left:0px;position:absolute;background:#666;width:"+bWidth+"px;height:"+bHeight+"px;";
	styleStr+=(isIe())?"filter:alpha(opacity=0);":"opacity:0;";
	thisback.style.cssText=styleStr;
	document.body.appendChild(thisback);
	return thisback;
}

//关闭窗口
function closeMaskBack()
{
	if(document.getElementById('TheMask_back')!=null)
	{
		document.getElementById('TheMask_back').parentNode.removeChild(document.getElementById('TheMask_back'));
	}
	if(isIe()){
		setSelectState('');}
}

//让背景渐渐变暗
function showBackground(obj,endInt)
{
	if(isIe())
	{
		obj.filters.alpha.opacity+=1;
		if(obj.filters.alpha.opacity<endInt)
		{
			setTimeout(function(){showBackground(obj,endInt)},5);
		}
	}
	else{
		var al=parseFloat(obj.style.opacity);al+=0.01;
		obj.style.opacity=al;
		if(al<(endInt/100)){
			setTimeout(function(){showBackground(obj,endInt)},5);
		}
	}
}

function showMaskBack()
{
	var thisback=CreateMaskBack(); //调用生成窗口
	showBackground(thisback,50);// 显示
}

/* add by zx IE fireFox safari 通用 end   */

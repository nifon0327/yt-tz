/*  add by zx 新增IE Firfox,safar 的兼容 20110319  begin */
//使firfox也可以用window.event,
function handle_click() {  //调用例子
	if(window.addEventListener)
	{
	   FixPrototypeForGecko();

	}
   alert(window.event.srcElement);
   alert(window.event.button);	
}

function FixPrototypeForGecko()
{
	HTMLElement.prototype.__defineGetter__("runtimeStyle",element_prototype_get_runtimeStyle);
	window.constructor.prototype.__defineGetter__("event",window_prototype_get_event);
	Event.prototype.__defineGetter__("srcElement",event_prototype_get_srcElement);
}
function element_prototype_get_runtimeStyle()
{
	//return style instead...
	return this.style;
}
function window_prototype_get_event()
{
	return SearchEvent();
}
function event_prototype_get_srcElement()
{
	return this.target;
}
function SearchEvent()
{
//IE
    /*
	if(document.all)
	{
		//alert ("too1");
		return window.event;
	}
	*/
	func=SearchEvent.caller;
	while(func!=null)
	{
		var arg0=func.arguments[0];
		if(arg0)
		{
			//if(arg0.constructor==Event)
			if(arg0.constructor==Event||arg0.constructor==MouseEvent || (typeof(arg0)=="object" && arg0.preventDefault && arg0.stopPropagation))
			return arg0;
		}
		func=func.caller;
	}
	return null;

   /*
   if(document.all){
        return window.event;
    }
	
    var caller = SearchEvent.caller;
    while(caller!=null){
        var argument = caller.arguments[0];
        if(argument){
            var temp = argument.constructor;
            if(temp.toString().indexOf("Event")!=-1){
                return argument;
            }
        }
        caller = caller.caller;
    }
    return null;
 */

}

// 使firfox也可以innertext;
/*
function isIE(){ 
  if   (window.navigator.userAgent.toString().toLowerCase().indexOf("msie") >=1)
	return   true;
  else
	return   false;
}
*/
//使firfox也可以用innertext;
function Fixinnertext(){  //使firfox也可以用window.event,innertext;
	try{
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
	catch(e){}
}
/*
var lBrowser = {};
lBrowser.agt = navigator.userAgent.toLowerCase();
lBrowser.isW3C = document.getElementById ? true:false;
lBrowser.isIE = ((lBrowser.agt.indexOf("msie") != -1) && (lBrowser.agt.indexOf("opera") == -1) && (lBrowser.agt.indexOf("omniweb") == -1));
lBrowser.isNS6 = lBrowser.isW3C && (navigator.appName=="Netscape") ;
lBrowser.isOpera = lBrowser.agt.indexOf("opera") != -1;
lBrowser.isGecko = lBrowser.agt.indexOf("gecko") != -1;
lBrowser.ieTrueBody =function (){
	return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body;
};

//为Firefox下的DOM对象增加innerText属性
if(lBrowser.isNS6){ //firefox innerText define
	HTMLElement.prototype.__defineGetter__( "innerText",
	function(){
	return this.textContent;
	}
);
HTMLElement.prototype.__defineSetter__( "innerText",
	function(sText){
	this.textContent=sText;
	}
);

} 
*/
function IE_FireFox(){
	//如果已定义window.event
	 if(window.event)  ////如果已定义window.event ,则跳过
	 {
		// alert ("No firfox,is IE or safari");
	 }
	 else 
	 {
		
		if(window.addEventListener)
		{
		   //alert ("firfox"); 
		   FixPrototypeForGecko();  //使firfox也可以用window.event,innertext;
		   Fixinnertext();   //使firfox也可以用innertext;
		   
	       
		}
	 }	
}


function passvalue(passvars){  //专为safari设计 add by zx 2011-05-05  pavlue 

	//为safari设计 add by zx 2011-05-05
	if(document.getElementById('Safaripassvars')){ //放在add_model_t.php 存在则
	    document.getElementById('Safaripassvars').value=passvars;  //传递给PHP的变量
		var tmpkeywords = passvars.split("|");
		for (i=0;i<tmpkeywords.length;i++){
			//var	StuffId_Str="^";
			 eval("var "+tmpkeywords[i]+"0='^'");  //给分隔符
			//alert("var "+tmpkeywords[i]+"0='^'");
		}

		
		var RecordCount=0;
		if(document.getElementById('RecordCount')){ 
			RecordCount=document.getElementById("RecordCount"+"").value;  //在当前页面的元素
		}
		
		//alert("RecordCount:"+RecordCount);
		//if(RecordCount>=0){	没作用，一般在保存关已判断是否存在表格数据。
		if(RecordCount>=0){	  //说明有记录，但不知存在多少，故用,document.getElementById('RecordCount') 有些地方没用此变量，>0 为成>=0
			if(document.getElementById('TempMaxNumber')){   //放在add_model_t.php 存在则
			    
				var TempMaxNumber=document.getElementById('TempMaxNumber').value;
				
				for(i=1;i<=TempMaxNumber;i++){
					//alert("TempMaxNumber:"+TempMaxNumber);
					for (j=0;j<tmpkeywords.length;j++){
						//alert ("document.getElementById('"+tmpkeywords[j]+i+"')");
						if(eval("document.getElementById('"+tmpkeywords[j]+i+"')") ){
							//StuffId_Str=StuffId_Str+document.getElementById("StuffId"+i+"").value+"^";
							 eval(""+tmpkeywords[j]+"0="+tmpkeywords[j]+"0"+"+document.getElementById('"+tmpkeywords[j]+i+"').value+'^'");  //给分隔符
							//alert(""+tmpkeywords[j]+"0="+tmpkeywords[j]+"0"+"+document.getElementById('"+tmpkeywords[j]+i+"').value+'^'");
						}
					}

				}
			}
	
		}

		 for (i=0;i<tmpkeywords.length;i++){
			 if(eval(""+"document.getElementById('"+tmpkeywords[i]+"0')") ){
			   //document.getElementById("StuffId0"+"").value=StuffId_Str; 
			 	eval(""+"document.getElementById('"+tmpkeywords[i]+"0').value="+tmpkeywords[i]+"0");  //给分隔符
			  //alert(""+"document.getElementById('"+tmpkeywords[i]+"0').value="+tmpkeywords[i]+"0");
			 }
		}
		//var ss=document.getElementById("StuffId0"+"").value;
		///alert ("SS:"+ss);
		 
	}
}


function swapNode(node1,node2)
{
	var parent = node1.parentNode;//父节点
	var t1 = node1.nextSibling;//两节点的相对位置
	var t2 = node2.nextSibling;
	if(t1) parent.insertBefore(node2,t1);
	else parent.appendChild(node2);
	if(t2) parent.insertBefore(node1,t2);
	else parent.appendChild(node1);

}

function CopyToClicp(p1,p2)
{
	if(p1=='1' && p2=='1'){
		//var selectedText = window.getSelection ? window.getSelection() : document.selection.createRange().text||false;
		var selectedText = window.getSelection ? window.getSelection() : document.selection.createRange().htmlText||false;
		/* 复制到剪贴板 */
		document.execCommand("selectAll");
		document.execCommand('Copy');
		/*
		if(window.clipboardData)
		{
		 	//window.clipboardData.setData('URL', selectedText);
			
		}else{
			alert("您的浏览器不支持复制");
		}
		*/
	}

}

/*  add by zx 新增IE Firfox,safar 的兼容 20110319  end */
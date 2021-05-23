// JScript File

// 下拉区背景色
var DIV_BG_COLOR = "#EEE";

// 高亮显示条目颜色
var DIV_HIGHLIGHT_COLOR = "#C30";

// 字体
var DIV_FONT = "Arial";
var DIV_FONTSIZE = "14px";

// 下拉区内补丁大小
var DIV_PADDING = "2px";

// 下拉区边框样式
var DIV_BORDER = "1px solid #FFF";

//DOV 显示的高度
var DIV_SHOW_HEIGHT="400px";

//一次可显示的条数
var DIV_SHOW_ITEMS=20; 

// 文本输入框
var queryField;

// 下拉区ID
var divName;

// IFrame名称
var ifName;

// 记录上次选择的值
var lastVal = "";

// 当前选择的值
var val = "";

// 显示结果的下拉区
var globalDiv;
var scrollDiv; //带个下拉的DIV

var spanscount=0; 
var OneSpanHeight=0; //一个span的高度,默认为20
var curstr;
var curthis;

var why="";
// 下拉区是否设置格式的标记
var divFormatted = false;

//新增加
/*
var globalDivHeigh;
var divFormattedHeigh = false;
var divNameHeigh="querydivHeigh";
var ifNameHeigh="queryiframeHeigh";
*/
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

/**
InitQueryCode函数必须在 <body onload>事件的响应函数中调用，其中：
queryFieldName为文本框控件的ID，
hiddenDivName为显示下拉区div的ID
*/
function InitQueryCode(queryFieldName, hiddenDivName)
{
   document.onclick = new Function("hideDiv()");  
   //document.onkeydown = keypressHandler;

  // 指定文本输入框的onblur和onkeydown响应函数
    queryField = document.getElementById(queryFieldName);
    //queryField.onblur = hideDiv;
	//alert();
    queryField.onkeydown = keypressHandler;

    // 设置queryField的autocomplete属性为"off"
    queryField.autocomplete = "off";

    // 如果没有指定hiddenDivName，取默认值"querydiv"
    if (hiddenDivName)
    {
        divName = hiddenDivName;
    }
    else
    {
        divName = "querydiv";
    }

  // IFrame的name
    ifName = "queryiframe";

  // 100ms后调用mainLoop函数
    setTimeout("mainLoop()", 100);
}

/**
获取下拉区的div,如果没有则创建之
*/
function getDiv (divID)
{
    if (!globalDiv)
    {
        // 如果div在页面中不存在，创建一个新的div
        if (!document.getElementById(divID))
        {
            var newNode = document.createElement("div");
            newNode.setAttribute("id", divID);
            document.body.appendChild(newNode);
        }

        // globalDiv设置为div的引用     
        globalDiv = document.getElementById(divID);
        // 计算div左上角的位置     
        var x = queryField.offsetLeft;
        var y = queryField.offsetTop + queryField.offsetHeight;
        var parent = queryField;
        while (parent.offsetParent)
        {
            parent = parent.offsetParent;
            x += parent.offsetLeft;
            y += parent.offsetTop;
        }

        // 如果没有对div设置格式，则为其设置相应的显示样式
        if (!divFormatted)
        {
            globalDiv.style.backgroundColor = DIV_BG_COLOR;
            //globalDiv.style.fontFamily = DIV_FONT;
			//globalDiv.style.fontSize = DIV_FontSize;
            globalDiv.style.padding = DIV_PADDING;
            globalDiv.style.border = DIV_BORDER;
            //globalDiv.style.width = "200px";
			//globalDiv.style.Height = "400px";
            globalDiv.style.fontSize = DIV_FONTSIZE;
            ///
            globalDiv.style.position = "absolute";
            globalDiv.style.left = x + "px";
            globalDiv.style.top = y + "px";
			//globalDiv.style.overflowY = "scroll";
			//globalDiv.style.height="auto!important";
			//globalDiv.style.maxHeight="400px";
            globalDiv.style.display = "block";
            globalDiv.style.visibility = "hidden";
			
			//globalDiv.onmousedown=divmousedown; //"window.event.cancelBubble=true;alert('here');";
			//globalDiv.onmouseout=display_none; 
			globalDiv.onkeydown = keypressHandler;
			globalDiv.onkeypress = KeyNULL;
			globalDiv.onkeyup = KeyNULL;
            globalDiv.style.zIndex = 10000;
            //globalDiv.className="SearchDiv";  //SearchDiv.css 强行增加Y滚动条			
            divFormatted = true;
        }
    }

    return globalDiv;
}

function KeyNULL(){
	return false;
}
//onmouseout="display_none();" 
function display_none()
{
	if (why=="is_Show"){
		hideDiv();
	}
	
}

/**
根据返回的结果集显示下拉区
*/
function showQueryDiv(resultArray)
{
  // 获取div的引用
    var div = getDiv(divName);
 
    // 如果div中有内容，则删除之
    while (div.childNodes.length > 0)
        div.removeChild(div.childNodes[0]);
        /*
        // 依次添加结果
        for (var i = 0; i < resultArray.length; i++)
        {
          // 每一个结果也是一个div
            var result = document.createElement("div");
            // 设置结果div的显示样式
            result.style.cursor = "pointer";
            result.style.padding = "2px 0px 2px 0px";
            // 设置为未选中
            _unhighlightResult(result);
           
            // 设置鼠标移进、移出等事件响应函数
            result.onmousedown = selectResult;
            result.onmouseover = highlightResult;
            result.onmouseout = unhighlightResult;


            // 结果的文本是一个span
            var result1 = document.createElement("span");
            // 设置文本span的显示样式
            result1.className = "result1";
            result1.style.textAlign = "left";
            result1.style.fontWeight = "bold";
            result1.innerHTML = resultArray[i];
         
            // 将span添加为结果div的子节点
            result.appendChild(result1);
            
            // 将结果div添加为下拉区的子节点
            div.appendChild(result);
			//alert(result.style.height);
        }
        */
		//alert(resultArray[0]);
		div.innerHTML=resultArray[0];
		spanscount=parseInt(resultArray[1]); //取得当前显示的个数
		if (spanscount>0 ) { DIV_SHOW_ITEMS=spanscount;}  //默认为20条
		if(resultArray[2]!="") {DIV_SHOW_HEIGHT=resultArray[2];} //默认为400px
		//alert(DIV_SHOW_ITEMS);
		//spanscount=resultArray[1]; //取得总个数
		//div.innerHTML="<div style='cursor:pointer;' onmousedown=selectResult(); onmouseover=highlightResult(); onmouseout=unhighlightResult() > <span>1234</span></div>";
		//testDiv = document.getElementById('test');
		//testDiv.innerHTML="<div style='cursor:pointer;' onmousedown=selectResult(); onmouseover=highlightResult(); onmouseout=unhighlightResult() ><span>1234</span></div>";
		
		//alert(div.style.height);
        // 如果结果集不为空，则显示，否则不显示
		var curthis=getcurdiv(0);  //取得第一个高度
		var curthisoffsetHeight=curthis.offsetHeight;
		
		var Show_Height=curthisoffsetHeight*DIV_SHOW_ITEMS; //当前显示的高度
		//alert(curthisoffsetHeight+"*"+DIV_SHOW_ITEMS);
		if (Show_Height<parseInt(DIV_SHOW_HEIGHT)){
			DIV_SHOW_HEIGHT=Show_Height+'px';
		}
		
		var divItems=getdivItem();
		if (divItems<DIV_SHOW_ITEMS)   //指定每次显示的条数
		{
			div.style.height="auto";
			div.style.overflowY="hidden";
		}
		else
		{
			div.style.height=DIV_SHOW_HEIGHT;	
			div.style.overflowY="scroll";
		}
		//why="显示1";
		
        showDiv(resultArray.length > 0);

}




function clearDiv()  //清空DIV
{
  // 获取div的引用
    var div = getDiv(divName);
 
    // 如果div中有内容，则删除之
    while (div.childNodes.length > 0)
        div.removeChild(div.childNodes[0]);
	//why="显示2";	
    showDiv(false);
}

/**
用户单击某个结果时，将文本框的内容替换为结果的文本，
并隐藏下拉区
*/
function getcurdiv(curNo)
{
	curstr='search_div'+curNo;
    curthis=document.getElementById(curstr);
	//alert(curthis.name);
	return curthis;
}

//取得div项目总数
function getdivItem()
{
	var div = getDiv(divName);
    var divs = div.getElementsByTagName("div");
    if (divs){
		return divs.length;
    }
	return 0;
}

function selectResult(curNo)
{   

    curthis=getcurdiv(curNo);
	//_selectResult(this);
	_selectResult(curthis);
}

// 选择一个条目
function _selectResult(item)
{
    var spans = item.getElementsByTagName("span");
    if (spans)
    {
        for (var i = 0; i < spans.length; i++)
        {
            if (spans[i].className == "result1")
            {
                var tmpstr=(spans[i].innerHTML).replace(/&nbsp;/g," ");
				tmpstr=tmpstr.replace(/&lt;/g,"<");
				tmpstr=tmpstr.replace(/&gt;/g,">");				
				queryField.value = tmpstr; //(spans[i].innerHTML).replace(/&nbsp;/g," ");
				//queryField.value = (spans[i].innerHTML).replace(/s/g," ");
				//queryField.value = spans[i].innerHTML;
				lastVal = val = escape(queryField.value);
                mainLoop();
                queryField.focus();
				
                //showDiv(false);
				clearDiv();
                return;
            }
        }
    }
}

/**
当鼠标移到某个条目之上时，高亮显示该条目
*/
function highlightResult(curNo)
{

	curthis=getcurdiv(curNo);
	//setscroll(curNo);
 	//_highlightResult(this);
	_highlightResult(curthis);
}

function _highlightResult(item)
{
 	item.focus();
	cancelSpan(item);
	item.style.backgroundColor = DIV_HIGHLIGHT_COLOR;
	//alert("???");
}


/**
当鼠标在某一条上时，要取消掉键盘所在的条目
*/
function cancelSpan(item)
{
    //var count = -1;
	var div = getDiv(divName);
    var spans = div.getElementsByTagName("div");
    if (spans)
    {
        for (var i = 0; i < spans.length; i++)
        {
            //count++; //如果不是当前选择，则取消
            if ((spans[i]!=item) && (spans[i].style.backgroundColor != div.style.backgroundColor))
            {
                spans[i].style.backgroundColor = div.style.backgroundColor;
				//return count;
            }
        }
    }

  //return -1;
}



/**
当鼠标移出某个条目时，正常显示该条目
*/
function unhighlightResult(curNo)
{
    curthis=getcurdiv(curNo);
	//_unhighlightResult(this);
	_unhighlightResult(curthis);
}

function _unhighlightResult(item)
{
   // item.style.backgroundColor = DIV_BG_COLOR;
}

function getSpanHeight(curNo)
{
	var curthis=getcurdiv(curNo);
	var SHeight=curthis.offsetHeight;
	return SHeight;
	
}

/**
显示/不显示下拉区
*/
function showDiv (show)
{
	//alert(why);
    var div = getDiv(divName);
    if (show)
    {
        
		div.style.visibility = "visible";	
		//OneSpanHeight=getSpanHeight(0); //显示时取得第一条span高度
		//div.focus();
		//event.cancelBubble = true;
    }
    else
    {
        div.style.visibility = "hidden";
		//div.style.visibility = "visible";
    }
    adjustiFrame();
}


/**
隐藏下拉区
*/
function hideDiv ()
{
    //why="显示5";
	showDiv(false);
}

/**
调整IFrame的位置，这是为了解决div可能会显示在输入框后面的问题
*/
function adjustiFrame()
{
    var div = getDiv(divName);
	
	// 如果没有IFrame，则创建
    if (!document.getElementById(ifName))
    {
        var newNode = document.createElement("iFrame");
        newNode.setAttribute("id", ifName);
        newNode.setAttribute("src", "javascript:false;");
        //newNode.setAttribute("scrolling", "yes");
		newNode.setAttribute("scrolling", "no");
        newNode.setAttribute("frameborder", "0");
        document.body.appendChild(newNode);
    }

    iFrameDiv = document.getElementById(ifName);
    var div = getDiv(divName);

    // 调整IFrame的位置与div重合，并在div的下一层
    try
    {
        iFrameDiv.style.position = "absolute";
        iFrameDiv.style.width = div.offsetWidth;
        iFrameDiv.style.height = div.offsetHeight;
        iFrameDiv.style.top = div.style.top;
        iFrameDiv.style.left = div.style.left;
        iFrameDiv.style.zIndex = div.style.zIndex - 1;
        iFrameDiv.style.visibility = div.style.visibility;
		//iFrameDiv.onclick=div.onclick;
		iFrameDiv.style.overflowY=div.style.overflowY;
    }
    catch (e)
    {
    } 

	
	
}



/**
文本输入框的onkeydown响应函数
*/
function keypressHandler (evt)
{
    // 获取对下拉区的引用       
    var div = getDiv(divName);

  // 如果下拉区不显示，则什么也不做     
    if (div.style.visibility == "hidden")
    {
        return true;
    }

    // 确保evt是一个有效的事件 
    if (!evt && window.event)
    {
        evt = window.event;
    }
    var key = evt.keyCode;

    var KEYUP = 38;
    var KEYDOWN = 40;
    var KEYENTER = 13;
    var KEYTAB = 9;
 
    // 只处理上下键、回车键和Tab键的响应     
    if ((key != KEYUP) && (key != KEYDOWN) && (key != KEYENTER) && (key != KEYTAB))
    {
        return true;
    }

    var selNum = getSelectedSpanNum(div);
	
	//alert(selNum);
    var selSpan = setSelectedSpan(div, selNum);
    //alert("Here");
    // 如果键入回车和Tab，则选择当前选择条目 
    if ((key == KEYENTER) || (key == KEYTAB))
    {
        if (selSpan)
        {
            _selectResult(selSpan);
      }
        evt.cancelBubble = true;
        return false;
    }
    else //如果键入上下键，则上下移动选中条目
    {
        var divItems=getdivItem();
		//alert(divItems);
		//如果最前一条或最后一条，则不外理
		
		
		if (key == KEYUP)
        {
            if((selNum==0)) {return false;}
			//alert("UP1");
			setscroll(selNum,"up",evt);
			selSpan = setSelectedSpan(div, selNum - 1);
        }
        if (key == KEYDOWN)
        {
            if (selNum>= divItems-1){return false;}
			setscroll(selNum,"down",evt);
			selSpan = setSelectedSpan(div, selNum + 1);
        }
        if (selSpan)
        {
            //alert("here"+selSpan);
			_highlightResult(selSpan);
        }
    }

    // 显示下拉区
	//why="显示4";
    //showDiv(true);
    return true;
}

function get_ffsetTop(curNo)
{
 	var curthis=getcurdiv(curNo);
	var STop=curthis.offsetTop;
	return STop;
}

function setscroll(curspan,updown,evt)
{
	var div = getDiv(divName);
	var scrollTop = parseInt(div.scrollTop);
    var clientHeight = parseInt(div.clientHeight);
    var scrollHeight = parseInt(div.scrollHeight);
	 
	//if((scrollTop==0) || ((scrollTop+clientHeight)==scrollHeight) ) {return false;}
	///if((curspan<0) || (curspan>=spanscount)) {return false;}
	
	var curthis=getcurdiv(curspan);
	var curthisoffsetTop=curthis.offsetTop;  //
	
	var curthisoffsetHeight=curthis.offsetHeight;
    
	var curYpos=curthisoffsetTop; //  get_ffsetTop(curspan);
  
	var predivoffHeight=curthisoffsetHeight+curthisoffsetHeight;
	var NextHeight=curthisoffsetHeight+curthisoffsetHeight; //当最底端时
	var predivoffTop=curYpos;
	var NextoffTop=curYpos;
	
    var divItems=getdivItem();
	
	
	if(curspan>0)
		{
	  		predivoffHeight=getSpanHeight(curspan-1);
			predivoffTop=get_ffsetTop(curspan-1);
		}
	if(curspan<divItems-1)	
		{
	  		NextHeight=getSpanHeight(curspan+1);
			NextoffTop=get_ffsetTop(curspan+1);
		}
	//alert("curspan:"+curspan+"predivoffTop:"+predivoffTop+"curYpos:"+curYpos+"NextoffTop:"+NextoffTop+"scrollTop:"+scrollTop+"clientHeight:"+clientHeight);	
	if(updown=="up"){  //向上移
	   if(predivoffTop<(scrollTop)*1){ //当前位置在上面
		div.scrollTop=predivoffTop;	
        //window.event.returnValue=false;
		evt.returnValue=false;
	    return false;
	 	}
	   else{
		   if( (predivoffTop>scrollTop)  && (predivoffTop<(scrollTop+clientHeight))){  //值在可视区之间，则不动
		   //alert("可视区");
		       evt.returnValue=false;
			   return true;
				
		   }
		   
		   if((predivoffTop)>=(scrollTop+clientHeight)){ //当前位置在下面			
				if((scrollTop+predivoffHeight)>scrollHeight)  //已到了最下面了
				{
					//alert ("最底部了");
					div.scrollTop=scrollHeight-clientHeight;	
        			evt.returnValue=false;
					return false;
				}
				else{
				//alert("其它");
		    	//alert("curspan:"+curspan+"predivoffTop:"+predivoffTop+"curYpos:"+curYpos+"NextoffTop:"+NextoffTop+"scrollTop:"+scrollTop+"clientHeight:"+clientHeight);						
				div.scrollTop=predivoffTop+predivoffHeight-clientHeight;
			    evt.returnValue=false;
			    return true;
				}
				
			}		   
		   

	   }
	 
	 
	 
	}
	if(updown=="down"){
		if(NextoffTop+NextHeight>((scrollTop)*1+clientHeight*1)){	
		  //alert("最下面");
		  div.scrollTop=NextoffTop-clientHeight+curthisoffsetHeight;//div.scrollTop-(clientHeight-(curYpos-scrollTop)); //NextoffTop+NextHeight-clientHeight;
		  evt.returnValue=false;
		  return true;
		}
		else
		{
		   if( (NextoffTop>scrollTop)  && (NextoffTop<(scrollTop+clientHeight))){  //值在可视区之间，刚不动
		  //alert("可视区");
		       evt.returnValue=false;
			   return true;
		   }
		   if((predivoffTop-predivoffHeight)<=(scrollTop)*1){ //当前位置在上面			
			if((predivoffTop-predivoffHeight)<=0)	{
	           //alert("当前上面");
				div.scrollTop=0;
				evt.returnValue=false;
			    return true;
				}
			else {
				//alert("其它");
				div.scrollTop=NextoffTop; //curYpos-curthisoffsetHeight;
				evt.returnValue=false;
			    return true;
				}	
			}
			
	   }
 
	 
	}
return false;	
	
}

/**
获取当前选中的条目的序号
*/
function getSelectedSpanNum(div)
{
    var count = -1;
    var spans = div.getElementsByTagName("div");
	
	
    if (spans)
    {
        for (var i = 0; i < spans.length; i++)
        {
            count++;
            if (spans[i].style.backgroundColor != div.style.backgroundColor)
            {
                return count;
				//count=count;
            }
        }
    }
  //alert(count);   
  return getCurLastSpan(div);
}

//获取当前鼠标所有第一个对象
function getCurLastSpan(div){
	
	var scrollTop = parseInt(div.scrollTop);
    var spans = div.getElementsByTagName("div");
    if (spans)
    {
        for (var i = 0; i < spans.length; i++)
        {
           if(spans[i].offsetTop>scrollTop)
		   {
			   return i;
		   }
        }
    }
   	
}

/**
从第一条记录到当前记录高度的总和
*/
function get_Cur_Height(curspan)
{
    var tmpHeight = 0;
	var div = getDiv(divName);
    var spans = div.getElementsByTagName("div");
    if (spans)
    {
        for (var i = 0; i < curspan; i++)
        {
            
			 tmpHeight=tmpHeight+(spans[i].offsetHeight)*1;
 
        }
    }

  return tmpHeight;
}

/**
选择指定序号的结果条目
*/
function setSelectedSpan(div, spanNum)
{
    var count = -1;
    var thisSpan;
    var spans = div.getElementsByTagName("div");
    

    if (spans)
    {
        for (var i = 0; i < spans.length; i++)
        {
            
			if (++count == spanNum)
            {
                
				_highlightResult(spans[i]);
                thisSpan = spans[i];
            }
            else
            {
               _unhighlightResult(spans[i]);
			
          }
      }
    }

  return thisSpan;
} 



//

//以下需要自由处理



mainLoop = function()
{
	//val = escape(queryField.value);
	val=queryField.value;
	//val=EncodeUtf8(val.toUpperCase());
	
	val=encodeURIComponent(val.toUpperCase()); //转换成UTF8吗传递中文
	
	if (lastVal != val)
	{   //alert(val);
		//var response = Ajax_UserReg_UserTip.GetSearhItmes(val);
		//showQueryDiv(response.value);
		var arrList = new Array();
		//var str='asp,csdn,asp.net,php,jsp,dvbbs,baidu,92mk,123cha,hao123,google,3721,123456,popasp,alimama,aku,cool';
		//arrList = str.split(",");
        var str="";
		var searchtable=document.getElementById("searchtable").value;
		//alert(searchtable);
		
		var searchfile="";
		if (document.getElementById("searchfile"))
		{
			searchfile=document.getElementById("searchfile").value;
			//alert(searchfile);
		}
		if(searchfile.length==0){
			searchfile="QuickSearch_ajax.php"; 
			//alert ("HERE!!");
		}
        //alert ("HERE!!2");
		var Arraysearch=searchtable.split("|");
		
		//条件，表名，表别名，字段名，Estate, 是否有like "%",0表示无，1表无有
		var url=searchfile+"?search="+val+"&Ttable="+Arraysearch[0]+"&TAsName="+Arraysearch[1]+"&TField="+Arraysearch[2]+"&Estate="+Arraysearch[3]+"&ProPer="+Arraysearch[4]+"Company="+Arraysearch[5];  
		
		//alert(url);
       //document.form1.Liststr.value=""; 
        if(val!=""){
			var ajax=InitAjax(); 
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
                    // alert(BackData);
					var CL=BackData.split("^");
					var j=0;
					if (CL.length>1){  //说明有数据
						/*
						for(var i=1;i<(CL.length-1);i++){
						   j=i-1;
						   CL[i]=CL[i].replace(/ /g,"&nbsp;");  //replace(/s/g," "); 
						   //newStr=strContainingMultipleWhiteSpace..replace(/s/g," ");
						   str=str+CL[i]+",";
						 }
						 arrList = str.split(",");
						 //showQueryDivHeigh(arrList);
						 showQueryDiv(arrList);
						*/
						arrList[0]=CL[1];
						arrList[1]=CL[2];
						arrList[2]=CL[3];
						showQueryDiv(arrList);

					   }
					else {
						clearDiv();
					}
					

					}
				}
				
			ajax.send(null);
			
		} 
		else
		{
			clearDiv();
		}
	 

	}

	lastVal = val;
	setTimeout('mainLoop()', 100);
	return true;
}


function ToSearch(){
	//var searchT=document.getElementById("search");
	document.form1.From.value="slist"; 
	document.form1.FromSearch.value="FromSearch"; 	
	//var searchs=searchT.value;
	document.form1.submit(); 
    
}

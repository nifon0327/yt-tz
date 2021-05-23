function KeyBoard(){
	var keyboard=this;
	this.target;
    this.contrast=0;
	this.contrastFlag=null;
	this.defaultValue="";
	this.keyboardPad=null;
	this.error=0;
	this.returnFun="";
	this.InputBox="";
	this.firstFlag=0;
	
	this.setup=function(){ //初始化键盘
         keyboard.addKeyBoard();
		 keyboard.hide();
     }
	
	this.addKeyBoard=function(){
		 var mousecss=" onMouseOver=this.style.backgroundColor='#55C8FF'  onMouseOut=this.style.backgroundColor='#FFFFFF' ";
        document.write("<div id='divKeyboard'>");
		document.write("<input type='text' id='keyValue' class='inputstyle' value='' maxlength='6' readonly>");
	    document.write("<input type='button' id='keyClears' onclick='keyboard.clears()' value='←' class='delbtn'/>");
		document.write ("<br style='clear:both;'/>");
		document.write ("<ul><li onclick='keyboard.keyfun(7)' "+ mousecss +">7</li><li onclick='keyboard.keyfun(8)'  "+ mousecss +">8</li><li onclick='keyboard.keyfun(9)'  "+ mousecss +">9</li></ul>");
		document.write ("<ul><li onclick='keyboard.keyfun(4)'  "+ mousecss +">4</li><li onclick='keyboard.keyfun(5)'  "+ mousecss +">5</li><li onclick='keyboard.keyfun(6)' "+ mousecss +">6</li></ul>");
		document.write ("<ul><li onclick='keyboard.keyfun(1)' "+ mousecss +">1</li><li onclick='keyboard.keyfun(2)' "+ mousecss +">2</li><li onclick='keyboard.keyfun(3)'  "+ mousecss +">3</li></ul>");
		document.write ("<ul><li onclick='keyboard.getValue()' "+ mousecss + " class='btn'>确认</li><li onclick='keyboard.keyfun(0)' "+ mousecss +">0</li><li onclick='keyboard.hide()'  "+ mousecss +" class='btn'>关闭</li></ul>");
        document.write("</div>");
        keyboard.keyboardPad=document.getElementById("divKeyboard");
		keyboard.InputBox=document.getElementById("keyValue");
		//document.getElementById("keyValue").value=keyboard.defaultValue;
   }  
   
this.show=function(targetObject,contrastValue,contrastFlag,defaultValue,returnFun){
     if(targetObject==undefined) {
         alert("未设置目标对象. \n方法keyboard.show(obj 目标对象,obj 比较值,string 数学运算符,string 默认值 ,string 返回时调时过程函数");
         return false;
     }
	 else keyboard.target=targetObject;
	 
	  if((contrastValue==undefined) || (contrastValue=="")){
		 keyboard.contrast=0;
	  }
	 else keyboard.contrast=contrastValue;
	
	if((contrastFlag==">=") || (contrastFlag=="<=") || (contrastFlag=="=") || (contrastFlag=="<") || (contrastFlag==">")){
		 keyboard.contrastFlag=contrastFlag;
	  }
	 else keyboard.contrastFlag=null; 
	 
    if((defaultValue==undefined) || (defaultValue=="")){
		 keyboard.defaultValue="";
	}else{
		if(isnumber(defaultValue)){
			keyboard.defaultValue=defaultValue;
		}
        else keyboard.defaultValue="";
	 }
	 
	 if(returnFun==""){
		 keyboard.returnFun="";
	}else{
		keyboard.returnFun=returnFun;
	}
	 keyboard.clears();
	 keyboard.InputBox.value=keyboard.defaultValue;
	 keyboard.firstFlag=1;
	 keyboard.keyboardPad.style.display="block";
	 keyboard.keyboardPad.style.visibility ="visible";
	 
	//调整位置;
	var offsetPos=keyboard.getAbsolutePos(keyboard.target);
   if(offsetPos.y-document.body.scrollTop<keyboard.keyboardPad.offsetHeight){
    var calTop=offsetPos.y;
   }
   else{ 
    var calTop=offsetPos.y-keyboard.keyboardPad.offsetHeight+keyboard.target.offsetHeight;
   }
   if((document.body.offsetWidth-(offsetPos.x+keyboard.target.offsetWidth))>keyboard.keyboardPad.offsetWidth){
    var calLeft=offsetPos.x+keyboard.target.offsetWidth;
   }
   else{
    var calLeft=offsetPos.x-keyboard.keyboardPad.offsetWidth;
   }
     keyboard.keyboardPad.style.left=calLeft+"px";
     keyboard.keyboardPad.style.top=calTop+"px";
 }
   
   this.hide=function(){
   keyboard.keyboardPad.style.display="none";
   keyboard.keyboardPad.style.visibility ="hidden";

  }
 
  this.getValue=function(){
	 var newNumber =document.getElementById("keyValue").value; 
	 if (!isnumber(newNumber) || newNumber=="") {
		 keyboard.error=1;
		 document.getElementById("keyValue").value="!>=0";
		 return false;
	 }
	 newNumber= parseFloat(newNumber);
	 if (keyboard.contrastFlag!=null && keyboard.contrast!=null){
		var oldNumber = parseFloat(keyboard.contrast);
	    switch(keyboard.contrastFlag){
		  case ">=":
			  if (oldNumber>newNumber){
			     keyboard.error=1;
		         document.getElementById("keyValue").value="!>="+oldNumber;
		         return false; 
			  }
			 break;
		case ">":
			  if (oldNumber>=newNumber){
			     keyboard.error=1;
		         document.getElementById("keyValue").value="!>"+oldNumber;
		         return false; 
			  }
			 break;
		case "<=":
			  if (oldNumber<newNumber){
			     keyboard.error=1;
		         document.getElementById("keyValue").value="!<="+oldNumber;
		         return false; 
			  }
			 break;
		case "<":
			  if (oldNumber<=newNumber){
			     keyboard.error=1;
		         document.getElementById("keyValue").value="!<"+oldNumber;
		         return false; 
			  }
			 break;
		case "=":
			  if (oldNumber!=newNumber){
			     keyboard.error=1;
		         document.getElementById("keyValue").value="!<="+oldNumber;
		         return false; 
			  }
			 break;
		}
	 }
	 //修改内容
	//keyboard.target.innerHTML=newNumber;
	if (keyboard.target.getAttribute("value")!=null){
		keyboard.target.value=newNumber;
	}else{
	    keyboard.target.innerHTML=newNumber;
	}
	
	keyboard.hide();
	if (keyboard.returnFun!=""){
	 	keyboard.returnFun();
	}
  }
  
  this.keyfun=function(keys){  
     var obj = document.getElementById("keyValue"); 
     if (keyboard.error==1 || keyboard.firstFlag==1){
		keyboard.error=0;
		 keyboard.firstFlag=0;
		obj.value=keys;
	 }else{
      obj.value = obj.value + keys;}
   }  
  this.clears=function(){  
     keyboard.error=0;
     var obj = document.getElementById("keyValue"); 
     obj.value ="";  
  }  
  
  this.getAbsolutePos = function(el) { //取得对象的绝对位置
   var r = { x: el.offsetLeft, y: el.offsetTop };
   if (el.offsetParent) {
    var tmp = keyboard.getAbsolutePos(el.offsetParent);
    r.x += tmp.x;
    r.y += tmp.y;
   }
   return r;
  };
   keyboard.setup();
}

function isnumber(str){ 
    var digits=".1234567890"; 
    var i=0; 
    var strlen=str.length; 
    while((i<strlen)){ 
        var char=str.charAt(i); 
        if(digits.indexOf(char)==-1)return false;
		i++; 
    } 
    return true; 
} 
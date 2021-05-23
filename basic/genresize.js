var theobject = null; //This gets a value as soon as a resize start
function resizeObject() {
	this.el  = null; //pointer to the object
	this.dir    = "";      //type of current resize (n, s, e, w, ne, nw, se, sw)
	this.graby = null;
	this.height = null;
}

//Find out what kind of resize! Return a string inlcluding the directions
function getDirection(el) {
	var yPos, offset, dir;
	dir = "";
	yPos = window.event.offsetY;
	offset = 8; //The distance from the edge in pixels
	if (yPos<offset) dir += "n";
	else if (yPos > el.offsetHeight-offset) dir += "s";
	return dir;
}

function doDown() {
	var el = getReal(event.srcElement, "className", "resizeMe");
	if (el == null) {
		theobject = null;
		return;
	}		
	dir = getDirection(el);
	if (dir == "") return;
	theobject = new resizeObject();
	theobject.el = el;
	theobject.dir = dir;
	theobject.graby = window.event.clientY;//屏幕点的Y值
	theobject.height = el.offsetHeight;//层的高度
	window.event.returnValue = false;
	window.event.cancelBubble = true;
}

function doUp() {
	if (theobject != null) {
		theobject = null;
	}
}

function doMove() {
	var el,  yPos, str,  yMin;
	yMin = 30; //层的最小高度
	el = getReal(event.srcElement, "className", "resizeMe");
	if (el.className == "resizeMe") {
		str = getDirection(el);
	//Fix the cursor	
		if (str == "") str = "default";
		else str += "-resize";//指针变为调整大小
		el.style.cursor = str;
	}
	
//拖动的大小
	if(theobject != null) {
		if (dir.indexOf("s") !=-1) //如果是向下拖
		//层的高度:一是层最小值,一是层的高度+屏幕点的Y值-层的Y值
		theobject.el.style.height =Math.max(yMin, theobject.height + window.event.clientY - theobject.graby) + "px";
		window.event.returnValue = false;
		window.event.cancelBubble = true;
	} 
}


function getReal(el, type, value) {
	temp = el;
	while ((temp != null) && (temp.tagName != "BODY")) {
		if (eval("temp." + type) == value) {
			el = temp;
			return el;
		}
		temp = temp.parentElement;
	}
	return el;
}

document.onmousedown = doDown;
document.onmouseup   = doUp;
document.onmousemove = doMove;



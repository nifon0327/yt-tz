var xmlHttp;
var livesearch=document.getElementById("livesearch");
var iframe2=document.getElementById("iframe2");
function showResult(str,FieldName,Table,Action,Condition){//Table 相关的表，Field 为Table的要查询字段名。Condition为额外条件
	if(Action==""){
		Action=2;
		}
	str=encodeURIComponent(str);
    if (str.length==0){ 
    	livesearch.innerHTML="";
     	livesearch.style.border="0px";
     	return ;
    	}
	xmlHttp=GetXmlHttpObject();
   	if(xmlHttp==null){
		alert ("Browser does not support HTTP Request");
      	return;
      	}
    var url="../model/testsearch/livesearch.php?qcName="+str+"&sid="+Math.random()+"&Table="+Table+"&FieldName="+FieldName+"&Action="+Action+"&Condition="+Condition;//Action=1共享表，2内部，3外部
   //alert(url);
	xmlHttp.onreadystatechange=function(){stateChanged(FieldName)};
    xmlHttp.open("GET",url,true);
    xmlHttp.send(null);
  	}   
function stateChanged(FieldName){ 
//alert(xmlHttp.responseText)
	if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){  
		var cName=document.getElementById(FieldName);
      	var CurSize=findPos(cName);
      	livesearch.innerHTML=xmlHttp.responseText;
      	livesearch.style.border="0px solid #A5ACB2";
	  	livesearch.style.display="block";
	  	livesearch.style.top =(CurSize.y+20)+"px";
	  	livesearch.style.left=CurSize.x+"px";
	  	iframe2.style.display="block";
	  	iframe2.style.top=(CurSize.y+20)+"px";
	  	iframe2.style.left=CurSize.x+"px";
	  	iframe2.style.offsetHeight=livesearch.style.offsetHeight;
     	}
	}
function ChangeColor(index){
   document.getElementById("TempName"+index).style.backgroundColor="#FF9900";
	}
function unChangeColor(index){
   document.getElementById("TempName"+index).style.backgroundColor="";
	}
function ChooseName(index,FieldName){
   	document.getElementById(FieldName).value=document.getElementById("TempName"+index).innerHTML;
   	livesearch.style.display="none";
   	iframe2.style.display="none";
	}
function LoseFocus(){
   	livesearch.style.display="none";
   	iframe2.style.display="none";
	}
function findPos(obj) {
	var curleft = obj.offsetLeft || 0;
    var curtop = obj.offsetTop || 0;
    while (obj = obj.offsetParent) {
    	curleft += eval(obj.offsetLeft);
    	curtop += obj.offsetTop;
    	}
    return { x: curleft, y: curtop };
    }
function GetXmlHttpObject(){
	var xmlHttp=null;
    try{
        // Firefox, Opera 8.0+, Safari
        xmlHttp=new XMLHttpRequest();
       }
    catch (e){
       // Internet Explorer
		try{
        	xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
            }
		catch(e){
    		xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
        	}
		}
    return xmlHttp;
	}
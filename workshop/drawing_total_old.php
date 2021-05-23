<!DOCTYPE html>
<meta charset="utf-8">
<title>drawing total</title>
<link rel='stylesheet' href='dzslides.css'>
<link rel='stylesheet' href='tasks.css'>
<section id='Section1'>
    <!-- This is the first slide -->
    <?php  
         include "drawing_product_read.php";
     ?>
</section>
<section id='Section2'>
    <!-- This is the first slide -->
    <?php  
        // include "tasks_total_read.php";
     ?>
</section>
 <div id='bottomdiv'>
	 <ul><li style='width:200px;'>未显示记录</li><li style='width:160px;'><span id='duration'><?php echo $workTimes; ?></span></li></ul>
	 <ul><li style='width:200px;text-align:center;'>(<span id='hCount'>0</span>)</li><li style='width:160px;text-align:right;font-size:22pt;'><span id='upTime'><?php echo $upTime;?></span></li></ul>
 </div>
<div id="progress-bar"></div>

<script src='dzslides.js' type=text/javascript></script>
<script>
    var playPage=1;
    var playCount=1;
     var replayTime=35000;
	 function init() {
		    Dz.init();
		    window.onkeydown = Dz.onkeydown.bind(Dz);
		    window.onresize = Dz.onresize.bind(Dz);
		    window.onhashchange = Dz.onhashchange.bind(Dz);
		    window.onmessage = Dz.onmessage.bind(Dz);
		    //autoScroll(3000,4000);
		     calculateTableRows();
		    setInterval("autoPlay()", replayTime);
		     //setTimeout("autoPlay()",replayTime);
	 }
	
     window.onload =init;
 
function autoPlay(){
	   var url="";
	   playPage=playPage==1?2:1;
	  if (playPage==2){
	      var url="drawing_stuff_read.php";
	   }
	   else{
	      var url="drawing_product_read.php";
	   }
	   
       var SectionName=playPage==2?"Section2":"Section1";
        var ajax=InitAjax();
        ajax.open("GET",url,true);
        ajax.onreadystatechange =function(){
            if(ajax.readyState==4){
                       if (ajax.responseText.length>10){
		                      document.getElementById(SectionName).innerHTML=ajax.responseText;
		                      if (playPage==2) {
			                       Dz.forward();
		                      }
		                      else{
			                      Dz.back(); 
		                      }
		                     var ClearName=playPage==2?"Section1":"Section2";
		                      document.getElementById(ClearName).innerHTML="";
		                       document.getElementById("upTime").innerHTML=document.getElementById("curTime").value;
                               document.getElementById("duration").innerHTML=document.getElementById("workTime").value;
                               calculateTableRows();
                      }
             }
             //setTimeout("autoPlay()",replayTime);
        }
        ajax.send(null);
 }
 
 function calculateTableRows(){
	var listTable=document.getElementsByName("ListTable[]");
    var row=0;
	for(var i=0;i<listTable.length;i++){
        var el_name="ListTable"+i;
        var offsetY=document.getElementById(el_name).offsetTop+document.getElementById(el_name).offsetHeight/2;
        if (offsetY>1920){
	          row=document.getElementById("TotalCount").value*1-i; 
	          break;
        }
	}
	document.getElementById("hCount").innerHTML=row;
}

 
 function autoScroll(speed,delay){    
     var t; 
     var times=0;
     var oHeight =1492; /** div的高度 **/  
     var p=false;   
     var o=document.getElementById("listdiv");    
     var y = 0;
     var rowHeight=210;//行高
     function start(){    
       t=setInterval(scrolling,speed);
    }
   
    function scrolling(){    
        if(o.scrollHeight-oHeight<y){   
           y=0;
           clearTimeout(t);
           var wtimes=replayTime-times;
           wtimes=wtimes<speed?speed:wtimes;
           setTimeout(autoPlay(),wtimes); 
       }
       else{
            times+=speed;
            y += rowHeight; 
            o.scrollTop=y;  
       }
   }
   setTimeout(start,delay);  
}
 
if (!Function.prototype.bind) {
		    Function.prototype.bind = function (oThis) {
		
		      // closest thing possible to the ECMAScript 5 internal IsCallable
		      // function 
		      if (typeof this !== "function")
		      throw new TypeError(
		        "Function.prototype.bind - what is trying to be fBound is not callable"
		      );
		
		var aArgs = Array.prototype.slice.call(arguments, 1),
		          fToBind = this,
		          fNOP = function () {},
		          fBound = function () {
		            return fToBind.apply( this instanceof fNOP ? this : oThis || window,
		                   aArgs.concat(Array.prototype.slice.call(arguments)));
		          };
		
		      fNOP.prototype = this.prototype;
		      fBound.prototype = new fNOP();
		
		      return fBound;
		    };
  }

  var $ = (HTMLElement.prototype.$ = function(aQuery) {
    return this.querySelector(aQuery);
  }).bind(document);

  var $$ = (HTMLElement.prototype.$$ = function(aQuery) {
    return this.querySelectorAll(aQuery);
  }).bind(document);

  $$.forEach = function(nodeList, fun) {
    Array.prototype.forEach.call(nodeList, fun);
  }
 </script>

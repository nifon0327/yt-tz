<!DOCTYPE html>
<meta charset="utf-8">
<title>品检任务</title>
<link rel='stylesheet' href='dzslides.css'>
<link rel='stylesheet' href='tasks.css'>
<section id='Section1'>
    <!-- This is the first slide -->
    <?php  
         $TestSign="new";
         include "ck_ready_read.php";
     ?>
</section>

 <input type='hidden' id='Floor' name='Floor' value='<?php echo $Floor;?>'>
 <input type='hidden' id='CheckSign1' name='CheckSign1' value='0'>
 <section id='Section2'>
    <!-- This is the first slide -->
    <?php  
        // include "ck_ready_read.php";
     ?>
</section>
<input type='hidden' id='CheckSign2' name='CheckSign2' value='1'>
 <div id='bottomdiv'>
	 <ul><li style='width:200px;'>未显示记录</li><li style='width:160px;'><span id='duration'><?php echo $workTimes; ?></span></li></ul>
	 <ul><li style='width:200px;text-align:center;'>(<span id='hCount'>0</span>)</li><li style='width:160px;text-align:right;font-size:22pt;'><span id='upTime'><?php echo $upTime;?></span></li></ul>
 </div>
<div id="progress-bar"></div>
<script src='dzslides.js' type=text/javascript></script>
<script>
     var replayTime=30000;
     var playPage=1;
	 function init() {
		    Dz.init();
		    window.onkeydown = Dz.onkeydown.bind(Dz);
		    window.onresize = Dz.onresize.bind(Dz);
		    window.onhashchange = Dz.onhashchange.bind(Dz);
		    window.onmessage = Dz.onmessage.bind(Dz);
		    //autoScroll(3000,4000);
		     calculateTableRows();
		    setInterval("autoPlay()", replayTime);
	 }
     window.onload =init;
 
function autoPlay(){
      var Floor=document.getElementById("Floor").value;
      var CheckSign;
      playPage=playPage==1?2:1;
	  if (playPage==2){
	        CheckSign=document.getElementById("CheckSign2").value;
	   }
	   else{
	       CheckSign=document.getElementById("CheckSign1").value;
	   }
	   
       var SectionName=playPage==2?"Section2":"Section1";
	   var url="ck_ready_read.php?Floor="+Floor+"&CheckSign="+CheckSign+"&Page="+playPage;
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
                }
        ajax.send(null);
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

function calculateTableRows(){
	var listTable=document.getElementsByName("ListTable[]");
    var row=0;
	for(var i=0;i<listTable.length;i++){
        var el_name="ListTable"+i;
        var offsetY=document.getElementById(el_name).offsetTop+document.getElementById(el_name).offsetHeight/2;
        if (offsetY>1920){
	          row=document.getElementById("TotalCount").value*1-i; break;
        }
	}
	document.getElementById("hCount").innerHTML=row;
}

function updatetime(){
   //不使用
	var d=new Date();
	var localTime = d.getTime();
    var localOffset=d.getTimezoneOffset()*60000; //获得当地时间偏移的毫秒数
    var utc = localTime + localOffset; //utc即GMT时间
    var offset =8; //以夏威夷时间为例，东8区
    var beijing = utc + (3600000*offset); 
    d = new Date(beijing); 

    var hours = add_zero(d.getHours());
    var minutes = add_zero(d.getMinutes());
    var seconds=add_zero(d.getSeconds());
    document.getElementById("upTime").innerHTML=hours+":"+minutes+":"+seconds;
    document.getElementById("duration").innerHTML=document.getElementById("workTime").value;
}

function add_zero(temp)
{
 if(temp<10) return "0"+temp;
 else return temp;
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

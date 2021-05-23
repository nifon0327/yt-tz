<!DOCTYPE html>
<meta charset="utf-8">
<title>tasks product</title>
<link rel='stylesheet' href='dzslides.css'>
<link rel='stylesheet' href='tasks_new.css'>
<section id='Section1'>
    <!-- This is the first slide -->
    <?php  include "tasks_product_read_new.php"; ?>
</section>

<div id="progress-bar"></div>

<script src='dzslides.js' type=text/javascript></script>
<script>
     function init() {
            Dz.init();
            window.onkeydown = Dz.onkeydown.bind(Dz);
            window.onresize = Dz.onresize.bind(Dz);
            window.onhashchange = Dz.onhashchange.bind(Dz);
            window.onmessage = Dz.onmessage.bind(Dz);
            //autoScroll(3000,4000);
           setInterval("autoPlay()", 15000);
     }
     var replayTime=30000;
     window.onload =init;
 
function autoPlay(){
       var url="tasks_product_read_new.php";
        var ajax=InitAjax();
        ajax.open("GET",url,true);
        ajax.onreadystatechange =function(){
            if(ajax.readyState==4){
                      document.getElementById("Section1").innerHTML=ajax.responseText;
                       //autoScroll(3000,4000);
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

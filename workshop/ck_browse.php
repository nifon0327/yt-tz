<!DOCTYPE html>
<?php 
      $Lines=explode(",", $Line);
      $Aligns=explode(",", $Align);
      $Floor=$Floor==""?3:$Floor;//送货楼层
?>
<meta charset="utf-8">
<title>品检任务</title>
<link rel='stylesheet' href='dzslides.css'>
<link rel='stylesheet' href='tasks.css'>
<section id='Section1'>
    <!-- This is the first slide -->
    <?php  
         $Line=$Lines[0]==""?"B":$Lines[0]; $Align=$Aligns[0]==""?"R":$Aligns[0]; 
         include "ck_browse_ac_read.php"; 
         /*
	     if ($ListSTR=="" && count($Lines)>1){
		     $Line=$Lines[1]==""?"C":$Lines[1]; $Align=$Aligns[1]==""?"L":$Aligns[1]; 
		      include "ck_browse_read.php"; 
		     $Line=$Lines[0]==""?"B":$Lines[0]; $Align=$Aligns[0]==""?"R":$Aligns[0];
	    }
	    */
     ?>
</section>
 <input type='hidden' id='Floor' name='Floor' value='<?php echo $Floor;?>'>
<input type='hidden' id='Line1' name='Line1' value='<?php echo $Line;?>'>
<input type='hidden' id='Align1' name='Align1' value='<?php echo $Align;?>'> 
 <section id='Section2'>
    <!-- This is the first slide -->
    <?php $Line=$Lines[1]==""?$Lines[0]:$Lines[1]; $Align=$Aligns[1]==""?"L":$Aligns[1];?>
</section>
  <input type='hidden' id='Line2' name='Line2' value='<?php echo $Line;?>'>
  <input type='hidden' id='Align2' name='Align2' value='<?php echo $Align?>'>
 <div id='bottomdiv'>
	 <ul><li style='width:200px;'>未显示记录</li><li style='width:160px;'><span id='duration'><?php echo $workTimes; ?></span></li></ul>
	 <ul><li style='width:200px;text-align:center;'>(<span id='hCount'>0</span>)</li><li style='width:160px;text-align:right;font-size:22pt;'><span id='upTime'><?php echo $upTime;?></span></li></ul>
 </div>
<div id="progress-bar"></div>
<script src='dzslides.js' type=text/javascript></script>
<script>
    var replayTime=15000;
    var playPage=1;
    var playCount=1;
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
     var Line="";
	  var Align="";
	  playPage=playPage==1?2:1;
	  if (playPage==2){
	        Line=document.getElementById("Line2").value;
	        Align=document.getElementById("Align2").value;
	   }
	   else{
	        Line=document.getElementById("Line1").value;
	        Align=document.getElementById("Align1").value;
	   }
	   
       var SectionName=playPage==2?"Section2":"Section1";

       var Floor=document.getElementById("Floor").value;
	   var url="ck_browse_ac_read.php?Floor="+Floor+"&Line="+Line+"&Align="+Align+"&Page="+playPage;
	   //alert(url);
       var ajax=InitAjax();
        ajax.open("GET",url,true);
        ajax.onreadystatechange =function(){
            if(ajax.readyState==4){
                   if (ajax.responseText.length>100){
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

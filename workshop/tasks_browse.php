<!DOCTYPE html>
<?php 
      $Lines=explode(",", $Line);
      $Aligns=explode(",", $Align);
?>
<meta charset="utf-8">
<title>tasks browse</title>
<link rel='stylesheet' href='dzslides.css'>
<link rel='stylesheet' href='tasks.css'>
<section id='Section1'>
    <!-- This is the first slide -->
    <?php  
        $Floor=$Floor==""?3:$Floor;//送货楼层
        $Line=$Lines[0]==""?"C":$Lines[0]; $Align=$Aligns[0]==""?"R":$Aligns[0]; 
        include "tasks_read.php"; 
	    if ($ListSTR==""){
		    $Line=$Lines[1]==""?"D":$Lines[1]; $Align=$Aligns[1]==""?"L":$Aligns[1]; 
		     include "tasks_read.php"; 
		     $Line=$Lines[0]==""?"C":$Lines[0]; $Align=$Aligns[0]==""?"R":$Aligns[0];
	    }
    ?>
</section>
<input type='hidden' id='Line1' name='Line1' value='<?php echo $Line;?>'>
<input type='hidden' id='Align1' name='Align1' value='<?php echo $Align;?>'>
<section id='Section2'>
      <?php $Line=$Lines[1]==""?"D":$Lines[1]; $Align=$Aligns[1]==""?"L":$Aligns[1]; //include "tasks_read.php"; ?>
</section>
     <input type='hidden' id='Line2' name='Line2' value='<?php echo $Line;?>'>
     <input type='hidden' id='Align2' name='Align2' value='<?php echo $Align?>'>
     
 <div id='bottomdiv'>
	 <ul><li style='width:200px;'>未显示记录</li><li style='width:160px;'><span id='duration'><?php echo $workTimes; ?></span></li></ul>
	 <ul><li style='width:200px;text-align:center;'>(<span id='hCount'>0</span>)</li><li style='width:160px;text-align:right;font-size:22pt;'><span id='upTime'><?php echo $upTime;?></span></li></ul>
 </div>
 
<div id="progress-bar"></div>
<audio id="myaudio" src="music\time.mp3" controls="controls" loop="true" hidden="true" volume="100" >
<script src='dzslides.js' type=text/javascript></script>
<script>
    var playPage=1;
    var playCount=1;
	 function init() {
		    Dz.init();
		    window.onkeydown = Dz.onkeydown.bind(Dz);
		    window.onresize = Dz.onresize.bind(Dz);
		    window.onhashchange = Dz.onhashchange.bind(Dz);
		    window.onmessage = Dz.onmessage.bind(Dz);
		    calculateTableRows();
		    setInterval("autoPlay()", 15000);
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
	   var url="tasks_read.php?Line="+Line+"&Align="+Align;
	   try {
		        var ajax=InitAjax();
		        ajax.open("GET",url,true);
		        ajax.onreadystatechange =function(){
		            if(ajax.readyState==4){
		                       if (ajax.responseText.length>100 && ajax.responseText.indexOf(" <input type='hidden' ")==1){
		                             //alert((ajax.responseText);
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
		                        audioPlay();//播放音乐
		             }
		        }
		        ajax.send(null);
		} catch(error) {}
 }
 
 function audioPlay(){
        var finishCount=document.getElementById("FinishCount").value*1;
		var myAuto = document.getElementById('myaudio');
		if (finishCount>0){
			myAuto.play();
		}
		else{
			myAuto.pause();
		}
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

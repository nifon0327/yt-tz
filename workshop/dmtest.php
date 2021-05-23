<!DOCTYPE html>
<meta charset="utf-8">
<title>测试图片</title>
<section id='Section1' style='margin-top:-25px;'>
<?php  
    $filesize=0;
	include "dmtest_image_read.php"; 
?>
</section>
<input id='FileSize' name='FileSize' type='hidden' value='<?php echo $filesize;?>'/>
<script src='dzslides.js' type=text/javascript></script>
<script>
    var replayTime=5000;
    var playPage=1;
    var playCount=1;
	 function init() {
		setInterval("autoPlay()", replayTime);
	 }
     window.onload =init;
 
function autoPlay(){
       var SectionName="Section1";
       var filesize=document.getElementById('FileSize').value;
	   var url="dmtest_image_read.php?filesize="+filesize;
	   //alert(url);
       var ajax=InitAjax();
        ajax.open("GET",url,true);
        ajax.onreadystatechange =function(){
            if(ajax.readyState==4){
                if (ajax.responseText=='reload'){
	                window.location.reload(); 
                }else{
	               //console.log(filesize); 
                } 
             }
         }
         ajax.send(null);
 }
 </script>

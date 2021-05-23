<!DOCTYPE html>
<html lang="en">
<head>
  <title>video play</title>
</head>
<style>
*{margin:0;padding:0;}
</style>
<script src="../../plugins/js/jquery.min.js" type="text/javascript"></script>
    
<body>
<section id="player">
  <video id="media" width="100%" height="100%" controls poster="images/video.jpg"> </video> 
</section>
</body>
</html>

<script>
	var vList = ['http://116.6.107.226:8060/download/video/video_7.mov', 'http://116.6.107.226:8060/download/video/video_9.mov']; // 初始化播放列表
	var vLen = vList.length; // 播放列表的长度
	
	var curr = 0; // 当前播放的视频
	var video = document.getElementById('media');
    
    setInterval("checkplay()",1000);
	  
    function checkplay(){
	    if (video.ended || video.src.length==0){
		    play();
	    }
    }
    
  function play() {
	   video.src = vList[curr];
	   //video.load(); // 如果短的话，可以加载完成之后再播放，监听 canplaythrough 事件即可
	   video.play();
	   //alert(video.src);
	
	   curr++;
	   if (curr >= vLen) curr = 0; // 播放完了，重新播放
	}

/*    
$(document).ready(function() {
   $.ajax({
    type: "GET",
    cache: false,
    url: "https://www.youtube.com/embed/2wq_FRyqZXw",
    data: "",
    dataType:"html",
    success: function(data){
     //alert("success");
     document.getElementById("player").innerHTML="<iframe width='100%' height='100%' src='https://www.youtube.com/embed/2wq_FRyqZXw' frameborder='0' allowfullscreen></iframe>";
   },
   error:function(){
     //alert("error");
   }
 });
});
*/
		    
</script>
<!DOCTYPE html>
<?php
$this->load->helper('html');
$this->load->helper('url');

$redColor = '#FF0000';
$blueIndex=$blueIndex>0.985?0.985:$blueIndex; 
$redIndex=$redIndex>1?1:$redIndex;
$listRows = 6;
?>

<base href="<?php echo $this->config->item('web_base_url');?>"/>
<!-- <meta http-equiv="refresh" content="30"> -->

<script src='js/jquery.js' type='text/javascript'></script>
<link rel='stylesheet' href='css/dzslides.css'>
<link rel='stylesheet' href='css/tv.css'>
    <script src='js/circlejs/raphael.js' type='text/javascript'></script>
	<script  type='text/javascript'>


	var o = {
		initx: function(){
			this.diagram();
		},
		diagram: function(){
			var six = 1;
			var basewidth = 1080;
			var r = Raphael('diagram', basewidth*six, basewidth*six);
			

			var all = 60;
			var each = 270 / all;
			var kedu = <?php echo($blueIndex) ?>*270;
			kedu = kedu-45;
			var disp = <?php  echo($redIndex) ?>*all;
			
			for (var i=0;i<all;i++) {
				
				r.rect(90,basewidth/2-4,74,8).attr({fill:i>=all-3?(i<=disp?'#000000':'#FF0000'):(i<=disp?'#000000':'#e0e0e0'),stroke:"none"}).rotate((-45+each*i),basewidth/2,basewidth/2);
			}
			
		var p1 =  r.image( 'images/evalue_p.png', 10, basewidth/2-40, 80, 80);
		p1.rotate(kedu,basewidth/2,basewidth/2);
		 
	  }
    }
  $(function(){ o.initx(); });
  

    
    
 </script>


<section id='Section1'>
   <div id='diagram' class="nomargin" style="position:absolute;top: 40px;height: 1080px;width: 1080px;left: 0px; overflow: visible;"></div>
   
   
   <?
	   
/*
	   $dayDict = array(
				   'darkVal'    =>$day_valuation>0?''.($day_output/ $day_valuation):($day_output>0?'100':'0'),
				   'pointVal'   =>$day_valuation>0?''.($newDay / $day_valuation):'0',
				   'pHidden'    =>$phidden,
				   'img'      =>$sleepRabit,
				   'percent'    =>$percent
			   );
*/
			   
		$imgCenter = $dayDict['img'];
		if ($imgCenter != '') {
			?>
			<img style="position: absolute; top: 300px; left: 300px; width: 480px;height: 480px; "
    src="images/<?php echo($imgCenter)?>"
   />
   <?php
		} else {
			
			?>
			
			 <div style="position: absolute; top:460px; width: 680px; left: 200px; text-align: center; height: 125px; color: <?php echo($dayDict['pColor'])?>">
	   <font style="font-family: 'AshCloud61'; font-size:240px;"><?php echo($dayDict['percent']) ?></font>
	   <font style="font-family: 'AshCloud61'; font-size:50px;">%</font>
   </div>
   
			
			<?php
		}
   ?>
   
   
  
   <div style="position: absolute; top:760px; width: 680px; left: 200px; text-align: center; height: 125px; color: #3b3e41">
	   <font style="font-family: 'AshCloud61'; font-size: 30px;">¥</font>
	   <font style="font-family: 'AshCloud61'; font-size:120px;"><?php echo($output) ?></font>
   </div>
   
   <img style="position: absolute; top: 880px; left: 380px; width: 47px;height: 47px; "
    src="images/workstaff.png"
   />
   <div style="position: absolute; top:882px; width: 200px; left: 434px; text-align: left; height: 50px; color: #3b3e41">
	   <div style="font-family: 'AshCloud61'; font-size: 52px;color: #787878;" id='numbers'><?php echo($groupnums) ?></div>
   </div>
   
   <img style="position: absolute; top: 880px; left: 560px; width: 47px;height: 47px; "
    src="images/workclock.png"
   />
   <div style="position: absolute; top:882px; width: 300px; left: 614px; text-align: left; height: 50px; color: #3b3e41">
	   <div id="times_fake" style="font-family: 'AshCloud61'; font-size: 52px; ">
		 <font id="tim_0" style="color: #787878;"><?php echo($worktime_0) ?></font>
		 <font id='tim_p' style="-webkit-animation: twinklingS 1s infinite ease-in-out;color: #787878;">:</font>
		 <font id='tim_1' style="color: #787878;"><?php echo($worktime_1) ?></font>
		 
	   </div>
   </div>
   
   
   
   
   
      <div style="width: 1080px;height: 1px; background-color:#e7f1f7;display: block  ;position: absolute;top: 1020px;"></div>
   
   

    
   
   <?php 


	$listC = count($list);
    $listC = $listC > $listRows ? $listRows :$listC;
	
	$pcolorGreen = "#01be56";
	$pcolorBlue = "#71bede";
	$pcolorGray = "#c5c5c5";
	
	$time2 =date("Y-m-d H:i:s");
	
	$min30 = 60 *30;
	$hour4 = $min30*2 *4;
	$oneDay = $hour4*3;
	
	$gxDisplay;
	

	for ($i=0;$i<$listC;$i++) {
		$row     = $list[$i];
		$week    = $row['week'];
		$week    = strlen($week) >= 2 ? $week : '00';
		$week0   = substr($week, 0, 1);
		$week1   = substr($week, 1, 1);
		$imgUrl  = $row['imgUrl'];

		$nameColor= '#000000';
		$remark = element('remark',$row,'');
		$isOver  = $row['isOver']=='1' ? true : false;
		
?>



<div style="height: 200px; width: 1080px; left: <?php echo($animate==1 && $i==0 ? 1080:0)?>px; position: absolute; top:<?php echo(1020+151*(($i>0 && $animate==1)?($i-1):$i))?>px" id="<?php echo('cell_'.$i)?>">
<div style="width: 105px; height: 105px; position:absolute; top: 24px; left: 37px">			
		
		<img width="105" height="105" src="<?php echo($row['imgUrl']) ?>">
		</div>
		
		<div class="week3 bgcolor_<?php echo($row['isOver'] ? 'red':'black')?>" style="position: absolute; top:42px; left: 170px;">
			<div><?php echo($week0) ?></div><div style="margin-left: 2px"><?php echo($week1) ?></div>
		</div>
		
		<div class="fontBlack32" style="display: block; word-break: break-all;width: 650px; height: 100px;position: absolute;top:40px;left: 230px;
text-overflow:ellipsis; 
white-space:nowrap; 
overflow:hidden;"><span class='fontBlue32'><?php echo($row['Forshort']);?></span>-<?php echo($row['cName']) ?>
		</div>
		
		<img style="width: 40px; height: 40px; position:absolute; top: 99px; left: 230px"			src="images/boxIcon.png">

		<div class="fontBlack34" style="width: 200px; height: 40px; position:absolute; top: 99px; left: 275px; font-size: 32px;"			><?php echo(element('Qty',$row,'')) ?></div>
		
		
		<img style="width: 178px; height: 40px; position:absolute; top: 99px; left: 420px"			src="images/boxPrice.png">

		<div class="fontBlack34" style="width: 138px; height: 40px; position:absolute; top: 99px; left: 465px; font-size: 32px; text-align: center; color: #727171"			><?php echo(element('Price',$row,'')) ?></div>
		
		
		<div class="fontBlack34" style="width: 200px; height: 40px; position:absolute; top: 99px; right: 255px; font-size: 32px; float: right; text-align: right;"			><?php echo(element('Amount',$row,'')) ?></div>
		
		<div class="fontBlack34" style="width: 200px; height: 40px; position:absolute; top: 99px; right: 20px; font-size: 30px; float: right; text-align: right;"	id='<?php echo('times_'.$i)?>'		><?php echo(element('times',$row,'')) ?></div>
		
		
		
        <div style="position: absolute; width: 200px; height: 28px; right: 20px; top: 46px; text-align: right;"><img  width="30" height="30" src="images/line/<?php echo(element('Line',$row,''))?>1.png"/></div>	
		
		
   
	   <div style="width: 1080px;height: 1px; background-color:#e7f1f7;display: block  ;position: absolute;top: 150px;"></div>

</div>

<?php 
	}
	for ($i=$listC;$i<$listRows;$i++) {
	     echo "<div style='display: none;' id='cell_$i'></div>";
	}
?>


   
   <input type="hidden" id='maxId' name="maxIdName" value="<?php echo($lastId) ?>"> 
    <input type="hidden" id='runCounts' name="runCounts" value="1"> 
</section>
<div id="progress-bar"></div>
<script src='js/dzslides.js' type='text/javascript'></script>
<script type='text/javascript'>

  
  function init() {
            Dz.init();
            window.onkeydown = Dz.onkeydown.bind(Dz);
            window.onresize = Dz.onresize.bind(Dz);
            window.onhashchange = Dz.onhashchange.bind(Dz);
            window.onmessage = Dz.onmessage.bind(Dz);
     }
    init();
    
		
		
		  
    var animate = 1*<?php echo($animate)?> ;
    if (animate > 0) {
	    
	
		var i = 1080;
		var times = 0;
	    var div  = document.getElementById("cell_0");
	    var div1 = document.getElementById("cell_1");
	    var div2 = document.getElementById("cell_2");
	    var div3 = document.getElementById("cell_3");
	    var div4 = document.getElementById("cell_4");
	    var div5 = document.getElementById("cell_5");
	    
	    var top1 = parseInt(div1.style.top);

	    var top2 = parseInt(div2.style.top);
	    var top3 = parseInt(div3.style.top);
	    var top4 = parseInt(div4.style.top);
	    var top5 = parseInt(div5.style.top);
	    


	    function test() {                 
	        if (times < 10) {
		         times ++;
	            var timein = 15*times;
	            
	            div1.style.top = (top1 + timein )+ 'px';
	            div2.style.top = (top2 + timein)+ 'px';
	            div3.style.top = (top3 + timein)+ 'px';
	            div4.style.top = (top4 + timein)+ 'px';
	            div5.style.top = (top5 + timein)+ 'px';
	        } else {
		        if (i >= 108) {
		            i-=108;
		            div.style.left = i + "px"; 
		        } 
	        };                
	    };
	    
    


        setTimeout(function() {
	        setInterval('test()',5);
	    }, 400);

		
    }
    
 </script>
 
<script type="text/javascript">
 
   function ajax() {
   
    	var ajax=InitAjax(); 
	　	ajax.open("GET",'../../web.php/zztv/ajax',true);
		ajax.onreadystatechange =function(){
	　		if(ajax.readyState==4){// && ajax.status ==200
				var BackData=ajax.responseText;	
				var models = eval("("+BackData+")");				
// alert(models['id']);

				document.getElementById("numbers").innerHTML = models['num'];
				document.getElementById("tim_0").innerHTML = models['tim_0'];
				document.getElementById("tim_1").innerHTML = models['tim_1'];
				
				
				var lasid  = document.getElementById('maxId').value;
				var runCounts = document.getElementById('runCounts').value*1;
				
				if  (models['id'] > lasid || runCounts>20) {
					window.location.reload();
				} else {
				  if(document.getElementById("times_0"))
					document.getElementById("times_0").innerHTML = models['time_0'];
				  if(document.getElementById("times_1"))
					document.getElementById("times_1").innerHTML = models['time_1'];
				  if(document.getElementById("times_2"))
					document.getElementById("times_2").innerHTML = models['time_2'];
				  if(document.getElementById("times_3"))
					document.getElementById("times_3").innerHTML = models['time_3'];
				  if(document.getElementById("times_4"))
					document.getElementById("times_4").innerHTML = models['time_4'];
				  if(document.getElementById("times_5"))
					document.getElementById("times_5").innerHTML = models['time_5'];
					
				    document.getElementById('runCounts').value = runCounts+1;
				}


			}
		}
		ajax.send(null); 


   }
   
   setInterval('ajax()',5000);
</script>
	
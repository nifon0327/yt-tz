<!DOCTYPE html>
<?php

$this->load->helper('html');
$this->load->helper('url');
 
$url = base_url().'views';


 
$stopGrayAngle = -180 - ($grayIndex/50)*180;

$stopGrayAngle1 = $stopGrayAngle - ($grayIndex1/50)*180;
$stopGrayAngle2 = $stopGrayAngle1 - ($grayIndex2/50)*180;

 
 
$lastRedIndexAngle = 180-$grayIndex2 *180;



 $realcts = count($list);
    $listC = $realcts > 4 ? 4 :$realcts;
	
	$pcolorGreen = "#01be56";
	$pcolorBlue = "#71bede";
	$pcolorGray = "#c5c5c5";
	
	$time2 =date("Y-m-d H:i:s");
	
	$min30 = 60 *30;
	$hour4 = $min30*2 *4;
	$oneDay = $hour4*3;
	
	$gxDisplay;

 $jsStrings = '';
for ($i=0;$i<$listC;$i++) {
	$row     = $list[$i];
	
	$imgUrl  = element('img', $row, null);
	$AgrayIndex = element('grayIndex', $row, 0);
	$AgrayIndex1 = element('grayIndex1', $row, 0);
	$AgrayIndex2 = element('grayIndex2', $row, 0);
	$AgrayIndex3 = element('grayIndex3', $row, 0);
	
	$AstopGrayAngle = -180 - ($AgrayIndex/50)*180;

	$AstopGrayAngle1 = $AstopGrayAngle - ($AgrayIndex1/50)*180;
	$AstopGrayAngle2 = $AstopGrayAngle1 - ($AgrayIndex2/50)*180;
	 
// 	compose js code for charts 
	$jsStrings .= "
		var s_$i = Raphael('diagram_$i', center.x*six, center.y*six);
		s_$i.customAttributes.arc = function(value, color, rad, beg){
			var v      = 3.6*value,
				alpha  = v == 360 ? 359.99 : v,
				random = beg ,
				a      = (random-alpha) * Math.PI/180,
				b      = random * Math.PI/180,
				sx     = center.x/2 + rad * Math.cos(b),
				sy     = center.y/2 - rad * Math.sin(b),
				x      = center.x/2 + rad * Math.cos(a),
				y      = center.y/2 - rad * Math.sin(a),
				path = [['M', sx, sy], ['A', rad, rad, 0, +(alpha > 180), 1, x, y]];
			return { path: path, stroke: color }
		};

		s_$i.path().attr({ arc: ['$AgrayIndex' , '#d5d5d5', 90,'-180'], 'stroke-width': 12 });
		s_$i.path().attr({ arc: ['$AgrayIndex1', '#c7e0ed', 90,'$AstopGrayAngle' ], 'stroke-width': 12 });
		s_$i.path().attr({ arc: ['$AgrayIndex2', '#358fc1', 90,'$AstopGrayAngle1'], 'stroke-width': 12 });
		s_$i.path().attr({ arc: ['$AgrayIndex3', '#fd0300', 90,'$AstopGrayAngle2'], 'stroke-width': 12 });		
	";
	
	
	
	$pucColor1 = $pucColor2 = $pucColor3 = '';
	$pucColor1 = $puc1 >= 90 ? '#01be56':'#ff0000';
	$pucColor2 = $puc2 >= 90 ? '#01be56':'#ff0000';
	$pucColor3 = $puc3 >= 90 ? '#01be56':'#ff0000';
	
	
}

?>

<base href="<?php  echo $this->config->item('web_base_url');?>"/>
<meta http-equiv="refresh" content="60">
<script src='js/jquery.js' type='text/javascript'></script>
<link rel='stylesheet' href='css/dzslides.css'>
<link rel='stylesheet' href='css/tv.css'>
<script src='js/circlejs/raphael.js' type='text/javascript'></script>
<script  type='text/javascript'>

	var myRaphael = {
	init: function(){
		this.diagram();
	},
	diagram: function(){
	
	   
		var six = 1;
		var center={x:640,y:640};

		var r = Raphael('diagram', center.x*six, center.y*six);
		
		r.customAttributes.arc = function(value, color, rad, beg){
			var v      = 3.6*value,
				alpha  = v == 360 ? 359.99 : v,
				random = beg ,
				a      = (random-alpha) * Math.PI/180,
				b      = random * Math.PI/180,
				sx     = center.x/2 + rad * Math.cos(b),
				sy     = center.y/2 - rad * Math.sin(b),
				x      = center.x/2 + rad * Math.cos(a),
				y      = center.y/2 - rad * Math.sin(a),
				path   = [['M', sx, sy], ['A', rad, rad, 0, +(alpha > 180), 1, x, y]];
			return { path: path, stroke: color }
		};
		
		r.path().attr({ arc: [<?php echo($grayIndex ) ?>, '#d5d5d5', 200,'-180'], 'stroke-width': 50 });
		r.path().attr({ arc: [<?php echo($grayIndex1) ?>, '#c7e0ed', 200,<?php echo($stopGrayAngle ) ?>], 'stroke-width': 50 });
		r.path().attr({ arc: [<?php echo($grayIndex2) ?>, '#358fc1', 200,<?php echo($stopGrayAngle1) ?>], 'stroke-width': 50 });
		r.path().attr({ arc: [<?php echo($grayIndex3) ?>, '#fd0300', 200,<?php echo($stopGrayAngle2) ?>], 'stroke-width': 50 });	
		
		
		
		
		center={x:240,y:240};
				
		//generate js chart code here 
		<?php  echo $jsStrings;?>
			
			
			
				
		
		center={x:80,y:80};
				
		
			
		var s_0_0 = Raphael('diagram_0_0', center.x*six, center.y*six);
		s_0_0.customAttributes.arc = function(value, color, rad, beg){
			var v 	   = 3.6*value,
				alpha  = v == 360 ? 359.99 : v,
				random = beg ,
				a      = (random-alpha) * Math.PI/180,
				b      = random * Math.PI/180,
				sx     = center.x/2 + rad * Math.cos(b),
				sy     = center.y/2 - rad * Math.sin(b),
				x      = center.x/2 + rad * Math.cos(a),
				y = center.y/2 - rad * Math.sin(a),
				path = [['M', sx, sy], ['A', rad, rad, 0, +(alpha > 180), 1, x, y]];
			return { path: path, stroke: color }
		};
		s_0_0.path().attr({ arc: ['100', '#dfdfdf', 36,'-180'], 'stroke-width': 1 }); 
		s_0_0.path().attr({ arc: [<?php echo($puc1) ?>, '<?php echo($pucColor1) ?>', 36,'90'], 'stroke-width': 3 }); 		
			
		var s_0_1 = Raphael('diagram_0_1', center.x*six, center.y*six);
		s_0_1.customAttributes.arc = function(value, color, rad, beg){
			var v = 3.6*value,
				alpha = v == 360 ? 359.99 : v,
				random = beg ,
				a = (random-alpha) * Math.PI/180,
				b = random * Math.PI/180,
				sx = center.x/2 + rad * Math.cos(b),
				sy = center.y/2 - rad * Math.sin(b),
				x = center.x/2 + rad * Math.cos(a),
				y = center.y/2 - rad * Math.sin(a),
				path = [['M', sx, sy], ['A', rad, rad, 0, +(alpha > 180), 1, x, y]];
			return { path: path, stroke: color }
		};
		s_0_1.path().attr({ arc: ['100', '#dfdfdf', 36,'-180'], 'stroke-width': 1 }); 
		s_0_1.path().attr({ arc: [<?php echo($puc2) ?>, '<?php echo($pucColor2) ?>', 36,'90'], 'stroke-width': 3 }); 		
	

		var s_0_2 = Raphael('diagram_0_2', center.x*six, center.y*six);
		s_0_2.customAttributes.arc = function(value, color, rad, beg){
			var v = 3.6*value,
				alpha = v == 360 ? 359.99 : v,
				random = beg ,
				a = (random-alpha) * Math.PI/180,
				b = random * Math.PI/180,
				sx = center.x/2 + rad * Math.cos(b),
				sy = center.y/2 - rad * Math.sin(b),
				x = center.x/2 + rad * Math.cos(a),
				y = center.y/2 - rad * Math.sin(a),
				path = [['M', sx, sy], ['A', rad, rad, 0, +(alpha > 180), 1, x, y]];
			return { path: path, stroke: color }
		};
		s_0_2.path().attr({ arc: ['100', '#dfdfdf', 36,'-180'], 'stroke-width': 1 }); 
		s_0_2.path().attr({ arc: [<?php echo($puc3) ?>,' <?php echo($pucColor3) ?>', 36,'90'], 'stroke-width': 3 }); 		

	}
}
$(function(){ myRaphael.init(); });


</script>
<section id='Section1' style="font-family: 'PingFang_Regular'">
	
	<div style="width: 1080px;height: 389px; background-color:#EAF4F9;display: block  ;position: absolute;top: 130px; z-index: -1;"></div>
	
	<div id='diagram' style="position:absolute; top:110px;left: -30px; width: 1080px; height: 1920px; display: block; z-index: 0"></div>





	<div style="width: 403px; height: 250px; position: absolute; top:310px; left:96px; display: block; font-family: 'PingFang_Regular'">

		<div style="position: absolute; width: 409px; height: 40px; left: 0px; top: 30px; text-align: center;">
		<font  style="font-size: 34px;    color: #000000;"><?php echo('未收') ?></font>
		</div>
		
		
		<div style="position: absolute; width: 409px; height: 40px; left: 0px; top: 68px; text-align: center;font-family: 'PingFang_Regular'">
		<font  style="font-size: 34px;    color: #000000;"><?php echo($all_amount) ?></font>
		</div>
		
		
		<div style="position: absolute; width: 409px; height: 40px; left: 0px; top: 110px; text-align: center;">
		<font  style="font-size: 27px;    color: #358fc1;"><?php echo($all_qty) ?></font>
		</div>
	</div>




	<div style="position: absolute; width: 80px; height: 80px; left: 572px; top: 177px;"><img  width="50" height="50" src="images/bomcg/cg_overwk.png"/></div>	
	<div style="position: absolute; width: 120px; height: 40px; left: 638px; top: 178px; text-align: left; vertical-align: middle; font-family: 'PingFang_Bold'; letter-spacing:-2px;">
	<font  style="font-size: 34px;    color: #ff0000;"><?php echo($grayIndex3>0?$grayIndex3*2:'--') ?></font>
	<font  style="font-size: 28px;    color: #ff0000;"><?php echo($grayIndex3>0?'%':'') ?></font>
	</div>

	<div style="position: absolute; width: 320px; height: 40px; left: 638px; top: 218px; text-align: left; vertical-align: middle; font-family: 'PingFang_Regular'; letter-spacing:-2px;">
	<font  style="font-size: 34px;    color: #3a3e41;"><?php echo($all_amount1) ?></font>
	<font  style="font-size: 28px;    color: #727171;">&nbsp;</font>
	<font  style="font-size: 28px;    color: #727171;"><?php echo($all_counts1) ?></font>
	</div>



	<div style="position: absolute; width: 80px; height: 80px; left: 572px; top: 287px;"><img  width="50" height="50" src="images/bomcg/cg_thiswk.png"/></div>	
	<div style="position: absolute; width: 120px; height: 40px; left: 638px; top: 288px; text-align: left; vertical-align: middle; font-family: 'PingFang_Bold'; letter-spacing:-2px; color: #358fc1;">
	<font  style="font-size: 34px;    "><?php echo($grayIndex2>0?$grayIndex2*2:'--') ?></font>
	<font  style="font-size: 28px;    "><?php echo($grayIndex2>0?'%':'') ?></font>
	</div>

	<div style="position: absolute; width: 320px; height: 40px; left: 638px; top: 328px; text-align: left; vertical-align: middle; font-family: 'PingFang_Regular'; letter-spacing:-2px;">
	<font  style="font-size: 34px;    color: #3a3e41;"><?php echo($all_amount2) ?></font>
	<font  style="font-size: 28px;    color: #727171;">&nbsp;</font>
	<font  style="font-size: 28px;    color: #727171;"><?php echo($all_counts2) ?></font>
	</div>


	<div style="position: absolute; width: 80px; height: 80px; left: 572px; top: 397px;"><img  width="50" height="50" src="images/bomcg/cg_nxtwk.png"/></div>	
	<div style="position: absolute; width: 120px; height: 40px; left: 638px; top: 398px; text-align: left; vertical-align: middle; font-family: 'PingFang_Bold'; letter-spacing:-2px;color: #358fc1;">
	<font  style="font-size: 34px;"><?php echo($grayIndex1>0?$grayIndex1*2:'--') ?></font>
	<font  style="font-size: 28px;"><?php echo($grayIndex1>0?'%':'') ?></font>
	</div>

	<div style="position: absolute; width: 320px; height: 40px; left: 638px; top: 438px; text-align: left; vertical-align: middle; font-family: 'PingFang_Regular'; letter-spacing:-2px;">
	<font  style="font-size: 34px;    color: #3a3e41;"><?php echo($all_amount3) ?></font>
	<font  style="font-size: 28px;    color: #727171;">&nbsp;</font>
	<font  style="font-size: 28px;    color: #727171;"><?php echo($all_counts3) ?></font>
	</div>




	
	<div id='diagram_0_0'  style="position:absolute; top:150px;right: 40px; width: 80px; height: 80px;   display: block ; z-index: 2;">
	
		<div style="position: absolute; width: 70px; height: 80px; left: 8px; top: 23px; text-align: center;   color: <?php echo ($pucColor1) ?>; vertical-align: middle;  font-family:'AshCloud61'; letter-spacing: -1px;">
		<font style="font-size: 40px; "><?php echo ($puc1) ?></font>
		<font style="font-size: 10px; "><?php echo ('%') ?></font>
		</div>
		
	</div>
	<div style="position: absolute; width: 160px; height: 30px; right: 0px; top: 230px; text-align: center;font-family: 'PingFang_Regular'">
	<font  style="font-size: 24px;    color: #358fc1;"><?php echo($pucTitle1) ?></font>
	</div>
		
		
	
	<div id='diagram_0_1'  style="position:absolute; top:270px;right: 40px; width: 80px; height: 80px;   display: block ; z-index: 2;"> 
		<div style="position: absolute; width: 70px; height: 80px; left: 8px; top: 23px; text-align: center;   color: <?php echo ($pucColor2) ?>; vertical-align: middle;  font-family:'AshCloud61';letter-spacing: -1px;">
		<font style="font-size: 40px; "><?php echo ($puc2) ?></font>
		<font style="font-size: 10px; "><?php echo ('%') ?></font>
		</div>
	</div>


<div style="position: absolute; width: 160px; height: 30px; right: 0px; top: 350px; text-align: center;font-family: 'PingFang_Regular';color: <?php echo ('#727171') ?>; ">
		<font  style="font-size: 24px;    color: #727171;"><?php echo($pucTitle2) ?></font>
		</div>
		
		

<div id='diagram_0_2'  style="position:absolute; top:390px;right: 40px; width: 80px; height: 80px;   display: block ; z-index: 2;">
	
	<div style="position: absolute; width: 70px; height: 80px; left: 8px; top: 23px; text-align: center;   color: <?php echo ($pucColor3) ?>; vertical-align: middle;  font-family:'AshCloud61';letter-spacing: -1px;">
		 <font style="font-size: 40px; "><?php echo ($puc3) ?></font>
		 <font style="font-size: 10px; "><?php echo ('%') ?></font>
		</div>
	
</div>


<div style="position: absolute; width: 160px; height: 30px; right: 0px; top: 470px; text-align: center;font-family: 'PingFang_Regular';color: <?php echo ('#727171') ?>; ">
		<font  style="font-size: 24px;    color: #727171;"><?php echo($pucTitle3) ?></font>
		</div>
		
		

	






<div   style="position:absolute; top:0px;right: 0px;left: 0px; width:
	auto; height: 100px; z-index: 600; background-color: #358fc1; font-family:'PingFang_Regular'; color: #ffffff; font-size: 60px; text-align: center; vertical-align: middle;"> 
	</div>
<div   style="position:absolute; top:30px;right: 0px;left: 0px; width:
	auto; height: 100px; z-index: 600; background-color: #358fc1; font-family:'PingFang_Medium'; color: #ffffff; font-size: 50px; text-align: center; vertical-align: middle;">
		采购
	</div>
   
   
   
   <?php 


	
	
		
	for ($i=0;$i<$listC;$i++) {
		$row     = $list[$i];

		
		$imgUrl = element('img', $row, '');

	
		$qty = element('allqty', $row, '');
		$cts = element('counts', $row, '');
		$amt = element('amount', $row, '');
		

		$cts1 = element('count1', $row, '');
		$amt1 = element('value1', $row, '');
		$cts2 = element('count2', $row, '');
		$amt2 = element('value2', $row, '');
		$cts3 = element('count3', $row, '');
		$amt3 = element('value3', $row, '');
		
		
		$name = element('name', $row, '');
		
		
		
?>
<div style="height: 350px; width: 1080px; margin-top: 0px; position: absolute; top:<?php echo(519+350*$i)?>px; z-index: 2; font-family: 'PingFang_Regular'">
	
	
	<div style="width: 168px; height: 168px; position:absolute; top: 46px; left: 121px;border-radius: 84px;  overflow: hidden; z-index: 400;">				
					<img  src="<?php echo($imgUrl)?>" style="width: 126px; height: 168px; margin-left: 21px; margin-bottom: 0px; background-repeat:repeat-x;">
				</div>
	
	
<div id='diagram_<?php echo($i) ?>'  style="position:absolute; top:10px;left: 85px; width: 240px; height: 240px;   display: block; -webkit-transform:  rotateZ(-90deg)  rotateY(180deg); z-index: 1000;"></div>

	
	
	
	<div style="position: absolute; width: 409px; height: 40px; left: 0px; top: 230px; text-align: center;">
		<font  style="font-size: 32px;    color: #727171;"><?php echo($name) ?></font>
		</div>
		
		
		<div style="position: absolute; width: 409px; height: 40px; left: 0px; top: 268px; text-align: center;font-family: 'PingFang_Regular'">
		<font  style="font-size: 32px;    color: #000000;"><?php echo($amt) ?></font>
		</div>
		
		
		<div style="position: absolute; width: 409px; height: 40px; left: 0px; top: 310px; text-align: center;">
		<font  style="font-size: 24px;    color: #727171;"><?php echo($qty) ?></font>
		</div>
		
		
		 <div style="position: absolute; width: 45px; height: 45px; left: 430px; top: 42px;"><img  width="40" height="40" src="images/bomcg/wait_cg_0.png"/></div>	
		<div style="position: absolute; width: 80px; height: 40px; left: 482px; top: 39px; text-align: center; vertical-align: middle;">
		<font  style="font-size: 32px;    color: #727171;"><?php echo('待采') ?></font>
		</div>
		
		<div style="position: absolute; width: 680px; height: 40px; right: 100px; top: 39px; text-align: right; vertical-align: middle;">
		<font  style="font-size: 32px;    color: #3a3e41;"><?php echo($amt1) ?></font>
		</div>
		
		<div style="position: absolute; width: 85px; height: 40px; right: 0px; top: 45px; text-align: left; vertical-align: middle;">
		<font  style="font-size: 24px;    color: #727171;"><?php echo($cts1) ?></font>
		</div>
		
		
		 <div style="position: absolute; width: 45px; height: 45px; left: 430px; top: 157px;"><img  width="42" height="42" src="images/bomcg/over_week_0.png"/></div>	
		<div style="position: absolute; width: 80px; height: 40px; left: 482px; top: 155px; text-align: center; vertical-align: middle;">
		<font  style="font-size: 32px;    color: #727171;"><?php echo('逾期') ?></font>
		</div>
		
		<div style="position: absolute; width: 680px; height: 40px; right: 100px; top: 155px; text-align: right; vertical-align: middle;">
		<font  style="font-size: 32px;    color: #ff0000;"><?php echo($amt2) ?></font>
		</div>
		
		<div style="position: absolute; width: 85px; height: 40px; right: 0px; top: 161px; text-align: left; vertical-align: middle;">
		<font  style="font-size: 24px;    color: #727171;"><?php echo($cts2) ?></font>
		</div>
		
		
		
	
	<div style="position: absolute; width: 45px; height: 45px; left: 430px; top: 268px;"><img  width="40" height="40" src="images/bomcg/cur_week_0.png"/></div>	
	<div style="position: absolute; width: 80px; height: 40px; left: 482px; top: 266px; text-align: center; vertical-align: middle;">
		<font  style="font-size: 32px;    color: #727171;"><?php echo('本周') ?></font>
		</div>
		
		<div style="position: absolute; width: 680px; height: 40px; right: 100px; top: 266px; text-align: right; vertical-align: middle;">
		<font  style="font-size: 32px;    color: #3a3e41;"><?php echo($amt3) ?></font>
		</div>
		
		<div style="position: absolute; width: 85px; height: 40px; right: 0px; top: 272px; text-align: left; vertical-align: middle;">
		<font  style="font-size: 24px;    color: #727171;"><?php echo($cts3) ?></font>
		</div>
		
		
		
	
	  <div style="width: 1px;height: 350px; background-color:#e7f1f7;display: block  ;position: absolute;top: 0px; left: 409px;"></div>
	  
	  
	  <div style="width: 680px;height: 1px; background-color:#e7f1f7;display: block  ;position: absolute;top: 116px; left: 409px;"></div>
	  
	  
	  <div style="width: 680px;height: 1px; background-color:#e7f1f7;display: block  ;position: absolute;bottom: 116px; left: 409px;"></div>
  
 

				
		
		
    
	   <div style="width: 1080px;height: 10px; background-color:#e7f1f7;display: block  ;position: absolute;top: 345px;"></div>
	   


</div>

<?php 
	}
			
		
	
	
	
?>


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

	
 </script>
	
	
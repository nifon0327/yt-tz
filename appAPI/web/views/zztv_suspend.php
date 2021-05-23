<!DOCTYPE html>
<?php

$this->load->helper('html');
$this->load->helper('url');
 
$url = base_url().'views';


 
$stopGrayAngle = -180 - ($grayIndex/50)*180;
$stopOrangeAngle = $stopGrayAngle - ($orangeIndex/50)*180;


$redIndexShow = round($redIndex*2);
$lastRedIndexAngle = 180-$lastRedIndex *180;
 
?>
<base href="<?php  echo $this->config->item('web_base_url');?>"/>
<meta http-equiv="refresh" content="30">
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
		
		
		r.path().attr({ arc: [<?php echo($grayIndex) ?>, '#e0e0e0', 220,'-180'], 'stroke-width': 50 });
		r.path().attr({ arc: [<?php echo($orangeIndex) ?>, '#f5ab47', 220,<?php echo($stopGrayAngle) ?>], 'stroke-width': 50 });
		r.path().attr({ arc: [ <?php echo($redIndex) ?>, '#ff0000', 220,<?php echo($stopOrangeAngle) ?>], 'stroke-width': 50 });
		
		
		var p1 =  r.image( 'images/arr_red.png', 30, 300, 40, 40);
		p1.rotate(<?php echo($lastRedIndexAngle)?>,320,320);
		
		var title = r.text(320, 278, '<?php echo($redIndexShow) ?>').attr({
				font: '120px AshCloud61',
				fill: '<?php echo('#ff0000') ?>'
			}).toFront();
			
			
		lent =  <?php echo(strlen($redIndexShow)) ?>;
		beginx = lent > 1 ? 380 : 360;
		beginx = lent > 2 ? beginx+30:beginx;
		var title2 = r.text(beginx, 308, '%').attr({
			font: '38px AshCloud61',
			fill: '<?php echo('#ff0000') ?>'
		}).toFront();
			

	}
}
$(function(){ myRaphael.init(); });


</script>
<section id='Section1'>
<div id='diagram' class='wrapcz' style="position:absolute; top:-120px;left: -30px; width: 640px; height: 320px;"></div>


<div class="topFontS" style="float: right; position: absolute; color: #ff0000; right: 80px;
	font-size: 74px; top:130px;">
	<?php echo(number_format($abnormal))?>
</div>
<div class="topFontS" style="float: right; position: absolute; color: #000000; right: 80px;
	font-size: 74px; top:230px;">
<?php echo(number_format($allqty))?>
</div>

   <div style="width: 1080px;height: 1px; background-color:#e7f1f7;display: block  ;position: absolute;top: 380px;"></div>
   
   
   
   <?php 


	$listC = count($list);
    $listC = $listC > 12 ? 12 :$listC;
	
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
		$time    = $row['time'];
		$nameColor= '#000000';
		$remark = element('remark',$row,'');
		$isOver  = $row['isOver']=='1' ? true : false;
?>
<div style="height: 200px; width: 1080px; margin-top: 0px; position: absolute; top:<?php echo(379+151*$i)?>px">
<div style="width: 105px; height: 105px; position:absolute; top: 24px; left: 37px">			
		
		<img width="105" height="105" src="<?php echo($row['imgUrl']) ?>">
		</div>
		
		<div class="week3 bgcolor_<?php echo($row['isOver'] ? 'red':'black')?>" style="position: absolute; top:42px; left: 170px;">
			<div><?php echo($week0) ?></div><div style="margin-left: 2px"><?php echo($week1) ?></div>
		</div>
		
		<div class="fontBlack32" style="display: block; word-break: break-all;width: 450px; height: 100px;position: absolute;top:40px;left: 230px;
text-overflow:ellipsis; 
white-space:nowrap; 
overflow:hidden;"><span class='fontBlue32'><?php echo($row['Forshort']);?></span>-<?php echo($row['cName']) ?>
		</div>
		
		<div style="position: absolute; width: 200px; height: 33px; right: 300px; top: 40px; text-align: right;">
		<font class="fontGreen30"><?php echo($row['scQty']==0?'':$row['scQty']) ?></font>
		</div>
		<div style="position: absolute; width: 100px; height: 33px; right: 190px; top: 40px; text-align: left;">
		 <font class="fontBlack30"><?php echo ($row['Qty']) ?></font>
		</div>
		
     <?php if ($row['ShipType']>0){ ?>
       <div style="position: absolute; width: 200px; height: 28px; right: 20px; top: 5px; text-align: right;"><img  width="30" height="30" src="images/ship/ship<?php echo($row['ShipType']) ?>.png"/></div>
    <?php } ?>
		<div class="fontGray28" style="position: absolute; width: 200px; height: 28px; right: 55px; top: 40px; text-align: right; color: <?php echo($row['timeColor'])?>;">...<?php echo($row['time']) ?></div>
		


        <div style="position: absolute; width: 200px; height: 28px; right: 20px; top: 46px; text-align: right;"><img  width="30" height="30" src="images/line/<?php echo(element('Line',$row,'A'))?>1.png"/></div>	

    <?php 
	    if ($remark!='') {
		    $remarker = element('remarker',$row,'');
    ?>
    		<div style="background-color: #F3F8FB; position: absolute; top:88px; left: 168px; width: 912px; height: 58px; display: block;">
	    		
	    		<div style="position: absolute; width: 200px; height: 28px; left: 15px; top: 12px;"><img  width="30" height="30" src="images/line/<?php echo(element('Line',$row,'A'))?>.png"/></div>	
	    		
	    		<div style="position: absolute; top:8px; left: 52px; width: 600px; font-family: 'NotoSansHans-Light'; font-size: 28px; color: #727171;text-overflow:ellipsis; 
white-space:nowrap; 
overflow:hidden;"><?php echo($remark)?></div>
	    		<div style="float: right ;position: absolute; top:8px; right: 54px; width: 180px; text-align: right;font-family: 'NotoSansHans-Light'; font-size: 28px; color: #727171;"><?php echo($remarker)?></div>
	    	
	    		
	    	    
    		</div>
    <?php
	    }
    ?>
    
    			<?php 
		    		if (element('isCoding',$row,'0')==1) {
			    		
			    		?>
			    			<div style="position: absolute; width: 200px; height: 28px; right: 20px; top: 100px; text-align: right;"><img  width="30" height="30" src="images/qr.png"/></div>	


			    		<?php 
		    		}
	    		?>
	   <div style="width: 1080px;height: 1px; background-color:#e7f1f7;display: block  ;position: absolute;top: 150px;"></div>

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
	
	
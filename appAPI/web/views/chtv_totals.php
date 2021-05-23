<!DOCTYPE html>
<?php

$this->load->helper('html');
$this->load->helper('url');
 
$url = base_url().'views';

$redIndex = round($OverQty/$tStockQty*50);
$grayIndex = 50 - $redIndex;

//$grayIndex = 35;
//$redIndex = 15; 

$stopGrayAngle = -180 - ($grayIndex/50)*180;
$redIndexShow = round($redIndex*2);
 
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
		r.path().attr({ arc: [ <?php echo($redIndex) ?>, '#ff0000', 220,<?php echo($stopGrayAngle) ?>], 'stroke-width': 50 });

		var title = r.text(320, 278, '<?php echo($redIndexShow) ?>').attr({
				font: '120px AshCloud61',
				fill: '<?php echo('#ff0000') ?>'
			}).toFront();
			
			
		lent =  <?php echo(strlen($redIndexShow)) ?>;
		beginx = lent > 1 ? 385 : 360;
		beginx = lent > 2 ? beginx+35:beginx;
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
	<?php echo(number_format($OverQty))?>
</div>
<div class="topFontS" style="float: right; position: absolute; color: #000000; right: 80px;
	font-size: 74px; top:230px;">
<?php echo(number_format($tStockQty))?>
</div>

<table  width="1080px" bgcolor="#e7f1f7" style="border-width: 0px;position: absolute;top: 380px;text-align:center;">
	<tr class="fontBlue32">
		<td width="200" height="75"> <font>客户</font></td>
		<td width="200" height="75"> <font>所占比(¥)</font></td>
		<td width="260" height="75"> <font>待出>5d</font></td>
		<td width="260" height="75"> <font>数量</font></td>
		<td width="160" height="75"> <font>业务</font></td>
	</tr>
</table>
<hr style="height:0.5px;border:none;border-top:1px solid #e7f1f7;margin-top: 0px" />

<?php 

   for ($i=0,$rownums = count($list);$i<$rownums;$i++) {
       $rows = $list[$i];
?>
      <div style="height: 200px; width: 1080px; margin-top: 0px; position: absolute; top:<?php echo(460+145*$i)?>px;">
         <div style="width: 140px; height: 140px; position:absolute; top:7px; left: 35px">			
		     <img width="130" height="130" src="<?php echo($rows['imgUrl'])?>" onerror="this.src='images/noimage.png';document.getElementById('cname<?php echo $i;?>').style.display='';">
		</div>
		
		<div id='cname<?php echo $i;?>' name='cname<?php echo $i;?>' style="position: absolute; width: 140px; height: 40px; left: 28px; top:110px; text-align: center;display:none;">
		     <font class="fontGray25"><?php echo($rows['Forshort']); ?></font>
		</div>
		
		<div style="position: absolute; width: 140px; height: 40px; left: 200px; top: 50px; text-align: right;">
		     <font class="fontBlue32"><?php echo($rows['Percent']>0?$rows['Percent'] . '%':''); ?></font>
		</div>
		
		<div style="position: absolute; width: 200px; height: 40px; left: 370px; top: 50px; text-align: right;">
		     <font class="fontRed32"><?php echo($rows['OverQty']==0?'':$rows['OverQty']); ?></font>
		</div>
		
		<div style="position: absolute; width: 100px; height: 40px; left: 578px; top: 58px;">
		     <font class="fontGray25"><?php echo($rows['OverCounts']==0?'':$rows['OverCounts']); ?></font>
		</div>
		
		<div style="position: absolute; width: 200px; height: 40px; right: 260px; top: 50px; text-align: right;">
		     <font class="fontBlack32"><?php echo($rows['tStockQty']); ?></font>
		</div>
		
		<div style="position: absolute; width: 100px; height: 40px; right: 152px; top: 58px;">
		     <font class="fontGray25"><?php echo($rows['Counts']); ?></font>
		</div>
		
		<div style="position: absolute; width: 200px; height: 40px; right: 15px; top: 50px; text-align: right;">
		     <font class="fontGray32"><?php echo ($rows['StaffName']); ?></font>
		</div>
		 <hr style="height:0.5px;border:none;border-top:1px solid #e7f1f7;margin-top: 0px" />
      </div>
     
<?php 
    if ($i>10) break;
}
?>
</div>
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
	
	
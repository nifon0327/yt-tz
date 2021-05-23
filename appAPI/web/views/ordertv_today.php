<!DOCTYPE html>
<?php

$this->load->helper('html');
$this->load->helper('url');
 
$url = base_url().'views';


 
$stopGrayAngle = -180 - ($grayIndex/50)*180;

$redIndexShow = round($grayIndex*2); 
 
 

	$realcts = count($list);
	
	
	
	$pages = ceil(($realcts-9) / 4);
	$pages ++;
	
	
	$eachPercent = 100 /( $pages * 8 -1 );
	
	$keyframes  = '';
	
	$animatedStrings = '';
	
	if ($pages>2) {
		$pagetime = $pages * 3;
		$animatedStrings = "-webkit-animation: scrollText1 ".$pagetime."s infinite cubic-bezier(1,0,0.5,0);";
		
		
		for ($i = 0; $i < ($pages * 8); $i++) {
		$numberpx = floor($i/8);
		$numberpx = $numberpx*(-892);
		
		 
			$keyframes .= ($eachPercent * $i)."%{ 
			 -webkit-transform: translateX($numberpx"."px);
	    }
		 ";
		
		
		
	}
	}
	
?>
<base href="<?php  echo $this->config->item('web_base_url');?>"/>
<meta http-equiv="refresh" content="60">
<script src='js/jquery.js' type='text/javascript'></script>
<link rel='stylesheet' href='css/dzslides.css'>
<link rel='stylesheet' href='css/tv.css'>
<style type="text/css">
	
	
	
	
	
	@-webkit-keyframes scrollText1 {
	   <?php 
		   echo($keyframes);
	   ?>  
	}
</style>

<script src='js/circlejs/raphael.js' type='text/javascript'></script>
<script  type='text/javascript'>

	var myRaphael = {
	init: function(){
		this.diagram();
	},
	diagram: function(){
	
	    
		var six = 1;
		var center={x:95,y:95};

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
		
		
		r.path().attr({ arc: [<?php echo($grayIndex) ?>, '#01be56', 39,'-180'], 'stroke-width': 15 }); 
		
		
		
		
			

	}
}
$(function(){ myRaphael.init(); });


</script>
<section id='Section1'>
<div id='diagram' class='wrapcz' style="position:absolute; top:50px;right: 260px; width: 95px; height: 95px; z-index: 1000; "></div>

<div style="position:absolute; top:182px;right: 260px; width: 95px; height: 95px; z-index: 1000; ">
	<img  width="95" height="95" src="images/order_new/<?php echo('chartFrame')?>.png"/>
	
</div>


<div   style="position:absolute; top:0px;right: 0px;left: 0px; width:
	auto; height: 100px; z-index: 600; background-color: #358fc1; font-family:'PingFang_Regular'; color: #ffffff; font-size: 60px; text-align: center; vertical-align: middle;"> 
	</div>
<div   style="position:absolute; top:30px;right: 0px;left: 0px; width:
	auto; height: 100px; z-index: 600; background-color: #358fc1; font-family:'PingFang_Medium'; color: #ffffff; font-size: 50px; text-align: center; vertical-align: middle;">
		
		订单
	</div>
   
   
   
   <?php 

    $listC = $realcts > 9 ? 9 :$realcts;
	
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
		
		$imgUrl  = element('img', $row, '-1');

		/*
			$listall[]=array(
			'vag1'       =>$LowProfit,
			'vag2'       =>$vag2, 
			'datecom'    =>$dateCom,
			'img'        =>'',
			'istoday'    =>$istoday,
			'amount'     =>'¥'.number_format($amount),
			'qty'        =>''.number_format(element('Qty',$rows,'')),
			'counts'     =>$Counts.'',
			'percent'    =>$percent,
			'percolor'   =>$percentcolor,
		);
			
		*/
		$vag1 = element('vag1', $row, '');
		$vag2 = element('vag2', $row, '');
		
		$qty = element('qty', $row, '');
		$cts = element('counts', $row, '');
		$amt = element('amount', $row, '');
		
		$per = element('percent', $row, '');
		
		$percolor = element('percolor', $row, '');
		$forshort = element('forshort', $row, '');
		
		$dateCom = element('datecom', $row, null);

		$istoday = element('istoday', $row, false);
		$lockImg = element('lockImg', $row, '');

		$lock_sImg = element('lock_sImg', $row, '');
		
		
?>
<div style="height: 200px; width: 1080px; margin-top: 0px; position: absolute; top:<?php echo(130+179*$i)?>px">
	
	
	<?php 
			if ($imgUrl != -1) { 
				?>
				<div style="width: 105px; height: 105px; position:absolute; top: <?php echo($imgUrl==''?'27':'37') ?>px; left: 45px">			
		
		<img width="105" height="105" src="<?php echo($imgUrl==''?'images/noImage.png':$imgUrl) ?>">
		</div>
				<?php
					
			}
			
			if ($imgUrl == '' && $forshort != '') {
				
			
			
		?>
		
	
		<div style="position: absolute; width: 165px; height: 50px; left: 15px; top: 125px; text-align: center;">
		<font  style="font-size: 23px;  font-family: 'PingFang_Light'; color: #727171;"><?php echo($forshort) ?></font>
		</div>
		
<?php 
	
	}
			
	
	if ($dateCom != null && count($dateCom)>1) {
		
		
		$j = count($dateCom);
		$todaycolorstyle = " 
		-webkit-box-shadow:0 0 5px 5px #ffff2e;  
  -moz-box-shadow:0 0 5px 5px #ffff2e;  
  box-shadow:0 0 5px 5px #ffff2e;
		";
		for ($iter=0; $iter<$j; $iter++) {
		?>
		
		<div style="position: absolute;top:64px; left:<?php echo (($iter*68+45).'px') ?>; text-align: center; vertical-align: middle; width:64px;height:49px;background:#fff;color:#727171;
border-radius: 10px; border:#dfdfdf 1px solid; <?php echo ($istoday==true ? $todaycolorstyle: '') ?>" >
			<div style="position: absolute; top:0px; left:-1px; text-align: center; vertical-align: middle; width:64px;height:49px;">
			<font style="font-family: 'PingFang_Regular'; font-size: 34px; ">
			<?php echo ($dateCom[$iter]) ?>
			</font>
			</div>
		</div>
		
		
		
		<?php
		}
		
	}
?>
		
		<?php 
			if ($vag1 != '') {
				?>
				<div style="position: absolute; width: 30px; height: 30px; left: 210px; top: 130px; text-align:center; vertical-align: middle; color: #ffffff; font-family: 'VAGRoundedStd-Bold'; font-size: 20px; background-color: #FF6665; border-radius: 4px;">
			<div style="top:5px; position: absolute; width: 30px; height: 28px; left: 0px;">
		 <font ><?php echo ($vag1) ?></font>
			</div>
		</div>
				<?php
			}
		?>
		
		
		<?php 
			if ($vag2 != '') {
				?>
				<div style="position: absolute; width: 30px; height: 30px; left: 250px; top: 130px; text-align:center; vertical-align: middle; color: #ffffff; font-family: 'VAGRoundedStd-Bold'; font-size: 20px; background-color: #9CA3AA; border-radius: 4px;">
			<div style="top:5px; position: absolute; width: 30px; height: 28px; left: 0px;">
		 <font ><?php echo ($vag2) ?></font>
			</div>
		</div>
				<?php
			}
		?>
		
		
		<?php 
			if ($lockImg != '') {
				?>
				
		 <div style="position: absolute; width: 30px; height: 30px; left: 290px; top: 130px;"><img  width="29" height="29" src="images/order_new/<?php echo($lockImg)?>.png"/></div>	
		 
				<?php
			}
		?>
		
		<?php 
			if ($lock_sImg != '') {
				?>
				  <div style="position: absolute; width: 30px; height: 30px; left: 324px; top: 130px;"><img  width="29" height="29" src="images/order_new/<?php echo($lock_sImg)?>.png"/></div>	

				<?php
			}
		?>
		
		
		
		
		
		
		
		<div style="position: absolute; width: 200px; height: 50px; right: 510px; top: 65px; text-align: right;">
		<font  style="font-size: 34px;  font-family: 'PingFang_Regular'; color: #3a3e41;"><?php echo($qty) ?></font>
		</div>
		<div style="position: absolute; width: 50px; height: 50px; right: 445px; top: 69px; text-align: left;">
		 <font style="font-size: 29px;  font-family: 'PingFang_Regular'; color: #717171;"><?php echo ($cts) ?></font>
		</div>
		
		
		<div style="position: absolute; width: 120px; height: 50px; right: 245px; top:  <?php echo ($i==0?'75px':'69px') ?>; text-align: center;  font-family: 'PingFang_Regular'; color: <?php echo ($percolor) ?>; ">
		 <font style="font-size: 30px; "><?php echo ($per) ?></font>
		 <font style="font-size: 15px; "><?php echo ('%') ?></font>
		</div>
		
		<div style="position: absolute; width: 200px; height: 50px; right: 40px; top: 65px; text-align: right;">
		<font  style="font-size: 34px;  font-family: 'PingFang_Regular'; color: #3a3e41;"><?php echo($amt) ?></font>
		</div>
		
    
	   <div style="width: 1080px;height: 1px; background-color:#e7f1f7;display: block  ;position: absolute;top: 179px;"></div>

</div>

<?php 
	}
	 //
	if ($realcts>9) {
		
		
		?>
		
		<div style="z-index: 1000; background-color: #ffffff;position: absolute; width: 187px; height: 179px; left: 0px; bottom: 0px; display: block;">
		
		<div style="position: absolute; width: 187px; height: 50px; left: 0px; bottom: 80px; text-align: center; ">
		<font  style="font-size: 36px;  font-family: 'PingFang_Regular'; color: #3a3e41;"><?php echo('未显示') ?></font>
		</div>
		<div style="position: absolute; width: 187px; height: 50px; left: 0px; bottom: 40px; text-align: center;">
		<font  style="font-size: 33px;  font-family: 'PingFang_Regular'; color: #3a3e41;"><?php echo(($realcts-9)) ?></font>
		</div>
		

		<div style="width: 1px;height: 179px; background-color:#e7f1f7;display: block  ;position: absolute;bottom:  0px; left: 187px;"></div>
</div>
		<div id='sub_show' style="position: absolute; width: 1080px;height: 179px;bottom: 0px; z-index: 900 ; <?php echo($animatedStrings) ?> ">
		
		<?php
		
		
		$fakei = $i;
		$fakeiLimit = $i+4;
		
		for ($i; $i<$realcts; $i++) {
			
			$row     = $list[$i];
		
		$imgUrl  = element('img', $row, null);

		/*
			$listall[]=array(
			'vag1'       =>$LowProfit,
			'vag2'       =>$vag2, 
			'datecom'    =>$dateCom,
			'img'        =>'',
			'istoday'    =>$istoday,
			'amount'     =>'¥'.number_format($amount),
			'qty'        =>''.number_format(element('Qty',$rows,'')),
			'counts'     =>$Counts.'',
			'percent'    =>$percent,
			'percolor'   =>$percentcolor,
		);
			
		*/
		$vag1 = element('vag1', $row, '');
		$vag2 = element('vag2', $row, '');
		
		$qty = element('qty', $row, '');
		$cts = element('counts', $row, '');
		$amt = element('amount', $row, '');
		
		$per = element('percent', $row, '');
		
		$percolor = element('percolor', $row, '');
		
		$dateCom = element('datecom', $row, null);

		$istoday = element('istoday', $row, false);
		$lockImg = element('lockImg', $row, '');

		$lock_sImg = element('lock_sImg', $row, '');

			?>
			
					
			
			
			<div style="height: 179px; width: 223px; bottom: 0px; position: absolute; left:<?php echo(187+223*($i-9))?>px">
			
			
			<?php
			if ($imgUrl != null) {
				?>
				<div style="width: 60px; height: 60px; position:absolute; top: 20px; left: 82px">			
		
		<img width="60" height="60" src="<?php echo($imgUrl==''?'images/noImage.png':$imgUrl) ?>">
		</div>
				<?php
			}
		?>

			<div style="position: absolute; width: 240px; height: 50px; right: 80px; top: 120px; text-align: right;">
		<font  style="font-size: 26px;  font-family: 'PingFang_Regular'; color: #3a3e41;"><?php echo($amt) ?></font>
		</div>
		
			<div style="position: absolute; width: 240px; height: 50px; right: 80px; top: 85px; text-align: right;">
		<font  style="font-size: 26px;  font-family: 'PingFang_Regular'; color: #3a3e41;"><?php echo($qty) ?></font>
		</div>
		
			<div style="position: absolute; width: 240px; height: 50px; left: 155px; top: 88px; text-align: left;">
		<font  style="font-size: 22px;  font-family: 'PingFang_Regular'; color: #727171;"><?php echo($cts) ?></font>
		</div>
		
			
			<div style="width: 1px;height: 179px; background-color:#e7f1f7;display: block  ;position: absolute;bottom:  0px; left: 223px;"></div>

</div>
			<?php
			
		}
		
		
		?>
		
		<?php
		
		
		
		
// 		$fakeSHowi = ceil(($realcts-9)/4);
		
		$fakeSHowi = 4*($pages-1);
		for ($i=$fakei; $i<$fakeiLimit && $i< $realcts; $i++) {
			
			$row     = $list[$i];
		
		$imgUrl  = element('img', $row, null);

		/*
			$listall[]=array(
			'vag1'       =>$LowProfit,
			'vag2'       =>$vag2, 
			'datecom'    =>$dateCom,
			'img'        =>'',
			'istoday'    =>$istoday,
			'amount'     =>'¥'.number_format($amount),
			'qty'        =>''.number_format(element('Qty',$rows,'')),
			'counts'     =>$Counts.'',
			'percent'    =>$percent,
			'percolor'   =>$percentcolor,
		);
			
		*/
	
		$vag1 = element('vag1', $row, '');
		$vag2 = element('vag2', $row, '');
		
		$qty = element('qty', $row, '');
		$cts = element('counts', $row, '');
		$amt = element('amount', $row, '');
		
		$per = element('percent', $row, '');
		
		$percolor = element('percolor', $row, '');
		
		$dateCom = element('datecom', $row, null);

		$istoday = element('istoday', $row, false);
		$lockImg = element('lockImg', $row, '');

		$lock_sImg = element('lock_sImg', $row, '');

			?>
			
					
			
			
			<div style="height: 179px; width: 223px; bottom: 0px; position: absolute; left:<?php echo(187+223*($fakeSHowi))?>px">
			
			
			<?php
			if ($imgUrl != null) {
				?>
				<div style="width: 60px; height: 60px; position:absolute; top: 20px; left: 82px">			
		
		<img width="60" height="60" src="<?php echo($imgUrl==''?'images/noImage.png':$imgUrl) ?>">
		</div>
				<?php
			}
		?>

			<div style="position: absolute; width: 240px; height: 50px; right: 80px; top: 120px; text-align: right;">
		<font  style="font-size: 26px;  font-family: 'PingFang_Regular'; color: #3a3e41;"><?php echo($amt) ?></font>
		</div>
		
			<div style="position: absolute; width: 240px; height: 50px; right: 80px; top: 85px; text-align: right;">
		<font  style="font-size: 26px;  font-family: 'PingFang_Regular'; color: #3a3e41;"><?php echo($qty) ?></font>
		</div>
		
			<div style="position: absolute; width: 240px; height: 50px; left: 155px; top: 88px; text-align: left;">
		<font  style="font-size: 22px;  font-family: 'PingFang_Regular'; color: #727171;"><?php echo($cts) ?></font>
		</div>
		
			
			<div style="width: 1px;height: 179px; background-color:#e7f1f7;display: block  ;position: absolute;bottom:  0px; left: 223px;"></div>

</div>
			<?php
				
					$fakeSHowi++;
			
		}
		
		
		?>

		
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
	
	
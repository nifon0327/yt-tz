<!DOCTYPE html>
<?php

$this->load->helper('html');
$this->load->helper('url');
 
$url = base_url().'views';

$stopBlueAngle = -135 - ($blueIndex/100)*0.75*360;
$stopBlueNextAngle = $stopBlueAngle - ($blueNextIndex/100)*0.75*360;

$nowMon = date('M');
$nowDay = 'Today';

$blueIndexShow = intval($blueIndex+0.5);
$blueNextIndexShow = intval($blueNextIndex+0.5);
$redIndexShow = 100-$blueIndexShow-$blueNextIndexShow;

$greenIndexShow = intval($greenIndex+0.5);
?>
<base href="<?php  echo $this->config->item('web_base_url');?>"/>
<meta http-equiv="refresh" content="60">
<script src='js/jquery.js' type='text/javascript'></script>
<link rel='stylesheet' href='css/dzslides.css'>
<link rel='stylesheet' href='css/cktv.css'>
<script src='js/circlejs/raphael.js' type='text/javascript'></script>
<script  type='text/javascript'>

			var o = {
	initx: function(){
		this.diagram();
	},
	diagram: function(){
		var six = 1.15;
		var r = Raphael('diagram', 900*six, 900*six),
			rad = 73,
			defaultText = '78',
			speed = 250;
		
		r.circle(450*six, 450*six, 85).attr({ stroke: 'none', fill: 'none' });
		
		var title = r.text(440*six, 400.5*six, '<?php echo($greenIndexShow) ?>').attr({
			font: '110px PingFang_Regular',
			fill: '#4bdd32'
		}).toFront();
		
		lent =  <?php echo(strlen($greenIndexShow)) ?>;
		beginx = lent > 2 ? 545 : 520;
		
		var title2 = r.text(beginx*six, 411*six, '%').attr({
			font: '60px PingFang_Regular',
			fill: '#4bdd32'
		}).toFront();
		
		var title = r.text(120*six, 100*six, '<?php echo($blueIndexShow) ?>').attr({
			font: '110px PingFang_Regular',
			fill: '#358fc1'
		}).toFront();
		lent =  <?php echo(strlen($blueIndexShow)) ?>;
		beginx = lent > 1 ? 200 : 180;
		var title2 = r.text(beginx*six, 110.5*six, '%').attr({
			font: '60px PingFang_Regular',
			fill: '#358fc1'
		}).toFront();
		
		var title = r.text(780*six, 100*six, '<?php echo($blueNextIndexShow) ?>').attr({
			font: '110px PingFang_Regular',
			fill: '#c7e0ed'
		}).toFront();
		
		var lent =  <?php echo(strlen($blueNextIndexShow)) ?>;
		var beginx = lent > 1 ? 860 : 840;
		var title2 = r.text(beginx*six, 110.5*six, '%').attr({
			font: '60px PingFang_Regular',
			fill: '#c7e0ed'
		}).toFront();
		
		var title2 = r.text(456*six, 474*six, '<?php echo($centerQty) ?>').attr({
			font: '60px PingFang_Regular',
			fill: '#000000'
		}).toFront();
		
		
		var title = r.text(640*six, 780*six, '<?php echo($redIndexShow) ?>').attr({
			font: '110px PingFang_Regular',
			fill: '#fd3131'
		}).toFront();
		lent =  <?php echo(strlen($redIndexShow)) ?>;
		beginx = lent > 1 ? 720 : 700;
		var title2 = r.text(beginx*six, 790.5*six, '%').attr({
			font: '60px PingFang_Regular',
			fill: '#fd3131'
		}).toFront();
		
		r.customAttributes.arc = function(value, color, rad, beg){
			var v = 3.6*value,
				alpha = v == 360 ? 359.99 : v,
				random = beg ,
				a = (random-alpha) * Math.PI/180,
				b = random * Math.PI/180,
				sx = 517.5 + rad * Math.cos(b),
				sy = 517.5 - rad * Math.sin(b),
				x = 517.5 + rad * Math.cos(a),
				y = 517.5 - rad * Math.sin(a),
				path = [['M', sx, sy], ['A', rad, rad, 0, +(alpha > 180), 1, x, y]];
			return { path: path, stroke: color }
		};
		
		var rds = 350;
		var linew = 60;
		six = 1.15;
		r.path().attr({ arc: [<?php echo($blueIndex*0.75) ?>, '#358fc1', rds*six,'-135'], 'stroke-width': linew });
		r.path().attr({ arc: ['<?php echo($blueNextIndex*0.75) ?>', '#c7e0ed', rds*six,'<?php echo($stopBlueAngle) ?>'], 'stroke-width': linew });
		r.path().attr({ arc: ['<?php echo($redIndex*0.75) ?>', '#fd0300', rds*six,'<?php echo($stopBlueNextAngle) ?>'], 'stroke-width': linew });

		r.path().attr({ arc: ['<?php echo($greenIndex*0.75) ?>', '#4bdd32', rds/240*206*six,'-135'], 'stroke-width': linew*0.4 });
	
/*
		
			var p1 = r.path('M517.5 950 L505 967 L505 993 L529 993 L529 967 Z').attr({stroke:'none',fill:"#fd0300"});  
         
		 //  var s = r.rect(450*six,450*six,22,480).attr({fill:"none",stroke:"none"});
p1.rotate((360-50),450*six,450*six);
*/
	}
}
$(function(){ o.initx(); });



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

 <section id='Section1'>
<table width="1080" class="nomargin">
	<tr class="nomargin">
		<td width="125" bgcolor="#6eaed1" class="nomargin">
			<img class="margin10" width="95" height="95" src="images/inware_w.png">
		</td>
		<td height="125" >
			<table width="900">
				<tr>
					<td>
						<div class="alignR">
							<font class="topFontB"><?php echo($ckQty) ?></font>
							<font class="topFontS">/<?php echo($ckStuffCount) ?></font>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<table  width="1080px" bgcolor="#e7f1f7" style="border-width: 0px;">
	<tr class="fontBlack34">
		<td width="120" height="75">
			<font >&nbsp;&nbsp;入库</font>
		</td>
		<td width="400" height="75">
			<div class="alignC">
				<font class="fontBlack32"><?php echo($rkQty) ?></font>
				<font class="fontGray30">(<?php echo($rkCount) ?>)</font>
			</div>
		</td>
		<td width="20">
			<table style="height:60px;border-color:#dceaf3;border-left-style:solid;border-width:1px"><tr><td valign="top"></td></tr></table> 
		</td>
		<td width="120" height="75">
			<font >&nbsp;&nbsp;备品</font>
		</td>
		<td width="400" height="75">
			<div class="alignC">
				<font class="fontBlack32"><?php echo($blQty) ?></font>
				<font class="fontGray30">(<?php echo($blCount) ?>)</font>
			</div>
		</td>
	</tr>
</table>

<div id='diagram' class='wrapcz'></div>

<hr style="height:0.5px;border:none;border-top:1px solid #e7f1f7;margin-top: 10px" />


<table style="margin-top: 26pt" width="1080" >
	<tr>
		<td width="350" style="height:416px">
			<table width="340" style="margin-left: 18px">
				<tr >
					<td width="80" height="120">
						<img style="margin-left: 7pt;margin-top: 2pt;" width="50" height="50" src="images/ck_bld.png">
					</td>
					<td width="260" height="120">
						<font class="fontRed45">补料单</font>
					</td>
				</tr>
	
				<tr >
					<td width="80" height="60">&nbsp;</td>
					<td width="260" height="60">
						<font class="fontGray30">待补</font>
					</td>
				</tr>
				<tr >
					<td width="80" height="60">&nbsp;</td>
					<td width="260" height="60">
						<font class="fontBlack40"><?php echo($waitbuQty) ?></font>
						<font class="fontGray30">(<?php echo($waitbuCount) ?>)</font>
					</td>
				</tr>
				
				<tr >
					<td width="80" style="height:14px"></td>
					<td width="260" style="height:14px">
					</td>
				</tr>
				<tr >
					<td width="80" height="60">&nbsp;</td>
					<td width="260" height="60">
						<font class="fontGray30">已补</font>
					</td>
				</tr>
				<tr >
					<td width="80" height="60">&nbsp;</td>
					<td width="260" height="60">
						<font class="fontBlack40"><?php echo($buQty) ?></font>
						<font class="fontGray30">(<?php echo($buCount) ?>)</font>
					</td>
				</tr>
			</table>
		</td>
		
		<td width="15"  style="height:416px">
			<table style="height:200px;border-color:#dceaf3;border-left-style:solid;border-width:1px;margin-top: 40px;"><tr><td valign="top"></td></tr></table> 
		</td>
		
		<td width="350" style="height:416px">
			<table width="340" style="margin-left: 1px">
				<tr >
					<td width="80" height="120">
						<img style="margin-left: 7pt;margin-top: 2pt;" width="50" height="50" src="images/ck_bp.png">
					</td>
					<td width="260" height="120">
						<font class="fontBlack45">备品</font>
					</td>
				</tr>
		
	   
				<tr >
					<td width="80" height="40">&nbsp;</td>
					<td width="260" height="40">
						<font class="fontGray30"><?php echo($nowDay) ?></font>
					</td>
				</tr>
				<tr >
					<td width="80" height="60">&nbsp;</td>
					<td width="260" height="60">
						<font class="fontBlack40"><?php echo($todayBpQty) ?></font>
						<font class="fontGray30">(<?php echo($todayBpCount) ?>)</font>
					</td>
				</tr>
				
				<tr >
					<td width="80" style="height:14px"></td>
					<td width="260" style="height:14px">
					</td>
				</tr>
				<tr >
					<td width="80" height="60">&nbsp;</td>
					<td width="260" height="60">
						<font class="fontGray30"><?php echo($nowMon) ?></font>
					</td>
				</tr>
				<tr >
					<td width="80" height="60">&nbsp;</td>
					<td width="260" height="60">
						<font class="fontBlack40"><?php echo($monthBpQty) ?></font>
						<font class="fontGray30">(<?php echo($monthBpCount) ?>)</font>
					</td>
				</tr>
			</table>
		</td>

		
		<td width="15" style="height:416px">
			<table style="height:200px;border-color:#dceaf3;border-left-style:solid;border-width:1px;margin-top: 40px;"><tr><td valign="top"></td></tr></table> 
		</td>
		
		<td width="350" style="height:416px">
			<table width="340" style="margin-left: 1px">
				<tr >
					<td width="80" height="120"><img style="margin-left: 7pt;margin-top: 2pt;" width="50" height="50" src="images/ck_bf.png"></td>
					<td width="260" height="120"><font class="fontBlack45">报废</font></td>
				</tr>
				
				<tr >
					<td width="80" height="40">&nbsp;</td>
					<td width="260" height="40">
						<font class="fontGray30"><?php echo($nowDay) ?></font>
					</td>
				</tr>
				<tr >
					<td width="80" height="60">&nbsp;</td>
					<td width="260" height="60">
						<font class="fontBlack40"><?php echo($todayBfQty) ?></font>
						<font class="fontGray30">(<?php echo($todayBfCount) ?>)</font>
					</td>
				</tr>
				
				<tr >
					<td width="80" style="height:14px"></td>
					<td width="260" style="height:14px">
					</td>
				</tr>
				<tr >
					<td width="80" height="60">&nbsp;</td>
					<td width="260" height="60">
						<font class="fontGray30"><?php echo($nowMon) ?></font>
					</td>
				</tr>
				<tr >
					<td width="80" height="60">&nbsp;</td>
					<td width="260" height="60">
						<font class="fontBlack40"><?php echo($monthBfQty) ?></font>
						<font class="fontGray30">(<?php echo($monthBfCount) ?>)</font>
					</td>
				</tr>
			</table>
		</td>

		
		
	</tr>
	
	
</table>
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
	
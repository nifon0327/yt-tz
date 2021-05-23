<!DOCTYPE html>
<?php
   $this->load->helper('html');
   $this->load->helper('url');
?>

<base href="<?php  echo $this->config->item('web_base_url');?>"/>
<meta http-equiv="refresh" content="30">
<script src='js/jquery.js' type='text/javascript'></script>
<link rel='stylesheet' href='css/dzslides.css'>
<link rel='stylesheet' href='css/tv.css'>

<?php
	$topPx = 0;
	if($part == 'head'){
		$topPx = 215;
?>
<section id='Section1'>
<table width="1080" class="nomargin">
	<tr class="nomargin">
		<td width="125" class="nomargin">
			<img width="110" height="125"  style="margin-left: 12px;width: auto;height: 100%; align-content: center;" src="<?php echo "$personImg"?>"> 
		</td>
		<td style="height: 125;" width="220">
			<table width="220" style="margin-top: 5px; margin-left: 12px;">
				<tr>
					<td style="height: 69px; width: 50px;">
						<div style="margin-top: 10px;">
						<img width="40" height="40" src="images/workshop/ws_<?php echo(''.$shopId) ?>.png"></div>
					</td>
					<td style="height: 69; width: 170px;">
						<div style="margin-top: 10px;">
						<font class="fontBlack32"><?php echo(''.$shopTitle) ?></font>
						</div>
					</td>
				</tr>
	      
				<tr>
					<td style="height: 20; width: 50px;">
					</td>
					<td style="height: 20;">
					</td>
				</tr>
				<tr>
					<td style="height: 42; width: 50px;">
						<div style="margin-top: 0px;">
						<img width="38" height="38" src="images/group_staff.png"></div>
					</td>
					<td style="height: 42;">
						<font class="fontGray25"><?php echo(''.$personInfo) ?></font>
					</td>
				</tr>
			</table>
		</td>
		<td height="125" width="680">
			<table width="680">
				<tr>
					<td>
						<div class="alignR">
							<font class="topFontB"><?php echo($overQty) ?></font>
							<font class="topFontS">/<?php echo($allQty) ?></font>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<table  width="1080px" bgcolor="#e7f1f7" style="border-width: 0px;">
	<tr class="fontBlack34">
		<td width="100" height="75">
			<font >&nbsp;&nbsp;逾期</font>
		</td>
		<td width="240" height="75">
			<div class="alignC">
				<font class="fontRed30"><?php echo($overQty) ?></font>
				<font class="fontGray25">(<?php echo($overCount) ?>)</font>
			</div>
		</td>
		<td width="20">
			<table style="height:60px;border-color:#dceaf3;border-left-style:solid;border-width:1px"><tr><td valign="top"></td></tr></table> 
		</td>
		<td width="100" height="75">
			<font >本周</font>
		</td>
		<td width="240" height="75">
			<div class="alignC">
				<font class="fontBlack30"><?php echo($curQty) ?></font>
				<font class="fontGray25">(<?php echo($curCount) ?>)</font>
			</div>
		</td>
		<td width="20">
			<table style="height:60px;border-color:#dceaf3;border-left-style:solid;border-width:1px"><tr><td valign="top"></td></tr></table> 
		</td>
		<td width="100" height="75">
			<font ><?php echo(substr($this->ThisWeek+1,4,2))?>周+</font>
		</td>
		<td width="240" height="75">
			<div class="alignC">
				<font class="fontBlack30"><?php echo($weekQty) ?></font>
				<font class="fontGray25">(<?php echo($weekCount) ?>)</font>
			</div>
		</td>
	</tr>
</table>
<hr style="height:0.5px;border:none;border-top:1px solid #e7f1f7;margin-top: 0px" />

<?php 
	}

	$listC = count($list);
	$listCountBase = $part == 'head' ? 12 : 13;
	$listC = $listC > $listCountBase ? $listCountBase : $listC;
	for ($i=0;$i<$listC;$i++) {
		
		$row     = $list[$i];
		$cName   = $row['cName'];
		$week    = $row['week'];
		$week    = strlen($week) >= 2 ? $week : '00';
		$week0   = substr($week, 0, 1);
		$week1   = substr($week, 1, 1);
		$imgUrl  = $row['imgUrl'];
		$qty     = $row['qty'];
		$time    = $row['time'];
		$process = $row['process'];
		
		$isOver  = $row['isOver']=='1' ? true : false;
	
?>
<div style="height: 142px; width: 1080px; margin-top: 0px; position: absolute; top:<?php echo($topPx+142*$i)?>px">

		<div style="width: 105px;height: 105px; position: absolute;top:18px;left: 18px;">
				<img style="margin-left: 0px; margin-top: 0px" width="105" height="105" src="<?php echo "$imgUrl"?>">
		</div>
	
					<div style="width: 50px;height: 68px; position: absolute;top:20px;left: 145px;">
						<div class="week3 bgcolor_<?php echo($isOver ? 'red':'black')?>" style="vertical-align: bottom;">
							<div><?php echo($week0) ?></div>
							<div style="margin-left: -2px"><?php echo($week1) ?></div>
						</div>
							
					</div>

						<div style="display: block; word-break: break-all;width: 670px;
							 position: absolute;top:20px;left: 205px;height: 68px;
      text-overflow:ellipsis; 
      white-space:nowrap; 
      overflow:hidden;">
						<font class="fontBlack30"><?php echo($cName) ?></font>
						</div>

					<div style="width: 160px;position: absolute;top:20px;right: 15px;">
						<font class="fontBlack30"  style="float: right"><?php echo($qty) ?></font>
					</div>
					
				
					<div style="width: 670px;height: 40px;position: absolute;top:100px;left: 205px;">
						<div style="margin-left: 5px; float: left;">
						<img width="35" height="35" src="images/<?php echo((isset($process['1'])==true && intval($process['1'])==1) ? '01':'001') ?>.png"></div>
						<div style="margin-left: 5px; float: left;">
						<img width="35" height="35" src="images/<?php echo((isset($process['2'])==true && intval($process['2'])==2) ? '02':'002') ?>.png"></div>
						<div style="margin-left: 5px; float: left;">
						<img width="35" height="35" src="images/<?php echo((isset($process['3'])==true && intval($process['3'])==3) ? '03':'003') ?>.png"></div>
						<div style="margin-left: 5px; float: left;">
						<img width="35" height="35" src="images/<?php echo((isset($process['4'])==true && intval($process['4'])==4) ? '04':'004') ?>.png"></div>
					</div>
					<div style="width: 160px;height: 40px;position: absolute;top:95px;right: 15px;">
						<font class="fontGray28" style="float: right"><?php echo($time) ?></font>
					</div>
			<div style="width: 1080px;height: 1px; background-color:#e7f1f7;display: block  ;position: absolute;top: 141px;"></div>
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
	
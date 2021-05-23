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
	        
<section id='Section1'>
<table width="1080" class="nomargin">
	<tr class="nomargin">
		<td width="125" bgcolor="#6eaed1" class="nomargin">
			<img class="margin10" width="95" height="95" src="images/out.png">
		</td>
		<td height="125" >
			<table width="900">
				<tr>
					<td>
						<div class="alignR">
							<font class="topFontB" style="color: #71bede;"><?php echo($waitQty) ?></font>
							<font class="topFontS">/<?php echo($sendQty) ?></font>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<table  width="1080px" bgcolor="#e7f1f7" style="border-width: 0px;">
	<tr class="fontBlack34">
		<td width="130" height="75">
			<font >&nbsp;&nbsp;生产中</font>
		</td>
		<td width="400" height="75">
			<div class="alignC">
				<font class="fontBlue32" style="color: #71bede;"><?php echo($scingQty) ?></font>
				<font class="fontGray30">(<?php echo($scingCount) ?>)</font>
			</div>
		</td>
		<td width="20">
			<table style="height:60px;border-color:#dceaf3;border-left-style:solid;border-width:1px"><tr><td valign="top">
			</td></tr></table> 
		</td>
		<td width="120" height="75">
			<font >&nbsp;&nbsp;待出</font>
		</td>
		<td width="400" height="75">
			<div class="alignC">
				<font class="fontGreen32"><?php echo($scedQty) ?></font>
				<font class="fontGray30">(<?php echo($scedCount) ?>)</font>
			</div>
		</td>
	</tr>
</table>

<?php 
	
	$listCount = count($list);
	for ($i=0;$i<$listCount;$i++) {
		
		$row = $list[$i];
		
		$week    = $row['week'];
		$week    = strlen($week) >= 2 ? $week : '00';
		$week0   = substr($week, 0, 1);
		$week1   = substr($week, 1, 1);
		
?>
<div style="height: 200px; width: 1080px; margin-top: 0px; position: absolute; top:<?php echo(209+151*$i)?>px">
	<?php
		
		
		if ($row['scok'] == true) {
			
		
		?>
		<div style="width: 140px; height: 140px; opacity:0.6;position:absolute; top: 10px; left: 770px">			
				<img width="140" height="140" src="images/flag<?php echo($row['flagFix'])?>.png">
				</div>
		<?php 
			}
		?>

		<div style="width: 105px; height: 105px; position:absolute; top: 24px; left: 37px">			
		
		<img width="105" height="105" src="<?php echo($row['imgUrl']) ?>">
		</div>
		
		<div class="week3 bgcolor_<?php echo($row['isOver'] ? 'red':'blue')?>" style="position: absolute; top:25px; left: 170px;">
			<div><?php echo($week0) ?></div><div style="margin-left: 2px"><?php echo($week1) ?></div>
		</div>
		
		<div class="fontBlack32" style="display: block; word-break: break-all;width: 540px; height: 100px;position: absolute;top:23px;left: 230px;
/*
text-overflow:ellipsis; 
white-space:nowrap; 
overflow:hidden;
*/">
		<?php echo($row['cName']) ?>
		</div>
		
		<div style="position: absolute; width: 200px; height: 33px; right: 20px; top: 28px; text-align: right;">
		<font class="fontGreen30" <?php echo(($row['scok'] == false)?"style='color: #71bede;'":'')?> ><?php echo($row['scQty']) ?></font>
		<font class="fontBlack30">/<?php echo($row['Qty']) ?></font>
		</div>
    

		<div class="fontGray28" style="position: absolute; width: 200px; height: 28px; right: 20px; top: 100px; text-align: right; color: <?php echo($row['timeColor'])?>;"><?php echo($row['time']) ?></div>

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
	
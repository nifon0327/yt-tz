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
			<img class="margin10" width="95" height="95" src="images/storage.png">
		</td>
		<td height="125" >
			<table width="900">
				<tr>
					<td>
						<div class="alignR">
							<font class="topFontB" style='color: #01be56;'><?php echo($todayQty) ?></font>
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
			<font >&nbsp;&nbsp;欠尾数</font>
		</td>
		<td width="400" height="75">
			<div class="alignC">
				<font class="fontRed33"><?php echo($scingQty) ?></font>
				<font class="fontGray30">(<?php echo($scingCount) ?>)</font>
			</div>
		</td>
		<td width="20">
			<table style="height:60px;border-color:#dceaf3;border-left-style:solid;border-width:1px"><tr><td valign="top">
			</td></tr></table> 
		</td>
		<td width="120" height="75">
			<font >待入库</font>
		</td>
		<td width="400" height="75">
			<div class="alignC">
				<font class="fontGreen33"><?php echo($scedQty) ?></font>
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
		<div style="width: 140px; height: 140px; opacity:0.6;position:absolute; top: 10px; left: 860px">			
				<img width="140" height="140" src="images/flag<?php echo($row['flagFix'])?>.png">
				</div>
		<?php 
			}
		?>

		<div style="width: 105px; height: 105px; position:absolute; top: 24px; left: 37px">			
		
		<img width="105" height="105" src="<?php echo($row['imgUrl']) ?>">
		</div>
		
		<div class="week3 bgcolor_<?php echo($row['isOver'] ? 'red':'black')?>" style="position: absolute; top:42px; left: 170px;">
			<div><?php echo($week0) ?></div><div style="margin-left: 2px"><?php echo($week1) ?></div>
		</div>
		
		<div class="fontBlack32" style="display: block; word-break: break-all;width: 500px; height: 100px;position: absolute;top:40px;left: 230px;
text-overflow:ellipsis; 
white-space:nowrap; 
overflow:hidden;"><span class='fontBlue32'><?php echo($row['Forshort']);?></span>-<?php echo($row['cName']) ?>
		</div>
		
		<div style="position: absolute; width: 200px; height: 33px; right: 250px; top: 45px; text-align: right;">
		<font class="fontGreen30"><?php echo($row['scQty']) ?></font>
		</div>
		<div style="position: absolute; width: 100px; height: 33px; right: 135px; top: 45px; text-align: left;">
		 <font class="fontBlack30"><?php echo $row['scok']==true?'':$row['Qty']; ?></font>
		</div>
		
     <?php if ($row['ShipType']>0){ ?>
       <div style="position: absolute; width: 200px; height: 28px; right: 20px; top: 5px; text-align: right;"><img  width="30" height="30" src="images/ship/ship<?php echo($row['ShipType']) ?>.png"/></div>
    <?php } ?>

		<div class="fontGray28" style="position: absolute; width: 200px; height: 28px; right: 55px; top: 50px; text-align: right; color: <?php echo($row['timeColor'])?>;">...<?php echo($row['time']) ?></div>
		<div style="position: absolute; width: 200px; height: 28px; right: 20px; top: 54px; text-align: right;"><img  width="30" height="30" src="images/line/<?php echo($row['Line']) ?>1.png"/></div>
		
   <?php if ($row['isCoding']==1){ ?>
       <div style="position: absolute; width: 200px; height: 28px; right: 20px; top: 110px; text-align: right;"><img  width="30" height="30" src="images/qr.png"/></div>
    <?php } ?>
    
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
	
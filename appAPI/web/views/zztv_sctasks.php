<!DOCTYPE html>
<?php
   $this->load->helper('html');
   $this->load->helper('url');
   
 ?>
<head>
<base href="<?php  echo $this->config->item('web_base_url');?>"/>
<meta http-equiv="refresh" content="30;url=../../web.php/zztv/sctasks?wsId=101&Line=<?php echo $Line;?>">
<link rel='stylesheet' href='css/dzslides.css'>
<link rel='stylesheet' href='css/tv.css'>

<script src='js/jquery.js' type='text/javascript'></script>
</head>
 <section id='Section1'>
<table width="1080" class="nomargin">
	<tr class="nomargin">
		<td width="145" class="nomargin">
			<img width="110" height="125"  style="margin-left: 12px;width: auto;height: 100%; align-content: center;" src="<?php echo "$personImg"?>"> 
			
		</td>
		<td style="height: 125;" width="200">
			<table width="200" style="margin-top: 5px;">
				<tr>
					<td style="height: 69px; width: 50px;">
						<div style="margin-top: 10px;">
						<img width="40" height="40" src="images/line/<?php echo($titleImg)?>"></div>
					</td>
					<td style="height: 69;">
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
			</table>
		</td>
		<td height="125" width="700">
			<table width="700">
				<tr>
					<td>
						<div class="alignR">
							<font class="topFontB" style="color: #01be56"><?php echo($dayQty) ?></font>
							<font class="topFontS">&nbsp;&nbsp;<?php echo($allQty) ?></font>
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
			<font >已登</font>
		</td>
		<td width="240" height="75">
			<div class="alignC">
				<font class="fontGreen30"><?php echo($scedQty) ?></font>
				<font class="fontBlack30"><?php echo($scinqQty) ?></font>
			</div>
		</td>
		<td width="20">
			<table style="height:60px;border-color:#dceaf3;border-left-style:solid;border-width:1px"><tr><td valign="top"></td></tr></table> 
		</td>
		<td width="100" height="75">
			<font >已配</font>
		</td>
		<td width="240" height="75">
			<div class="alignC">
				<font class="fontBlack30"><?php echo($bledQty) ?></font>
				<font class="fontGray25">(<?php echo($bledCount) ?>)</font>
			</div>
		</td>
	</tr>
</table>
<hr style="height:0.5px;border:none;border-top:1px solid #e7f1f7;margin-top: 0px" />

<?php 
	
	$listC = count($list);
    $listC = $listC > 11 ? 11 :$listC;
	
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
		$isOver  = $row['isOver']=='1' ? true : false;
?>
<div style="height: 200px; width: 1080px; margin-top: 0px; position: absolute; top:<?php echo(209+151*$i)?>px">
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
		
		<div style="position: absolute; width: 200px; height: 33px; right: 300px; top: 45px; text-align: right;">
		<font class="fontGreen30"><?php echo($row['scqty']==0?'':$row['scqty']) ?></font>
		</div>
		<div style="position: absolute; width: 100px; height: 33px; right: 190px; top: 45px; text-align: left;">
		 <font class="fontBlack30"><?php echo ($row['qty']) ?></font>
		</div>
	  <?php if ($row['Remark']!=''){ ?>
	     <div style="position: absolute; width:910px; height: 45px;line-height:45px;vertical-align: middle; left: 170px; top: 103px; text-align: left;background:#F3F8FB;font-family: 'NotoSansHans-Light'; font-size: 28px; color: #727171;"><img width="35" height="35" src="images/line/<?php echo($titleImg2)?>" style='float:left;margin:5px 0px 0px 5px;'>
	        <font style='float:left;margin-left:10px;'><?php echo ($row['Remark']) ?></font>
	        <font style='float:right;margin-right:20px;'><?php echo ($row['R_time']) ?></font>
	     </div>
	  <?php } ?>	
	  
     <?php if ($row['ShipType']>0){ ?>
       <div style="position: absolute; width: 200px; height: 28px; right: 20px; top: 5px; text-align: right;"><img  width="30" height="30" src="images/ship/ship<?php echo($row['ShipType']) ?>.png"/></div>
    <?php } ?>
		<div class="fontGray28" style="position: absolute; width: 200px; height: 28px; right: 55px; top: 50px; text-align: right; color: <?php echo($row['timeColor'])?>;">...<?php echo($row['time']) ?></div>
		
	 <?php if ($row['isCoding']==1){ ?>	
		<div style="position: absolute; width: 200px; height: 28px; right: 20px; top: 54px; text-align: right;"><img  width="30" height="30" src="images/qr.png"/></div>	
       <?php }else{ ?>
        <div style="position: absolute; width: 200px; height: 28px; right: 20px; top: 54px; text-align: right;"><img  width="30" height="30" src="images/stock.png"/></div>	
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
	
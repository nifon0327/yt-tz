<!DOCTYPE html>
<?php
   $this->load->helper('html');
   $this->load->helper('url');
 ?>
<head>
<base href="<?php  echo $this->config->item('web_base_url');?>"/>
<meta http-equiv="refresh" content="15">
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
						<img width="40" height="40" src="images/<?php echo($titleImg)?>"></div>
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
		<td height="125" width="700">
			<table width="700">
				<tr>
					<td>
						<div class="alignR">
							<font class="topFontB" style="color: #01be56"><?php echo($dayQty) ?></font>
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
		$aimaa = array('A'=>'','B'=>'','C'=>'');
		$snailAll = array('A'=>'0','B'=>'0','C'=>'0');
		$row     = $list[$i];
		$cName   = $row['cName'];
		$week    = $row['week'];
		$week    = strlen($week) >= 2 ? $week : '00';
		$week0   = substr($week, 0, 1);
		$week1   = substr($week, 1, 1);
		$imgUrl  = $row['imgUrl'];
		$qty     = $row['qty'];
		$scqty     = $row['scqty'];
		$time    = $row['time'];
		$process = $row['process'];
		$nameColor= '#000000';
		$isOver  = $row['isOver']=='1' ? true : false;
		
		$psct = count($process);
		$bg = 4-$psct;
		
		$snail = '';
		$snailInd = 0;  
		for ($x=0;$x <$psct; $x++) {
			
			$find = $process[$x];
			$findT = $find['LastTime'];
			$val1 = $find['Qty'];
			$val2 = $find['GxQty'];
$typeid = $find['TypeId'];
$boolneedanima = $typeid == $gxDisplay?true :false;
							
			if ($val2>0 && $val2<$val1 && $findT!='' ) {
				$minutes=floor((strtotime($time2)-strtotime($findT))); 
				if ($minutes > $oneDay) {
					$snail = 'C';
					//$snailInd = $bg+$x;
					$snailInd = $psct-$x;
					if ($boolneedanima) {
											
								$aimaa[$snail]= '-webkit-animation: twinkling 1.5s infinite ease-in-out;

';
					}
					break;
				} else if ($minutes > $hour4) {
					$snail = 'B';
					//$snailInd = $bg+$x;
					$snailInd = $psct-$x;
					if ($boolneedanima) {
											
								$aimaa[$snail]= '-webkit-animation: twinkling 1.5s infinite ease-in-out;

';
					}
					break;
				} else if ($minutes > $min30)  {
					$snail = 'A';
					//$snailInd = $bg+$x;
					$snailInd = $psct-$x;
					if ($boolneedanima) {
											
								$aimaa[$snail]= '-webkit-animation: twinkling 1.5s infinite ease-in-out;

';
					}
					break;
				}
			}
			
		}
	
?>

<table style="height: 136px; margin-top: 0px ;margin-bottom: 0px;">
	<tr>
		<td style="width: 150px;height: 136px">
				<img style="margin-left: 22px; margin-top: 13px" width="105" height="105" src="<?php echo $imgUrl?>">
		</td>
		<td style="width: 920px;height: 136px">
			<table style="height: 150px; margin-top: 0px margin-bottom: 0px;">
				<tr style="vertical-align: bottom;">
					<td style="width: 60px;height: 61px">
						<div class="week3 bgcolor_<?php echo($isOver ? 'red':'black')?>" style="vertical-align: bottom; margin-bottom: 3px">
							<div><?php echo($week0) ?></div><div style="margin-left: 2px"><?php echo($week1) ?></div>
						</div>
							
					</td>
					<td class="fontBlack30" style="width: 540px;">
						<div style="display: block; word-break: break-all;width: 540px;
      text-overflow:ellipsis; 
	  color: <?php echo($nameColor)?>;
      white-space:nowrap; 
      overflow:hidden;">
      <?php echo($cName) ?>
						</div>
					</td>
					<td style="width: 185px;">
						<div style="float: right">
						<font class="fontGreen30"><?php echo($scqty) ?></font>
						<font class="fontBlack30">/<?php echo($qty) ?></font>
						</div>
					</td>
					<td style="width: 130px;">
						<font class="fontGray28" style="float: right"><?php echo($time) ?></font>
					</td>
					
				</tr>
				<tr style="vertical-align: bottom;">
					
					<td style="height: 40px" colspan="4">
						
						
						<?php 
							
							if ($snail!='') {
								
								$snailAll[$snail]='1';
								?>
								<div style="margin-left: <?php echo(920-$snailInd*128)?>px; float: left; width: 39px;height: 40px; <?php echo($aimaa['A'])?> ">
							<img style="margin-left: 0px; margin-top: 11px" width="38" height="38" src="images/snailA<?php echo($snailAll['A'])?>.png">
						</div>
						<div style="margin-left: 1px; float: left; width: 39px;height: 40px; <?php echo($aimaa['B'])?> ">
							<img style="margin-left: 0px; margin-top: 11px" width="38" height="38" src="images/snailB<?php echo($snailAll['B'])?>.png">
						</div>
						<div style="margin-left: 1px; float: left; width: 39px;height: 40px;<?php echo($aimaa['C'])?> ">
							<img style="margin-left: 0px; margin-top: 11px" width="38" height="38" src="images/snailC<?php echo($snailAll['C'])?>.png">
						</div>
								<?php
							}
						?>
						
						
					</td>
				</tr>
				<tr style="vertical-align: bottom;">
					
					<td style="height: 34px" colspan="4">
					    <div style='float:right;'>
						<?php 
							
							
							 
							for ($j=0;$j<$bg;$j++) {
								
								?>
								
						<div style="margin-left: 2px; float: left; width: 227px;height: 34px; background-color: #ffffff; display: block; margin-top: 0px;">
							
							<table style="width: 227px;height:32px; margin-top: 0px;">
								<tr>
									<td width="50px">
										<div class="innerIcon" style="background-color: white; font-family: 'PingFang_Regular';">
								
							</div>
									</td>
									<td>
										<font  style="margin-top: 0px; color: white;  font-family: 'PingFang_Regular'; font-size: 22px; vertical-align: bottom;">
										</font>
										</td>
								</tr>
							</table>
							
						</div>
						
					<?php
								
							}

							for ($j=$bg;$j<4;$j++) {
								
								$oneP = $process[$j-$bg];
								$val1 = $oneP['Qty'];
								$val2 = round($oneP['GxQty']);
								$TypeId = $oneP['TypeId'];
								if ($val2>0){
					                $bgColor=$val2>=$val1?"#43ca79":"#71bede";
					                $val2Color="white";
					            }
					            else{
					                $bgColor = "#c5c5c5";
					                $val2Color = "#bbbbbb";
					            }
								
								
								?>
						
						<div style="margin-left: 2px; float: left; width:128px;height: 58px; background-color: <?php echo($bgColor)?>; display: block; margin-top: 0px;">
							
							<table style="width: 128px;height:58px; margin-top: 0px;">
								<tr>
									<td width="40px">
										<div class="innerIcon" style="background-color: white; font-family: 'PingFang_Regular';">
								<?php echo($TypeId)?>
							</div>
									</td>
									<td>
										<font  style="margin-top: 0px; color: white;  font-family: 'PingFang_Regular'; font-size: 22px; line-height:28px;vertical-align: bottom;">
										<?php echo(round($val1)."<br><span style='color:$val2Color' >" .round($val2)) . "<span>"?>
										</font>
										</td>
								</tr>
							</table>
							
						</div>

						<?php
								
								
							}
						?>
						
					    </div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<hr style="height:0.5px;border:none;border-top:1px solid #e7f1f7;margin-top: 0px" />

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
	
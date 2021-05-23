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
<hr style="height:0.5px;border:none;border-top:1px solid #e7f1f7;margin-top: 0px" />

<?php 
   $stratIndex = 10;
   $limitIndex = 25;
   for ($i=10,$rownums = count($list);$i<$rownums;$i++) {
       $rows = $list[$i];
       $ht = $i - $stratIndex;
?>
      <div style="height: 200px; width: 1080px; margin-top: 0px; position: absolute; top:<?php echo(145*$ht)?>px;">
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
    if ($i>$limitIndex) break;
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
	
	
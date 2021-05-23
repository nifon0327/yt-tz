<!DOCTYPE html>
<?php
$this->load->helper('html');
$this->load->helper('url');
?>

<base href="<?php echo $this->config->item('web_base_url');?>"/>
<meta http-equiv="refresh" content="30">
<script src='js/jquery.js' type='text/javascript'></script>
<link rel='stylesheet' href='css/dzslides.css'>
<link rel='stylesheet' href='css/tv.css'>

<script type="text/javascript" src='js/ichart.1.2.1.src.js'></script>
<script type="text/javascript">
  function writeObj(obj)
  { 
	    var description = ""; 
	    for(var i in obj){   
	        var property=obj[i];   
	        description+=i+" = "+property+"\n";  
	    }   
	    alert(description); 
   } 
   
   $(function(){
			
			var dataSub= [
			         	{
			         		name : '',
			         		value:[<?php echo($valSm)?>],
			         		color:'#ffffff'
			         	},
			         	{
			         		name : '',
			         		value:[<?php echo($allSm)?>],
			         		color:'#ffffff'
			         	}
			         ];
			var chartSub = new iChart.ColumnMulti2D({
			data: dataSub,
			align:'center',
					scaleAlign : 'left',
			sub_option : {
				listeners : {
					parseText : function(r, t) {
						
						
						return Math.round(t/1000)  + "k";
					}
				},
				label : {
					fontsize:17,
					font:'PingFang_Regular',
					fontweight:100,
					color : '#727171'
				}
			},
			column_width:50,
			text_space:0,
			offsetx:-42,
			width : 1260,//设置宽度，默认单位为px
			height : 300,//设置高度，默认单位为px
           label:{color:'#727171',font:'PingFang_Regular',fontsize:12,fontweight:100},
			coordinate : {
				background_color : null,
				grid_color : '#ffffff',
				width:1207,
				height:240,
				axis : {
					enable : false,
				},
				scale : [{
					position : 'left',
					start_scale : 0,
					min_scale:0,
// 						end_scale : 280,
                    max_scale : 200,
					scale_space : 50,
					scale_share : 5,
					scale_enable : false
				}],
				label:{color:'#ffffff',fontsize:10,fontweight:100,font:'PingFang_Regular'}
			}
			});
			var data = [
			         	{
			         		name : 'DPS01A',
			         		value:[<?php echo($valAm)?>],
			         		color:'#71bede'
			         	},
			         	{
			         		name : 'DPS01A',
			         		value:[<?php echo($valPm)?>],
			         		color:'#71bede'
			         	},
			         	{
			         		name : 'DPS01B',
			         		value:[<?php echo($valEm)?>],
			         		color:'#71bede'
			         	}
			         ];
			         var data2 = [
			         	{
			         		name : 'DPS01A',
			         		value:[<?php echo($allAm)?>],
			         		color:'#00aa00'
			         	},
			         	{
			         		name : 'DPS01A',
			         		value:[<?php echo($allPm)?>],
			         		color:'#00aa00'
			         	},
			         	{
			         		name : 'DPS01B',
			         		value:[<?php echo($allEm)?>],
			         		color:'#00aa00'
			         	}
			         ];
			var chart = new iChart.ColumnStacked2D({
					render : 'canvasDiv',
					data: data,
					align:'left',
					sub_option : {
				listeners : {
					parseText : function(r, t) {
// 							writeObj(r);
						return '';
					}
				},
				border : {
					width : 1,
					//radius : '5 5 0 0',//上圆角设置
					color : '#ffffff'
				}
			},
					column_width:37,
/*
					labels:[
					<?php echo($headArr)?>,'          '
					],
*/
					label:{color:'#727171',font:'PingFang_Regular',fontsize:18,fontweight:200},
					width : 1430,
					text_space:1,
					height : 300,
					background_color : '#ffffff',
					legend:{
						enable:false,
						
					},
						coordinate : {
				background_color : null,
				grid_color : '#ffffff',
				width:1270,
				
						height:240,
				axis : {
					enable : false,
				},
				scale : [{
					position : 'left',
					start_scale : 0,
					max_scale : 200,
					scale_space : 50,
					scale_share:5,
					scale_enable : false
				}],
				label:{color:'#ffffff',fontsize:10,fontweight:100}
			}
					
			});
			
			
			
			var chart2 = new iChart.ColumnStacked2D({
					data: data2,
					align:'right',
					scaleAlign : 'right',
					offsetx:-90,
					sub_option : {
				listeners : {
					parseText : function(r, t) {
						return '';
					}
				},
				border : {
					width : 1,
					//radius : '5 5 0 0',//上圆角设置
					color : '#ffffff'
				}
			},
					column_width:37,
					label:{color:'#727171',font:'PingFang_Regular',fontsize:12,fontweight:200},
					width : 1430,
					text_space:1,
					height : 300,
					background_color : '#ffffff',
					legend:{
						enable:false,
						
					},
					coordinate : {
				background_color : null,
				grid_color : '#ffffff',
				width:1270,
						height:240,
				axis : {
					enable : false,
				},
				scale : [{
					position : 'left',
					start_scale : 0,
     				//end_scale : 140,
                    max_scale : 200,
					scale_space : 50,
					scale_share:5,
					scale_enable : false
				}],
				label:{color:'#ffffff',fontsize:10,fontweight:100}
			}
					
			});
			chart.plugin(chartSub);
			chart.plugin(chart2);
				
			chart.draw();
		});

	</script>
	
    <script src='js/circlejs/raphael.js' type='text/javascript'></script>
	<script  type='text/javascript'>

	var o = {
		initx: function(){
			this.diagram();
		},
		diagram: function(){
			var six = 1;
			var r = Raphael('diagram', 340*six, 340*six);
			
			var title = r.text(165*six, 160*six, '<?php  echo($redIndexShow) ?>').attr({
				font: '115px AshCloud61',
				fill: '<?php echo($redColor) ?>'
			}).toFront();
			
			
			lent =  <?php echo(strlen($redIndexShow)) ?>;
			beginx = lent > 1 ? 230 : 200;
			beginx = lent > 2 ? beginx+30:beginx;
			var title2 = r.text(beginx*six, 186*six, '%').attr({
				font: '44px AshCloud61',
				fill: '<?php echo($redColor) ?>'
			}).toFront();
			
					
			var title2 = r.text(170, 228, '<?php echo('日产值') ?>').attr({
				font: '30px PingFang_Regular',
				fill: '#9ca3aa'
			}).toFront();
			
			
			r.customAttributes.arc = function(value, color, rad, beg){
				var v = 3.6*value,
					alpha = v == 360 ? 359.99 : v,
					random = beg ,
					a = (random-alpha) * Math.PI/180,
					b = random * Math.PI/180,
					sx = 160 + rad * Math.cos(b),
					sy = 160 - rad * Math.sin(b),
					x = 160 + rad * Math.cos(a),
					y = 160 - rad * Math.sin(a),
					path = [['M', sx, sy], ['A', rad, rad, 0, +(alpha > 180), 1, x, y]];
				return { path: path, stroke: color }
			};
			
			var rds = 150;
			var linew = 60;
			six = 1.15;
	
		/*
				r.path().attr({ arc: ['100', '#9ca3aa', 128,'-135'], 'stroke-width': 3 });
			r.path().attr({ arc: [<?php echo($blueIndex) ?>, '#358fc1', 128,'-180'], 'stroke-width': 8 });
		
			r.path().attr({ arc: [<?php echo($redIndex) ?>, '<?php echo($redColor) ?>', 113,'-180'], 'stroke-width': 9 });
			
		*/
	
			var all = 70;
			var each = 270 / all;
			var kedu = <?php echo($blueIndex>1?1:$blueIndex) ?>*270;
			var disp = <?php echo($redIndex) ?>*all;
			
			for (var i=0;i<all;i++) {
				
				r.rect(35,170,20,3).attr({fill:i<=disp?'#000000':"#c5c5c5",stroke:"none"}).rotate((-45+each*i),170,170);
			}
			
			
		    var p1 = r.path('M170 313 L165 318 L165 335 L175 335 L175 318 Z').attr({stroke:'none',fill:"#000000"}).rotate((45+kedu),170,170);  
	  }
    }
  $(function(){ o.initx(); });
 </script>


 <section id='Section1'>
	 	<div id='diagram' class="nomargin" style="position: fixed;top: 14px;height: 360px;width: 360px;left: 10px; overflow: visible;"></div>
<table bgcolor="white" width="1080" class="nomargin" style="height: 340px;">
	<tr>
		<td style="width: 340px;">
		
		</td>
		
		<td style="width: 740px;">
			<table width="720">
				<tr>
					<td colspan="2" style="height: 200px">
						<div class="alignR">
							<font class="fontGreen70"><?php echo($daySc) ?></font>
							<font class="fontGreenB70">/<?php echo($monSc) ?></font>
						</div>
					</td>
				</tr>
				<tr>
					<td style="height: 60px; width: 360px;">
						<font class="fontBlack30">日产值</font>
					</td>
					<td>
						<font class="fontBlack30">月产值</font>
					</td>
				</tr>
				<tr>
					<td style="height: 60px;">
						<div style="float: left; margin-left: 0px;">
			  
							<font class="<?php echo($day_color) ?>"><?php echo($dayScRMB) ?></font>
							<font class="fontBlack30">/<?php echo($dayGjRMB) ?></font>
						</div>
						
						<div style="float: left; margin-left: 3px; margin-top: 6px;">
							<img src="images/evaluate.png" style="width: 32px; height: 32px;">
						</div>
					</td>
					<td>
						<div style="float: left; margin-left: 0px;">
							<font class="fontRed30"><?php echo($monScRMB) ?></font>
							<font class="fontBlack30">/<?php echo($monGjRMB) ?></font>
						</div>
						
						<div style="float: left; margin-left: 1px; margin-top: 5px;">
							<img src="images/evaluate.png" style="width: 32px; height: 32px;">
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<hr style="height:0.5px;border:none;border-top:1px solid #e7f1f7;margin-top: 0px" />


<?php 
	
	
	$listC = count($list);
	for ($i=0;$i<$listC;$i++) {
		
	$data1=$list[$i];
	
	
?>
<!-- <br> -->
<!-- 12312412441 -->
<div style="height: 200px; width: 1080px; margin-top: 0px; position: absolute; top:<?php echo(340+231*$i)?>px">
	
			<div style="width: 200px; height: 200px; ">
				<div style="width: 50px; height: 50px; position:absolute; top: 0px; left: 0px">				
				    <img src="images/<?php echo ($i+1)?>.png" style="width: 50px; height: 50px;">
				</div>
				
				<div style="width: 120px; height: 120px; position:absolute; top: 42px; left: 40px;border-radius: 45px;border-radius: 60px; overflow: hidden;">				
					<img  src="<?php echo($data1['personImg'])?>" style="width: 90px; height: 120px; margin-left: 15px; margin-bottom: 0px; background-repeat:repeat-x;">
				</div>
				<font class="fontBlack25" style="position: absolute; top: 168px;width: 190px; text-align: center"><?php echo($data1['name'])?></font>
			</div>
			

	
						<div style="float:right;
		vertical-align: middle;
		margin-top: 0px; position: absolute; top:42px; right: 70px;">
							<font class="fontGreen65"><?php echo($data1['sc1']) ?></font>
							<font class="fontGray65">/<?php echo($data1['dsc1']) ?></font>
						</div>
			
			<div style="float:right;
		vertical-align: middle;
		margin-top: 0px; position: absolute; top: 123px; right: 30px;">
							<font class="fontRed50"><?php echo($data1['left1']) ?></font>
							<font class="fontGray30">(<?php echo($data1['leftCt1']) ?>)</font>
						</div>
			<div style="width: 1080px;height: 1px; background-color:#e7f1f7;display: block  ;position: absolute;top: 230px;"></div>
		
</div>

<?php 
	}
?>

<div style="margin-left: -55px; display: block; width: 1080px; height: 305px; overflow: hidden; margin-bottom: 0px; position: fixed; bottom: 0px;">
		<div id='canvasDiv' style="margin-left: 0px"></div>
		</div>
		
		
		<div style="position: fixed; bottom: 300px; width: 1080px; height: 40px; z-index: 10005;">
			<div style="width: 1080px;height: 1px; background-color:#e7f1f7;display: block  ;position: absolute;top: 0px; "></div>
			<div style="position:absolute; top:10px; width: 14px;height: 14px; display: block; border-radius: 7px; background-color: #71bede; right: 600px;" ></div> 
			<div  style="position:absolute; top:4px; width: 80px;height: 14px; display: block; border-radius: 7px; right: 514px; 	font-size: 18px;
		color: #727171;
		font-family: 'PingFang_Regular'; " >预估</div> 
		
			<div style="position:absolute; top:10px; width: 14px;height: 14px; display: block; border-radius: 7px; background-color: #01be56; right: 500px;" ></div> 
			<div  style="position:absolute; top:4px; width: 80px;height: 14px; display: block; border-radius: 7px; right: 414px; 	font-size: 18px;
		color: #727171;
		font-family: 'PingFang_Regular'; " >实际</div> 
		
		
		<div style="width: 400px;height: 1px; background-color:#e7f1f7;display: block  ;position: absolute;top: 40px; right: 0px;"></div>
		
		<div  style="position:absolute; top:2px; width: 170px;height: 14px; display: block; border-radius: 7px; right: 225px; 	font-size: 14px;
		color: #727171;
		font-family: 'PingFang_Regular'; " >日产值</div> 
		<div  style="position:absolute; top:20px; width: 170px;height: 14px; display: block; border-radius: 7px; right: 225px; 	font-size: 13px;
		font-family: 'PingFang_Regular'; " >
			<font style="color: #ff0000"><?php echo($dayScRMB) ?></font>
			<font style="color: #000000">/<?php echo($dayGjRMB) ?></font>
		</div> 
		<div  style="position:absolute; top:2px; width: 225px;height: 14px; display: block; border-radius: 7px; right: 1px; 	font-size: 14px;
		color: #727171;
		font-family: 'PingFang_Regular'; " >月产值</div> 
		<div  style="position:absolute; top:20px; width: 225px;height: 14px; display: block; border-radius: 7px; right: 1px; 	font-size: 13px;
		font-family: 'PingFang_Regular'; " >
			<font style="color: #ff0000"><?php echo($monScRMB) ?></font>
			<font style="color: #000000">/<?php echo($monGjRMB) ?></font>
		</div> 
		
		<div style="width: 1px;height: 40px; background-color:#e7f1f7;display: block  ;position: absolute;top: 0px; right: 400px;"></div>
		<div style="width: 1px;height: 40px; background-color:#e7f1f7;display: block  ;position: absolute;top: 0px; right: 231px;"></div>
		
		</div>
		
<div style="position: fixed;bottom: 10px;left: 35px; height: 20px;width: 1040px; display: block">
	
	
	<?php 

		$c = count($headArr);
		$c = $c > 7 ? 7 : $c;
		for ($i=0; $i<$c; $i++) {
			$eachT = ( $headArr[$i] );
	?>
		<div style="float: left;width: 145px;height: 20;color:<?php echo($eachT['color'])?>;font-family: 'PingFang_Regular';font-size: 18px;display: block;text-align: center;font-weight: 100;"><?php echo($eachT['title'])?></div>
	<?php
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
	
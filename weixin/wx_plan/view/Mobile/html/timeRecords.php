<!DOCTYPE html>
<html>
<head>
	<title>运输车次时间记录</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="../css/common.css">
	<link rel="stylesheet" type="text/css" href="../css/goodsTime.css">
	<style>
		#file{
			display: none;
			width: 0;
			height: 0;
			position: absolute;
			z-index: -100;
		}
	</style>
</head>
<body>
	<div class="bar">
		<span id="goToLogin"><返回</span>
		运输车次时间记录
	</div>
	<p class="select-box mt-30">
		<input id="carNum" />
		<select>
			<option>车次编号</option>
		</select>
	</p>
	<div class="table-box mt-30">
		<table border="1" cellspacing="0" cellpadding="0" width="100%">
			<tbody id="info"></tbody>
		</table>
	</div>
	<div class="table-box mt-30">
		<table border="1" cellspacing="0" cellpadding="0" width="100%">
			<tbody id="list"></tbody>
		</table>
	</div>
	<div class="button-box btn-box-small mt-30">
		<p>
			<button id="setTime">到厂确定</button>
			<button id="outF">出厂确定</button>
			<button id="arrive">到达工地确定</button>
		</p>
		<p class="mt-30">
			<button id="dzwcqr">吊装完成确定</button>
			<button id="yc">押车</button>
		</p>
	</div>
	<div class="model" id="model">
		<div class="model-contain">
			<p>时间：<span id="time"></span></p>
			<p id="showAddress">地点：<span id="address"></span></p>
			<p class="mt-10">
				<button id="sure">确定</button>
				<button id="cancel">取消</button>
			</p>
		</div>
	</div>
	<div class="model" id="model1">
		<div class="model-contain">
			<form id="form" method="post" action="http://8.tag5.cn/controller/upload.php" enctype="multipart/form-data" onsubmit="return uploadFile();">
				<p>时间：<span id="time1"></span></p>
				<p>
					<button id="uploadFile" type="button">上传附件</button>
					<input type="file" id="file" name="file" accept="image/*" />
				</p>
				<p class="mt-10">
					<input id="sure1" value="确定" type="submit" />
					<!-- <button id="sure1">确定</button> -->
					<button id="cancel1">取消</button>
				</p>
			</form>
		</div>
	</div>
	<div class="model" id="model2">
		<div class="model-contain">
			<p>押车原因：<input id="reason" /></p>
			<p class="mt-10">
				<button id="sure2">确定</button>
				<button id="cancel2">取消</button>
			</p>
		</div>
	</div>
	<!-- 通过 iframe 嵌入前端定位组件 --> 
     <iframe id="geoPage" width="0" height="0" frameborder=0 scrolling="no" style="position: absolute;z-index: -1000;" src="https://apis.map.qq.com/tools/geolocation?key=B7HBZ-EDACJ-3RLFY-FQTAD-442OF-IWFOR&referer=myapp&effect=zoom" allow="geolocation"></iframe> 
     <!-- 接收到位置信息后 通过 iframe 嵌入位置标注组件 --> 
     <!-- <iframe id="markPage" width="100%" height="70%" frameborder=0 scrolling="no" src=""></iframe>  -->
	<script type="text/javascript" src="../js/moment.js"></script>
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/jquery.form.js"></script>
	<script type="text/javascript" src="../js/common.js"></script>
	<script type="text/javascript" src="../js/jquery.cookie.js"></script>
	<script type="text/javascript">
		(function(){
			$('#form').attr('action',url.split('controller')[0]+'controller/upload.php');
			window.addEventListener('message', function(event) { 
		        // 接收位置信息
		        var loc = event.data||{};
		        $('#address').html(loc.city+'，'+loc.addr)
		        // var markUrl = 'https://apis.map.qq.com/tools/poimarker?marker=coord:' + loc.lat + ',' +loc.lng+ ';title:我的位置;addr:' + (loc.addr||loc.city) + '&referer=myapp&key=B7HBZ-EDACJ-3RLFY-FQTAD-442OF-IWFOR';
		        // document.getElementById('markPage').src = markUrl;
		    }, false);
		    var step=0;
			$('#carNum').on('input',function(){
				getTable();
			});
			var CarNumber='';
			var CarNo='';
			var TradeId='';
			var BuildingNo='';
			var FloorNo='';
			var finishThis=false;
			function getTable(){
				myPost({
			        data: {
			          action: 'getReplenishTransportRecord',
			          carnumber: $('#carNum').val(),
			        },
			        successFn: function (data) {
			          if (data.status == 0) {
			          	CarNumber=data.result.CarNumber;
			          	CarNo=data.result.CarNo;
			          	TradeId=data.result.TradeId;
			          	BuildingNo=data.result.BuildingNo;
			          	FloorNo=data.result.FloorNo;
			          	var str="<tr><td>"+data.result.Forshort+"</td><td>"+data.result.CarNo+"</td></tr><tr><td colspan='2' style='text-align:left;'>"+data.result.ShipInfo+"</td></tr>"
			            $('#info').html(str);
			            str='';
			            step=data.result.Records.length;
			            console.log(step);
			            for(var i=0;i<data.result.Records.length;i++){
			            	str+='<tr><td>'+(i-(-1))+'</td>';
			            	if(data.result.Records[i].TypeID==1){
			            		str+='<td>到厂时间</td><td><p>'+data.result.Records[i].CreateDateTime+'</p><p>'+data.result.Records[i].Address+'</p></td>';
			            	}
			            	if(data.result.Records[i].TypeID==2){
			            		str+='<td>出厂时间</td><td><p>'+data.result.Records[i].CreateDateTime+'</p><p><a href="'+data.result.Records[i].Col01+'" download="">附件</a></p></td>';
			            	}
			            	if(data.result.Records[i].TypeID==3){
			            		str+='<td><td><p>'+data.result.Records[i].CreateDateTime+'</p><p>'+data.result.Records[i].Address+'</p></td>';
			            	}
			            	if(data.result.Records[i].TypeID==4){
			            		str+='<td>吊装完成时间<td><p>'+data.result.Records[i].CreateDateTime+'</p><p>'+data.result.Records[i].Address+'</p></td>';
			            	}
			            	if(data.result.Records[i].TypeID==5){
			            		str+='<td>押车原因</td><td>data.result.Records[i].Col01</td>';
			            	}
			            	finishThis=false;
			            	if(data.result.Records[i].CreateBy&&data.result.Records[i].GroupUserName) finishThis=true;
			            	console.log(finishThis);
							str+='<td>'+data.result.Records[i].CreateBy+'</td><td>'+data.result.Records[i].GroupUserName+'</td></tr>';
			            }
			            $('#list').html(str);
			          }
			        }
			      });
			}
			var type=0;
			function getTimeNow(){
				var time=new Date();
				var date=time.getFullYear()+'/'+(time.getMonth()+1)+'/'+time.getDate()+' '+time.getHours()+':'+time.getMinutes();
				return moment().format('YYYY-MM-DD HH:mm:ss');
			}
			// 司机：38，物流组：13，现场人员：20？
			$('#setTime').on('click',function(){
				if((step==0&&role==38&&!finishThis)){
					type=1;
					$('#model').show();
					$('#time').html(getTimeNow());
				}else if(step==1&&role==13&&!finishThis){
					inFactory();
				}else{
					window.alert('请按顺序操作！');
				}
				
			});
			$('#outF').on('click',function(){
				if(step==1&&role==13&&finishThis){
					$('#time1').html(getTimeNow());
				$('#model1').show();
				}else if(step==2&&role==38&&!finishThis){
					outFactory();
				}else{
					window.alert('请按顺序操作！');
				}
			})
			$('#arrive').on('click',function(){
				if(step==2&&role==38&&finishThis){
					type=2;
					$('#model').show();
					$('#time').html(getTimeNow());
				}else if(step==3&&role==13&&!finishThis){
					atConstruction();
				}else{
					window.alert('请按顺序操作！');
				}
			});
			$('#dzwcqr').on('click',function(){
				if(step==3&&role==20&&finishThis){
					type=3;
					$('#model').show();
					$('#showAddress').hide();
					$('#time').html(getTimeNow());
				}else if(step==4&&role==38&&!finishThis){
					diaoZhuang();
				}else{
					window.alert('请按顺序操作！');
				}
			});
			$('#yc').on('click',function(){
				if((step==4&&finishThis)||step==5){
					$('#model2').show();
				}else{
					window.alert('请按顺序操作！');
				}
			});
			$('#sure2').on('click',function(){
				yaChe();
			});
			$('#cancel2').on('click',function(){
				$('#model2').hide();
			})
			$('#sure').on('click',function(){
				$('#showAddress').show();
				$('#model').hide();
				if(type===1) inFactory();
				if(type===2) atConstruction();
				if(type===3) diaoZhuang();
			})
			$('#cancel,#cancel1').on('click',function(){
				$('#showAddress').show();
				$('#model,#model1').hide();
				return false;
			});
			$('#goToLogin').on('click',function(){
				document.location.href = './login.html';
			});
			function inFactory(){
				myPost({
			        data: {
			          action: 'setReplenishTransportRecord',
			          typeid: 1,
			          createby: role==13?name:null,
			          address: $('#address').html(),
			          createdatetime: $('#time').html(),
			          tradeId: TradeId,
			          buildingno: BuildingNo,
			          floorno: FloorNo,
			          carno: CarNo,
			          carnumber: CarNumber,
			        },
			        successFn: function (data) {
			          if (data.status == 0) {
			          	$('#model').hide();
						getTable();
			          }
			          window.alert(data.msg);
			        }
			      });
			}
			
			$('#uploadFile').on('click',function(){
				$('#file').click();
			})
			$('#sure1').on('click',function(){
				outFactory();
				uploadFile();
				return false;
			})
			function uploadFile(){
				$('#form').ajaxSubmit({
		            success: function (responseText) {
		                alert(responseText);
		            }
		        });
			}
			function outFactory(){
				myPost({
			        data: {
			          action: 'setReplenishTransportRecord',
			          typeid: 2,
			          createby: role==38?name:null,
			          createdatetime: $('#time1').html(),
			          tradeId: TradeId,
			          buildingno: BuildingNo,
			          floorno: FloorNo,
			        },
			        successFn: function (data) {
			        	if (data.status == 0) {
			          	$('#model').hide();
						getTable();
			          }
			          window.alert(data.msg);
			        }
			      });
			}
			function atConstruction(){
				myPost({
			        data: {
			          action: 'setReplenishTransportRecord',
			          typeid: 3,
			          createby: role==13?name:null,
			          address: $('#address').html(),
			          createdatetime: $('#time').html(),
			          tradeId: TradeId,
			          buildingno: BuildingNo,
			          floorno: FloorNo,
			          carno: CarNo,
			          carnumber: CarNumber,
			        },
			        successFn: function (data) {
			          if (data.status == 0) {
			          	$('#model').hide();
						getTable();
			          }
			          window.alert(data.msg)
			        }
			      });
			}
			function diaoZhuang(){
				myPost({
			        data: {
			          action: 'setReplenishTransportRecord',
			          typeid: 4,
			          createby: role==38?name:null,
			          createdatetime: $('#time').html(),
			          tradeId: TradeId,
			          buildingno: BuildingNo,
			          floorno: FloorNo,
			          carno: CarNo,
			          carnumber: CarNumber,
			        },
			        successFn: function (data) {
			          if (data.status == 0) {
			          	$('#model').hide();
						getTable();
			          }
			          window.alert(data.msg)
			        }
			      });
			}
			
			function yaChe(){
				myPost({
			        data: {
			          action: 'setReplenishTransportRecord',
			          typeid: 5,
			          col01: $('#reason').val(),
			          createby: $('#people').html(),
			          tradeId: TradeId,
			          buildingno: BuildingNo,
			          floorno: FloorNo,
			          carno: CarNo,
			          carnumber: CarNumber,
			          createdatetime: getTimeNow(),
			        },
			        successFn: function (data) {
			          if (data.status == 0) {
			          	$('#model2').hide();
						getTable();
			          }
			          window.alert(data.msg)
			        }
			      });
			}
			getOpenID();
			function getOpenID(){
				myPost({
					data: {
						action:'getopenid'
					},
			        successFn: function (data) {
			        	if(data.status == 0){
			        		getUserInfo(data.result);
			        	}
			        }
				})
			}
			var role=null;
			var name=null;
			function getUserInfo(openid){
				myPost({
			        data: {
			          action: 'userinfo',
			          openid: openid
			        },
			        successFn: function (data) {
			          if (data.status == 0) {
			          	role=data.result.RoleId;
			          	name=data.result.TrueName;
			          }
			        }
			      });
			}
		})();
	</script>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
	<title>要货计划</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" href="../css/ued.css"/>
	<link rel="stylesheet" type="text/css" href="../css/common.css">
	<link rel="stylesheet" type="text/css" href="../css/goodsTime.css">
</head>
<body>
	<div class="bar">
		<span id="goToGoodsTime"><返回</span>
		要货计划
	</div>
	<p class="mt-30 text-c" id="address">####</p>
	<p class="select-box mt-30">
		<select id="xiangMu"></select>
		<select id="louDong"></select>
		<select id="louCeng"></select>
	</p>
	<div class="table-box mt-30">
		<table border="1" cellspacing="0" cellpadding="0" width="100%">
			<thead>
				<tr>
					<td>序号</td>
					<td>楼栋</td>
					<td>楼层</td>
					<td>构件类型</td>
					<td>要货时间</td>
					<td>状态</td>
				</tr>
			</thead>
			<tbody id="tableList"></tbody>
		</table>
	</div>
	<div id="pagination"></div>
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/common.js"></script>
	<script type="text/javascript" src="../js/jquery.cookie.js"></script>
	<script type="text/javascript" src="../js/index.js"></script>
	<script type="text/javascript">
		(function(){
			getXiangMu();

		    function getXiangMu () {
		      myPost({
		        data: {
		          action: 'searchTradeById',
		          objectid: $.cookie('objectid')
		        },
		        successFn: function (data) {
		          if (status == 0) {
		            var str = '';
		            for (var i = 0, l = data.result.length; i < l; i++) {
		              str += "<option data-id='" + data.result[i].Id + "'>" + data.result[i].Forshort + "</option>"
		            }
		            $('#xiangMu').html(str);
		            getLouDong();
		          }
		        }
		      });
		    }
		    function getLouDong () {
		      myPost({
		        data: {
		          action: 'buildings',
		          tradeid: $('#xiangMu').find("option:selected").attr("data-id")
		        },
		        successFn: function (data) {
		          if (status == 0) {
		            var str = '';
		            for (var i = 0, l = data.result.length; i < l; i++) {
		              str += "<option data-id='" + data.result[i].BuildingNo + "'>" + data.result[i].BuildingNo + "</option>"
		            }
		            $('#louDong').html(str);
		            getLouCeng();
		          }
		        }
		      });
		    }
		    function getLouCeng () {
		      myPost({
		        data: {
		          action: 'floor',
		          tradeid: $('#xiangMu').find("option:selected").attr("data-id"),
          		  buildid: $('#louDong').find("option:selected").attr("data-id")
		        },
		        successFn: function (data) {
		          if (status == 0) {
		            var str = '';
		            for (var i = 0, l = data.result.length; i < l; i++) {
		              str += "<option data-id='" + data.result[i].FloorNo + "'>" + data.result[i].FloorNo + "</option>"
		            }
		            $('#louCeng').html(str);
		          }
		          getTable();
		        }
		      });
		    }
			function getTable(curPage){
		      $('#address').html($('#xiangMu').val()+$('#louDong').val()+'栋'+$('#louCeng').val()+'层')
		      myPost({
		        data: {
		          action: 'getTradeInfoPageExt',
		          tradeid: $('#xiangMu').find("option:selected").attr("data-id"),
		          buildingno: $('#louDong').find("option:selected").attr("data-id"),
		          floorno: $('#louCeng').find("option:selected").attr("data-id"),
		          current: curPage||1,
		          pagenum: 15,
		        },
		        successFn: function (data) {

		          if (data.status == 0&&data.result.data[0]) {
		            var str = '';
		            for (var i = 0, l = data.result.data.length; i < l; i++) {
		            	str += "<tr><td>"+(i-(-1))+"</td><td>"+(data.result.data[i].BuildingNo||"")+"</td><td>"+data.result.data[i].FloorNo+"</td><td>"+data.result.data[i].CmptType+"</td><td>"+(data.result.data[i].RequestDateTime||"")+"</td><td>"+(data.result.data[i].Status==0?"未提交":data.result.data[i].Status==1?"已提交":"已审核")+"</td></tr>";
		            }
		            $('#tableList').html(str);
		            $('.checkbox').change(function(){
		              if($(this)[0].checked) $(this).parent().parent().addClass('active');
		              else $(this).parent().parent().removeClass('active');
		            });
		            $('.checkbox').on('click',function(){
		              if($(this).parent().parent().hasClass('active')){
		                $(this).prop('checked', false);
		                $(this).parent().parent().removeClass('active');
		              } 
		            });
		            if(data.result[0]){
		            	Pagination({
			              activeIndex: data.result[0].Floors.current, // 当前活动页
			              totalPage: data.result[0].Floors.pagesize, // 分页总页数
			              showNumberOfPage: false, // 是否可切换每页数量，boolen类型
			              father: '#pagination', // 插槽id
			              goToPage: function (index) {
			                // 切换分页回调函数，index为要去第几页
			                getTable(index);
			              },
			            })
		            }
		          }
		        }
		      });
		    }
			$('#goToGoodsTime').on('click',function(){
				document.location.href = './goodsTime.html';
			});
		})();
	</script>
</body>
</html>
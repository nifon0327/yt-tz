<!DOCTYPE html>
<html>
<head>
	<title>运输记录信息查询</title>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="../css/common.css">
    <link rel="stylesheet" type="text/css" href="../plugin/layer/theme/default/layer.css">
    <link rel="stylesheet" type="text/css" href="../plugin/laydate/theme/default/laydate.css">
</head>
<body>
	<div class="row">
	  <div class="search bg-white">
	  	 <span class="col-cell">
	  	 	<input type="text" name="datetime" class="input-date-icon" placeholder="出厂时间">
	  	 </span>
	  	 <span class="col-cell">
	  	 	<select class="slt_trade">
              <option value="">项目名称</option>
            </select>
	  	 </span>

	  	 <span class="col-cell">
	  	 	<select class="slt_carno">
              <option value="">车牌号</option>
            </select>
	  	 </span>
	  	 <span class="col-cell">
	  	 	<select class="slt_carnumber">
              <option value="">车次单号</option>
            </select>
	  	 </span>
	  	 <span class="col-cell">
            <button class="green btnSearch">查询</button>
         </span>
	  </div>
	</div>
	<div class="row">
		<span class="table-container bg-white">
		  <table class="table">
			<thead>
				<tr>
					<th>序号</th>
					<th>车牌号码</th>
					<th>车次单号</th>
					<th>项目名称</th>
					<th>工字钢数量</th>
					<th>木方数量</th>
					<th>到厂时间</th>
					<th>到厂确认人</th>
					<th>出厂时间</th>
					<th>出厂确认人</th>
					<th>到达工地时间</th>
					<th>到达工地确认人</th>
					<th>吊装完成时间</th>
					<th>吊装完成确认人</th>
					<th>押车原因</th>
					<th>附件</th>
				</tr>
			</thead>
			<tbody class="center-align">
			  <tr>
                 <td class="no-record" colspan="16">
                   暂没有任何记录
                 </td>
              </tr>  
			</tbody>
		  </table>
		</span>
	</div>
   <script type="text/javascript" src="../js/jquery.js"></script>
   <script type="text/javascript" src="../js/common.js"></script>
   <script type="text/javascript" src="../plugin/layer/layer.js"></script>
   <script type="text/javascript" src="../plugin/laydate/laydate.js"></script>
	<script type="text/javascript">
	   var g_tradeid,g_carno;
	   var page=this;
	   laydate.render({elem: 'input[name="datetime"]'});
	   $(function(){
          init();
          onInit();
	   });
	   var init=function(){
           initTrade();
	   }

	   var onInit=function(){
            $('.slt_trade').on('change',function(){
               var tradeid=$(this).val();
               page.g_tradeid=tradeid;
               if(!_isNull(tradeid)){
                 initCarNo(tradeid);
               }
            });

            $('.slt_carno').on('change',function(){
            	var carno=$(this).val();
                
            	if(!_isNull(carno)){
                   initCarNumber(page.g_tradeid,carno);
            	}

            });

            $('.btnSearch').on('click',function(){
                initRecord();
            });
	   }
       var initTrade=function(){
         webPOST({
                  action:'tradeObject'
                },function(data){
                  if(data.status==0){
                    $.each(data.result,function(i,item){
                      var option='<option value="'+item.Id+'">'+item.Forshort+'</option>';
                      $('.slt_trade').append(option);
                    });
                  }
                });
       }

             //初始化楼栋
      

      var initCarNo=function(tradeid){
      	    webPOST({
              action:'getCarNoByTradeID',
              tradeid:tradeid
      	    },function(data){

      	    $('.slt_carno').empty().append('<option value="">车牌号</option>');
      	    $('.slt_carnumber').empty().append('<option value="">车次单号</option>');
      	       if(data.status==0){
      	       	  $.each(data.result,function(i,item){
                    var option='<option value="'+item.CarNo+'">'+item.CarNo+'</option>';
                    $('.slt_carno').append(option);
                  })
      	       }

      	    });
       }

       var initCarNumber=function(tradeid,carno){
       	   webPOST({
       	   	 action:'getCarNumberByCarNo',
       	   	 tradeid:tradeid,
       	   	 carno:carno
       	   },function(data){
       	   	  console.log(data);
              $('.slt_carnumber').empty().append('<option value="">车次单号</option>');
              if(data.status==0){
                $.each(data.result,function(i,item){
              	  var option='<option value="'+item.CarNumber+'">'+item.CarNumber+'</option>';
                  $('.slt_carnumber').append(option);
                });
              }
       	   });
       }

       var initRecord=function(){
       	   var _loading='<tr><td class="loading" colspan="16">'+
                       '  数据查询中...'+
                       '</td></tr>';
          $('.table > tbody').empty().append(_loading);
       	   var datetime=$('input[name="datetime"]').val();
       	   var tradeid=$('.slt_trade').find('option:selected').val();
       	   if(_isNull(tradeid)){
       	   	 layer.msg('项目名称不能为空',{icon:5,shade:0});
       	   	 return;
       	   }

       	   var carno=$('.slt_carno').find('option:selected').val();
       	   if(_isNull(carno)){
       	   	 layer.msg('车牌号不能为空',{icon:5,shade:0});
       	   	 return;
       	   }
       	   var carnumber=$('.slt_carnumber').find('option:selected').val();
       	   webPOST({
	       	   	action : 'getShipsAndReplenishTransportRecordPc',
	       	   	date   :datetime,
	       	   	tradeId:tradeid,
	       	   	carno  :carno,
	            carnumber:carnumber
       	   },function(data){
                if(data.status==0){
                   var result=data.result;
                   console.log(result);
                   if(result.length==0){
                     var _error='<tr><td class="no-record" colspan="16">'+
                           '   查询为空'+
                           '</td></tr>';
                     $('.table > tbody').empty().append(_error);
                     return;
                   }
                   $('.table > tbody').empty();
                   $.each(result,function(i,record){
                   	   var attachment=_isNull(record.OutAttachment)?'':'<a href="'+site+'/weixin/'+ record.OutAttachment+'" target="_blank">附件</a>';
                       var _html='<tr>'+
                                 '  <td class="seq">'+(i+1)+'</td>'+
                                 '  <td>'+record.CarNo+'</td>'+
                                 '  <td>'+record.CarNumber+'</td>'+
                                 '  <td>'+record.Forshort+'</td>'+
                                 '  <td>'+record.Qty+'</td>'+
                                 '  <td></td>'+
                                 '  <td>'+record.InFactoryCreateDateTime+'</td>'+
                                 '  <td>'+record.InFactoryCreateBy+'</td>'+
                                 '  <td>'+record.OutFactoryCreateDateTime+'</td>'+
                                 '  <td>'+record.OutFactoryCreateBy+'</td>'+
                                 '  <td>'+record.WorkFactoryCreateDateTime+'</td>'+
                                 '  <td>'+record.WorkFactoryCreateBy+'</td>'+
                                 '  <td>'+record.LeftFactoryCreateDateTime+'</td>'+
                                 '  <td>'+record.LeftFactoryCreateBy+'</td>'+
                                 '  <td>'+record.EscortAddress+'</td>'+
                                 '  <td>'+attachment+'</td>'+
                                 '</tr>';
                       $('.table > tbody').append(_html);

                   });
                }else{
                   var _loading='<tr><td class="error" colspan="16">'+
                       '  数据较大,系统处理较慢，请选择详细查询条件'+
                       '</td></tr>';
                   $('.table > tbody').empty().append(_loading)
                }
       	   });
       }

	</script>
</body>
</html>
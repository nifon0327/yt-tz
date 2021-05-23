<?php
   session_start();
   $checkid=1141;
   $checker='lisi';
   $openid='qSqXfNs2rEt8GAvVMJMI';
   $OPERATE='product';
   if(empty($_SESSION)&&$OPERATE=='product'){
   	  header('Location:noright.php');

      die();
   }else{
      $checkid = $_SESSION['Login_Id'];
      $checker = $_SESSION['Login_Name'];
      $openid  = $_SESSION['Login_P_Number'];
   }
?>
<!DOCTYPE html>
<html>
<head>
  <title>PMC确认</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="../css/common.css">
  <link rel="stylesheet" type="text/css" href="../plugin/layer/theme/default/layer.css">
  <link rel="stylesheet" type="text/css" href="../plugin/laydate/theme/default/laydate.css">
</head>
<body>
	<div class="row search">
	  <div class="search bg-white">
	    <span class="col-cell">
	   	  <select class="slt_trade">
            <option value="">项目名称</option>
          </select>
	    </span>
	    <span class="col-cell">
	   	  <select class="slt_building">
	   	  	 <option value="">楼栋</option>
	   	  </select>
	    </span>
	    <span class="col-cell">
	   	  <select class="slt_floor">
            <option>楼层</option>
          </select>
	    </span>
	    <span class="col-cell">
	    	<input type="text" name="reqdate" class="input-date-icon" placeholder="要货日期" />
	    </span>
	    <span class="col-cell">
	    	<button class="green btnSearch">查询</button>
	    </span>
	    <span class="col-cell">
	    	<button class="green btnOK">PMC确认</button>
	    </span>
	  </div>
	</div>
	<div class="row">
	  <span class="table-container bg-white">
	   	<table class="table">
		  <thead>
			<tr>
			  <td width="25">
			  	 <input type="checkbox" name="box">
			  </td>
			  <th width="40">序号</th>
			  <th width="50">楼栋</th>
			  <th width="50">楼层</th>
			  <th width="80">构建类型</th>
			  <th>要货时间</th>
			  <th width="120">要货操作人</th>
			  <th width="100">PMC确定</th>
			  <th>PMC确定时间</th>
			  <th width="120">PMC确定操作人</th>
			</tr>
		   </thead>
		   <tbody>
			 <tr>
			   <td class="no-record" colspan="10">
			   	   暂没有任何记录
			   </td>
			 </tr>  
		   </tbody>
		</table>
	   </span>
		
	</div>
	<div class="row">
		<span class="page-container">
			
		</span>
	</div>
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/common.js"></script>
	<script type="text/javascript" src="../plugin/layer/layer.js"></script>
	<script type="text/javascript" src="../plugin/laydate/laydate.js"></script>
	<script type="text/javascript">
	  laydate.render({elem: 'input[name="reqdate"]'});
	  $(function(argument) {
	  	 init();
	  	 onInit();
	  });
     
	  var init=function(){
           initTrade();
	  }
	  var onInit=function(){
          $('.slt_trade').on('change',function(){
          	 var tradeid=$(this).val();
          	 if(!_isNull(tradeid)){
          	 	initBuilding(tradeid);
          	 }
          });

          $('.slt_building').on('change',function(){
          	 var buildingno=$(this).val();
          	 var tradeid=$('.slt_trade').find('option:selected').val();
          	 if(!_isNull(buildingno)){
          	 	initFloor(tradeid,buildingno);
          	 }
          });

          $('.btnSearch').on('click',function(){
          	 var tradeid=$('.slt_trade').find('option:selected').val();
          	 if(_isNull(tradeid)){
          	 	layer.msg('请选择项目');
          	 	return;
          	 }
          	 var buildingno=$('.slt_building').find('option:selected').val();
          	 if(_isNull(buildingno)){
          	 	layer.msg('请选择楼栋');
          	 	return;
          	 }
          	 var floorno=$('.slt_floor').find('option:selected').val();
          	 var reqdate=$('input[name="reqdate"]').val();
          	 console.log(reqdate);
             searchTradeInfomation(tradeid,buildingno,floorno,reqdate,1);
          });

          $('.page-container').on('click','a',function(){
          	 var curr=$(this).attr('page');
          	 var tradeid=$('.slt_trade').find('option:selected').val();
          	 var buildingno=$('.slt_building').find('option:selected').val();
          	 var floorno=$('.slt_floor').find('option:selected').val();
          	 var reqdate=$('input[name="reqdate"]').val();

          	 searchTradeInfomation(tradeid,buildingno,floorno,reqdate,curr);
          });

          $('.btnOK').on('click',function(){
	          layer.confirm('是否提交审核?', {icon: 3, title:'审核提示'}, function(index){
				var ids=getCheckBoxValue();
	            if(ids.length==0){
	          	   layer.alert('请选择审核项', {icon: 1});
	          	   return;
	          }
              var sids=ids.join(',');
	          webPOST({
	          	  action    : 'setTradeStateExtByIds',
	          	  statecode : 3,
	          	  ids       : sids,
	          	  checkid   : <?php echo $checkid?>,
	          	  checker   : '<?php echo $checker?>',
	          	  openid    : '<?php echo $openid?>'
	           },function(data){
	           	  layer.alert('审核成功',{icon:6,shade:0});
	              var pageIndex  = $('.page-container').find('.selected').attr('page');
	              var tradeid    = $('.slt_trade').find('option:selected').val();
	          	  var buildingno = $('.slt_building').find('option:selected').val();
	          	  var floorno    = $('.slt_floor').find('option:selected').val();
	          	  var reqdate    = $('input[name="reqdate"]').val(); 
	          	  searchTradeInfomation(tradeid,buildingno,floorno,reqdate,pageIndex); 
	          });
			  layer.close(index);
			});
          });
	  }
      //初始化项目
	  var initTrade=function(){
	  	   webPOST({action:'tradeObject'},
                   function(data){
                     if(data.status==0){
                        $.each(data.result,function(i,item){
                          var option='<option value="'+item.Id+'">'+item.Forshort+'</option>';
                          $('.slt_trade').append(option);
                        });
                     }
                   });
	  }
      //初始化楼栋
	  var initBuilding=function(tradeid){
            webPOST({action:'buildings',tradeid:tradeid},
            	    function(data){
          
            	      $('.slt_building').empty().append('<option value="">楼栋</option>');
                      if(data.status==0){
                         $.each(data.result,function(i,item){
                           var option='<option value="'+item.BuildingNo+'">'+
                                         item.BuildingNo+'栋</option>';
                           $('.slt_building').append(option);
                         })
                      }
            	    });
	  }
      //初始化楼层
	  var initFloor=function(tradeid,buildingno){
            webPOST({action:'floor',tradeid:tradeid,buildid:buildingno},
            	    function(data){
                      $('.slt_floor').empty().append('<option value="">楼层</option>');
                      if(data.status==0){
                      	$.each(data.result,function(i,item){
                           var option='<option value="'+item.FloorNo+'">'+
                                         item.FloorNo+'层</option>';
                           $('.slt_floor').append(option);
                      	});
                      }

            	    });
	  }

	  //查询数据
	  var searchTradeInfomation=function(tradeid,buildingno,floorno,reqdate,current){
             webPOST({
               	action:'getPMCTradeRequestInfoPageExt',
               	tradeid:tradeid,
               	buildingno:buildingno,
               	floorno:floorno,
               	requestdatetime:reqdate,
               	current:current,
               	pagenum:25
             },function(data){
             	var norecord='<tr><td class="no-record" colspan="9">暂没有任何记录</td></tr>';
             	$('.table > tbody').empty().append(norecord);
                if(data.status==0){
                  var tradeData=data.result.data;
                  if(tradeData.length>0){
                     $('.table > tbody').empty();
                  }else{
                  	var norecord='<tr><td class="no-record" colspan="10">暂没有任何记录</td></tr>';
             	    $('.table > tbody').empty().append(norecord);
                  }
                  $.each(tradeData,function(i,row){
                  	 var disabled=(row.Status!=2)?'disabled="disabled"':'';
                  	 var type=(row.Status==1)?'bgrad':'';
                     var _cot='<tr class="'+type+'">'+
                              '  <td class="text-align-center">'+
                              '    <input type="checkbox" name="cbox" '+disabled+
                              '       value="'+row.ReplenishID+'">'+
                              '  </td>'+
                              '  <td class="text-align-center">'+(i+1)+'</td>'+
                              '  <td class="text-align-center">'+row.BuildingNo+'</td>'+
                              '  <td class="text-align-center">'+row.FloorNo+'</td>'+
                              '  <td class="text-align-center">'+row.CmptType+'</td>'+
                              '  <td style="padding-left:5px;">'+row.RequestDateTime+'</td>'+
                              '  <td class="text-align-center">'+row.ReqName+'</td>'+
                              '  <td class="text-align-center">'+row.StatusName+'</td>'+
                              '  <td class="text-align-center">'+row.CheckDate+'</td>'+
                              '  <td class="text-align-center">'+row.Checker+'</td>'+
                              '</tr>';
                      $('.table > tbody').append(_cot);
                  });
                  var pagesize=data.result.pagesize;
                  initpage(current,pagesize);
                }
             });
	  }

	  var getCheckBoxValue=function(){
           var ids=new Array();
           $('input[name="cbox"]:checked').each(function(v,row){
          	  ids.push($(this).val());
           });
           return ids; 
	  }




	</script>
</body>
</html>
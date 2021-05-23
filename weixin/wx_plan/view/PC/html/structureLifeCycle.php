<?php
  $productid='187771';
  if(isset($_GET['productid'])){
     $productid=$_GET['productid'];
  }
?>
<!DOCTYPE html>
<html>
<head>
	<title>运输记录信息查询</title>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="../css/common.css">
    <link rel="stylesheet" type="text/css" href="../css/lifecycle.css">
    <link rel="stylesheet" type="text/css" href="../plugin/layer/theme/default/layer.css">
    <link rel="stylesheet" type="text/css" href="../plugin/laydate/theme/default/laydate.css">
</head>
<body class="goyeer">
  <div class="row">
    <h1>构件生命周期追踪</h1>
  </div>
  <div class="row search bg-white">
      <dl>
        <dt>项目：</dt>
        <dd><em class="forshort"></em></dd>
      </dl>
      <dl>
        <dt>楼栋：</dt>
        <dd>
           <em class="buildingno"></em>
        </dd>
      </dl>
      <dl>
        <dt>楼层：</dt>
        <dd>
          <em class="floorno"></em>
        </dd>
      </dl>
      <dl>
        <dt>构件编号：</dt>
        <dd><em class="ecode"></em> </dd>
      </dl>
  </div>
  <div class="row panel">
    <div class="panel-title">
       <span class="t1">1.原材料追踪</span>
    </div>
    <div class="panel-tbody raw-material" style="background-color: #ffffff;">
      <div class="material-scroll">
        <table class="table">
          <thead>
             <tr>
               <th width="35">序号</th>
               <th width="145">原料名称</th>
               <th>供应商</th>
               <th width="80">品牌</th>
               <th width="110">购买时间</th>
               <th width="80">入库单号</th>
               <th width="80">入库库位</th>
               <th width="90">质检报告</th>
             </tr>
          </head>
          <tbody>
          </tbody>
        </table>
      </div>
  
    </div>
  </div>
  <div class="row panel drawing-track" style="margin-top:10px;">
     <div class="panel-title">
       <span class="t1">2.设计图纸追踪</span>
     </div>
     <div class="panel-tbody tbody-list">
        <div class="col-06 content">
           <dl>
              <dt>图纸名称:</dt>
              <dd></dd>
           </dl>
           <dl>
              <dt>图纸上传时间:</dt>
              <dd></dd>
           </dl>
           <dl>
              <dt>图纸审核时间:</dt>
              <dd></dd>
           </dl>
           <dl>
              <dt>图纸审核人:</dt>
              <dd></dd>
           </dl>
        </div>
        <div class="col-06 drawing">
          <figure class="bg01" style="height: 192px;"> 
            <div class="btn-linke"></div>
          </figure>
        </div>
     </div>
  </div>

  <div class="row panel" style="margin-top:10px;">
     <div class="panel-title">
       <span class="t1">3.质量检测追踪</span>
     </div>
     <div class="panel-tbody tbody-list semi-finished">
        <div class="col-06 content">
           <dl>
              <dt>半成品质检</dt>
              <dd></dd>
           </dl>
           <dl>
              <dt>产线:</dt>
              <dd></dd>
           </dl>
           <dl>
              <dt>质检时间:</dt>
              <dd></dd>
           </dl>
           <dl>
              <dt>操作人:</dt>
              <dd></dd>
           </dl>
           <dl>
              <dt>状态:</dt>
              <dd></dd>
           </dl>
        </div>
        <div class="col-06">
          <figure class="bg01 border-bottom" style="height:239px;">
           <div class="btn-linke none-quality">
               
           </div>
          </figure>
        </div>
     </div>
     <div class="panel-tbody tbody-list finished-quality">
        <div class="col-06 content">
           <dl>
              <dt>成品质检</dt>
              <dd></dd>
           </dl>
           <dl>
              <dt>产线:</dt>
              <dd></dd>
           </dl>
           <dl>
              <dt>质检时间:</dt>
              <dd></dd>
           </dl>
           <dl>
              <dt>操作人:</dt>
              <dd></dd>
           </dl>
           <dl>
              <dt>状态:</dt>
              <dd></dd>
           </dl>
        </div>
        <div class="col-06">
          <figure class="bg01"  style="height:239px;">
            <span style="height:210px;">
               <div class="btn-linke none-quality"></div>
            </span>
          </figure>
        </div>
     </div>
  </div>
  <div class="row panel" style="margin-top:10px;">
     <div class="panel-title">
       <span class="t1">4.成品追踪追踪</span>
     </div>
     <div class="panel-tbody tbody-list finished-product-track">
        <div class="col-06 content">
           <dl>
              <dt>库位:</dt>
              <dd></dd>
           </dl>
           <dl>
              <dt>垛号:</dt>
              <dd></dd>
           </dl>
           <dl>
              <dt>出货时间:</dt>
              <dd></dd>
           </dl>
           <dl>
              <dt>出货操作人:</dt>
              <dd></dd>
           </dl>
           <dl>
              <dt>运输车辆:</dt>
              <dd></dd>
           </dl>
           <dl>
              <dt>司机：</dt>
              <dd></dd>
           </dl>
           <dl>
              <dt>到达工地时间:</dt>
              <dd></dd>
           </dl>
           <dl>
              <dt>到达确认人:</dt>
              <dd></dd>
           </dl>
        </div>
        <div class="col-06 content-01">
         <dl class="border-left">
              <dt>入库时间:</dt>
              <dd></dd>
           </dl>
          <dl class="border-left">
              <dt>操作人：</dt>
              <dd></dd>
           </dl>
         
           
          <figure class="bg01 border-bottom" style="height:285px;">
              <div class="btn-linke none-quality"></div>
          </figure>
        </div>
     </div>
  </div>
</body>
</html>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../plugin/layer/layer.js"></script>
<script type="text/javascript" src="../plugin/laydate/laydate.js"></script>
<script type="text/javascript">
  $.ajaxSetup({cache:false});
  var ajaxuri='http://cz.matechstone.com/weixin/wx_plan/controller/report.php';
  var param={};
      param.object    = 'object';
      param.objectid  = 0;
      param.building  = 'building';
      param.floor     = 'floor';
      param.structure = '';
      param.productid = '0';
      param.productid ='<?php echo $productid?>';
      
  $(function(){
    initProductData();
    $('.raw-material').on('click','a',function(){
        var href=$(this).attr('path');
        InitWindowFrame(href,'质检报告');
    });
    $('.drawing-track').on('click','.icon-design',function(){
        var href=$(this).attr('path');
        InitWindowFrame(href,'设计图纸');
    });

    $('body').on('click','figure img',function(){
        var src=$(this).attr('src');
        InitWindow(src,'照片信息');
    });

  });
  
  var initProductData=function(){
       param.action='getProductData';
       $.post(ajaxuri,param,function(data){
         if(data.status==0){
            var info=data.result;
            param.object    = info.forshort;
            param.building  = info.buildingno;
            param.floor     = info.floorno;
            param.structure = info.ecode;
            param.objectid  = info.objectid;
            param.productid = info.productid;
            param.drawingid = info.drawingid;
            $('.forshort').text(param.object);
            $('.buildingno').text(param.building+'栋');
            $('.floorno').text(param.floor+'层');
            $('.ecode').text(param.structure);
            initRawMaterial();
            loadDrawingTrack();
            loadSemiFinished();
            loadFinishedQualityCheck();
            loadFinishedProductTrack();
         }
       });
  }

  var initRawMaterial=function(){
       $('.raw-material').find('.loading1').remove();
       $('.raw-material').append('<div class="loading1"></div>');
       $('.raw-material table tbody').empty();
       param.action='listRawMaterialByStructure';
       $.post(ajaxuri,param,function(data){
          if(data.status==0){
            $.each(data.result,function(i,v){
               var tbody='<tr>'+
                         '  <td class="text-center" width="40">'+(i+1)+'</td>'+
                         '  <td width="120">'+v.materialname+'</td>'+
                         '  <td>'+v.suppliername+'</td>'+
                         '  <td  class="text-center" width="80">'+v.brandname+'</td>'+
                         '  <td  class="text-center" width="100">'+v.buyingtime+'</td>'+
                         '  <td  class="text-center" width="80">'+v.stockin+'</td>'+
                         '  <td  class="text-center" width="80">'+v.stockinstorage+'</td>'+

                         '  <td  class="text-center linked" width="90">'+
                              (v.qualityreport==1?'<a path="'+v.BillPath+'">质检报告</a>':'')+'</td>'+
                         '</tr>';
                $('.raw-material table tbody').append(tbody);
            });
          }else{
            var html='<tr>'+
                     '  <td colspan="8" class="no-record">暂无原材料追踪信息</td>'+
                     '</tr>';
            $('.raw-material table tbody').empty().append(html);
          }
          $('.raw-material').find('.loading1').remove();
       });
  }
  // 2.设计图纸追踪
  var loadDrawingTrack=function(){
        $('.drawing-track .panel-tbody').empty();
        $('.drawing-track .panel-tbody').append('<div class="loading1"></div>');


        var html='<div class="col-06 content">'+
                 ' <dl><dt>图纸名称:</dt><dd></dd></dl>'+
                 ' <dl><dt>图纸上传时间:</dt><dd></dd></dl>'+
                 ' <dl><dt>图纸审核时间:</dt><dd></dd></dl>'+
                 ' <dl><dt>图纸审核人:</dt><dd></dd></dl>'+
                 '</div>'+
                 '<div class="col-06 drawing">'+
                 '  <figure class="bg01" style="height: 192px;">'+
                 '    <div class="btn-linke"></div>'+
                 '  </figure>'+
                 '</div>';
        $('.drawing-track .panel-tbody').append(html);
        param.action='getDrawingTrack';
        $.post(ajaxuri,param,function(data){
           if(data.status==0){
              $('.drawing-track .panel-tbody').empty();
              html='<div class="col-06 content">'+
                   '  <dl><dt>图纸名称:</dt><dd>'+
                        data.result.drawingname+
                   '  </dd></dl>'+
                   ' <dl><dt>图纸上传时间:</dt><dd>'+
                        data.result.drawingupdatetime+
                   ' </dd></dl>'+
                   '<dl><dt>图纸审核时间:</dt><dd>'+data.result.checkeddatetime+'</dd></dl>'+
                   '<dl><dt>图纸审核人:</dt><dd>'+data.result.checkor+'</dd></dl>'+
                   '</div>'+
                   '<div class="col-06 drawing">'+
                   '  <figure class="bg01" style="height: 192px;">';
              if(data.result.isdwg==0){
                  html+='     <div class="btn-linke none-design"></div>';
              }else{
                  html+='     <div class="btn-linke icon-design" path="/design/dwgFiles/'+param.objectid+'/Pord/'+data.result.reportpath+'.pdf"></div>';
              }
              
              html+='  </figure>'+
                  '</div>';
              $('.drawing-track .panel-tbody').append(html);
           }
        });
  }

  //半成品质检
  var loadSemiFinished=function(){
        var that=$('.semi-finished');
        that.empty();
        that.append('<div class="loading1"></div>');
        html='<div class="col-06 content">';
        html+=' <dl><dt>半成品质检</dt><dd></dd></dl>'+
             '  <dl><dt>产线:</dt><dd></dd></dl>'+
             '  <dl><dt>质检时间:</dt><dd></dd></dl>'+
             '  <dl><dt>操作人:</dt><dd></dd></dl>'+
             '  <dl><dt>状态:</dt><dd></dd></dl>'+
             '</div>';
        html+='<div class="col-06">'+
              ' <figure class="bg01 border-bottom" style="height:239px;">'+
              '      <div class="btn-linke none-quality"></div>'+
              ' </figure>'+
              '</div>';     
        $('.semi-finished').append(html);
        param.action='getSemiFinishedQualityCheck';
        $.post(ajaxuri,param,function(data){
           if(data.status==0){
               that.empty();
               info=data.result;
               html='<div class="col-06 content">';
               html+='  <dl><dt>半成品质检</dt><dd></dd></dl>'+
                   '    <dl><dt>产线:</dt><dd>'+info.productline+'</dd></dl>'+
                   '    <dl><dt>质检时间:</dt><dd>'+info.qualitydatetime+'</dd></dl>'+
                   '    <dl><dt>操作人:</dt><dd>'+info.operator+'</dd></dl>'+
                   '    <dl><dt>状态:</dt><dd>'+info.statename+'</dd></dl>'+
                  '</div>';
              html+='<div class="col-06">'+
                   ' <figure class="bg01 border-bottom" style="height:239px;">';
              if(info.isimage==0){
                 html+='<span class="btn-linke none-quality"></span>';
              }else{
                 html+='<img src="/weixin/inspectionconfirm'+info.reportpath+'" >';
              }     
              html+=' </figure>'+
                    '</div>';   
               that.append(html);
            }
        });
  }

  //成品质量检查
  var loadFinishedQualityCheck=function(){
       var that=$('.finished-quality');
       that.empty();
       that.append('<div class="loading1"></div>');
       html='<div class="col-06 content">';
       html+='<dl><dt>成品质检</dt><dd></dd></dl>'+
             '<dl><dt>产线:</dt><dd></dd></dl>'+
             '<dl><dt>质检时间:</dt><dd></dd></dl>'+
             '<dl><dt>操作人:</dt><dd></dd></dl>'+
             '<dl><dt>状态:</dt><dd></dd></dl>';
            '</div>';
       html+='<div class="col-06">'+
           ' <figure class="bg01 border-bottom" style="height:239px;">';
       html+='<span class="btn-linke none-quality"></div>'+
           ' </figure>'+
           '</div>';
       that.append(html);
       param.action='getFinishedQualityCheck';
       $.post(ajaxuri,param,function(data){
  
           that.empty();
           if(data.status==0){
               info=data.result;
               html='<div class="col-06 content">';
               html+='  <dl><dt>成品质检</dt><dd></dd></dl>'+
                    '   <dl><dt>产线:</dt><dd>'+info.productline+'</dd></dl>'+
                    '   <dl><dt>质检时间:</dt><dd>'+info.qualitydatetime+'</dd></dl>'+
                    '   <dl><dt>操作人:</dt><dd>'+info.operator+'</dd></dl>'+
                    '   <dl><dt>状态:</dt><dd>'+info.statename+'</dd></dl>'+
                    '</div>';
              html+='<div class="col-06">'+
                   ' <figure class="bg01 border-bottom" style="height:239px;">';
              if(info.isimage==0){
                html+='<div class="btn-linke none-quality"></div>';
              }else{
                html+='<img src="/weixin/inspectionconfirm'+info.reportpath+'">';
              }
                   
                html+=' </figure>'+
                   '</div>';
               that.append(html);
            }
        });
  }


  var loadFinishedProductTrack=function(){
     var that=$('.finished-product-track');
     that.empty();
     that.append('<div class="loading1"></div>');
   
     html = '<div class="col-06 content"> '+
            '  <dl><dt>库位:</dt><dd></dd></dl> '+
            '  <dl><dt>垛号:</dt><dd></dd></dl> '+
            '  <dl><dt>出货时间:</dt><dd></dd></dl> '+
            '  <dl><dt>出货操作人:</dt><dd></dd></dl> '+
            '  <dl><dt>运输车辆:</dt><dd></dd></dl> '+
            '  <dl><dt>司机：</dt><dd></dd></dl> '+
            '  <dl><dt>到达工地时间:</dt><dd></dd></dl> '+
            '  <dl><dt>到达确认人:</dt><dd></dd></dl>'+
            '</div>';
     html+= '<div class="col-06 content-01">'+
            '  <dl class="border-left"><dt>入库时间:</dt><dd></dd></dl>'+
            '  <dl class="border-left"><dt>操作人：</dt><dd></dd></dl>'+
            '  <figure class="bg01 border-bottom" style="height:285px;">'+
            '    <div class="btn-linke none-shipment"></div>'+
            '  </figure>'+
            '</div>';
     that.append(html);
     param.action='getFinishedProductTrack';
     $.post(ajaxuri,param,function(data){
        console.log(data);
        if(data.status==0){
          that.empty();
          var info=data.result; 
          html = '<div class="col-06 content"> '+
            '  <dl><dt>库位:</dt><dd>'+info.storagelocation+'</dd></dl> '+
            '  <dl><dt>垛号:</dt><dd>'+info.crib+'</dd></dl> '+
            '  <dl><dt>出货时间:</dt><dd>'+info.outdatetime+'</dd></dl> '+
            '  <dl><dt>出货操作人:</dt><dd>'+info.outoperator+'</dd></dl> '+
            '  <dl><dt>运输车辆:</dt><dd>'+info.outcarno+'</dd></dl> '+
            '  <dl><dt>司机：</dt><dd>'+info.chauffeur+'</dd></dl> '+
            '  <dl><dt>到达工地时间:</dt><dd>'+info.arrivedatetime+'</dd></dl> '+
            '  <dl><dt>到达确认人:</dt><dd>'+info.arrivedor+'</dd></dl>'+
            '</div>';
          html+= '<div class="col-06 content-01">'+
            '  <dl class="border-left"><dt>入库时间:</dt><dd>'+info.indatetime+'</dd></dl>'+
            '  <dl class="border-left"><dt>操作人：</dt><dd>'+info.inoperator+'</dd></dl>'+
            '  <figure class="bg01 border-bottom" style="height:285px;">';
          if(info.isimage==0){
            html+=' <div class="btn-linke none-shipment"></div>';
          }else{
            html+='<img src="'+info.imageurl+'">';
          }
      
          html+='  </figure>'+
            '</div>';
          that.append(html);
        }
        that.find('.loading1').remove();
     }); 
  }

  var InitWindow=function(src,title){
        layer.open({
           title:title,
           type: 1,
           skin: 'layui-layer-rim', //加上边框
           area: ['100%', '100%'], //宽高
           content: '<div align="center"><img src="'+src+'"/></div>'
        });
  }

  var InitWindowFrame=function(src,title){
    layer.open({
        type: 2,
        title: title,
        shade: [0],
        area: ['100%', '100%'],
        content: [src, 'yes']
    });
  }

 
</script>
<!DOCTYPE html>
<html>
<head>
  <title>要货时间</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../css/ued.css"/>
  <link rel="stylesheet" type="text/css" href="../css/common.css"/>
  <link rel="stylesheet" type="text/css" href="../plugin/jquery-ui.min.css"/>
  <link rel="stylesheet" type="text/css" href="../js/layer.mobile.v2.0/need/layer.css"/>
  <link rel="stylesheet" type="text/css" href="../plugin/mdate/css/Mdate.css"/>
</head>
<body class="light-gray">
  <header class="blue">
    <span class="back">
     返回
    </span>
    要货时间
  </header>
  <div class="row">
    <strong class="title">操作人:</strong>
    <span class="p-01 person"></span>
    <span class="p-02 address"></span>
  </div>
  <div class="row">
    <span class="col-04">
      <span class="search">
        <select class="slt_trade">
          <option>项目名称</option>
        </select>
      </span>
    </span>
    <span class="col-04">
      <span class="search">
        <select class="slt_building">
          <option>楼栋</option>
        </select>
      </span>
    </span>
    <span class="col-04">
      <span class="search">
        <select class="slt_floor">
          <option>楼层</option>
        </select>
      </span>
    </span>
  </div>
  <div class="row" style="background-color: #ffffff;">
    <span class="table-container">
      <table class="table">
        <thead>
          <tr>
            <td width="20"></td>
            <td width="35">序号</td>
            <td width="75">构件类型</td>
            <td>要货时间</td>
            <td width="80">PMC确认</td>
          </tr>
        </thead>
        <tbody class="center-align">
          <tr>
            <td colspan="5" class="no-record">
               暂无要货信息
            </td>
          </tr>
        </tbody>
      </table>
    </span>
  </div>
  <div class="row text-align-center">
     <button id="setTime" class="green">设置要货时间</button>
     <button id="submitPMC" class="orange">提交给PMC</button>
     <button id="goToGoodsPlan" class="green">查看要货计划</button>
  </div>
<div class="model" id="model">
  <div class="model-contain">
    <p>要货时间</p>
    <input class="mt-10" id="time" />
    <p class="mt-10">
      <button id="setTimeNow">确定</button>
      <button id="cancel">取消</button>
    </p>
  </div>
</div>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery.cookie.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui.min.js"></script>
<script type="text/javascript" src="../js/layer.mobile.v2.0/layer.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/index.js"></script>
<script type="text/javascript" src="../plugin/mdate/iScroll.js"></script>
<script type="text/javascript" src="../plugin/mdate/Mdate.js"></script>
<script type="text/javascript">
  var uId     = $.cookie('Number');
  var uName   = $.cookie('Name');
  var uOpenID = $.cookie('openID');
  new Mdate("setTime", {
    acceptId: "dateSelectorTwo",
    format:'-',
    onPreDateShow:function(){
      var selectRows=$('.table').find('input[name="radtype"]:checked');
      if(selectRows.length==undefined||selectRows.length==0){
        layerMsg('请选择一个交货产品类型');
        return false;
      }
    },
    onOkDown:function(date){
      var that=$('.table').find('input[name="radtype"]:checked');
      that.parent().siblings('.ReqDate').text(date);
      that.parent().siblings('.StatusName').text('未提交');
      that.parent().parent().data('status',2);
      var tradeid    = $('.slt_trade').find('option:selected').val();
      var buildingno  = $('.slt_building').find('option:selected').val();
      var floorno    = $('.slt_floor').find('option:selected').val();
      var cmpttypeid = that.data('cmpttypeid');
      updateRquestReplenish(tradeid,buildingno,floorno,cmpttypeid,date);    
    }
  });
  $(function(){
    init();
    $('header > .back').on('click',function(){
       window.history.back(-1);
    });

    $('.table > tbody').on('click','tr',function(){
        var status=$(this).data('status');
        $('.table > tbody').find('input[type="radio"]').attr("checked",false);
        if(status==3){
          layerMsg('PMC审核后，不能更改');
          return;
        }
        $(this).find('input[type="radio"]').attr('checked',true);
    });

    $('#submitPMC').on('click',function(){
        var tradeid    = $('.slt_trade').find('option:selected').val();
        var buildingno  = $('.slt_building').find('option:selected').val();
        var floorno    = $('.slt_floor').find('option:selected').val();
        updateApplyPMCStatus(tradeid,buildingno,floorno);
    });

    $('#goToGoodsPlan').on('click',function(){
        var tradeId    = $('.slt_trade').find('option:selected').val();
        var tradeName  =  $('.slt_trade').find('option:selected').text();
        if(_isNull(tradeId)){
           layerAlert('非法操作');
           return;
        }
        location.href="goodsPlan.php?tradeid="+tradeId+"&tradeName="+encodeURI(tradeName);
    });
  })

  var init=function(){
      if(_isNull(uName)){
         window.location.href='login.php';
      }
      $('.person').html(uName);
      initTrade();
      $('.slt_trade').on('change',function(){
         var Option=$(this).find('option:selected');
         var tradeName = Option.text();
         var tradeid   = Option.val();
         if(_isNull(tradeid))
            return;
         $('.address').empty().append(tradeName);
         initBuildings(tradeid);
      });

      $('.slt_building').on('change',function(){
         var OptionTrade=$('.slt_trade').find('option:selected');
         var Option=$(this).find('option:selected');
         var buildingName = Option.val();
         var address=OptionTrade.text()+'&nbsp;&nbsp;'+buildingName+'层';
         $('.address').empty().append(address);
         var buildid = Option.val();
         var tradeid = OptionTrade.val();
         initFloor(tradeid,buildid);
      });

      $('.slt_floor').on('change',function(){
         var OptionTrade    = $('.slt_trade').find('option:selected');
         var OptionBuilding = $('.slt_building').find('option:selected');
         var OptionFloor    = $(this).find('option:selected');
         var address = OptionTrade.text()    +'&nbsp;&nbsp;'+
                       OptionBuilding.text() +
                       OptionFloor.text();
         $('.address').empty().append(address);
         initTradeBinding(0,30);
      });
      //设置要货事件
 
  }
  //项目绑定
  var initTrade=function(){
     var number = $.cookie('Number');
      $.post(url,{action:'getTradeByNumber',number:number},function(data){
         $('.slt_trade').empty();
         $('.slt_building').empty();
         $('.slt_floor').empty();
         $('.table > tbody').empty();
         var norecord='<tr><td colspan="5" class="no-record">'+
                      '暂无要货信息</td></tr>';
         $('.table > tbody').append(norecord);
         if(data.status==0){
           $('.slt_trade').append("<option value=''>项目名称</option>");
           $('.slt_building').append("<option value=''>楼栋</option>");
           $('.slt_floor').append("<option value=''>楼层</option>");
           $.each(data.result,function(i,v){
             $('.slt_trade').append('<option value="'+v.Id+'">'+v.Forshort+'</option>');
           })
         }else{

            layerMsg(data.msg);
         } 
      },'json');
  }
  //楼栋绑定
  var initBuildings=function(tradeid){
       $('.slt_building').empty();
       $('.slt_floor').empty();
       $('.table > tbody').empty();
       var norecord='<tr><td colspan="5" class="no-record">'+
                      '暂无要货信息</td></tr>';
       $('.table > tbody').append(norecord);
       $.post(url,{action:'buildings',tradeid:tradeid},function(data){
         if(data.status==0){
            $('.slt_building').append("<option value=''>楼栋</option>");
            $('.slt_floor').append("<option value=''>楼层</option>");
            $.each(data.result,function(i,v){
              var _opt='<option value="'+v.BuildingNo+'">'+v.BuildingNo+'栋</option>';
              $('.slt_building').append(_opt);
            });
         }else{
            layerMsg(data.msg);
         }
       });
  }
  //楼层绑定
  var initFloor=function(tradeid,buildid){
      $('.slt_floor').empty();
      $('.table > tbody').empty();
      var norecord='<tr><td colspan="5" class="no-record">'+
                      '暂无要货信息</td></tr>';
      $('.table > tbody').append(norecord);
      $('.slt_floor').append("<option value=''>楼层</option>");
      $.post(url,{action:'floor',tradeid:tradeid,buildid:buildid},function(data){
        if(data.status==0){
           $.each(data.result,function(i,v){
             var _opt='<option value="'+v.FloorNo+'">'+v.FloorNo+'层</option>';
             $('.slt_floor').append(_opt);
           });
        }else{
          layerMsg(data.msg);
        }
      });
  }

  var initTradeBinding=function(current,pagenum){

      var tradeId    = $('.slt_trade').find('option:selected').val();
      var buildingId = $('.slt_building').find('option:selected').val();
      var floorId    = $('.slt_floor').find('option:selected').val();
      if(_isNull(tradeId)||_isNull(buildingId)||_isNull(floorId)){
        var norecord='<tr><td colspan="5" class="no-record">'+
                     '暂无要货信息</td></tr>';
        $('.table > tbody').empty().append(norecord);
        return;
      }
      $.post(url,
             {
               action:'getTradeInfoPageExt',
               tradeid:tradeId,
               buildingno:buildingId,
               floorno:floorId,
               current:current,
               pagenum:pagenum
             },
             function(data){
              var result=data.result.data;
              if(result.lenght==0){
                var norecord='<tr><td colspan="5" class="no-record">'+
                      '暂无要货信息</td></tr>';
                $('.table > tbody').append(norecord);
                return;
              }
              $('.table > tbody').empty();
              $.each(result,function(i,item){
                var content='<tr data-status="'+item.Status+'"'+
                            '    data-cmpttypeid="'+item.CmptTypeId+'"> '+
                            '  <td><input type="radio" name="radtype"'+
                            '        disabled="disabled" class="checkbox"'+
                            '        value="'+item.Status+'" '+
                            '        data-cmpttypeid="'+item.CmptTypeId+'"></td>'+
                            '  <td>'+(i+1)+'</td>'+
                            '  <td><a href="deliveryrecord.php?tradeid='+tradeid+'&buildingno='+buildingno+'&floor='+floorId+'&typeid='+item.CmptTypeId+'">'+item.CmptType+'</a></td>'+
                            '  <td class="left-align ReqDate">'+
                                 item.RequestDateTime+
                            '  </td>'+
                            '  <td class="StatusName">'+
                                 item.StatusName+
                            '  </td>'+
                            '</tr> ';
                $('.table > tbody').append(content);
              });
             },'json');
  }

  var updateRquestReplenish=function(tradeid,buildingno,floorno,cmpttypeid,reqdate){
       $.post(url,
              {
                action     : 'setTradeTimeExt',
                tradeid    : tradeid,
                buildingno : buildingno,
                floorno    : floorno,
                cmpttypeid : cmpttypeid,
                requestdatetime:reqdate,
                reqid      : uId,
                reqname    : uName,
                openid     : uOpenID
              },
              function(data){});
  }

  var updateApplyPMCStatus=function(tradeid,buildingno,floorno){
        var arr=Array();
        layerConfirmFunction('是否提交PMC审核',function(){
          $('.table tbody > tr').each(function(i,v){
            var that=$(this);
            var status     = that.data('status');
            var cmpttypeid = that.data('cmpttypeid');
            if(status==1 || status==2){
              var trade='{ "tradeid":"'+tradeid+'",'+
                        '  "buildingno":"'+buildingno+'",'+
                        '  "floorno":"'+floorno+'",'+
                        '  "cmpttypeid":"'+cmpttypeid+'"}';
                  arr.push(trade);
            }
          });
          if(arr.length==0){
            layerMsg('暂无提交审核项');
            return;
          }
          var json='['+arr.join(',')+']';
          $.post(url,
                {
                  action:'setTradeStateByParamExt',
                  param:json
                },
                function(data){
                  layerAlertFunction('项目已提交，请等待审核',function(index){
                     initTradeBinding(0,30);
                     layer.close(index);
                  });
                });
        });
  }


</script>

</body>
</html>
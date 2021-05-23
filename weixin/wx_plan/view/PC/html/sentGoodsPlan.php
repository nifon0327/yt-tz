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

   }
?>
<!DOCTYPE html>
<html>
<head>
  <title>发货计划</title>
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
         <button class="green btnSearch">查询</button>
      </span>
    </div>
  </div>
  <div class="row">
    <span class="table-container bg-white">
      <table class="table">
       <thead>

       </thead>
       <tbody class="center-align">
         <tr>
           <td class="no-record">
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
    var deliveryDateTime;
    var page=this;
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
             layer.alert('请选择项目',{icon:5,shade:0});
            return;
          }
          var buildingno=$('.slt_building').find('option:selected').val();
          if(_isNull(buildingno)){
            layer.alert('请选择楼栋',{icon:5,shade:0});
            return;
          }

          var floorno=$('.slt_floor').find('option:selected').val();
          var _loading='<tr><td class="loading" colspan="10">'+
                       '  数据查询中...'+
                       '</td></tr>';
          $('.table > tbody').empty().append(_loading);
          $('.table > thead').empty();
          initSendPlan(tradeid,buildingno,floorno,0,20);
        });

        $('.table > tbody').on('click','.delivery',function(){
           var edit=$(this).hasClass('edit');
           if(!edit){
             var date=$(this).text();
             page.deliveryDateTime=date;
             var _hdate='<span><input type="text" name="date" class="deliveryDate" value="'+date+'"></span>';
             $(this).empty().append(_hdate);
             $(this).addClass('edit');
           }


           lay('.deliveryDate').each(function(){
             var that=this;
             laydate.render({
               elem: that
               ,trigger: 'click'
               ,done: function(value, date){
                  var mythat=$(that).parent().parent();
                  mythat.empty().append(value);
                  mythat.removeClass('edit');
                  var tradeid    = mythat.data('tradeid');
                  var buildingno = mythat.data('buildingno');
                  var floor      = mythat.data('floor');
                  var cmpttypeid = mythat.data('cmptypeid');
                  saveReplenishShipments(tradeid,buildingno,floor,cmpttypeid,value,mythat);
               }
             });
           }); 

        })


    };
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

    var initSendPlan=function(tradeid,buildingno,floorno,current,pagesize){
          webPOST({
            action     : 'getTradeSentPlan',
            objectid   : tradeid,
            buildingno : buildingno,
            floorno    : floorno,
            current    : current,
            pagesize   : pagesize
          },function(data){
             if(data.status==0){
                 var result=data.result;
                 _cmptTypes = result.CmptType;
                 _floors    = result.Floor;
                 _orders    = result.Orders;

                 if(_cmptTypes.length==undefined){
                    var _error='<tr><td class="no-record" >'+
                           '   查询为空'+
                           '</td></tr>';
                    $('.table > tbody').empty().append(_error);
                    return;
                 }
                 $('.table > thead').empty();
                 $('.table > tbody').empty();
                 theader(buildingno,_cmptTypes);
                 tbodyer(_floors,_cmptTypes,_orders,buildingno,tradeid);
             }else{
                var _error='<tr><td class="error">'+
                           '  数据较大，请选择楼层后重新查询'+
                           '</td></tr>';
                $('.table > tbody').empty().append(_loading);
             }
          });
    }
    var theader=function(buildingno,cmptTypes){
         var cmptLength=cmptTypes.length+1;
         var _header='<tr>'+
                     '  <th colspan="'+cmptLength+'" style="line-height: 40px;font-size:12pt;">'+buildingno+'栋</th>'+
                     '</tr>'+
                     '<tr>'+
                     '  <th width="60" style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;类型<br/>层数</th>';
         $.each(cmptTypes,function(i,row){
           _header+='<th>'+row.CmptType+'</th>';

         });
            _header+='</tr>';
         $('.table > thead').empty().append(_header);

    }

    var tbodyer=function(floors,cmptTypes,orders,buildingno,tradeid){
        $.each(floors,function(i,floor){
          var _html='<tr>';
              _html+='<td class="seq">'+floor+'层</td>';

          $.each(cmptTypes,function(i,type){
              var flag=false;
              $.each(orders,function(i,order){
                if(order.FloorNo==floor&&order.CmptTypeId==type.CmptTypeId){
                  _html+='<td>'+
                         '  <span class="delivery" '+
                         '     data-floor="'+floor+'" '+
                         '     data-cmptypeid="'+type.CmptTypeId+'"'+
                         '     data-buildingno="'+buildingno+'"'+
                         '     data-tradeid="'+tradeid+'" >'+
                                 order.DeliveryTime +
                         '  </span>'+
                         '  <span class="lft">'+
                         '    已发：'+order.SendQty+
                         '    未发：'+order.NoneSendQty+
                         '   <br/>最后发货时间:<br/>&nbsp;&nbsp;' +order.ModifiedDateTime+
                         '  </span>'+
                         '</td>'; 
                  flag=true;
                }
              })

              if(!flag){
                 _html+='<td class="not"></td>';
              }
               
           });
          _html+='</tr>';
          $('.table > tbody').append(_html);
        })
    }
    
    var saveReplenishShipments=function(tradeid,buildingno,floor,cmpttypeid,deliverydate,my){
        webPOST({
          action      : 'setShipMentsTime',
          TradeId     : tradeid,
          BuildingNo  : buildingno,
          FloorNo     : floor,
          CmptTypeId  : cmpttypeid,
          DeliveryDate:deliverydate

        },function(data){
          if(data.status==0){
             layer.msg('时间设置成功',{icon:6,shade:0});
          }else{
            layer.alert('时间设置失败，请重新设置',{icon:5,shade:0});
            mythat.text(that.deliveryDateTime);
          }

        })
    }

  </script>
</body>
</html>

<?php
  session_start();
  include '../../../config/dbconnect.php';
  include '../../../config/common.model.php';
  $common=new common();
  $URI=$_SERVER["REQUEST_URI"];
  $wxUseInfo=$common->WXInit($URI,'transportRecords.php');
  //var_dump($wxUseInfo);
?>
<!DOCTYPE html>
<html>
<head>
  <title>运输车次时间记录</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../css/ued.css"/>
  <link rel="stylesheet" type="text/css" href="../css/common.css"/>
  <link rel="stylesheet" type="text/css" href="../js/layer.mobile.v2.0/need/layer.css"/>
  <link rel="stylesheet" type="text/css" href="../plugin/mdate/css/Mdate.css"/>
  <link rel="stylesheet" type="text/css" href="../plugin/jqueryui/jquery-ui.min.css">
  <link rel="stylesheet" type="text/css" href="../plugin/webuploader/webuploader.css">
</head>
<body class="light-gray">
  <header class="blue">
    <span class="back">
     返回
    </span>
    运输车次时间记录
  </header>
  <div class="row">
    <span class="col-06">
      <span class="search col">
        <input type="text" name="carNumber" id="carNumber" placeholder="车次编号">
      </span>
      
    </span>
    <span class="col-06">
      <span class="search col">
         <select name="carNo">
            <option value="">车牌号</option>
         </select>
      </span>
    </span>
  </div>
  <div class="row" style="margin-top: -5px;">
    <span class="col ul-table">
      <dl>
        <dd>
           <span class="cot forshort" ></span>
        </dd>
        <dd><span class="cot carno"></span></dd>
      </dl>
      <dl class="bottom">
        <dd><span class="cot shipinfo"></span></dd>
        <dd><span class="cot"></span></dd>
      </dl>
    </span>
     
  </div>
  <div class="row">
    <span class="table-container bg-white">
       <table class="table" id="ShipInfo">
        <thead>
          <tr>
            <td width="35">序号</td>
            <td width="90">项目</td>
            <td>要货时间</td>
            <td width="70">司机</td>
            <td width="70">负责人</td>
          </tr>
        </thead>
        <tbody>
           <tr>
            <td colspan="6" class="no-record">
              暂无跟踪记录
            </td>
          </tr>
        </tbody>
      </table>
    </span>
  </div>
  <div class="row button-container text-align-center">
     <button class="blue receiving" disabled="disabled">
       到厂确定
     </button>
     <button class="blue leavefactory" disabled="disabled">
       出厂确定
     </button>
     <button class="blue construction" disabled="disabled">
       到达施工确定
     </button>
  </div>
  <div class="row button-container text-align-center">
     <button class="blue hoisting" disabled="disabled">
       吊装完成确认
     </button>
     <button class="blue truck" disabled="disabled">
       押车
     </button>
  </div>
  <iframe id="geoPage" width="0" height="0" frameborder=0 scrolling="no" style="position: absolute;z-index: -1000;" src="https://apis.map.qq.com/tools/geolocation?key=B7HBZ-EDACJ-3RLFY-FQTAD-442OF-IWFOR&referer=myapp&effect=zoom"></iframe>
  <script type="text/javascript" src="../js/jquery.js"></script>
  <script type="text/javascript" src="../plugin/jqueryui/jquery-ui.min.js"></script>
  <script type="text/javascript" src="../js/jquery.cookie.js"></script>
  <script type="text/javascript" src="../js/layer.mobile.v2.0/layer.js"></script>
  <script type="text/javascript" src="../js/jquery.form.js"></script>
  <script type="text/javascript" src="../js/common.js"></script>
  <script type="text/javascript" src="../plugin/webuploader/webuploader.min.js"></script>
  <script type="text/javascript" src="../js/mywebuploader.js"></script>
 </body>
</html>
<script type="text/javascript">
  var address='';
  var uId='<?php echo $wxUseInfo["UId"]?>';
  var uName='<?php echo $wxUseInfo["UName"]?>';
  var TrueName='<?php echo $wxUseInfo["TrueName"]?>';
  var GroupId='<?php echo $wxUseInfo["GroupId"]?>';
  var GroupName='<?php echo $wxUseInfo["GroupName"]?>';
  var OpenId='<?php echo $wxUseInfo["OpenId"]?>';
  var RoleName='<?php echo $wxUseInfo["RoleName"]?>';
  var RoleId='<?php echo $wxUseInfo["RoleId"]?>';
  var CurrentDate="<?php echo date('Y-m-d H:i:s')?>";
  var tradeId,buildingNo,floorNo,carNo,carNumber;
  var transportCheckState=-1,transportID=0,transportTypeCode=0;

       // RoleName = '司机';
       // RoleId   = 38;
       // TrueName ='测试';

       // RoleName = '物流组';
       // RoleId   = 13;
       // TrueName ='物流组-人员';


       // RoleName = '项管';
       // RoleId   = 20;
       // TrueName = '项管-人员';


  var that=this;
  that.GroupId=_isNull(that.GroupId)?0:that.GroupId;
  that.OpenId=_isNull(that.OpenId)?0:that.OpenId;
  that.TrueName=_isNull(that.TrueName)?that.uName:that.TrueName;
  that.RoleId=_isNull(that.RoleId)?0:that.RoleId;
  window.addEventListener('message', function(event) { 
    // 接收位置信息
    var loc = event.data||{};
    that.address=loc.city+loc.addr;           
  }, false);
  $(function() {
    $("#carNumber" ).autocomplete({
      source: function(request,response){
          $.post(url,
                {
                  action:'getcarnumber',
                  keyword:request.term
                },
                function(data){
                   response(data.result);
                },'json');
      },
      minLength:0,
      select:function(event, ui){
         var carNumber=ui.item.value;
         $.post(url,
                {
                  action:'getcarno',
                  carno:ui.item.value
                },
                function(data){
                  var carNo='';
                  if(data.status==0){
                    $('select[name="carNo"]').empty();
                    $.each(data.result,function(i,v){
                      carNo=(i==0)?v.CarNo:'';
                       var option='<option value="'+v.CarNo+'">'+
                                     v.CarNo+
                                   '</option>';
                       $('select[name="carNo"]').append(option);
                    });
                    if(_isNull(carNo)){
                      $('select[name="carNo"]').empty()
                      .append('<option value="">车牌号</option>');
                       $('.cot').empty();
                       var epy='<tr>'+
                                '  <td colspan="6" class="no-record">'+
                                '   暂无跟踪记录'+
                                '</td>'+
                                '</tr>';
                       $('.tbody').empty().append(epy);
                       $('.blue').attr('disabled','disabled');

                      layerMsg('车次编号对应车牌号为空');
                      return;
                    }
                    transportRecordBind(carNo,carNumber);
                  }else{
                    layer.layerMsg(data.msg);
                  }   
                },'json');
      },
      search:function(event,ui){
        if(ui.length==undefined){
          $('select[name="carNo"]').empty()
          .append('<option value="">车牌号</option>');
          $('.cot').empty();
          var epy='<tr>'+
                  '  <td colspan="6" class="no-record">'+
                  '   暂无跟踪记录'+
                  '</td>'+
                  '</tr>';
          $('.tbody').empty().append(epy);
          $('.blue').attr('disabled','disabled');

        }
        
      }
    });

    onInit();
  })

  var onInit=function(){
       $('.receiving').on('click',function(){
         if(that.transportCheckState==-1){
           var _title='到厂确定-司机';
           var _html='<span class="row">'+CurrentDate+'</span>'+
                     '<span class="row">'+ that.address+'</span>';
           layerOpenWindos(_title,_html,['确定','取消'],function(){
              
              saveReplenishTransportRecord(1,that.address,'','');
            });
         }else{
           _title='到厂确定-物流组';
           _html='<span class="row">'+CurrentDate+'</span>';
           layerOpenWindos(_title,_html,['确定','取消'],function(){

              setCheckInfomation(that.transportID);
            });
         }
       });

       $('.leavefactory').on('click',function(){
          if(that.transportCheckState==0){
              _title='出厂时间-物流组';
              _html='<form id="fileForm" action="'+fileURL+'" method="post" enctype="multipart/form-data">'+
                    '  <span class="row">'+CurrentDate+'</span>'+
                    '  <span class="row fileinfo"></span>'+
                    '  <span class="row">'+
                    '    <span class="file-upload">'+
                    '      文件上传'+
                    '      <input type="file" name="file">'+
                    '    </span>'+
                    '  </span>'+
                    '</form>';
              layerOpenWindos(_title,_html,['确定','取消'],
                function(){
                  var filePath=$('#fileForm').find('input[name="file"]').val();
                  if(_isNull(filePath)){
                     layerAlert('请选择上传内容');
                     return;
                  }else{
                     $('#fileForm').ajaxSubmit(function(data){
                        if(data.status==0){
                           var path=data.result;
                           saveReplenishTransportRecord(2,'',path,'');
                        }else{
                          layerAlert('文件上传失败');
                        }
                     });
                  }
                });
          }else{
           _title='出厂时间-司机';
           _html='<span class="row">'+CurrentDate+'</span>';
           layerOpenWindos(_title,_html,['确定','取消'],function(){
              setCheckInfomation(that.transportID);
            });
          }
       });
        
       $('.construction').on('click',function(){
          if(that.transportCheckState==0){
           var _title='到达工地时间-司机';
           var _html='<span class="row">'+CurrentDate+'</span>'+
                     '<span class="row">'+address+'</span>';
           layerOpenWindos(_title,_html,['确定','取消'],function(){
              saveReplenishTransportRecord(3,address,'','');
            });
          }
          // else if(that.transportCheckState==1){
          //  _title='到达工地时间-物流组';
          //  _html='<span class="row">'+CurrentDate+'</span>';
          //  layerOpenWindos(_title,_html,['确定','取消'],function(){
          //     setCheckInfomation(that.transportID);
          //   });
          // }
       });
       
       $('.hoisting').on('click',function(){

          if(that.transportCheckState==0){
            var _title='吊装完成时间-现场负责人';
            var _html='<span class="row">'+CurrentDate+'</span>'+
                      '<span class="row">'+address+'</span>';
            layerOpenWindos(_title,_html,['确定','取消'],function(){
              saveReplenishTransportRecord(4,address,'','');
            });
          }else if(that.transportCheckState==1){
            _title='吊装完成时间-司机';
            _html='<span class="row">'+CurrentDate+'</span>';
            layerOpenWindos(_title,_html,['确定','取消'],function(){
              setCheckInfomation(that.transportID)
            });
          }
        });

        $('.truck').on('click',function(){
            var _title='押车原因';
            var _html='<span class="row">'+CurrentDate+'</span>'+
                       '<span class="row truck">'+
                       ' <textarea name="ridingRecord" cols="35" rows="5" id="ridingRecord"> </textarea>'+
                       '</span>';
            $('row truck').find('#ridingRecord').focus();
            layerOpenWindos(_title,_html,['确定','取消'],function(){
               var ridingRecord=$('body').find('#ridingRecord').val();
               // alert(ridingRecord);
               saveReplenishTransportRecord(5,address,ridingRecord,'');
            });
        });
        $('body').on('change','input[type="file"]',function(){
           var val=$(this).val();
           $('.fileinfo').html(val);
        });
     
  }

  var transportRecordBind=function(carNo,carNumber){
      var _typeId=0;
      var _checkState=-1;
      if(_isNull(carNumber)){
          return;
      }
      $.post(url,
             {
               action:'getReplenishTransportRecord',
               carnumber:carNumber
             },
             function(data){
              if(data.status== 0){
                var shipinfo=data.result;
                that.tradeId   =shipinfo.TradeId;
                that.buildingNo=shipinfo.BuildingNo;
                that.floorNo   =shipinfo.FloorNo;
                that.carNo     =carNo;
                that.carNumber =carNumber;
                $('.forshort').text(shipinfo.Forshort);
                $('.carno').text(shipinfo.CarNo);
                $('.shipinfo').text(shipinfo.ShipInfo);
                $('#ShipInfo > tbody').empty()
                if(shipinfo.Records.length==0){
                  var _none='<tr><td colspan="6" class="no-record">'+
                            '暂无跟踪记录</td></tr>';
                   $('#ShipInfo > tbody').append(_none);
                   that.transportCheckState=-1;
                   if(that.RoleId==38){
                     $('.receiving').removeAttr('disabled');
                   }
                   return;
                }

                
                $.each(shipinfo.Records,function(i,v){
                  _typeId=parseInt(v.TypeID);
             

                  if(!_isNull(v.CheckerBy)&&!_isNull(v.CreateBy)){
                      _checkState=0;
                  }else if(_isNull(v.CheckerBy)&&!_isNull(v.CreateBy)){
                      _checkState=1;

                  }
                  that.transportID=v.Id;
                  var _transText='',
                      _createBy='',
                      _checkerBy='';
                  switch(_typeId)
                  {
                     case 1:
                        _transText=v.CreateDateTime+'<br/>'+v.Address;
                        _createBy=v.CreateBy;
                        _checkerBy=v.CheckerBy;
                       break;
                     case 2:
                        _createBy= v.CheckerBy;
                        _checkerBy= v.CreateBy;
                        _transText=v.CreateDateTime+'<br/>'+
                                   '<a href="'+site+'/weixin/'+
                                     v.Col01+'">附件</a>'
                       break;
                     case 3:
                        _checkState=0;
                        _createBy=v.CreateBy;
                        _checkerBy=v.CheckerBy;
                        _transText=v.CreateDateTime+'<br/>'+v.Address;
                       break;
                     case 4:
                        _createBy= v.CheckerBy;
                        _checkerBy= v.CreateBy;
                        _transText=v.CreateDateTime+'<br/>'+v.Address;
                       break;
                     case 5:
                        _createBy=v.CheckGroupID==38?v.CheckerBy:v.CreateBy;
                        _checkerBy= v.CheckGroupID==20?v.CheckerBy:v.CreateBy;;
                        _transText=v.CreateDateTime+'<br/>'+v.Col01;
                       break;

                  }
                  var _html='<tr>'+
                            '  <td class="text-align-center">'+_typeId+'</td>'+
                            '  <td class="text-align-center">'+getStepName(_typeId)+'</td>'+
                            '  <td class="text-align-center">'+
                                _transText+
                            '  </td>'+
                            '  <td class="text-align-center">'+_createBy+'</td>'+
                            '  <td class="text-align-center">'+_checkerBy+'</td>'+
                            '</tr>';
                  $('#ShipInfo > tbody').append(_html);
                })
                
                that.transportCheckState=_checkState;
                buttonState(_typeId,_checkState);
              }
             }); 
  }
  

  
  var buttonState=function(typeid,_checkState){
      
       // console.log('roleid='+that.RoleId);
       // console.log('typeid=='+typeid);
       // console.log('checkState=='+_checkState);
       // console.log('');
       buttonContainerDisabled();
       switch(typeid){
         case 0:
            if(that.RoleId==38){
              $('.receiving').removeAttr('disabled');
            }
           break;
         case 1:
             
             if(_checkState==1&&that.RoleId==13){
                $('.receiving').removeAttr('disabled'); 
             }else if(_checkState==0&&that.RoleId==13){
                $('.leavefactory').removeAttr('disabled'); 
             }
             
           break;
         case 2:
             if(_checkState==1&&that.RoleId==38){
               $('.leavefactory').removeAttr('disabled');
             }else{
              if(_checkState==0&&that.RoleId==38){
                $('.construction').removeAttr('disabled');
              }
             }
           break;
         case 3:

             if(_checkState==1&&that.RoleId==38){
                // $('.hoisting').removeAttr('disabled');
                //$('.construction').removeAttr('disabled');
             }else{
              if(_checkState==0&&that.RoleId==20){
                $('.hoisting').removeAttr('disabled');
              }
             }   
           break;
         case 4:
             
            
             if(_checkState==1&&that.RoleId==38){
                $('.hoisting').removeAttr('disabled');
             }else if(_checkState==0&&(that.RoleId==20||that.RoleId==38)){
                $('.truck').removeAttr('disabled');
             }
           break;
         case 5:
            if(that.RoleId==20||that.RoleId==38){
               $('.truck').removeAttr('disabled');
            }
           break;
         default:
              $('.receiving').removeAttr('disabled');
           break;
       }
  }

  var getStepName=function(typeid){
       var stepName='';
       switch(parseInt(typeid)){
         case 1:
            stepName='到厂时间';
           break;
         case 2:
            stepName='出厂时间';
           break;
         case 3:
            stepName='到达工地时间';
           break;
         case 4:
            stepName='吊装完成时间';
           break;
         case 5:
            stepName='押车原因';
           break;
         default:
            stepName='';
           break;
       }
       return stepName;
  }

  var saveReplenishTransportRecord=function(typeid,address,col01,col02){
       $.post(url,
              {
                action        : 'setReplenishTransportRecord',
                typeid        : typeid,
                address       : address,
                createdatetime:that.CurrentDate,
                createby      :that.TrueName,
                createuserid  :that.uId,
                groupusername :that.GroupName,
                groupuserid   :that.GroupId,
                col01         :col01,
                col02         :col02,
                carno         :that.carNo,
                carnumber     :that.carNumber,
                tradeId       :that.tradeId,
                buildingno    :that.buildingNo,
                floorno       :that.floorNo

              },
              function(data){
                transportRecordBind(that.carNo,that.carNumber);
              },'text');
  }

  var setCheckInfomation=function(id){
       $.post(url,{
         action:'setReviewedBy',
         CheckerBy:TrueName,
         CheckerUserID:uId,
         CheckGroupID:RoleId,
         CheckGroupName:RoleName,
         ID:id
       },function(data){
         var rtv=JSON.stringify(data);
         layerAlertFunction(data.msg,function(index){
            transportRecordBind(that.carNo,that.carNumber);
            layer.close(index);
         });
       });
  }


  var buttonContainerDisabled=function(){
     $('.button-container > button').each(function(i,b){
        var btn=$(this);
        if(!btn.prop('disabled')){
            btn.attr('disabled','disabled');
        }
     });
  }
</script>

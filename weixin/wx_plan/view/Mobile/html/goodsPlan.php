<?php
   $tradeId   = isset($_GET['tradeid'])   ? $_GET['tradeid']:'';
   $tradeName = isset($_GET['tradeName']) ? $_GET['tradeName']:'';
   if(empty($tradeName)){
      header('Location:noright.php');
      die();
   }
?>
<!DOCTYPE html>
<html>
<head>
  <title>要货计划</title>
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
    要货计划
  </header>
  <div class="row title">
     <h1><?php echo $tradeName;?></h1>
  </div>
  <div class="row">
    <span class="col-04">
      <span class="search">
        <input type="text" readonly="readonly" name="reqdate" id="slt_reqdate"  placeholder="要货时间" class="input-date-icon">
      </span>
    </span>
    <span class="col-04">
      <span class="search">
        <select class="slt_building" placeholder="楼栋">
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
  <div class="row">
    <span class="table-container bg-white">
       <table class="table">
        <thead>
          <tr>
            <td width="35">序号</td>
            <td width="40">楼栋</td>
            <td width="40">楼层</td>
            <td width="70">构件类型</td>
            <td>要货时间</td>
            <td width="55">状态</td>
          </tr>
        </thead>
        <tbody class="center-align">
          <tr>
            <td colspan="6" class="no-record">
               暂无要货信息
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery.cookie.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui.min.js"></script>
<script type="text/javascript" src="../js/layer.mobile.v2.0/layer.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../plugin/mdate/iScroll.js"></script>
<script type="text/javascript" src="../plugin/mdate/Mdate.js"></script>
  <script type="text/javascript">
     var tradeId='<?php echo $tradeId;?>';
     new Mdate("slt_reqdate",{
                    format:'-',
                    onOkDown:function(date){
                      var buildingNo = $('.slt_building').find('option:selected').val();
                      var floorNo    = $('.slt_floor').find('option:selected').val();
                      tableBind(buildingNo,floorNo,date,0,60);

                    }
              });
     $(function(){
        $('header > .back').on('click',function(){
           history.back(-1);
        });
        $('.slt_building').on('change',function(){
            var buildingNo=$(this).val();
            initFloor(tradeId,buildingNo);
            var reqdate=$('#slt_reqdate').val();
            console.log(reqdate);
            var buildingNo = $('.slt_building').find('option:selected').val();
            var floorNo    = $('.slt_floor').find('option:selected').val();
            tableBind(buildingNo,floorNo,reqdate,0,60);
        });

        $('.slt_floor').on('change',function(){
            var buildingNo = $('.slt_building').find('option:selected').val();
            var floorNo    = $('.slt_floor').find('option:selected').val();
            var reqdate=$('#slt_reqdate').val();
            tableBind(buildingNo,floorNo,reqdate,0,60);
        })
        onInit();
     })

     var onInit=function(){
          initBuildings(tradeId);
          tableBind(0,0,0,0,60);
     }

     //楼栋绑定
     var initBuildings=function(tradeid){
       $('.slt_building').empty();
       $('.slt_floor').empty();
       $('.table > tbody').empty();

       var norecord='<tr><td colspan="6" class="no-record">'+
                      '查询中...</td></tr>';
       $('.table > tbody').append(norecord);
       if(_isNull(tradeid))
          return;
       $.post(url,{action:'buildings',tradeid:tradeid},function(data){
         if(data.status==0){
            $('.slt_building').append("<option value='0'>楼栋</option>");
            $('.slt_floor').append("<option value='0'>楼层</option>");
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
       var norecord='<tr><td colspan="6" class="no-record">'+
                      '查询中...</td></tr>';
       $('.table > tbody').append(norecord);
       $('.slt_floor').append("<option value='0'>楼层</option>");
       if(_isNull(buildid))
          return;
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

     var tableBind=function(buildingNo,floorNo,requestdate,current,pagenum){
          $.post(url,
                 {
                   action:'getPMCTradeRequestInfoPageExt',
                   tradeid:tradeId,
                   buildingno:buildingNo,
                   floorno:floorNo,
                   requestdatetime:requestdate,
                   current:current,
                   pagenum:pagenum
                 },
                 function(data){
                    var result=data.result.data;
                    if(result.lenght==0){
                      var norecord='<tr><td colspan="6" class="no-record">'+
                            '暂无要货信息</td></tr>';
                      $('.table > tbody').append(norecord);
                      return;
                    }
                    $('.table > tbody').empty();
                    $.each(result,function(i,item){
                        var content='<tr> '+
                                    '  <td>'+(i+1)+'</td>'+
                                    '  <td>'+item.BuildingNo+'栋</td>'+
                                    '  <td>'+item.FloorNo+'层</td>'+
                                    '  <td>'+item.CmptType+'</td>'+
                                    '  <td class="left-align ReqDate">'+
                                         item.RequestDateTime+
                                    '  </td>'+
                                    '  <td class="StatusName">'+
                                      item.StatusName+
                                    '  </td>'+
                                    '</tr> ';
                        $('.table > tbody').append(content);
                    });
                  });
     }
  </script>
</body>
</html>

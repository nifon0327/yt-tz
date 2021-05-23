<?php
  session_start();
  include '../../../config/dbconnect.php';
  include '../../../config/common.model.php';
  $common=new common();
  $URI=$_SERVER["REQUEST_URI"];
  $wxUseInfo=$common->WXInit($URI,'pmc.php');
  if($wxUseInfo['RoleName']!='资材'){
    header('Location:noright.php');
    die();
  }

  
?>
<!DOCTYPE html>
<html>
<head>
  <title>要货时间确定</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../css/ued.css"/>
  <link rel="stylesheet" type="text/css" href="../css/common.css">
  <link rel="stylesheet" type="text/css" href="../css/goodsTime.css">
  <link rel="stylesheet" type="text/css" href="../plugin/jquery-ui.min.css">
  <style type="text/css">
     select{
       width: auto;
     }
  </style>
</head>
<body>
<div class="bar">
  要货时间确定
</div>
<p class="mt-30">操作人：<span id="operator"></span><span id="address"></span></p>
<p class="select-box mt-30">
  <select id="xiangMu">
     <option>&nbsp;</option>
  </select>
  <select id="louDong">
    <option>&nbsp;</option>
  </select>
  <select id="louCeng">
    <option>&nbsp;</option>
  </select>
</p>
<div class="table-box mt-30">
  <table class="table">
    <thead>
    <tr>
      <th width="20"></th>
      <th width="35">序号</th>
      <th width="80">构件类型</th>
      <th >要货时间</th>
      <th width="70">PMC确认</th>
    </tr>
    </thead>
    <tbody id="tableList">
      <tr>
        <td colspan="5" style="color:#c2c2c2;padding: 8px 2px;">暂无审核信息</td>
      </tr>
    </tbody>
  </table>
</div>
<div id="pagination"></div>
<div class="btn-box mt-30">
    <button id="btn_state">确定</button>
</div>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery.cookie.js"></script>
<script type="text/javascript" src="../js/layer.mobile.v2.0/layer.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/index.js"></script>
<script type="text/javascript">
  var objectid=0,buildingsno=0,floorno=0;
  !(function () {
    $('#operator').html('<?php echo $wxUseInfo['TrueName']?>');
    getXiangMu();
    function getXiangMu () {
      myPost({
        data: {
          action: 'tradeObject'
          // objectid: $.cookie('objectid')
        },
        successFn: function (data) {
          if (status == 0) {
            $('#xiangMu').empty();
            $.each(data.result,function(i,v){
               if(i==0){
                  objectid=v.Id;
               }
               var _option='<option value="'+(v.Id)+'">'+(v.Forshort)+'</option>';
               $('#xiangMu').append(_option);
            });
            getLouDong(objectid);
          }else{
            layer.open({
              content: data.msg
              ,btn: '确定'
            });
          }
        }
      });
    }
    function getLouDong (objectid) {
      $('#louDong').empty();
      myPost({
        data: {
          action: 'buildings',
          tradeid: objectid
        },
        successFn: function (data) {
          if (status == 0) {       
            $.each(data.result,function(i,v){
               if(i==0){
                  buildingsno=v.BuildingNo;
               }
               var _option='<option value="'+v.BuildingNo+'">'+v.BuildingNo+'栋</option>';
               $('#louDong').append(_option);
            });
            getLouCeng(objectid,buildingsno);
          }
        }
      });
    }
    function getLouCeng (objectid,buildingsno) {
      $('#louCeng').empty();
      myPost({
        data: {
          action: 'floor',
          tradeid: objectid,
          buildid: buildingsno
        },
        successFn: function (data) {
          if (status == 0) {
            $.each(data.result,function(i,v){
              var _option='<option value="'+v.FloorNo+'">'+v.FloorNo+'层</option>';
              $('#louCeng').append(_option);
            });
          }
          getTable();
        }
      });
    }
    
   
    
    $('#btn_state').on('click',function(){
      changeState();
    });

   
    $('#xiangMu').on('change',function(){
       var tradeid=$(this).find('option:selected').val();
       $('#tableList').empty();
       getLouDong(tradeid);
    })
    $('#louDong').on('change',function(){
      $('#tableList').empty();
      var tradeid=$("#xiangMu").find('option:selected').val();
      var buildingsno=$(this).find('option:selected').val();
      getLouCeng(tradeid,buildingsno);
    })
    $('#louCeng').on('change',function(){
      $('#tableList').empty();
      getTable();
    })
   
  })();

  var changeState=function(){
      var ids=new Array();
      var checkid = <? echo $wxUseInfo['UId']?>;
      var checker = '<? echo $wxUseInfo['TrueName']?>';
      var openid  = '<? echo $wxUseInfo['OpenId']?>';
      $('tbody input[type="radio"]:checked').each(function(i,v){
        ids.push($(this).val());
      });
      if(ids.length==0){
          layerAlert('请选择审核项目');
          return;
      }
      $_ids=ids.join(',');
      layer.open({
          content: '是否通过项目审核？'
          ,btn: ['审核', '不审核']
          ,yes: function(index){
             $.post(url,
              {
                action:'setTradeStateExtByIds',
                statecode:3,
                ids:$_ids,
                checkid:checkid,
                checker:checker,
                openid:openid
              },
              function(data){
                if(data.status==0){
                  layerAlertFunction('审核通过',function(index){
                    $('#tableList').empty();
                    getTable();
                    layer.close(index);
                  });
                }else{

                }
                //console.log(data);
             },'json');
           
           layer.close(index);
          }
      });
  }
  
  var getTable = function(curPage){
      var tradename  = $('#xiangMu').find("option:selected").text();
      var tradeid    = $('#xiangMu').find("option:selected").val();
      var buildingno = $('#louDong').find("option:selected").val();
      var floorno    = $('#louCeng').find("option:selected").val();
      if(_isNull(buildingno)){
         layerMsg('请选择楼栋');
         return;
      }

      if(_isNull(floorno)){
        layerMsg('请选择楼层');
        return;
      }
      var _addr  = _isNull(tradename)  ? '':tradename+'  ';
          _addr += _isNull(buildingno) ? '':buildingno+'栋';
          _addr += _isNull(floorno)    ? '':floorno+'层';
      $('#address').text(_addr);
      myPost({
        data: {
          action     : 'getPMCTradeInfoPageExt',
          tradeid    : tradeid,
          buildingno : buildingno,
          floorno    : floorno,
          requestdatetime : '',
          current : curPage||1,
          pagenum : 15,
        },
        successFn: function (data) {
          if (data.status == 0) {
            $('#tableList').empty();
            var row=0;
            $.each(data.result.data,function(i,node){
               row=i;
               var _html ='<tr data-state="'+node.Status+'">';
                   _html+=' <td><input type="radio" disabled="disabled" class="checkbox"   value="'+node.ReplenishID+'"></td>';
                   _html+=' <td>'+(i+1)+'</td>';
                   _html+=' <td>'+(node.CmptType)+'</td>';
                   _html+=' <td>'+(node.RequestDateTime)+'</td>';
                   _html+=' <td>'+(node.StatusName)+'</td>';
                   _html+='</tr>';
               $('#tableList').append(_html); 
            });
            if(row<0){
              $('#tableList').append('<tr><td colspan="5" style="color:#c2c2c2;padding:8px 2px;">暂无审核信息</td></tr>');
            }
            $('.table tr').on('click',function(){
              var state=$(this).data('state');
              if(state==2){
                if($(this).find('input[type="radio"]').is(':checked')){
                   $(this).find('input[type="radio"]').attr("checked",false);
                }else{
                   $(this).find('input[type="radio"]').attr('checked',true);
                }
              }
            });
            Pagination({
              activeIndex: data.result.current, // 当前活动页
              totalPage: data.result.pagesize, // 分页总页数
              showNumberOfPage: false, // 是否可切换每页数量，boolen类型
              father: '#pagination', // 插槽id
              goToPage: function (index) {
                // 切换分页回调函数，index为要去第几页
                getTable(index);
              },
            })
          }
        }
      });
  }



</script>
</body>
</html>
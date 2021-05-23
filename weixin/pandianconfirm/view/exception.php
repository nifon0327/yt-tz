<?php
/**
 * User: Elina
 * Date: 2018/11/27
 */

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Expires" content="0">
  <meta http-equiv="Cache-Control" content="no-cache">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="renderer" content="webkit">
  <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
  <meta name="format-detection" content="telephone=no">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <title>异常构件综合页</title>
  <link rel="stylesheet" type="text/css" href="./resource/bootstrap-table-origin.css">
  <link rel="stylesheet" type="text/css" href="./resource/base.css">
  <style type="text/css">
  body{padding: 0 5%}
  header{font-size: 20px;line-height: 80px;text-align: center; font-weight: 700;}
  article{display: block;}
  #noData{text-align: center; padding: 30px 0; color: darkred; border-top: solid 1px darkgray; border-bottom: solid 1px darkgray; margin-top: 30px;}
  </style>
</head>
<body>
  <header>异常构件综合页</header>
  <nav>
    <div class="criteria">
      <label>垛号</label>
      <div class="select-inline select-area" type='stack'>
        <span class="select-value"></span>
        <select id="stack-selector"></select>
      </div>
    </div>
    <div class="criteria">
      <label>盘点状态</label>
      <div class="select-inline select-area" type='pandian'>
        <span class="select-value"></span>
        <select>
           <option param='-1'>未知</option>
           <option param='1'>已盘</option>
           <option param='2'>复盘</option>
        </select>
      </div>
    </div>
  </nav>
  <article></article>
  <div id="noData">未查到构件</div>
  <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
  <script type="text/javascript" src="./resource/base.js"></script>
  <script type="text/javascript">
  (function($,POP,undefined){
    var stackId, stackNo, serviceUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/pandianconfirm/controller/index.php', targetStackId = null, targetStatus = '-1'
    var module = {
      doms: {
        table:$('table'),
        stackNoSelector:$('#stack-selector'),
        article:$('article')
      },
      init: function(){
        stackNo = this.getUrlParameter('stackNo')
        stackId = this.getUrlParameter('stackId')
        this.retrieveStackNo()
      },
      retrieveStackData:function(){
        var _self = this
        SERVICE.sendSHR(serviceUrl,{action:'getStackNo'}, function(oData){
          if(oData.result && oData.result.length>0){
            _self.updateStackData(oData.result)
          }
        })
      },
      getUrlParameter: function(sParamName, sURL) {
        var sURL = decodeURIComponent(sURL || location.search.slice(1));
        var rexUrl = new RegExp("(^|&)" + sParamName + "=([^&]*)(&|$)", "i");
        var aRes = sURL.match(rexUrl);
        return(aRes && aRes[2]) || "";
      },
      initSelection: function(){
        var _self = this
        $(".select-area .select-value").each(function(){
          if( $(this).next("select").find("option:selected").length != 0 ){
            $(this).text( $(this).next("select").find("option:selected").text() );
          }
        });
        $(".select-area select").off('change').change(function(){
          var value = $(this).find("option:selected").text();
          $(this).parent(".select-area").find(".select-value").text(value);
          _self.triggerSelectionClick($(this).parent(".select-area").attr('type'),$(this).find("option:selected").attr('param'))
        });
      },
      triggerSelectionClick: function(type,param){
        var _self = this
        if(type==='stack'){
          targetStackId = param
        } else if (type === 'pandian'){
          targetStatus = param
        }

        SERVICE.sendSHR(serviceUrl,{
          action:'getErrorProduct',
          stackNo:targetStackId?targetStackId:'-1',
          status:targetStatus
        }, function(oData){
          if(oData.result && oData.result.length>0){
            _self.generateTableCell(oData.result)
          } else {
            _self.doms.article.hide()
            $('#noData').show()
          }
        })
      },
      retrieveStackNo: function(){
        var _self = this
        SERVICE.sendSHR(serviceUrl,{action:'getStackNo'}, function(oData){
          if(oData.result && oData.result.length>0){
            _self.generateStackNoSelector(oData.result)
          }
        })
      },
      generateStackNoSelector: function(stacklist){
        var _html = '<option param="">请选择</option>'
        for(var i=0;i<stacklist.length;i++){
          _html += '<option param="' + stacklist[i].StackNo +'">' + stacklist[i].StackNo + '</option>'
        }
        this.doms.stackNoSelector.empty().append(_html)
        this.initSelection()
      },
      retrievePandianName: function(status){
        var text = '未盘'
        if(!!status && status == 1){
          text = '已盘'
        } else if(!!status && status == 2){
          text = '复盘'
        }
        return text
      },
      generateTableCell: function(list){
        if(!list || list.length<1) return false
        var i, _el, _html='', _table='';
        for(i = 0; i < list.length; i++){
          _el = '<tr><td>' + (i+1) + '</td><td>' + list[i].StackNo + '</td><td>' + list[i].Forshort + '</td><td>' + list[i].CName + '</td><td>' + this.retrievePandianName(list[i].Status) + '</td><td>' +  list[i].inventory_num + '/' + list[i].tstockqty + '</td></tr>'
          _html += _el
        }

        _table = '<table class="table table-striped table-condensed"><tr><th>序号</th><th>垛号</th><th>项目名称</th><th>构件编号</th><th>盘点状态</th><th>实物数/<br/>账面数</th></tr>' +
          _html + '</table>'
        console.log(_table)
        $('#noData').hide()
        this.doms.article.empty().append(_table).show()
      }

    }
    module.init()
  })(jQuery,POP)
  </script>
</body>
</html>

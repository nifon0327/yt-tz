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
  <title>添加模具</title>
  <link rel="stylesheet" type="text/css" href="./resource/bootstrap-table.css">
  <link rel="stylesheet" type="text/css" href="./resource/base.css">
  <style type="text/css">
  .wrapper{padding:0 5% 50px;}
  header{height: 40px;font-size:14px;width: 100%;padding-top: 10px;}
  table{font-size:13px;}
  footer{display:block; position: fixed; bottom: 0; text-align: center; background-color:white; height: 40px;width:100%;}
  footer button{width:100%; height:100%; margin: auto; font-size:16px; background-color:teal; color:white;}
  #search{border-radius:3px; border: solid 1px lightgray; padding: 5px 10px; background-color:teal; color:white;}
  #noData{text-align: center; padding: 30px 0; color: darkred; border-top: solid 1px darkgray; border-bottom: solid 1px darkgray; margin-top: 30px;}
  </style>
</head>
<body>
  <div class="wrapper">
    <header>
      <span id="back">< 返回</span>
    </header>
    <nav>
      <div class="criteria">
        <label>项目</label>
        <div class="select-inline select-area" type='project'>
          <span class="select-value" id="project-select-value"></span>
          <select id="project"></select>
        </div>
      </div>
      <div class="criteria">
        <label>类别</label>
        <div class="select-inline select-area" type='mould'>
          <span class="select-value" id="mould-select-value"></span>
          <select id="mould"></select>
        </div>
      </div>
      <div class="criteria">
        <label>模具编号</label>
        <input type="text" name="search-input" class="search-input" id="code">
        <button id="search">查询</button>
      </div>
    </nav>
    <article></article>
    <div id="noData">未查到模具资料</div>
  </div>
  <footer>
    <button id="save">保存</button>
  </footer>
  <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
  <script type="text/javascript" src="./resource/bootstrap-table.js"></script>
  <script type="text/javascript" src="./resource/base.js"></script>
  <script type="text/javascript">
  (function($,POP,SERVICE,undefined){
    var trolleyId, selectedRow =[], serviceUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/bomconfirm/controller/index.php',
      tradeId = null, mould = null, searchResult = [];
    var module = {
      doms: {
        table:$('table'),
        projectSelector:$('#project'),
        mouldSelector:$('#mould'),
        article: $('article')
      },
      init: function(){
        trolleyId = this.getUrlParameter('trolleyId')
        if(!trolleyId) {
          POP.ShowAlert('台车号不存在，请确认后重试')
        } else {
          this.generateSelectorOptions('projectSelector','<option>请选择</option>')
          this.generateSelectorOptions('mouldSelector','<option>请选择</option>')
          this.initSelection()
          this.retrieveOptionData({action:'getCompanyForShort'},'generateProjectOptions')
          this.bind()
        }
      },
      setSelectedValue:function(target){
        var _el = $("#" + target + "-select-value")
        _el.text(_el.next("select").find("option:selected").text())
      },
      generateProjectOptions:function(list,target){
        var _html = '<option param="">请选择</option>'

        if(list){
          for(var i=0;i<list.length;i++){
            if(!!target && list[i].TradeId === target){
              _html += '<option selected="true" param="' + list[i].TradeId +'">' + list[i].Forshort + '</option>'
            } else {
              _html += '<option param="' + list[i].TradeId +'">' + list[i].Forshort + '</option>'
            }
          }
        }
        this.generateSelectorOptions('projectSelector',_html)

        if(target){
          this.setSelectedValue('project')
        } else {
          this.generateSelectorOptions('mouldSelector','<option>请选择</option>')
        }
      },
      generateMouldOptions:function(list,target){
        var _html = '<option param="">请选择</option>'
        if(list){
          for(var i=0;i<list.length;i++){
            if(!!target && list[i].MouldCat === target){
              _html += '<option selected="true" param="' + list[i].MouldCat +'">' + list[i].MouldCat + '</option>'
            } else {
              _html += '<option param="' + list[i].MouldCat +'">' + list[i].MouldCat + '</option>'
            }
          }
        }
        this.generateSelectorOptions('mouldSelector',_html)
      },
      search:function(){
        var _self = this
        SERVICE.sendSHR(serviceUrl,{
          action:'getBom',
          tradeId:tradeId?tradeId:0,
          mouldCat:mould?mould:'',
          mouldNo:$('#code').val()?$('#code').val():""
        }, function(oData){
          if(oData.result && oData.result.length>0){
            _self.updateList(oData.result)
          } else {
            _self.doms.article.hide()
            $('#noData').show()
          }
        })
      },
      getUrlParameter: function(sParamName, sURL) {
        var sURL = decodeURIComponent(sURL || location.search.slice(1));
        var rexUrl = new RegExp("(^|&)" + sParamName + "=([^&]*)(&|$)", "i");
        var aRes = sURL.match(rexUrl);
        return(aRes && aRes[2]) || "";
      },
      retrieveOptionData:function(oSend,cb){
        var _self = this
        SERVICE.sendSHR(serviceUrl,oSend, function(oData){
          if(oData.result && oData.result.length>0){
            _self[cb](oData.result)
          }
        })
      },
      triggerSelectionClick: function(type,param){
        if (type === 'project'){
          tradeId = param
          mould = null
          this.retrieveOptionData({action:'getModuleCat',tradeId:tradeId},'generateMouldOptions')
        } else if (type === 'mould'){
          mould = param
        }
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
      generateSelectorOptions: function(selector,options){
        this.doms[selector].empty().append(options)
      },
      updateList: function(list){
        var temp = []
        searchResult = list
        for(var i=0; i<list.length;i++){
          temp.push({
            indexId: i+1,
            BomId: list[i].BomId,
            project: list[i].Forshort,
            MouldCat: list[i].MouldCat,
            MouldNo: list[i].MouldNo,
            ProQty: list[i].ProQty,
            Completions: list[i].Completions,

          })
        }
        this.generateTableCell(temp)
        $('#noData').hide()
        this.doms.article.show()
      },
      generateTableCell: function(list){
        if(!list || list.length<1) return false

        this.doms.article.empty().append('<table id="table" class="table bootstrap-table table-striped">' +
          '<thead><tr><th data-field="state" data-checkbox="true"></th>' +
          '<th data-field="indexId">序号</th>' +
          '<th data-field="project">项目名称</th>' +
          '<th data-field="MouldCat">模具类别</th>' +
          '<th data-field="MouldNo">模具编号</th>' +
          '<th data-field="ProQty">制作数</th>' +
          '<th data-field="Completions">完成数</th>' +
          '</tr></thead></table>')

        $('#table').bootstrapTable({
          data: list
        });

        Array.prototype.remove = function(val) { 
          var index = this.indexOf(val); 
          if (index > -1) { 
          this.splice(index, 1); 
          }
        };

        $('#table').off('check.bs.table').on('check.bs.table', function (e, row, $el) {
          selectedRow.push($el.closest('tr').data('index'))
        });
        $('#table').off('uncheck.bs.table').on('uncheck.bs.table', function (e, row, $el) {
          selectedRow.remove($el.closest('tr').data('index'))
        });

        $($('input[name="btSelectAll"]')[0]).change(function(){
          if($('input[name="btSelectAll"]')[0].checked){
            selectedRow = []
            var i;
            for(i=0;i<list.length;i++){
              selectedRow.push(i)
            }
          } else {
            selectedRow = []
          }
        })
      },
      selectedRowCheck: function(){
        if(selectedRow.length<1){
          POP.ShowAlert('请先选择指定构件')
          return false
        } else {
          return true
        }
      },
      bind:function(){
        var _self = this

        $('#search').on('click',function(){
          _self.search()
        })

        $('#save').on('click',function(){
          if(_self.selectedRowCheck()){
            POP.ShowConfirm('请确认是否添加已选模具', '确定', '取消', function(){
              var oArray = []
              for(var i=0;i<selectedRow.length;i++){
                oArray.push({bomId:searchResult[selectedRow[i]].BomId})
              }
              console.log('oArray',oArray)

              SERVICE.sendSHR(serviceUrl,{action:'addMouldTrolley',trolleyId:trolleyId,mouldArray:JSON.stringify(oArray)}, function(oData){
                if(oData.result == true){
                  POP.ShowAlert('添加成功','确定',function(){
                    window.location.href = './bom.php?trolleyId=' + trolleyId
                  })
                } else {
                  POP.ShowAlert('添加失败')
                }
              })
            })
          }
        })

        $('#back').on('click',function(){
          window.location.href = "javascript:history.go(-1)"; 
        })
      }

    }
    module.init()
  })(jQuery,POP,SERVICE)
  </script>
</body>
</html>





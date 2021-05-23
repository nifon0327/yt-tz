<?php
/**
 * User: Elina
 * Date: 2019/01/29
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
  <title>新增构件</title>
  <link rel="stylesheet" type="text/css" href="./resource/bootstrap-table.css">
  <link rel="stylesheet" type="text/css" href="./resource/base.css">
  <style type="text/css">
  .wrapper{padding:0 5% 50px;}
  header{height: 40px;font-size:14px;width: 100%;padding-top: 10px;}
  table{font-size:13px;}
  footer{display:block; position: fixed; bottom: 0; text-align: center; background-color:white; height: 40px;width:100%;}
  footer button{width:100%; height:100%; margin: auto; font-size:16px; background-color:teal; color:white;}
  #search{border-radius:3px; border: solid 1px lightgray; padding: 5px 10px; background-color:teal; color:white;}
  #base{margin-bottom: 15px}
  #base span{display: inline-block; text-align: right; margin-right: 20px;}
  #base span:last-child{margin-right: 0;}
  #back{display: block; position: absolute;}
  #refresh{display: block; float:right;}
  #noData{text-align: center; padding: 30px 0; color: darkred; border-top: solid 1px darkgray; border-bottom: solid 1px darkgray; margin-top: 30px;}
  </style>
</head>
<body>
  <div class="wrapper">
    <header>
      <span id="back"> <返回 </span>
      <span id="refresh"> 刷新 </span>
    </header>
    <nav>
      <div class="criteria" id="base"></div>
      <div class="criteria">
        <label>项目</label>
        <div class="select-inline select-area" type='project'>
          <span class="select-value" id="project-select-value"></span>
          <select id="project"></select>
        </div>
      </div>
      <div class="criteria">
        <label>栋</label>
        <div class="select-inline select-area" type='building'>
          <span class="select-value" id="building-select-value"></span>
          <select id="building"></select>
        </div>
      </div>
      <div class="criteria">
        <label>层</label>
        <div class="select-inline select-area" type='floor'>
          <span class="select-value" id="floor-select-value"></span>
          <select id="floor"></select>
        </div>
      </div>
      <div class="criteria">
        <label>类型</label>
        <div class="select-inline select-area" type='type'>
          <span class="select-value" id="type-select-value"></span>
          <select id="type"></select>
        </div>
      </div>
      <div class="criteria">
        <label>构件编号</label>
        <input type="text" name="search-input" class="search-input" id="productCode">
        <button id="search">查询</button>
      </div>
    </nav>
    <article></article>
    <div id="noData">未查到构件</div>
  </div>
  <footer>
    <button id="save">保存</button>
  </footer>
  <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
  <script type="text/javascript" src="./resource/bootstrap-table.js"></script>
  <script type="text/javascript" src="./resource/base.js"></script>
  <script type="text/javascript">
  (function($,POP,SERVICE,undefined){
    var recordId, selectedRow =[], serviceUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/jianyanconfirm/controller/index.php',
      tradeId = null, buildingNo = null, floorNo = null, typeId = null, searchResult = [];
    var module = {
      doms: {
        table:$('table'),
        projectSelector:$('#project'),
        buildingSelector:$('#building'),
        floorSelector:$('#floor'),
        typeSelector:$('#type'),
        article: $('article')
      },
      init: function(){
        recordId = this.getUrlParameter('recordId')
        if(!recordId) {
          POP.ShowAlert('记录不存在，请确认后重试')
        } else {
          this.generateSelectorOptions('projectSelector','<option>请选择</option>')
          this.generateSelectorOptions('buildingSelector','<option>请选择</option>')
          this.generateSelectorOptions('floorSelector','<option>请选择</option>')
          this.generateSelectorOptions('typeSelector','<option>请选择</option>')
          this.initSelection()
          this.bind()
          this.retrieveOptionData({action:'productionAddItem'},'generateProjectOptions')
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
              _html += '<option selected="true" param="' + list[i].id +'">' + list[i].name + '</option>'
            } else {
              _html += '<option param="' + list[i].id +'">' + list[i].name + '</option>'
            }
          }
        }
        this.generateSelectorOptions('projectSelector',_html)

        if(target){
          this.setSelectedValue('project')
        } else {
          this.generateSelectorOptions('buildingSelector','<option>请选择</option>')
          this.generateSelectorOptions('floorSelector','<option>请选择</option>')
          this.generateSelectorOptions('typeSelector','<option>请选择</option>')
        }
      },
      generateBuildingOptions:function(list,target){
        var _html = '<option param="">请选择</option>'
        if(list){
          for(var i=0;i<list.length;i++){
            if(!!target && list[i].BuildingNo === target){
              _html += '<option selected="true" param="' + list[i].id +'">' + list[i].name + '</option>'
            } else {
              _html += '<option param="' + list[i].id +'">' + list[i].name + '</option>'
            }
          }
        }
        this.generateSelectorOptions('buildingSelector',_html)

        if(target){
          this.setSelectedValue('building')
        } else {
          this.generateSelectorOptions('floorSelector','<option>请选择</option>')
          this.generateSelectorOptions('typeSelector','<option>请选择</option>')
        }
      },
      generateFlourOptions:function(list,target){
        var _html = '<option param="">请选择</option>'
        if(list){
          for(var i=0;i<list.length;i++){
            if(!!target && list[i].FloorNo === target){
              _html += '<option selected="true" param="' + list[i].id +'">' + list[i].name + '</option>'
            } else {
              _html += '<option param="' + list[i].id +'">' + list[i].name + '</option>'
            }
          }
        }
        this.generateSelectorOptions('floorSelector',_html)

        if(target){
          this.setSelectedValue('floor')
        } else {
          this.generateSelectorOptions('typeSelector','<option>请选择</option>')
        }
      },
      generateTypeOptions:function(list,target){
        var _html = '<option param="">请选择</option>'
        if(list){
          for(var i=0;i<list.length;i++){
            if(!!target && list[i].TypeId === target){
              _html += '<option selected="true" param="' + list[i].id +'">' + list[i].name + '</option>'
            } else {
              _html += '<option param="' + list[i].id +'">' + list[i].name + '</option>'
            }
          }
        }
        this.generateSelectorOptions('typeSelector',_html)
        if(target){
          this.setSelectedValue('type')
        }
      },
      updateStackData: function(pandian,fupan){
        // $('#base').empty().append('<span>垛号：' + stackNo + '</span><span>盘点人：' + (!!pandian?pandian:'') + '</span><span>复盘人：' + (!!fupan?fupan:'') + '</span>')
        $('#base').empty().append('<span>垛号：' + stackNo + '</span><span>盘点人：' + '<?php echo $_SESSION["creator"]?>' + '</span><span>复盘人：' + '<?php echo $_SESSION["doubleCheckUser"]?>' + '</span>')
      },
      search:function(){
        var _self = this
        SERVICE.sendSHR(serviceUrl,{
          action:'productionAdd',
          id:recordId,
          itemId:tradeId,
          buildingId:buildingNo,
          floorId:floorNo,
          typeId:typeId,
          // productCode:$('#productCode').val()?$('#productCode').val():"",
        }, function(oData){
          if(oData.result && oData.result.length>0){
            _self.updateList(oData.result)
          } else {
            _self.doms.article.hide()
            $('#noData').show()
          }
        })
      },
      updateList: function(list){
        var temp = []
        searchResult = list
        for(var i=0; i<list.length;i++){
          temp.push({ProductId: list[i].ProductId,project: list[i].Forshort, name: list[i].cName})
        }
        this.generateTableCell(temp)
        $('#noData').hide()
        this.doms.article.show()
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
        console.log('triggerSelectionClick',type)
        if (type === 'project'){
          tradeId = param
          buildingNo = null
          floorNo = null
          typeId = null
          this.retrieveOptionData({action:'productionAddBuilding',itemId:tradeId},'generateBuildingOptions')
        } else if (type === 'building'){
          buildingNo = param
          floorNo = null
          typeId = null
          this.retrieveOptionData({action:'productionAddFloor',itemId:tradeId,buildingId:buildingNo},'generateFlourOptions')
        } else if (type === 'floor'){
          floorNo = param
          typeId = null
          this.retrieveOptionData({action:'productionAddType',itemId:tradeId,buildingId:buildingNo,floorId:floorNo},'generateTypeOptions')
        } else if (type === 'type'){
          typeId = param
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
      generateTableCell: function(list){
        if(!list || list.length<1) return false

        this.doms.article.empty().append('<table id="table" class="table bootstrap-table table-striped">' +
          '<thead><tr><th data-field="state" data-checkbox="true"></th>' +
          '<th data-field="indexId">序号</th>' +
          '<th data-field="project">项目</th>' +
          '<th data-field="name">构建编号</th></tr></thead></table>')

        for(var i=0;i<list.length;i++){
          list[i].indexId = i+1
        }

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
            POP.ShowConfirm('请确认是否保存已选构件', '确定', '取消', function(){
              var oArray = []
              for(var i=0;i<selectedRow.length;i++){
                oArray.push({productId:searchResult[selectedRow[i]].ProductId})
              }

              SERVICE.sendSHR(serviceUrl,{action:'insertInspectionRecord',inspectionRecordId:recordId,productId:JSON.stringify(oArray)}, function(oData){
                POP.ShowAlert('添加成功','确定',function(){
                  window.location.href = './index.php?recordId=' + recordId
                })
              })
            })
          }
        })

        $('#back').on('click',function(){
          window.location.href = "javascript:history.go(-1)";
        })

        $('#refresh').on('click', function() {
          window.location.reload();
        })
      }

    }
    module.init()
  })(jQuery,POP,SERVICE)
  </script>
</body>
</html>

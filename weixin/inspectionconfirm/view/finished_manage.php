<?php
/**
 * User: Elina
 * Date: 2019/02/14
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
  <title>成品检验记录表</title>
  <link rel="stylesheet" type="text/css" href="./resource/base.css">
  <link rel="stylesheet" type="text/css" href="./lib/jquery-ui-datepicker.min.css">
  <link rel="stylesheet" type="text/css" href="./lib/chosen.css">
  <link rel="stylesheet" type="text/css" href="./resource/bootstrap-table.css">
  <style type="text/css">
  .wrapper{padding: 20px;font-size: 14px;}
  .wrapper header{line-height: 40px}
  .wrapper article{width: 100%; overflow-y: scroll;}
  /*.wrapper article{display: none;}*/
  #search,#export{display:inline-block;margin-left: 10px; width:100px; background-color: teal; color: white; border: none; border-radius: 3px; font-size: 13px;line-height: 23px}
  #search{margin-left: 30px;}
  #start-date, #recordId{line-height: 23px; border-radius: 3px; border: solid 1px lightgray; padding-left: 10px; font-size: 13px;}
  #noData{text-align: center; padding: 30px 0; color: darkred; border-top: solid 1px darkgray; border-bottom: solid 1px darkgray; margin-top: 30px;}
  #export.disabled{background-color: #A9A9A9;}
  select,input,textarea,button {font: 99% sans-serif;}
  header select{width: 130px; margin-right: 20px;}
  header .select-ctn{display: inline-block; width: 150px;}
  header p{line-height: 25px;}
  #recordId{margin-left: 30px;}
  table{width: 90%; text-align: center; line-height: 2}
  table th{font-weight: 700}
  article img{width: 50px; display:inline-block;padding: 5px;}
  </style>
</head>
<body>
  <div class="wrapper">
    <header>
      <div class="select-ctn" id="project-select-ctn">
        <select data-placeholder="项目名称" class="chosen-select" id="project">
          <option>项目名称</option>
        </select>
      </div>
      <div class="select-ctn" id="building-select-ctn">
        <select data-placeholder="楼栋" class="chosen-select" id="building">
          <option>楼栋</option>
        </select>
      </div>
      <div class="select-ctn" id="floor-select-ctn">
        <select data-placeholder="楼层" class="chosen-select" id="floor">
          <option>楼层</option>
        </select>
      </div>
      <div class="select-ctn" id="type-select-ctn">
        <select data-placeholder="构件类型" class="chosen-select" id="type">
          <option>构件类型</option>
        </select>
      </div>
      <div class="select-ctn" id="line-select-ctn">
        <select data-placeholder="产线" class="chosen-select" id="line">
          <option>产线</option>
        </select>
      </div>
      <p>
        <input type="text" id="start-date" placeholder="日期筛选">
        <input type="text" name="" id="recordId" placeholder="记录编号">
        <button id="search">搜索</button>
        <!-- <button id="export" class="disabled" disabled="disabled">导出报表</button> -->
      </p>
    </header>
    <article></article>
    <div id="noData">暂无数据</div>
  </div>
  <script type="text/javascript" src="./lib/jquery-3.2.1.min.js"></script>
  <script type="text/javascript" src="./lib/jquery-ui-datepicker.min.js"></script>
  <script type="text/javascript" src="./lib/chosen.jquery.js"></script>
  <script type="text/javascript" src="./resource/base2.js"></script>
  <script type="text/javascript">
  (function($,POP,SERVICE,undefined){
    var serviceUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/inspectionconfirm/controller/index.php',
        picurl = '<?php $_SERVER['HTTP_HOST']?>/weixin/inspectionconfirm/view/',
        projectlist, tradeId,
        buildinglist, buildingNo,
        floorlist, floorNo,
        typelist, typeId,
        linelist, lineId,
        pageType = 1; // 1 - 成品
    var module = {
      doms: {
        btnSearch:$('#search'),
        btnExport:$('#export'),
        article:$('article'),
        noData:$('#noData'),
        selectProject:$('#project'),
        selectBuilding:$('#building')
      },
      init: function(){
        $( "#start-date" ).datepicker();
        this.initSelector()
        this.bind()
        this.retrieveOptionData({action:'getCompanyForShort'},'generateProjectOptions')
        this.retrieveLineOptions()
      },
      noDataShow:function(){
        this.doms.article.empty()
        this.doms.noData.show()
        this.doms.btnExport.attr('disabled','disabled').addClass('disabled')
      },
      initSelector: function(){
        var config = {
          '.chosen-select'           : {},
          '.chosen-select-deselect'  : {allow_single_deselect:true},
          '.chosen-select-no-single' : {disable_search_threshold:10},
          '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
          '.chosen-select-width'     : {width:"95%"}
        }
        for (var selector in config) {
          $(selector).chosen(config[selector]);
        }
      },
      retrieveOptionData:function(oSend,cb){
        var _self = this
        SERVICE.sendSHR(serviceUrl,oSend, function(oData){
          if(oData.result && oData.result.length>0){
            _self[cb](oData.result)
          }
        })
      },
      updateSelect(target,options){
        if(!target || !options) return false

        $('#' + target +'-select-ctn').empty().append('<select class="chosen-select" id="' + target + '"></select>')
        $('#' + target).append(options)
        $('#' + target).chosen({})
      },
      generateProjectOptions:function(list){
        projectlist = list

        var _self = this,
            _html = '<option>项目名称</option>'

        if(list){
          for(var i=0;i<list.length;i++){
            _html += '<option>' + list[i].Forshort + '</option>'
          }
        }
        this.updateSelect('project',_html)

        $('#project').on('change',function(e){
          if(e.target.selectedIndex === 0){
            tradeId = null
          } else {
            tradeId = projectlist[e.target.selectedIndex-1].TradeId
            _self.retrieveOptionData({action:'getCompanyBuilding',tradeId:tradeId},'generateBuildingOptions')
          }
          buildingNo = null
          floorNo = null
          typeId = null
          _self.generateBuildingOptions()
          _self.generateFlourOptions()
          _self.generateTypeOptions()
        })
      },
      generateBuildingOptions:function(list){
        buildinglist = list

        var _self = this,
            _html = '<option>楼栋</option>'

        if(list){
          for(var i=0;i<list.length;i++){
            _html += '<option>' + list[i].BuildingNo + '</option>'
          }
        }
        this.updateSelect('building',_html)

        $('#building').on('change',function(e){
          if(e.target.selectedIndex === 0){
            buildingNo = null
          } else {
            buildingNo = buildinglist[e.target.selectedIndex-1].BuildingNo
            _self.retrieveOptionData({action:'getBuildingFloor',tradeId:tradeId,buildingNo:buildingNo},'generateFlourOptions')
          }
          floorNo = null
          typeId = null
          _self.generateFlourOptions()
          _self.generateTypeOptions()
        })
      },
      generateFlourOptions:function(list){
        floorlist = list

        var _self = this,
            _html = '<option>楼层</option>'

        if(list){
          for(var i=0;i<list.length;i++){
            _html += '<option>' + list[i].FloorNo + '</option>'
          }
        }
        this.updateSelect('floor',_html)

        $('#floor').on('change',function(e){
          if(e.target.selectedIndex === 0){
            floorNo = null
          } else {
            floorNo = floorlist[e.target.selectedIndex-1].FloorNo
            _self.retrieveOptionData({action:'getCmptType',tradeId:tradeId,buildingNo:buildingNo,floorNo:floorNo},'generateTypeOptions')  
          }
          typeId = null
          _self.generateTypeOptions()
        })
      },
      generateTypeOptions:function(list){
        typelist = list

        var _self = this,
            _html = '<option>构件类型</option>'

        if(list){
          for(var i=0;i<list.length;i++){
            _html += '<option>' + list[i].CmptType + '</option>'
          }
        }
        this.updateSelect('type',_html)

        $('#type').on('change',function(e){
          if(e.target.selectedIndex === 0)
            typeId = null
          else
            typeId = typelist[e.target.selectedIndex-1].TypeId
        })
      },
      generateLineOptions:function(list){
        linelist = list

        var _self = this,
            _html = '<option>产线</option>'

        if(list){
          for(var i=0;i<list.length;i++){
            _html += '<option>' + list[i].Name + '</option>'
          }
        }
        this.updateSelect('line',_html)

        $('#line').on('change',function(e){
          lineId = linelist[e.target.selectedIndex-1].Id
        })
      },
      retrieveLineOptions:function(){
        var _self = this
        SERVICE.sendSHR(serviceUrl,{action:'getWorkShop'}, function(oData){
          if(oData && oData.result && oData.result.length>0){
            _self.generateLineOptions(oData.result)
          } else {
            POP.ShowAlert('出错了，未获取到产线信息')
          }
        })
      },
      retrieveStatusName: function(status){
        var text = ''
        if(!!status && status == 1){
          text = '未审核'
        } else if (!!status && status == 2){
          text = '合格'
        } else if (!!status && status == 3) {
          text = '不合格'
        }
        return text
      },
      generateImgHtml:function(imgliststr){
        if(!imgliststr) return ''
        var imglist = imgliststr.split(';'),
            imgHtml = ''
        for(var i=0;i<imglist.length;i++){
          if(imglist[i].replace(/^\s+|\s+$/g,"")){
            imgHtml += '<a href="' + picurl + imglist[i] + '" target="_blank"><img src="' + picurl + imglist[i] + '" /></a>'
          }
        }
        console.log(imgHtml)
        return imgHtml
      },
      renderPage: function(list){
        if(!list || list.length<1) return false
        var i, _el, _html='', _table=''
        for(i = 0; i < list.length; i++){
          var _imgHtml = ''
          if(list[i].ImageUrl)
            _imgHtml = this.generateImgHtml(list[i].ImageUrl)
          _el = '<tr><td>' + (i+1) + '</td><td>' + list[i].Forshort + '</td><td>' + list[i].cName + '</td><td>' + list[i].threadName + '</td><td>' + list[i].RecordNo + '</td><td>' +  this.retrieveStatusName(list[i].EState) + '</td><td>' + list[i].modified + '</td><td>' + (list[i].uName?list[i].uName:'') + '</td><td>' + _imgHtml + '</td></tr>'
          _html += _el
        }

        _table = '<table class="table table-striped table-bordered"><tr><th>序号</th><th>项目名称</th><th>构件名称</th><th>产线</th><th>记录编号</th><th>状态</th><th>时间</th><th>操作人</th><th>附件</th></tr>' +
          _html + '</table>'
        console.log(_table)
        $('#noData').hide()
        this.doms.article.empty().append(_table).show()
      },
      search:function(oSend){
        var _self = this
        //POP.ShowNotify("正在查询，请稍后...")
        SERVICE.sendSHR(serviceUrl, oSend, function(oData){
          POP.dialog.close()
          if(oData.result && oData.result.length>0){
            _self.renderPage(oData.result)
          } else {
            _self.doms.article.hide()
            $('#noData').show()
            POP.ShowAlert("未查询到相关内容")
          }
        })
      },
      bind: function(){
        var _self = this

        _self.doms.btnSearch.on('click', function(){
          var date = $( "#start-date" ).datepicker({dateFormat:'yy-mm-dd'}).val()
          var oSend = {
            action:'searchInspectionRecord',
            date:date,
            status:pageType,
            tradeId:tradeId?tradeId:0,
            buildingNo:buildingNo?buildingNo:0,
            floorNo:floorNo?floorNo:0,
            type:typeId?typeId:"",
            productCode:$('#recordId').val()?$('#recordId').val():"",
            workshopId:lineId?lineId:0
          }
          _self.search(oSend)
        })


      }

    }
    module.init()
  })(jQuery,POP,SERVICE)
  </script>
</body>
</html>


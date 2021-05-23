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
  <title>新增构件</title>
  <link rel="stylesheet" type="text/css" href="../static/css/bootstrap-table.css">
  <link rel="stylesheet" type="text/css" href="../static/css/base.css">
  <style type="text/css">
  .wrapper{padding:0 5% 50px;}
  .head { position:fixed; top: 0; right: 0; bottom: 0; left: 0; width: 100%; height: 130px; background:#fff; z-index:999;}
  .selectiongroup { display: block; margin-top: 40px; margin-left:30px;}
  header{height: 40px;font-size:14px;width: 100%;padding-top: 10px; position: fixed; top: 0;}
  table{font-size:13px;}
  article { position: absolute; top: 140px; height: 100%;}
  footer{display:block; position: fixed; bottom: 0; text-align: center; background-color:white; height: 40px;width:100%;margin-bottom:5px;}
  footer button{width:45%; height:100%; margin: 5px auto; font-size:16px; background-color:teal; color:white;}
  /* #search{border-radius:3px; border: solid 1px lightgray; padding: 5px 10px; background-color:teal; color:white;} */
  #base{margin-bottom: 15px}
  #base span{display: inline-block; text-align: right; margin-right: 20px;}
  #base span:last-child{margin-right: 0;}
  #back{display: block; position: absolute;padding-left: 8px;}
  #noData{position: absolute; top: 140px; text-align: center; padding: 30px 0; color: darkred; border-top: solid 1px darkgray; border-bottom: solid 1px darkgray; width:90%;}
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="head">
    <header>
      <span id="back"> <返回 </span>
    
    <nav class="selectiongroup">
      <div class="criteria" id="base"></div>
      <div class="criteria">
        <label style="width:40px">项目</label>
        <div class="select-inline " style="width:60%" type='project'>
          <select id="project" style="width: 100%" > </select>
        </div>
      </div>
      <div class="criteria">
        <label style="width:40px">产线</label>
        <div class="select-inline "  style="width:60%" type='workshop'>
          <select id="workshop" style="width: 100%"></select>
        </div>
      </div>
    </nav>
    </header>
</div>
    <article></article>
    <div id="noData">未查到构件</div>
  </div>
  <footer>
    <button id="search">搜索</button>
    <button id="save">添加</button>
  </footer>
  <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
  <script type="text/javascript" src="../static/js/bootstrap-table.js"></script>
  <script type="text/javascript" src="../static/js/base.js"></script>
  <script type="text/javascript">
  (function($,POP,SERVICE,undefined){
    var stackId, stackNo, seatId, serviceUrl='<?php $_SERVER['HTTP_HOST']?>',searchUrl='<?php $_SERVER['HTTP_HOST']?>',selectedRow=[];
    // var searchResult = []
    var module = {
      doms: {
        table:$('table'),
        projectSelector:$('#project'),
        workshopSelector:$('#workshop'),
        article: $('article')
      },
      init: function(){
         stackNo = this.getUrlParameter('stackNo')
         stackId = this.getUrlParameter('stackId')
         seatId = this.getUrlParameter('seatId')
       
         this.initServerUrl()
         this.generateSelectorOptions('projectSelector','<option>请选择</option>')
         this.generateSelectorOptions('workshopSelector','<option>请选择</option>')
          
         this.bind()
         $('#noData').hide()
        
      },
      initServerUrl: function() {
        var _self = this
        $.getJSON("../project.json", function(json) {
           serviceUrl =  serviceUrl + json.commonUrl;
           searchUrl  =  searchUrl + json.batchstoreUrl; 
           console.log(serviceUrl)

           _self.initSelection()
        })
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

        // if(target){
        //   this.setSelectedValue('project')
        // }
      },
      
      generateWorkshopOptions:function(list,target){
        var _html = '<option param="">请选择</option>'
        if(list){
          for(var i=0;i<list.length;i++){
            if(!!target && list[i].Id === target){
              _html += '<option selected="true" param="' + list[i].Id +'">' + list[i].Name + '</option>'
            } else {
              _html += '<option param="' + list[i].Id +'">' + list[i].Name + '</option>'
            }
          }
        }
        this.generateSelectorOptions('workshopSelector',_html)
      },
     
      search:function(){
        var _self = this
        POP.ShowNotify("正在查询，请稍后...")
        SERVICE.sendSHR(searchUrl,{
          action:'searchFinishedProducts',
          tradeId:tradeId?tradeId:0,
          workshopId: workshopId?workshopId:0
        }, function(oData){
          POP.dialog.close()
          if(oData.result && oData.result.length>0){
            console.log(oData.result)
            _self.updateList(oData.result)
          } else {
            _self.doms.article.hide()
            $('#noData').show()
            POP.ShowAlert("未查询到结果")
          }
        })
      },
      updateList: function(list){
        var temp = []
        searchResult = list
        for(var i=0; i<list.length;i++){
          temp.push({
            index:list[i].ProductId,
            ProductId: list[i].ProductId,
            project: list[i].Forshort, 
            name: list[i].cName,
            POrderId: list[i].POrderId,
            cjtjId: list[i].cjtjId
            })
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
          
        } else if (type === 'workshop'){
          workshopId = param
         
        }
      },
      initSelection: function(){
        var _self = this
        // $(".select-area .select-value").each(function(){
        //   if( $(this).next("select").find("option:selected").length != 0 ){
        //     $(this).text( $(this).next("select").find("option:selected").text() );
        //   }
        // });
        this.retrieveOptionData({action:'getCompanyForShort'},'generateProjectOptions')
        this.retrieveOptionData({action:'getWorkShop'},'generateWorkshopOptions')
      },
      generateSelectorOptions: function(selector,options){
        this.doms[selector].empty().append(options)
      },
      generateTableCell: function(list){
        // console.log(list)
        if(!list || list.length<1) return false

        this.doms.article.empty().append('<table id="table" class="table bootstrap-table table-striped">' +
          '<thead><tr><th data-field="state" data-checkbox="true"></th>' +
          '<th data-field="indexId">序号</th>' +
          '<th data-field="project">项目</th>' +
          '<th data-field="name">构件编号</th></tr></thead></table>')

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
                oArray.push({
                  productId:searchResult[selectedRow[i]].ProductId
                 })
              }
              console.log('oArray',oArray)

              SERVICE.sendSHR(searchUrl,{action:'addFinishedProducts',stackId:stackId,products:JSON.stringify(oArray)}, function(oData){
                if(oData.result == true){
                  POP.ShowAlert('添加成功','确定',function(){
                    window.location.href = './stack.php?stackId=' + stackId + '&stackNo=' + stackNo + '&seatId=' + seatId
                  })
                } else {
                  POP.ShowAlert("添加失败")
                }
              })
            })
          }
        })

        $('#back').on('click',function(){
          window.location.href = "javascript:history.go(-1)";
        })

        $(".select-inline select").off('change').change(function(){
          // var value = $(this).find("option:selected").text();
          // $(this).parent(".select-inline").find(".select-value").text(value);
          _self.triggerSelectionClick($(this).parent(".select-inline").attr('type'),$(this).find("option:selected").attr('param'))
        });
      }
    }
    module.init()
  })(jQuery,POP,SERVICE)
  </script>
</body>
</html>





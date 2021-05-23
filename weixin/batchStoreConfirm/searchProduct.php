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
  <title>查询构件</title>
  <link rel="stylesheet" type="text/css" href="../static/css/bootstrap-table.css">
  <link rel="stylesheet" type="text/css" href="../static/css/base.css">
  <style type="text/css">
  .wrapper{padding:0 5% 50px;}
  .head { position:fixed; top: 0; right: 0; bottom: 0; left: 0; width: 100%; height: 130px; background:#fff; z-index:999;}
  .selectiongroup { display: block; margin-top: 40px; margin-left:30px;}
  header{height: 40px;font-size:14px;width: 100%;padding-top: 10px; position: fixed; top: 0;}
  table{font-size:13px;}
  table tr td:nth-child(1){width: 5%;}
  /* table tr td:nth-child(2){width: 200px;} */
  article { position: absolute; top: 140px; height: 100%; margin-right: 8px;}
  footer{display:block; position: fixed; bottom: 0; text-align: center; background-color:white; height: 40px;width:100%;margin-bottom:10px;}
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
        <label style="width:40px">库位</label>
        <div class="select-inline " style="width:60%" type='warehouse'>
          <select id="warehouse" style="width: 100%" > </select>
        </div>
      </div>
      <div class="criteria">
        <label style="width:40px">垛号</label>
        <div class="select-inline "  style="width:60%" type='stack'>
          <select id="stack" style="width: 100%"></select>
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
  </footer>
  <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
  <script type="text/javascript" src="../static/js/bootstrap-table.js"></script>
  <script type="text/javascript" src="../static/js/base.js"></script>
  <script type="text/javascript">
  (function($,POP,SERVICE,undefined){
    var stackId, stackNo, seatId, serviceUrl='<?php $_SERVER['HTTP_HOST']?>',searchUrl='<?php $_SERVER['HTTP_HOST']?>',selectedRow=[];
    
    var module = {
      doms: {
        table:$('table'),
        stackSelector:$('#stack'),
        warehouseSelector:$('#warehouse'),
        article: $('article')
      },
      init: function(){
      
          this.initServerUrl()
          this.generateSelectorOptions('stackSelector','<option>请选择</option>')
          this.generateSelectorOptions('warehouseSelector','<option>请选择</option>')
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
     
      generateStackOptions:function(list,target){
        var _html = '<option param="">请选择</option>'

        if(list){
          for(var i=0;i<list.length;i++){
            if(!!target && list[i].StackId === target){
              _html += '<option selected="true" param="' + list[i].StackId +'">' + list[i].StackId + '</option>'
            } else {
              _html += '<option param="' + list[i].StackId +'">' + list[i].StackId + '</option>'
            }
          }
        }
        this.generateSelectorOptions('stackSelector',_html)

      },
      
      generateWarehouseOptions:function(list,target){
        var _html = '<option param="">请选择</option>'
        if(list){
          for(var i=0;i<list.length;i++){
            if(!!target && list[i].SeatId === target){
              _html += '<option selected="true" param="' + list[i].SeatId +'">' + list[i].SeatId + '</option>'
            } else {
              _html += '<option param="' + list[i].SeatId +'">' + list[i].SeatId + '</option>'
            }
          }
        }
        this.generateSelectorOptions('warehouseSelector',_html)

      
      },
     
      search:function(){
        var _self = this
        POP.ShowNotify("正在查询，请稍后...")
        SERVICE.sendSHR(searchUrl,{
          action:'getProductByStackId',
          stackId:stackId?stackId:0,
        }, function(oData){
          if(oData.result && oData.result.length>0){
            POP.dialog.close()
            console.log(oData.result)
            _self.updateList(oData.result)
          } else {
            POP.ShowAlert("未查询到结果")
            _self.doms.article.hide()
            $('#noData').show()
          }
        })
      },
      updateList: function(list){
        var temp = []
        searchResult = list
        for(var i=0; i<list.length;i++){
          temp.push({
            name: list[i].cName,
            forshort: list[i].Forshort,
            building: list[i].BuildingNo,
            floor: list[i].FloorNo
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
        SERVICE.sendSHR(searchUrl,oSend, function(oData){
          if(oData.result && oData.result.length>0){
            _self[cb](oData.result)
          }
        })
      },
      triggerSelectionClick: function(type,param){
        var _self = this
        console.log('triggerSelectionClick',type)
        if (type === 'warehouse'){
          seatId = param
          _self.retrieveOptionData({action:'getStackIdBySeat', seatId: seatId},'generateStackOptions')
        } else if (type === 'stack'){
          stackId = param
        }
      },
      initSelection: function(){
        this.retrieveOptionData({action:'getSeats'},'generateWarehouseOptions')
      },
      generateSelectorOptions: function(selector,options){
        this.doms[selector].empty().append(options)
      },
      generateTableCell: function(list){
        // console.log(list)
        if(!list || list.length<1) return false

        this.doms.article.empty().append('<table id="table" class="table bootstrap-table table-striped">' +
          '<thead><tr><th data-field="indexId">序号</th>' +
          '<th data-field="name">构件编号</th>' +
          '<th data-field="forshort">项目名</th>' +
          '<th data-field="building">楼栋</th>' +
          '<th data-field="floor">楼层</th>' +
          '</tr></thead></table>')

        for(var i=0;i<list.length;i++){
          list[i].indexId = i+1
        }

        $('#table').bootstrapTable({
          data: list
        });

        // Array.prototype.remove = function(val) {
        //   var index = this.indexOf(val);
        //   if (index > -1) {
        //   this.splice(index, 1);
        //   }
        // };

        // $('#table').off('check.bs.table').on('check.bs.table', function (e, row, $el) {
        //   selectedRow.push($el.closest('tr').data('index'))
        // });
        // $('#table').off('uncheck.bs.table').on('uncheck.bs.table', function (e, row, $el) {
        //   selectedRow.remove($el.closest('tr').data('index'))
        // });

        // $($('input[name="btSelectAll"]')[0]).change(function(){
        //   if($('input[name="btSelectAll"]')[0].checked){
        //     selectedRow = []
        //     var i;
        //     for(i=0;i<list.length;i++){
        //       selectedRow.push(i)
        //     }
        //   } else {
        //     selectedRow = []
        //   }
        // })
      },
      // selectedRowCheck: function(){
      //   if(selectedRow.length<1){
      //     POP.ShowAlert('请先选择指定构件')
      //     return false
      //   } else {
      //     return true
      //   }
      // },
      bind:function(){
        var _self = this
        $('#search').on('click',function(){
          _self.search()
        })

        // $('#save').on('click',function(){
        //   if(_self.selectedRowCheck()){
        //     POP.ShowConfirm('请确认是否保存已选构件', '确定', '取消', function(){
        //       var oArray = []
        //       for(var i=0;i<selectedRow.length;i++){
        //         oArray.push({
        //           ProductID:searchResult[selectedRow[i]].ProductId,
        //           POrderId:searchResult[selectedRow[i]].POrderId,
        //           cjtjId:searchResult[selectedRow[i]].cjtjId})
        //       }
        //       console.log('oArray',oArray)

        //       SERVICE.sendSHR(searchUrl,{action:'addFinishedProducts',stackId:stackId,products:JSON.stringify(oArray)}, function(oData){
        //         if(oData.result == true){
        //           POP.ShowAlert('添加成功','确定',function(){
        //             window.location.href = './stack.php?stackId=' + stackId + '&stackNo=' + stackNo + '&seatId=' + seatId
        //           })
        //         } else {
        //           POP.ShowAlert("添加失败")
        //         }
        //       })
        //     })
        //   }
        // })

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





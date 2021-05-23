<?php

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
  <title>库位选择</title>
  <link rel="stylesheet" type="text/css" href="../static/css/bootstrap-table.css">
  <link rel="stylesheet" type="text/css" href="../static/css/base.css">
  <style type="text/css">
  .wrapper{padding:0 5% 50px;}
  header{height: 40px;font-size:14px;width: 100%;padding-top: 10px;}
  table{font-size:13px;}
  #base{margin-bottom: 15px}
  #base span{display: inline-block; text-align: right; margin-right: 20px;}
  #base span:last-child{margin-right: 0;}
  #back{display: block; position: absolute;}
  #refresh{display: block; float:right;}
  .contents {display: flex; align-items: center; height: 500px; }
  .btn-cfm { width: 50%; margin:20px auto; text-align: center;}
  #confirm{border: solid 1px gray; display: inline-block; width: 70%;line-height: 45px; 
    border-radius: 3px; background-color: teal; color: white; letter-spacing: 10px; margin-top: 50px;font-size:16px;}
  .labels {text-shadow: 5px 0 4px rgba(0,0,0,.3);font-weight: 700;color: #5a5e66; font-size:20px;}
  </style>
</head>
<body>
  <div class="wrapper">
    <header>
      <span id="back"> <返回 </span>
    </header>
    
    <div class="contents">
      <!-- <div class="house-select">  -->
      <div class="criteria" style="width:100%">
        <label class="labels">库位：</label>
        <!-- <select id="seat">
            
         </select> -->
         <div class="select-inline " style="width:60%" type='seat'>
          <select id="seat" style="width: 100%" > </select>
        </div>
         <div class="btn-cfm">
           <button id="confirm">确定</button>
         </div>
      </div>
    </div>
 
  <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
  <script type="text/javascript" src="../static/js/bootstrap-table.js"></script>
  <script type="text/javascript" src="../static/js/base.js"></script>
  <script type="text/javascript">
  (function($,POP,SERVICE,undefined){
    var stackId, stackNo, selectedRow =[], serviceUrl = '', batchStoreUrl;

    var module = {
      doms: {
        seat : $('#seat'),
        confirm: $('#confirm')
      },
      init: function() {
        stackNo = this.getUrlParameter('stackNo')
        stackId = this.getUrlParameter('stackId')
        console.log(stackNo, stackId)
        this.initServerUrl()
        this.bind()
      },
      initServerUrl: function() {
        var _self = this
        $.getJSON("../project.json", function(json) {
           batchStoreUrl = '<?php $_SERVER['HTTP_HOST']?>' + json.batchstoreUrl;
           serviceUrl = '<?php $_SERVER['HTTP_HOST']?>' + json.commonUrl;
           _self.getSeatInfo()
        })
      },
      getUrlParameter: function(sParamName, sURL) {
        var sURL = decodeURIComponent(sURL || location.search.slice(1));
        var rexUrl = new RegExp("(^|&)" + sParamName + "=([^&]*)(&|$)", "i");
        var aRes = sURL.match(rexUrl);
        return(aRes && aRes[2]) || "";
      },
      getSeatInfo: function() {
        var _self = this

        SERVICE.sendSHR(batchStoreUrl,{action:'getSeats'}, function(oData){
          if(oData.result && oData.result.length>0)
            _self.generateSeatOptions(oData.result)
        })
      },
      generateSelectorOptions: function(selector,options){
        this.doms[selector].empty().append(options)
      },
      generateSeatOptions:function(list){
        var _html = '<option param="">请选择</option>'

        if(list){
          for(var i=0;i<list.length;i++){
              _html += '<option param="' + list[i].SeatId +'">' + list[i].SeatId + '</option>'
            }
        }
        this.generateSelectorOptions('seat', _html)
      },
      moveseat: function(seatid) {
        SERVICE.sendSHR(batchStoreUrl,{action:'moveSeat', stackId: stackId, seatId: seatid}, function(oData){
           if(oData.result == true) {
            POP.ShowAlert('移库成功','确定',function(){
                  window.location.href = './stack.php?stackId=' + stackId + '&stackNo=' + stackNo + '&seatId=' + seatid
            })
          } else {
            POP.ShowAlert('移库失败, ' + oData.msg)
          }
        })
      },
      bind: function() {
        var _self = this

        this.doms.confirm.on('click',function(){
          var num = _self.doms.seat.val()
          if(!num || num == "请选择"){
            POP.ShowAlert('请选择库号')
          } else {
            _self.moveseat(num)
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





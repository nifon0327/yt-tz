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
  <title>构件移剁</title>
  <link rel="stylesheet" type="text/css" href="../static/css/bootstrap-table.css">
  <link rel="stylesheet" type="text/css" href="../static/css/base.css">
  <style type="text/css">
  label{ display: block; font-size: 26px;padding:150px 10% 30px; text-shadow: 5px 0 7px rgba(0,0,0,.3);font-weight: 700;color: #5a5e66;}
  article{text-align: center;display: block;}
  #tstackid{border: solid 1px gray; display: inline-block; width: 80%;line-height: 45px; border-radius: 3px;padding:0 10px;font-size:14px;}
  #confirm{border: solid 1px gray; display: inline-block; width: 70%;line-height: 45px; 
    border-radius: 3px; background-color: teal; color: white; letter-spacing: 10px; margin-top: 50px;font-size:16px;}
  #back{display: block; position: absolute;padding-left: 8px;}
  #stackno {position: relative;padding: 40px 0px 0px 8px;}
  </style>
</head>
<body>
  <!-- <div class="wrapper">
    <header>
      <span id="back"> <返回 </span>
    </header>
    <div class="stackno"></div>
    <div class="content">
      <div class="stack-select"> 
        <span> 移动该剁至 </span>
        <input id="tstackid" type="text" name="tstackid" >
         <div class="btn-cfm">
           <button id="confirm">确定</button>
         </div>
      </div>
    </div> -->
    <span id="back"> <返回 </span>
    <div id="stackno"></div>
  <article>
    <label> 移动该剁至 </label>
    <input type="text" name="num" id="tstackid" placeholder="请输入垛号...">
    <button id='confirm'>确定</button>
  </article>
 
  <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
  <script type="text/javascript" src="../static/js/bootstrap-table.js"></script>
  <script type="text/javascript" src="../static/js/base.js"></script>
  <script type="text/javascript">
  (function($,POP,SERVICE,undefined){
    var stackId, stackNo, seatId, products, selectedRow =[], serviceUrl = '', batchstoreUrl='';
    var storage=window.localStorage;
    var regexpat = RegExp('^[A-Z]+[0-9]+$');
    var module = {
      doms: {
        tstackid: $('#tstackid'),
        confirm: $('#confirm'),
        origstack: $('#stackno'),
        back : $('#back')
      },
      init: function() {
        stackNo = this.getUrlParameter('stackNo')
        stackId = this.getUrlParameter('stackId')
        seatId = this.getUrlParameter('seatId')

        this.doms.origstack.html('原垛号:' + stackNo)
       
        products =   JSON.parse(storage.getItem('products'))
        storage.removeItem('products')

        console.log(products)
        this.initServerUrl()
        this.bind()
        
      },
      initServerUrl: function() {
        var _self = this
        $.getJSON("../project.json", function(json) {
          batchStoreUrl = '<?php $_SERVER['HTTP_HOST']?>' + json.batchstoreUrl;
           serviceUrl = '<?php $_SERVER['HTTP_HOST']?>' + json.commonUrl;
           console.log(serviceUrl)
         
        })
      },
      getUrlParameter: function(sParamName, sURL) {
        var sURL = decodeURIComponent(sURL || location.search.slice(1));
        var rexUrl = new RegExp("(^|&)" + sParamName + "=([^&]*)(&|$)", "i");
        var aRes = sURL.match(rexUrl);
        return(aRes && aRes[2]) || "";
      },
      bind: function() {
        var _self = this

        _self.doms.confirm.on('click', function() {
          var targetstack = _self.doms.tstackid.val()
          if(regexpat.test(targetstack) == false) {
            POP.ShowAlert('请正确填写垛号')
            return
          } else {
            SERVICE.sendSHR(batchStoreUrl,{action:'moveStack', originStackId: stackId, stackNo: targetstack, products:JSON.stringify(products)}, function(oData){
               if(oData.result == true) {
                POP.ShowAlert('移垛成功','确定',function(){
                  window.location.href = './stack.php?stackId=' + stackId + '&stackNo=' + stackNo + '&seatId=' + seatId
                })
               } else {
                 POP.ShowAlert('移垛失败, ' + oData.msg)
               }
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





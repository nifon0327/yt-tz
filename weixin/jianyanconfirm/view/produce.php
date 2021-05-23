<?php
/**
 * User: Elina
 * Date: 2019/01/24
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
  <title>生产过程检验</title>
  <link rel="stylesheet" type="text/css" href="./resource/bootstrap-table.css">
  <link rel="stylesheet" type="text/css" href="./resource/base.css">
  <style type="text/css">
  .wrapper{padding:0 5% 150px;}
  .wrapper-pop{padding-top: 40px;}
  header{line-height: 80px;text-align: right; font-size:14px}
  footer{display:block; position: fixed; bottom: 0; text-align: center; background-color:white; height: 90px; border-top: solid 1px gray; width:100%; padding-top: 10px;}
  footer button{width:25%; margin: 5px auto; border: solid 1px lightgray; border-radius:3px; padding-top: 3px; padding-bottom: 3px; font-size:16px; background-color:teal; color:white;}
  footer button.disabled{background-color: #A9A9A9;}
  #back{display: block;position:absolute;}
  .fixed-table-container tbody .selected td { background-color: darkseagreen;}
  #noData{text-align: center; padding: 30px 0; color: darkred; border-top: solid 1px darkgray; border-bottom: solid 1px darkgray; margin-top: 30px;}
  </style>
</head>
<body>
  <div class="wrapper">
    <header>
      <span id="back">< 返回</span>
      <span>操作人：<span id="openid"></span></span>
    </header>
    <nav>
      <div class="criteria"><label>记录编号</label><span id="record"></span></div>
      <div class="criteria"><label>产品名称</label><span id="name"></span></div>
      <div class="criteria"><label>产线</label><span id="line"></span></div>
      <div class="criteria"><label>操作日期</label><span id="today"></span></div>
    </nav>
    <article></article>
    <div id="noData">正在查询</div>
  </div>
  <footer>
    <button id="scan" class="disabled" disabled="disabled">扫码添加</button>
    <button id="add" class="disabled" disabled="disabled">添加</button>
    <button id="delete" class="disabled" disabled="disabled">删除</button>
    <button id="upload" class="disabled" disabled="disabled">上传照片</button>
    <button id="qualified" class="disabled" disabled="disabled">质检合格</button>
  </footer>
  <script src="./resource/jquery.min.js"></script>
  <!-- <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script> -->
  <script type="text/javascript" src="./resource/bootstrap-table.js"></script>
  <script type="text/javascript" src="./resource/base.js"></script>
  <script type="text/javascript">
  (function($,POP,SERVICE,undefined){
    var recordId,
        openid='<?php echo $_SESSION["openid"];?>', 
        serviceUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/jianyanconfirm/controller/index.php'
    var module = {
      doms: {
        btnAdd:$('#add'),
        btnScan:$('#scan'),
        btnDelete:$('#delete'),
        btnUpload:$('#upload'),
        btnQualified:$('#qualified'),
        noData:$('#noData'),
      },
      init: function(){
        recordId = this.getUrlParameter('id')
        if(!recordId) {
          POP.ShowAlert('记录不存在，请确认后重试')
        } else {
          // this.initWechat()
          this.bind()
          this.setupBtns()
          this.retrievePageData()
        }
      },
      setupBtns:function(){
        this.doms.btnAdd.removeClass('disabled').removeAttr('disabled')
        this.doms.btnScan.removeClass('disabled').removeAttr('disabled')
        this.doms.btnDelete.removeClass('disabled').removeAttr('disabled')
        this.doms.btnUpload.removeClass('disabled').removeAttr('disabled')
        this.doms.btnQualified.removeClass('disabled').removeAttr('disabled')
      },
      renderPage: function(result){
        var today = new Date()
        $('#record').html(result.recordNumber)
        $('#name').html(result.item)
        $('#line').html(result.productionLine)
        $('#today').html(today.getFullYear() + '/' + (today.getMonth() + 1) + '/' + today.getDate())
        if(!result.list){
          this.doms.noData.show().html('暂无数据')
        }
      },
      retrievePageData:function(){
        var _self = this
        SERVICE.sendSHR(serviceUrl,{action:'productionTest',id:recordId}, function(oData){
          console.log(oData)
          _self.renderPage(oData.result)
        })
      },
      getUserName:function(){
        SERVICE.sendSHR(serviceUrl,{action:'username',openid:openid}, function(oData){
          console.log(oData)
        })
      },
      getUrlParameter: function(sParamName, sURL) {
        var sURL = decodeURIComponent(sURL || location.search.slice(1));
        var rexUrl = new RegExp("(^|&)" + sParamName + "=([^&]*)(&|$)", "i");
        var aRes = sURL.match(rexUrl);
        return(aRes && aRes[2]) || "";
      },
      qrCodeScan: function(){
        var _self = this
        wx.scanQRCode({
          needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
          scanType: ["qrCode"], // 可以指定扫二维码还是一维码，默认二者都有
          success: function (res) {
            var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
            _self.addProductByName(result)
          }
        });
      },
      initWechat:function(){
        var _self = this
        wx.config({
            debug: false,
            appId: '<?php echo $sign["appId"];?>',
            timestamp: '<?php echo $sign["timestamp"];?>',
            nonceStr: '<?php echo $sign["nonceStr"];?>',
            signature: '<?php echo $sign["signature"];?>',
            jsApiList: ['scanQRCode']
        });

        wx.ready(function(){
          _self.doms.btnScan.on('click',function(){
            _self.qrCodeScan()
          })
        });
      },
      bind:function(){
        var _self = this

        this.doms.btnAdd.on('click',function(){
          window.location.href = './add.php?recordId=' + recordId
        })

        $('#back').on('click',function(){
          window.location.href = "./entry.php"; 
        })
      }

    }
    module.init()
  })(jQuery,POP,SERVICE)
  </script>
</body>
</html>

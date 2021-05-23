<?php
/**
 * User: Elina
 * Date: 2019/01/24
 */
session_start();
error_reporting(E_ALL & ~E_NOTICE);

include_once('../../auth.php');
include_once('../../configure.php');
include_once('../../log.php');
include_once('../../jsapi.php');
include '../config/dbconnect.php';

spl_autoload_register(function ($class_name) {
    require_once $class_name . '.php';
});

$auth = new auth();
$query = "select u.Number, s.name from usertable u 
  left join wx_token w on w.openid = u.openid 
  left join staffmain s on u.number = s.number where w.openid='$_SESSION[openid]'";
$cursor = mysql_query($query);

if(true){
  $user_id = $row[0];
  $user_name = $row[1];
  $_SESSION['user_id'] = $user_id;
  $_SESSION['user_name'] = $user_name;
  $js_sdk = new jsapi();
  $sign = $js_sdk->get_sign();
  $hour = date('H');
  $grace = $hour<6?'凌晨':($hour<11?'早上':($hour<13?'中午':($hour<17?'下午':'晚上')));
}else{
  //usertable 的openid 绑定系统及微信用户，如没绑定，则转到信息页
  $result = 0;
  $title = '权限缺失';
  $msg = '您无此功能权限，如有疑问，请联系信息部人员</br>电话：15919701518';
  header("Location:msg.php?result=$result&title=$title&msg=$msg&onscan=1");
}

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
  #imgCtn{text-align: center; display: none;}
  #imgTitle{text-align: left; margin-top: 20px; font-weight: 700;font-size: 13px}
  #imgCtn img{width: 25%; display: inline-block;padding: 5px}
  </style>
</head>
<body>
  <div class="wrapper">
    <header>
      <span id="back">< 返回</span>
      <span>创建人：<span id="creator"></span></span>
    </header>
    <nav>
      <div class="criteria"><label>记录编号</label><span id="record"></span></div>
      <div class="criteria"><label>产品名称</label><span id="name"></span></div>
      <div class="criteria"><label>产线</label><span id="line"></span></div>
      <div class="criteria"><label>操作日期</label><span id="today"></span></div>
    </nav>
    <article></article>
    <div id="noData">正在查询</div>
    <div id="imgCtn">
      <div id="imgTitle"></div>
      <div id="imgList"></div>
    </div>
  </div>
  <footer>
    <button id="scan" class="disabled" disabled="disabled">扫码添加</button>
    <button id="add" class="disabled" disabled="disabled">添加</button>
    <button id="delete" class="disabled" disabled="disabled">删除</button>
    <input type="file" name="images[]" accept="image/*" id="upload" style="display:none" multiple />
    <button id="uploadBtn" class="disabled" disabled="disabled">上传照片</button>
    <button id="qualified" class="disabled" disabled="disabled">质检合格</button>
  </footer>
  <script src="./resource/jquery.min.js"></script>
  <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
  <script type="text/javascript" src="./resource/bootstrap-table.js"></script>
  <script type="text/javascript" src="./resource/base2.js"></script>
  <script type="text/javascript">
  (function($,POP,SERVICE,undefined){
    var recordId, lineId,
        pageType = 0, // 0 - 生产过程
        list, selectedRow=[],
        openid='<?php echo $_SESSION["openid"];?>',
        uploadfileUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/inspectionconfirm/controller/upload.php',
        picurl = '<?php $_SERVER['HTTP_HOgetProductByInspectionRecordST']?>/weixin/inspectionconfirm/view/';
        serviceUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/inspectionconfirm/controller/index.php';
    var module = {
      doms: {
        btnAdd:$('#add'),
        btnScan:$('#scan'),
        btnDelete:$('#delete'),
        btnUpload:$('#uploadBtn'),
        upload: $('#upload'),
        btnQualified:$('#qualified'),
        noData:$('#noData'),
        article:$('article'),
        imgCtn:$('#imgCtn'),
        imgTitle:$('#imgTitle'),
        imgList:$('#imgList')
      },
      init: function(){
        recordId = this.getUrlParameter('recordId')
        if(!recordId) {
          POP.ShowAlert('记录不存在，请确认后重试')
        } else {
          this.initWechat()
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
      retrieveStatusName: function(status){
          var text = ''
          if(status === null || status === 'null'){
              text = '未知'
          } else if (parseInt(status) === 2){
              text = '待质检'
          } else if (parseInt(status) >= 0) {
              text = '合格'
          }
          return text
      },
      renderlist:function(data){
        var temp = [], i
        for(i=0;i<data.length;i++){
          temp.push({
            showNo:i+1,
            project:data[i].Forshort,
            name:data[i].cName,
            productId:data[i].ProductId,
            estatus:this.retrieveStatusName(data[i].Estate),
            uname:data[i].uName,
            InspectionProductId:data[i].InspectionProductId,
            cjtjId:data[i].CjtjId
          })
        }
        list = temp;
        this.generateTableCell()
        this.doms.noData.hide()
        this.doms.article.show()
      },
      renderPage: function(result){
        var today = new Date()
        $('#record').html(result[0].RecordNo)
        $('#name').html(result[0].RecordName)
        $('#line').html(result[0].WorkShopName)
        $('#today').html(result[0].Created)
        $('#creator').html(result[0].Creator)
        lineId = result[0].WorkShopId
        if(!result[1]){
          this.doms.article.empty().hide()
          this.doms.noData.show().html('暂无数据')
        } else {
          this.renderlist(result[1])
        }
        if(result[0].ImageUrl){
          this.renderImg(result[0].ImageUrl)
        }
      },
      retrievePageData:function(){
        var _self = this
        SERVICE.sendSHR(serviceUrl,{
          action:'getProductByInspectionRecord',
          inspectionRecordId:recordId,
          status:pageType
        }, function(oData){
          _self.renderPage(oData.result)
        })
      },
      getUrlParameter: function(sParamName, sURL) {
        var sURL = decodeURIComponent(sURL || location.search.slice(1));
        var rexUrl = new RegExp("(^|&)" + sParamName + "=([^&]*)(&|$)", "i");
        var aRes = sURL.match(rexUrl);
        return(aRes && aRes[2]) || "";
      },
      generateTableCell: function(){
        if(!list || list.length<1) return false

        this.doms.article.empty().append('<table id="table" class="table bootstrap-table table-striped">' +
          '<thead><tr><th data-field="state" data-checkbox="true"></th>' +
          '<th data-field="showNo">序号</th>' +
          '<th data-field="project">项目</th>' +
          '<th data-field="name">构件编号</th>' +
          '<th data-field="estatus">状态</th>' +
          '<th data-field="uname">操作人</th>' +
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

        $($('input[name="btSelectAll"]')[0]).off('change').change(function(){
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

        $('table td').each(function(){
          var _el = $(this)
          if(_el.text() === '异常')
            _el.parent().addClass('abnormal')
        })
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
      selectedRowCheck: function(){
        if(selectedRow.length<1){
          POP.ShowAlert('请先选择指定构件')
          return false
        } else {
          return true
        }
      },
      selectedRowStatusCheck:function(){
        var pass = true
        for(var i=0;i<selectedRow.length;i++){
          // oArray.push({inspectionProductId:list[selectedRow[i]].InspectionProductId})
          if(list[selectedRow[i]].estatus === '合格')
            pass = false
        }
        if(!pass){
          POP.ShowAlert('无法删除质检合格构件')
        }
        return pass
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
      addProductByName: function(productname){
        var _self = this
        SERVICE.sendSHR(serviceUrl,{
          action:'insertInspectionProductByQrCodeProducting',
          inspectionRecordId:recordId,
          workShopId:lineId,
          productName:productname
        }, function(oData){
          POP.ShowAlert('添加成功','确定',function(){
            _self.retrievePageData()
          })
        })
      },
      renderImg: function(imgliststr){
        if(!imgliststr) return false
        var imglist = imgliststr.split(';'),
            imgHtml = '',
            imgLength = 0
        for(var i=0;i<imglist.length;i++){
          if(imglist[i].replace(/^\s+|\s+$/g,"")){
            imgHtml += '<a href="' + picurl + imglist[i] + '" target="_blank"><img src="' + picurl + imglist[i] + '" /></a>'
            imgLength += 1
          }
        }
        this.doms.imgTitle.empty().append('附件图片，共' + imgLength + "张")
        this.doms.imgList.empty().append(imgHtml)
        this.doms.imgCtn.show()
      },
      uploadImg:function(imgliststr){
        var _self = this
        if(!imgliststr) return false
        SERVICE.sendSHR(serviceUrl,{action:'insertImageUrl',inspectionRecordId:recordId,imageUrl:imgliststr.replace(/^\s+|\s+$/g,"")}, function(oData){
          POP.ShowAlert('图片添加成功','确定',function(){
            _self.retrievePageData()
          })
        })
      },
      bind:function(){
        var _self = this

        this.doms.btnAdd.on('click',function(){
          window.location.href = './produce_add.php?recordId=' + recordId + '&lineId=' + lineId
        })

        $('#back').on('click',function(){
          window.location.href = './produce_entry.php'
        })

        // this.doms.btnScan.on('click',function(){
        //   _self.addProductByName('15-13-WGQ7-84')
        // })

        this.doms.btnDelete.on('click',function(){
          if(_self.selectedRowCheck() && _self.selectedRowStatusCheck()){
            POP.ShowConfirm('请确认是否删除已选构件', '确定', '取消', function(){
              var oArray = []
              for(var i=0;i<selectedRow.length;i++){
                oArray.push({inspectionProductId:list[selectedRow[i]].InspectionProductId})
              }

              SERVICE.sendSHR(serviceUrl,{
                action:'deleteInspectionProduct',
                status:pageType,
                inspectionProductId:JSON.stringify(oArray)}, function(oData){
                POP.ShowAlert('删除成功')
                selectedRow = []
                _self.retrievePageData()
              })
            })
          }
        })

        this.doms.btnUpload.on('click',function(){
          _self.doms.upload.click()
          _self.doms.upload.unbind().change(function() {
            var uimages = document.getElementById('upload').files;
            if(uimages.length == 0){
              return;
            }
            var formdata = new FormData();
            for(var i=0; i<uimages.length; i++){
              formdata.append('images[]', uimages[i]);
            }

            SERVICE.uploadfile(uploadfileUrl, formdata, function(data) {
              if(data && (data.status == 0)) {
                POP.ShowAlert("图片上传成功")
                _self.uploadImg(data.result)
              }else{
                POP.ShowAlert("图片上传失败 ")
              }
            })
          })
        })

        this.doms.btnQualified.on('click',function(){
          if(_self.selectedRowCheck()){
            POP.ShowConfirm('请确认是否质检合格已选构件', '确定', '取消', function(){
              var oArray = []
              for(var i=0;i<selectedRow.length;i++){
                oArray.push({cjtjId:list[selectedRow[i]].cjtjId})
              }

              SERVICE.sendSHR(serviceUrl,{
                action:'inspectProduct',
                cjtjId:JSON.stringify(oArray),
                status:pageType
              }, function(oData){
                POP.ShowAlert('质检成功')
                selectedRow = []
                _self.retrievePageData()
              })
            })
          }
        })
      }

    }
    module.init()
  })(jQuery,POP,SERVICE)
  </script>
</body>
</html>

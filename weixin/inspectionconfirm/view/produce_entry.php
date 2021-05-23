<?php
/**
 * User: Elina
 * Date: 2019/01/23
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
  <link rel="stylesheet" type="text/css" href="./resource/base.css">
  <link rel="stylesheet" type="text/css" href="./resource/mobileSelect.css">
  <style type="text/css">
  header{font-size: 20px;padding:30px 10% 70px; text-shadow: 5px 0 7px rgba(0,0,0,.3);font-weight: 700;color: #5a5e66;}
  article{text-align: center;display: block; font-size: 16px}
  article p{width: 80%; text-align: left; margin: 10px auto;}
  article p label{width: 80px;vertical-align: middle; display: inline-block;}
  article p input, #line, #name{font-size: 15px; padding:3px 10px; border: solid 1px lightgray; vertical-align: middle;border-radius: 3px;}
  #line, #name{display: none;}
  #search{border: solid 1px gray; display: inline-block; width: 70%;line-height: 45px; 
    border-radius: 3px; background-color: teal; color: white; letter-spacing: 10px; margin-top: 50px;font-size:16px;}
  </style>
</head>
<body>
  <header>生产过程检验</header>
  <article>
    <p>
      <label>记录编号：</label>
      <input type="text" name="record" id="record" placeholder="请输入...">
    </p>
    <p>
      <label>产品名称：</label>
      <span id="name">请选择</span>
    </p>
    <p>
      <label>产线：</label>
      <span id="line">请选择</span>
    </p>
    
    <button id='search'>确定</button>
  </article>
  <script src="./resource/jquery.min.js"></script>
  <script type="text/javascript" src="./resource/base.js"></script>
  <script src="./resource/mobileSelect.js"></script>
  <script type="text/javascript">
  (function($,POP,SERVICE,undefined){
    var serviceUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/inspectionconfirm/controller/index.php',
      pageType = 0, // 0 - 生产过程
      lineSelector,lineSelected,
      nameSelector,nameSelected;
    var module = {
      doms: {
        lineSelectorCtn:$('#line'),
        productName:$('#name'),
        btn:$('#search')
      },
      init: function(){
        var today = new Date(),
          defaultDate = '' + today.getFullYear() + this.numberformat(today.getMonth() + 1) + this.numberformat(today.getDate())
        $('#record').val(defaultDate)

        this.bind()
        this.retrieveLineOptions()
      },
      numberformat: function(num){
        if(!num) return '00'
        else if(num<10) return '0' + num
        else return num
      },
      initLineSelector: function(list){
        console.log(list)
        var _self = this
        lineSelector = new MobileSelect({
          trigger: '#line',
          title: '产线',
          wheels: [{data:list}],
          keyMap: {
            id:'Id',
            value: 'Name',
          },
          callback:function(indexArr, data){
            lineSelected = data[0].Id
          }
        });
        this.doms.lineSelectorCtn.css('display','inline-block')
      },
      initProductName: function(list) {
        console.log(list)
        var _self = this
        nameSelector = new MobileSelect({
          trigger: '#name',
          title: '产品名称',
          wheels: [{data:list}],
          keyMap: {
            id:'NameRule',
            value: 'NameRule',
          },
          callback:function(indexArr, data){
            nameSelected = data[0].NameRule
          }
        });
        this.doms.productName.css('display','inline-block')
      },
      retrieveLineOptions:function(){
        var _self = this
        SERVICE.sendSHR(serviceUrl,{action:'getWorkShop'}, function(oData){
          if(oData && oData.result && oData.result.length>0){
            console.log('oData.result',oData.result)
            _self.initLineSelector(oData.result)
          } else {
            POP.ShowAlert('出错了，未获取到产线信息')
          }
        })

        SERVICE.sendSHR(serviceUrl,{action:'getNameRule'}, function(oData){
          if(oData && oData.result && oData.result.length>0){
            console.log('oData.result',oData.result)
            _self.initProductName(oData.result)
          } else {
            POP.ShowAlert('出错了，未获取到产品信息')
          }
        })

      },
      checkRecord: function(recordNumber){
        SERVICE.sendSHR(serviceUrl,{
          action:'createInspectionRecord',
          recordNo:recordNumber,
          recordName:nameSelected?nameSelected:'',
          status:pageType,
          workShopId:lineSelected?lineSelected:''}, function(oData){
          if(oData.result){
            if(oData.result[1].exist === 1){
              POP.ShowConfirm('记录编号已存在', '重新输入', '查看', null, function(){
                window.location.href = './produce.php?recordId=' + oData.result[0].Id
              })
            }else{
              POP.ShowAlert('添加成功', '确定', function(){
                window.location.href = './produce.php?recordId=' + oData.result[0].Id
              })
            }
          }
        })
      },
      bind: function(list){
        var _self = this
        this.doms.btn.on('click',function(){
          var record = $('#record').val()
          if(!record){
            POP.ShowAlert('请输入记录编号')
          } else {
            _self.checkRecord(record)
          }
        })
      }

    }
    module.init()
  })(jQuery,POP,SERVICE)
  </script>
</body>
</html>

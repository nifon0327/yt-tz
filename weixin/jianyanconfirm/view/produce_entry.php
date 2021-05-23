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
  article p input, #line{font-size: 15px; padding:3px 10px; border: solid 1px lightgray; vertical-align: middle;border-radius: 3px;}
  #line{display: none;}
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
      <input type="text" name="name" id="name" placeholder="请输入...">
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
    var serviceUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/jianyanconfirm/controller/index.php',
      lineSelector,lineSelected;
    var module = {
      doms: {
        lineSelectorCtn:$('#line'),
        btn:$('#search')
      },
      init: function(){
        this.bind()
        this.retrieveLineOptions()
      },
      initLineSelector: function(list){
        var _self = this
        lineSelector = new MobileSelect({
          trigger: '#line',
          title: '产线',
          wheels: [{data:list}],
          keyMap: {
            id:'id',
            value: 'name',
          },
          callback:function(indexArr, data){
            lineSelected = data[0].id
          }
        });
        this.doms.lineSelectorCtn.css('display','inline-block')
      },
      retrieveLineOptions:function(){
        var _self = this
        SERVICE.sendSHR(serviceUrl,{action:'productionLine'}, function(oData){
          if(oData && oData.result && oData.result.length>0){
            _self.initLineSelector(oData.result)
          } else {
            POP.ShowAlert('出错了，未获取到产线信息')
          }
        })
      },
      checkRecord: function(recordNumber,item){
        SERVICE.sendSHR(serviceUrl,{action:'confirm',recordNumber:recordNumber,item:item,productionLine:lineSelected}, function(oData){
          console.log(oData)
        })
      },
      bind: function(list){
        var _self = this
        this.doms.btn.on('click',function(){
          var record = $('#record').val(),
              name = $('#name').val()
          if(!record){
            POP.ShowAlert('请输入记录编号')
          } else {
            _self.checkRecord(record,name)
          }
        })
      }

    }
    module.init()
  })(jQuery,POP,SERVICE)
  </script>
</body>
</html>

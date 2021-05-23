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
  <title>输入台车号</title>
  <link rel="stylesheet" type="text/css" href="./resource/base.css">
  <style type="text/css">
  header{font-size: 20px;padding:30px 10% 70px; text-shadow: 5px 0 7px rgba(0,0,0,.3);font-weight: 700;color: #5a5e66;}
  article{text-align: center;display: block;}
  #num{border: solid 1px gray; display: inline-block; width: 80%;line-height: 45px; border-radius: 3px;padding:0 10px;font-size:14px;}
  #search{border: solid 1px gray; display: inline-block; width: 70%;line-height: 45px; 
    border-radius: 3px; background-color: teal; color: white; letter-spacing: 10px; margin-top: 50px;font-size:16px;}
  </style>
</head>
<body>
  <header>请输入台车号</header>
  <article>
    <input type="text" name="num" id="num" placeholder="请输入台车号...">
    <button id='search'>保存</button>
  </article>
  <script type="text/javascript" src="./resource/jquery.min.js"></script>
  <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
  <script type="text/javascript" src="./resource/base.js"></script>
  <script type="text/javascript">
  (function($,POP,undefined){
    var stackId, serviceUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/bomconfirm/controller/index.php'
    var module = {
      doms: {
        num:$('#num'),
        btn:$('#search')
      },
      init: function(){
        this.bind()
      },
      bind: function(list){
        var _self = this
        this.doms.btn.on('click',function(){
          var num = _self.doms.num.val()
          if(!num){
            POP.ShowAlert('请输入垛号')
          } else {
            window.location.href = './bom.php?trolleyId=' + num
          }
        })
      }

    }
    module.init()
  })(jQuery,POP)
  </script>
</body>
</html>




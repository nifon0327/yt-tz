<?php
/**
 * User: JesseChen
 * Date: 2019/1/16
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
  <title></title>
  <style type="text/css">
     header{height: 40px;font-size:14px;width: 100%;padding-top: 10px;}
     #pics {position: relative;}
     #pics img {max-width:100%;}
     .imgs { max-width: 90%; margin:auto; margin-bottom:25px;}
  </style>
</head>
<body>
    <header>
      <!-- <span id="back">  </span> -->
      <a href="#" onclick="javascript:history.back();return false;"><返回</a>
    </header>
    <div id="pics"> </div>

<script src="./resource/jquery.min.js"></script>
<script type="text/javascript" src="./resource/base.js"></script>
<script type="text/javascript">
(function($,POP,SERVICE,undefined){

    var picurl = 'http://localhost/weixin/chuhuoconfirm/controller/';

    var module = {
    doms: {
     pics: $('#pics')
    },

    init: function(){
        var picpath = this.getUrlParameter('picpath')
        var patharray = picpath.split(';')

        this.appending(patharray);
        this.bind();
    },

    bind: function() {
        // $('#back').on('click',function(){
        //   window.location.href = "javascript:history.back()";
        // })
    },

    appending: function(paths) {
        var _self = this;
        var html_content =""
        for(var i = 0; i< paths.length; i++) {
            console.log(picurl + paths[i])
            html_content += '<div class="imgs"><img src=' + picurl + paths[i] + ' alt=""></div>'
        }
        _self.doms.pics.html(html_content)
    },

    getUrlParameter: function(sParamName, sURL) {
        var sURL = decodeURIComponent(sURL || location.search.slice(1));
        var rexUrl = new RegExp("(^|&)" + sParamName + "=([^&]*)(&|$)", "i");
        var aRes = sURL.match(rexUrl);
        return(aRes && aRes[2]) || "";
      },
}

module.init()
})(jQuery,POP,SERVICE,undefined)
</script>
</body>
</html>
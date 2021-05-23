<?php
    /**
     * Copyright (c) 2018 JesseChen <lkchan0719@gmail.com>
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

  <link rel="stylesheet" href="./resource/back.css"> 
  <link rel="stylesheet" href="./resource/base.css">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <!-- <link rel="stylesheet" href="./resource/main.css"> -->
  <link rel="stylesheet" href="./resource/specify.css">
</head>

<body>
    <div id="sheader">
        <div class="bbtns"><button id="back">返回</button></div>
        <div class="selection">
            <select name="ptype" id="pdstatus">
                <option value="-1">构件状态</option>
                <option value="0">待排产</option>
                <option value="1">排产中</option>
                <option value="2">浇筑</option>
                <option value="3">脱模</option>
                <option value="4">待入库</option>
                <option value="5">已入库</option>
                <option value="6">出货</option>

            </select>
        </div>
        <div class="sinfos"><span id="infos"></span></div>
    </div>

    <div id="sbody">
        <table id="specific">
            <thead>
                <tr>
                    <th>序号</th>
                    <th>构件名称</th>
                    <th>待排产</th>
                    <th>排产中</th>
                    <th>浇筑</th>
                    <th>脱模</th>
                    <th>待入库</th>
                    <th>已入库</th>
                    <th>出货</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</body>
<script src="./resource/LCalendar.js"></script>
<script src="./resource/mobileSelect.js"></script>

<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.bootcss.com/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.bootcss.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="./resource/datepicker.js"></script>
<script type="text/javascript" src="./resource/base.js"></script>
<script type="text/javascript"> 
(function($, POP, SERVICE, undefined){
    var serviceUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/scheduleconfirm/controller/index.php';
    var specifyUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/scheduleconfirm/view/progress_info.php?';
    var status_map = ["待排产","排产中","浇筑","脱模","待入库","已入库","出货"];
    var ttypes; 
    var companyId,buildno,floorno,typeid,cname;
    var module = {
        doms: {
            pdstatus: $('#pdstatus'),
            infos: $('#infos'),
            tcontent: $('#specific tbody'),
        },

        init: function(){
            var _self = this;
            _self.getparams();
            // _self.getstatus();
            _self.genheaderinfo();
            _self.bind();
        },

        bind: function() {
            var _self = this;
            _self.doms.pdstatus.change(function(){
                var status    = $('#pdstatus :selected').val()
                if(status != -1)
                    _self.getstatus()
            }),

            $('#back').on("click", function() {
                window.location.href = specifyUrl + 'cid=' + companyId + '&bno=' + buildno + '&cname=' + cname;
            })
        },
        getparams: function(){
            var _self = this
            companyId = _self.getUrlParameter('cid')
            buildno   = _self.getUrlParameter('bno')
            floorno   = _self.getUrlParameter('fno')
            typeid    = _self.getUrlParameter('tid')  
            cname     = _self.getUrlParameter('cname')
            ttypes    = _self.getUrlParameter('sptype')
        },

        genheaderinfo: function() {
            var _self = this

            _self.doms.infos.append(cname + ' ' + buildno + '栋' + floorno + '层' + '  ' + ttypes )
        },

        getstatus: function() {
            var _self = this;
            var status    = $('#pdstatus :selected').val()
            SERVICE.sendSHR(serviceUrl, {action:"getProductStateSpecify", CompanyId:companyId, BuildingNo:buildno, FloorNo:floorno, TypeId:typeid, Status:status}, function(data) {
                if(data && data.result) {
                    console.log(data.result);
                    _self.gentable(data.result)
                }
            })
        },
        gentable: function(items) {
            var _self = this
            _self.doms.tcontent.empty()
            var content = '';
            var operator = '';
            var date = ''

            for(var i = 0; i<items.length; i++){
                var index = parseInt(items[i][2])
                content += '<tr><td class="status">'+ (i+1) + '</td><td class="status">'+ items[i][0] + '</td>'
                for(var j = 0; j <= 6; j++) {
                    if(j == index){
                        operator = (items[i][3] == null) ? " " : items[i][3]
                        date = (items[i][4] == null) ? " " : items[i][4]
                        content += '<td class="status">' +  operator + '<br>' + date +'</td>'
                    }else{
                        content += '<td></td>'
                    }
                }
            }
            content += '</tr>'
            _self.doms.tcontent.append(content)

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
</html>
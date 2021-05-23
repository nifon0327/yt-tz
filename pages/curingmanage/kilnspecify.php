<?php
    /**
     * Copyright (c) 2019 JesseChen <lkchan0719@gmail.com>
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

  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <link rel="stylesheet" href="../static/css/bootstrap-select.min.css">
  <link href="https://unpkg.com/bootstrap-table@1.14.1/dist/bootstrap-table.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="../static/css/base.css">
  <style>
    .infos {font-size: 24px; padding:0 50px;}
    button{width:100px; margin: 5px auto; border: solid 1px lightgray; border-radius:3px; padding-top: 3px; padding-bottom: 3px; font-size:16px; background-color:teal; color:white;}   
    #outkilnByHand{background-color:#eb5757;}
  </style>
</head>

<body>
    <div> 
        <button id="back"> 返回 </button>
        <span class="infos"> </span>
        <button id="outkiln"> 出窑 </button>
        <button id="outkilnByHand"> 手动出窑</button>
    </div>

    <div id="maintainer"> 
        <!-- table content -->
    </div>


 <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
 <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
 <script src="../static/js/bootstrap-select.min.js"></script>
 <script src="https://unpkg.com/bootstrap-table@1.14.1/dist/bootstrap-table.min.js"></script>  
 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>  
 <script type="text/javascript" src="../static/js/base.js"></script> 

 <script> 
    (function($,POP,SERVICE,undefined){
        var MaintanOrderId,workshopName,productMaintainUrl,serviceUrl;
        var selectedRow = []

        var module = {
            init: function() {
                var _self = this

                _self.initServerUrl()
                _self.bind()
               
            },

            initServerUrl: function() {
                var _self = this
                $.getJSON("../project.json", function(json) {
                    productMaintainUrl = '<?php $_SERVER['HTTP_HOST']?>' + json.productMaintenance;
                    serviceUrl = '<?php $_SERVER['HTTP_HOST']?>' + json.common;
                    console.log(serviceUrl)
                    _self.getProductsByMaintanOrderId()
                })
            },

            getUrlParameter: function(sParamName, sURL) {
                var sURL = decodeURIComponent(sURL || location.search.slice(1));
                var rexUrl = new RegExp("(^|&)" + sParamName + "=([^&]*)(&|$)", "i");
                var aRes = sURL.match(rexUrl);
                return(aRes && aRes[2]) || "";
            },

            getProductsByMaintanOrderId: function() {
                var _self = this

                MaintanOrderId = this.getUrlParameter('MaintanOrderId')
                workshopName = this.getUrlParameter('workshopName')

                SERVICE.sendSHR(productMaintainUrl,{action:'getProductsByMaintanOrderId', maintanOrderId: MaintanOrderId}, function(oData){
                    if(oData.result) {
                        var ktype = oData.result.KType==1?'前库':'后库'
                        var spantext = workshopName + ' ' + ktype + ' ' + oData.result.LineNo + oData.result.KRowNo + ""
                        $('.infos').html(spantext)
                        _self.makeupdata(oData.result)
                        
                    } else {
                        POP.ShowAlert("未能获取到养护窑详情")
                    }
                })
            },

            makeupdata:function(data) {
                console.log(data)
                selectedRow = []
                var _self = this
                $('#maintainer').empty().append('<table id="table" class="table bootstrap-table" data-row-style="rowStyle">' +
                    '<thead>' +
                    '<th data-field="trolleyNo">台车号</th>' +
                    '<th data-field="forshort">客户名称</th>' +
                    '<th data-field="fcolD">业务单号</th>' +
                    '<th data-field="cname">产品名称</th>' +
                    '<th data-field="scdate">日期</th>' +
                    '<th data-field="semiproduct">半成品名称</th>' +
                    '<th data-field="amount">数量</th>' +
                    '<th data-field="inkilntime">入窑时间</th>' +
                    '<th data-field="timeelapse">在窑时长</th>' +
                    '</tr></thead></table>')
                
                    var i = 0, j = 0, temp = []

                    for(j = 0; j < data.listInfo.length; j++) {
                        temp.push({
                            indexId: i+1,
                            index: i,
                            maintanOrderId: data.maintanOrderId,
                            trolleyNo: data.TrolleyNo,
                            forshort: data.listInfo[j].Forshort,
                            fcolD: data.listInfo[j].OrderPO,
                            cname: data.listInfo[j].cName,
                            scdate: data.listInfo[j].scdate,
                            semiproduct: data.listInfo[j].StuffCname,
                            amount: data.listInfo[j].Qty,
                            inkilntime: data.MaintanTime,
                            timeelapse: _self.timeDifference(data.MaintanTime),
                        })
                    }
    
                    $('#table').bootstrapTable({
                        data: temp
                    });

                    for(i = 0; i <temp.length;) {
                        var skiplen = data.listInfo.length
                        $('#table').bootstrapTable('mergeCells', {
                            index: i,
                            field: 'state',
                            rowspan: skiplen,
                        })

                        $('#table').bootstrapTable('mergeCells', {
                            index: i,
                            field: 'indexId',
                            rowspan: skiplen,
                        })

                        $('#table').bootstrapTable('mergeCells', {
                            index: i,
                            field: 'trolleyNo',
                            rowspan: skiplen,
                        })

                        $('#table').bootstrapTable('mergeCells', {
                            index: i,
                            field: 'kilnbit',
                            rowspan: skiplen,
                        })

                        $('#table').bootstrapTable('mergeCells', {
                            index: i,
                            field: 'inkilntime',
                            rowspan: skiplen,
                        })

                        $('#table').bootstrapTable('mergeCells', {
                            index: i,
                            field: 'timeelapse',
                            rowspan: skiplen,
                        })
                        
                        i = i + skiplen
                    }

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

            },

            selectedRowCheck: function() {
                console.log(selectedRow)
                if (selectedRow.length == 0) {
                    POP.ShowAlert('请勾选构件')
                    return false
                } else if(selectedRow.length > 1) {
                    POP.ShowAlert("只能选择一个构件")
                    return false
                }

                return true
            },

            bind: function() {
                var _self = this

                $('#outkiln').click(function() {
                    // if () {
                        POP.ShowConfirm('请确认是否出窑', '确定', '取消', function(){
                            SERVICE.sendSHR(productMaintainUrl,{action:'outKilnBit', maintanOrderId: MaintanOrderId}, function(oData){
                                if(oData.result == true) {
                                    POP.ShowAlert("出窑操作已发送，请等待")
                                } else {
                                    POP.ShowAlert("出窑操作发送失败")
                                }
                            })
                        })
                    // }
                })

                $('#outkilnByHand').click(function() {
                    // if () {
                        POP.ShowConfirm('谨慎操作！请确认是否手动出窑该构件！', '确定', '取消', function(){
                            SERVICE.sendSHR(productMaintainUrl,{action:'outKilnBitForce', maintanOrderId: MaintanOrderId}, function(oData){
                                if(oData.result == true) {
                                    POP.ShowAlert("手动出窑成功")
                                } else {
                                    POP.ShowAlert("手动出窑失败")
                                }
                            })
                        })
                    // }
                })
                $('#back').click(function() {
                    window.location.href = './kilnmaintain.php?workshopName=' + workshopName
                })
            },

            timeDifference: function(tmpTime) {
                var retstring = ''

                var mm=1000;//1000毫秒 代表1秒
                var minute = mm * 60;
        
                var ansTimeDifference=0;//记录时间差
                var tmpTimeStamp = tmpTime ? Date.parse(tmpTime.replace(/-/gi, "/")) : new Date().getTime();//将 yyyy-mm-dd H:m:s 进行正则匹配
                var nowTime = new Date().getTime();//获取当前时间戳
                var tmpTimeDifference = nowTime - tmpTimeStamp;//计算当前与需要计算的时间的时间戳的差值
                if (tmpTimeDifference < 0) {                //时间超出，不能计算
                    return retstring
                }
                console.log(tmpTimeDifference)
                var DifferebceMinute = tmpTimeDifference / minute;//进行分钟取整

                var hours = parseInt(DifferebceMinute/60);
                var days = parseInt(hours/24);
                hours -= days * 24
                var minutes = Math.floor(DifferebceMinute % 60)

                if (days >= 1) {
                    retstring += days + '天'
                }

                if (hours >= 1) {
                    retstring += hours + '小时'
                }

                if (minutes >= 1) {
                    retstring += minutes + '分钟'
                }

                return retstring
            },

    }
        
        module.init()
    })(jQuery,POP,SERVICE)
</script>
</body>
</html>
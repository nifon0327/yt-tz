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

  <!-- <link rel="stylesheet" href="./resource/back.css"> 
  <link rel="stylesheet" href="./resource/base.css"> -->
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <link rel="stylesheet" href="../static/css/bootstrap-select.min.css">
  <link href="https://unpkg.com/bootstrap-table@1.14.1/dist/bootstrap-table.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="../static/css/base.css">
  <style>
    body {width: 1500px;}
    button{width:100px; margin: 5px auto; border: solid 1px lightgray; border-radius:3px; padding-top: 3px; padding-bottom: 3px; font-size:16px; background-color:teal; color:white;}
    .title{dispaly:inline-block; width:90%;}
    .selection {margin:auto; width: 20%;}
    .bg-color{ background-color: #d1e5f7;}
    #table th { border: 1px solid #1f1f1f;}
    #table td { border: 1px solid #1f1f1f; width: 420px; height:120px;}
    #table tr td:nth-child(1),
    #table tr td:nth-child(7) {
        width: 50px;
        background-color: #fff;
    }
    #table tr:nth-child(1) td:nth-child(9),
    #table tr:nth-child(1) td:nth-child(12) {
        background-color: #333333;
    }
    .kilntitle { display: flex; justify-content: center; }
    .title {flex:1;}
    .title h1  { text-align: center; font-size: 1.5rem; }
  </style>
</head>

<body>
 <div class="selection"> <select class="selectpicker" data-width="150px" id="line" title="产线"></select> </div>
 <div class="kilntitle"><span class="title"><h1>前库</h1></span> <span class="title"><h1> 后库 </h1></span></div>
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
        var workshopId, workshopIdUrl, KRowNo, list=[], workshopName;

        var module = {
            init: function() {
                this.reloadpage()
                this.bind()
                this.initServerUrl()
                
            },

            initServerUrl: function() {
                var _self = this
                $.getJSON("../project.json", function(json) {
                    productMaintainUrl = '<?php $_SERVER['HTTP_HOST']?>' + json.productMaintenance;
                    serviceUrl = '<?php $_SERVER['HTTP_HOST']?>' + json.common;
                    console.log(serviceUrl)
                    _self.selectinit();
                   
                })
            },

            reloadpage: function() {
                var _self = this
                setInterval(function() {
                    if (workshopId || workshopIdUrl) {
                        _self.getKilnBits()
                    }
                }, 30*1000)
            },

            selectinit: function() {
                var _self = this;

                _self.urlparameter()
                _self.getWorkShop()
                
            },

            urlparameter: function() {
                var _self = this
                var workshopidurl = _self.getUrlParameter('workshopName')
                
                if (workshopidurl) {
                    if(workshopidurl == "PC-1") {
                        workshopIdUrl = 101
                    } else if(workshopidurl == "PC-4") {
                        workshopIdUrl = 104
                    }
                    _self.getKilnBits()  
                }
            },

            getUrlParameter: function(sParamName, sURL) {
                var sURL = decodeURIComponent(sURL || location.search.slice(1));
                var rexUrl = new RegExp("(^|&)" + sParamName + "=([^&]*)(&|$)", "i");
                var aRes = sURL.match(rexUrl);
                return(aRes && aRes[2]) || "";
            },

            bind: function() {
                var _self = this

                $('#line').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
                    workshopId = this.value
                    console.log(this.value)
                    console.log(previousValue)
                    if(this.value == "101" || this.value == "104") {
                        workshopIdUrl = -1
                        workshopName = $('#line :selected').text()
                        _self.getKilnBits()
                    }
                       
                })  
            },

            getWorkShop: function() {
                SERVICE.sendSHR(serviceUrl,{action:'getWorkShop'}, function(oData){
                    if(oData.result.length != 0) {
                        var content = ''
                        for(var i = 0; i<oData.result.length; i++) {
                            if((oData.result[i].Id == "101") || (oData.result[i].Id == "104")) {
                                content += '<option value=' + oData.result[i].Id + '>' + oData.result[i].Name + '</option>' 
                            }
                        }
                        $('#line').empty().append(content);
                        $('#line').selectpicker('refresh');
                        if (workshopIdUrl != -1) {
                            $('#line').selectpicker('val', workshopIdUrl)
                        }
                    } else {
                        POP.ShowAlert("未能获取到产线信息")
                    }
                })
            },

            getKilnBits: function() {
                var _self = this;
                workshopId = workshopId ? workshopId: workshopIdUrl
                SERVICE.sendSHR(productMaintainUrl,{action:'getKilnBits', workshopId: workshopId}, function(oData){
                    if(oData.result) {
                        _self.makeupdata(oData.result)
                    } else {
                        POP.ShowAlert("未能获取到养护窑详情")
                    }
                })
            },

            makeupdata: function(data) {
                var _self = this;
                var temp = []
                for(var i = 1; i<11; i++) {
                    KRowNo = i;
                    var rowinfo = data.filter(function(element, index, array){
                        return (element.KRowNo == KRowNo)
                    })

                    // console.log(rowinfo)
                    temp.push(_self.makeuprowinfo(rowinfo))
                }
                temp[0].bcolB = ''
                temp[0].bcolE = ''
                list = temp
                _self.generateTable()
            },

            makeuprowinfo: function(rows) {
                var o = new Object()
                o.frowidx = rows[0].KRowNo
                o.browidx = rows[0].KRowNo
                for(var i = 0; i<rows.length; i++) {
                    var showinfo = rows[i].TemperatureValue + ',' + rows[i].HumidityValue + ',' + rows[i].MaintanTime + ',' + rows[i].TrolleyNo + ',' + rows[i].MaintanOrderId;
                    if(rows[i].KType == 1) {
                        switch(rows[i].LineNo) {
                        case 'A':
                            o.fcolA = showinfo
                            break;
                        case 'B':
                            o.fcolB = showinfo
                            break;
                        case 'C':
                            o.fcolC = showinfo
                            break;
                        case 'D':
                            o.fcolD = showinfo
                            break;
                        case 'E':
                            o.fcolE = showinfo
                            break;
                        }
                    } else if(rows[i].KType == 2) {
                        switch(rows[i].LineNo) {
                        case 'A':
                            o.bcolA = showinfo
                            break;
                        case 'B':
                            o.bcolB = showinfo
                            break;
                        case 'C':
                            o.bcolC = showinfo
                            break;
                        case 'D':
                            o.bcolD = showinfo
                            break;
                        case 'E':
                            o.bcolE = showinfo
                            break;
                        }
                    }
                }

                return o;
            },
 
            generateTable: function() {
                var _self = this
                $('#maintainer').empty().append('<table id="table" class="table bootstrap-table" data-row-style="rowStyle">' +
                    '<thead><tr><th data-field="frowidx" data-halign="center" data-align="center">前库</th>' +
                    '<th data-field="fcolA" data-formatter="beautifydata" data-halign="center" >A</th>' +
                    '<th data-field="fcolB" data-formatter="beautifydata" data-halign="center" >B</th>' +
                    '<th data-field="fcolC" data-formatter="beautifydata" data-halign="center" >C</th>' +
                    '<th data-field="fcolD" data-formatter="beautifydata" data-halign="center" >D</th>' +
                    '<th data-field="fcolE" data-formatter="beautifydata" data-halign="center" >E</th>' +
                    '<th data-field="frowidx" data-halign="center" data-align="center">后库</th>' +
                    '<th data-field="bcolA" data-formatter="beautifydata" data-halign="center" >A</th>' +
                    '<th data-field="bcolB" data-formatter="beautifydata" data-halign="center" >B</th>' +
                    '<th data-field="bcolC" data-formatter="beautifydata" data-halign="center" >C</th>' +
                    '<th data-field="bcolD" data-formatter="beautifydata" data-halign="center" >D</th>' +
                    '<th data-field="bcolE" data-formatter="beautifydata" data-halign="center" >E</th>' +
                    '</tr></thead></table>')

                $('#table').bootstrapTable({
                    data: list,
                    onClickCell:function(field, value, row, $element){
                            var index = row.browidx
                            if((field == 'bcolB' || field == 'bcolE') && (index== 1)) {
                                // do nothing
                            } else {
                                _self.jumpPage(value)
                            }
                           
                    },
                });

                var classes = []
                classes.push('table-bordered')
                $("#table").bootstrapTable('refreshOptions', {
                    classes: classes.join(' ')
                })
            },

            jumpPage: function(value) {
                var arr = value.split(',')
                var MaintanOrderId = arr[4]
                window.location.href = './kilnspecify.php?MaintanOrderId=' + MaintanOrderId + '&workshopName=' + workshopName
            },
            
        }

        module.init()
    })(jQuery,POP,SERVICE)

    function beautifydata(value) {
        var content = ""
        if(value) {
            var arr = value.split(',')
            // console.log(arr)
            var intime = arr[2]
            var timeElapse = timeDifference(intime)
            if (arr[3] != "null") {
                content = '台车号：' + arr[3] + '<br>进窑时间：' + intime + '<br>在窑时长：' + timeElapse + '<br>温度：' + arr[0] + '<br>湿度：' + arr[1]
            } else {
                content = ""
            }
        }

        return content
    }

    function rowStyle() {
        classes = []
        return {
            classes: 'bg-color'
        }
    }

    function timeDifference(tmpTime) {
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
            }

 </script>
</body>
</html>
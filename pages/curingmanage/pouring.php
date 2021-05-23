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
    button{width:100px; margin: 5px auto; border: solid 1px lightgray; border-radius:3px; padding-top: 3px; padding-bottom: 3px; font-size:16px; background-color:teal; color:white;}
    #inkilnByHand { background-color:#eb5757;}
    #lineTitle {text-align: center;}
    #showhint { text-align: center;}
  </style>
</head>

<body>
<div class="selectgroup">
    <select class="selectpicker" data-width="150px" id="line" title="产线"></select>
    <!-- <select class="selectpicker" data-width="150px" id="project" title="项目"> </select> -->
    <select class="selectpicker" data-width="150px" id="date" title="日期"> </select>
    <select class="selectpicker" data-width="150px" id="trolleyno" title="台车号"> </select>
    <select class="selectpicker" data-width="150px" id="maintainstatus" title="状态"> </select>


    <button id="searching"> 查询 </button>
    <button id="kilnChooseBtn"> 窑位选择 </button>
    <button id="inKilnPouringBtn"> 入窑养护 </button>
    <button id="inkilnByHand">手动入窑</button>

</div>

<div id="information">  </div>

<div id="kilnchoosePop" title="窑位选择">
    <h1 id="lineTitle"> </h1>
    <div>
        <label> 位置：</label>
        <select id="seats" class="selectpicker">
            <option value="-1">请选择</option>
            <option value="1"> 前库</option>
            <option value="2"> 后库</option>
        </select>
    </div>

    <div>
        <label> 位号： </label>
        <select class="selectpicker" id="selcolumn" title="列号"> </select>
        <select class="selectpicker" id="selrow" title="行号">  </select>
    </div>
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
    var workshopId,
        workshopName,
        tradeId,
        scdate,
        trolleyNo,
        maintenanceStatus,
        seatType,
        lineNo,
        kilnId;
    var productMaintainUrl = '', serviceUrl = '';
    var selectedRow = [], list = [];

    var module = {
        init: function() {
            this.initServerUrl()
            this.bind()
            this.initDiag()
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

        selectinit: function() {
           var _self = this;

           _self.getWorkShop()
        //    _self.getCompanyForShort()
           _self.getTrolleyInfo()
           _self.getMaintenanceStatus()

        },

        getWorkShop: function() {
            SERVICE.sendSHR(serviceUrl,{action:'getWorkShop'}, function(oData){
                if(oData.result) {
                    var content = '<option title="产线" value=-1>取消选择</option>'
                    for(var i = 0; i<oData.result.length; i++) {
                        if((oData.result[i].Id == "101") || (oData.result[i].Id == "104")) {
                            content += '<option value=' + oData.result[i].Id + '>' + oData.result[i].Name + '</option>'
                        }
                    }
                    workshopId = -1
                    $('#line').empty().append(content);
                    $('#line').selectpicker('refresh');
                    $('#line').selectpicker('val', -1);

                } else {
                    POP.ShowAlert("未能获取到产线信息")
                }
            })
        },

        // getCompanyForShort: function() {
        //     SERVICE.sendSHR(productMaintainUrl,{action:'getCompanyForShort'}, function(oData){
        //         if(oData.result) {
        //             var content = '<option title="项目" value=-1>取消选择</option>'
        //             for(var i = 0; i<oData.result.length; i++) {
        //                 content += '<option value=' + oData.result[i].TradeId + '>' + oData.result[i].Forshort + '</option>'
        //             }
        //             tradeId = -1;
        //             $('#project').empty().append(content);
        //             $('#project').selectpicker('refresh');
        //             $('#project').selectpicker('val', -1);
        //         } else {
        //             POP.ShowAlert("未能获取到项目信息")
        //         }
        //     })
        // },

        getTrolleyInfo: function() {
            SERVICE.sendSHR(serviceUrl,{action:'getTrolleyInfo'}, function(oData){
                if(oData.result) {
                    var content = '<option title="台车号" value=-1>取消选择</option>'
                    for(var i = 0; i<oData.result.length; i++) {
                        content += '<option value=' + oData.result[i].trolleyNo + '>' + oData.result[i].trolleyNo + '</option>'
                    }
                    trolleyNo = -1
                    $('#trolleyno').empty().append(content);
                    $('#trolleyno').selectpicker('refresh');
                    $('#trolleyno').selectpicker('val', -1);
                } else {
                    POP.ShowAlert("未能获取到台车号信息")
                }
            })
        },

        getMaintenanceStatus: function() {
            SERVICE.sendSHR(productMaintainUrl,{action:'getMaintenanceStatus'}, function(oData){
                if(oData.result) {
                    var content = '<option title="状态" value=-1>取消选择</option>'
                    for(var i = 0; i<oData.result.length; i++) {
                        content += '<option value=' + oData.result[i].key + '>' + oData.result[i].value + '</option>'
                    }
                    maintenanceStatus = -1
                    $('#maintainstatus').empty().append(content);
                    $('#maintainstatus').selectpicker('refresh');
                    $('#maintainstatus').selectpicker('val', -1);
                } else {
                    POP.ShowAlert("未能获取到窑位状态信息")
                }
            })
        },

        getDate: function() {
            SERVICE.sendSHR(productMaintainUrl,{action:'getDate',tradeId:'', workshopId:workshopId}, function(oData){
                if(oData && oData.result) {
                    var content = '<option title="日期" value=-1>取消选择</option>'
                    for(var i = 0; i<oData.result.length; i++) {
                        content += '<option value=' + oData.result[i].scdate + '>' + oData.result[i].scdate + '</option>'
                    }
                    scdate = -1
                    $('#date').empty().append(content);
                    $('#date').selectpicker('refresh');
                    $('#date').selectpicker('val', -1);
                } else {
                    POP.ShowAlert("未能获取到排产时间信息")
                }
            })
        },

        getLineNo: function() {
            if( (workshopId == -1)  || (seatType == -1) ) {
                POP.ShowAlert("请确认是否选择产线和库位置")
                return;
            }

            SERVICE.sendSHR(productMaintainUrl,{action:'getLineNo', workshopId: workshopId, type: seatType}, function(oData){
                if(oData.result) {
                    var content = '<option title="列号" value=-1>取消选择</option>'
                    for(var i = 0; i<oData.result.length; i++) {
                        content += '<option value=' + oData.result[i].LineNo + '>' + oData.result[i].LineNo + '</option>'
                    }
                    $('#selcolumn').empty().append(content);
                    $('#selcolumn').selectpicker('refresh');
                    $('#selcolumn').selectpicker('val', -1);
                } else {
                    POP.ShowAlert("未能获取到列信息")
                }
            })
        },

        getRowNo: function() {
            if( (workshopId == -1)  || (seatType == -1) ) {
                POP.ShowAlert("请确认是否选择产线和库位置")
                return;
            }

            SERVICE.sendSHR(productMaintainUrl,{action:'getRowNo', workshopId: workshopId, type: seatType, lineNo: LineNo}, function(oData){
                if(oData.result) {
                    var content = '<option title="行号" value=-1>取消选择</option>'
                    for(var i = 0; i<oData.result.length; i++) {
                        if (seatType == 2 && (LineNo == "B" || LineNo == "E") && oData.result[i].KRowNo == 1){
                            content += '<option disabled value=' + oData.result[i].kilnId + '>' + oData.result[i].KRowNo + '</option>'
                        } else {
                            content += '<option value=' + oData.result[i].kilnId + '>' + oData.result[i].KRowNo + '</option>'
                        }
                    }
                    $('#selrow').empty().append(content);
                    $('#selrow').selectpicker('refresh');
                    $('#selrow').selectpicker('val', -1);
                } else {
                    POP.ShowAlert("未能获取到行信息")
                }
            })
        },

        reloadpage: function() {
                var _self = this
                setInterval(function() {
                   _self.updateSomeColumns()
                }, 30*1000)
        },

        updateSomeColumns: function() {
            var _self = this

            var rscdate = scdate == -1?'':scdate
            var rtrolleyNo = trolleyNo == -1?'':trolleyNo
            var rstatus = maintenanceStatus == -1?'':maintenanceStatus


            SERVICE.sendSHR(productMaintainUrl,{action:'searchProducts', workshopId: workshopId,tradeId: '',scdate:rscdate,trolleyNo:rtrolleyNo,status:rstatus}, function(oData){
                        if(oData.result) {
                           // console.log(oData.result)
                            _self.refreshpartly(oData.result)
                        } else {
                            POP.ShowAlert("暂无养护窑相关信息")
                            $('#information').empty().append('<h2 id="showhint>无相关内容</h2>')
                        }
            })

        },

        refreshpartly: function(data) {
            var _self = this;
            var j = 0, i = 0;
            
            for(i = 0; i < data.length; i++) {
                for(var k = 0; k < data[i].list.length; k++) {
                    $('#table').bootstrapTable('updateCell', {
                        //reinit: false,
                        index: j+k,
                        field: 'status',
                        value:  _self.convertStatus(data[i].Status)
                    })
                    $('#table').bootstrapTable('updateCell', {
                        //reinit: false,
                        index: j+k,
                        field: 'timeelapse',
                        value: _self.timeDifference(data[i].MaintanTime)
                    })
                }

                j += data[i].list.length;
            }

            for(j = 0, i= 0; j < data.length; i++){
                var skiplen = data[i].list.length
                $('#table').bootstrapTable('mergeCells', {
                    index: j,
                    field: 'state',
                    rowspan: skiplen,
                })

                $('#table').bootstrapTable('mergeCells', {
                    index: j,
                    field: 'indexId',
                    rowspan: skiplen,
                })

                $('#table').bootstrapTable('mergeCells', {
                    index: j,
                    field: 'trolleyNo',
                    rowspan: skiplen,
                })

                $('#table').bootstrapTable('mergeCells', {
                    index: j,
                    field: 'workshop',
                    rowspan: skiplen,
                })

                $('#table').bootstrapTable('mergeCells', {
                    index: j,
                    field: 'kilnbit',
                    rowspan: skiplen,
                })

                $('#table').bootstrapTable('mergeCells', {
                    index: j,
                    field: 'inkilntime',
                    rowspan: skiplen,
                })

                $('#table').bootstrapTable('mergeCells', {
                    index: j,
                    field: 'status',
                    rowspan: skiplen,
                })

                $('#table').bootstrapTable('mergeCells', {
                    index: j,
                    field: 'timeelapse',
                    rowspan: skiplen,
                })

                $('#table').bootstrapTable('mergeCells', {
                    index: j,
                    field: 'operator',
                    rowspan: skiplen,
				})


                j += data[i].list.length;
            }
        },

        bind: function() {
            var _self = this
            $('#line').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
                workshopId = this.value
                workshopName = $('#line :selected').text()
                $('#lineTitle').empty().text(workshopName)
                //console.log(this.value, workshopName, previousValue)
                if(workshopId != -1) {
                    _self.getDate()
                }
            })

            // $('#project').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
            //     tradeId = this.value
            //     console.log(this.value, previousValue)
            //     if(tradeId != -1 && workshopId != -1) {
            //         _self.getDate()
            //     }
            // })

            $('#trolleyno').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
                trolleyNo = this.value
                //console.log(this.value, previousValue)
            })

            $('#maintainstatus').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
                maintenanceStatus = this.value
                //console.log(this.value, previousValue)
            })

            $('#date').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
                scdate = this.value
                //console.log(this.value, previousValue)
            })

            $('#seats').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
                seatType = this.value
                //console.log(this.value, previousValue)
                _self.getLineNo()
            })

            $('#selcolumn').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
                LineNo = this.value
                //console.log(this.value, previousValue)
                if(LineNo != -1) {
                    _self.getRowNo()
                }
            })

            $('#selrow').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
                kilnId = this.value
                //console.log(this.value, previousValue)
            })

            $('#kilnChooseBtn').click(function() {
                if(_self.kilnChooseCheck()) {
                    $('#kilnchoosePop').dialog('open')
                }

            })

            $('#searching').click(function() {
                if((workshopId && workshopId != -1)) {
                    var rscdate = scdate == -1?'':scdate
                    var rtrolleyNo = trolleyNo == -1?'':trolleyNo
                    var rstatus = maintenanceStatus == -1?'':maintenanceStatus

                    SERVICE.sendSHR(productMaintainUrl,{action:'searchProducts', workshopId: workshopId,tradeId: '',scdate:rscdate,trolleyNo:rtrolleyNo,status:rstatus}, function(oData){
                        if(oData.result) {
                            _self.reloadpage()
                            //console.log(oData.result)
                            _self.generateTable(oData.result)
                        } else {
                            POP.ShowAlert("暂未查询到相关信息")
                        }
                    })
                } else {
                    POP.ShowAlert("请选择产线和项目信息")
                }
            })

            $('#inKilnPouringBtn').click(function() {
                if(_self.selectedRowCheck()){
                    POP.ShowConfirm('请确认是否入窑养护已选构件', '确定', '取消', function(){
                        var oArray = []
                        //console.log(list[selectedRow[0]])
                        oArray.push({maintanOrderId:list[selectedRow[0]].maintanOrderId})

                        SERVICE.sendSHR(productMaintainUrl,{action:'intoKilnBit',orders:JSON.stringify(oArray)}, function(oData){
                            // selectedRow = []
                            if(oData.result) {
                                //console.log(oData.result)
                                _self.retrieveInfo()
                                POP.ShowAlert("入窑养护设置成功")
                            } else {
                                POP.ShowAlert("入窑养护设置失败")
                            }
                        })
                     })
                }
            })

            $('#inkilnByHand').click(function() {
                if(_self.selectedRowCheck()){
                    POP.ShowConfirm('谨慎操作！请确认是否直接入窑该构件！', '确定', '取消', function(){
                        var oArray = []
                        console.log(list[selectedRow[0]])
                        oArray.push({maintanOrderId:list[selectedRow[0]].maintanOrderId})

                        SERVICE.sendSHR(productMaintainUrl,{action:'intoKilnBitForce',orders:JSON.stringify(oArray)}, function(oData){
                            // selectedRow = []
                            if(oData.result) {
                                //console.log(oData.result)
                                _self.retrieveInfo()
                                POP.ShowAlert("构件已入窑")
                            } else {
                                POP.ShowAlert("构件入窑失败，请检查构件相关状态是否正确")
                            }
                        })
                     })
                }
            })
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

            if (list[selectedRow[0]].status == '未设置') {
                POP.ShowAlert("请先对构件选择窑位")
                return false
            }

            return true
        },

        kilnChooseCheck: function(){
            if(selectedRow.length != 1){
                POP.ShowAlert('请先勾选一个构件')
                return false;
            }
            return true;
        },

        initDiag: function() {
            var _self = this
            $('#kilnchoosePop').dialog({
                autoOpen: false,
                height:500,
                width:800,
                modal: true,
                buttons: [
                    {
                        text: "确定",
                        // "class": "btn",
                        click: function() {
                            var trolleryNo_temp = list[selectedRow[0]].trolleyNo
                            if(trolleryNo_temp == -1){ return }
                            if((kilnId == -1) ) {
                                POP.ShowAlert("请确认台车号和窑位行号是否正确")
                                return
                            }

                            SERVICE.sendSHR(productMaintainUrl,{action:'selectKilnBit', kilnId: kilnId, trolleyNo: trolleryNo_temp}, function(oData){
                                if(oData.result == true) {
                                    POP.ShowAlert("窑位选择成功")
                                    $('#seat').selectpicker('val', -1);
                                    $('#selcolumn').selectpicker('val', -1);
                                    $('#selrow').selectpicker('val', -1);
                                    _self.retrieveInfo()
                                } else {
                                    POP.ShowAlert(oData.msg)
                                }
                            })
                        }
                    },
                    {
                        text: "取消",
                        // "class": "btn",
                        click: function() {
                            $('#seat').selectpicker('val', -1);
                            $('#selcolumn').selectpicker('val', -1);
                            $('#selrow').selectpicker('val', -1);
                            $(this).dialog("close")
                        }
                    }
                ],
                close: function() {
                    $('#seat').selectpicker('val', -1);
                    $('#selcolumn').selectpicker('val', -1);
                    $('#selrow').selectpicker('val', -1);
                }
            })
        },
        retrieveInfo: function() {
            var _self = this

            var rscdate = scdate == -1?'':scdate
            var rtrolleyNo = trolleyNo == -1?'':trolleyNo
            var rstatus = maintenanceStatus == -1?'':maintenanceStatus

            $('#kilnchoosePop').dialog('close')
            SERVICE.sendSHR(productMaintainUrl,{action:'searchProducts', workshopId: workshopId,tradeId: '',scdate:rscdate,trolleyNo:rtrolleyNo,status:rstatus}, function(oData){
                        if(oData.result) {
                            //console.log(oData.result)
                            _self.generateTable(oData.result)
                        } else {
                            POP.ShowAlert("暂无养护窑相关信息")
                            $('#information').empty().append('<h2 id="showhint>无相关内容</h2>')
                        }
            })
        },
        convertKtype: function(ktype) {
            if(ktype == "1")
                return "前库"
            else if(ktype == "2") {
                return "后库"
            }
            
        },
        generateTable: function(data) {
            var _self = this
            selectedRow = []
            $('#information').empty().append('<table id="table" class="table bootstrap-table" data-row-style="rowStyle">' +
                    '<thead><tr><th data-field="state" data-checkbox="true">选项</th>' +
                    '<th data-field="indexId" data-halign="center" data-align="center">序号</th>' +
                    '<th data-field="trolleyNo">台车号</th>' +
                    '<th data-field="workshop">产线</th>' +
                    '<th data-field="forshort">客户名称</th>' +
                    '<th data-field="fcolD">业务单号</th>' +
                    '<th data-field="cname">产品名称</th>' +
                    '<th data-field="scdate">日期</th>' +
                    '<th data-field="semiproduct">半成品名称</th>' +
                    '<th data-field="amount">数量</th>' +
                    '<th data-field="kilnbit">窑位</th>' +
                    '<th data-field="inkilntime">入窑时间</th>' +
                    '<th data-field="status">状态</th>' +
                    '<th data-field="timeelapse">在窑时长</th>' +
                    '<th data-field="operator">操作人</th>' +
                    '</tr></thead></table>')


            var i = 0, j = 0, temp = []
            for (i = 0; i<data.length; i++) {
                for(j = 0; j < data[i].list.length; j++) {
                    temp.push({
                        indexId: i+1,
                        index: i,
                        maintanOrderId: data[i].maintanOrderId,
                        trolleyNo: data[i].TrolleyNo,
                        workshop: _self.convertWorkshopId(data[i].list[j].WorkShopId),
                        forshort: data[i].list[j].Forshort,
                        fcolD: data[i].list[j].OrderPO,
                        cname: data[i].list[j].cName,
                        scdate: data[i].list[j].scdate,
                        semiproduct: data[i].list[j].StuffCname,
                        amount: data[i].list[j].Qty,
                        kilnbit: data[i].KType?_self.convertKtype(data[i].KType) + ' ' + data[i].klinName:data[i].klinName,
                        inkilntime: data[i].MaintanTime,
                        status: _self.convertStatus(data[i].Status),
                        timeelapse: _self.timeDifference(data[i].MaintanTime),
                        operator: data[i].Name
                    })
                }
            }

            $('#table').bootstrapTable({
                 data: temp
            });

            for(i = 0, j = 0; i<temp.length; i += skiplen) {

                var skiplen = data[j++].list.length
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
                    field: 'workshop',
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
                    field: 'status',
                    rowspan: skiplen,
                })

                $('#table').bootstrapTable('mergeCells', {
                    index: i,
                    field: 'timeelapse',
                    rowspan: skiplen,
                })

                $('#table').bootstrapTable('mergeCells', {
                    index: i,
                    field: 'operator',
                    rowspan: skiplen,
                })

            }

            list = temp
            Array.prototype.remove = function(val) {
                var index = this.indexOf(val);
                if (index > -1) {
                    this.splice(index, 1);
                }
            };

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

            $('#table').off('check.bs.table').on('check.bs.table', function (e, row, $el) {
                selectedRow.push($el.closest('tr').data('index'))
            });
            $('#table').off('uncheck.bs.table').on('uncheck.bs.table', function (e, row, $el) {
                selectedRow.remove($el.closest('tr').data('index'))
            });

        },
        convertWorkshopId: function(shopid) {
            if(shopid == '101') {
                return 'PC-1'
            }else if(shopid == '104') {
                return 'PC-4'
            }else {
                return shopid
            }
        },
        convertStatus: function(status) {
            // 0 待养护 1 养护待确认  2 养护中  3 出窑待确
            if (status == 0) {
                return "待养护"
            } else if (status == 1) {
                return "养护待确认"
            } else if (status == 2) {
                return "养护中"
            } else if (status == 3) {
                return "出窑待确认"
            } else if (status == 4) {
                return "已出窑"
            }

            return ""
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

            var DifferebceMinute = tmpTimeDifference / minute;//进行分钟取整

            var hours = parseInt(DifferebceMinute/60);
            var days = parseInt(hours/24);
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

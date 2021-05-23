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

</head>

<body>
    <div class="contents">
    <div class="hselect">
        <div class="selection">
            <!-- <label for="customer">客户名</label> -->
            <select id="customer" ></select>
        </div>
        <!-- <div class="selection">
            <label for="building">楼栋信息</label>
            <select id="building" ></select>
        </div> -->
        <!-- <div class="selection">
            <label for="floor">楼层信息</label>
            <select id="floor" ></select>
        </div> -->
        <div class="selection">
            <!-- <label for="type">构件类型</label> -->
            <select id="type" ></select>
        </div>
        <div class="inputs">
            <!-- <label for="date">返修日期</label> -->
            <input type="text" id="date">
        </div>
    
    
        <div class="btns">
            <div class="btn">
                <button id="search">查询</button>
            </div>
            <div class="btn">
                <button id="newback">新增</button>
            </div>
            <div class="btn">
                <button id="scrap">报废</button>
            </div>
            <div class="btn">
                <button id="delete">删除</button>
            </div>
        </div>
    </div>
    
    
        <table class="btable" id="backcontent">
            <thead>
                <tr>
                    <th>选项</th>
                    <th>序号</th>
                    <th>退货日期</th>
                    <th>项目名称</th>
                    <th>产品类型</th>
                    <th>产品编号</th>
                    <th>报废原因</th>
					<th>负责产线</th>
                    <th>负责人</th>
                    <th>状态</th>
                    <th>操作人</th>
                    <th>操作时间</th>
                    <th>单据</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
</body>


<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.bootcss.com/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.bootcss.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="./resource/datepicker.js"></script>
<script type="text/javascript" src="./resource/base.js"></script>
<script type="text/javascript"> 
(function($, POP, SERVICE, undefined){
    var companyId, buildId, floorId, productType, scrapDate, projectname;
    var serviceUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/baofeiconfirm/controller/index.php';
    var lastcompanyId, lastbuildId, lastfloorId;
	var dlurl = 'http://test.matechstone.com/weixin/baofeiconfirm'
    var estates = new Array();
    estates[12] = "报废"
    estates[11] = "新增"

    var module = {
        doms: {
            customer: $('#customer'),
            // buildingname: $('#building'),
            // floorname: $('#floor'),
            productiontype: $('#type'),
            backdate: $('#date'),
            srcbtn: $('#search'),
            newbtn: $('#newback'),
            scrapbtn: $('#scrap'),
            deletebtn: $('#delete'),
            backcontent: $('#backcontent tbody'),
            checkboxs: $('#backcontent input:checkbox')
          
        },

        init: function () {
            var _self = this
            _self.initchoose()
            _self.initDateSelector()
            _self.bind()
            _self.initvar()
            _self.showAllScrapInfo()
        },
       
        initvar: function(){
            productType = '';
        },

        initchoose: function() {
            var _self = this
            SERVICE.sendSHR(serviceUrl,{action:'getScrapCompany'}, function(oData){
                console.log(oData)
                if(oData && oData.result && oData.result.length>0){
                    var cleandata = _self.removedup(oData.result)
                    _self.initCompany(cleandata)
                }
            })     
        },

        initCompany: function(list) {
            var _self = this
            var content = '<option value="-1"></option>'
            for(var i = 0; i< list.length; i++) {
                content += "<option value=" + list[i].CompanyId + '>' + list[i].Forshort + "</option>"
            }
            this.doms.customer.append(content)
            // companyId = ""
            companyId = $('#customer :selected').val()
            // projectname = $('#customer :selected').text()
            // console.log(companyId)
            // _self.getbuildId()
            // this.doms.customer.css("display", 'inline-block')
        },
       
       removedup: function(data) {
            var newArr = [];
            var result = [];
            for(var i = 0; i<data.length; i++) {
                newArr[data[i].Forshort] = data[i]
            }

            for(item in newArr) {
                result.push(newArr[item])
            }

            return result;
       },
    
    //    getbuildId: function() {
    //         var _self = this;
    //         var id = companyId;
    //         if(companyId == -1){
    //             id = lastcompanyId
    //         }

    //         SERVICE.sendSHR(serviceUrl, {action:'getReworkBuildNo',companyId:id}, function(data){
    //             if(data && data.result) {
    //                 _self.doms.buildingname.empty()
    //                 var content = '<option value="-1"></option>'
    //                 for(var i = 0; i < data.result.length; i++) {
    //                     content += "<option value=" + data.result[i].BuildNo + '>' + data.result[i].BuildNo + " 栋</option>"   
    //                 }
    //                 _self.doms.buildingname.append(content)
    //                 buildId = $('#building :selected').val()
    //                 console.log(buildId)
    //                 _self.doms.buildingname.css('display', 'inline-block');
    //                 // _self.getFloorNo()
    //             }
    //             else
    //             {
    //                 POP.ShowAlert("找不对对应的楼栋号")
    //             }
    //         })
    //     },

    //     getFloorNo: function(){
    //         var _self = this
    //         var cid = companyId, bid = buildId;
    //         if(companyId == -1){
    //             cid = lastcompanyId
    //         }
    //         if(buildId == -1){
    //             bid = lastbuildId
    //         }
    //         SERVICE.sendSHR(serviceUrl, {action:'getScrapFloorNo', companyId:cid, buildNo:bid}, function(data) {
    //             if(data && data.result) {
    //                 console.log(data.result)
    //                 _self.doms.floorname.empty()
    //                 var content = '<option value="-1"></option>'
    //                 for(var i = 0; i < data.result.length; i++) {
    //                     content += "<option value=" + data.result[i].OrderPO + '>' + data.result[i].Floor + " 层</option>"
    //                 }
    //                 _self.doms.floorname.append(content)
    //                 floorId = $('#floor :selected').val()
    //                 console.log(floorId)
    //                 _self.doms.floorname.css('display', 'inline-block');
    //                 // _self.getType();
    //             }
    //             else
    //             {
    //                 POP.ShowAlert("找不到对应的楼层信息")
    //             }
    //        })
    //     },

        getType: function() {
            var _self = this
            var cid = companyId
            // bid = buildId, fid = floorId;
            if(companyId == -1){
                cid = lastcompanyId
            }
            // if(buildId == -1){
            //     bid = lastbuildId
            // }
            // if(floorId == -1){
            //     fid = lastfloorId
            // }
            SERVICE.sendSHR(serviceUrl, {action: 'getScrapType', companyId:cid, buildId:"", orderPO:""}, function(data){
                if(data && data.result) {
                    _self.doms.productiontype.empty()
                    console.log(data.result)
                    var res = _self.removeduptype(data.result)
                    var content = '<option value="-1"></option>'
                    for(var i = 0; i < res.length; i++) {
                        content += "<option value=" + res[i].TypeId + '>' + res[i].TypeName + "</option>"
                    }
                    _self.doms.productiontype.append(content)
                    productType = $('#type :selected').val()
                    productType = ""
                    console.log(productType)
                    _self.doms.productiontype.css('display', 'inline-block');
                }else
                {
                    POP.ShowAlert("找不到对应的构件信息")
                }
            })
        },

        removeduptype: function(data){
            var newArr = [];
            var result = [];
            for(var i = 0; i<data.length; i++) {
                newArr[data[i].TypeName] = data[i]
            }

            for(item in newArr) {
                result.push(newArr[item])
            }

            return result;
        },

        initDateSelector: function(){
            var _self = this
            _self.doms.backdate.datepicker({dateFormat: "yy-mm-dd"})
        },


        bind: function() {
            var _self = this

            _self.doms.customer.change(function() {
                companyId = $('#customer :selected').val()
                console.log(companyId)
                if(companyId != -1)
                {
                    lastcompanyId=companyId;
                    //projectname = $('#customer :selected').text();
                    console.log(companyId);
                    // _self.getbuildId();
                    _self.getType();
                }
            });

            // _self.doms.buildingname.change(function() {
            //     buildId = $('#building :selected').val()
            //     if(buildId != -1)
            //     {
            //         lastbuildId = buildId
            //         _self.getFloorNo()
            //     }
            // });

            // _self.doms.floorname.change(function() {
            //     floorId = $('#floor :selected').val()
            //     if(floorId != -1)
            //     {
            //         lastfloorId = floorId;
            //         _self.getType();
            //     }
            // });

            _self.doms.productiontype.change(function() {
                productType = $('#type :selected').val()
                // if(productType != -1){
                //     productType = ""
                // }
                //     console.log(productType)
                // else
                //     productType='';
            });

             _self.doms.backdate.change(function(){
                scrapDate = _self.doms.backdate.datepicker({dateFormat:'yy-mm-dd'}).val()
            });

             _self.doms.srcbtn.click(function() {
                scrapDate = _self.doms.backdate.val()
                 _self.showSearchScrapInfo()
            });

             //新增
             _self.doms.newbtn.click(function() {
                window.location.href = '<?php $_SERVER['HTTP_HOST']?>/weixin/baofeiconfirm/view/newback.php'
             });

             _self.doms.scrapbtn.click(function() {
                 var ids = []
                 var states = 0
                 var i = 0, res = 0
                 $('input[type=checkbox]:checked').each(function() {
                        if(i++ == 0)
                        {
                            states = $(this).attr('id')
                            console.log(states)
                        }else{
                            if(states != $(this).attr('id'))
                            {
                                POP.ShowAlert("所选构件状态不一致")
                                res = 1
                                return
                            }
                        }
                        ids.push($(this).val())
                 })

                 if(ids.length == 0){
                     POP.ShowAlert("请先勾选对应的项目")
                 }
                 else if (res == 0){
                     console.log(ids.join(','))
                     _self.updateScrapStatus(ids.join(','))
                 }
             });

            _self.doms.deletebtn.click(function(){
                var ids = []
                var states = 0
                $('input[type=checkbox]:checked').each(function(){
                    states = $(this).attr('id')
                    console.log(states)
                    if(states == 11){
                        ids.push($(this).val())
                    }else{
                        POP.ShowAlert("请勾选状态为新增的构件")
                        return
                    }
                })

                if(ids.length == 0){
                    POP.ShowAlert("请勾选状态为新增的构件")
                }
                else
                {
                    console.log(ids.join(','))
                    _self.deleteUnfixed(ids.join(','))
                }
            })
        },

        deleteUnfixed: function(id){
            var _self = this
            SERVICE.sendSHR(serviceUrl, {action: 'dropScrapProduct', scrapProductIds:id}, function(data) {
                if(data && data.status == 0) {
                    _self.showSearchScrapInfo()
                        // _self.showAllReworkInfo()
                    POP.ShowAlert("删除成功")
                }
                else
                {
                    POP.ShowAlert("删除失败")
                }
            })
        },

        bindcheckbox: function() {
            var _self = this;
            $(':checkbox').click(function(){
                 console.log("hello")
                 if($(this).is(":checked")){
                     $(this).parent().parent().css({
                         backgroundColor: "#e8cb58"
                     })
                 }else{
                    $(this).parent().parent().css({
                         backgroundColor: ''
                     })
                 }
             });
        },
        // cleanselect: function() {
        //     var _self = this;
        
        //     $('#customer :selected').text(' ')
        //     _self.doms.buildingname.empty()
        //     // _self.doms.buildingname.find("option:selected").text("")
        //     _self.doms.floorname.empty()
        //     // _self.doms.floorname.find("option:selected").text("")
        //     _self.doms.productiontype.empty()
        //     // _self.doms.productiontype.find("option:selected").text("")
        //     _self.doms.backdate.val('')
        //     // _self.doms.backdate.find("option:selected").text("")
        // },

        updateScrapStatus: function(id) {
            var _self = this
            SERVICE.sendSHR(serviceUrl, {action: 'updateScrapProduct', scrapProductIds:id}, function(data) {
                if(data && data.status == 0) {
                    _self.showSearchScrapInfo()
                        // _self.showAllReworkInfo()
                    POP.ShowAlert("构件报废状态更新完成")
                }
                else
                {
                    POP.ShowAlert("无法更新该构件的报废状态")
                }
            })
        },

        showSearchScrapInfo: function() {
            var _self = this
            var cid = companyId, ttype = productType;
            // bid = buildId, fid = floorId, 
            if(companyId == -1){
                cid = ""
            }
            // if(buildId == -1){
            //     bid = ""
            // }
            // if(floorId == -1){
            //     fid = ""
            // }
            // if(productType == -1){
            //     ttype = ""
            // }
            console.log(cid,ttype,scrapDate)
            SERVICE.sendSHR(serviceUrl, {action: 'getScrapProduct', companyId:cid, buildNo:"", orderPO:"", typeId:ttype,scrapDate:scrapDate}, function(data){
                if(data && data.result){
                    console.log(data.result)
                    _self.createtable(data.result)
                }else{
                    //POP.ShowAlert("无报废相关内容")
                }
            })
        },

        showAllScrapInfo: function() {
            var _self = this
            SERVICE.sendSHR(serviceUrl, {action: 'getScrapProduct', companyId:"", buildNo:"", orderPO:"", typeId:"",reworkDate:""}, function(data){
                if(data && data.result){
                    console.log(data.result)
                    _self.createtable(data.result)
                }else{
                    //POP.ShowAlert("无报废相关内容")
                }
            })
        },

        createtable: function(data) {
            var _self = this
            _self.doms.backcontent.empty()
            for(var i=1; i<=data.length; i++){
                // var status = _self.getEState(data[i-1].ss_Estate, data[i-1].sm_Estate)
                var paths = data[i-1].InvoicePath.split('/')
                
                var filename = paths[paths.length-1]
                // console.log(filename)
                var firsttwo = '<tr><td><input type="checkbox" value='+ data[i-1].Id + ' id=' + data[i-1].Estate +' ></td>' + "<td>" + i + "</td>" 
                var lefts = "<td>"+ data[i-1].ScrapDate + "</td><td>"+ data[i-1].Forshort + "</td><td>" + data[i-1].TypeName + "</td><td>" + data[i-1].cName + "</td><td>" + data[i-1].ScrapAnalysis + "</td><td>" + data[i-1].Name + "</td><td>" + data[i-1].Directory + "</td><td>" + 
                             estates[data[i-1].Estate] +"</td><td>" + data[i-1].Operator + "</td><td>" + data[i-1].Modified + "</td>"
                var invoice = "<td><a href=" + dlurl +  data[i-1].InvoicePath + " download=" + filename +" >下载单据</a></td></tr>"

                _self.doms.backcontent.append(firsttwo+lefts+invoice)
            }
            _self.bindcheckbox()
        }

        // getEState:function(ss_Estate,sm_Estate){
        //     if(ss_Estate==11){
        //         return 11;
        //     }else if(ss_Estate==1&&sm_Estate==0){
        //         return 0;
        //     }else{
        //         return 1;
        //     }
        // }

    }

module.init()
})(jQuery,POP,SERVICE,undefined)

</script>

</html>

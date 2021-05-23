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
    <div class="hselect newhselect" id="productchoose">
        <div class="selection">
            <!-- <label for="customer" >项目名称</label> -->
            <select id="customer"></select>
        </div>

        <div class="selection">
            <!-- <label for="building">楼栋信息</label> -->
            <select id="building"></select>
        </div>

        <div class="selection">
            <!-- <label for="floor">楼层信息</label> -->
            <select id="floor"></select>
        </div>

        <div class="selection">
            <!-- <label for="type">构件类型</label> -->
            <select id="type"></select>
        </div>
        
        <div class="inputs">
            <input id="pname" type="text" placeholder="请输入产品名称">
        </div>
        
        <div class="btns">
            <button id="productsrch">查询</button>
        </div>      

        <div class="row newbtable">
            <div class="col align-self-center">
                <table id="productshow" class="btable">
                    <thead>
                        <tr>
                            <th>选项</th>
                            <th>序号</th>
                            <th>产品名称</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div> 
   

   

    <div class="row">
        <div id="items" class="col-12 col-lg-3 form-group">
            <label for="reworkdate">退货日期</label>
            <input type="text" id="reworkdate" class="form-control form-control-sm">
        </div>
    </div>

    <div class="row">
        <div id="items" class="col-12 col-lg-3 form-group">
            <label for="productname">产品名称</label>
            <input type="text" id="productname" class="form-control form-control-sm ">
        </div>
    </div>

    <div class="row">
        <div id="items" class="col-12 col-lg-3 form-group">
            <label for="reworkcompany">负责产线</label>
            <select id="reworkcompany" class="form-control form-control-sm"></select>
        </div>
    </div>

    <div id="items" class="row form-group">
        <div class="col-12 col-lg-3">
            <label for="operator">负责人</label>
            <select id="operator" class="form-control form-control-sm"></select>
        </div>
    </div>

    <div id="items" class="row form-group">
        <div class='col-12 col-lg-3'>
            <label for="invoice">单据</label>
            <input type="file" id="invoice" class="form-control  form-control-sm">
        </div>
        <div class="col-3 col-lg-2 btn">
            <button id="uploadfile">上传单据</button>
        </div>
    </div>

    

    <div id="items" class="row form-group">
        <div class="col-6 col-lg-6">
            <label for="reworkreason">报废原因</label>
            <textarea name="" id="reworkreason" cols="30" rows="8" class="form-control form-control-sm"></textarea>
        </div>
    </div>

    <div class="btn">
        <button id="save" >保存</button>
    </div>
    <div class="btn">
        <button id="cancel">取消</button>
    </div>
    
    
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
    var serviceUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/baofeiconfirm/controller/index.php';
    var uploadfileUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/baofeiconfirm/controller/upload.php'
    var currproductionname ="";
    var reworkDate="", currreworkcompany, currreworkreason, ipath,ProductID,curroperator;
    var productioninfoobj = [];

    var customer = $('#customer'),
        building = $('#building'),
        floor = $('#floor'),
        type = $('#type'),
        srch = $('#productsrch'),
        pname = $('#pname'),
        productbody = $('#productshow tbody'),
        allfiled = $([]).add(customer).add(building).add(floor).add(type);

    var companyId, buildId, floorId, productType;

    var module = {
        doms: {
            productname: $('#productname'),
            reworkdate: $('#reworkdate'),
            reworkcompany: $('#reworkcompany'),
            operator: $('#operator'),
            reworkreason: $('#reworkreason'),
            productchoose: $('#productchoose'),
            save: $('#save'),
            cancel: $('#cancel'),
            uploadfile: $('#uploadfile')
        },

        init: function(){
            var _self = this
            _self.initdiag()
            _self.initDateSelector()
            _self.initworkshop()
            _self.initoperator()
            _self.bind()
        
        },

        initDateSelector: function(){
            var _self = this
            _self.doms.reworkdate.datepicker({dateFormat: "yy-mm-dd"})
        },

        initworkshop: function() {
            var _self = this
            SERVICE.sendSHR(serviceUrl,{action:'getWorkshop'}, function(data){
                console.log(data)
                if(data && data.result)
                {
                        var datas = data.result;
                        var contents = "";
                        for(var i = 0; i< datas.length; i++) {
                            contents += "<option value=" + datas[i].Id + '>' + datas[i].Name + "</option>"
                        }
                        _self.doms.reworkcompany.append(contents)
                        currreworkcompany = $('#reworkcompany :selected').val();
                        _self.doms.reworkcompany.css("display", 'inline-block')
                    }
                    else 
                    {
                        POP.ShowAlert('出错了，未找到对应的公司名')
                    }
            })   
        },
        initoperator: function() {
            var _self = this
            SERVICE.sendSHR(serviceUrl,{action:'getDirector'}, function(data){
                console.log(data)
                if(data && data.result)
                {
                        var datas = data.result;
                        var contents = "";
                        for(var i = 0; i< datas.length; i++) {
                            contents += "<option value=" + datas[i].Id + '>' + datas[i].Name + "</option>"
                        }
                        _self.doms.operator.append(contents)
                        curroperator = $('#operator :selected').val();
                        // _self.doms.reworkcompany.css("display", 'inline-block')
                    }
                    else 
                    {
                        POP.ShowAlert('出错了，未找到对应的负责人信息')
                    }
            })   
        },

        initdiag: function() {
            var _self = this
            _self.doms.productchoose.dialog({
                autoOpen: false,
                height:500,
                width:800,
                modal: true,
                buttons: [
                    {
                        text: "确定",
                        "class": "btn",
                        click: function() {
                            if($("input[type=checkbox]:checked").length > 1)
                            {
                                POP.ShowAlert("只能选取一个")
                            }
                            else
                            {
                                //currproductionname =  $("input[type=checkbox]").prop("checked", true).attr('value')
                                tempid = $("input[type=checkbox]:checked").attr('value')
                                //console.log(tempid)
                                var info = productioninfoobj[tempid];
                                //console.log(info)
                                currproductionname = info.POrderId;
                                ProductID = info.ProductId;

                                _self.doms.productname.val(info.cName)
                                pname.val('')
                                $(this).dialog("close")
                            }
                        }
                    },
                    {
                        text: "取消",
                        "class": "btn",
                        click: function() {
                            pname.val('')
                            productbody.empty()
                            $(this).dialog("close")
                        }
                    }
                ],
                close: function() {
                    allfiled.val("")
                    productbody.empty()
                }
            })
        },

        bind: function() {
            var _self = this

            _self.doms.productname.click(function() {
                _self.doms.productchoose.dialog('open')
            
                SERVICE.sendSHR(serviceUrl,{action:'getForshort'}, function(oData){
                 console.log(oData)
                    if(oData && oData.result && oData.result.length>0){
                        _self.initCompany(oData.result)
                     }else {
                        POP.ShowAlert('出错了，未找到对应的公司名')
                    } 
                }) 
            });

            customer.change(function(){
                companyId = $('#customer :selected').val()
                console.log(companyId)
                _self.getbuildId()
            });

            building.change(function(){
                buildId = $('#building :selected').val();
                console.log(buildId);
                _self.getFloorNo();
            });

            floor.change(function(){
                floorId = $('#floor :selected').val()
                console.log(floorId)
                _self.getType();
            });

            type.change(function(){
                productType = $('#type :selected').val()
                console.log(productType);
            })

            srch.click(function() {
                var  pName = pname.val()
                console.log(pName);
                $('#productshow tbody').empty()
                SERVICE.sendSHR(serviceUrl, {action:'searchCName', companyId:companyId, buildNo:buildId, orderPO:floorId, typeId: productType,pName:pName}, function(data){
                    if(data && data.result) {
                        console.log(data.result)
                        var newcontent = ""
                        for(var i=1; i<=data.result.length; i++){
                            newcontent += '<tr><td><input type="checkbox" value=' + data.result[i-1].Id + ' id=' + data.result[i-1].cName + ' /></td>' + "<td>" + i + "</td><td>" + data.result[i-1].cName + "</td></tr>" 
                            productioninfoobj[data.result[i-1].Id] = data.result[i-1]
                        }
                        console.log(productioninfoobj)
                        $('#productshow tbody').append(newcontent)
                    }
                    else
                    {
                        POP.ShowAlert("未能查询到相关内容")
                    }
                })
            });

            _self.doms.reworkdate.change(function(){
                reworkDate = _self.doms.reworkdate.datepicker({dateFormat:'yy-mm-dd'}).val()
             });

            _self.doms.reworkcompany.change(function() {
                currreworkcompany = $('#reworkcompany :selected').val()
            });

            _self.doms.save.click(function() {
                currreworkreason = _self.doms.reworkreason.val();
                console.log(curroperator)
                SERVICE.sendSHR(serviceUrl, {action: 'createScrapProduct', companyId:companyId, buildNo:buildId, orderPO:floorId, typeId: productType,productId:ProductID,
                                             scrapDate:reworkDate, workshopId:currreworkcompany,headerId:curroperator,invoicePath:ipath,scrapAnalysis:currreworkreason, pOrderId:currproductionname}, function(data){
                    if(data.result == true){
                        POP.ShowAlert("新增报废构件成功")
                        window.location.href = '<?php $_SERVER['HTTP_HOST']?>/weixin/baofeiconfirm/view/back.php'
                    }
                    else
                    {
                        POP.ShowAlert("新增报废构件失败")
                    }
                })
            });

            _self.doms.cancel.click(function() {
                window.location.href = '<?php $_SERVER['HTTP_HOST']?>/weixin/baofeiconfirm/view/back.php'
            });

            _self.doms.uploadfile.click(function(){
                // var file = $('#invoice').prop('files');
                var file = document.getElementById('invoice').files[0];
                //var data = new FormData(file);
                var formData = new FormData();
                formData.append('invoice', file);
                SERVICE.uploadfile(uploadfileUrl, formData, function(data) {
                    if(data && (data.status == 0)){
                        ipath=data.result;
                        POP.ShowToast("单据上传成功")
                    }else{
                        POP.ShowAlert("单据上传失败")
                    }
                })
            })
            
        },

        initCompany: function(list) {
            var _self = this;
            var contents = "";
            customer.empty();
            for(var i = 0; i< list.length; i++) {
                contents += "<option value=" + list[i].CompanyId + '>' + list[i].Forshort + "</option>"
            }
            customer.append(contents)
            companyId = $('#customer :selected').val()
            console.log(companyId)
            _self.getbuildId()
    
            customer.css("display", 'inline-block')
        },
       
        getbuildId: function() {
            var _self = this
            building.empty()
            SERVICE.sendSHR(serviceUrl, {action:'getBuildNo',companyId:companyId}, function(data){
                if(data && data.result) {
                    var contents = "";
                    for(var i = 0; i < data.result.length; i++) {
                        contents += "<option value=" + data.result[i].BuildNo + '>' + data.result[i].BuildNo + "</option>";
                    }
                    building.append(contents)
                    buildId = $('#building :selected').val()
                    console.log(buildId)
                    building.css('display', 'inline-block');
                    _self.getFloorNo()
                }
                else
                {
                    POP.ShowAlert("找不对对应的楼栋号")
                }
            })
        },

        getFloorNo: function(){
            var _self = this
            floor.empty()
            SERVICE.sendSHR(serviceUrl, {action:'getFloorNo', companyId:companyId, buildNo:buildId}, function(data) {
                if(data && data.result) {
                    var contents = "";
                    for(var i = 0; i < data.result.length; i++) {
                        contents += "<option value=" + data.result[i].OrderPO + '>' + data.result[i].Floor + "</option>";
                    }
                    floor.append(contents);
                    floorId = $('#floor :selected').val()
                    console.log(floorId)
                    floor.css('display', 'inline-block');
                    _self.getType();
                }
                else
                {
                    POP.ShowAlert("找不到对应的楼层信息")
                }
           })
        },

        getType: function() {
            var _self = this
            type.empty()
            SERVICE.sendSHR(serviceUrl, {action: 'getType', companyId:companyId, buildNo:buildId, orderPO:floorId}, function(data){
                if(data && data.result) {
                    var contents = "";
                    for(var i = 0; i < data.result.length; i++) {
                        contents += "<option value=" + data.result[i].TypeId + '>' + data.result[i].TypeName + "</option>";
                    }
                    type.append(contents);
                    productType = $('#type :selected').val()
                    console.log(productType)
                    type.css('display', 'inline-block');
                }else
                {
                    POP.ShowAlert("找不到对应的构件信息")
                }
            })
        },
        

    }
  

    module.init()
})(jQuery,POP,SERVICE,undefined)

</script>

</html>

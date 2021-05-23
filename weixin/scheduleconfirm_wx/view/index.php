<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>进度跟踪</title>
        <link rel="stylesheet" href="./resource/main.css">
        <link rel="stylesheet" href="./resource/base.css">
    </head>
    <body>
        <div class="container">
            <div>
                <h1 class="header1">工程进度跟踪表</h1>
            </div>
            <div class="selection">
                <select name="company" id="forshort">
                </select>
            </div>
            <div class="buttons">
                <button id="confirm">确定</button>
            </div>
        </div>

    </body>

<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.bootcss.com/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.bootcss.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="./resource/base.js"></script>
<script type="text/javascript"> 
    
(function($, POP, SERVICE, undefined){
    var serviceUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/scheduleconfirm_wx/controller/index.php';
    var detailUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/scheduleconfirm_wx/view/detail.php?'
    var gcompanyid = -1;

    var module = {
        doms: {
            company: $('#forshort'),
            confirm: $('#confirm'),
        },

        init: function() {
            var _self = this

            _self.getcompany()
            _self.bind()
        },

        getcompany: function() {
            var _self = this
            
            SERVICE.sendSHRSync(serviceUrl, {action:"getCompanyForShort"}, function(data) {
                if(data && data.result) {
                    console.log(data)
                    var content = '<option value="-1">项目名</option>';
                    for(var i = 0; i<data.result.length; i++) {
                        content += "<option value=" + data.result[i].CompanyId + '>' + data.result[i].Forshort + "</option>"
                    }
                    _self.doms.company.append(content)
                }
            }) 
        },

        bind: function() {
            var _self = this;

            _self.doms.confirm.on('click', function() {
                if(gcompanyid != -1){
                    window.location.href = detailUrl + 'cid=' + gcompanyid + '&cname=' + gcompanyname;                    
                }else{
                    POP.ShowAlert("请先选择项目")
                }
            })

            _self.doms.company.on('change', function() {
                gcompanyid = $('#forshort :selected').val()
                gcompanyname = $('#forshort :selected').text()
            })
        }
    }

    module.init()
})(jQuery,POP,SERVICE,undefined)
</script>
</html>
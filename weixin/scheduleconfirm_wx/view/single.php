<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>构件状态</title>
    <link rel="stylesheet" href="./resource/main.css">
</head>
<body>
        <div id="back"><span> <返回 </span></div>
        <div id="tinfo">
            <h1 id="projectname"></h1>
            <div class="specify">
                    <span id="buildinfo"></span>
                    <span id="floorinfo"></span>
            </div>
            
        </div>
        <div id="selections">
            <select name="pstate" id="pstate">
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
        <div class="contents" id="specific">
                <table>
                    <tbody>
                    <tr><td>序号</td><td>构件名称</th><td>状态</td></tr>
                    </tbody>
                </table>
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
    var specifyUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/scheduleconfirm_wx/view/detail.php?';
    var status_map = ["待排产","排产中","浇筑","脱模","待入库","已入库","出货"];
    var ttypes; 
    var companyId,buildno,floorno,typeid,cname;
    var module = {
        doms: {
            pdstatus: $('#pstate'),
            companyname: $('#projectname'),
            buildinfo: $('#buildinfo'),
            floorinfo: $('#floorinfo'),
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
                var status    = $('#pstate :selected').val()
                if(status != -1)
                    _self.getstatus()
            }),

            $('#back').on("click", function() {
                 window.location.href = specifyUrl + 'cid=' + companyId + '&bno=' + buildno + '&cname=' + cname + '&ttype=' + ttypes;
                // window.location.href = specifyUrl;
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
            _self.doms.companyname.text(cname);
            _self.doms.buildinfo.text(buildno + '栋')
            _self.doms.floorinfo.text(floorno + '层')
            // _self.doms.infos.append(cname + ' ' + buildno + '栋' + floorno + '层' + '  ' + ttypes )
        },

        getstatus: function() {
            var _self = this;
            var status    = $('#pstate :selected').val()
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
            var content = '<tr><td>序号</td><td>构件名称</th><td>状态</td></tr>';

            for(var i = 0; i<items.length; i++){
                var index = parseInt(items[i][2])
                content += '<tr><td class="status">'+ (i+1) + '</td><td class="status">'+ items[i][0] + '</td><td class="status">' + status_map[index] + '</td></tr>'
            }
           
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
<?php
    /**
     * Copyright (c) 2018 JesseChen <lkchan0719@gmail.com>
     */
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="renderer" content="webkit">
  <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
  <meta name="format-detection" content="telephone=no">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black"> 

  <!-- <link rel="stylesheet" href="./resource/back.css">  -->
  <link rel="stylesheet" href="./resource/base.css">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="stylesheet" href="./resource/main.css">
</head>

<body>
    <div class="containers">
        <div class="selection">
            <select name="project" id="project">
            </select>
            <select name="building" id="building">
                <option value="-1">楼栋</option>
            </select>
            <div class="legends">
                <span class="legend waitarr">待排产</span>
                <span class="legend inarr">排产中</span>
                <span class="legend inproduce">生产中</span>
                <span class="legend produced">已生产</span>
                <span class="legend out">已发货</span>
            </div>
        </div>
        <div class="contents">
            <span id="companyId"></span>
            <div><h1 id="buildname"></h1></div>
            <div id="progressTable">
                <div class="tablechild">
                    <table id="flooridx"> </table>
                </div>
                <div class="tablechild">
                    <table id="main">
                    </table>
                </div>
                
            </div>
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
    var serviceUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/scheduleconfirm/controller/index.php';
    var specifyUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/scheduleconfirm/view/specify.php?';
    var gcompanyid = 0, gbuildno = 0, companyname='';
    var gcelllength = 100;
    var gcellappend = 25;
	var maxtypelen = 0;
	var fulltypearr = [];
    var gspecialfloorinfo = []; 
    var gfloorninfo = [];

    var module = {
        doms: {
            company:$('#project'),
            companytitle: $('#companyId'),
            building: $('#building'),
            schspecify: $('#main'),
            progresstb: $('#progressTable'),
            flooridx: $('#flooridx'),
            buildname: $('#buildname')          
        },

        init: function(){
            var _self = this;
            _self.getcompany()
            _self.bind()
            _self.checkquerystring()
            
        },
        bind: function(){
            var _self = this;
            _self.doms.company.on('change', function(){
                gcompanyid = $('#project :selected').val()
                companyname = $('#project :selected').text()
                if(gcompanyid != -1){
                    _self.doms.companytitle.text(companyname)
                    _self.getfloorno(gcompanyid);
                }
            }),

            _self.doms.building.on('change', function() {
                gbuildno = $('#building :selected').val()
                buildingname = $('#building :selected').text()
                if(gbuildno != -1) {
                    _self.doms.buildname.text(buildingname + '#栋')
                    _self.getscheduleinfo(gcompanyid, gbuildno)
                }
            })
        },

        checkquerystring: function() {
            var _self = this
            var companyId = _self.getUrlParameter('cid')
            var buildno   = _self.getUrlParameter('bno')
            var lcname    = _self.getUrlParameter('cname')

            console.log(companyId, buildno, lcname)
            if(companyId == "" || typeof companyId === "undefined"){
                return
            }

            $('#project option:contains(' + lcname + ')').each(function() {
                console.log($(this).text())
                if($(this).text() == lcname) {
                    console.log("find it")
                    $(this).attr('selected', true)
                    _self.doms.company.val(companyId).trigger('change')
                }
            })
            
            _self.getfloorno(companyId)
            $('#building option:contains(' + buildno + ')').each(function() {
                if($(this).text() == buildno) {
                    $(this).attr('selected', true)
                    _self.doms.building.val(buildno).trigger('change')
                }
            })

        },

        getcompany: function() {
            var _self = this;

            SERVICE.sendSHRSync(serviceUrl, {action:"getCompanyForShort"}, function(data) {
                if(data && data.result) {
                    console.log(data)
                    var content = '<option value="-1">项目</option>';
                    for(var i = 0; i<data.result.length; i++) {
                        content += "<option value=" + data.result[i].CompanyId + '>' + data.result[i].Forshort + "</option>"
                    }
                    _self.doms.company.append(content)
                }
            })
        },

        getfloorno: function(companyid) {
            var _self = this;

            SERVICE.sendSHRSync(serviceUrl, {action:'getCompanyBuilding', CompanyId: companyid}, function(data){
                if(data && data.result) {
                    console.log(data)
                    var content = '<option value="-1">楼栋</option>';
                    for(var i=0; i<data.result.length; i++) {
                        content += '<option value='+ data.result[i].BuildingNo + '>' + data.result[i].BuildingNo + '</option>'
                    }
                    _self.doms.building.empty()
                    _self.doms.building.append(content)
                }
            })
        },

        getscheduleinfo: function(companyid, buildno) {
            var _self = this;
            POP.ShowNotify("正在查询，请稍后...")
            SERVICE.sendSHR(serviceUrl, {action:"getProductSchedule", CompanyId:companyid, BuildingNo:buildno}, function(data){
                if(data && data.result) {
                    POP.dialog.close()
                    console.log(data.result);
                    fulltypearr.length = 0
                    gspecialfloorinfo = [];
                    _self.genscheduletable(data.result);
                    // console.log(data.result)
                } else {
                    _self.doms.schspecify.empty()
                    POP.ShowAlert("未查询到相关进度信息")
                }
            })
        },

        genscheduletable: function(pdata) {
            var _self = this;
            var detail = [];
            gfloorninfo.length = 0;

            for(var i=1; i<34;i++){
                var info = pdata.filter(function(elem) {
                    return elem[1] == i;
                })
                console.log(info)
                if(info.length != 0){
                   gfloorninfo.push(i);
                   var ret =  _self.makeupfloorinfo(info,i)
                }
                else {
                   var ret = {}
                }
                detail[i] = ret;
            }
            
            //filter special name floor
            var specialfloor = pdata.filter(function(elem){
                return isNaN(elem[1]) == true
            })

            console.log(specialfloor)
            _self.makeupspecial(specialfloor)

            console.log(detail)
            gfloorninfo.sort(function(a,b) {
                return a-b;
            });
            _self.drawtable(detail)
        },
        makeupspecial: function(sfloors) {
            var _self = this;
            var floorname = [];

            for(var i = 0; i<sfloors.length; i++) {
                if(floorname.indexOf(sfloors[i][1]) == -1)
                    floorname.push(sfloors[i][1])
            }

            for(var i = 0; i<floorname.length; i++) {
                var info = sfloors.filter(function(elem){
                    return elem[1] == floorname[i]
                })

                if(info.length != 0) {
                    var ret = _self.makeupfloorinfo(info, floorname[i])
                }else{
                    var ret = {}
                }

                gspecialfloorinfo[floorname[i]] = ret
            }

            console.log(gspecialfloorinfo)
        },

        makeupfloorinfo: function(info, floorno) {
            var obj= {info:[]}
            var ptype = []
            
            for(var i = 0; i<info.length; i++)
			{
                if(fulltypearr.indexOf(info[i][3]) == -1)
                {
                    fulltypearr.push(info[i][3])
                }

				if(ptype.indexOf(info[i][3]) == -1){
                    ptype.push(info[i][3])
                }

                obj.info[info[i][3]] = {}	
			}

            
			for(var i = 0; i<ptype.length;i++)
            {
				var idx = ptype[i]
                
                obj.info[idx].waitarr = 0;
                obj.info[idx].inarr = 0;
                obj.info[idx].inproduce = 0;
                obj.info[idx].produced = 0;
                obj.info[idx].out = 0;
                obj.info[idx].total = 0;
                obj.info[idx].nums = 0;
                obj.info[idx].floorno = 0;
                
                
            }
            for(var i = 0; i<info.length; i++){
                // {
                //     info: [
                //         {
                //             ptype: xxx,
                //             waitarr:1,
                //             xxx: xx
                //         }
                //     ]
                // }
                var amount = parseInt(info[i][0])
				var idx = info[i][3]
				
				obj.info[idx].ptype = info[i][3]
				obj.info[idx].typeid = info[i][4]
                obj.info[idx].floorno = floorno;
                

				if(info[i][2] == "0"){
					obj.info[idx].waitarr += amount
				}
				else if(info[i][2] == "1") {
					obj.info[idx].inarr += amount
				}
				else if(info[i][2] == "2" || info[i][2] == "3"){
					obj.info[idx].inproduce += amount
				}
				else if(info[i][2] == "4" || info[i][2] == "5"){
					obj.info[idx].produced += amount
				}
				else if(info[i][2] == "6"){
					obj.info[idx].out += amount
				}
				obj.info[idx].total += amount;
            }
            
            for(var i = 0; i<ptype.length;i++)
            {
				var idx = ptype[i]
                if(obj.info[idx].inarr != 0)
                    obj.info[idx].nums++
                if(obj.info[idx].waitarr != 0)
                    obj.info[idx].nums++
                if(obj.info[idx].inproduce != 0)
                    obj.info[idx].nums++
                if(obj.info[idx].produced != 0)
                    obj.info[idx].nums++
                if(obj.info[idx].out != 0)
                    obj.info[idx].nums++
            }

            console.log(obj)
            return obj;
        },

        drawtable: function(details) {
            var _self = this

            // step1: gen left floor index info
            _self.doms.flooridx.empty()
            var floortablecontent = '<tr><td class="findex"></td></tr>'
            for(var item in gspecialfloorinfo) {
                floortablecontent += '<tr><td class="findex">' + item + '层</td></tr>'
            }

            // for(var i = details.length-1; i > 0; i--){
            //     floortablecontent += '<tr><td class="findex">' + i + '</td></tr>'
            // }

            for(var i = gfloorninfo.length-1; i >= 0; i--) {
                floortablecontent += '<tr><td class="findex">' + gfloorninfo[i] + '层</td></tr>'
            }
            
            _self.doms.flooridx.append(floortablecontent)

            maxtypelen = fulltypearr.length;
            // step2: gen each floor schedule info
            // var	 tfooter = '<tr>' + ('<td></td>').repeat(maxtypelen) + '</tr><tr>'
            var tfooter = '<tr>'
            var theader = '<tr>'
            var tbody = ''

            
            for(var item in gspecialfloorinfo) {

                if(JSON.stringify(gspecialfloorinfo[item]) == '{}') {
                    tbody += '<tr>' + ('<td></td>').repeat(maxtypelen) + '</tr>'
                    continue
                }

                tbody += '<tr>'
                for(var j = 0; j < fulltypearr.length; j++){
                    var idx = fulltypearr[j];
                    console.log(idx)
                    if(JSON.stringify(gspecialfloorinfo[item].info[idx]) == "{}") {
                        tbody += '<td></td>'
                    } else {
                        if(typeof gspecialfloorinfo[item].info[idx] === 'undefined'){
                            tbody += '<td></td>'
                            continue
                        }
                        
                        var tdchild = _self.gentabletd(gspecialfloorinfo[item].info[idx])
                        tbody += '<td class="empty">' + tdchild + '</td>'
                    }
                }
                tbody += '</tr>'
            }
          

            // for(var i = details.length-1; i > 0; i--) {
            for(var k = gfloorninfo.length-1; k >= 0; k--) {
                var i = gfloorninfo[k];
                if(JSON.stringify(details[i]) == '{}') {
                    tbody += '<tr>' + ('<td></td>').repeat(maxtypelen) + '</tr>'
                    continue
                }
                
                tbody += '<tr>'
				// var typearr = details[i].typearr
                for(var j = 0; j < fulltypearr.length; j++) {
					var idx = fulltypearr[j]

                    if(JSON.stringify(details[i].info[idx]) == '{}')
                    {
                        console.log(details.info[idx])
                        tbody += '<td></td>'
                    }
                    else
                    {
                        if(typeof details[i].info[idx] === 'undefined'){
                            tbody += '<td></td>'
                            continue
                        }
                            
                        var tdchild = _self.gentabletd(details[i].info[idx])
                        tbody += '<td class="empty">' + tdchild + '</td>'
                    }
                }
                tbody += '</tr>'
            }

            console.log(tbody)
			for(var i =0;i < fulltypearr.length; i++)
			{
                tfooter += '<td class="ptypes">' + fulltypearr[i] + '</td>'
                theader += '<td class="ptypes">' + fulltypearr[i] + '</td>'
			}
            theader += '</tr>'
			tfooter += '</tr>'

            _self.doms.schspecify.empty()
            _self.doms.schspecify.append(theader + tbody + tfooter )
            _self.bindclick()

            console.log(fulltypearr)
        },

        gentabletd: function(schedule) {
            var _self = this

            var content = '';
            var left = schedule.nums;
            var appends = gcellappend/left;
            var lens = 0;
            
            if(schedule.waitarr != 0){
                lens = (schedule.waitarr/schedule.total)*gcelllength + appends;
                content += '<span id=' + schedule.typeid + ',' + schedule.floorno + ',' + schedule.ptype +' class="waitarr cell" style="width:'+ lens + 'px;">'+ schedule.waitarr +'</span>'
                if(left == 1){
                    return content   
                }
            }
            
            if(schedule.inarr != 0){
                lens = (schedule.inarr/schedule.total)*gcelllength + appends;
                content += '<span id=' + schedule.typeid + ',' + schedule.floorno + ',' + schedule.ptype + ' class="inarr cell" style="width:'+ lens + 'px;">' + schedule.inarr + '</span>'
                if(left == 1){
                    return content   
                }
            }
            
            if(schedule.inproduce != 0){
                lens = (schedule.inproduce/schedule.total)*gcelllength + appends;
                content += '<span id=' + schedule.typeid + ',' + schedule.floorno + ',' + schedule.ptype +' class="inproduce cell" style="width:'+ lens + 'px;">'+ schedule.inproduce +'</span>'
                if(left == 1){
                    return content   
                }
            }
            
            if(schedule.produced != 0) {
                lens = (schedule.produced/schedule.total)*gcelllength + appends;
                content += '<span id=' + schedule.typeid + ',' + schedule.floorno + ',' + schedule.ptype +' class="produced cell" style="width:'+ lens + 'px;">'+ schedule.produced +'</span>'
                if(left == 1){
                    return content   
                }
            }
            
            
            if(schedule.out != 0){
                lens = (schedule.out/schedule.total)*gcelllength + appends;
                content += '<span id=' + schedule.typeid + ',' + schedule.floorno + ',' + schedule.ptype +' class="out cell" style="width:'+ lens + 'px;">'+ schedule.out +'</span>'
                if(left == 1){
                    return content   
                }
            }
           
            return content;
        },
        bindclick: function() {
            $('#main td span').each(function() {
                $(this).on('click', function() {
                    var ids = $(this).attr("id")
                    var scontent = ids.split(',')
                    console.log(scontent)
                    var typeid = scontent[0]
                    var floorno = scontent[1]
                    var sptypes = scontent[2]
                   
                    window.location.href = specifyUrl + 'cid=' + gcompanyid + '&bno=' + gbuildno + '&tid=' + typeid + '&fno=' + floorno + '&cname=' + companyname + '&sptype=' + sptypes;
                    return false;
                })
            })
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

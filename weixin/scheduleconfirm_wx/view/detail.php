<?php
    /**
     * Copyright (c) 2019 JesseChen <lkchan0719@gmail.com>
     */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>进度详情</title>
    <link rel="stylesheet" href="./resource/main.css">
</head>
<body>
    <div id="back"><span> <返回 </span></div>
    <div id="titles">
        <h1 id="projectname"></h1>
        <span id="buildinfo"></span>
    </div>
    <div id="selections">
        <select name="building" id="building"></select>
        <select name="type" id="ptype"></select>

    </div>
    <div class="legends">
            <span class="legend waitarr">待排产</span>
            <span class="legend inarr">排产中</span>
            <span class="legend inproduce">生产中</span>
            <span class="legend produced">已生产</span>
            <span class="legend out">已发货</span>
    </div>
    <div class="contents">
        <table id="main">
            <tbody>
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
    var specifyUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/scheduleconfirm_wx/view/single.php?';
    var indexUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/scheduleconfirm_wx/view/index.php';
    var gcid, gcname, gbno, gtype;
    var gcelllength = 130;
    var gcellappend = 30;
	var maxtypelen = 0;
	var fulltypearr = [];
    var detail = [];
    var gbtype = "";

    var module = {
        doms: {
            back: $('#back'),
            projname: $('#projectname'),
            buildno: $('#buildinfo'),
            selbuilding: $('#building'),
            seltype: $('#ptype'),
            tblcontent: $('#main tbody')
        },

        init: function() {
            var _self = this

            _self.bind();
            _self.getcompanyinfo()
            // _self.getbuildno();
           
        },

        getcompanyinfo: function() {
            var _self = this
            gcid = _self.getUrlParameter('cid');
            gcname = _self.getUrlParameter('cname');
           
            _self.doms.projname.text(gcname);
            gbno = _self.getUrlParameter('bno')
            _self.getbuildno();
            if(gbno != "") {
                 gbtype =  _self.getUrlParameter('ttype');
                 console.log(gbtype)
                 $('#building option:contains(' + gbno + ')').each(function() {
                     if($(this).text() == gbno) {
                         console.log("find it")
                         $(this).attr('selected', true)
                         _self.doms.selbuilding.trigger('change')
                         console.log("triggered")
                     }
                 });
            }
        },

        getbuildno: function() {
            var _self = this
            
            SERVICE.sendSHRSync(serviceUrl, {action:'getCompanyBuilding', CompanyId: gcid}, function(data){
                if(data && data.result) {
                    console.log(data)
                    var content = '<option value="-1">楼栋</option>';
                    for(var i=0; i<data.result.length; i++) {
                        content += '<option value='+ data.result[i].BuildingNo + '>' + data.result[i].BuildingNo + '</option>'
                    }
                    _self.doms.selbuilding.empty()
                    _self.doms.selbuilding.append(content)
                }
            })

        },

        bind: function() {
            var _self = this

            _self.doms.selbuilding.on('change', function() {
                console.log("buidling trigger")
                gbno = $('#building :selected').val()
                console.log(gbno)
                if(gbno != -1) {
                    _self.doms.buildno.text(gbno + "栋")
                    _self.doms.tblcontent.empty()
                    _self.gentable()
                }
                else{
                    _self.doms.buildno.text("")
                }
            })

            _self.doms.seltype.on('change', function() {
                gtype = $('#ptype :selected').text()
                _self.drawtable()
            })

            _self.doms.back.on("click", function() {
                window.location.href = indexUrl;
            })
        },

        gentable: function() {
            var _self = this
            POP.ShowNotify("正在查询，请稍后...")
            SERVICE.sendSHR(serviceUrl, {action:"getProductSchedule", CompanyId:gcid, BuildingNo:gbno}, function(data){
                if(data && data.result) {
                    POP.dialog.close()
                    console.log(data.result);
                    fulltypearr.length = 0
                    gspecialfloorinfo = []
                    _self.genscheduletable(data.result);

                    if(gbtype != "") {    
                            $('#ptype option:contains(' + gbtype + ')').each(function() {
                                if($(this).text() == gbtype) {
                                console.log(gbtype)
                                $(this).attr('selected', true)
                                _self.doms.seltype.trigger('change')
                            }
                        })
                        gbtype = ""
                    }
                } else {
                    POP.ShowAlert("未查询到进度信息")
                }
            })
        },

        genscheduletable: function(pdata) {
            var _self = this;
            
            for(var i=1; i<34;i++){
                var info = pdata.filter(function(elem) {
                    return elem[1] == i;
                })
                // console.log(info)
                if(info.length != 0){
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
            _self.settype()
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
        settype: function() {
            var _self = this

            var content = '<option value="-1">构件类型</option>';
            for(var item in fulltypearr) {
                    content += '<option value='+ item + '>' + fulltypearr[item] + '</option>'
            }
            _self.doms.seltype.empty()
            _self.doms.seltype.append(content)
        },

        makeupfloorinfo: function(info, floorno) {
            var obj= {info:[]}
			var ptype = []

			for(var i = 0; i<info.length; i++)
			{
                if(fulltypearr.indexOf(info[i][3]) == -1)
                {
                    fulltypearr[info[i][4]] = info[i][3]
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
        // compare: function(fno) {
        //     return function(floor1, floor2) {
        //         var val1 = floor1[fno];
        //         var val2 = floor2[fno];
        //         return val1 - val2;
        //     }
        // },
        drawtable: function() {
            var _self = this
            //content is an array, index is the floorno from 1 to 33

            // var infos = detail.filter(function(elem) {
            //         return elem.ptype == type;
            // }).sort(_self.compare('floorno'))
            var theader='<tr><td>楼层</td><td>构件类型</th><td>进度</td></tr>'
            var tbody = ''
            for(var item in gspecialfloorinfo) {

                if(JSON.stringify(gspecialfloorinfo[item]) == '{}') {
                    continue
                }
                if(typeof gspecialfloorinfo[item].info[gtype] === 'undefined'){
                        continue
                }
                tbody += '<tr><td>'+item+'</td><td>'+ gtype+ '</td>'
                var tdchild = _self.gentabletd(gspecialfloorinfo[item].info[gtype])
                tbody += '<td class="empty">' + tdchild + '</td></tr>'
            }

            for(var i = 1; i < detail.length; i++){
                
                if(JSON.stringify(detail[i]) == '{}') {
                    continue
                }

                if(typeof detail[i].info[gtype] === 'undefined')
                    continue
                tbody += '<tr><td>'+i+'</td><td>'+ gtype+ '</td>'
                var tdchild = _self.gentabletd(detail[i].info[gtype])
                tbody += '<td class="empty">' + tdchild + '</td></tr>'
            }

            _self.doms.tblcontent.empty()
            _self.doms.tblcontent.append( theader + tbody )

            _self.bindclick()
        },

        getUrlParameter: function(sParamName, sURL) {
            var sURL = decodeURIComponent(sURL || location.search.slice(1));
            var rexUrl = new RegExp("(^|&)" + sParamName + "=([^&]*)(&|$)", "i");
            var aRes = sURL.match(rexUrl);
            return(aRes && aRes[2]) || "";
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
            $('#main tbody td span').each(function() {
                $(this).on('click', function() {
                    var ids = $(this).attr("id")
                    var scontent = ids.split(',')
                    console.log(scontent)
                    var typeid = scontent[0]
                    var floorno = scontent[1]
                    var sptypes = scontent[2]
                   
                    window.location.href = specifyUrl + 'cid=' + gcid + '&bno=' + gbno + '&tid=' + typeid + '&fno=' + floorno + '&cname=' + gcname + '&sptype=' + sptypes;
                    return false;
                })
            })
        },

    }

    module.init()
})(jQuery,POP,SERVICE,undefined)

</script>
</html>
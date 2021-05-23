<?php
/**
 * User: Elina
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
  <title>车间报表</title>
  <link rel="stylesheet" type="text/css" href="./resource/base.css">
  <link rel="stylesheet" type="text/css" href="./lib/jquery-ui-datepicker.min.css">
  <style type="text/css">
  .wrapper{padding: 20px;font-size: 14px;}
  .wrapper header{line-height: 40px}
  .wrapper article{width: 100%; overflow-y: scroll;}
  /*.wrapper article{display: none;}*/
  #search,#export{margin-left: 10px; width:100px; background-color: teal; color: white; border: none; border-radius: 3px; font-size: 13px;line-height: 23px}
  #search{margin-left: 30px;}
  #start-date{line-height: 23px; line-height: 23px; border-radius: 3px; border: solid 1px lightgray; padding-left: 10px; font-size: 13px;}
  table{width: 100%; border-width: 1px; border-color: #333; border-collapse: collapse;}
  th,td{border-width: 1px;padding: 5px 8px; border-style: solid; border-color: lightgray; min-width: 50px;}
  th{background-color: #f9f9f9;}
  #noData{text-align: center; padding: 30px 0; color: darkred; border-top: solid 1px darkgray; border-bottom: solid 1px darkgray; margin-top: 30px;}
  #cell-bottom-left{border-right: none;border-top: none}
  #cell-bottom-right{border-left: none;border-top: none}
  #cell-top{border-bottom: none}
  #export.disabled{background-color: #A9A9A9;}
  </style>
</head>
<body>
  <div class="wrapper">
    <header>
      <p>请选择起始时间: <input type="text" id="start-date">
        <button id="search">搜索</button>
        <button id="export" class="disabled" disabled="disabled">导出报表</button>
      </p>
    </header>
    <article></article>
    <div id="noData">暂无数据</div>
  </div>
  <script type="text/javascript" src="./lib/jquery-3.2.1.min.js"></script>
  <script type="text/javascript" src="./lib/jquery-ui-datepicker.min.js"></script>
  <script type="text/javascript" src="./lib/jquery.table2excel.min.js"></script>
  <script type="text/javascript" src="./resource/base.js"></script>
  <script type="text/javascript">
  (function($,POP,SERVICE,undefined){
    var serviceUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/workhourconfirm/controller/index.php';
    var module = {
      doms: {
        btnSearch:$('#search'),
        btnExport:$('#export'),
        article:$('article'),
        noData:$('#noData')
      },
      init: function(){
        $( "#start-date" ).datepicker();
        this.bind()
      },
      noDataShow:function(){
        this.doms.article.empty()
        this.doms.noData.show()
        this.doms.btnExport.attr('disabled','disabled').addClass('disabled')
      },
      uniqArray(array){
        var temp = {}, r = [], len = array.length, val, type;
        for (var i = 0; i < len; i++) {
          val = array[i];
          type = typeof val;
          if (!temp[val]) {
            temp[val] = [type];
            r.push(val);
          } else if (temp[val].indexOf(type) < 0) {
            temp[val].push(type);
            r.push(val);
          }
        }
        return r;
      },
      updatePageData: function(data){
        if(!data.workshopPlan ||!data.avgWorkdatePlan || !data.avgWorkshopPlan){
          POP.ShowAlert('未查询到数据')
          this.noDataShow()
          return false
        }

        var workshopPlan = data.workshopPlan,
          avgWorkshopPlan = {},
          datelist = [],
          sortbyline = {},
          tableHeaderHtml, lineHtml = ''

        if(workshopPlan.length == 0){
          this.noDataShow()
          return false
        }

        for(var i=0;i<workshopPlan.length;i++){
          sortbyline[workshopPlan[i].WorkShop]
          if(!sortbyline[workshopPlan[i].WorkShop]){
            sortbyline[workshopPlan[i].WorkShop] = {}
          }
          sortbyline[workshopPlan[i].WorkShop][workshopPlan[i].WorkDate] = workshopPlan[i]
          datelist.push(workshopPlan[i].WorkDate)

        }

        datelist = this.uniqArray(datelist)
        console.log(sortbyline)
        console.log(datelist)

        tableHeaderHtml = this.generateTableHeader(datelist)

        for(var i=0;i<data.avgWorkshopPlan.length;i++){
          avgWorkshopPlan[data.avgWorkshopPlan[i].WorkShop] = data.avgWorkshopPlan[i]
        }

        for(key in sortbyline){
          lineHtml += this.generateLineHtml(sortbyline[key],datelist,avgWorkshopPlan)
        }

        tableFooterHtml = this.generateTableFooter(data.avgWorkdatePlan,datelist)

        tableHtml = '<table id="plan" class="table table-condensed">' + tableHeaderHtml + lineHtml + tableFooterHtml + '</table>'
        console.log(lineHtml)
        this.doms.article.empty().append(tableHtml).show()
        this.doms.noData.hide()

        this.doms.btnExport.attr('disabled',false).removeClass('disabled')

      },
      generateTableFooter: function(data,datelist){
        var _PlanCubeHtml = '<tr><td rowspan="5">汇总</td>' + '<td>计划排产</td>',
          _FinishedCube = '<tr><td>实际完成</td>',
          _AttainmentRate = '<tr><td>达成率</td>',
          _WorkHours = '<tr><td>日总工时</td>',
          _WorkShop = '<tr><td>日总效率</td>',
          totalPlanCube = totalFinishedCube = totalAttainmentRate = totalWorkHours = totalWorkShop =0

        var i = 0
        for(key in data){
          i++
        }

        for(var j = 1;j<8;j++){
          if(j>i)
            data['9999-99-' + j] = {
              AttainmentRate: "",
              FinishedCube: "",
              PlanCube: "",
              WorkHours: "",
              WorkShop: "",
              Efficiency: ""
            }
        }

        for(key in data){
          if(data[key].PlanCube == 0)
          {
            AttainmentRates = 0.00
          }
          else
          {
            AttainmentRates = parseFloat(data[key].FinishedCube/data[key].PlanCube) * 100
          }
          
          _PlanCubeHtml += '<td>' + data[key].PlanCube+ '</td>'
          _FinishedCube += '<td>' + data[key].FinishedCube+ '</td>'
          _AttainmentRate += '<td>' + AttainmentRates.toFixed(2)+ '%</td>'
          _WorkHours += '<td>' + data[key].WorkHours+ '</td>'
          _WorkShop += '<td>' + data[key].Efficiency+ '</td>'

          totalPlanCube += data[key].PlanCube?parseFloat(data[key].PlanCube):0
          totalFinishedCube += data[key].FinishedCube?parseFloat(data[key].FinishedCube):0
          totalAttainmentRate += data[key].AttainmentRate?parseFloat(data[key].AttainmentRate):0
          totalWorkHours += data[key].WorkHours?parseFloat(data[key].WorkHours):0
          totalWorkShop += data[key].Efficiency?parseFloat(data[key].Efficiency):0
        }
        avarageWorkHours = totalWorkHours/7
        if(totalPlanCube == 0)
        {
          avarageAttainment = 0.00
        }
        else {
          avarageAttainment = parseFloat(totalFinishedCube/totalPlanCube)*100
        }
        avarageWorkShop = totalWorkShop/7
        _PlanCubeHtml += '<td>' + totalPlanCube.toFixed(2) + '</td></tr>'
        _FinishedCube += '<td>' + totalFinishedCube.toFixed(2) + '</td></tr>'
        _AttainmentRate += '<td>' + avarageAttainment.toFixed(2) + '%</td></tr>'
        _WorkHours += '<td>' + avarageWorkHours.toFixed(2) + '</td></tr>'
        _WorkShop += '<td>' + avarageWorkShop.toFixed(2) + '</td></tr>'

        return _PlanCubeHtml + _FinishedCube + _AttainmentRate + _WorkHours + _WorkShop
      },
      generateLineHtml: function(data,datelist,avgWorkshopPlan){
        console.log('generateLineHtml',data)

        var _PlanCubeHtml = '<tr><td rowspan="8">' + data[datelist[0]].WorkShop + '</td>' + '<td>计划排产</td>',
          _FinishedCube = '<tr><td>实际完成</td>',
          _AttainmentRate = '<tr><td>达成率</td>',
          _CauseAnalysis = '<tr><td>原因分析</td>',
          _WorkHours = '<tr><td>工时</td>',
          _WorkerNum = '<tr><td>人数</td>',
          _WorkDate = '<tr><td>每人平均工时</td>',
          _WorkShop = '<tr><td>效率</td>'

        var i = 0
        for(key in data){
          i++
        }

        for(var j = 1;j<8;j++){
          if(j>i)
            data['9999-99-' + j] = {
              AttainmentRate: "", //达成率
              FinishedCube: "", //实际完成
              PlanCube: "", //计划排产
              WorkDate: "",
              WorkHours: "",//工时
              WorkShop: "",
              WorkerNum: "",  //人数
              CauseAnalysis: "", //原因分析
              Efficiency: ""
            }
        }

        for(key in data){
          if(data[key].WorkerNum == 0 || data[key].WorkHours == 0){
             workdate = 0.00
          }
          else
          {
            workdate = data[key].WorkHours / data[key].WorkerNum
          }
          if(data[key].PlanCube == 0)
          {
            AttainmentRates = 0.00
          }
          else
          {
            AttainmentRates = parseFloat(data[key].FinishedCube/data[key].PlanCube) * 100
          }
          _PlanCubeHtml += '<td>' + data[key].PlanCube+ '</td>'
          _FinishedCube += '<td>' + data[key].FinishedCube+ '</td>'
          _AttainmentRate += '<td>' + AttainmentRates.toFixed(2)+ '%</td>'
          _CauseAnalysis += '<td>' + data[key].CauseAnalysis+ '</td>'
          _WorkHours += '<td>' + data[key].WorkHours+ '</td>'
          _WorkerNum += '<td>' + data[key].WorkerNum+ '</td>'
          _WorkDate += '<td>' + workdate.toFixed(2) +'</td>'
          _WorkShop += '<td>' + data[key].Efficiency + '</td>'
        }
        if(avgWorkshopPlan[data[datelist[0]].WorkShop].PlanCube == 0)
        {
          _AttainmentRates = 0.00
        }
        else
        {
          _AttainmentRates = parseFloat(avgWorkshopPlan[data[datelist[0]].WorkShop].FinishedCube/avgWorkshopPlan[data[datelist[0]].WorkShop].PlanCube)*100
        }
        _workdates = parseFloat(avgWorkshopPlan[data[datelist[0]].WorkShop].AvgHours)
        _PlanCubeHtml += '<td>' + avgWorkshopPlan[data[datelist[0]].WorkShop].PlanCube + '</td></tr>'
        _FinishedCube += '<td>' + avgWorkshopPlan[data[datelist[0]].WorkShop].FinishedCube + '</td></tr>'
        _AttainmentRate += '<td>' + _AttainmentRates.toFixed(2) + '%</td></tr>'
        _CauseAnalysis += '<td></td></tr>'
        _WorkHours += '<td>' + avgWorkshopPlan[data[datelist[0]].WorkShop].WorkHours + '</td></tr>'
        _WorkerNum += '<td>' + avgWorkshopPlan[data[datelist[0]].WorkShop].WorkerNum + '</td></tr>'
        _WorkDate += '<td>' + _workdates.toFixed(2) + '</td></tr>'
        _WorkShop += '<td>' + avgWorkshopPlan[data[datelist[0]].WorkShop].Efficiency + '</td></tr>'

        return _PlanCubeHtml + _FinishedCube + _AttainmentRate + _CauseAnalysis + _WorkHours + _WorkerNum + _WorkDate + _WorkShop
      },
      generateDateCell: function(target){
        var date = new Date(target)
          datename = new Array("日", "一", "二", "三", "四", "五", "六");
        return {
          date:target,
          datename:'星期' + datename[date.getDay()]
        }
      },
      generateTableHeader:function(datelist){
        var list = [], _html
        for(var i=0;i<7;i++){
          if(i<datelist.length)
            list.push(this.generateDateCell(datelist[i]))
          else
            list.push({
              date:'',
              datename:''
            })
        }

        // _html = '<tr><th rowspan="2" colspan="2">日期</th><th>' + list[0].date + '</th><th>' + list[1].date + '</th><th>' + list[2].date + '</th><th>' + list[3].date + '</th><th>' + list[4].date + '</th><th>' + list[5].date + '</th><th>' + list[6].date + '</th><th rowspan="2">汇总</th></tr>' +
        //   '<tr><th>' + list[0].datename + '</th><th>' + list[1].datename + '</th><th>' + list[2].datename + '</th><th>' + list[3].datename + '</th><th>' + list[4].datename + '</th><th>' + list[5].datename + '</th><th>' + list[6].datename + '</th>'

        _html = '<tr><th colspan="2" id="cell-top">日期</th><th>' + list[0].date + '</th><th>' + list[1].date + '</th><th>' + list[2].date + '</th><th>' + list[3].date + '</th><th>' + list[4].date + '</th><th>' + list[5].date + '</th><th>' + list[6].date + '</th><th rowspan="2">汇总</th></tr>' +
          '<tr><th id="cell-bottom-left"></th><th id="cell-bottom-right"></th><th>' + list[0].datename + '</th><th>' + list[1].datename + '</th><th>' + list[2].datename + '</th><th>' + list[3].datename + '</th><th>' + list[4].datename + '</th><th>' + list[5].datename + '</th><th>' + list[6].datename + '</th>'
        
        return _html

      },
      retrievePageData:function(date){
        var _self = this
		POP.ShowNotify("正在查询，请稍等...")
        SERVICE.sendSHR(serviceUrl,{action:'getWorkPlan',startDate:date}, function(oData){
		  POP.dialog.close()
          if(oData.result){
            _self.updatePageData(oData.result)
           }else{
            _self.noDataShow()
		   }
        })
      },
      formatDate: function(date){
        if(!date) date = new Date()
        return date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate()
      },
      bind: function(){
        var _self = this

        _self.doms.btnSearch.on('click', function(){
          var date = $( "#start-date" ).datepicker({dateFormat:'yy-mm-dd'}).val()
          console.log(date)
          _self.retrievePageData(date)
        })

        _self.doms.btnExport.on('click', function(){
          $("#plan").table2excel({
            exclude  : ".noExl", //过滤位置的 css 类名
            filename : "车间计划" + new Date().getTime() + ".xls", //文件名称
            name: "Excel Document Name.xlsx",
            exclude_img: true,
            exclude_links: true,
            exclude_inputs: true
          });
        })
      }

    }
    module.init()
  })(jQuery,POP,SERVICE)
  </script>
</body>
</html>


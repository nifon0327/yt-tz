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
  <link rel="stylesheet" type="text/css" href="./resource/base.css">
  <link rel="stylesheet" type="text/css" href="./resource/LCalendar.css">
  <link rel="stylesheet" type="text/css" href="./resource/mobileSelect.css">
  <title>公时人数填报</title>
  <style type="text/css">
    .wrapper{padding:0 5% 70px;}
    header{line-height: 30px; font-size: 14px; height: 50px; text-align: right; color: teal;padding-top: 20px}
    .criteria {line-height: 40px;}
    .criteria input, #line,.criteria textarea{border: solid 1px lightgray;display: inline-block;width: 50%;vertical-align: middle;padding:3px 5px; line-height: 20px; border-radius: 2px; -webkit-appearance: none;font-size:14px;}
    article{display: block;}
    #line{display: none; height: 20px;}
    footer{display:block; position: fixed; bottom: 0; text-align: center; background-color:white; height: 50px; border-top: solid 1px gray; width:100%; padding-top: 10px;}
    footer button{width:60%; border: none; border-radius:3px; padding:8px; font-size:16px; background-color:teal; color:white;}
    footer button.disabled{background-color: #A9A9A9;}
  </style>
</head>
<body>
  <div class="wrapper">
    <header>
      <div>当前操作人:<span id="openid"></span></div>
    </header>
    <nav>
      <div class="criteria">
        <label>当前产线</label>
        <div id="line"></div> 
      </div>
      <div class="criteria">
        <label>选择日期</label>
        <input type="text" name="start_date" id="start_date" placeholder="请选择日期" readonly=""/>
      </div>
      <div class="criteria">
        <label>计划排产</label>
        <input type="text" name="" id="plancube" placeholder="" readonly="readonly" />
      </div>
      <div class="criteria">
        <label>实际完成</label>
        <input type="text" name="" id="finishedcube" placeholder="" readonly="readonly" />
      </div>
      <div class="criteria">
        <label>达成率</label>
        <input type="text" name="" id="attrate" placeholder="" readonly="readonly" />
      </div>
      <div class="criteria">
        <label>当日工时</label>
        <input type="text" name="" id="hour" placeholder="" disabled="disabled" />
      </div>
      <div class="criteria">
        <label>当日人数</label>
        <input type="text" name="" id="people" placeholder="" disabled="disabled" />
      </div>
      <div class="criteria">
        <label>达成率原因分析</label>
        <textarea rows="5" id="reason" disabled="disabled"></textarea>
      </div>
    </nav>
  </div>
  <footer><button disabled="disabled" class="disabled" id="submit">提交</button></footer>
<script src="./lib/jquery.js"></script>
<script src="./resource/LCalendar.js"></script>
<script src="./resource/mobileSelect.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script type="text/javascript" src="./resource/base.js"></script>
<script type="text/javascript">
(function($,POP,SERVICE,undefined){
  var lineSelector, lineSelected, dateSelected,
      openid='<?php echo $_SESSION["openid"];?>', 
      serviceUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/workhourconfirm/controller/index.php'

  var module = {
    doms: {
      lineSelectorCtn:$('#line'),
      btnSubmit:$('#submit'),
      inputHour:$('#hour'),
      inputPeople:$('#people'),
      showplan:$('#plancube'),
	    showfinishcube:$('#finishedcube'),
	    showrate: $('#attrate'),
      inputReason:$('#reason')
    },
    init: function(){
      this.initDateSelector()
      this.bind()
      this.getUserName()
      this.retrieveLineOptions()
    },
    getUserName:function(){
      var _self = this
      SERVICE.sendSHR(serviceUrl,{action:'getUserName'}, function(oData){
        if(oData.result && oData.result.length>0)
          $('#openid').html(oData.result[0].uName)
      })
    },
    initDateSelector: function(){
      var calendar = new LCalendar(), _self = this;
      calendar.init({
        'trigger': '#start_date',
        'type': 'date',
        'minDate': (new Date().getFullYear()-1) + '-' + 1 + '-' + 1, //最小日期
        'maxDate': (new Date().getFullYear()+20) + '-' + 12 + '-' + 31, //最大日期
        selectCB: function(value){
          dateSelected = value
          if(lineSelected)
            _self.retrievePageData()
          else
            POP.ShowAlert('您还未选择产线')
        }
      });
    },
    updatePageData:function(data){
      if(data && data.length>0){
        this.doms.inputHour.val(data[0].WorkHours)
        this.doms.inputPeople.val(data[0].WorkerNum)
        this.doms.inputReason.val(data[0].CauseAnalysis)
        this.doms.showplan.val(data[0].PlanCube)
        this.doms.showfinishcube.val(data[0].FinishedCube)
        this.doms.showrate.val(data[0].AttainmentRate)
      } else {
        this.doms.inputHour.val('')
        this.doms.inputPeople.val('')
        this.doms.inputReason.val('')
        this.doms.showplan.val('')
        this.doms.showfinishcube.val('')
        this.doms.showrate.val('')
      }
      this.doms.inputHour.attr('disabled',false).removeClass('disabled')
      this.doms.inputPeople.attr('disabled',false).removeClass('disabled')
      this.doms.inputReason.attr('disabled',false).removeClass('disabled')
      this.doms.btnSubmit.attr('disabled',false).removeClass('disabled')
    },
    retrievePageData: function(){
      var _self = this
      SERVICE.sendSHR(serviceUrl,{action:'getWorkHourInfo',workdate:dateSelected,workshopId:lineSelected[0].Id}, function(oData){
        console.log(oData)
        _self.updatePageData(oData.result)
      })
    },
    initLineSelector: function(list){
        var _self = this
        orderIdSelect = new MobileSelect({
          trigger: '#line',
          title: '出货单号',
          wheels: [{data:list}],
          keyMap: {
            id:'Id',
            value: 'Name',
          },
          callback:function(indexArr, data){
            lineSelected = data
            if(dateSelected)
              _self.retrievePageData()
          }
        });
        this.doms.lineSelectorCtn.css('display','inline-block')
    },
    retrieveLineOptions:function(){
      var _self = this
        SERVICE.sendSHR(serviceUrl,{action:'getWorkShop'}, function(oData){
          console.log(oData)
          if(oData && oData.result && oData.result.length>0){
            _self.initLineSelector(oData.result)
          } else {
            POP.ShowAlert('出错了，未获取到产线信息')
          }
        })
    },
    bind:function(){
      var _self = this

      _self.doms.btnSubmit.on('click', function(){
        var hour = $('#hour').val(),
            people = $('#people').val(),
            reason = $('#reason').val()
        console.log(hour,people,reason)

        if(!lineSelected){
          POP.ShowAlert('请先选择产线信息')
        } else if(!dateSelected){
          POP.ShowAlert('请先选择日期')
        } else if(!hour){
          POP.ShowAlert('请填写工时信息')
        } else if(!people){
          POP.ShowAlert('请填写人数信息')
        } else if(!reason){
          POP.ShowAlert('请填写原因分析')
        } else {
          POP.ShowConfirm('请确认是否提交', '确定', '取消', function(){
            SERVICE.sendSHR(serviceUrl,{
              action:'updateWorkHourInfo',
              workdate:dateSelected,
              workshopId:lineSelected[0].Id,
              workHours:hour,
              workerNum:people,
              causeAnalysis:reason
            }, function(oData){
              console.log(oData)
              if(oData && oData.result){
                POP.ShowAlert('提交成功')
              } else {
                POP.ShowAlert('提交失败，请稍后再试')
              }
            })
          })
        }
      })
    }

}
module.init()
})(jQuery,POP,SERVICE,undefined)
</script>
</body>
</html>

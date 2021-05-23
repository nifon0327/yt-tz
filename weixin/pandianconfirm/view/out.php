<?php
/**
 * User: Elina
 * Date: 2018/12/10
 */
session_start();
error_reporting(E_ALL & ~E_NOTICE);

include_once('../../auth.php');
include_once('../../configure.php');
include_once('../../log.php');
include_once('../../jsapi.php');
include '../config/dbconnect.php';

spl_autoload_register(function ($class_name) {
    require_once $class_name . '.php';
});

$auth = new auth();
$query = "select u.Number, s.name from usertable u 
  left join wx_token w on w.openid = u.openid 
  left join staffmain s on u.number = s.number where w.openid='$_SESSION[openid]'";
$cursor = mysql_query($query);

if(true){
  $user_id = $row[0];
  $user_name = $row[1];
  $_SESSION['user_id'] = $user_id;
  $_SESSION['user_name'] = $user_name;
  $js_sdk = new jsapi();
  $sign = $js_sdk->get_sign();
  $hour = date('H');
  $grace = $hour<6?'凌晨':($hour<11?'早上':($hour<13?'中午':($hour<17?'下午':'晚上')));
}else{
  //usertable 的openid 绑定系统及微信用户，如没绑定，则转到信息页
  $result = 0;
  $title = '权限缺失';
  $msg = '您无此功能权限，如有疑问，请联系信息部人员</br>电话：13775147477';
  header("Location:msg.php?result=$result&title=$title&msg=$msg&onscan=1");
}
$db = new DbConnect();
$userArray = $db->get_user_name($_GET["stackId"],$_SESSION["openid"]);
$_SESSION["creator"] = $userArray["creator"];
$_SESSION["doubleCheckUser"] = $userArray["doubleCheckUser"];
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
  <link rel="stylesheet" type="text/css" href="./resource/bootstrap-table-origin.css">
  <link rel="stylesheet" type="text/css" href="./resource/base.css">
  <link rel="stylesheet" type="text/css" href="./resource/LCalendar.css">
  <link rel="stylesheet" type="text/css" href="./resource/mobileSelect.css">
  <title>盘点</title>
  <style type="text/css">
    .wrapper{padding:0 5% 70px;}
    header{line-height: 30px; font-size: 14px; height: 50px; text-align: right; color: teal;padding-top: 20px}
    .criteria {line-height: 40px;}
    .criteria input, #orderId{border: solid 1px lightgray;display: inline-block;width: 50%;vertical-align: middle;padding:3px 5px;height: 20px; line-height: 20px; border-radius: 2px; -webkit-appearance: none;font-size:14px;}
    article{display: block;}
    #orderId{display: none;}
    #car-plate{min-height: 18px; min-width: 65px; color: brown; background-color: yellow; text-align: center;}
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
        <label>日期</label>
          <input type="text" name="start_date" id="start_date" placeholder="请选择日期" readonly=""/>
        </div>
        <div class="criteria">
          <label>出货单号</label>
          <div id="orderId"></div> 
        </div>
      </nav>
    <article>
      <div id="table-1">
        <table class="table table-striped table-condensed">
          <tr><th>出货单号</th><th>出货日期</th><th>运输车辆</th><th>运货信息</th></tr>
        </table>
      </div>
      <div id="table-2"></div>
    </article>
  </div>
  <footer><button disabled="disabled" class="disabled" id="out">出货</button></footer>
<script src="./resource/jquery.min.js"></script>
<script src="./resource/LCalendar.js"></script>
<script src="./resource/mobileSelect.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script type="text/javascript" src="./resource/base.js"></script>
<script type="text/javascript">
(function($,POP,SERVICE,undefined){
  var orderIdSelect, carPlateSelect, carPlateOptions = null,
      carNo,dateTime,invoiceNo,
      openid='<?php echo $_SESSION["openid"];?>', 
      serviceUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/pandianconfirm/controller/index.php'

  var module = {
    doms: {
      orderSelector:$('#orderId'),
      table1:$('#table-1'),
      table2:$('#table-2'),
      btnOut:$('#out')
    },
    init: function(){
      var _self = this
      SERVICE.sendSHR(serviceUrl,{action:'apiauth',tag:2}, function(oData){
        if(oData.result == true){
            _self.retrieveCarOptions()
            _self.initDateSelector()
            _self.initOrderSelector()
            _self.bind()
            _self.getUserName()
        } else {
            POP.ShowAlert('您无此功能权限，如有疑问，请联系信息部人员，电话：13775147477')
        }
      })
    },
    getUserName:function(){
      var _self = this
      SERVICE.sendSHR(serviceUrl,{action:'getOperatorName'}, function(oData){
        if(oData.result && oData.result.length>0)
          $('#openid').html(oData.result[0].uName)
      })
    },
    initDateSelector: function(){
      var calendar = new LCalendar(), _self = this;
      calendar.init({
        'trigger': '#start_date',
        'type': 'date',
        'minDate': (new Date().getFullYear()) + '-' + 1 + '-' + 1, //最小日期
        'maxDate': (new Date().getFullYear()+1) + '-' + 12 + '-' + 31, //最大日期
        selectCB: function(value){
          dateTime = value;
          invoiceNo = null;
          carNo = null;
          _self.doms.btnOut.attr('disabled','disabled').addClass('disabled')
          SERVICE.sendSHR(serviceUrl,{action:'getInvoiceNoByDate',currentDate:value}, function(oData){
            if(oData && oData.result && oData.result.length>0){
              _self.updateOrderSelector(oData.result)
            } else {
              POP.ShowAlert('未查询到当日出货单')
              _self.updateOrderSelector()
            }
          })
        }
      });
    },
    updateOrderSelector: function(list){
        var newData = []
        if(list && list.length>0){
            for(var i=0;i<list.length;i++){
                newData.push(list[i].InvoiceNO)
            }
        } else {
            newData.push('未查询到当日出货单')
        }
        
        if(orderIdSelect){
            orderIdSelect.updateWheel(0, newData);
            orderIdSelect.curValue = null;
            orderIdSelect.curIndexArr = [];
            orderIdSelect.trigger.innerHTML = '';
            this.doms.orderSelector.css('display','inline-block')
        }
    },
    updatePage: function(list){
        var _el_table1 = $('<table class="table table-striped table-condensed"><tr><th>出货单号</th><th>出货日期</th><th>运输车辆</th><th>运货信息</th></tr></table>'),
            _el_table2 = $('<table class="table table-striped table-condensed"><tr><th>序号</th><th>项目</th><th>构件</th></tr></table>'),
            _list_html = '';
        if(list && list.length > 0){
            _el_table1.append('<tr><td>' + list[0].InvoiceNO + '</td><td>' + list[0].Date + '</td><td><div id="car-plate"></div></td><td>' + list[0].Wise + '</td></tr>')

            for(var i =0; i<list.length;i++){
                _list_html += '<tr><td>' + (i+1) + '</td><td>' + list[i].Forshort + '</td><td>' + list[i].cName + '</td></tr>'
            }
            _el_table2.append(_list_html)
           
        }
        this.doms.table1.empty().append(_el_table1)
        this.doms.table2.empty().append(_el_table2)

        this.initCarSelector()
    },
    initCarSelector: function(){
        var _self = this
        carPlateSelect = new MobileSelect({
          trigger: '#car-plate',
          title: '运输车辆',
          wheels: [{data:carPlateOptions}],
          keyMap: {
            id:'Id',
            value: 'CarNo',
          },
          callback:function(indexArr, data){
            // _self.retrieveInvoiceDetail(data[0])
            console.log('initCarSelector', indexArr, data)
            if(data.length>0){
                carNo = data[0].CarNo
                _self.doms.btnOut.attr('disabled',false).removeClass('disabled')
            }

          }
        });

        carPlateSelect.trigger.innerHTML = '请点击选择运输车辆';
    },
    retrieveCarOptions:function(){
        SERVICE.sendSHR(serviceUrl,{action:'getCarNo'}, function(oData){
          console.log(oData)
          if(oData && oData.result && oData.result.length>0){
            carPlateOptions = oData.result
          } else {
            POP.ShowAlert('出错了，未获取到车辆信息')
          }
        })
    },
    retrieveInvoiceDetail: function(num){
        var _self = this
        carNo = null;
        _self.doms.btnOut.attr('disabled','disabled').addClass('disabled')

        if(num === '未查询到当日出货单') {
            invoiceNo = null;
            return false
        }

        invoiceNo = num;
        SERVICE.sendSHR(serviceUrl,{action:'getInvoiceInfo',invoiceNo:num}, function(oData){
          console.log(oData)
          if(oData && oData.result && oData.result.length>0){
            _self.updatePage(oData.result)
          } else {
            POP.ShowAlert('未找到当前出货单信息')
          }
        })
    },
    initOrderSelector: function(){
        var _self = this
        orderIdSelect = new MobileSelect({
          trigger: '#orderId',
          title: '出货单号',
          wheels: [{data:['未查询到当日出货单']}],
          callback:function(indexArr, data){
            _self.retrieveInvoiceDetail(data[0])
          }
        });
    },
    refreshPage: function(){
        var _self = this
        invoiceNo = null;
        carNo = null;
        _self.doms.btnOut.attr('disabled','disabled').addClass('disabled')
        SERVICE.sendSHR(serviceUrl,{action:'getInvoiceNoByDate',currentDate:dateTime}, function(oData){
            if(oData && oData.result && oData.result.length>0){
                _self.updateOrderSelector(oData.result)
            } else {
                _self.updateOrderSelector()
            }
        })
        var _html = '<table class="table table-striped table-condensed"><tr><th>出货单号</th><th>出货日期</th><th>运输车辆</th><th>运货信息</th></tr></table>'
        this.doms.table1.empty().append(_html)
        this.doms.table2.empty()
    },
    updateInvoiceEstate: function(){
        var _self = this
        SERVICE.sendSHR(serviceUrl,{action:'updateInvoiceEstate',opDatetime:dateTime,invoiceNo:invoiceNo,carNo:carNo}, function(oData){
          if(oData.status == 0 && oData.msg == '成功'){
            POP.ShowAlert('出货成功')
            _self.refreshPage()
          } else {
            POP.ShowAlert(oData.msg || '出错了')
          }
        })
    },
    bind:function(){
        var _self = this
        _self.doms.btnOut.on('click', function(){
            if(carNo && dateTime && invoiceNo){
                POP.ShowConfirm('请确认是否出货', '确定', '取消', function(){
                    _self.updateInvoiceEstate()
                })
            } else {
                POP.ShowAlert('请先选择车辆信息')
            }
        })
    }

}
module.init()
})(jQuery,POP,SERVICE,undefined)
</script>
</body>
</html>

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

$auth   = new auth();
$query  = "select u.Number, s.name from usertable u
  left join wx_token w on w.openid = u.openid
  left join staffmain s on u.number = s.number where w.openid='$_SESSION[openid]'";
$cursor = mysql_query($query);
$row    = mysql_fetch_row($cursor);

if (true) {
    $user_id               = $row[0];
    $user_name             = $row[1];
    $_SESSION['user_id']   = $user_id;
    $_SESSION['user_name'] = $user_name;
    $js_sdk                = new jsapi();
    $sign                  = $js_sdk->get_sign();
    $hour                  = date('H');
    $grace                 = $hour < 6 ? '凌晨' : ($hour < 11 ? '早上' : ($hour < 13 ? '中午' : ($hour < 17 ? '下午' : '晚上')));
} else {
    //usertable 的openid 绑定系统及微信用户，如没绑定，则转到信息页
    $result = 0;
    $title  = '权限缺失';
    $msg    = '您无此功能权限，如有疑问，请联系信息部人员</br>电话：15919701518';
    header("Location:msg.php?result=$result&title=$title&msg=$msg&onscan=1");
}
// $db = new DbConnect();
// $userArray = $db->get_user_name($_GET["stackId"],$_SESSION["openid"]);
// $_SESSION["creator"] = $userArray["creator"];
// $_SESSION["doubleCheckUser"] = $userArray["doubleCheckUser"];

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
    <meta name="viewport"
          content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="stylesheet" type="text/css" href="./resource/bootstrap-table-origin.css">
    <link rel="stylesheet" type="text/css" href="./resource/base.css">
    <link rel="stylesheet" type="text/css" href="./resource/LCalendar.css">
    <link rel="stylesheet" type="text/css" href="./resource/mobileSelect.css">
    <title>出货</title>
    <style type="text/css">
        .wrapper {
            padding: 0 5% 70px;
        }

        header {
            line-height: 30px;
            font-size: 14px;
            height: 50px;
            text-align: right;
            color: teal;
            padding-top: 20px
        }

        .criteria {
            line-height: 40px;
        }

        .criteria input, #orderId {
            border: solid 1px lightgray;
            display: inline-block;
            width: 50%;
            vertical-align: middle;
            padding: 3px 5px;
            height: 20px;
            line-height: 20px;
            border-radius: 2px;
            -webkit-appearance: none;
            font-size: 14px;
        }

        .criteria button {
            border: none;
            border-radius: 3px;
            padding: 3px 10px;
            font-size: 14px;
            background-color: teal;
            color: white;
            vertical-align: middle;
        }

        .criteria button.sub {
            margin-left: 5px;
        }

        article {
            display: block;
        }

        #orderId {
            display: none;
        }

        #car-plate {
            min-height: 18px;
            min-width: 65px;
            color: brown;
            background-color: yellow;
            text-align: center;
        }

        footer {
            display: block;
            position: fixed;
            bottom: 0;
            text-align: center;
            background-color: white;
            height: 50px;
            border-top: solid 1px gray;
            width: 100%;
            padding-top: 10px;
        }

        footer button {
            width: 30%;
            border: none;
            border-radius: 3px;
            padding: 8px;
            font-size: 16px;
            background-color: teal;
            color: white;
        }

        footer button.disabled {
            background-color: #A9A9A9;
        }

        .text-right {
            text-align: right;
        }

        ul, li {
            list-style: none;
        }

        .slide-box {
            margin-top: 20px;
            display: -webkit-box;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .slide-item {
            width: 300px;
            height: 300px;
            border: 2px solid #ccc;
            margin-right: 30px;
        }

        .slide-box::-webkit-scrollbar {
            display: none;
        }

        /* .imgs { max-width:90%; margin:auto; margin-bottom:25px;} */
        .imgs {
            width: 300px;
            height: 300px;
        }

        #imageurl {
            text-decoration: underline;
            color: #1187e4;
        }

        #closepics {
            border-color: #eb5757;
            background-color: #eb5757;
            border-radius: 5px;
            color: #fff;
            height: 24px;
            width: 100px;
            padding: 0 10px;
            font-size: 12px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <header>
        <div>当前操作人:<span id="openid"></span></div>
    </header>
    <nav>
        <div class="criteria">
            <label for="for_short">项目名称</label>
            <input type="text" id="forshort_value" placeholder="清选择项目名称" readonly="true">
        </div>
        <div class="criteria">
            <label>日期</label>
            <input type="text" id="date_value" placeholder="请选择日期" readonly="true"/>
        </div>
        <div class="criteria">
            <label>出货单号</label>
            <div id="orderId"></div>
        </div>
        <!-- <div class="criteria">
          <label>工字钢</label>
          <input type="text" name="" placeholder="请输入工字钢数量" id="GZG">
        </div>
        <div class="criteria">
          <label>木方</label>
          <input type="text" name="" placeholder="请输入木方数量" id="MF">
        </div> -->
        <div class="criteria">
            <button id="car-create">新增车辆信息</button>
        </div>
        <div class="criteria" id="car-create-ctn" style="display: none;">
            <input type="text" name="" placeholder="请输入新增车牌" id="car-create-plate">
            <button id="car-create-ok" class="sub">确定</button>
            <button id="car-create-cancel" class="sub">取消</button>
        </div>
    </nav>
    <article>
        <div id="table-1">
            <table class="table table-striped table-condensed">
                <tr>
                    <th>出货单号</th>
                    <th>出货日期</th>
                    <th>运输车辆</th>
                    <th>运货信息</th>
                    <th>照片</th>
                </tr>
            </table>
        </div>
        <div id="table-2"></div>
    </article>
    <!-- <div><span id="imageurl">dianji</span></div> -->
    <div id="hidebox">
        <ul class="slide-box" id="slidebox"></ul>
        <button id="closepics">关闭</button>
    </div>
    <footer>
        <input type="file" name="images[]" accept="image/*" id="upload" style="display:none" multiple/>
        <button id="updatepicform">上传照片</button>
        <button disabled="disabled" class="disabled" id="out">出货</button>
    </footer>
    <script src="./resource/jquery.min.js"></script>
    <script src="./resource/LCalendar.js"></script>
    <script src="./resource/mobileSelect.js"></script>
    <script type="text/javascript" src="./resource/base.js"></script>
    <script type="text/javascript">
        (function ($, POP, SERVICE, undefined) {
            var orderIdSelect, carPlateSelect, carPlateOptions, dateSelect, ForshortSelect = null,
                carNo, dateTime, invoiceNo, CompanyId = [],
                imageurl, imagelocalids,
                openid = '<?php echo $_SESSION["openid"];?>',
                accesstoken = '<?php echo $Access_Token; ?>',
                serviceUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/chuhuoconfirm/controller/index.php',
                uploadfileUrl = '<?php $_SERVER['HTTP_HOST']?>/weixin/chuhuoconfirm/controller/upload.php',
                picurl = '<?php $_SERVER['HTTP_HOST']?>/weixin/chuhuoconfirm/view/';

            var uimages, imghref = '', nimages = 0;

            var module = {
                doms: {
                    orderSelector: $('#orderId'),
                    ForshortValue: $('#forshort_value'),
                    DateValue: $('#date_value'),
                    table1: $('#table-1'),
                    table2: $('#table-2'),
                    btnOut: $('#out'),
                    picupform: $('#updatepicform'),
                    upload: $('#upload'),
                    carCreate: $('#car-create'),
                    carCreateCtn: $('#car-create-ctn'),
                    slideBox: $('#slidebox'),
                    hidebox: $('#hidebox'),
                    closepics: $('#closepics')
                },
                init: function () {
                    var _self = this

                    _self.doms.hidebox.hide()
                    SERVICE.sendSHR(serviceUrl, {action: 'apiauth', tag: 2}, function (oData) {
                        if (oData.result == true) {
                            _self.getForshort();
                            _self.retrieveCarOptions()
                            _self.initOrderSelector()
                            _self.bind()
                            _self.getUserName()
                        } else {
                            POP.ShowAlert('您无此功能权限，如有疑问，请联系信息部人员，电话：15919701518')
                        }

                    })
                },
                getForshort: function () {
                    var _self = this
                    SERVICE.sendSHR(serviceUrl, {action: 'getForshort'}, function (oData) {
                        if (oData.result && oData.result.length > 0)
                            _self.initForshortSelector(oData.result);


                    })
                },
                getUserName: function () {
                    var _self = this
                    SERVICE.sendSHR(serviceUrl, {action: 'getOperatorName'}, function (oData) {
                        if (oData.result && oData.result.length > 0)
                            $('#openid').html(oData.result[0].uName)
                    })
                },
                updateOrderSelector: function (list) {
                    var newData = []
                    if (list && list.length > 0) {
                        for (var i = 0; i < list.length; i++) {
                            newData.push(list[i].InvoiceNO)
                        }
                    } else {
                        newData.push('未查询到当日出货单')
                    }

                    if (orderIdSelect) {
                        orderIdSelect.updateWheel(0, newData);
                        orderIdSelect.curValue = null;
                        orderIdSelect.curIndexArr = [];
                        orderIdSelect.trigger.innerHTML = '';
                        this.doms.orderSelector.css('display', 'inline-block')
                    }
                },
                updatePage: function (list) {
                    var _el_table1 = $('<table class="table table-striped table-condensed"><tr><th>出货单号</th><th>出货日期</th><th>运输车辆</th><th>运货信息</th><th>照片</th></tr></table>'),
                        _el_table2 = $('<table class="table table-striped table-condensed"><tr><th>序号</th><th>项目</th><th>构件</th></tr></table>'),
                        _list_html = '';
                    if (list && list.length > 0) {
                        _el_table1.append('<tr><td>' + list[0].InvoiceNO + '</td><td>' + list[0].Date + '</td><td><div id="car-plate"></div></td><td>' + list[0].Wise + '</td><td><span id="imageurl"></span></td></tr>')

                        for (var i = 0; i < list.length; i++) {
                            _list_html += '<tr><td>' + (i + 1) + '</td><td>' + list[i].Forshort + '</td><td>' + list[i].cName + '</td></tr>'
                        }
                        _el_table2.append(_list_html)

                    }
                    this.doms.table1.empty().append(_el_table1)
                    this.doms.table2.empty().append(_el_table2)

                    this.initCarSelector()
                },
                initCarSelector: function () {
                    var _self = this
                    carPlateSelect = new MobileSelect({
                        trigger: '#car-plate',
                        title: '运输车辆',
                        wheels: [{data: carPlateOptions}],
                        keyMap: {
                            id: 'Id',
                            value: 'CarNo',
                        },
                        callback: function (indexArr, data) {
                            if (data.length > 0) {
                                carNo = data[0].CarNo
                            }
                        }
                    });
                    carPlateSelect.trigger.innerHTML = '请点击选择运输车辆';
                },
                retrieveCarOptions: function (plate) {
                    var _self = this
                    SERVICE.sendSHR(serviceUrl, {action: 'getCarNo'}, function (oData) {
                        if (oData && oData.result && oData.result.length > 0) {
                            carPlateOptions = oData.result
                            if (plate && carPlateSelect) {
                                carPlateSelect.updateWheel(0, carPlateOptions);
                                carPlateSelect.curValue = null;
                                carPlateSelect.curIndexArr = [];
                                carPlateSelect.trigger.innerHTML = plate;
                                carNo = plate
                            }
                        } else {
                            POP.ShowAlert('出错了，未获取到车辆信息')
                        }
                    })
                },
                retrieveInvoiceDetail: function (num) {
                    var _self = this
                    carNo = null;
                    _self.doms.btnOut.attr('disabled', 'disabled').addClass('disabled')
                    if (num === '未查询到当日出货单') {
                        invoiceNo = null;
                        return false
                    }

                    invoiceNo = num;
                    SERVICE.sendSHR(serviceUrl, {action: 'getInvoiceInfo', invoiceNo: num}, function (oData) {
                        console.log(oData)
                        if (oData && oData.result && oData.result.length > 0) {
                            _self.updatePage(oData.result)
                        } else {
                            POP.ShowAlert('未找到当前出货单信息')
                        }
                    })
                },
                initForshortSelector: function (data) {
                    var _self = this
                    ForshortSelect = new MobileSelect({
                        trigger: '#forshort_value',
                        title: '项目名称',
                        wheels: [{data: data}],
                        keyMap: {
                            id: 'CompanyId',
                            value: 'Forshort',
                        },
                        callback: function (indexArr, data) {
                            // 调用日期函数带参数
                            _self.doms.ForshortValue.val(data[0].Forshort);
                            // 传参
                            CompanyId = data[0].CompanyId;
                            _self.getDate(data[0].CompanyId);

                        }
                    });
                },
                // 获取时间参数
                getDate(CompanyId) {
                    let _self = this;
                    SERVICE.sendSHR(serviceUrl, {action: 'getDate', CompanyId: CompanyId}, function (oData) {
                        if (oData && oData.result && oData.result.length > 0) {
                            _self.initDateSelector(oData.result)
                        } else {
                            POP.ShowAlert('未找到当前项目出货日期')
                        }
                    })
                },
                initDateSelector: function (data) {
                    let _self = this
                    dateSelect = new MobileSelect({
                        trigger: '#date_value',
                        title: '日期',
                        wheels: [{data: data}],
                        callback: function (indexArr, data) {
                            // 调用接口获取出货单号
                            dateTime = data[0];
                            _self.doms.DateValue.val(dateTime);
                            SERVICE.sendSHR(serviceUrl, {
                                action: 'getInvoiceNoByDate',
                                CompanyId: CompanyId,
                                currentDate: dateTime
                            }, function (oData) {
                                if (oData && oData.result && oData.result.length > 0) {
                                    _self.updateOrderSelector(oData.result)
                                } else {
                                    POP.ShowAlert('未查询到当日出货单')
                                    _self.updateOrderSelector()
                                }
                            })

                        }
                    });
                },
                initOrderSelector: function () {
                    var _self = this
                    orderIdSelect = new MobileSelect({
                        trigger: '#orderId',
                        title: '出货单号',
                        wheels: [{data: ['未查询到当日出货单']}],
                        callback: function (indexArr, data) {
                            _self.retrieveInvoiceDetail(data[0])
                        }
                    });
                },
                refreshPage: function () {
                    var _self = this
                    orderIdSelect, carPlateSelect, carPlateOptions, dateSelect, ForshortSelect = null,
                        carNo, dateTime, invoiceNo, CompanyId = []
                    _self.doms.ForshortValue.val('');
                    _self.doms.DateValue.val('');
                    _self.doms.btnOut.attr('disabled', 'disabled').addClass('disabled')
                    orderIdSelect.trigger.innerHTML = '';
                    _self.doms.orderSelector.attr('disabled', 'disabled').addClass('disabled')
                    module.init();
                    var _html = '<table class="table table-striped table-condensed"><tr><th>出货单号</th><th>出货日期</th><th>运输车辆</th><th>运货信息</th></tr></table>'
                    this.doms.table1.empty().append(_html)
                    this.doms.table2.empty()
                },
                updateInvoiceEstate: function () {
                    var _self = this
                    SERVICE.sendSHR(serviceUrl, {
                        action: 'updateInvoiceEstate',
                        opDatetime: dateTime,
                        invoiceNo: invoiceNo,
                        carNo: carNo,
                        imageUrl: imghref,
                        GZG: 0,
                        MF: 0
                    }, function (oData) {
                        if (oData.status == 0 && oData.result) {
                            POP.ShowAlert('出货成功')
                            _self.refreshPage()
                        } else {
                            POP.ShowAlert(oData.msg || '出错了')
                        }
                    })
                    imghref = '';
                    nimages = 0;
                },
                addNewCar: function (carNo) {
                    var _self = this
                    SERVICE.sendSHR(serviceUrl, {action: 'createCarNo', carNo: carNo}, function (oData) {
                        if (oData.status == 0) {
                            POP.ShowAlert('新增成功')
                            _self.retrieveCarOptions(carNo)
                            _self.doms.carCreateCtn.hide()
                        } else {
                            POP.ShowAlert(oData.msg || '出错了')
                        }
                    })
                },
                bind: function () {
                    var _self = this

                    _self.doms.btnOut.on('click', function () {
                        if (carNo && dateTime && invoiceNo) {
                            POP.ShowConfirm('请确认是否出货', '确定', '取消', function () {
                                _self.updateInvoiceEstate()
                            })
                        } else {
                            POP.ShowAlert('请确认车辆信息和图片信息是否完整')
                        }
                    });

                    _self.doms.carCreate.on('click', function () {
                        _self.doms.carCreateCtn.show()
                    })

                    $('#car-create-cancel').on('click', function () {
                        _self.doms.carCreateCtn.hide()
                    })

                    $('#car-create-ok').on('click', function () {
                        var plate = $('#car-create-plate').val()
                        if (!plate) {
                            POP.ShowAlert('请输入正确的车牌')
                        } else {
                            POP.ShowConfirm('请确认是否新增车牌：' + plate, '确定', '取消', function () {
                                _self.addNewCar(plate)
                            })
                        }
                    })

                    _self.doms.picupform.on('click', function () {
                        _self.doms.upload.click()
                        _self.doms.upload.unbind().change(function () {
                            var piclink = $('#imageurl');
                            var uimages = document.getElementById('upload').files;
                            if (uimages.length == 0) {
                                return;
                            }
                            console.log(uimages.length)
                            var formdata = new FormData();
                            for (var i = 0; i < uimages.length; i++) {
                                formdata.append('images[]', uimages[i]);
                            }

                            SERVICE.uploadfile(uploadfileUrl, formdata, function (data) {
                                if (data && (data.status == 0)) {
                                    console.log(data, uimages.length)
                                    nimages += uimages.length
                                    if (imghref != '') {
                                        imghref = imghref + ';' + data.result
                                    } else {
                                        imghref += data.result
                                    }
                                    imghref = imghref.substring(0, imghref.length - 1);
                                    console.log(nimages, imghref)
                                    // piclink.attr("href", picurl + imghref);
                                    piclink.text("附件:共" + nimages + "张图片");
                                    _self.imgcheck_bind(imghref)
                                    _self.doms.btnOut.attr('disabled', false).removeClass('disabled')
                                    POP.ShowAlert("图片上传成功")
                                } else {
                                    // piclink.attr("href", "");
                                    piclink.text('');
                                    POP.ShowAlert("图片上传失败 ")
                                }
                            })
                        })

                    });

                    _self.doms.closepics.on("click", function () {
                        _self.doms.table1.show();
                        _self.doms.table2.show();
                        _self.doms.hidebox.hide();
                        // $('#imageurl').show();
                    })

                },

                imgcheck_bind: function (imghref) {
                    var _self = this
                    $('#imageurl').on("click", function () {
                        _self.doms.table1.hide();
                        _self.doms.table2.hide();
                        _self.doms.hidebox.show();
                        // $('#imageurl').hide();
                        var picpaths = imghref.split(";");
                        var html_content = ""
                        for (var i = 0; i < picpaths.length; i++) {
                            //console.log(picurl + paths[i])
                            html_content += '<li class="slide-item"><img class="imgs" src=' + picurl + picpaths[i] + ' alt=""></li>'
                        }
                        _self.doms.slideBox.html(html_content)
                    });
                }
            }
            module.init()
        })(jQuery, POP, SERVICE, undefined)
    </script>
</body>
</html>

<?php
/**
 * Created by PhpStorm.
 * User: IceFire
 * Date: 2018/11/25
 * Time: 15:49
 */
session_start();
$_SESSION["openid"] = "op_TywzYDwG4walmycIBLQWKdEn8";
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="./resource/jquery.min.js"></script>
    <script>
        var cookie = {
            set:function(key,val,time){//设置cookie方法
                var date=new Date(); //获取当前时间
                var expiresDays=time;  //将date设置为n天以后的时间
                date.setTime(date.getTime()+expiresDays*24*3600*1000); //格式化为cookie识别的时间
                document.cookie=key + "=" + val +";expires="+date.toGMTString();  //设置cookie
            },
            get:function(key){//获取cookie方法
                /*获取cookie参数*/
                var getCookie = document.cookie.replace(/[ ]/g,"");  //获取cookie，并且将获得的cookie格式化，去掉空格字符
                var arrCookie = getCookie.split(";")  //将获得的cookie以"分号"为标识 将cookie保存到arrCookie的数组中
                var tips;  //声明变量tips
                for(var i=0;i<arrCookie.length;i++){   //使用for循环查找cookie中的tips变量
                    var arr=arrCookie[i].split("=");   //将单条cookie用"等号"为标识，将单条cookie保存为arr数组
                    if(key==arr[0]){  //匹配变量名称，其中arr[0]是指的cookie名称，如果该条变量为tips则执行判断语句中的赋值操作
                        tips=arr[1];   //将cookie的值赋给变量tips
                        break;   //终止for循环遍历
                    }
                }
                return tips;
            }
        }


        function stackCheck() {

            var stackNo = "test123";//$('input#stackCode').val();
            var action = "searchProducts";
            var arrayDemo = JSON.stringify([{productId: 2048}, {productId: 2048}]);
            var mouldArray = JSON.stringify([{bomId: 5}, {bomId: 6}, {bomId: 7}, {bomId: 8}])
            // var arrayTest = JSON.stringify([{},{}]);
            var productIds = JSON.stringify([1, 2]);
            var param = $("#param").val();
            cookie.set("param",param,8);
            param = "{" + param + "}";
            var requestParam = JSON.parse(param);
            // alert(param);
            // return;

            $.ajax({
                url: 'http://localhost/yantong/function/Controller/ProductFinishedStorageController.php',
                // url: 'http://localhost/yantong/function/Controller/CommonController.php',
                // url: 'http://localhost/yantong/function/Controller/ProductMaintenanceController.php',
                type: 'post',
                data: {
                    action: action,
                    // recordNo: '2019111132111111111111',
                    // // productId:arrayDemo,
                    // recordName: '111',
                    // status: '0',
                    workshopId: "104",
                    // // workShopId:"101",
                    // tradeId: 41,
                    // buildingNo: 0,
                    // floorNo: '',
                    // type: '',
                    // productCode: '',
                    // stackId: '321',
                    cjtjId:'[{"cjtjId":"96321"}]',
                    inspectionProductId:'[{"inspectionProductId":"129"}]',
                    orders:'[{"maintanOrderId":"3"}]',
                    products:'[{"productId":"57524","storageNO":"test"}]',
                    // products:'[{"inventoryDataId":"8"},{"inventoryDataId":"10"}]',
                    inventoryDataIds:'[{"inspectionProductId":1}]',
                    ...requestParam
                    // media_ids:'[{"media_id":"fUUJ24lLE1Knk3lAYIjm4rj-eCX8UcSR_F4Ost5Rap5amGBOHZH_ZMi1LWQtfofR"}]',
                    // access_token:'16_FOKBLHuxu04onyPnipXeMQ7Ui6s2TgBAbcoRJ1_nL62j1Op40muiAkRJdyvumowgJaOq4D4q5mkngKcausvtXbLq_aMiLuwa2Ho_BXKy0K8',
                    // CompanyId: "100032",
                    // BuildingNo: "13",
                    // FloorNo:"2",
                    // TypeId:"8001",
                    // Status:"0",
                    // orderPO: "",
                    // typeId: "",
                    // productId: arrayDemo,
                    // reworkDate: "",
                    // workshopId: 101,
                    // invoicePath: "helloworld",
                    // reworkAnalysis: "345",
                    // pOrderId: 201810081069,
                    //
                    // workdate:"2018-09-15",
                    // pName:"",
                    // workHours:12,
                    // workerNum:2,
                    // causeAnalysis:"测试分析语句",
                    // startDate:"2018-09-15",
                    // trolleyId:80001,
                    // originStackId:12,
                    // stackNo:stackNo,
                    // stackId:1,
                    // seatId:'A111',
                    // tradeId:0,
                    // mouldCat:"PCB",
                    // mouldNo:"MPCB54",
                    // mouldArray:mouldArray,
                    // buildingNo:2,
                    //
                    // type:'',
                    // floorNo:12,
                    // productCode:'',
                    // productIds: productIds,
                    // openId:'op_TywzYDwG4walmycIBLQWKdEn8',
                    // arrayDemo:arrayDemo,
                    // arrayTest:{
                    //     name:"冷荣富",
                    //     age:22,
                    //     sex:"男"
                    // },
                    // productName:'13-2-YBS-1L-1-1',
                    // currentDate:'2018-10-19',
                    // invoiceNo:'2018101928',
                    // carNo:'皖S2W921',
                    // opDatetime:'2018-10-19 11:06:07',
                    // tag:2,

                },
                dataType: 'json',
                success: function (result) {
                    $("#result").html(JSON.stringify(result));
                    if (result.status == 0) {
                        //window.location.href = '<?php $_SERVER['HTTP_HOST']?>/Test/view/stackinfo.php?stackId='+result.result;
                        // alert(result.result)
                    } else {
                        // alert(result.result)
                    }
                },
                error:(error)=>{
                    $("#result").html(error.responseText);
                }
            });
        }
    </script>
</head>
<body>
<!--<input id="stackCode" type="text" name="stackCode" class="stack-input" required="" placeholder="请输入垛号"-->
<!--       autocomplete="off">-->
<textarea style="width: 400px;height: 400px;" id="param">
</textarea>
<button id="stackBtn" class="stack-button" onclick="stackCheck();">发送</button>
<div id="result">
</div>

<br />
<br />
</body>
<script>
    var value = cookie.get("param");
    $("#param").html(value);
</script>
</html>

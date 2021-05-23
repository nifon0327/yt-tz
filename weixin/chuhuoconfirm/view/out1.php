<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="./resource/mobileSelect.css">
</head>
<body>

<h1>111</h1>
<div id="day"><label for="day_value">出货单号:</label><input type="text" id="day_value"></div>

</body>
</html>
<script src="./resource/jquery.min.js"></script>
<script src="./resource/mobileSelect.js"></script>
<script>

    var InvoiceSelect = new MobileSelect({
        trigger: '#day',
        title: '单项选择',
        wheels: [
            {data:['周日','周一','周二','周三','周四','周五','周六']}
        ],
        position:[2] //初始化定位
    });
</script>
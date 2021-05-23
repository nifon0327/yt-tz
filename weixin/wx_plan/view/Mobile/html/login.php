<!doctype html>
<html lang="cn">
<head>
  <title>登陆</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="../css/common.css">
  <link rel="stylesheet" type="text/css" href="../css/login.css">
  <link rel="stylesheet" type="text/css" href="../js/layer.mobile.v2.0/need/layer.css">
</head>
<body>
<div class="login text-c">
  <h1 class="title">要货时间</h1>
  <div class="contain">
    <p>
      <span>帐号：</span>
      <input type="text" id="account"/>
    </p>
    <p class="mt-10">
      <span>密码：</span>
      <input type="password" id="password"/>
    </p>
  </div>
  <div>
     <button id="goToLogin">确定</button>
  </div>
</div>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/jquery.cookie.js"></script>
<script type="text/javascript" src="../js/layer.mobile.v2.0/layer.js"></script>
<script type="text/javascript">
  !(function () {
  
    $.cookie('Number', '');
    $.cookie('uName', '');
    $.cookie('uPwd', '');
    $.cookie('openID', '');
    $.cookie('Name', '');
    $.cookie('Forshort','');
    $('#goToLogin').on('click', function () {
      var account  = $('#account').val();
      if(_isNull(account)){
        layer.open({
          content: '用户名不能为空',skin: 'msg',time: 2
        });
        return;
      }
      var password = $('#password').val();
      if(_isNull(password)){
        layer.open({content: '密码不能为空',skin: 'msg',time: 2});
        return;
      }
      myPost({
        data: {
          action: 'ckLoginExt',
          name: account,
          password: password
        },
        successFn: function (data) {
            var useinfo=data.result;
          if (data.status == 0) {
             $.cookie('Number', useinfo.Number);
             $.cookie('uName', useinfo.uName);
             $.cookie('uPwd', useinfo.uPwd);
             $.cookie('openID', useinfo.openID);
             $.cookie('Name', useinfo.Name);
             $.cookie('Forshort',useinfo.Forshort);
             window.location.href = 'goodsTime.php';
          } else {
            layer.open({
              content: data.msg
              ,btn: '确定'
            });
          }
        }
      })
    });
  })();
</script>
</body>
</html>
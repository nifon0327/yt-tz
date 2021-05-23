<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
<link rel="stylesheet" href="../model/css/ac_ln.css">
<style>
  body {
    background-color: #f2f3f5;
  }

  .div-search {
    position: relative;
    width: 600px;
    top: 30px;
    left: 30px;
    -webkit-box-shadow: #767676 0px 0px 8px;
    -moz-box-shadow: #767676 0px 0px 8px;
    box-shadow: #767676 0px 0px 8px;
  }
</style>
<?php
//电信-zxq 2012-08-01

$ClientList = "";
$nowInfo = "当前:订单状态查询";

$queryList = "<input name='PorderId' type='text' id='PorderId' width='60px;' value='12位订单流水号'  onfocus=\"this.value=this.value=='12位订单流水号'?'' : this.value;\"  onblur= \"this.value=this.value=='' ? '12位订单流水号' : this.value;\" style='color:#000;height:20px;'>";
$queryList .= "&nbsp;&nbsp;&nbsp;&nbsp;<span class='ButtonH_25' id='Querybtn' name='Querybtn' onclick='QueryEstate();'>查询</span>";

echo "<div class='div-search'>
    <div style='width:600px;height:50px;'>
      <div style='width:520px;height:50px;float: left;background-color: #fff;'>
      <input name='PorderId' type='text' id='PorderId' width='60px;' value='请输入12位订单流水号'  onfocus=\"this.value=this.value=='请输入12位订单流水号'?'' : this.value;\"  onblur= \"this.value=this.value=='' ? '请输入12位订单流水号' : this.value;\" style='color:#000;height:20px;border:0;height:50px;outline:none;margin-left: 20px;color:#BABABA;caret-color:#48BBCB;'>
</div>
      <div id='btn-hover' style='width:80px;height:50px;float: left;color:#fff;background-color: #48bbcb;'><i  class='iconfont  icon-sousuofangdajing' style='margin-left: 25px;margin-top:10px;font-size:30px;display:inline-block' onclick='QueryEstate();'></i></div>
    </div>
    <div>
    <input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled style='height:20px;border:0;margin-left: 20px;width:580px;height:30px;background-color: #f2f3f5;'>
</div>

	</tr>";
?>
</div>
<div id="contentList" name="contentList" align="center"></div>
</form>
</body>
</html>
<script src="https://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js">
</script>
<script>
$(function () {
    $("#btn-hover").mouseover(function () {
        $("#btn-hover").css("background-color", "#3FA9B9");
        $("#btn-hover").css("cursor", "pointer");
    });
    $("#btn-hover").mouseout(function () {
        $("#btn-hover").css("background-color", "#48bbcb");
        $("#btn-hover").css("cursor", "default");
    });

})
</script>
<script src='cj_function.js' type=text/javascript></script>
<script>
function RegisterEstate(e, POrderId, ActionId) {
    var url = "item3_7_ajax.php?POrderId=" + POrderId + "&ActionId=" + ActionId;
    var ajax = InitAjax();
    ajax.open("GET", url, true);
    ajax.onreadystatechange = function () {
        if (ajax.readyState == 4 && ajax.status == 200) {
            if (ajax.responseText == "Y") {
                e.innerHTML = "&nbsp;";
            } else {
                e.innerHTML = "<span style='color:#ff0000'>更新失败！</span>";
            }
        }
    }
    ajax.send(null);
    // QueryEstate();
}

function QueryEstate() {
    var POrderId = document.getElementById("PorderId").value;
    POrderId = POrderId.replace(/(^\s*)|(\s*$)/g, ""); //去掉空格

    if (fucCheckNUM(POrderId, '') && POrderId.length == 12) {

        var url = "item3_7_ajax.php?POrderId=" + POrderId + "&ActionId=5";
        var ajax = InitAjax();
        ajax.open("GET", url, true);
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4 && ajax.status == 200) {
                var e = document.getElementById("contentList");
                //更新该单元格底色和内容
                e.innerHTML = ajax.responseText;
            }
        }
        ajax.send(null);
    }
    else {
        alert("请输入正确的订单流水号！");
    }
}


</script>
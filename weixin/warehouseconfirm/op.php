<?php

session_start();

error_reporting(E_ALL & ~E_NOTICE);

include_once('../configure.php');

include_once('../log.php');

include_once('../jsapi.php');

include_once('../../model/modelfunction.php');

include_once('../../basic/parameter.inc');

$js_sdk = new jsapi();

$sign = $js_sdk->get_sign();

$Login_P_Number = $_SESSION['user_id'];

//判断此p_name 所处的阶段 并返回POrderId
$p_name = $_POST['p_name'];

$p_name_filter = " AND P.cName='$p_name' ";
// $test_filter = ' AND SC.ActionId=101';//101 脱模入库 103 钢筋下料 104 骨架搭建 106 浇捣养护

$checkScSign = 3;//可生产标识

//获取工单信息
$query="SELECT O.Forshort,Y.POrderId,Y.OrderPO,Y.Qty AS OrderQty,SUM(S.Qty) AS Qty,S.StockId,
    P.ProductId,P.cName,P.eCode,P.TestStandard,P.pRemark,S.Estate,
    U.Name AS Unit,
    PI.Leadtime,PI.Leadweek
	FROM $DataIn.sc1_cjtj  S 
	INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = S.StockId
	INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
	INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId 
	INNER JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
	INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=Y.OrderNumber 
	LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
	INNER JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
	INNER JOIN $DataIn.productunit U ON U.Id=P.Unit
	LEFT JOIN $DataIn.yw3_pileadtime PI ON PI.POrderId=Y.POrderId
	WHERE S.Estate in (1,2,3) $p_name_filter  AND G.Level = 1";

log::e($query, 'test');
$cursor = mysql_query($query);
if (($myRow = @mysql_fetch_array($cursor)) && $myRow['Forshort'] != null) {
    $Forshort=$myRow['Forshort'];
    $POrderId=$myRow["POrderId"];
    $ProductId=$myRow["ProductId"];
    $OrderPO=toSpace($myRow["OrderPO"]);
    $cName=$myRow["cName"];
    $eCode=toSpace($myRow["eCode"]);
    $Field = explode("-", $eCode);
    $BuildNO = $Field['1'];//楼栋
    $FloorNo = $Field['2'];//楼层
    $TestStandard=$myRow["TestStandard"];
    $Estate = $myRow["Estate"];
    $WorkShopName=$myRow["WorkShopName"];
    $Qty=$myRow["Qty"];
    $OrderQty=$myRow["OrderQty"];
    $pRemark=$myRow["pRemark"]==""?"&nbsp;":$myRow["pRemark"];
    $OrderDate=$myRow["OrderDate"];
    $Leadtime=$myRow["Leadtime"];
    $Leadweek=$myRow["Leadweek"];
    $StockId =$myRow["StockId"];
    $Estate = $myRow['Estate'];
    if ($Estate == 1) {
        $Estate = '待审核';
    }elseif($Estate == 2){
        $Estate = '合格';
        $button_value = '<button type="submit" class="weui-form-preview__btn weui-form-preview__btn_primary" style="background-color: #990000;color;#fff" onclick="reg(1)">入库</button>';
    }elseif($Estate == 3){
        $Estate = '不合格';
    }


}
else {
    //不存在此构件，则转至信息页
    $result = 0;

    $cName = $p_name;
    include "../processName.php";
    if ($ret) {
        $title = urlencode("$ret");

        $msg = urlencode("$inform");
    }else {
        $title = urlencode("获取信息失败");

        $msg = urlencode('如有疑问，请联系信息部人员</br>电话：15919701518');
    }

//    $mysql="SELECT S.Estate
//	FROM $DataIn.sc1_cjtj  S
//	INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = S.StockId
//	INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
//	INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId
//	INNER JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
//	INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=Y.OrderNumber
//	LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
//	INNER JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
//	INNER JOIN $DataIn.productunit U ON U.Id=P.Unit
//	LEFT JOIN $DataIn.yw3_pileadtime PI ON PI.POrderId=Y.POrderId
//	WHERE S.Estate = 0 $p_name_filter  AND G.Level = 1";
//    $ret = mysql_query($mysql, $link_id);
//    if ($res = mysql_fetch_array($ret)) {
//        if ($res['Estate'] == 0) {
//
//            $result = 0;
//
//            $title = urlencode("该构件已入库");
//
//            $msg = urlencode('该构件已入库！');
//        }
//    }

    header("Location:msg.php?result=$result&title=$title&msg=$msg");
}

$SeatIdResult= mysql_query("SELECT SeatId
    FROM wms_seat
    WHERE WareHouse='成品仓库' order by SeatId",$link_id);
if ($SeatIdRow = mysql_fetch_array($SeatIdResult)){
    $SeatIdList="<select name='SeatId' id='SeatId' style='height:25px'>";
    do{
        $theSeatId=$SeatIdRow["SeatId"];

        $SeatIdList.="<option value='$theSeatId' >$theSeatId</option>";
    }while($SeatIdRow = mysql_fetch_array($SeatIdResult));
    $SeatIdList.="</select>";
}


$qtyRes = mysql_query("SELECT COUNT(*) AS zqty from $DataIn.yw1_ordersheet where OrderPO = '$OrderPO'", $link_id);
if ($qtyRow = mysql_fetch_array($qtyRes)) {
    $zqty = $qtyRow['zqty'];//总构件
}
$kqtyRes = mysql_query("SELECT COUNT(*) AS kqty from $DataIn.yw1_ordersheet where OrderPO = '$OrderPO' AND SeatId IS NULL", $link_id);
if ($kqtyRow = mysql_fetch_array($kqtyRes)) {
    $kqty = $kqtyRow['kqty'];//未设库位
}
$rqtyRes = mysql_query("SELECT COUNT(*) AS rqty FROM $DataIn.ch1_shipsplit SP LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId = SP.POrderId WHERE S.OrderPO ='$OrderPO'", $link_id);
if ($rqtyRow = mysql_fetch_array($rqtyRes)) {
    $rqty = $rqtyRow['rqty'];//已入库
}

$kSeat=$kqty-($zqty-$rqty);
?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
  <title>研砼治筑运营平台</title>
  <link rel="stylesheet" type="text/css" href="http://res.wx.qq.com/open/libs/weui/1.1.2/weui.min.css">
  <script src="../../public/js/jquery.min.js"></script>
  <style>
  </style>
</head>
<body>
<div class="weui-form-preview">
  <div class="weui-form-preview__hd">
    <label class="weui-form-preview__label">项目名称</label>
    <span class="weui-form-preview__value"><?php echo $Forshort; ?></span>
  </div>
  <div class="weui-form-preview__bd">
    <div class="weui-form-preview__item">
      <label class="weui-form-preview__label">订单编号</label>
      <span class="weui-form-preview__value"><?php echo $OrderPO; ?></span>
    </div>
    <div class="weui-form-preview__item">
      <label class="weui-form-preview__label">构件编号</label>
      <span class="weui-form-preview__value"><?php echo $eCode; ?></span>
    </div>
    <div class="weui-form-preview__item">
      <label class="weui-form-preview__label">当前状态</label>
      <span class="weui-form-preview__value"><?php echo $Estate; ?></span>
    </div>
      <?php
      if ($Estate == '合格'){
          echo "<div class=\"weui-form-preview__item\" style='height: 30px'>
      <label class=\"weui-form-preview__label\">入库单号</label>
      <span class=\"weui-form-preview__value\"><input type='text' id='rkNo' name='rkNo' style='width: 130px;height: 25px;' maxlength=\"20\" value=''></span>
    </div>
    <div class=\"weui-form-preview__item\" style='height: 30px'>
      <label class=\"weui-form-preview__label\">入库库位</label>
      <span class=\"weui-form-preview__value\">$SeatIdList</span>
    </div>
    <div class=\"weui-form-preview__item\" style='height: 30px'>
      <label class=\"weui-form-preview__label\">入库垛号</label>
      <span class=\"weui-form-preview__value\"><input type='text' id='rkDNo' name='rkDNo' style='width: 130px;height: 25px;' maxlength=\"20\" value=''></span>
    </div>";
      }

      ?>
      <div class="weui-form-preview__item">
          <label class="weui-form-preview__label">入库数量</label>
          <span class="weui-form-preview__value"><?PHP echo '总共'.$zqty.' / 入库'.$rqty. ' / 空位'.$kSeat ; ?></span>
      </div>
  </div>
    <br/>
  <div class="weui-form-preview__ft">
    <a class="weui-form-preview__btn weui-form-preview__btn_default" href="javascript:" id="btn-scan">继续扫码</a>
      <?php echo $button_value; ?>

  </div>

  <form action="op.php" method="post" id=form1>
    <input type=hidden name=p_name id=p_name/>
  </form>
  <form action="reg.php" method="post" id=form2>
    <input type=hidden name=POrderId id=POrderId value="<?php echo $POrderId; ?>"/>
    <input type=hidden name=sPOrderId id=sPOrderId value="<?php echo $sPOrderId; ?>"/>
    <input type=hidden name=StockId id=StockId value="<?php echo $StockId; ?>"/>
    <input type=hidden name=Seat id=Seat value=""/>
    <input type=hidden name=storageNO id=storageNO value=""/>
    <input type=hidden name=fromAction id=fromAction value="<?php echo($ActionId == 101 ? 1 : 2); ?>"/>
    <div class="js_dialog" id="Dialog" style="display: none;">
      <div class="weui-mask"></div>
      <div class="weui-dialog">
        <div class="weui-dialog__hd">
          <strong class="weui-dialog__title"><?php echo $dialog_title; ?></strong></div>
        <div class="weui-dialog__bd">
          <div class="weui-cell">
            <div class="weui-cell__bd">
                <?php echo $dialog_input; ?>
              <div class="weui-footer warning">
                <p class="weui-footer__text">
                    <?php echo $dialog_tip; ?>
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="weui-dialog__ft">
          <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_default" id="dismiss">取消</a>
          <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary" id="forward">保存</a>
        </div>
      </div>
    </div>
  </form>
</div>
<div class="weui-footer weui-footer_fixed-bottom">
  <p class="weui-footer__text">如有疑问，请联系信息部人员</p>
  <p class="weui-footer__text">电话：15919701518</p>
</div>
</body>
</html>
<script src=http://res.wx.qq.com/open/js/jweixin-1.2.0.js></script>
<script>
wx.config({
    debug: false,
    appId: '<?php echo $sign["appId"];?>',
    timestamp: '<?php echo $sign["timestamp"];?>',
    nonceStr: '<?php echo $sign["nonceStr"];?>',
    signature: '<?php echo $sign["signature"];?>',
    jsApiList: [
        'scanQRCode'
    ]
});
var reg_argu = '<?php echo $reg;?>';
wx.ready(function () {
    document.getElementById('btn-scan').onclick = function () {
        wx.scanQRCode({
            needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
            scanType: ["qrCode"], // 可以指定扫二维码还是一维码，默认二者都有
            success: function (res) {
                var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
                document.getElementById('p_name').value = result;
                document.getElementById('form1').submit();
            }
        });
    }
});

$('#dismiss').click(function () {

    $('#Dialog').hide();

})
$('#forward').click(function () {

    if (reg_argu == 1) {

        //入库验证 验证是否是数字，切与原值不同
        var temp = $('#tmpRelation').val();

        var rel = $('#Relation').val();

        if (fucCheckNUM(rel, '') == 0) {

            $('.warning p').css('color', 'red').text('非数字，请输入正确的数字');

            return false;

        } else if (temp == rel) {

            $('#Relation').val('');

        }

    } else if (reg_argu == 2) {

        //钢筋下料验证 验证是否是数字，后加不可超过总数的验证
        var Qty = $('#Qty').val();

        if (fucCheckNUM(Qty, '') == 0) {

            $('.warning p').css('color', 'red').text('非数字，请输入正确的数字');

            return false;

        }

    }
    document.getElementById('form2').submit();

})
function reg(argu) {

    var generateHideElement = function (name, value) {
        var tempInput = document.createElement("input");
        tempInput.type = "hidden";
        tempInput.name = name;
        tempInput.value = value;
        return tempInput;
    }

    var SeatId = generateHideElement("Seat",$('#SeatId option:selected').val());

    var rkNo = generateHideElement("storageNO",document.getElementById("rkNo").value);
    var rkDNo = generateHideElement("StackId",document.getElementById("rkDNo").value);

    // if( document.getElementById("rkNo").value == ''  || document.getElementById("rkNo").value == null) {
    //     return false; //空
    // } else {
    //     return true; //非空
    // }

    var Estate = generateHideElement("Estate", argu);

    document.getElementById('form2').appendChild(SeatId);

    document.getElementById('form2').appendChild(rkNo);

    document.getElementById('form2').appendChild(rkDNo);

    document.getElementById('form2').appendChild(Estate);

    document.getElementById('form2').submit();
}
function fucCheckNUM(NUM, Objects) {

    var i, j, strTemp;

    if (Objects != "Price") {

        strTemp = "0123456789";

    } else {

        strTemp = ".0123456789";

    }
    if (NUM.length == 0)

        return 0

    for (i = 0; i < NUM.length; i++) {

        j = strTemp.indexOf(NUM.charAt(i));

        if (j == -1) {
            //说明有字符不是数字
            return 0;
        }
    }
    //说明是数字
    return 1;
}

</script>
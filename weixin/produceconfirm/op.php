<?php

session_start();

error_reporting(E_ALL & ~E_NOTICE);

include_once('../configure.php');

include_once('../log.php');

include_once('../jsapi.php');

$js_sdk = new jsapi();

$sign = $js_sdk->get_sign();

$Login_P_Number = $_SESSION['user_id'];

//判断此p_name 所处的阶段 并返回POrderId
$p_name = $_POST['p_name'];

$p_name_filter = " AND P.cName='$p_name' ";

// $test_filter = ' AND SC.ActionId=101';//101 脱模入库 103 钢筋下料 104 骨架搭建 106 浇捣养护

$checkScSign = 3;//可生产标识

//获取工单信息
$query = "SELECT O.Forshort,SC.Id,SC.POrderId,SC.sPOrderId,SC.Qty,SC.Estate,SC.ActionId,SC.StockId AS scStockId,SC.Remark,SC.mStockId,
D.StuffId,D.StuffCname,D.Picture,SD.TypeId,P.eCode,OM.OrderPO,W.Name AS scLine,SC.WorkShopId,Y.ProductId,
G.DeliveryDate,G.DeliveryWeek,SM.PurchaseID,G.Mid,SC.mStockId
FROM  yw1_scsheet SC 
LEFT JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
LEFT JOIN yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
LEFT JOIN productdata P ON P.ProductId = Y.ProductId 
LEFT JOIN trade_object O ON O.CompanyId = OM.CompanyId
LEFT JOIN workscline W ON W.Id = SC.scLineId
LEFT JOIN cg1_semifinished M ON M.StockId = SC.StockId
LEFT JOIN cg1_stocksheet G ON G.StockId = M.mStockId
LEFT JOIN cg1_stockmain SM ON SM.Id = G.Mid
LEFT JOIN stuffdata D ON D.StuffId = M.mStuffId
LEFT JOIN stuffdata SD  ON SD.StuffId = M.StuffId
WHERE 1 $p_name_filter $test_filter AND SC.scFrom>0 AND SC.Estate>0 AND getCanStock(SC.sPOrderId,$checkScSign)=$checkScSign limit 1";

log::e($query, 'test');
$cursor = mysql_query($query);
if ($myRow = @mysql_fetch_array($cursor)) {

    $Id = $myRow["Id"];

    $Forshort = $myRow['Forshort'];

    $POrderId = $myRow["POrderId"];

    $sPOrderId = $myRow["sPOrderId"];

    $StuffId = $myRow["StuffId"];

    $mStockId = $myRow["mStockId"];

    $Picture = $myRow["Picture"];

    $StuffCname = $myRow["StuffCname"];//有保留

    $PurchaseID = $myRow["PurchaseID"];

    $Qty = $myRow["Qty"];

    $Remark = $myRow["Remark"] == "" ? "&nbsp;" : $myRow["Remark"];

    $DeliveryDate = $myRow["DeliveryDate"];

    $DeliveryWeek = $myRow["DeliveryWeek"];//本配件的交期

    $sumQty = $sumQty + $Qty;

    $scStockId = $myRow["scStockId"];

    $mStockId = $myRow["mStockId"];

    $TypeId = $myRow["TypeId"];

    $Estate = $myRow["Estate"];

    $OrderPO = $myRow["OrderPO"];

    $eCode = $myRow["eCode"];

    $scLine = $myRow["scLine"];

    $WorkShopId = $myRow["WorkShopId"];

    $ActionId = $myRow["ActionId"];

    $ProductId = $myRow["ProductId"];

    // preg_match('/^[^-]*-[^-]*-(.*)/', $eCode, $matches);

    // $cmptNo = $matches[1];

    $cmptNo = $eCode;

    //领完料未下采购单的，自动下采购单
    $cgMid = $myRow["Mid"];

    if ($cgMid == 0) {

        $DateTemp = date("Y");
        $Date = date("Y-m-d");
        $Operator = $Login_P_Number;//

        if ($mStockId > 0) {

            $GetcgStockRow = mysql_fetch_array(mysql_query("SELECT CompanyId,BuyerId FROM cg1_stocksheet WHERE StockId IN ($mStockId) LIMIT 1"));
            $cgCompanyId = $GetcgStockRow["CompanyId"];
            $cgBuyerId = $GetcgStockRow["BuyerId"];

            //自动单号计算
            $Bill_TempRow = mysql_fetch_array(mysql_query("SELECT MAX(PurchaseID) AS maxID FROM cg1_stockmain WHERE PurchaseID LIKE '$DateTemp%'"));
            $cgPurchaseID = $Bill_TempRow["maxID"];

            if ($cgPurchaseID) {
                $cgPurchaseID = $cgPurchaseID + 1;
            }
            else {
                $cgPurchaseID = $DateTemp . "0001";
            }

            //保存主采购单资料
            $CheckMidRow = mysql_fetch_array(mysql_query("SELECT Id FROM  cg1_stockmain WHERE CompanyId ='$CompanyId' AND Date = '$Date'"));
            $thisCgMid = $CheckMidRow["Id"];
            if ($thisCgMid == "") {
                $inStockMainSql = "INSERT INTO cg1_stockmain 
			  (Id,CompanyId,BuyerId,PurchaseID,DeliveryDate,Remark,Date,Operator)
			  VALUES (NULL,'$cgCompanyId','$cgBuyerId','$cgPurchaseID','0000-00-00','系统生成','$Date','$Operator')";
                $inStockMainAction = mysql_query($inStockMainSql);
                $thisCgMid = mysql_insert_id();
            }

            if ($thisCgMid > 0) {

                $updateStocksheetSql = "UPDATE cg1_stocksheet SET Mid='$thisCgMid',Locks=0 WHERE StockId IN ($mStockId)  AND Mid='0' AND (AddQty+FactualQty)>0 ";
                $updateStockSheetResult = mysql_query($updateStocksheetSql);
                if ($updateStockSheetResult) {
                    //按设置的交货周期更新交货日期
                    $CheckSql = "SELECT G.Id,S.jhDays,T.jhDays AS TypeJhDays  
				FROM cg1_stocksheet G
				LEFT JOIN stuffdata S ON S.StuffId=G.StuffId 
				LEFT JOIN stufftype T ON T.TypeId=S.TypeId 
				WHERE G.StockId IN ($mStockId)";
                    $CheckjhDayResult = mysql_query($CheckSql);
                    while ($CheckjhDayRow = mysql_fetch_array($CheckjhDayResult)) {
                        $stockSheetId = $CheckjhDayRow["Id"];
                        $jhDays = $CheckjhDayRow["jhDays"] == 0 ? $CheckjhDayRow["TypeJhDays"] : $CheckjhDayRow["jhDays"];
                        $DeliveryDate = date("Y-m-d", strtotime("$Date  +$jhDays  day"));

                        $weekRow = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$DeliveryDate',1) AS DeliveryWeek"));
                        $DeliveryWeek = $weekRow["DeliveryWeek"];

                        $DeliveryDateSql = "UPDATE cg1_stocksheet SET DeliveryDate=' $DeliveryDate',DeliveryWeek ='$DeliveryWeek' WHERE Id='$stockSheetId'";
                        $DeliveryDateResult = mysql_query($DeliveryDateSql);
                    }
                }
            }

        }

    }


    $czSign = 1;//操作标记
    //已完成的工序数量
    $CheckscQty = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(C.Qty),0) AS scQty 
	FROM sc1_cjtj C 
	WHERE  C.sPOrderId = '$sPOrderId' AND C.StockId = '$scStockId' "));
    $scQty = $CheckscQty["scQty"];
    $UnPQty = $Qty - $scQty;//未生产数量
    //已生产数字显示方式
    switch ($scQty) {
        case 0:
            $scQty = "&nbsp;";
            break;
        default://生产数量非0
            if ($Qty == $scQty) {//生产完成
                // $scQty="<div class='greenB'>$scQty</div>";
                $czSign = 0;//不能操作
            }
            else {
                if ($Qty > $scQty) {//未完成
                    // $scQty="<div class='yellowB'>$scQty</div>";
                }
                else {//多完成
                    // $scQty="<div class='redB'>$scQty</div>";
                }
            }
            break;
    }

    //获取操作权限:如果权限=31 则可以操作,否则不能操作
    $checkSubSql = mysql_query("SELECT F.ModuleId,P.Action,F.ModuleName,F.Parameter
		FROM sc4_upopedom P
		LEFT JOIN sc4_funmodule F ON F.ModuleId=P.ModuleId 
		LEFT JOIN usertable U ON U.Id=P.UserId
		WHERE F.Parameter = '$WorkShopId|$ActionId' AND U.Number='$Login_P_Number' 
		AND F.Estate=1 limit 1");

    if ($checkSubRow = mysql_fetch_row($checkSubSql)) {

        $module_id = $checkSubRow[0];

        $SubAction = $checkSubRow[1];

        $module_name = $checkSubRow[2];

    };

    if ($czSign == 1) {//是否可以生产

        if ($SubAction == 31 || true) {//有权限:需要是该类别下的小组成员，方有权登记，测试加了true，生产应去掉

            $reg = 0;

            switch ($ActionId) {//101 脱模入库 103 钢筋下料 104 骨架搭建 106 浇捣养护

                case 101:

                    $reg = 1;

                    //配置外箱参数
                    $Relation = 0;

                    $RelationResult = mysql_query("SELECT Relation FROM sc1_newrelation WHERE POrderId='$POrderId' LIMIT 1");

                    log::e("SELECT Relation FROM sc1_newrelation WHERE POrderId='$POrderId' LIMIT 1", 'wxnm');

                    if ($RelationRows = mysql_fetch_array($RelationResult)) {

                        $Relation = $RelationRows["Relation"];

                    }
                    else {

                        $BoxResult = mysql_query("SELECT P.Relation FROM pands P 
									 LEFT JOIN stuffdata D ON D.StuffId=P.StuffId 
									 LEFT JOIN stufftype T ON T.TypeId=D.TypeId 
									 WHERE 1 and P.ProductId='$ProductId' AND P.ProductId>0 and T.TypeId='9040' ");

                        if ($BoxRows = mysql_fetch_array($BoxResult)) {

                            $Relation = $BoxRows["Relation"];

                            if ($Relation != "") {

                                $RelationArray = explode("/", $Relation);

                                $Relation = $RelationArray[1];

                            }
                        }
                    }

                    $button_value = '<button type="submit" class="weui-form-preview__btn weui-form-preview__btn_primary" onclick="reg()">登记</button>';

                    break;

                case 103:

                    $reg = 2;

                    //登记数量

                    $dialog_title = '登记数量';

                    $dialog_input = '<input class="weui-input" type="number" id="Qty" pattern="[0-9]*" placeholder="可登记的数量：' . $UnPQty . '" name="Qty" />';


                    break;

                default://钢筋下料，暂时不启用

                    $button_value = '<button type="submit" class="weui-form-preview__btn weui-form-preview__btn_primary" onclick="reg(' . $reg . ')">登记</button>';

            }


        }
        else {
            //无权
        }
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

//    $mysql = "SELECT SC.Estate FROM  yw1_scsheet SC
//LEFT JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
//LEFT JOIN productdata P ON P.ProductId = Y.ProductId
//WHERE 1 $p_name_filter  and SC.ActionId=101";
//    $ret = mysql_query($mysql, $link_id);
//    if ($res = mysql_fetch_array($ret)) {
//        if ($res['Estate'] == 0) {
//
//            $result = 0;
//
//            $title = urlencode("二维码已扫");
//
//            $msg = urlencode('该构件已脱模登记，请勿重复登记！');
//        }
//        else {
//            $mysql = "SELECT SC.Estate FROM  yw1_scsheet SC
//LEFT JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
//LEFT JOIN productdata P ON P.ProductId = Y.ProductId
//WHERE 1 $p_name_filter  and SC.ActionId=104";
//            $ret = mysql_query($mysql, $link_id);
//            if ($res = mysql_fetch_array($ret)) {
//                if ($res['Estate'] == 0) {
//
//                    $result = 0;
//
//                    $title = urlencode("二维码已扫");
//
//                    $msg = urlencode('该构件已浇捣登记，请勿重复登记！');
//                }
//                else {
//                    $result = 0;
//
//                    $title = urlencode("获取信息失败");
//
//                    $msg = urlencode('系统中无此构件，如有疑问，请联系信息部人员</br>电话：15919701518');
//                }
//
//            }
//
//
//        }
//    }

    header("Location:msg.php?result=$result&title=$title&msg=$msg");

}

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
      <span class="weui-form-preview__value"><?php echo $cmptNo; ?></span>
    </div>
    <div class="weui-form-preview__item">
      <label class="weui-form-preview__label">当前状态</label>
      <span class="weui-form-preview__value"><?php echo $module_name; ?></span>
    </div>
    <div class="weui-form-preview__item">
      <label class="weui-form-preview__label">当前产线</label>
      <span class="weui-form-preview__value"><?php echo $scLine; ?></span>
    </div>
    <div class="weui-form-preview__item">
      <label class="weui-form-preview__label">当前位置</label>
      <span class="weui-form-preview__value">不知道</span>
    </div>

  </div>
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
    <input type=hidden name=StockId id=StockId value="<?php echo $scStockId; ?>"/>
    <input type=hidden name=mStockId id=mStockId value="<?php echo $mStockId; ?>"/>
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

    if (argu) {

        $('#Dialog').fadeIn(200);

        $('#Dialog').find('input:visible')[0].focus();

    } else {

        document.getElementById('form2').submit();

    }

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
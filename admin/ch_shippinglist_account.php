<?php

defined('IN_COMMON') || include '../basic/common.php';

include "../model/modelhead.php";
$From = $From == "" ? "read" : $From;
//需处理参数
$ColsNumber = 14;
//$tableMenuS=1490;
ChangeWtitle(" 对账单");
$funFrom = "ch_shippinglist";
$nowWebPage = $funFrom . "_account";
//$sumCols="4";		//求和列
$Th_Col = "选项|60|序号|40|对账单名称|90|客户名称|90|起始时间|100|截至时间|120|总方量|110|操作人|80|生成时间|100|状态|100|附件|100|操作|50";
$Pagination = $Pagination == "" ? 1 : $Pagination;  //分页标志 0-不分页 1-分页
$Page_Size = 500; //每页数量
$ActioToS = "8,9"; //功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消

$d = anmaIn("design/phpExcelReader/", $SinkOrder, $motherSTR);
$f = anmaIn("trade_sample.xls", $SinkOrder, $motherSTR);

$SearchRows = " and M.Estate='0'";

//项目
$mySql = "SELECT C.Id,M.CompanyId,C.Forshort 
	FROM $DataIn.ch1_shipmain M,$DataIn.trade_object C 
	WHERE 1 AND C.CompanyId=M.CompanyId $SearchRows GROUP BY M.CompanyId ORDER BY M.CompanyId ";
$tradeList = array();
$myResult = mysql_query($mySql, $link_id);
if ($myResult && $myRow = mysql_fetch_array($myResult)) {
    do {
        $tradeList[] = $myRow;
        if ($proId) {
            if ($proId == $myRow["Id"]) {

            }
        } else {
            $proId = $myRow["Id"];
        }
    } while ($myRow = mysql_fetch_array($myResult));
}

//时间
$dateRes = explode("-", $dateTime);
?>

<style type="text/css">
    .input_radio1 {
        vertical-align: top;
        margin-top: -1.5px;
        margin-left: 20px;
    }

    .select1 {
        min-width: 100px;
        height: 25px;
        margin-right: 25px;
        border: 1px solid lightgray;
    }

    .btn_a1 {
        width: 70px;
        height: 25px;
        font-size: 12px;
        color: #0099FF;
        text-align: center;
        line-height: 25px;
        box-sizing: border-box;
        border: 1px solid rgba(121, 121, 121, 1);
        border-radius: 5px;
        display: inline-block;
    }

    a.btn_a1:link, a.btn_a1:visited {
        color: #0099FF;
    }

    .input_btn2 {
        width: 80px;
        height: 25px;
        color: #000;
        border: 1px solid #000;
        border-radius: 5px;
        margin-left: 10px;
        background-color: rgba(0, 153, 102, 1);
    }

    #cmptNoCon {
        width: 150px;
        height: 25px;
    }

    .tds1 {
        height: 35px;
    }

    .lable_active {
        font-weight: bold;
    }

    #topMenu {
        TABLE-LAYOUT: fixed;
        WORD-WRAP: break-word;
        width: 1000px;
        border: 1px solid #E2E8E8;
        border-radius: 5px;
        -moz-box-shadow: 0px 0px 10px #c7c7c7;
        box-shadow: 0px 0px 10px #c7c7c7;
        /*margin: 10px 10px 10px 0*/
        min-height: 70px;
    }

    #TableHead {
        TABLE-LAYOUT: fixed;
        WORD-WRAP: break-word
        min-height: 30px;
        height: 30px;
        background-color: rgb(242, 243, 245);
    }
</style>
<script src="../plugins/laydate/laydate.js"></script>
<script src="../plugins/layer/layer.js"></script>
<table border="0" cellspacing="0" bgcolor='#FFF' class="div-select" id="topMenu">
    <tr>
        <td class="tds1" colspan="2" style="padding-left: 10px;height: 50px">
            &nbsp;&nbsp;
            <!-- 项目 -->
            <select name='proIdCon' id='proIdCon' onchange='proIdConChange()' class=" ">
                <?php
                foreach ($tradeList as $trade) {
                    $Id = $trade["Id"];
                    $Forshort = $trade["Forshort"];
                    echo "<option value='$Id' ", $Id == $proId ? "selected" : "", ">$Forshort</option>";
                }
                ?>
            </select>
            &nbsp;&nbsp;
            <!-- 时间 -->
            <input type="text" id="dateChoose" value="<?php echo $dateTime ?>"
                   style="color: #949598;background-color: #F5F5F5;font: 12px 思源雅黑;border: 0;width: 80px"
                   placeholder="请选择日期">
            <script>
                laydate.render({
                    elem: '#dateChoose'
                    , type: 'month'
                    // ,showBottom: false
                    // , range: '-'
                    // , format: 'yyyy/MM'
                });
            </script>
            &nbsp;&nbsp;
            <span class="btn-confirm" style="width: auto;" onclick="searchGo()">查询</span>
        </td>
        <td>
            <span class="btn-confirm" style="width: auto;" onclick="Generate()">生成对账单</span>
            <span class="btn-confirm" onclick="Submit()">提交</span>
            <span class="btn-confirm" onclick="Del()">删除</span>
        </td>
    </tr>
</table>
<?php
//步骤3：
include "../model/subprogram/read_model_3.php";

//检索条件隐藏值
echo " <input type='hidden' name='proId' id='proId' value='$proId' />";
echo " <input type='hidden' name='dateTime' id='dateTime' value='$dateTime' />";

/*$Orderby = "order by (a.FloorNo+0) ";*/
$Orderby = "order by a.SN ";

//步骤5：
// 菜单
include "../model/subprogram/read_model_5.php";

//步骤6：需处理数据记录处理
$i = 1;
$y = ($Page - 1) * $Page_Size + 1;
//List_Title($Th_Col,"1",1);//($Th_Col, $Sign, $Height)
/////////////////////////////////////////////////////////
$Field = explode("|", $Th_Col);
$Count = count($Field);

$tableWidth = 0;
for ($i = 0; $i < $Count; $i = $i + 2) {
    $y = $i;
    $k = $y + 1;
    $tableWidth += $Field[$k];
}
if (isFireFox() == 1) {
    $tableWidth = $tableWidth + $Count * 2;
}
if (isSafari6() == 1) {
    $tableWidth = $tableWidth + ceil($Count * 1.5) + 1;
}
if (isGoogleChrome() == 1) {
    $tableWidth = $tableWidth + ceil($Count * 1.5);
}
for ($i = 0; $i < $Count; $i = $i + 2) {
    $Class_Temp = $i == 0 ? "A1111" : "A1101";

    $y = $i;
    $k = $y + 1;
    if (isSafari6() == 0) {
        if ($k == ($Count - 1)) {
            $Field[$k] = "";
        }
    }
    $h = $y + 2;


    $TableStr .= "<td width='$Field[$k]' Class='$Class_Temp'>$Field[$y]</td>";

}


echo "<table width='$tableWidth' border='0' cellspacing='0'  id='TableHead'><tr class='' align='center'>" . $TableStr . "</tr></table>";
echo "<div id='floatTable' class='t-list' style='display: none;'><table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' ><tr height='30' id='tr_float' class='' align='center'>" . $TableStr . "</tr></table></div>";

//if ($dateTime!="") {
//    $Sql = "SELECT C.id,C.PROJECT_NAME,C.ACCOUNT_START_DATE,C.ACCOUNT_END_DATE,T.Forshort
////    FROM ch_statement C
////	LEFT JOIN trade_object T ON T.CompanyId = C.COMPANY_ID
////	WHERE T.Id = $proId ";
$Sql = "SELECT C.id,C.PROJECT_NAME,C.ACCOUNT_START_DATE,C.ACCOUNT_END_DATE,T.Forshort,C.ACCOUNT_CREATEUSER,C.STATUS,C.CREATED_TIME,C.ACCOUNT_NAME
    FROM ch_account C
	LEFT JOIN trade_object T ON T.CompanyId = C.COMPANY_ID
	";
//}
//echo $Sql;

if ($dateTime!="") {
    $Sql = $Sql . " WHERE  T.Id = $proId ";
}

    $i = 1;
    $j = 1;    //必须
    $Result = mysql_query($Sql , $link_id);
    if ($Result && $Row = mysql_fetch_array($Result)) {

        do {
            $m = 1;
            $Id = $Row["id"];
            $accountName = $Row['ACCOUNT_NAME'];
            $startValue = $Row['ACCOUNT_START_DATE'];
            $endValue = $Row['ACCOUNT_END_DATE'];
            $clientValue = $Row['COMPANY_ID'];
            $Forshort = $Row['Forshort'];
            $caozuoren = $Row['ACCOUNT_CREATEUSER'];
            $createTime = $Row['CREATED_TIME'];
            $Estate = "";
            /*
             * 0 - 未提交
             * 1 - 已提交
             * 2 - 审核通过
             * 3 - 审核不通过
             */

            switch ($Row['STATUS']){
                case '0':
                    $Estate = '未提交';
                    break;
                case '1':
                    $Estate = '审批中';
                    break;
                case '2':
                    $Estate = '审核通过';
                    break;
                case '3':
                    $Estate = '审核不通过';
                    break;
            }
            //操作更新
            $upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"ch_shippinglist_account_update\",".$Id.")' src='../images/edit.gif' title='更新对账单' width='13' height='13'>";

            $ValueArray = array(
                array(0 => $accountName, 1 => "align='center'"),
                array(0 => $Forshort, 1 => "align='center'"),

                array(0 => $startValue, 1 => "align='center'"),
                array(0 => $endValue, 1 => "align='center'"),
                array(0 => $InvoiceNO, 1 => "align='center'"),
                array(0 => $caozuoren, 1 => "align='center'"),
                array(0 => $createTime, 1 => "align='center'"),
                array(0 => $Estate, 1 => "align='center'"),
                array(0 => $gzg, 1 => "align='center'"),
                array(0 => $upMian, 1 => "align='center'"),
//                array(0 => $mf, 1 => "align='center'"),
//                array(0 => $Date, 1 => "align='center'"),
//                array(0 => $Wise, 1 => "align='center'"),
//                array(0 => $Operator, 1 => "align='center'"),
//                array(0 => $PIcreator, 1 => "align='center'"),
//                array(0 => $PIcreatetime, 1 => "align='center'"),
            );


            $checkidValue = $Row["id"];
            $ChooseOut = "N";
            if ($PINO == "") {
                $myOpration = "<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled>";
            }else{
                $myOpration = "&nbsp;";
            }
            include "../model/subprogram/read_model_6.php";
        } while ($Row = mysql_fetch_array($Result));
    } else {
        noRowInfo($tableWidth);
    }



//步骤7：
echo '</div>';

$myResult = mysql_query($mySql, $link_id);
if ($myResult) $RecordToTal = mysql_num_rows($myResult);
pBottom($RecordToTal, $i - 1, $Page, $Pagination, $Page_Size, $timer, $Login_WebStyle, $tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<div id="tj" style="display: none">
    <table>
        <tr>
            <td class="tds1" colspan="2" style="padding-left: 10px;height: 50px">
                &nbsp;&nbsp;对账单名称：<input type="text" id="accountName" name="accountName"/><br/>
                &nbsp;&nbsp;项目：
                <!-- 项目 -->
                <select name='proIdCons' id='proIdCons'>
                    <?php
                    foreach ($tradeList as $trade) {
                        $Id = $trade["Id"];
                        $Forshort = $trade["Forshort"];
                        echo "<option value='$Id' ", $Id == $proId ? "selected" : "", ">$Forshort</option>";
                    }
                    ?>
                </select>
                <br/><br/>
                &nbsp;&nbsp;起止日期：
                <!-- 时间 -->
                <input type="text" id="dateChooses" value=""
                       style="color: #949598;background-color: #F5F5F5;font: 12px 思源雅黑;border: 0;width: 150px"
                       placeholder="请选择起止日期">

                <br/><br/>
                &nbsp;&nbsp;供货单位确认日期:<input type="text" id="supplyDate" name="supplyDate" placeholder="请选择日期" style="color: #949598;background-color: #F5F5F5;font: 12px 思源雅黑;border: 0;width: 150px"/>
                <br/><br/>
                &nbsp;&nbsp;收货单位确认日期:<input type="text" id="receivingDate" name="receivingDate" placeholder="请选择日期" style="color: #949598;background-color: #F5F5F5;font: 12px 思源雅黑;border: 0;width: 150px"/><br/>
                <script>
                    laydate.render({
                        elem: '#dateChooses'
                        , type: 'date'
                        , range: '-'
                        , format: 'yyyy/MM/dd'
                    });
                    laydate.render({
                        elem: '#supplyDate'
                        , type: 'date'
                        , format: 'yyyy/MM/dd'
                    });
                    laydate.render({
                        elem: '#receivingDate'
                        , type: 'date'
                        , format: 'yyyy/MM/dd'
                    });
                </script>
                &nbsp;&nbsp;
            </td>
        </tr>
    </table>
</div>
<script>
    jQuery(document).ready(function () {
        //首先获取导航栏距离浏览器顶部的高度
        var top = jQuery('#TableHead').offset().top;
        //开始监控滚动栏scroll
        jQuery(document).scroll(function () {
            //获取当前滚动栏scroll的高度并赋值
            var scrTop = jQuery(window).scrollTop();
            //开始判断如果导航栏距离顶部的高度等于当前滚动栏的高度则开启悬浮
            if (scrTop >= top) {
                jQuery('#TableHead').css({'position': 'fixed', 'top': '0', 'z-index': '99'});
            } else {
                //否则清空悬浮
                jQuery('#TableHead').css({'position': '', 'top': ''});
            }
        })
    });

    //查看照片
    function ImageUrl(e) {
        var index = layer.open({
            type: 2,
            title: '出货照片',
            shadeClose: true,
            maxmin: true,
            shade: 0.8,
            area: ['800px', '500px'],
            content: 'ch_ImageUrl.php?Id=' + e //iframe的url
        });

        layer.full(index);
    }

    function searchGo() {
        jQuery("#proId").val(jQuery("#proIdCon").val());
        jQuery("#dateTime").val(jQuery("#dateChoose").val());
        jQuery("#searchGo").val('GO');

        if (jQuery("#dateChoose").val() == "") {
            layer.msg("请选择日期", function () {
            });
            return;
        }

        RefreshPage("ch_shippinglist_account");
    }

    function Generate_alert() {
       var dates = jQuery("#dateChooses").val();
        var proName = jQuery("#proIdCons  option:selected").text();
        var con = proName+'（'+dates+'）';
        var accountName = jQuery("#accountName").val();
        var supplyDate = jQuery("#supplyDate").val();
        var receivingDate = jQuery("#receivingDate").val();

        if (dates == "") {
            layer.msg("请选择起止日期", function () {
            });
            return;
        }
        if (accountName == "") {
            layer.msg("请填写项目", function () {
            });
            return;
        }
        if (supplyDate == "") {
            layer.msg("请选择供货单位确认日期", function () {
            });
            return;
        }
        if (receivingDate == "") {
            layer.msg("请选择收货单位确认日期", function () {
            });
            return;
        }
        layer.open({
            type: 1
            ,title: false //不显示标题栏
            ,closeBtn: false
            ,area: '300px;'
            ,shade: 0.8
            ,id: 'LAY_layuipro' //设定一个id，防止重复弹出
            ,resize: false
            ,btn: ['确定', '取消']
            ,btnAlign: 'c'
            ,moveType: 1 //拖拽模式，0或者1
            ,content: '<div style="padding: 30px; line-height: 22px; background-color: #393D49; color: #fff; font-weight: 300;font-size: medium;">确定生成 '+con+' 的对账单？</div>'
            ,success: function(layero){
                var btn = layero.find('.layui-layer-btn');
                btn.find('.layui-layer-btn0').attr({
                    href: 'javascript:;'
                    ,onclick:'batchCreatePI()'
                });
            }
        });
    }

    //生成PI
    function batchCreatePI() {

        var formFile = new FormData();
        formFile.append("ActionId", 'account');
        formFile.append("proId", jQuery("#proIdCons").val());
        formFile.append("dates", jQuery("#dateChooses").val());
        formFile.append("accountName", jQuery("#accountName").val());
        formFile.append("supplyDate", jQuery("#supplyDate").val());
        formFile.append("receivingDate", jQuery("#receivingDate").val());

        var ajax = InitAjax();
        ajax.open("POST", 'ch_shippinglist_account_ajax.php', true);
        ajax.onreadystatechange = function (result) {
            if (ajax.readyState == 4 && ajax.status == 200) {
                var str = ajax.responseText.replace(/\s+/g,"");
                if (str == "Y") {
                    layer.msg('生成对账单成功！', {icon: 1}, function () {
                        RefreshPage("<?php echo $nowWebPage; ?>")
                    });
                } else {
                    layer.confirm(str, {
                        btn: ['确定'] //按钮
                        ,icon: 2
                    }, function(index){
                        layer.close(index);
                        RefreshPage("<?php echo $nowWebPage; ?>")
                    })
                }
            }
        };
        ajax.send(formFile);
    }

    //提交
    function Submit() {
        var choosedRow = 0;
        var value = '';
        jQuery('input[name^="checkid[]"]:checkbox').each(function () {
            if (jQuery(this).prop('checked') == true) {
                choosedRow = choosedRow + 1;
                if (choosedRow === 1) {
                    value = jQuery(this).val();
                } else {
                    value = value + '|' + jQuery(this).val();
                }
            }

        });


        if (choosedRow == 0) {
            layer.msg("该操作要求选定记录！", function () {
            });
            return;
        }
        if (choosedRow > 1) {
            layer.msg("该操作只能选取定一条记录!", function () {
            });
            return;
        }
        var htmlobj =jQuery.ajax({
            url: 'ch_shippinglist_account_ajax.php',
            type: 'post',
            asyns: false,
            data: {
                ActionId: 'submit',
                id: value
            },
            success: function (result) {

                layer.msg('提交对账单返回结果：'+ result, {icon: 1}, function () {
                    RefreshPage("<?php echo $nowWebPage; ?>")
                });
            },
            dataType: 'text'
        });


    }
    //删除
    function Del() {
        var choosedRow = 0;
        var value = '';
        jQuery('input[name^="checkid[]"]:checkbox').each(function () {
            if (jQuery(this).prop('checked') == true) {
                choosedRow = choosedRow + 1;
                if (choosedRow === 1) {
                    value = jQuery(this).val();
                } else {
                    value = value + '|' + jQuery(this).val();
                }
            }

        });


        if (choosedRow == 0) {
            layer.msg("该操作要求选定记录！", function () {
            });
            return;
        }
        if (choosedRow > 1) {
            layer.msg("该操作只能选取定一条记录!", function () {
            });
            return;
        }

        jQuery.ajax({
            url: 'ch_shippinglist_account_ajax.php',
            type: 'post',
            asyns:false,
            data: {
                ActionId:'del',
                value: value
            },
            success: function (result) {

                layer.msg('删除成功！', {icon: 1}, function () {
                    RefreshPage("<?php echo $nowWebPage; ?>")
                });
            },
            dataType: 'json'
        });

    }


    function Generate() {
        layer.open({
            type: 1,
            title: '生成对账单',
            content: jQuery('#tj')
            ,btn: ['确定','取消']
            , success: function (layero) {
                layero.find('.layui-layer-btn').css('text-align', 'center')
            }
            ,yes: function(index, layero){
                Generate_alert();
            }
        });

    }
</script>

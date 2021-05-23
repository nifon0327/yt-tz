<?php
include "../model/modelhead.php";
$From = $From == "" ? "read" : $From;
//需处理参数
$ColsNumber = 1000;
//$tableMenuS=1490;
ChangeWtitle("$SubCompany 成品区库位图");
$funFrom = "bom_mould";
$nowWebPage = $funFrom . "_read";
//$sumCols="4";     //求和列
$Th_Col = "";
$Pagination = $Pagination == "" ? 1 : $Pagination;  //分页标志 0-不分页 1-分页
$Page_Size = 100; //每页数量
$ActioToS = "8,9"; //功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消

$d = anmaIn("./public/phpExcelReader/", $SinkOrder, $motherSTR);
$f = anmaIn("trade_sample.xls", $SinkOrder, $motherSTR);

?>
<script src="https://cdn.bootcss.com/layer/2.3/layer.js"></script>
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
        width: 80px;
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

    #mouldNoCon {
        width: 150px;
        height: 25px;
    }

    .tds1 {
        height: 35px;
    }

    .lable_active {
        font-weight: bold;
    }

    .gray_color {
        background: #dde1e1;
    }

    .red_color {
        background: #efd9eb;
    }

    .green_color {
        background: #d5efe7;
    }

    .blue_color {
        background: #cde5f9;
    }

    .orange_color {
        background: #ffedcb;
    }

    .color_color {
        background: #f9ddd3;
    }

    .table_concent td {
        border-color: darkgray;
    }

    .table_concent tr td {
        position: relative;
        /*width:110px;*/
    }

    .table_concent tr div span {
        position: absolute;
        left: 0;
        top: 0;
    }

    .table_concent tr td {
        text-align: center;
    }

    .table_concent tr td div i {
        display: block;
        margin-top: -130px;
        margin-bottom: 25px;
        font-style: normal;
        font-size: 15px;
    }

    .table_concent tr td div strong {
        font-weight: 100;
        display: block;
        /*width: 20px;*/
        margin: 0 auto;
        font-size: 15px;
    }

    .table_concent tr td div {
        /*height: 100px;*/
        padding-top: 20px;
        /*width: 85px;*/
        word-wrap: break-word;
        margin: 0 auto;
    }
</style>

<?php
//步骤3：
include "../model/subprogram/read_model_3.php";

//检索条件隐藏值

//步骤5：
// 菜单
include "../model/subprogram/read_model_5.php";
?>
</div>
<table border="0" cellspacing="0" cellpadding="0">

    <tr>
        <td align="left" valign="top">
            <table class="table_concent" border="1" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="red_color " style="text-align: center;height: 100%;">
                        <div style="margin:0 auto;word-wrap: break-word;"
                        <?php
                        echo selectSeatTrade("A01", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="red_color">
                        <div style="width:100%;" class=""
                        <?php
                        echo selectSeatTrade("A02", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="red_color">
                        <div style="width:100%;" class=""
                        <?php
                        echo selectSeatTrade("A03", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="red_color " style="text-align: center;height: 100%;">
                        <div style="margin:0 auto;word-wrap: break-word;"
                        <?php
                        echo selectSeatTrade("A04", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="red_color " style="text-align: center;height: 100%;">
                        <div style="margin:0 auto;word-wrap: break-word;"
                        <?php
                        echo selectSeatTrade("A05", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="red_color " style="text-align: center;height: 100%;">
                        <div style="margin:0 auto;word-wrap: break-word;"
                        <?php
                        echo selectSeatTrade("A06", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="red_color " style="text-align: center;height: 100%;">
                        <div style="margin:0 auto;word-wrap: break-word;"
                        <?php
                        echo selectSeatTrade("A07", $DataIn, $link_id);
                        ?>
                    </td>
                    <!--<td rowspan="2" class="gray_color "
                        style="border-bottom: 1px solid gray;border-right: 1px solid gray;text-align: center;height: 100%;">
                        报废区
                    </td>-->
                </tr>
                <tr>
                    <td class="top color_color ">
                        <div
                        <?php
                        echo selectSeatTrade("A08", $DataIn, $link_id);
                        ?>

                    </td>
                    <td class="color_color">
                        <div style="width:100%;" class=""
                        <?php
                        echo selectSeatTrade("A09", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="color_color">
                        <div style="width:100%;" class=""
                        <?php
                        echo selectSeatTrade("A10", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="color_color">
                        <div style="width:100%;" class=""
                        <?php
                        echo selectSeatTrade("A11", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="color_color">
                        <div style="width:100%;" class=""
                        <?php
                        echo selectSeatTrade("A12", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="color_color">
                        <div style="width:100%;" class=""
                        <?php
                        echo selectSeatTrade("A13", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="color_color">
                        <div style="width:100%;" class=""
                        <?php
                        echo selectSeatTrade("A14", $DataIn, $link_id);
                        ?>
                    </td>
                </tr>
               <!--<tr>
                    <td class="gray_color " style="width:100px;height: 30px;">
                        工字钢存放区
                    </td>
                    <td class="gray_color ">
                        木方存放区
                    </td>
                    <td class="gray_color ">
                        木方存放区
                    </td>
                    <td class="gray_color ">
                        工具存放区
                    </td>
                    <td class="gray_color ">
                        工字钢存放区
                    </td>
                    <td colspan="3" class="gray_color ">
                        木方存放区
                    </td>
                    <td colspan="2" class="">

                    </td>
                </tr>-->
                <tr>
                    <td class="green_color ">
                        <div
                        <?php
                        echo selectSeatTrade("B01", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="green_color ">
                        <div
                        <?php
                        echo selectSeatTrade("B02", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="green_color ">
                        <div
                        <?php
                        echo selectSeatTrade("B03", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="green_color ">
                        <div
                        <?php
                        echo selectSeatTrade("B04", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="green_color ">
                        <div
                        <?php
                        echo selectSeatTrade("B05", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="green_color ">
                        <div
                        <?php
                        echo selectSeatTrade("B06", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="green_color ">
                        <div
                        <?php
                        echo selectSeatTrade("B07", $DataIn, $link_id);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="blue_color ">
                        <div
                        <?php
                        echo selectSeatTrade("C01", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="blue_color ">
                        <div
                        <?php
                        echo selectSeatTrade("C02", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="blue_color ">
                        <div
                        <?php
                        echo selectSeatTrade("C03", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="blue_color ">
                        <div
                        <?php
                        echo selectSeatTrade("C04", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="blue_color ">
                        <div
                        <?php
                        echo selectSeatTrade("C05", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="blue_color ">
                        <div
                        <?php
                        echo selectSeatTrade("C06", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="green_color ">
                        <div
                        <?php
                        echo selectSeatTrade("B08", $DataIn, $link_id);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="blue_color ">
                        <div
                        <?php
                        echo selectSeatTrade("C07", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="blue_color">
                        <div
                        <?php
                        echo selectSeatTrade("C08", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="blue_color ">
                        <div
                        <?php
                        echo selectSeatTrade("C09", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="blue_color ">
                        <div
                        <?php
                        echo selectSeatTrade("C10", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="blue_color ">
                        <div
                        <?php
                        echo selectSeatTrade("C11", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="blue_color ">
                        <div
                        <?php
                        echo selectSeatTrade("C12", $DataIn, $link_id);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="orange_color ">
                        <div
                        <?php
                        echo selectSeatTrade("D01", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="orange_color ">
                        <div
                        <?php
                        echo selectSeatTrade("D02", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="orange_color ">
                        <div
                        <?php
                        echo selectSeatTrade("D03", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="orange_color ">
                        <div
                        <?php
                        echo selectSeatTrade("D04", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="orange_color ">
                        <div
                        <?php
                        echo selectSeatTrade("D05", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="orange_color ">
                        <div
                        <?php
                        echo selectSeatTrade("D06", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="orange_color ">
                        <div
                        <?php
                        echo selectSeatTrade("D07", $DataIn, $link_id);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="orange_color ">
                        <div
                        <?php
                        echo selectSeatTrade("D08", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="orange_color ">
                        <div
                        <?php
                        echo selectSeatTrade("D09", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="orange_color ">
                        <div
                        <?php
                        echo selectSeatTrade("D10", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="orange_color ">
                        <div
                        <?php
                        echo selectSeatTrade("D11", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="orange_color ">
                        <div
                        <?php
                        echo selectSeatTrade("D12", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="orange_color ">
                        <div
                        <?php
                        echo selectSeatTrade("D13", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="orange_color ">
                        <div
                        <?php
                        echo selectSeatTrade("D14", $DataIn, $link_id);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="orange_color ">
                        <div
                        <?php
                        echo selectSeatTrade("D15", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="orange_color ">
                        <div
                        <?php
                        echo selectSeatTrade("D16", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="orange_color ">
                        <div
                        <?php
                        echo selectSeatTrade("D17", $DataIn, $link_id);
                        ?>
                    </td>
                    <td class="orange_color ">
                        <div
                        <?php
                        echo selectSeatTrade("D18", $DataIn, $link_id);
                        ?>
                    </td>
                </tr>

            </table>
        </td>
        <td width="10"></td>
        <td align="left" valign="top">
            <div id="TradeListDiv" style="display: none;">
            </div>
        </td>
    </tr>
</table>

<?php
function selectSeatTrade($seatid, $DataIn, $link_id)
{

//    $seatid = "A011";

    $sql = "SELECT DISTINCT S.SeatId, T.CompanyId, T.Forshort FROM $DataIn.ch1_shipsplit SP
        INNER JOIN $DataIn.yw1_ordersheet S ON S.POrderId = SP.POrderId 
        INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
        INNER JOIN $DataIn.yw1_ordermain O on S.OrderNumber = O.OrderNumber 
        INNER JOIN $DataIn.trade_object T on T.CompanyId = O.CompanyId 
        WHERE S.Estate > 0 AND SP.Estate = 1 AND S.SeatId ='$seatid' ";

    $SeatHtml = "><span>$seatid</span>";
    $TradeResult = mysql_query($sql, $link_id);
    if ($TradeResult && $Row = mysql_fetch_array($TradeResult)) {

        $CompanyId = $Row["CompanyId"];
        if ($CompanyId) {
            $SeatHtml = " onclick='showSeatCmptList(\"$seatid\")'><span>$seatid</span>";
        }

        //分类
        $TypeNames = "";
        $Forshorts = "";


        do {
            $CompanyId = $Row["CompanyId"];

            // if ($Forshorts) {$Forshorts .= ","; }
            $Forshorts = $Row["Forshort"];

            if ($CompanyId) {
                $TypeSql = "select distinct a.TypeId, a.TypeName FROM $DataIn.ch1_shipsplit SP
                inner join $DataIn.yw1_ordersheet c on c.POrderId = SP.POrderId 
                inner join $DataIn.productdata b on c.ProductId = b.ProductId
                INNER JOIN $DataIn.producttype a ON b.TypeId = a.TypeId
                inner join $DataIn.yw1_ordermain d on d.OrderNumber = c.OrderNumber
                where c.Estate > 0 AND SP.Estate = 1 and d.CompanyId = '$CompanyId' and c.SeatId ='$seatid' order by a.SortId";

                $TypeResult = mysql_query($TypeSql, $link_id);

                if ($TypeResult && $TypeRow = mysql_fetch_array($TypeResult)) {
                    do {
                        if ($TypeNames) {
                            $TypeNames .= ";";
                        }

                        $TypeNames .= $TypeRow["TypeName"];

                    } while ($TypeRow = mysql_fetch_array($TypeResult));
                }
            }

            //项目名称
            $SeatHtml .= "<p style='margin:0;padding:0;cursor: pointer' onmouseover='showTradeList(\"$CompanyId\",\"$seatid\")' onmouseout='hideTradeList()'>" . $Forshorts;

            $SeatHtml .= "(";
            $SeatHtml .= $TypeNames;
            $SeatHtml .= ")</p><br/>";
            unset($TypeNames);
        } while ($Row = mysql_fetch_array($TradeResult));


    } else {

    }
    $SeatHtml .= "</div>";

    return $SeatHtml;
}

?>
<script>

    function showTradeList(CompanyId, SeatId) {

        layer.msg('玩命加载中…');
        if (CompanyId != "" && SeatId != "") {
            var url = "ck_cpqkwt_trade_ajax.php?CompanyId=" + CompanyId + "&SeatId=" + SeatId;
            var show = document.getElementById("TradeListDiv");
            var ajax = InitAjax();
            ajax.open("GET", url, true);

            ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {// && ajax.status ==200
                    var BackData = ajax.responseText;
                    var DataArray = BackData.split("`");
                    show.innerHTML = DataArray[0];
                    show.style.display = "block";
                }
            }
            ajax.send(null);
        }
    }

    function hideTradeList() {
        var show = document.getElementById("TradeListDiv");
        show.innerHTML = "";
        show.style.display = "none";
    }

    function showSeatCmptList(Seatid) {
        layer.load(0, {shade: [0.8, '#dde1e1']});
        document.form1.action = "ck_seat_cmpt_read.php?SeatId=" + Seatid;
        document.form1.target = "_self";
        document.form1.submit();
    }

</script>

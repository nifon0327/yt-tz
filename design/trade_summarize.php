<?php
defined('IN_COMMON') || include '../basic/common.php';

include "../model/modelhead.php";

echo "<script src='../plugins/layer/layer.js'></script>
<script>
    var index = layer.load(0, {
        shade: [0.7, '#393D49'] //0.1透明度的白色背景
    });
</script>";

$From = $From == "" ? "read" : $From;
//需处理参数
$ColsNumber = 15;
//$tableMenuS=1490;
ChangeWtitle("$SubCompany 数据报表");
$funFrom = "trade_summarize";
$nowWebPage = $funFrom;
//$sumCols="4";		//求和列
$Th_Col = "选项|60|序号|50|项目编号|80|项目名称|100|构件类型|60|楼栋编号|60|楼层编号|60|构件编号|100|浇捣日期|100|混凝土强度|70|图纸体积|60|混凝土体积|60|重量|60|钢筋|60";
$Pagination = $Pagination == "" ? 1 : $Pagination;  //分页标志 0-不分页 1-分页
$Page_Size = 500; //每页数量
$ActioToS = "8,9"; //功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消

$d = anmaIn("design/phpExcelReader/", $SinkOrder, $motherSTR);
$f = anmaIn("trade_sample.xls", $SinkOrder, $motherSTR);

//检索条件

// 项目取得
$tradeList = array();

$TmySql = "SELECT TT.Id, TT.Forshort,TD.BuildingNo, TI.Estate FROM $DataIn.trade_object TT
LEFT JOIN $DataIn.trade_drawing TD on TT.Id = TD.TradeId
INNER JOIN $DataIn.trade_info TI on TT.Id = TI.TradeId ";
$TmySql .= " where TT.ObjectSign = 2 GROUP BY TT.Id order by TT.Date desc  ";
$TmyResult = mysql_query($TmySql, $link_id);
if ($TmyResult && $TmyRow = mysql_fetch_array($TmyResult)) {
    do {
        $tradeList[] = $TmyRow;
        if ($proId == "") {
            $proId = $TmyRow["Id"];
        }
    } while ($TmyRow = mysql_fetch_array($TmyResult));
}


//栋号
$build = "";
if ($_GET["build"]) {
    $build = $_GET["build"];
}
if ($_POST["build"]) {
    $build = $_POST["build"];
}

// 项目栋号取得
$buildList = array();
if ($proId) {
    $BmySql = "SELECT DISTINCT BuildingNo FROM $DataIn.trade_drawing  where TradeId = $proId order by BuildingNo+0";
    $BmyResult = mysql_query($BmySql, $link_id);
    if ($BmyResult && $BmyRow = mysql_fetch_array($BmyResult)) {
        do {
            $buildList[] = $BmyRow;
            if ($build == "") {
                $build = $BmyRow["BuildingNo"];
            }

        } while ($BmyRow = mysql_fetch_array($BmyResult));
    }
}


//楼层
$floor = "";
if ($_POST["floor"]) {
    $floor = $_POST["floor"];
}

// 项目楼层取得
$floorList = array();
if ($proId) {
    $mySql = "SELECT DISTINCT FloorNo FROM $DataIn.trade_steel  where TradeId = $proId order by FloorNo+0";
    $myResult = mysql_query($mySql, $link_id);

    if ($myResult && $myRow = mysql_fetch_array($myResult)) {
        do {
            $floorList[] = $myRow;
        } while ($myRow = mysql_fetch_array($myResult));
    }
}


//构件类型
$type = "";
if ($_POST["type"]) {
    $type = $_POST["type"];
}

// 项目构件类型取得
$typeList = array();
if ($proId) {
    $TmySql = "SELECT DISTINCT CmptType FROM $DataIn.trade_drawing  where TradeId = $proId  and BuildingNo = $build  order by CmptType";
    $myResult = mysql_query($TmySql, $link_id);

    if ($myResult && $myRow = mysql_fetch_array($myResult)) {
        do {
            $typeList[] = $myRow;
            if ($type == "") {
                $type = $myRow["CmptType"];
            }
        } while ($myRow = mysql_fetch_array($myResult));
    }
}

//构件编号
$cmptNo = "";
if ($_POST["cmptNo"]) {
    $cmptNo = $_POST["cmptNo"];
}

$titles = array();
$specs = array();
$sizes = array();

//数据
$mySql = "SELECT
	TT.Id,
	TT.Forshort,
	TSD.Titles AS steel_Titles,
	TSD.Specs AS steel_Specs,
	TSD.Sizes AS steel_Sizes,
	TSD.BuildingNo,
	TSD.TypeName,
	TED.Titles AS embedded_Titles,
	TED.Specs AS embedded_Specs 
FROM
	trade_steel_data TSD
	INNER JOIN ac_cz.trade_object TT ON TT.Id = TSD.TradeId
	INNER JOIN trade_embedded_data TED ON TED.BuildingNo = TSD.BuildingNo 
	AND TED.TypeName = TSD.TypeName
	INNER JOIN ac_cz.trade_info TI ON TT.Id = TI.TradeId  
	";

$mySql .= " WHERE TT.Id = $proId AND TSD.BuildingNo = '$build'  AND TSD.TypeName = '$type' ";

//echo  $mySql;
$myResult = mysql_query($mySql, $link_id);

//$tradeList = array();
if ($myResult && $myRow = mysql_fetch_array($myResult)) {
    do {

        if ($proId == $myRow["Id"] && $build == $myRow["BuildingNo"]) {
            $steel_Titles = json_decode($myRow["steel_Titles"]);
            $steel_Specs = json_decode($myRow["steel_Specs"]);
            $steel_Sizes = json_decode($myRow["steel_Sizes"]);

            $embedded_Titles = json_decode($myRow["embedded_Titles"]);
            $embedded_Specs = json_decode($myRow["embedded_Specs"]);
        }

    } while ($myRow = mysql_fetch_array($myResult));
}

$m = 0;
if ($steel_Titles) {
    foreach ($steel_Titles as $title) {
        $Th_Col .= "|" . $title . "|80";
        $m++;
    }
    $ColsNumber += $m;
}
$n = 0;
if ($embedded_Titles) {
    foreach ($embedded_Titles as $title) {
        $Th_Col .= "| |80";
        $n++;
    }
    $ColsNumber += $n;
}

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
        margin-right: 10px;
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
</style>
<table border="0" cellspacing="0"
       style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width: 1000px;border: 1px solid #E2E8E8;border-radius:5px;-moz-box-shadow: 0px 0px 10px #c7c7c7; box-shadow: 0px 0px 10px #c7c7c7;margin:10px 10px 10px 0'
       bgcolor='#FFF' class="div-select">
    <tr>
        <td colspan="2" class="tds1" style="padding-left: 10px;">

            <!--项目-->
            <select name='proIdCon' id='proIdCon' onchange='Changes("proId")' class=" ">
                <?php
                foreach ($tradeList as $trade) {
                    $Id = $trade["Id"];
                    $Forshort = $trade["Forshort"];
                    echo "<option value='$Id' ", $Id == $proId ? "selected" : "", ">$Forshort</option>";
                }
                ?>
            </select>
            &nbsp;&nbsp;
            <!-- 栋号-->
            <select name='buildCon' id='buildCon' class=" " onchange='Changes("build")'>
                <!--                <option value='' -->
                <?php //if ($build == null) echo "selected" ?><!-- >楼栋选择</option>-->
                <?php
                foreach ($buildList as $buildData) {
                    $BuildingNo = $buildData["BuildingNo"];
                    echo "<option value='$BuildingNo' ", $BuildingNo == $build ? "selected" : "", ">$BuildingNo 栋</option>";
                }
                ?>
            </select>

            <!-- 楼层-->
            <select name='floorCon' id='floorCon' class="" onchange='Changes("floor")'>
                <option value='' <?php if ($floor == null) echo "selected" ?> >楼层选择</option>
                <?php
                foreach ($floorList as $floorData) {
                    $FloorNo = $floorData["FloorNo"];
                    echo "<option value='$FloorNo' ", $FloorNo == $floor ? "selected" : "", ">$FloorNo</option>";
                }
                ?>
            </select>

            <!-- 构件类型 -->
            <!--            <select name='typeCon' id='typeCon' class=" " onchange='Changes("type")'>-->
            <select name='typeCon' id='typeCon' class=" ">
                <?php
                foreach ($typeList as $typeData) {
                    $CmptType = $typeData["CmptType"];
                    echo "<option value='$CmptType' ", $CmptType == $type ? "selected" : "", ">$CmptType</option>";
                }
                ?>
            </select>

            <input name='cmptNoCon' type='text' id='cmptNoCon' placeholder="输入构件编号" autocomplete='off'
                   value='<?php echo $cmptNo ?>'/>
            <span class="btn-confirm" onClick='toSearchResult()'>查　询</span>
        </td>
        <td class="tds1" style="height: 50px;">
            <span type='button' name='button' class="btn-confirm" title='批量查询' onClick='batch()'
                  style="margin: 10px;">批量查询</span>
            <span type='button' name='button' class="btn-confirm" title='导出数据' onClick='toExport()'>导出数据</span>
        </td>
    </tr>
</table>
<?php
//步骤3：
include "../model/subprogram/read_model_3.php";

//检索条件隐藏值
echo " <input type='hidden' name='proId' id='proId' value='$proId' />";
echo " <input type='hidden' name='build' id='build' value='$build' />";
echo " <input type='hidden' name='Estate' id='Estate' value='$Estate' />";
echo " <input type='hidden' name='floor' id='floor' value='$floor' />";
echo " <input type='hidden' name='type' id='type' value='$type' />";
echo " <input type='hidden' name='cmptNo' id='cmptNo' value='$cmptNo' />";

//查询
if ($_POST['Estate'] == 'ok') {
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
    $tId = "id='TableHead'";

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

        if ($i > 25) {
            $TableStr .= "<td width='$Field[$k]' Class='$Class_Temp'>$Field[$y]</td>";
        } else {
            $TableStr .= "<td rowspan='3' width='$Field[$k]' Class='$Class_Temp'>$Field[$y]</td>";
        }
    }

    $TableStr .= "</tr><tr height='16' class='' align='center' style='background-color: #fff;'>";
    $TableStr .= "<td Class='A0001'>规格</td>";
    if ($steel_Specs) {
        foreach ($steel_Specs as $spec) {
            $StuffSql = "SELECT StuffCName FROM stuffdata WHERE StuffEname = '$spec' AND TypeId = '9018'";
            $StuffResult = mysql_query($StuffSql, $link_id);
            $StuffCName = $spec;
            if ($StuffResult && $StuffRow = mysql_fetch_array($StuffResult)) {
                $StuffCName = $StuffRow["StuffCName"];
            }
            $TableStr .= "<td  Class='A0001'>$StuffCName</td>";
        }
    }
    if ($embedded_Titles) {
        foreach ($embedded_Titles as $spec) {
            $TableStr .= "<td  Class='A0001'>$spec</td>";
        }
    }

    $TableStr .= "</tr><tr height='16' class='' align='center' style='background-color: #fff;'>";
    $TableStr .= "<td Class='A1101'>下料尺寸</td>";
    if ($steel_Sizes) {
        foreach ($steel_Sizes as $size) {
            $TableStr .= "<td  Class='A1101'>$size</td>";
        }
    }
    if ($embedded_Specs) {
        foreach ($embedded_Specs as $size) {
            $TableStr .= "<td  Class='A1101'>$size</td>";
        }
    }
    echo "<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' $tId><tr height='16' class='' align='center'>" . $TableStr . "</tr></table>";
//    echo "<div id='floatTables' class='t-list' style='display: none;'><table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' ><tr height='30' id='tr_floats' class='' align='center'>" . $TableStr . "</tr></table></div>";


//A.Estate 不用,使用项目表状态

    $mySql = "SELECT
	TI.TradeNo,TT.Forshort,TD.CmptType,TD.BuildingNo,TD.FloorNo,TD.CmptNo,TD.SN,TD.CStr,TD.DwgVol,TD.CVol,TD.Weight,TS.Quantities AS steel_Quantities,TE.Quantities AS embedded_Quantities 
FROM
	trade_drawing TD
	INNER JOIN trade_steel TS ON TS.TradeId = TD.TradeId  AND TS.BuildingNo = TD.BuildingNo AND TS.FloorNo = TD.FloorNo AND TS.CmptType = TD.CmptType AND TS.CmptNo = TD.CmptNo AND TS.SN = TD.SN
	INNER JOIN trade_embedded TE ON TE.TradeId = TD.TradeId  AND TE.BuildingNo = TD.BuildingNo AND TE.FloorNo = TD.FloorNo AND TE.CmptType = TD.CmptType AND TE.CmptNo = TD.CmptNo AND TE.SN = TD.SN
	LEFT JOIN trade_object TT ON TT.Id = TD.TradeId
	LEFT JOIN trade_info TI ON TI.TradeId = TD.TradeId ";

    $mySql .= " WHERE TD.TradeId= $proId";

    if ($build) {
        $mySql .= " AND TD.BuildingNo= '$build' ";
    }

    if ($type) {
        $mySql .= " AND TD.CmptType= '$type'";
    }
    if ($floor) {
        $mySql .= " AND TD.FloorNo= '$floor'";
    }
    if ($cmptNo) {
        $mySql .= " AND TD.CmptNo like '%$cmptNo%'";
    }

//    $mySql .= " $Orderby";

    $i = $j = 1;
//echo $mySql;
//echo $PageSTR;

    $myResult = mysql_query($mySql . " $PageSTR", $link_id);
    if ($myResult && $myRow = mysql_fetch_array($myResult)) {
        do {
            $m = 1;  //必须

            $FinishDateSql = "SELECT
	YS.FinishDate 
FROM
	yw1_scsheet YS
	INNER JOIN yw1_ordersheet YO ON YO.POrderId = YS.POrderId
	INNER JOIN productdata PD ON PD.ProductId = YO.ProductId
	INNER JOIN trade_drawing TD ON PD.cName = CONCAT_WS( '-', TD.BuildingNo, TD.FloorNo, TD.CmptNo, TD.SN )
	INNER JOIN trade_object TT ON TT.Id = TD.TradeId 
WHERE
	YS.ActionId = '101' 
	AND TD.TradeId = $proId 
	AND PD.cName = CONCAT_WS( '-', '$myRow[BuildingNo]', '$myRow[FloorNo]', '$myRow[CmptNo]', '$myRow[SN]' )";
            $FinishDateResult = mysql_query($FinishDateSql, $link_id);

            if ($FinishDateResult && $FinishDateRow = mysql_fetch_array($FinishDateResult)) {
                $FinishDate = $FinishDateRow["FinishDate"];
                $Finish = "";
                if ($FinishDate) {
                    $Finish = date('Y-m-d', strtotime($FinishDate));
                }
            }

            $ValueArray = array(
                array(0 => $myRow["TradeNo"], 1 => "align='center'"),
                array(0 => $myRow["Forshort"], 1 => "align='center'"),
                array(0 => $myRow["CmptType"], 1 => "align='center'"),
                array(0 => $myRow["BuildingNo"], 1 => "align='center'"),
                array(0 => $myRow["FloorNo"], 1 => "align='center'"),
                array(0 => $myRow["CmptNo"], 1 => "align='center'"),
                array(0 => $Finish, 1 => "align='center'"),
                array(0 => $myRow["CStr"], 1 => "align='center'"),
                array(0 => $myRow["DwgVol"], 1 => "align='center'"),
                array(0 => $myRow["CVol"], 1 => "align='center'"),
                array(0 => $myRow["Weight"], 1 => "align='center'"),
                array(0 => "数量", 1 => "align='center'")
            );

            $steel_Quantities = json_decode($myRow["steel_Quantities"]);
            if ($steel_Quantities) {
                foreach ($steel_Quantities as $qti) {
                    $ValueArray[] = array(0 => $qti, 1 => "align='center'");
                }
            }
            $embedded_Quantities = json_decode($myRow["embedded_Quantities"]);
            if ($embedded_Quantities) {
                foreach ($embedded_Quantities as $qti) {
                    $ValueArray[] = array(0 => $qti, 1 => "align='center'");
                }
            }

            $checkidValue = $myRow["Id"];
            $ChooseOut = "N";
            $myOpration = "<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled>";
            include "../model/subprogram/read_model_6.php";
        } while ($myRow = mysql_fetch_array($myResult));
    } else {
        noRowInfo($tableWidth);
    }

//步骤7：
    echo '</div>';
}

$myResult = mysql_query($mySql, $link_id);
if ($myResult) $RecordToTal = mysql_num_rows($myResult);

pBottom($RecordToTal, $i - 1, $Page, $Pagination, $Page_Size, $timer, $Login_WebStyle, $tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script>
    function load() {
        var index = layer.load(0, {
            shade: [0.7, '#393D49'] //0.1透明度的白色背景
        });
    }

    document.onreadystatechange = function () {
        if (document.readyState == "complete") {
            layer.closeAll();
        }
    }

    var topFloat = 100;

    (function (a) {
        a.fn.goToTop = function (d) {
            var e = a(window);
            var c = a(this);
            var b = (e.scrollTop() > d) ? true : false;
            if (b) {
                c.stop().show()
            } else {
                c.stop().hide()
            }
            return this
        };
        a.fn.headerFloat = function () {
            var b = function (c) {
                a(window).on("scroll resize", function () {
                    var e = a(this).scrollTop();
                    _top_ = c.position().top;
                    if (e > topFloat) {
                        a("#floatTables").css({position: "absolute", top: e - 115});
                        a("#floatTables").css("z-index", "1900");
                        a("#floatTables").css("background-color", "#F0F5F8");
                        a("#floatTables").show();
                    } else {
                        a("#floatTables").css({
                            top: _top_
                        });
                        a("#floatTables").hide();
                    }
                })
            };
            return a(this).each(function () {
                b(a(this))
            })
        }
    })(jQuery);

    jQuery(document).ready(function () {
        jQuery("#tr_floats").headerFloat();
    });

    //查询
    function toSearchResult() {
        load();
        jQuery("#statusType").val(jQuery("input[name='statusTypeCon']:checked").val());
        jQuery("#proId").val(jQuery("#proIdCon").val());
        jQuery("#build").val(jQuery("#buildCon").val());
        jQuery("#floor").val(jQuery("#floorCon").val());
        jQuery("#type").val(jQuery("#typeCon").val());
        jQuery("#cmptNo").val(jQuery("#cmptNoCon").val());
        jQuery("#Estate").val('ok');

        document.form1.action = "trade_summarize.php";
        document.form1.target = "_self";
        document.form1.submit();
    }


    //导出数据
    function toExport() {
        var proId = jQuery("#proIdCon").val();
        var build = jQuery("#buildCon").val();
        var type = jQuery("#typeCon").val();

        if (proId != '' && build != '' && type != '') {
            document.form1.action = "trade_summarize_download.php?proId=" + proId + "&build=" + build + "&type=" + type;
            document.form1.target = "_blank";
            document.form1.submit();
        } else {
            layer.msg('请选择相关项目及楼栋', function () {
            });
        }

    }

    function Changes(e) {
        load();
        switch (e) {
            case 'proId':
                jQuery("#proId").val(jQuery("#proIdCon").val());
                jQuery("#build").val("");
                jQuery("#floor").val("");
                jQuery("#type").val("");
                jQuery("#cmptNo").val("");
                jQuery("#Estate").val("");
                break;
            case 'build':
                jQuery("#proId").val(jQuery("#proIdCon").val());
                jQuery("#build").val(jQuery("#buildCon").val());
                jQuery("#floor").val("");
                jQuery("#type").val("");
                jQuery("#cmptNo").val("");
                jQuery("#Estate").val("");
                break;
            case 'floor':
                jQuery("#proId").val(jQuery("#proIdCon").val());
                jQuery("#build").val(jQuery("#buildCon").val());
                jQuery("#floor").val(jQuery("#floorCon").val());
                jQuery("#type").val("");
                jQuery("#cmptNo").val("");
                jQuery("#Estate").val("");
                break;
            case 'type':
                jQuery("#proId").val(jQuery("#proIdCon").val());
                jQuery("#build").val(jQuery("#buildCon").val());
                jQuery("#floor").val(jQuery("#floorCon").val());
                jQuery("#type").val(jQuery("#typeCon").val());
                jQuery("#cmptNo").val("");
                jQuery("#Estate").val("");
                break;
            case 'cmptNo':
                jQuery("#proId").val(jQuery("#proIdCon").val());
                jQuery("#build").val(jQuery("#buildCon").val());
                jQuery("#floor").val(jQuery("#floorCon").val());
                jQuery("#type").val(jQuery("#typeCon").val());
                jQuery("#cmptNo").val(jQuery("#cmptNoCon").val());
                jQuery("#Estate").val("");
                break;


        }
        RefreshPage("trade_summarize");

    }
</script>

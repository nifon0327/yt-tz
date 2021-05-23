<?php
defined('IN_COMMON') || include '../basic/common.php';

include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=15;
//$tableMenuS=1490;
ChangeWtitle("$SubCompany 钢筋");
$funFrom="trade_steel";
$nowWebPage=$funFrom."_read";
//$sumCols="4";		//求和列
$Th_Col="选项|60|序号|50|原始顺序号|60|项目编号|55|项目名称|100|构件类型|60|楼栋编号|60|楼层编号|60|构件编号|60|产品条码|60|审核状态|70|长<br/>(mm)|60|宽<br/>(mm)|60|厚<br/>(mm)|60|钢筋|60";
$Pagination=$Pagination==""?1:$Pagination;  //分页标志 0-不分页 1-分页
$Page_Size = 500; //每页数量
$ActioToS="8,9"; //功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消

$d=anmaIn("design/phpExcelReader/",$SinkOrder,$motherSTR);
$f=anmaIn("trade_sample.xls",$SinkOrder,$motherSTR);

//检索条件
// 0-已审核 1-未审核 2-在生产 3-已生产
$statusType = 1;
if (isset($_POST["statusType"])) {
    $statusType = $_POST["statusType"];
}

//项目ID
$proId = "";
if ($_GET["proId"]) {
    $proId = $_GET["proId"];
}
if ($_POST["proId"]) {
    $proId = $_POST["proId"];
}

$Estate = "";
if ($_GET["proId"] || $_POST["proId"] ) {
    //echo "setproid=",$proId;
    $mySql = "select Estate from $DataIn.trade_info where TradeId = $proId";
    $myResult = mysql_query($mySql, $link_id);
    if($myResult  && $myRow = mysql_fetch_array($myResult)){

        $Estate = $myRow["Estate"];
        if ($Estate >= 0 && $Estate <= 6 ) {
            //未审核
            $statusType = 1;
        } else if ($Estate >= 7 && $Estate <= 9 ) {
            //已审核
            $statusType = 0;
        } else if ($Estate == 10) {
            $statusType = 2;
        } else {
            $statusType = 3;
        }
    }
}

$titles = array();
$specs = array();
$sizes = array();

//项目数据检索
$mySql="SELECT a.Id, a.Forshort, b.Titles,b.Specs, b.Sizes, b.BuildingNo, c.Estate, b.TypeName FROM $DataIn.trade_object a
LEFT JOIN $DataIn.trade_steel_data b on a.Id = b.TradeId
INNER JOIN $DataIn.trade_info c on a.Id = c.TradeId ";

if ($statusType == 0) {
    $mySql .= " AND C.Estate in (7, 8, 9)";
} else if ($statusType == 1) {
    $mySql .= " AND C.Estate in (0, 1, 2, 3, 4, 5, 6)";
} else if ($statusType == 2) {
    $mySql .= " AND C.Estate= 10";
} else if ($statusType == 3) {
    $mySql .= " AND C.Estate= 11";
}

/*$mySql .= " where a.ObjectSign = 2 order by a.Date";

//echo  $mySql;
$myResult = mysql_query($mySql, $link_id);

$tradeList = array();
if($myResult  && $myRow = mysql_fetch_array($myResult)){
    do{
        $tradeList[] = $myRow;
        
        if ($proId) {
            if ($proId == $myRow["Id"] ) {
                $titles = json_decode( $myRow["Titles"]);
                $specs = json_decode( $myRow["Specs"]);
                $sizes = json_decode( $myRow["Sizes"]);
                $Estate = $myRow["Estate"];
            }
        } else {
            $proId = $myRow["Id"];
            $titles = json_decode( $myRow["Titles"]);
            $specs = json_decode( $myRow["Specs"]);
            $sizes = json_decode( $myRow["Sizes"]);
            $Estate = $myRow["Estate"];
        }
        
    }while ($myRow = mysql_fetch_array($myResult));
}*/


// 项目取得
$tradeList = array();

$TmySql = $mySql;
$TmySql .= " where a.ObjectSign = 2 GROUP BY a.Id order by a.Date desc  ";
$TmyResult = mysql_query($TmySql, $link_id);
if ($TmyResult && $TmyRow = mysql_fetch_array($TmyResult)) {
    do {
        $tradeList[] = $TmyRow;
        if ($proId) {
            if ($proId == $TmyRow["Id"]) {

            }
        } else {
            $proId = $TmyRow["Id"];
        }
    } while ($TmyRow = mysql_fetch_array($TmyResult));
}

/* 新增栋号 by.lwh 20180403 */
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
    $BmySql = "SELECT DISTINCT a.BuildingNo FROM $DataIn.trade_steel a where a.TradeId = $proId order by a.BuildingNo";
    $BmyResult = mysql_query($BmySql, $link_id);
    if ($BmyResult && $BmyRow = mysql_fetch_array($BmyResult)) {
        do {
            $buildList[] = $BmyRow;
            if ($build) {
                if ($build == $BmyRow["BuildingNo"]) {

                }
            } else {
                $build = $BmyRow["BuildingNo"];
            }
        } while ($BmyRow = mysql_fetch_array($BmyResult));
    }
}

//echo $BmySql;


//构件类型
$type = "";
if ($_POST["type"]) {
    $type = $_POST["type"];
}

// 项目构件类型取得
$typeList = array();
if ($proId) {
    $TmySql="SELECT DISTINCT a.CmptType FROM $DataIn.trade_steel a where a.TradeId = $proId  and a.BuildingNo = '$build'  order by a.CmptType";
    $myResult = mysql_query($TmySql, $link_id);

    if($myResult  && $myRow = mysql_fetch_array($myResult)){
        do{
            $typeList[] = $myRow;
            if ($type) {
                if ($type == $myRow["CmptType"]) {

                }
            } else {
                $type = $myRow["CmptType"];
            }
        }while ($myRow = mysql_fetch_array($myResult));
    }
}



$mySql .= " WHERE b.TradeId = $proId AND BuildingNo = '$build' AND TypeName = '$type' ";

//echo  $mySql;
$myResult = mysql_query($mySql, $link_id);

//$tradeList = array();
if($myResult  && $myRow = mysql_fetch_array($myResult)){
    do{
        //$tradeList[] = $myRow;

        if ($proId && $build) {
            if ($proId == $myRow["Id"] && $build == $myRow["BuildingNo"]) {
                $titles = json_decode( $myRow["Titles"]);
                $specs = json_decode( $myRow["Specs"]);
                $sizes = json_decode( $myRow["Sizes"]);
                $Estate = $myRow["Estate"];
            }
        } else {
            //$proId = $myRow["Id"];
            //$build = $myRow["BuildingNo"];
            $titles = json_decode( $myRow["Titles"]);
            $specs = json_decode( $myRow["Specs"]);
            $sizes = json_decode( $myRow["Sizes"]);
            $Estate = $myRow["Estate"];
        }

    }while ($myRow = mysql_fetch_array($myResult));
}


if ($titles) {
    foreach ($titles as $title) {
        $Th_Col .= "|" . $title ."|40";
    }
    $ColsNumber += count($title);
}


//查看时间 0-全部
$period = 0;
if ($_POST["period"]) {
    $period = $_POST["period"];
}

//审核状态
if ($_POST["status"]) {
    $status = $_POST["status"];
}

//楼层
$floor = null;
if ($_POST["floor"]) {
    $floor = $_POST["floor"];
}

// 项目楼层取得
$floorList = array();
if ($proId) {
    $mySql="SELECT DISTINCT a.FloorNo FROM $DataIn.trade_steel a where a.TradeId = $proId order by a.CmptNo";
    $myResult = mysql_query($mySql, $link_id);

    if($myResult  && $myRow = mysql_fetch_array($myResult)){
        do{
            $floorList[] = $myRow;
        }while ($myRow = mysql_fetch_array($myResult));
    }
}

//构件编号
$cmptNo = "";
if ($_POST["cmptNo"]) {
    $cmptNo = $_POST["cmptNo"];
}

?>

<style type="text/css">
    .input_radio1{
        vertical-align: top;
        margin-top: -1.5px;
        margin-left: 20px;
    }
    .select1{
        min-width: 100px;
        height: 25px;
        margin-right: 10px;
        border: 1px solid lightgray;
    }
    .btn_a1{
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
    a.btn_a1:link,a.btn_a1:visited {
        color: #0099FF;
    }
    .input_btn2{
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
    .tds1{
        height: 35px;
    }
    .lable_active{
        font-weight: bold;
    }
</style>
<table border="0" cellspacing="0" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width: 1000px;border: 1px solid #E2E8E8;border-radius:5px;-moz-box-shadow: 0px 0px 10px #c7c7c7; box-shadow: 0px 0px 10px #c7c7c7;margin:10px 10px 10px 0' bgcolor='#FFF' class="div-select">
    <tr>
        <td class="tds1" colspan="2" style="padding-left: 10px;height: 50px">
            <select name="statusTypeCon" id="statusTypeCon" onchange='statusTypeChange()' class=" ">
                <option value="1" <?php if ($statusType == '1') {echo "selected"; }  ?>>未审核</option>
                <option value="0" <?php if ($statusType == '0') {echo "selected"; }  ?>>已审核</option>
                <option value="2" <?php if ($statusType == '2') {echo "selected"; }  ?>>在生产</option>
                <option value="3" <?php if ($statusType == '3') {echo "selected"; }  ?>>已生产</option>
            </select>
            &nbsp;&nbsp;
            <!-- 项目 -->
            <select name='proIdCon' id='proIdCon' onchange='proIdConChange()' class=" ">
                <?php
                foreach ($tradeList as $trade){
                    $Id=$trade["Id"];
                    $Forshort=$trade["Forshort"];
                    echo "<option value='$Id' ", $Id == $proId?"selected":"", ">$Forshort</option>";
                }
                ?>
            </select>
            &nbsp;&nbsp;
            <!-- 栋号-->
            <select name='buildCon' id='buildCon' class=" " onchange='buildConChange()'>
                <?php
                foreach ($buildList as $buildData){
                    $BuildingNo=$buildData["BuildingNo"];
                    echo "<option value='$BuildingNo' ", $BuildingNo == $build?"selected":"", ">$BuildingNo 栋</option>";
                }
                ?>
            </select>
            &nbsp;&nbsp;
            <!-- 构件类型 -->
            <select name='typeCon' id='typeCon' class=" " onchange='typeConConChange()'>
                <?php
                foreach ($typeList as $typeData){
                    $CmptType=$typeData["CmptType"];
                    echo "<option value='$CmptType' ", $CmptType == $type?"selected":"", ">$CmptType</option>";
                }
                ?>
            </select>
            <!--<input name="statusTypeCon" type="radio" id="statusType1" class="input_radio1" value="1" <?php /*if ($statusType == 1) {echo "checked"; } else { echo "onClick='statusTypeChange(1)'";} */?> /><LABEL for="statusType1" <?php /*if ($statusType == 1) {echo "class='lable_active'";} */?> >未审核</LABEL>
      	<input name="statusTypeCon" type="radio" id="statusType0" class="input_radio1" value="0" <?php /*if ($statusType == 0) {echo "checked "; } else { echo "onClick='statusTypeChange(0)'";} */?> /><LABEL for="statusType0" <?php /*if ($statusType == 0) {echo "class='lable_active'";} */?> >已审核</LABEL>
		<input name="statusTypeCon" type="radio" id="statusType2" class="input_radio1" value="2" <?php /*if ($statusType == 2) {echo "checked"; } else { echo "onClick='statusTypeChange(2)'";} */?> /><LABEL for="statusType2" <?php /*if ($statusType == 2) {echo "class='lable_active'";} */?> >在生产</LABEL>
      	<input name="statusTypeCon" type="radio" id="statusType3" class="input_radio1" value="3" <?php /*if ($statusType == 3) {echo "checked"; } else { echo "onClick='statusTypeChange(3)'";} */?> /><LABEL for="statusType3" <?php /*if ($statusType == 3) {echo "class='lable_active'";} */?> >已生产</LABEL>
      	--></td>
        <td>
            <a href='trade_drawing_read.php?proId=<?php echo $proId ?>&build=<?php echo $build ?>' class="btn-confirm">图　纸</a>
            <a href='trade_embedded_read.php?proId=<?php echo $proId ?>&build=<?php echo $build ?>' class="btn-confirm">预 埋 件</a>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="tds1" style="padding-left: 10px;">

            <!-- 时间 -->
            <select name='periodCon' id='periodCon' class="">
                <option value='0' <?php if ($period == 0) echo "selected" ?>>时间选择</option>
                <option value='1' <?php if ($period == 1) echo "selected" ?>>最近7天</option>
                <option value='2' <?php if ($period == 2) echo "selected" ?>>最近15天</option>
                <option value='3' <?php if ($period == 3) echo "selected" ?>>最近30天</option>
                <option value='4' <?php if ($period == 4) echo "selected" ?>>30天前</option>
            </select>

            <!-- 审核状态 -->
            <?php
            if ($statusType == 0) {
                //已审核
                echo "<select name='statusCon' id='statusCon' class=''>";
                echo "<option value='' ", $status == ""?"selected":"", ">状态选择</option>";
                echo "<option value='7' ", $status == "7"?"selected":"", ">已审核通过</option>";
                echo "<option value='8' ", $status == "8"?"selected":"", ">审核不通过</option>";
                echo "<option value='9' ", $status == "9"?"selected":"", ">审核退回</option>";
                echo "</select>";
            } else if ($statusType == 1) {
                //未审核
                echo "<select name='statusCon' id='statusCon' class=''>";
                echo "<option value='' ", $status == ""?"selected":"", ">状态选择</option>";
                echo "<option value='0' ", $status == "0"?"selected":"", ">未提交</option>";
                echo "<option value='1' ", $status == "1"?"selected":"", ">未初校</option>";
                echo "<option value='2' ", $status == "2"?"selected":"", ">未复校</option>";
                echo "<option value='3' ", $status == "3"?"selected":"", ">初校未通过</option>";
                echo "<option value='4' ", $status == "4"?"selected":"", ">复校通过</option>";
                echo "<option value='5' ", $status == "5"?"selected":"", ">复校未通过</option>";
                echo "<option value='6' ", $status == "6"?"selected":"", ">等待审核</option>";
                echo "</select>";
            }
            ?>

            <!-- 楼层-->
            <select name='floorCon' id='floorCon' class="">
                <option value='' <?php if ($floor == null) echo "selected" ?>>楼层选择</option>
                <?php
                foreach ($floorList as $floorData){
                    $FloorNo=$floorData["FloorNo"];
                    echo "<option value='$FloorNo' ", $FloorNo == $floor?"selected":"", ">$FloorNo</option>";
                }
                ?>
            </select>

            <input name='cmptNoCon' type='text' id='cmptNoCon' placeholder="输入构件编号" autocomplete='off' value='<?php echo $cmptNo ?>'/>
            <span type='button' name='Submit' value='查询' class="btn-confirm" onClick='toSearchResult()' >查　询</span>
        </td>
        <td   class="tds1" style="height: 50px;">
            <?php if ($Estate == 0 || $Estate == 3 || $Estate == 5 || $Estate == 8 || $Estate == 9 ) { ?>
                <span type='button' name='button' class="btn-confirm" value='删除' onClick='toDeleteData()' >删　除</span>
                <span type='button' name='button' class="btn-confirm" value='导入数据' onClick='toImportData()' >导入数据</span>
            <?php } ?>
            <span type='button' name='button' class="btn-confirm" value='导出数据' onClick='toExportData()'>导出数据</span>
            <a href="../admin/openorload.php?d=<?php echo $d ?>&f=<?php echo $f ?>&Type=&Action=6"target="download" style="color: #169BD5;margin-left: 5px;margin-right: 5px;">下载模板</a>
        </td>
    </tr>
</table>
<?php
//步骤3：
include "../model/subprogram/read_model_3.php";

//检索条件隐藏值
echo " <input type='hidden' name='statusType' id='statusType' value='$statusType' />";
echo " <input type='hidden' name='proId' id='proId' value='$proId' />";
echo " <input type='hidden' name='build' id='build' value='$build' />";
echo " <input type='hidden' name='type' id='type' value='$type' />";
echo " <input type='hidden' name='period' id='period' value='$period' />";
echo " <input type='hidden' name='status' id='status' value='$status' />";
echo " <input type='hidden' name='floor' id='floor' value='$floor' />";
echo " <input type='hidden' name='cmptNo' id='cmptNo' value='$cmptNo' />";
echo " <input type='hidden' name='Estate' id='Estate' value='$Estate' />";

$SearchRows = "";
if ($statusType == 0) {
    if ($status == "") {
    } else {
        $SearchRows .= " AND C.Estate= $status";
    }
} else if ($statusType == 1) {
    if ($status == "") {
    } else {
        $SearchRows .= " AND C.Estate= $status";
    }
}

/*$Orderby = "order by (a.FloorNo+0) ";*/
$Orderby = "order by a.SN ";

//步骤5：
// 菜单
include "../model/subprogram/read_model_5.php";

//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
//List_Title($Th_Col,"1",1);//($Th_Col, $Sign, $Height)
/////////////////////////////////////////////////////////
$Field = explode("|", $Th_Col);
$Count = count($Field);
$tId = "id='TableHead'";

$tableWidth = 0;
for ($i = 0; $i < $Count; $i = $i + 2) {
    $j = $i;
    $k = $j + 1;
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

    $j = $i;
    $k = $j + 1;
    if (isSafari6() == 0) {
        if ($k == ($Count - 1)) {
            $Field[$k] = "";
        }
    }
    $h = $j + 2;

    if ($i > 26) {
        $TableStr .= "<td width='$Field[$k]' Class='$Class_Temp'>$Field[$j]</td>";
    } else {
        $TableStr .= "<td rowspan='3' width='$Field[$k]' Class='$Class_Temp'>$Field[$j]</td>";
    }
}

$TableStr .= "</tr><tr height='16' class='' align='center' style='background-color: #fff;'>";
$TableStr .= "<td Class='A0001'>规格</td>";
if ($specs) {
    foreach ($specs as $spec) {
        $TableStr .= "<td  Class='A0001'>$spec</td>";
    }
}

$TableStr .= "</tr><tr height='16' class='' align='center' style='background-color: #fff;'>";
$TableStr .= "<td Class='A1101'>下料尺寸</td>";
if ($sizes) {
    foreach ($sizes as $size) {
        $TableStr .= "<td  Class='A1101'>$size</td>";
    }
}
echo "<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' $tId><tr height='16' class='' align='center'>" . $TableStr . "</tr></table>";
echo "<div id='floatTable' class='t-list' style='display: none;'><table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' ><tr height='30' id='tr_float' class='' align='center'>" . $TableStr . "</tr></table></div>";


//A.Estate 不用,使用项目表状态
$mySql="SELECT A.Id,A.SN, A.TradeId, A.CmptType, A.BuildingNo, A.FloorNo, A.CmptNo,
	A.ProdCode, A.Length, A.Width, A.Thick, A.Quantities,
	B.Forshort, C.TradeNo, C.Estate 
FROM $DataIn.trade_steel A
INNER JOIN $DataIn.trade_object B ON A.TradeId = b.Id and b.ObjectSign = 2
INNER JOIN $DataIn.trade_info C ON A.TradeId = C.TradeId $SearchRows";

$mySql .= " WHERE A.TradeId= $proId";

if ($build){
    $mySql .=" AND A.BuildingNo= '$build' ";
}

if ($type) {
    $mySql .= " AND A.CmptType= '$type'";
}
if ($floor) {
    $mySql .= " AND A.FloorNo= '$floor'";
}
if ($cmptNo) {
    $mySql .= " AND A.CmptNo like '%$cmptNo%'";
}

if ($period == 1) {
    //最近7天
    $mySql .= " AND TO_DAYS(NOW()) - TO_DAYS(A.created) <= 7";

} else if ($period == 2) {
    //最近15天
    $mySql .= " AND TO_DAYS(NOW()) - TO_DAYS(A.created) <= 15";

} else if ($period == 3) {
    //最近30天
    $mySql .= " AND TO_DAYS(NOW()) - TO_DAYS(A.created) <= 30";

} else if ($period == 4) {
    //30天前
    $mySql .= " AND TO_DAYS(NOW()) - TO_DAYS(A.created) > 30";
}

$mySql .= " $Orderby";

$i = 1;
//echo $mySql;
//echo $PageSTR;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myResult  && $myRow = mysql_fetch_array($myResult)){
    do{
        $m=1;  //必须

        $Estate=$myRow["Estate"];
        switch($Estate){
            case 0:$Estate="<div class='yellowB'>未提交</div>";
                break;
            case 1:$Estate="<div class='blueB'>未初校</div>";
                break;
            case 2:$Estate="<div class='blueB'>未复校</div>";
                break;
            case 3:$Estate="<div class='redB'>初校未通过</div>";
                break;
            case 4:$Estate="<div class='greenB'>校核通过</div>";
                break;
            case 5:$Estate="<div class='redB'>复校未通过</div>";
                break;
            case 6:$Estate="<div class='blueB'>未审核</div>";
                break;
            case 7:$Estate="<div class='greenB'>审核通过</div>";
                break;
            case 8:$Estate="<div class='redB'>审核不通过</div>";
                break;
            case 9:$Estate="<div class='redB'>审核退回</div>";
                break;
            case 10:$Estate="<div class='blueB'>生产中</div>";
                break;
            case 11:$Estate="<div class='greenB'>生产完成</div>";
                break;
            default:
                $Estate="<div class='redB'>未通过</div>";
                break;
        }

        $ValueArray=array(
            array(0=>$myRow["SN"],      1=>"align='center'"),
            array(0=>$myRow["TradeNo"],      1=>"align='center'"),
            array(0=>$myRow["Forshort"],     1=>"align='center'"),
            array(0=>$myRow["CmptType"],     1=>"align='center'"),
            array(0=>$myRow["BuildingNo"],   1=>"align='center'"),
            array(0=>$myRow["FloorNo"],      1=>"align='center'"),
            array(0=>$myRow["CmptNo"],       1=>"align='center'"),
            array(0=>$myRow["ProdCode"],     1=>"align='center'"),
            array(0=>$Estate,             1=>"align='center'"),
            array(0=>$myRow["Length"],    1=>"align='center'"),
            array(0=>$myRow["Width"],     1=>"align='center'"),
            array(0=>$myRow["Thick"],     1=>"align='center'"),
            array(0=>"数量",     1=>"align='center'")
        );

        $Quantities = json_decode($myRow["Quantities"]);
        if ($Quantities) {
            foreach ($Quantities as $qti) {
                $ValueArray[] = array(0=>$qti,     1=>"align='center'");
            }
        }

        $checkidValue=$myRow["Id"];
        $ChooseOut = "N";
        $myOpration = "<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled>";
        include "../model/subprogram/read_model_6.php";
    }while ($myRow = mysql_fetch_array($myResult));
}
else{
    noRowInfo($tableWidth);
}

//步骤7：
echo '</div>';

$myResult = mysql_query($mySql,$link_id);
if ($myResult ) $RecordToTal= mysql_num_rows($myResult);

pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script >

    topFloat = 150;

    //查询
    function toSearchResult(){
        jQuery("#statusType").val(jQuery("input[name='statusTypeCon']:checked").val());
        jQuery("#proId").val(jQuery("#proIdCon").val());
        jQuery("#build").val(jQuery("#buildCon").val());
        jQuery("#type").val(jQuery("#typeCon").val());
        jQuery("#period").val(jQuery("#periodCon").val());
        jQuery("#status").val(jQuery("#statusCon").val());
        jQuery("#floor").val(jQuery("#floorCon").val());
        jQuery("#cmptNo").val(jQuery("#cmptNoCon").val());

        document.form1.action="trade_steel_read.php";
        document.form1.target = "_self";
        document.form1.submit();
    }

    //导入数据
    function toImportData() {
        var proId = jQuery("#proId").val();
        var r=Math.random();
        window.open("trade_cmpt_add.php?r=" + r + "&fromWebPage=trade_steel_read&type=2&proId=" + proId,"_self","");
    }

    //删除
    function toDeleteData() {
        var choosedRow=0;
        var Ids;

        jQuery('input[name^="checkid"]:checkbox').each(function() {
            if (jQuery(this).prop('checked') ==true) {
                choosedRow=choosedRow+1;
                if (choosedRow == 1) {
                    Ids = jQuery(this).val();
                } else {
                    Ids = Ids + "," + jQuery(this).val();
                }
            }
        });

        if (choosedRow == 0) {
            alert("该操作要求选定记录！");
            return;
        }

        var message=confirm("确定要进行此操作吗？");
        if(message==false){
            return;
        }

        var proId = jQuery("#proId").val();
        document.form1.action="trade_steel_del.php?Ids="+Ids+"&proId="+proId;
        document.form1.target = "_self";
        document.form1.submit();
    }

    //导出数据
    function toExportData() {
        //document.form1.action="trade_steel_toexcel.php";
        //document.form1.target = "_blank";
        document.form1.action="trade_cmpt_export.php?type=2";
        document.form1.target = "_self";
        document.form1.submit();
    }

    function statusTypeChange() {
        jQuery("#statusType").val(jQuery("#statusTypeCon").val());
        jQuery("#proId").val("");
        jQuery("#build").val("");
        jQuery("#type").val("");
        jQuery("#period").val("");
        jQuery("#status").val("");
        jQuery("#floor").val("");
        jQuery("#cmptNo").val("");

        document.form1.action="trade_steel_read.php";
        document.form1.target = "_self";
        document.form1.submit();
    }

    function proIdConChange() {
        jQuery("#proId").val(jQuery("#proIdCon").val());
        jQuery("#build").val("");
        jQuery("#type").val("");
        jQuery("#period").val("");
        jQuery("#status").val("");
        jQuery("#floor").val("");
        jQuery("#cmptNo").val("");

        RefreshPage("trade_steel_read");
    }

    function buildConChange() {
        jQuery("#proId").val(jQuery("#proIdCon").val());
        jQuery("#build").val(jQuery("#buildCon").val());
        jQuery("#type").val("");
        jQuery("#period").val("");
        jQuery("#status").val("");
        jQuery("#floor").val("");
        jQuery("#cmptNo").val("");

        RefreshPage("trade_steel_read");
    }

    function typeConConChange() {
        jQuery("#proId").val(jQuery("#proIdCon").val());
        jQuery("#build").val(jQuery("#buildCon").val());
        jQuery("#type").val(jQuery("#typeCon").val());
        jQuery("#period").val("");
        jQuery("#status").val("");
        jQuery("#floor").val("");
        jQuery("#cmptNo").val("");

        RefreshPage("trade_steel_read");
    }
</script>

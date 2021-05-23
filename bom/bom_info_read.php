<?php
include "../model/modelhead.php";
$From = $From == "" ? "read" : $From;
//需处理参数
$ColsNumber = 17;
//$tableMenuS=1490;
ChangeWtitle("$SubCompany BOM信息");
$funFrom = "bom_info";
$nowWebPage = $funFrom . "_read";
//$sumCols="4";		//求和列
$Th_Col = "选项|60|顺序号|50|项目编号|65|项目名称|100|栋号|50|构件类型|60|配件分类|60|物料编号|60|审核状态|60|配件主分类|65|是否新增|60|名称|120|规格|80|单位|55|数量|80|单价|80|金额|80|本次损耗（%）|70|额定损耗（%）|70|损耗|80|损耗金额|80|总数|80|总额|80|备注|80|昨日计划用量|80|昨日实际用量|80|计划总量|80|累计用量|80|进度|60|计划立方|80|完成立方|80|已采购数量|80|采购金额|100";
$Pagination = $Pagination == "" ? 1 : $Pagination;  //分页标志 0-不分页 1-分页
$Page_Size = 100; //每页数量
$ActioToS = "8,9"; //功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消

//项目初始数据生成
//项目初始数据生成
$mySql = "INSERT INTO $DataIn.bom_object (tradeid)(
        SELECT a.id FROM $DataIn.trade_object a
        inner join $DataIn.trade_info c on a.id = c.TradeId and c.Estate in (10, 11) WHERE a.ObjectSign = 2
        AND NOT EXISTS ( SELECT b.tradeId FROM $DataIn.bom_object b WHERE a.id = b.tradeid ) )";
mysql_query($mySql);

//检索条件
// 0-未审核 1-已审核
$statusType = 0;
if ($_POST["statusType"]) {
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
if ($_POST["$ids"]) {
    $ids = $_POST["$ids"];
}

if ($_GET["proId"] || $_POST["proId"]) {
    $mySql = "select Estate from $DataIn.bom_object where TradeId = $proId";
    $myResult = mysql_query($mySql, $link_id);
    if ($myResult && $myRow = mysql_fetch_array($myResult)) {

        $Estate = $myRow["Estate"];
        if ($Estate == 0 || $Estate == 1) {
            //未审核
            $statusType = 0;
        }
        else {
            $statusType = 1;
        }
    }
}

//项目数据检索
$mySql = "SELECT a.Id, a.Forshort, b.Estate FROM $DataIn.trade_object a 
    inner join $DataIn.bom_object b on a.id = b.tradeid ";
if ($statusType == 0) {
    $mySql .= " AND b.Estate in (0, 1)";
}
else if ($statusType == 1) {
    $mySql .= " AND b.Estate in (2, 3, 4)";
}
$mySql .= "where a.ObjectSign = 2 order by a.Date";
//echo $mySql;

$myResult = mysql_query($mySql, $link_id);

$tradeList = array();
if ($myResult && $myRow = mysql_fetch_array($myResult)) {
    do {
        $tradeList[] = $myRow;

        if ($proId) {
            if ($proId == $myRow["Id"]) {
                $Estate = $myRow["Estate"];
            }
        }
        else {
            $proId = $myRow["Id"];
            $Estate = $myRow["Estate"];
        }

    } while ($myRow = mysql_fetch_array($myResult));
}

//构件类型
$type = "";
if ($_POST["type"]) {
    $type = $_POST["type"];
}
$type = "";
if ($_POST["stype"]) {
    $stufftype = $_POST["stype"];
}
// 项目构件类型取得
$typeList = array();
if ($proId) {
    $mySql = "SELECT DISTINCT a.CmptType FROM $DataIn.bom_info a where a.TradeId = $proId order by a.CmptType";
    $myResult = mysql_query($mySql, $link_id);

    if ($myResult && $myRow = mysql_fetch_array($myResult)) {
        do {
            $typeList[] = $myRow;
        } while ($myRow = mysql_fetch_array($myResult));
    }
}

// 栋号取得
$BuildingNoList = array();
if ($proId) {
    $mySql = "SELECT DISTINCT a.BuildingNo FROM $DataIn.bom_info a where a.TradeId = $proId order by a.BuildingNo ";
    $myResult = mysql_query($mySql, $link_id);

    if ($myResult && $myRow = mysql_fetch_array($myResult)) {
        do {
            $BuildingNoList[] = $myRow;
        } while ($myRow = mysql_fetch_array($myResult));
    }
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
    $mySql = "SELECT DISTINCT a.FloorNo FROM $DataIn.bom_info a where a.TradeId = $proId order by a.CmptNo";
    $myResult = mysql_query($mySql, $link_id);

    if ($myResult && $myRow = mysql_fetch_array($myResult)) {
        do {
            $floorList[] = $myRow;
        } while ($myRow = mysql_fetch_array($myResult));
    }
}

//构件编号
$cmptNo = "";
if ($_POST["cmptNo"]) {
    $cmptNo = $_POST["cmptNo"];
}

//类型名称
$MStuffType = "";
if ($_POST["mtype"]) {
    $MStuffType = $_POST["mtype"];
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
<?php include '../basic/loading.php' ?>
<table border="0" cellspacing="0" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width: 1200px;border: 1px solid #E2E8E8;border-radius:5px;-moz-box-shadow: 0px 0px 10px #c7c7c7; box-shadow: 0px 0px 10px #c7c7c7;margin:10px 10px 10px 0' bgcolor='#FFF' class="div-select">
  <tr>
    <td style="height: 50px">
      <input name="statusTypeCon" type="radio" id="statusType0" class="input_radio1" value="0" <?php if ($statusType == 0) {
          echo "checked";
      }
      else {
          echo "onClick='statusTypeChange(0)'";
      } ?> /><LABEL for="statusType0" <?php if ($statusType == 0) {
            echo "class='lable_active'";
        } ?> >未审核</LABEL>
      <input name="statusTypeCon" type="radio" id="statusType1" class="input_radio1" value="1" <?php if ($statusType == 1) {
          echo "checked";
      }
      else {
          echo "onClick='statusTypeChange(1)'";
      } ?> /><LABEL for="statusType1" <?php if ($statusType == 1) {
            echo "class='lable_active'";
        } ?> >已审核</LABEL>
    </td>
    <td>
      <a href='bom_loss_read.php?proId=<?php echo $proId ?>' class="btn-confirm" style="display:inline-block">损耗信息</a>
      <a href='bom_mould_read.php?proId=<?php echo $proId ?>' class="btn-confirm" style="display:inline-block">模具信息</a>
    </td>
  </tr>
  <tr>
    <td colspan="2" style="padding-left: 10px;height: 50px">
      <!-- 项目 -->
      <select name='proIdCon' id='proIdCon' onchange='ConChange("proId")'>
          <?php
          foreach ($tradeList as $trade) {
              $Id = $trade["Id"];
              $Forshort = $trade["Forshort"];
              echo "<option value='$Id' ", $Id == $proId ? "selected" : "", ">$Forshort</option>";
          }
          ?>
      </select>&nbsp;
      <!-- 栋号 -->
      <select name='BuildingNoCon' id='BuildingNoCon' onchange='ConChange("Build")'>
        <option value='' <?php if ($BuildingNo == "") echo "selected" ?>>全部栋号</option>
          <?php
          foreach ($BuildingNoList as $bNo) {
              $no = $bNo["BuildingNo"];
              echo "<option value='$no' ", $no == $BuildingNo ? "selected" : "", ">$no</option>";
          }
          ?>
      </select>&nbsp;
      <!-- 构件类型 -->
      <select name='typeCon' id='typeCon'>
        <option value='' <?php if ($type == "") echo "selected" ?>>全部类型</option>
          <?php
          foreach ($typeList as $typeData) {
              $CmptType = $typeData["CmptType"];
              echo "<option value='$CmptType' ", $CmptType == $type ? "selected" : "", ">$CmptType</option>";
          }
          ?>
      </select>&nbsp;
      <!-- 配件类型 -->
      <select name='mtypeCon' id='mtypeCon'>
        <option value='' <?php if ($MStuffType == "") echo "selected" ?>>全部类型</option>
        <option value='生产类配件' <?php if ($MStuffType == '生产类配件') echo "selected" ?>>生产类配件</option>
        <option value='采购类配件' <?php if ($MStuffType == '采购类配件') echo "selected" ?>>采购类配件</option>
        <option value='统计类配件' <?php if ($MStuffType == '统计类配件') echo "selected" ?>>统计类配件</option>
      </select>&nbsp;
      <!-- 配件类型 -->
      <select name='stufftype' id='stufftype'>
        <option value='' <?php if ($stufftype == "") echo "selected" ?>>全部配件类型</option>
        <option value='钢材' <?php if ($stufftype == '钢材') echo "selected" ?>>钢材</option>
        <option value='预埋' <?php if ($stufftype == '预埋') echo "selected" ?>>预埋</option>
      </select>&nbsp;
      <!-- 时间 -->
      <select name='periodCon' id='periodCon'>
        <option value='0' <?php if ($period == 0) echo "selected" ?>>时间选择</option>
        <option value='1' <?php if ($period == 1) echo "selected" ?>>最近7天</option>
        <option value='2' <?php if ($period == 2) echo "selected" ?>>最近15天</option>
        <option value='3' <?php if ($period == 3) echo "selected" ?>>最近30天</option>
        <option value='4' <?php if ($period == 4) echo "selected" ?>>30天前</option>
      </select>

      <!-- 审核状态 -->
        <?php
        if ($statusType == 0) {
            //未审核
            echo "<select name='statusCon' id='statusCon' >";
            echo "<option value='' ", $status == "" ? "selected" : "", ">状态选择</option>";
            echo "<option value='0' ", $status == "0" ? "selected" : "", ">未提交</option>";
            echo "<option value='1' ", $status == "1" ? "selected" : "", ">未审核</option>";
            echo "</select>&nbsp;";
        }
        else if ($statusType == 1) {
            //已审核
            echo "<select name='statusCon' id='statusCon' >";
            echo "<option value='' ", $status == "" ? "selected" : "", ">状态选择</option>";
            echo "<option value='2' ", $status == "2" ? "selected" : "", ">已审核通过</option>";
            echo "<option value='3' ", $status == "3" ? "selected" : "", ">已审核未通过</option>";
            echo "<option value='4' ", $status == "4" ? "selected" : "", ">已退回</option>";
            echo "</select>&nbsp;";
        }
        ?>

      <!-- 楼层
			<select name='floorCon' id='floorCon' class="select1">
				<option value='' <?php if ($floor == null) echo "selected" ?>>楼层选择</option>
			<?php
      foreach ($floorList as $floorData) {
          $FloorNo = $floorData["FloorNo"];
          echo "<option value='$FloorNo' ", $FloorNo == $floor ? "selected" : "", ">$FloorNo</option>";
      }
      ?>
			</select>
			-->
      <input name='cmptNoCon' type='text' id='cmptNoCon' placeholder="输入构件编号" autocomplete='off' value='<?php echo $cmptNo ?>'/>
      <span name='Submit' value='查询' class="btn-confirm" style="display:inline-block" onClick='toSearchResult()'>查询</span>
      <div style="float: right;margin-right: 20px">
          <?php if ($Estate == 0 || $Estate == 3 || $Estate == 4) { ?>
            <span name='button' class="btn-confirm" style="display:inline-block;width: auto;" value='初始化BOM' onClick='toInitData()'>初始化BOM</span>
          <?php }
          else {
              echo "<span name='button' class=\"btn-confirm\" style=\"display:inline-block;width: auto;\" value='生成采购单' onClick='showcgsheet(this)'>生成采购单</span>";
          } ?>
        <span name='button' class="btn-confirm" style="display:inline-block;width: auto;" value='导出数据' onClick='toExportData()'>导出数据</span>
          <?php if ($Estate == 0 || $Estate == 3 || $Estate == 4) { ?>
<!--            <span name='button' class="btn-confirm" style="display:inline-block;width: auto;" value='清空数据' onClick='toEmptyData()'>清空数据</span>-->
              <span id="buttonSaveBtn" onclick="Validator.Validate(document.getElementById(document.form1.id),3,'bom_info_del_all')" class="btn-confirm"  style="display:inline-block;width: auto;">清空BOM数据</span>
          <?php } ?>
        <div id='winDialog' style="position:absolute;display:none;z-index:9;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #333;background:#CCC;min-height:500px" onDblClick="closeWinDialog()"></div>
        <div id="mask" style="text-align: center;vertical-align: middle;display: none;position:absolute;width:160%;height:4500%;background-color: rgba(0,0,0,0.2);z-index: 8;top:-30px;left:-40px">　</div>
      </div>
    </td>
  </tr>
</table>
<!--<table>
	<tr>
		<td class="tds1" colspan="2"  >
			<?php /*if ($Estate == 0 || $Estate == 3 || $Estate == 4 ) { */ ?>
			<span type='button' name='button' class="btn-confirm" style="display:inline-block;width: auto;" value='初始化BOM' onClick='toInitData()' >初始化BOM</span>
			<?php /*} */ ?>
			<span type='button' name='button' class="btn-confirm" style="display:inline-block;width: auto;" value='导出数据' onClick='toExportData()' >导出数据</span>
			<?php /*if ($Estate == 0 || $Estate == 3 || $Estate == 4 ) { */ ?>
            <span name='button' class="btn-confirm" style="display:inline-block;width: auto;" value='清空数据' onClick='toEmptyData()' >清空数据</span>
			<?php /*} */ ?>
		</td>
    </tr>
</table>-->
<?php
//步骤3：
include "../model/subprogram/read_model_3.php";

//检索条件隐藏值
echo " <input type='hidden' name='statusType' id='statusType' value='$statusType' />";
echo " <input type='hidden' name='proId' id='proId' value='$proId' />";
echo " <input type='hidden' name='BuildingNo' id='BuildingNo' value='$BuildingNo' />";
echo " <input type='hidden' name='type' id='type' value='$type' />";
echo " <input type='hidden' name='period' id='period' value='$period' />";
echo " <input type='hidden' name='status' id='status' value='$status' />";
echo " <input type='hidden' name='floor' id='floor' value='$floor' />";
echo " <input type='hidden' name='cmptNo' id='cmptNo' value='$cmptNo' />";
echo " <input type='hidden' name='Estate' id='Estate' value='$Estate' />";
echo " <input type='hidden' name='mtype' id='mtype' value='$MStuffType' />";
echo " <input type='hidden' name='stype' id='stype' value='$stufftype' />";
echo " <input type='hidden' name='ids' id='ids' value='$ids' />";

$SearchRows = " AND a.TradeId= $proId";

if ($status == "") {
}
else {
    $SearchRows .= " AND d.Estate= $status";
}
if ($BuildingNo) {
    $SearchRows .= " AND a.BuildingNo= '$BuildingNo'";
}
if ($type) {
    $SearchRows .= " AND a.CmptType= '$type'";
}
if ($floor) {
    $SearchRows .= " AND b.FloorNo= '$floor'";
}
if ($cmptNo) {
    $SearchRows .= " AND a.MaterNo like '%$cmptNo%'";
}

if ($MStuffType) {
    $SearchRows .= " AND MStuffType= '$MStuffType'";
}
if ($stype) {
    $SearchRows .= " AND e.TypeName= '$stype'";
}
if ($period == 1) {
    //最近7天
    $SearchRows .= " AND TO_DAYS(NOW()) - TO_DAYS(A.created) <= 7";

}
else if ($period == 2) {
    //最近15天
    $SearchRows .= " AND TO_DAYS(NOW()) - TO_DAYS(A.created) <= 15";

}
else if ($period == 3) {
    //最近30天
    $SearchRows .= " AND TO_DAYS(NOW()) - TO_DAYS(A.created) <= 30";

}
else if ($period == 4) {
    //30天前
    $SearchRows .= " AND TO_DAYS(NOW()) - TO_DAYS(A.created) > 30";
}

$Orderby = "order by a.CmptType, a.Id ";

//步骤5：
// 菜单
include "../model/subprogram/read_model_5.php";

//步骤6：需处理数据记录处理
$i = 1;
$j = ($Page - 1) * $Page_Size + 1;
List_Title($Th_Col, "1", 1);
//A.Estate 不用,使用项目表状态
$mySql = "select distinct a.Id, a.TradeId, a.buildingNo,a.CmptType, e.TypeName as StuffType, sd.StuffEname as MaterNo, f.TypeName as MStuffType, a.IsNew, sd.StuffCname as MaterName,
a.Spec, a.Unit, a.Quantity, sd.Price, a.Total, a.Loss, a.Remark,
b.TradeNo, c.Forshort, d.Estate, su.name as unitname,sl.ThisStd,sl.PcStd,sd.StuffId,a.CmptTypeId
from $DataIn.bom_info a
LEFT JOIN $DataIn.trade_info b on a.TradeId = b.TradeId
LEFT JOIN $DataIn.trade_object c on c.id = a.TradeId
INNER JOIN $DataIn.bom_object d on d.TradeId = a.TradeId
LEFT JOIN $DataIn.stufftype e on a.StuffTypeId = e.TypeId 
LEFT JOIN $DataIn.stuffmaintype f on a.MStuffTypeId = f.id
LEFT JOIN $DataIn.stuffdata sd on a.MaterNo = sd.StuffId
LEFT JOIN $DataIn.stuffunit su on sd.unit = su.id
LEFT JOIN $DataIn.stuff_loss sl on sd.StuffId = sl.StuffId
where 1 $SearchRows $Orderby";
//echo $mySql;
//echo $mySql.$MStuffType;
$myResult = mysql_query($mySql . " $PageSTR", $link_id);
if ($myResult && $myRow = mysql_fetch_array($myResult)) {
    do {
        $m = 1;  //必须

        $Estate = $myRow["Estate"];
        switch ($Estate) {
            case 0:
                $Estate = "<div class='yellowB'>未提交</div>";
                break;
            case 1:
                $Estate = "<div class='blueB'>未审核</div>";
                break;
            case 2:
                $Estate = "<div class='greenB'>审核通过</div>";
                break;
            case 3:
                $Estate = "<div class='redB'>审核不通过</div>";
                break;
            case 4:
                $Estate = "<div class='redB'>审核退回</div>";
                break;
            default:
                $Estate = "<div class='redB'>未通过</div>";
                break;
        }
        $Quantity = $myRow["Quantity"];

        $unitname = $myRow["unitname"];
        if ($unitname == "") {
            $unitname = $myRow["Unit"];
        }

        $ValueArray = array(
            array(0 => $myRow["TradeNo"], 1 => "align='center'"),
            array(0 => $myRow["Forshort"], 1 => "align='center'"),
            array(0 => $myRow["buildingNo"], 1 => "align='center'"),
            array(0 => $myRow["CmptType"], 1 => "align='center' name='CmptType'"),
            array(0 => $myRow["StuffType"], 1 => "align='center'"),
            array(0 => $myRow["MaterNo"], 1 => "align='center'"),
            array(0 => $Estate, 1 => "align='center'"),
            array(0 => $myRow["MStuffType"], 1 => "align='center'"),
            array(0 => $myRow["IsNew"], 1 => "align='center'"),
            array(0 => $myRow["MaterName"], 1 => "align='center'"),
            array(0 => $myRow["Spec"], 1 => "align='center'"),
            array(0 => $unitname, 1 => "align='center'"),
            array(0 => round($Quantity, 3), 1 => "align='center' name='Quantity'"),
            array(0 => $myRow["Price"], 1 => "align='center'"),
            array(0 => round($Quantity * $myRow["Price"], 3), 1 => "align='center'"),
            array(0 => $myRow["ThisStd"], 1 => "align='center' contentEditable='true' onblur='updateLoss(this)'"),
            array(0 => $myRow["PcStd"], 1 => "align='center'"),
            array(0 => $myRow["Loss"], 1 => "align='center' name='Loss'"),
            array(0 => round($myRow["Price"] * $myRow["Loss"], 3), 1 => "align='center'"),
            array(0 => round($Quantity + $myRow["Loss"], 3), 1 => "align='center'"),
            array(0 => round(($Quantity + $myRow["Loss"]) * $myRow["Price"], 3), 1 => "align='center'"),
            array(0 => $myRow["Remark"], 1 => "align='center'"),
            array(0 => '', 1 => "align='center'"),
            array(0 => '', 1 => "align='center'"),
            array(0 => '', 1 => "align='center'"),
            array(0 => '', 1 => "align='center'"),
            array(0 => '', 1 => "align='center'"),
            array(0 => '', 1 => "align='center'"),
            array(0 => '', 1 => "align='center'"),
            array(0 => '', 1 => "align='center'"),
            array(0 => '', 1 => "align='center'"),
            array(0 => $myRow["StuffId"], 1 => "align='center' name='StuffId' style='display:none'"),
            array(0 => $myRow["CmptTypeId"], 1 => "align='center' name='CmptTypeId' style='display:none'"),

        );

        $checkidValue = $myRow["Id"];
        $ChooseOut = "N";
        $myOpration = "<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled>";
        include "../model/subprogram/read_model_6.php";
    } while ($myRow = mysql_fetch_array($myResult));
}
else {
    noRowInfo($tableWidth);
}

//步骤7：
echo '</div>';
$myResult = mysql_query($mySql, $link_id);
if ($myResult) $RecordToTal = mysql_num_rows($myResult);
pBottom($RecordToTal, $i - 1, $Page, $Pagination, $Page_Size, $timer, $Login_WebStyle, $tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script src='../cjgl/cj_function.js' type=text/javascript></script>
<script src="../plugins/layer/layer.js" type="text/javascript"></script>
<script>
topFloat = 150;

//查询
function toSearchResult() {
    jQuery("#statusType").val(jQuery("input[name='statusTypeCon']:checked").val());
    jQuery("#proId").val(jQuery("#proIdCon").val());
    jQuery("#BuildingNo").val(jQuery("#BuildingNoCon").val());
    jQuery("#type").val(jQuery("#typeCon").val());
    jQuery("#period").val(jQuery("#periodCon").val());
    jQuery("#status").val(jQuery("#statusCon").val());
    jQuery("#floor").val(jQuery("#floorCon").val());
    jQuery("#cmptNo").val(jQuery("#cmptNoCon").val());
    jQuery("#mtype").val(jQuery("#mtypeCon").val());
    jQuery("#stype").val(jQuery("#stufftype").val());
    document.form1.action = "bom_info_read.php";
    document.form1.target = "_self";
    document.form1.submit();
}

// 损耗
function updateLoss(e) {
    var ProId = jQuery("#proIdCon").val();
    var loss = parseFloat(jQuery(e).text());
    if (loss > 20) {
        alert("输入的损耗值不能超过20%");
        jQuery(e).text('0');
        return;
    }
    if (loss < 0) {
        alert("输入的损耗值不能小于0%");
        jQuery(e).text('0');
        return;
    }
    if (isNumber(loss) === false) {
        alert("请输入有效的损耗值！");
        jQuery(e).text('0');
        return;
    }

    // 获取相关数据
    var StuffId = jQuery(e).parent().find('td[name="StuffId"]').text();
    var CmptTypeId = jQuery(e).parent().find('td[name="CmptTypeId"]').text();
    var CmptType = jQuery(e).parent().find('td[name="CmptType"]').text();
    jQuery.ajax({
        url: 'update_stuff_loss.php',
        type: 'post',
        data: {
            StuffId: StuffId,
            CmptTypeId: CmptTypeId,
            Loss: loss,
            CmptType: CmptType
        },
        dataType: 'json',
        success: function (rlt) {
            if (rlt === true) {
                var Quantity = jQuery(e).parent().find('td[name="Quantity"]').text();
                var change = (Quantity * loss / 100).toFixed(2);
                jQuery(e).parent().find('td[name="Loss"]').text(change);
                jQuery.ajax({
                    url: 'update_stuff_loss.php',
                    type: 'post',
                    data: {
                        flag: 1,
                        StuffId: StuffId,
                        CmptTypeId: CmptTypeId,
                        change: change,
                        ProId: ProId
                    },
                    dataType: 'json',
                    success: function (rlt) {
                        if (rlt === true) {
                            alert('修改成功! ');
                        }
                    }

                })

            }
        }

    })
}

function isNumber(val) {
    var regPos = /^\d+(\.\d+)?$/; //非负浮点数
    if (regPos.test(val)) {
        return true;
    } else {
        return false;
    }
}

//初始化BOM
function toInitData() {
    var proId = jQuery("#proIdCon").val();

    if (proId == null) {
        alert("请选择初始化项目!");
        return;
    }

    var message = confirm("确定要进行此操作吗？");
    if (message == false) {
        return;
    }

    jQuery.ajax({
        url: 'bom_info_init.php',
        type: 'post',
        data: {
            id: proId
        },
        dataType: 'json',
        beforeSend: function () {
            jQuery('.response').show();
        },
        success: function (result) {
          console.log(result);
            if (result.rlt) {
                // window.location.reload();
                alert("测试");
            } else {
                if (result.msg) {
                    alert(result.msg);
                } else {
                    alert("初始化BOM操作有误!");
                }
            }
        }
    }).done(function () {
        jQuery('.response').hide();
        return false;
    });

    setTimeout(function () {
    }, 180000);
    var flag = 0;
    var getResponse = setInterval(function () {
        jQuery.ajax({
            url: 'bom_init_response.php',
            timeout: 3000,
            type: 'post',
            async: false,
            data: {
                proId: proId
            },
            beforeSend: function () {
                if (flag === 1) {
                    clearInterval(getResponse);
                }
            },
            dataType: 'json',
            success: function (result) {
                if (result.rlt === true) {
                    flag = 1;
                    jQuery('.response').hide();
                    window.location.reload();
                } else {
                    flag = 1;
                    jQuery('.response').hide();
                    window.location.reload();
                }
            }
        });
    }, 60000);
}

//清空
function toEmptyData() {
    var message = confirm("确定要进行此操作吗？");
    if (message == false) {
        return;
    }

    var proId = jQuery("#proIdCon").val();

    if (proId == null || proId == "") {
        alert("请选择要清空的项目");
        return;
    }

    jQuery.ajax({
        url: 'bom_info_del.php',
        type: 'post',
        data: {
            id: proId
        },
        dataType: 'json',
        beforeSend: function () {
            //$('#LoginMsg').html('登入中，請稍後...').show();
        },
        success: function (result) {
            if (result.rlt) {
                alert("BOM信息已清空!");
                window.location.reload();
            } else {
                window.location.reload();
            }
        }
    }).done(function () {
        //$('#LoginMsg').html('').hide();
    });
}

//导出数据
function toExportData() {
    document.form1.action = "bom_data_export.php?type=1";
    document.form1.target = "_self";
    document.form1.submit();
}

function statusTypeChange(statusType) {
    jQuery("#statusType").val(statusType);
    jQuery("#proId").val("");
    jQuery("#type").val("");
    jQuery("#period").val("");
    jQuery("#status").val("");
    jQuery("#floor").val("");
    jQuery("#cmptNo").val("");

    document.form1.action = "bom_info_read.php";
    document.form1.target = "_self";
    document.form1.submit();
}

function ConChange(e) {

    switch (e)
    {
        case 'proId':
            jQuery("#proId").val(jQuery("#proIdCon").val());
            jQuery("#BuildingNo").val("");
            jQuery("#type").val("");
            jQuery("#period").val("");
            jQuery("#status").val("");
            jQuery("#floor").val("");
            jQuery("#cmptNo").val("");
            break;
        case 'Build':
            jQuery("#proId").val(jQuery("#proIdCon").val());
            jQuery("#BuildingNo").val(jQuery("#BuildingNoCon").val());
            jQuery("#period").val("");
            jQuery("#status").val("");
            jQuery("#floor").val("");
            jQuery("#cmptNo").val("");
            break;
    }

    RefreshPage("bom_info_read");
}

function showcgsheet(e) {
    var num = 1;
    var Ids = '';
    jQuery('input[name^="checkid"]:checked').each(function () {
        if (num === 1) {
            Ids = jQuery(this).val();

        } else {
            Ids = Ids + '|' + jQuery(this).val();
        }
        num++;
    });
    if (num === 1) {
        layer.confirm('该操作要求选定记录！', {
            btn: ['确定'] //按钮
            , icon: 2
            ,offset: '150px'
        }, function (index) {
            layer.close(index);
        });
        return;
    }

    layer.open({
        type: 2,
        title: '生成采购单',
        area: ['1000px', '650px'],
        btn: ['确定', '取消'],
        fixed: false, //不固定
        // maxmin: true,
        offset: '100px',
        content: 'other_cgsheet.php?Ids=' + Ids,
        success: function (layero) {
            layero.find('.layui-layer-btn').css('text-align', 'center')
        },
        yes: function (index) {
            var num = 1;
            var value = '';
            var $table = layer.getChildFrame('.ListTable', index);
            $table.find('input[name="need"]').each(function () {
            // var need = jQuery('input[name="need"]');
            //
            // need.each(function(){
                if (num === 1){
                    value = jQuery(this).val();

                }else{
                    value = value+'^^'+jQuery(this).val();
                }
                num++;
            });

            // jQuery.ajax({
            //     url: 'do_other_cgsheet.php', // BOM采购
            //     data: {
            //         value: value
            //     },
            //     type: 'post',
            //     dataType: 'json'
            // });

            var formFile = new FormData();
            formFile.append("value", value);

            var ajax = InitAjax();
            ajax.open("POST", 'other_cgsheet_nobom.php', true);
            ajax.onreadystatechange = function (result) {
                if (ajax.readyState == 4 && ajax.status == 200) {
                    // var str = ajax.responseText.replace(/\s+/g, "");

                    if (jQuery.trim(ajax.responseText) == "Y") {

                        layer.confirm('新增采购单成功！', {
                            btn: ['确定'] //按钮
                            , icon: 1
                            ,offset: '150px'
                        }, function (index) {
                            layer.closeAll(index);
                            RefreshPage("<?php echo $nowWebPage; ?>")
                        })

                    } else {
                        layer.confirm(jQuery.trim(ajax.responseText), {
                            btn: ['确定'] //按钮
                            , icon: 2
                            ,offset: '150px'
                        }, function (index) {
                            layer.closeAll(index);
                            RefreshPage("<?php echo $nowWebPage; ?>")
                        })
                    }
                }
            };
            ajax.send(formFile);

        },
        btn2: function (index) {//layer.alert('aaa',{title:'msg title'});  ////点击取消回调
            layer.close(index);
        }

    });


    // openWinDialogWithParas(e, "other_cgsheet.php", 800, 600, 'left', Ids);
    // jQuery('#mask').show();
}

function doOtherCgsheet() {
    jQuery('.response').show();
    var need = jQuery('input[name="need"]');
    var num = 1;
    var value = '';
    need.each(function(){
        if (num === 1){
            value = jQuery(this).val();

        }else{
            value = value+'^^'+jQuery(this).val();
        }
       num++;
    });
    jQuery.ajax({
        url: 'do_other_cgsheet.php',
        data: {
            value: value
        },
        type: 'post',
        dataType: 'json'
    });
    window.location.reload();
}

function changeTotal(e){
  var need = jQuery(e).parent().find("input[name='need']");
  var val = need.val();
  var ehtml = (e.innerText);
  var total = val.split('|');
  total.splice(1,1,ehtml);
  need.val(total.join('|'));
  var price = total.splice(2,1);
  var totalPrice = parseFloat(ehtml)*parseFloat(price);
  jQuery(e).next().html(totalPrice.toFixed(3));
  // alert('修改成功！');

}

</script>

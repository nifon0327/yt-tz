<?php
include "../model/modelhead.php";
$From = $From == "" ? "read" : $From;
//需处理参数
$ColsNumber = 1000;
//$tableMenuS=1490;
ChangeWtitle("$SubCompany 模具信息");
$funFrom = "bom_mould";
$nowWebPage = $funFrom . "_read";
//$sumCols="4";		//求和列
//$Th_Col = "选项|60|顺序号|50|项目编号|55|项目名称|65|模具类别|55|模具编号|60|制作数量|55|已完成数量|70|共模比|55|长(mm)|60|宽(mm)|60|台车号|55|楼栋号|60|楼层号|60|构件编号|70";
$Th_Col = "选项|60|顺序号|50|项目编号|55|项目名称|65|模具类别|55|模具编号|60|制作数量|55|已完成数量|70|共模比|55|长(mm)|60|宽(mm)|60|楼栋号|60|楼层号|60|构件编号|70";
$Pagination = $Pagination == "" ? 1 : $Pagination;  //分页标志 0-不分页 1-分页
$Page_Size = 100; //每页数量
$ActioToS = "8,9"; //功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消

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

//模具类别
$type = "";
if ($_POST["type"]) {
    $type = $_POST["type"];
}

// 项目构件类型取得
$typeList = array();
if ($proId) {
    $mySql = "SELECT DISTINCT a.MouldCat FROM $DataIn.bom_mould a where a.TradeId = $proId order by a.MouldCat";
    $myResult = mysql_query($mySql, $link_id);

    if ($myResult && $myRow = mysql_fetch_array($myResult)) {
        do {
            $typeList[] = $myRow;
        } while ($myRow = mysql_fetch_array($myResult));
    }
}

//审核状态
if ($_POST["status"]) {
    $status = $_POST["status"];
}

//构件编号
$mouldNo = "";
if ($_POST["mouldNo"]) {
    $mouldNo = $_POST["mouldNo"];
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
</style>
<table border="0" cellspacing="0" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width: 1000px;border: 1px solid #E2E8E8;border-radius:5px;-moz-box-shadow: 0px 0px 10px #c7c7c7; box-shadow: 0px 0px 10px #c7c7c7;margin:10px 10px 10px 0' bgcolor='#FFF' class="div-select">
  <tr>
    <td class="tds1" style="padding-left: 10px;height: 50px">
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
    <td style="height: 50px">
      <a href='bom_info_read.php?proId=<?php echo $proId ?>' class="btn-confirm" style="display:inline-block;width: auto">BOM信息</a>
      <a href='bom_loss_read.php?proId=<?php echo $proId ?>' class="btn-confirm" style="display:inline-block">损耗信息</a>
    </td>
  </tr>
  <tr>
    <td class="tds1" colspan="2" style="padding-left: 10px;height: 50px">
      <!-- 项目 -->
      <select name='proIdCon' id='proIdCon' onchange='proIdConChange()'>
          <?php
          foreach ($tradeList as $trade) {
              $Id = $trade["Id"];
              $Forshort = $trade["Forshort"];
              echo "<option value='$Id' ", $Id == $proId ? "selected" : "", ">$Forshort</option>";
          }
          ?>
      </select>
      <!-- 构件类型 -->
      <select name='typeCon' id='typeCon'>
        <option value='' <?php if ($type == "") echo "selected" ?>>全部类型</option>
          <?php
          foreach ($typeList as $typeData) {
              $MouldType = $typeData["MouldCat"];
              echo "<option value='$MouldType' ", $MouldType == $type ? "selected" : "", ">$MouldType</option>";
          }
          ?>
      </select>

      <!-- 审核状态 -->
        <?php
        if ($statusType == 0) {
            //未审核
            echo "<select name='statusCon' id='statusCon' >";
            echo "<option value='' ", $status == "" ? "selected" : "", ">状态选择</option>";
            echo "<option value='0' ", $status == "0" ? "selected" : "", ">未提交</option>";
            echo "<option value='1' ", $status == "1" ? "selected" : "", ">未审核</option>";
            echo "</select>";
        }
        else if ($statusType == 1) {
            //已审核
            echo "<select name='statusCon' id='statusCon' >";
            echo "<option value='' ", $status == "" ? "selected" : "", ">状态选择</option>";
            echo "<option value='2' ", $status == "2" ? "selected" : "", ">已审核通过</option>";
            echo "<option value='3' ", $status == "3" ? "selected" : "", ">已审核未通过</option>";
            echo "<option value='4' ", $status == "4" ? "selected" : "", ">已退回</option>";
            echo "</select>";
        }
        ?>

      <input name='mouldNoCon' type='text' id='mouldNoCon' placeholder="输入模具编号" autocomplete='off' value='<?php echo $mouldNo ?>' style="width:100px">
      <input name='carNoCon' type='text' id='carNoCon' placeholder="台车号" autocomplete='off' value='<?php echo $carNo ?>' style="width:100px;height: 25px;">
      <span type='button' name='Submit' value='查询' class="btn-confirm" style="display:inline-block" onClick='toSearchResult()'>查询</span>
     <?php //echo "<span type='button' name='button' class='btn-confirm' value='修改台车号' onClick='setTrolleyId(this)' style='width:80px'>修改台车号</span> <span type='button' name='button' class='btn-confirm' value='布模信息导入' onClick='batchSetTrolley(this)' style='width:100px'>布模信息导入</span>" ?>
      <div style="float: right;margin-right: 20px">
        <span type='button' name='button' class="btn-confirm" style="display:inline-block" value='导出数据' onClick='toExportData()'>导出数据</span>
          <?php if ($Estate == 0 || $Estate == 3 || $Estate == 4) { ?>
            <span type='button' name='button' class="btn-confirm" style="display:inline-block" value='导入数据' onClick='toImportData()'>导入数据</span>
            <span type='button' name='button' class="btn-confirm" style="display:inline-block" value='删除' onClick='toDeleteData()'>删　除</span>
          <?php } ?>
        <div id='winDialog' style="position:absolute;display:none;z-index:9;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #333;background:#CCC;min-height:500px" onDblClick="closeWinDialog()"></div>
        <div id="mask" style="text-align: center;vertical-align: middle;display: none;position:absolute;width:130%;height:11500%;background-color: rgba(0,0,0,0.2);z-index: 8;top:-30px;left:-40px">　</div>
      </div>

    </td>
  </tr>
</table>

<?php
//步骤3：
include "../model/subprogram/read_model_3.php";

//检索条件隐藏值
echo " <input type='hidden' name='statusType' id='statusType' value='$statusType' />";
echo " <input type='hidden' name='proId' id='proId' value='$proId' />";
echo " <input type='hidden' name='type' id='type' value='$type' />";
echo " <input type='hidden' name='status' id='status' value='$status' />";
echo " <input type='hidden' name='mouldNo' id='mouldNo' value='$mouldNo' />";
echo " <input type='hidden' name='carNo' id='carNo' value='$carNo' />";
echo " <input type='hidden' name='Estate' id='Estate' value='$Estate' />";

$SearchRows = " AND a.TradeId= $proId";

//审核状态
if ($status == "") {
}
else {
    $SearchRows .= " AND d.Estate= $status";
}

//模具类别
if ($type) {
    $SearchRows .= " AND a.MouldCat= '$type'";
}

//模具编号
if ($mouldNo) {
    $SearchRows .= " AND a.MouldNo like '%$mouldNo%'";
}

//模具编号
if ($carNo) {
    $SearchRows .= " AND a.TrolleyId like '%$carNo%'";
}

$Orderby = "order by a.Id ";

//步骤5：
// 菜单
include "../model/subprogram/read_model_5.php";

//步骤6：需处理数据记录处理
$i = 1;
$j = ($Page - 1) * $Page_Size + 1;
List_Title($Th_Col, "1", 1);
//A.Estate 不用,使用项目表状态
$mySql = "select a.Id, a.TradeId, a.MouldCat, a.MouldNo, a.ProQty, a.Ratio,
a.Length, a.Width, b.TradeNo, c.Forshort, d.Estate,a.TrolleyId,a.completions 
from $DataIn.bom_mould a
LEFT JOIN $DataIn.trade_info b on a.TradeId = b.TradeId
LEFT JOIN $DataIn.trade_object c on c.id = a.TradeId
INNER JOIN $DataIn.bom_object d on d.TradeId = a.TradeId
where 1 $SearchRows $Orderby";

//echo $mySql;

$myResult = mysql_query($mySql . " $PageSTR", $link_id);
if ($myResult && $myRow = mysql_fetch_array($myResult)) {
    do {
        $m = 1;  //必须

        /* //构件数量
        $MouldNo=$myRow["MouldNo"];
        
        $mouldResult=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS mouldNum FROM $DataIn.trade_drawing a
                WHERE a.TradeId= $proId and a.CmptNo = '$MouldNo' ",$link_id));
        $mouldNum=$mouldResult["mouldNum"];
array(0=>"<a href='../design/trade_drawing_read.php?proId=$proId&cmptNo=$MouldNo'>$mouldNum</a>", 1=>"align='center'"),
                */
        $ValueArray = array(
            array(0 => $myRow["TradeNo"], 1 => "align='center'"),
            array(0 => $myRow["Forshort"], 1 => "align='center'"),
            array(0 => $myRow["MouldCat"], 1 => "align='center'"),
            array(0 => $myRow["MouldNo"], 1 => "align='center'"),
            array(0 => $myRow["ProQty"], 1 => "align='center'"),
            array(0 => $myRow["completions"], 1 => "align='center'"),
            array(0 => $myRow["Ratio"], 1 => "align='center'"),
            array(0 => $myRow["Length"], 1 => "align='center'"),
            array(0 => $myRow["Width"], 1 => "align='center'"),
//            array(0 => $myRow["TrolleyId"], 1 => "align='center'"),
        );

        $checkidValue = $myRow["Id"];
        $ChooseOut = "N";
        $myOpration = "<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled>";

        //构件编号
        $mouldResult = mysql_query("SELECT BuildingNo,FloorNo,CmptNo  FROM $DataIn.bom_mould_data a
                WHERE a.MouldId= $checkidValue order by Id ", $link_id);

        $mouldArr = array();
        if ($mouldResult && $mouldRow = mysql_fetch_array($mouldResult)) {
            do {
                $mouldArr[] = $mouldRow;

            } while ($mouldRow = mysql_fetch_array($mouldResult));
        }

        $rowspan = count($mouldArr);

        /////////////////////////////////
        echo "<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
        //$ColsNumber着色列数
        echo "<tr bgcolor='$theDefaultColor'
        onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);'
        onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'
        onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
        $ColbgColor = $ColbgColor == "" ? "bgcolor='#FFFFFF'" : $ColbgColor;
        $rowheight = $rowspan * 25;

        echo "<td rowspan='$rowspan' class='A0111' width='$Field[$m]' height='$rowheight' align='center' $ColbgColor rowspan='$rowspan'>$myOpration</td>";
        $m = $m + 2;
        echo "<td rowspan='$rowspan' class='A0101' width='$Field[$m]' align='center' $OrderSignColor  title='$WarnRemark'>$j</td>";

        for ($k = 0; $k < count($ValueArray); $k++) {
            if ($ValueArray[$k][4] == "") {
                $m = $m + 2;
                $Value0 = $Value0_Title = $ValueArray[$k][0];
                if (isSafari6() == 0) {
                    if ($m == ($Count - 1)) {
                        $Field[$m] = "";
                    }
                }
                $Value0 = strlen($Value0) <= 0 ? "&nbsp;" : $Value0;
                if ($ValueArray[$k][3] == "...") {
                    $Value0 = "<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0_Title'><NOBR>$Value0</NOBR></DIV>";
                }
                if ($ValueArray[$k][3] == "line") {
                    echo "<td rowspan='$rowspan' class='A0100' width='$Field[$m]' " . $ValueArray[$k][1] . " " . $ValueArray[$k][2] . ">" . $Value0 . "</td>";
                }
                else {
                    echo "<td rowspan='$rowspan' class='A0101' width='$Field[$m]' " . $ValueArray[$k][1] . " " . $ValueArray[$k][2] . ">" . $Value0 . "</td>";
                }
            }
        }

        /*
        //合并单元格
        for ($index = 0; $index < count($mouldArr); $index++) {
            $mouldData = $mouldArr[$index];
            if ($index > 0) {
                echo "<tr>";
            }
 
            $Value0=strlen($mouldData["BuildingNo"])<=0?"&nbsp;": $mouldData["BuildingNo"];
            echo"<td height='25' class='A0101' align='center' width='$Field[$m]'>".$Value0."</td>";
            $Value0=strlen($mouldData["FloorNo"])<=0?"&nbsp;": $mouldData["FloorNo"];
            echo"<td height='25' class='A0101' align='center' width='$Field[$m]'>".$Value0."</td>";
            $Value0=strlen($mouldData["CmptNo"])<=0?"&nbsp;": $mouldData["CmptNo"];
            echo"<td height='25' class='A0101' align='center' width='$Field[$m]'>".$Value0."</td></tr>";
        }
        */

        // 楼栋号
        $m = $m + 2;
        if (isSafari6() == 0) {
            if ($m == ($Count - 1)) {
                $Field[$m] = "";
            }
        }

        echo "<td class='A0101' width='$Field[$m]' align='center'><table width='100%' border='0' cellspacing='0' cellpadding='0'>";
        for ($index = 0; $index < count($mouldArr); $index++) {
            $mouldData = $mouldArr[$index];
            $Value0 = $Value0_Title = $mouldData["BuildingNo"];
            $Value0 = strlen($Value0) <= 0 ? "&nbsp;" : $Value0;
            if ($index == count($mouldArr) - 1) {
                echo "<tr><td height='25' align='center' width='$Field[$m]'>" . $Value0 . "</td></tr>";
            }
            else {
                echo "<tr><td height='25' class='A0100' align='center' width='$Field[$m]'>" . $Value0 . "</td></tr>";
            }
        }
        echo "</table></td>";

        // 楼层号
        $m = $m + 2;
        if (isSafari6() == 0) {
            if ($m == ($Count - 1)) {
                $Field[$m] = "";
            }
        }
        echo "<td class='A0101' width='$Field[$m]' align='center' padding-left='0px'><table width='$Field[$m]' border='0' cellspacing='0' cellpadding='0'>";
        for ($index = 0; $index < count($mouldArr); $index++) {
            $mouldData = $mouldArr[$index];
            $Value0 = $Value0_Title = $mouldData["FloorNo"];
            $Value0 = strlen($Value0) <= 0 ? "&nbsp;" : $Value0;

            if ($index == count($mouldArr) - 1) {
                echo "<tr><td height='25' align='center' width='$Field[$m]'>" . $Value0 . "</td></tr>";
            }
            else {
                echo "<tr><td height='25' class='A0100' align='center' width='$Field[$m]'>" . $Value0 . "</td></tr>";
            }
        }
        echo "</table></td>";

        // 构件编号
        $m = $m + 2;
        if (isSafari6() == 0) {
            if ($m == ($Count - 1)) {
                $Field[$m] = "";
            }
        }
        echo "<td class='A0101' width='$Field[$m]' align='center'><table width='100%' border='0' cellspacing='0' cellpadding='0'>";
        for ($index = 0; $index < count($mouldArr); $index++) {
            $mouldData = $mouldArr[$index];
            $Value0 = $Value0_Title = $mouldData["CmptNo"];
            $Value0 = strlen($Value0) <= 0 ? "&nbsp;" : $Value0;

            if ($index == count($mouldArr) - 1) {
                echo "<tr><td height='25' align='center' width='$Field[$m]'>" . $Value0 . "</td></tr>";
            }
            else {
                echo "<tr><td height='25' class='A0100' align='center' width='$Field[$m]'>" . $Value0 . "</td></tr>";
            }
        }
        echo "</table></td>";


        echo "</tr></table>";
        $i++;
        $j++;

        /////////////////////////////////

        // include "../model/subprogram/read_model_6.php";
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
<script>
topFloat = 150;

//查询
function toSearchResult() {

    jQuery("#statusType").val(jQuery("input[name='statusTypeCon']:checked").val());
    jQuery("#proId").val(jQuery("#proIdCon").val());
    jQuery("#type").val(jQuery("#typeCon").val());
    jQuery("#status").val(jQuery("#statusCon").val());
    jQuery("#mouldNo").val(jQuery("#mouldNoCon").val());
    jQuery("#carNo").val(jQuery("#carNoCon").val());

    document.form1.action = "bom_mould_read.php";
    document.form1.target = "_self";
    document.form1.submit();
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
            //$('#LoginMsg').html('登入中，請稍後...').show();
        },
        success: function (result) {
            if (result.rlt) {
                window.location.reload();
            } else {
                alert("初始化BOM操作有误!");
            }
        }
    }).done(function () {
        //$('#LoginMsg').html('').hide();
    });
}

//删除
function toDeleteData() {
    var choosedRow = 0;
    var Ids;

    jQuery('input[name^="checkid"]:checkbox').each(function () {
        if (jQuery(this).prop('checked') == true) {
            choosedRow = choosedRow + 1;
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

    var message = confirm("确定要进行此操作吗？");
    if (message == false) {
        return;
    }

    var proId = jQuery("#proId").val();

    document.form1.action = "bom_mould_del.php?Ids=" + Ids + "&proId=" + proId;
    document.form1.target = "_self";
    document.form1.submit();
}

//导出数据
function toExportData() {
    document.form1.action = "bom_data_export.php?type=3";
    document.form1.target = "_self";
    document.form1.submit();
}

function toImportData() {
    document.form1.action = "bom_data_add.php?type=3";
    document.form1.target = "_self";
    document.form1.submit();
}

function statusTypeChange(statusType) {
    jQuery("#statusType").val(statusType);
    jQuery("#proId").val("");
    jQuery("#type").val("");
    jQuery("#status").val("");
    jQuery("#mouldNo").val("");
    jQuery("#carNo").val("");

    document.form1.action = "bom_mould_read.php";
    document.form1.target = "_self";
    document.form1.submit();
}

function proIdConChange() {
    jQuery("#proId").val(jQuery("#proIdCon").val());
    jQuery("#type").val("");
    jQuery("#status").val("");
    jQuery("#mouldNo").val("");
    jQuery("#carNo").val("");

    RefreshPage("bom_mould_read");
}

function setTrolleyId(e) {
    var num = 0;
    var value = '';
    jQuery('input[id^=checkid]:checked').each(function () {
        if (num === 0) {
            value = jQuery(this).val();
        } else {
            value = value + '|' + jQuery(this).val();
        }
        num++;
    });
    if (num === 0) {
        alert('请选择模具编号！');
        return false;
    }
    openWinDialogWithParas(e, "set_trolley.php", 800, 600, 'center', value);
    jQuery('#mask').show();
}

function setTrolleyconfirm() {
    var a = '';
    var num = 0;
    jQuery('input[name="need"]').each(function () {
        if (num === 0) {
            a = jQuery(this).val() + '|' + jQuery(this).parent().parent().find('td[name="trolleyId"]').text().replace(/^\s+|\s+$/g, "").replace(/，/ig,',');
        } else {
            a = a + '^^' + jQuery(this).val() + '|' + jQuery(this).parent().parent().find('td[name="trolleyId"]').text().replace(/^\s+|\s+$/g, "").replace(/，/ig,',');
        }
        num++;
    });
    jQuery.ajax({
        url: 'do_set_trolley.php',
        type: 'post',
        asyns:false,
        data: {
            value: a
        },
        dataType: 'json'
    });
    alert('修改成功！');
    window.location.reload();
}

function batchSetTrolley(e){
    jQuery('#winDialog').css('min-height','300');
    openWinDialog(e,"batch_set_trolley.php",405,300,'bottom');
    jQuery('#mask').show();
}

function doBatchSetTrolley(){
    jQuery('.response').show();
    var tempTrolley = jQuery('#setTrolley').val();
    jQuery.ajax({
        url: 'do_batch_set_trolley.php',
        data: {
            tempTrolley: tempTrolley
        },
        async:false,
        type: 'post',
        dataType: 'json'
    });
    window.location.reload();
}
</script>

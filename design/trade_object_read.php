<?php
include "../model/modelhead.php";
echo '<link href="../model/css/ac_ln.css">';
$From = $From == "" ? "read" : $From;
//需处理参数
$ColsNumber = 15;
//$tableMenuS=1490;
ChangeWtitle("$SubCompany 项目列表");
$funFrom = "trade_object";
$nowWebPage = $funFrom . "_read";
//$sumCols="4";		//求和列  
$Th_Col = "选项|60|序号|50|项目编号|65|项目名称|100|构件总数量|80|已导入数量|80|立项时间|70|校核负责人|75|审核负责人|75|项目成员|70|审核状态|70|生产负责人|75| |65| |65| |90";
$Pagination = $Pagination == "" ? 1 : $Pagination;  //分页标志 0-不分页 1-分页
$Page_Size = 100; //每页数量
$ActioToS = "8,9"; //功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消

//检索条件
$type = 1;
if (isset($_POST["type"])) {
    $type = $_POST["type"];
}
if (isset($_GET["type"])) {
    $type = $_GET["type"];
}
$period = 0;
if ($_POST["period"]) {
    $period = $_POST["period"];
}

if ($_POST["status"]) {
    $status = $_POST["status"];
}

$name = "";
if ($_POST["name"]) {
    $name = $_POST["name"];
}
?>
<style type="text/css">
  .input_radio1 {
    vertical-align: top;
    margin-top: -1.5px;
    margin-left: 20px;
  }

  .input_btn1 {
    width: 93px;
    height: 25px;
    border-radius: 5px;
    padding: 0;
    color: #333;
    font-weight: 700;
    background: rgb(33, 172, 168);
    border: 1px solid rgba(121, 121, 121, 1);
    margin-right: 5px;
  }

  .select1 {
    width: 100px;
    height: 25px;
    margin-right: 10px;
    border: 1px solid lightgray;
  }

  #qjProjectname {
    width: 150px;
    height: 25px;
  }

  .input_btn2 {
    width: 60px;
    height: 25px;
    color: #000;
    border: 1px solid #000;
    border-radius: 5px;
    margin-left: 30px;
    background-color: rgba(0, 153, 102, 1);
  }

  .tds1 {
    height: 35px;
    padding: 0 10px;
  }

  .lable_active {
    font-weight: bold;
  }
</style>
<div class='div-select div-mcmain' style="width: 850px;margin-bottom: 10px;">
  <table border="0" cellspacing="0" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width: 850px;' bgcolor="white">
    <p style="height:10px;background-color: #fff;margin: 0;">　</p>
    <tr>
      <td class="tds1" style="color:#949598;">
        <input name="chooseType" type="radio" id="Type1" value="1" class="input_radio1" style="margin-left: 0;" <?php if ($type == 1) {
            echo "checked";
        }
        else {
            echo "onClick='typeChange(1)'";
        } ?> /><LABEL for="Type1" <?php if ($type == 1) {
              echo "class='lable_active'";
          } ?> >在设计项目</LABEL>
        <input name="chooseType" type="radio" id="Type2" value="2" class="input_radio1" <?php if ($type == 2) {
            echo "checked";
        }
        else {
            echo "onClick='typeChange(2)'";
        } ?> /><LABEL for="Type2" <?php if ($type == 2) {
              echo "class='lable_active'";
          } ?> >已经完成设计项目</LABEL>
        <input name="chooseType" type="radio" id="Type3" value="3" class="input_radio1" <?php if ($type == 3) {
            echo "checked";
        }
        else {
            echo "onClick='typeChange(3)'";
        } ?> /><LABEL for="Type3" <?php if ($type == 3) {
              echo "class='lable_active'";
          } ?> >已完结项目</LABEL>
        <span class="btn-confirm" onClick='toApply(1)' style="display:inline-block;float: right;margin-right: 20px;">申请校对</span>
        <span class="btn-confirm" onClick='toApply(6)' style="display:inline-block;float: right;margin-right: 20px;">提交审核</span>
      </td>
    </tr>
      <?php if ($type == 1) { ?>
        <tr>

        </tr>
      <?php }
      else if ($type == 2) { ?>
        <tr>
          <td class="tds1" style="padding-left: 10px;">
            <span class="btn-confirm" onClick='toApply(10)' style="width:135px">设计完毕,导入生产</span>
          </td>
        </tr>
      <?php } ?>

    <tr>
      <td class="tds1">
        <select name='choosePeriod' id='choosePeriod' class="select1" style="background-color: #fff;">
          <option value='0' <?php if ($period == 0) echo "selected" ?>>全部</option>
          <option value='1' <?php if ($period == 1) echo "selected" ?>>最近30天</option>
          <option value='2' <?php if ($period == 2) echo "selected" ?>>最近180天</option>
          <option value='3' <?php if ($period == 3) echo "selected" ?>>今年</option>
          <option value='4' <?php if ($period == 4) echo "selected" ?>>今年前</option>
        </select>
          <?php
          if ($type == 1) {
              //在设计项目
              echo "<select name='chooseCheck' id='chooseCheck' class='select1' style=\"background-color: #fff;\">";
              echo "<option value='' ", $status == "" ? "selected" : "", ">状态选择</option>";
              echo "<option value='0' ", $status == "0" ? "selected" : "", ">未提交</option>";
              echo "<option value='1' ", $status == "1" ? "selected" : "", ">未初校</option>";
              echo "<option value='2' ", $status == "2" ? "selected" : "", ">未复校</option>";
              echo "<option value='3' ", $status == "3" ? "selected" : "", ">初校未通过</option>";
              echo "<option value='4' ", $status == "4" ? "selected" : "", ">复校通过</option>";
              echo "<option value='5' ", $status == "5" ? "selected" : "", ">复校未通过</option>";
              echo "<option value='6' ", $status == "6" ? "selected" : "", ">等待审核</option>";
              echo "<option value='7' ", $status == "7" ? "selected" : "", ">已审核通过</option>";
              echo "<option value='8' ", $status == "8" ? "selected" : "", ">审核未通过</option>";
              echo "<option value='9' ", $status == "9" ? "selected" : "", ">审核退回</option>";
              echo "</select>";
          }
          ?>
        <input name='qjProjectname' type='text' id='qjProjectname' placeholder="输入项目名称" autocomplete='off' style="height:28px;color:#949598" value='<?php echo $name ?>'/>
        <span class="btn-confirm" onClick='toSearchResult()'>查询</span>

      </td>
    </tr>
  </table>
  <p style="height:10px;background-color: #fff;margin: 0;">　</p>

</div>
<?php
//步骤3：
include "../model/subprogram/read_model_3.php";

$SearchRows = "";
//在设计项目 已经完成设计项目 已完结项目 
if ($type == 1) {
    if ($status == "") {
        $SearchRows = " AND b.Estate <= 9 AND b.Estate <> 7 ";
    }
    else {
        $SearchRows = " AND b.Estate = $status ";
    }
}
else if ($type == 2) {
    $SearchRows = " AND b.Estate in (7, 10)";
}
else {
    $SearchRows = " AND b.Estate = 11 ";
}

if ($period == 1) {
    //最近30天
    $SearchRows .= " AND TO_DAYS(NOW()) - TO_DAYS(b.EstTime) <= 30";
}
else if ($period == 2) {
    $SearchRows .= " AND TO_DAYS(NOW()) - TO_DAYS(b.EstTime) <= 180";
}
else if ($period == 3) {
    $SearchRows .= " AND YEAR(b.EstTime) = YEAR(NOW())";
}
else if ($period == 4) {
    $SearchRows .= " AND YEAR(b.EstTime) < YEAR(NOW())";
}

//检索条件隐藏值
echo " <input type='hidden' name='proId' id='proId' value='' />";
echo " <input type='hidden' name='type' id='type' value='$type' />";
echo " <input type='hidden' name='period' id='period' value='$period' />";
echo " <input type='hidden' name='status' id='status' value='$status' />";
echo " <input type='hidden' name='name' id='name' value='$name' />";

//echo"<a>&nbsp;全选&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;<a>&nbsp;不选&nbsp;</a>";

//步骤5：
include "../model/subprogram/read_model_5.php";

//步骤6：需处理数据记录处理
$i = 1;
$j = ($Page - 1) * $Page_Size + 1;
List_Title($Th_Col, "1", 1);
$mySql = "SELECT a.Id, a.Forshort, b.TradeNo, b.EstTime, s1.name as Proofreader, b.Proofreaded,
s2.name as Checker, b.Checked, b.Members, b.Produced, s3.name as Producer, b.Estate, b.CmptTotal
FROM $DataIn.trade_object a 
inner join $DataIn.trade_info b on a.Id = b.tradeId $SearchRows 
left join $DataPublic.staffmain s1 on b.Proofreader = s1.Number 
left join $DataPublic.staffmain s2 on b.Checker = s2.Number
left join $DataPublic.staffmain s3 on b.Producer = s3.Number
where a.ObjectSign = 2 ";
if ($name) {
    $mySql .= " and a.Forshort like '%$name%' ";
}
$mySql .= " order by a.Id ";

//echo $mySql;
$myResult = mysql_query($mySql . " $PageSTR", $link_id);
if ($myResult && $myRow = mysql_fetch_array($myResult)) {
    do {
        $m = 1;  //必须

        $Id = $myRow["Id"];
        $Estate = $myRow["Estate"];
        switch ($Estate) {
            case 0:
                $Estate = "<div class='yellowB'>未提交</div>";
                break;
            case 1:
                $Estate = "<div class='blueB'>未初校</div>";
                break;
            case 2:
                $Estate = "<div class='blueB'>未复校</div>";
                break;
            case 3:
                $Estate = "<div class='redB'>初校未通过</div>";
                break;
            case 4:
                $Estate = "<div class='greenB'>校核通过</div>";
                break;
            case 5:
                $Estate = "<div class='redB'>复校未通过</div>";
                break;
            case 6:
                $Estate = "<div class='blueB'>未审核</div>";
                break;
            case 7:
                $Estate = "<div class='greenB'>审核通过</div>";
                break;
            case 8:
                $Estate = "<div class='redB'>审核不通过</div>";
                break;
            case 9:
                $Estate = "<div class='redB'>审核退回</div>";
                break;
            case 10:
                $Estate = "<div class='blueB'>生产中</div>";
                break;
            case 11:
                $Estate = "<div class='greenB'>生产完成</div>";
                break;
            default:
                $Estate = "<div class='redB'>未通过</div>";
                break;
        }

        $total = 0;
        //构件总数量
        $cmptResult = mysql_query("SELECT count(a.CmptNo) as total FROM trade_drawing a where  a.TradeId = $Id", $link_id);
//         $cmptResult = mysql_query("select SUM(t.count) as total from (
//                         SELECT count(*) as count from $DataIn.trade_drawing a where a.TradeId = $Id
//                             union all
//                         SELECT count(*) as count from $DataIn.trade_steel b where b.TradeId = $Id
//                             union all
//                         SELECT count(*) as count from $DataIn.trade_embedded c where c.TradeId = $Id) t ",$link_id);

        if ($cmptRow = mysql_fetch_array($cmptResult)) {
            $total = $cmptRow["total"];
        }

        $ValueArray = array(
            array(0 => $myRow["TradeNo"], 1 => "align='center'"),
            array(0 => $myRow["Forshort"], 1 => "align='center'"),
            array(0 => $myRow["CmptTotal"], 1 => "align='center'"),
            array(0 => $total, 1 => "align='center'"),
            array(0 => $myRow["EstTime"], 1 => "align='center'"),
            array(0 => $myRow["Proofreader"], 1 => "align='center'"),
            array(0 => $myRow["Checker"], 1 => "align='center'"),
            array(0 => $myRow["Members"], 1 => "align='center'"),
            array(0 => $Estate, 1 => "align='center'"),
            array(0 => $myRow["Producer"], 1 => "align='center'"),
            array(0 => "<a href='trade_drawing_read.php?proId=$Id'>图纸信息</a>", 1 => "align='center'"),
            array(0 => "<a href='trade_steel_read.php?proId=$Id'>钢筋信息</a>", 1 => "align='center'"),
            array(0 => "<a href='trade_embedded_read.php?proId=$Id'>预埋件信息</a>", 1 => "align='center'"),
        );
        $checkidValue = $Id;
        $ChooseOut = "N";

        $Estate = $myRow["Estate"];
        $myOpration = "<input name='checkid[]' type='checkbox' id='checkid$i' data-state='$Estate' value='$checkidValue' disabled>";
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
<script>

topFloat = 130;

function toSearchResult() {
    jQuery("#type").val(jQuery("input[name='chooseType']:checked").val());
    jQuery("#period").val(jQuery("#choosePeriod").val());
    jQuery("#status").val(jQuery("#chooseCheck").val());
    jQuery("#name").val(jQuery("#qjProjectname").val());

    document.form1.action = "trade_object_read.php";
    document.form1.submit();
}

function typeChange(statusType) {
    jQuery("#type").val(statusType);
    jQuery("#period").val("");
    jQuery("#status").val("");
    jQuery("#name").val("");

    document.form1.action = "trade_object_read.php";
    document.form1.target = "_self";
    document.form1.submit();
}


//申请
function toApply(state) {

    var choosedRow = 0;
    var proId;
    var Estate;

    jQuery('input[name^="checkid"]:checkbox').each(function () {
        if (jQuery(this).prop('checked') == true) {
            choosedRow = choosedRow + 1;
            if (choosedRow == 1) {
                proId = jQuery(this).val();
                Estate = jQuery(this).attr("data-state");
                //} else {
                //	Ids = Ids + "," + jQuery(this).val();
            }
        }
    });

    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        return;
    }

    if (choosedRow > 1) {
        alert("该操作只能选取定一条记录!");
        return;
    }

    var msg;
    if (state == 1) {
        msg = "申请校对";
        if (Estate != 0 && Estate != 3 && Estate != 5 && Estate != 8 && Estate != 9) {
            alert("该项目已经申请校对!");
            return;
        }
    } else if (state == 6) {
        msg = "提交审核";
        if (Estate != 4) {
            alert("该项目不能提交审核!");
            return;
        }
    } else if (state == 10) {
        msg = "导入生产";
        //申请校对
        if (Estate != 7) {
            alert("该项目不能导入生产!");
            return;
        }
    } else {
        return;
    }

    var message = confirm("确定要进行此操作吗？");
    if (message == false) {
        return;
    }

    jQuery.ajax({
        url: 'trade_state_update.php',
        type: 'post',
        data: {
            id: proId,
            state: state
        },
        dataType: 'json',
        beforeSend: function () {
            //$('#LoginMsg').html('登入中，請稍後...').show();
        },
        success: function (result) {
            if (result.rlt) {
                window.location.reload();
            } else {
                if (result.msg) {
                    alert(result.msg);
                } else {
                    alert(msg + "操作有误!");
                }
            }
        }
    }).done(function () {
        //$('#LoginMsg').html('').hide();
    });
}

//校对
function toProofreade() {
    var choosedRow = 0;
    var proId;
    var Estate;

    jQuery('input[name^="checkid"]:checkbox').each(function () {
        if (jQuery(this).prop('checked') == true) {
            choosedRow = choosedRow + 1;
            if (choosedRow == 1) {
                proId = jQuery(this).val();
                Estate = jQuery(this).attr("data-state");
                //} else {
                //	Ids = Ids + "," + jQuery(this).val();
            }
        }
    });

    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        return;
    }

    if (choosedRow > 1) {
        alert("该操作只能选取定一条记录!");
        return;
    }

    //校对
    if (Estate != 1 && Estate != 2) {
        alert("该项目不能校核!");
        return;
    }

    jQuery("#proId").val(proId);

    document.form1.action = "trade_proofreade.php";
    document.form1.target = "_self";
    document.form1.submit();
}

//审核
function toCheck() {

    var choosedRow = 0;
    var proId;
    var Estate;

    jQuery('input[name^="checkid"]:checkbox').each(function () {
        if (jQuery(this).prop('checked') == true) {
            choosedRow = choosedRow + 1;
            if (choosedRow == 1) {
                proId = jQuery(this).val();
                Estate = jQuery(this).attr("data-state");
                //} else {
                //	Ids = Ids + "," + jQuery(this).val();
            }
        }
    });

    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        return;
    }

    if (choosedRow > 1) {
        alert("该操作只能选取定一条记录!");
        return;
    }

    //审核
    if (Estate != 6) {
        alert("该项目不能审核!");
        return;
    }

    jQuery("#proId").val(proId);

    document.form1.action = "trade_check.php";
    document.form1.target = "_self";
    document.form1.submit();
}

</script>
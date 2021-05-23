<?php
include "../model/modelhead.php";
$From = $From == "" ? "read" : $From;
//需处理参数
$ColsNumber = 13;
//$tableMenuS=1490;
ChangeWtitle("$SubCompany 项目列表-BOM");
$funFrom = "bom_object";
$nowWebPage = $funFrom . "_read";
//$sumCols="4";		//求和列
$Th_Col = "选项|60|序号|50|项目编号|80|楼栋-楼层|80|项目名称|100|构件总数量|80|生成时间|90|操作人|75|提交审核时间|90|审核负责人|75|审核状态|70|BOM信息|75|模具信息|70|损耗信息|70|生成业务订单|100";
$Pagination = $Pagination == "" ? 1 : $Pagination;  //分页标志 0-不分页 1-分页
$Page_Size = 100; //每页数量
$ActioToS = "8,9"; //功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消

//检索条件
//时间选择
if ($_POST["period"]) {
    $period = $_POST["period"];
}

//审核状态
if ($_POST["status"]) {
    $status = $_POST["status"];
}

//项目名称
if ($_POST["name"]) {
    $name = $_POST["name"];
}



//项目初始数据生成
$mySql = "INSERT INTO $DataIn.bom_object (tradeid)( 
        SELECT a.id FROM $DataIn.trade_object a 
        inner join $DataIn.trade_info c on a.id = c.TradeId and c.Estate in (10, 11) WHERE a.ObjectSign = 2
        AND NOT EXISTS ( SELECT b.tradeId FROM $DataIn.bom_object b WHERE a.id = b.tradeid ) )";
mysql_query($mySql);

?>
<style type="text/css">
  .input_radio1 {
    vertical-align: top;
    margin-top: -1.5px;
    margin-left: 20px;
  }

  .input_btn1 {
    width: 90px;
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

  .selectBuild {
    width: 60px;
    height: 25px;
    /*margin-right: 10px;*/
    border: 1px solid lightgray;
    text-align: center;
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
<?php include '../basic/loading.php' ?>
<table border="0" cellspacing="0" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width: 1000px;border: 1px solid #E2E8E8;border-radius:5px;-moz-box-shadow: 0px 0px 10px #c7c7c7; box-shadow: 0px 0px 10px #c7c7c7;margin:10px 10px 10px 0' bgcolor='#FFF' class="div-select">
  <tr >
    <td style="padding-left: 10px;height: 50px;">
      <select name='choosePeriod' id='choosePeriod' >
        <option value='0' <?php if ($period == 0) echo "selected" ?>>全部</option>
        <option value='1' <?php if ($period == 1) echo "selected" ?>>最近30天</option>
        <option value='2' <?php if ($period == 2) echo "selected" ?>>最近180天</option>
        <option value='3' <?php if ($period == 3) echo "selected" ?>>今年</option>
        <option value='4' <?php if ($period == 4) echo "selected" ?>>今年前</option>
      </select>

      <select name='chooseCheck' id='chooseCheck' >
        <option value='' <?php if ($status == "") echo "selected" ?>>状态选择</option>
        <option value='0' <?php if ($status == "0") echo "selected" ?>>未提交</option>
        <option value='1' <?php if ($status == "1") echo "selected" ?>>未审核</option>
        <option value='2' <?php if ($status == "2") echo "selected" ?>>审核通过</option>
        <option value='3' <?php if ($status == "3") echo "selected" ?>>审核不通过</option>
        <option value='4' <?php if ($status == "4") echo "selected" ?>>审核退回</option>
      </select>

      <input name='qjProjectname' type='text' id='qjProjectname' placeholder="输入项目名称" autocomplete='off' value='<?php echo $name ?>'/>
        <span  class="btn-confirm" style="display: inline-block;" onClick='toSearchResult()'>查询</span>
    </td>

    <td >
        <span  class="btn-confirm"   style="display: inline-block;" onClick='toApply(1)'>提交审核</span>
        　<span  class="btn-confirm" style="display: inline-block;" onClick='toCheck()'>审　核</span>
        　<span  class="btn-confirm"  style="display: inline-block;width: auto" onClick='toCreatemould()'>生成模具订单</span>
    </td>
  </tr>
</table>
<?php
//步骤3：
include "../model/subprogram/read_model_3.php";

$SearchRows = "";
//在设计项目 已经完成设计项目 已完结项目
if ($status == 1) {
    $SearchRows = " AND a.Estate = $status ";
}

if ($period == 1) {
    //最近30天
    $SearchRows .= " AND TO_DAYS(NOW()) - TO_DAYS(a.BomCreated) <= 30";
}
else if ($period == 2) {
    $SearchRows .= " AND TO_DAYS(NOW()) - TO_DAYS(a.BomCreated) <= 180";
}
else if ($period == 3) {
    $SearchRows .= " AND YEAR(a.BomCreated) = YEAR(NOW())";
}
else if ($period == 4) {
    $SearchRows .= " AND YEAR(a.BomCreated) < YEAR(NOW())";
}

if ($name) {
    $SearchRows .= " and C.Forshort like '%$name%' ";
}

//检索条件隐藏值
echo " <input type='hidden' name='proId' id='proId' value='' />";
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
$mySql = "SELECT a.Id, a.TradeId, s1.name as BomCreater, a.BomCreated,
        s3.name as Operator, a.Submited, s2.name as Checker, a.Checked, a.CReasons, a.Estate,
        b.TradeNo, b.CmptTotal, C.Forshort
        FROM $DataIn.bom_object a 
        INNER JOIN $DataIn.trade_info b on a.TradeId = b.TradeId
        INNER JOIN $DataIn.trade_object c on a.TradeId = c.id
        left join $DataPublic.staffmain s1 on a.BomCreater = s1.Number
        left join $DataPublic.staffmain s3 on a.Operator = s3.Number
        left join $DataPublic.staffmain s2 on a.Checker = s2.Number
where 1 $SearchRows order by a.Id ";

//echo $mySql;
$myResult = mysql_query($mySql . " $PageSTR", $link_id);
if ($myResult && $myRow = mysql_fetch_array($myResult)) {
    do {
        $m = 1;  //必须

        $Id = $myRow["Id"];
        $TradeId = $myRow["TradeId"];
        // 项目是否初始化
        $isBomInit = 0;
        $mysql = "select isBomInit from $DataIn.bom_object where tradeId = $TradeId";
        $myResult1 = mysql_query($mysql, $link_id);
        if($myRow1 = mysql_fetch_array($myResult1)){
            $isBomInit = $myRow1['isBomInit'];
        }
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
        $BomCreated = "";
        if (!empty($myRow["BomCreated"])) {
            $BomCreated = date("Y-m-d", strtotime($myRow["BomCreated"]));
        }
        $Submited = "";
        if ($myRow["Submited"]) {
            $Submited = date("Y-m-d", strtotime($myRow["Submited"]));
        }
        $BuildList = "";
        $BuildSql = "SELECT CONCAT( substring_index( C.PO, '-', - 2), '-', C.CmptType ) AS LeftFloor, CONCAT( substring_index( C.PO, '-', - 2 ), '-', C.TypeId ) AS LeftFloorId, C.NameRule FROM(SELECT DISTINCT CONCAT( B.TradeNo, '-', A.BuildingNo, '-', A.FloorNo ) AS PO, A.CmptType, P.TypeId, P.NameRule FROM trade_drawing A LEFT JOIN trade_info B ON A.TradeId = B.TradeId LEFT JOIN producttype P ON P.TypeId = A.CmptTypeId WHERE A.TradeId = $TradeId ORDER BY  A.BuildingNo+0, P.NameRule, A.FloorNo+0, A.CmptTypeId ) C WHERE CONCAT( C.PO, '-', C.TypeId ) NOT IN (SELECT CONCAT( Y.OrderPO, '-', P.TypeId ) AS OrderType FROM yw1_ordersheet Y INNER JOIN productdata P ON P.ProductId = Y.ProductId )";
//$BuildSql = "SELECT BuildingNo
//        FROM $DataIn.trade_drawing
//where TradeId = $TradeId GROUP by BuildingNo";
//echo $BuildSql;
        $BuildResult = mysql_query($BuildSql, $link_id);
        $disabled = "";
        if ($BuildResult && $BuildRow = mysql_fetch_array($BuildResult)) {

            $BuildList = "<select name='BuildingNo' class='BuildingNo' class=\"selectBuild\">";
            //$BuildList .=  "<option value='' >全部</option>";
            do {
                $BuildingNo = $BuildRow['LeftFloor'];
                $BuildingId = $BuildRow['LeftFloorId'];
                $NameRule = $BuildRow['NameRule'];
                $BuildList .= "<option value='$BuildingId' > $BuildingNo ( $NameRule ) </option>";

            } while ($BuildRow = mysql_fetch_array($BuildResult));
            $BuildList .= "</select>";
            $CreateOrder = "<span class='btn-confirm' onClick='toCreateOrder()' style='cursor: pointer;font-size: 12px;'>生成订单</span>";
        }
        else {
            $BuildList .= "<span style='color: green' class='BuildingNo'><b>已生成订单</b></span>";
            $disabled = " style='border: 1px solid #e6e6e6;background-color: #FBFBFB;color: #c0c0c0;cursor: not-allowed;opacity: 1; height: 28px; line-height: 28px; width: 60px; padding-left: 8px; padding-right: 8px;display: inline-block;' ";
            $CreateOrder = "<span $disabled>已生成订单</span>";
        }

        $ValueArray = array(
            array(0 => $myRow["TradeNo"], 1 => "align='center' name='tradeNo'"),
            array(0 => $BuildList, 1 => "align='center'"),
            array(0 => $myRow["Forshort"], 1 => "align='center'"),
            array(0 => $myRow["CmptTotal"], 1 => "align='center'"),
            array(0 => $BomCreated, 1 => "align='center'"),
            array(0 => $myRow["Operator"], 1 => "align='center'"),
            array(0 => $Submited, 1 => "align='center'"),
            array(0 => $myRow["Checker"], 1 => "align='center'"),
            array(0 => $Estate, 1 => "align='center'"),
            array(0 => "<a href='bom_info_read.php?proId=$TradeId' TITLE='BOM信息'>查看</a>", 1 => "align='center'"),
            array(0 => "<a href='bom_mould_read.php?proId=$TradeId' title='模具信息'>查看</a>", 1 => "align='center'"),
            array(0 => "<a href='bom_loss_read.php?proId=$TradeId' title='损耗信息'>查看</a>", 1 => "align='center'"),
            array(0 => $CreateOrder, 1 => "align='center'"),
        );
        $checkidValue = $TradeId;
        $ChooseOut = "N";

        $Estate = $myRow["Estate"];
        $myOpration = "<input name='checkid[]' type='checkbox' id='checkid$i' data-state='$Estate' value='$checkidValue' disabled>";
        echo "<span class='isBom' style='display:none'>$isBomInit</span>";
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
topFloat = 150;

function toSearchResult() {
    jQuery("#period").val(jQuery("#choosePeriod").val());
    jQuery("#status").val(jQuery("#chooseCheck").val());
    jQuery("#name").val(jQuery("#qjProjectname").val());

    document.form1.action = "bom_object_read.php";
    document.form1.submit();
}

//申请
function toApply(state) {

    var choosedRow = 0;
    var proId;
    var Estate;
    var isBomInit = 0;

    jQuery('input[name^="checkid"]:checkbox').each(function () {
        if (jQuery(this).prop('checked') == true) {
            var isBomInit = jQuery(this).parent().parent().parent().parent().prev().text();
            if( isBomInit != 1) {
                alert ('提交审核前需初始化BOM！');
                return;
            }

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

    //alert(Estate);
    if (Estate != 0 && Estate != 3 && Estate != 4) {
        alert("该项目已经提交审核!");
        return;
    }


    var message = confirm("确定要进行此操作吗？");
    if (message == false) {
        return;
    }

    jQuery.ajax({
        url: 'bom_state_update.php',
        type: 'post',
        data: {
            id: proId,
            state: state
        },
        dataType: 'json',
        success: function (result) {
            if (result.rlt) {
                window.location.reload();
            } else {
                if (result.msg) {
                    alert(result.msg);
                } else {
                    alert("提交审核操作有误!");
                }
            }
        }
    }).done(function () {
        //$('#LoginMsg').html('').hide();
    });
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
    if (Estate != 1) {
        alert("该项目不能审核!");
        return;
    }

    jQuery("#proId").val(proId);

    document.form1.action = "bom_check.php";
    document.form1.target = "_self";
    document.form1.submit();
}

function toCreateOrder() {

    var choosedRow = 0;
    var proId = '';
    var Estate;
    var BuildNo = '';
    var OrderPO = '';
    jQuery('input[name^="checkid"]:checkbox').each(function () {
        if (jQuery(this).prop('checked') == true) {
            BuildNo = jQuery(this).parent().parent().find("option:selected").text();
            if (BuildNo == "") {
                alert("该操作需要选择栋号!");
                return;
            }
            OrderPO = jQuery(this).parent().parent().find('td[name="tradeNo"]').text();
            console.log(OrderPO);
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

    //alert(Estate);
    if (Estate != 2) {
        alert("该项目还没有审核通过!");
        return;
    }

    var message = confirm("确定要进行此操作吗？");
    if (message == false) {
        return;
    }
    jQuery.ajax({
        url: 'bom_createorder2.php',
        type: 'post',
        data: {
            proId: proId,
            BuildNo: BuildNo
        },
        dataType: 'json',
        beforeSend: function () {
            jQuery('.response').show();
        },
        success: function (result) {
            if (result.rlt) {
                //window.location.reload();
                alert("生成订单操作成功!");
            } else {
                if (result.msg) {
                    alert(result.msg);
                } else {
                    alert("生成订单操作有误!");
                }
            }
        }

    }).done(function () {
        jQuery('.response').hide();
    });
    // setTimeout(function(){},20000);
    // var flag = 0;
    // var getResponse = setInterval(function () {
    //     jQuery.ajax({
    //         url: 'bom_createresponse.php',
    //         timeout: 3000,
    //         type: 'post',
    //         async: false,
    //         data: {
    //             proId: proId,
    //             OrderPO: OrderPO,
    //             BuildNo: BuildNo
    //         },
    //         beforeSend: function () {
    //             if (flag === 1) {
    //                 clearInterval(getResponse);
    //             }
    //         },
    //         dataType: 'json',
    //         success: function (result) {
    //             // if (result.rlt === true) {
    //             //     if (result.top === result.cur) {
    //             //         flag = 1;
    //             //         alert('生成订单操作成功! ' + result.msg);
    //             //         jQuery('.response').hide();
    //             //         window.location.reload();
    //             //     } else {
    //             //         flag = 1;
    //             //         alert('生成订单操作部分成功! ' + result.msg);
    //             //         jQuery('.response').hide();
    //             //         window.location.reload();
    //             //     }
    //             // } else {
    //             //     flag = 1;
    //             //     alert(result.msg);
    //             //     jQuery('.response').hide();
    //             //     window.location.reload();
    //             // }
    //             window.location.reload();
    //         }
    //     });
    // }, 10000);

}
function toCreatemould() {

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

    //alert(Estate);
    if (Estate != 2) {
        alert("该项目还没有审核通过!");
        return;
    }


    var message = confirm("确定要进行此操作吗？");
    if (message == false) {
        return;
    }

    jQuery.ajax({
        url: 'bom_createmoduleorder.php',
        type: 'post',
        data: {
            proId: proId
        },
        dataType: 'json',
        beforeSend: function () {
            //$('#LoginMsg').html('登入中，請稍後...').show();
        },
        success: function (result) {
            if (result.rlt) {
                //window.location.reload();
                alert("生成订单操作成功!");
            } else {
                if (result.msg) {
                    alert(result.msg);
                    window.location.reload();
                } else {
                    alert("生成订单操作有误!");
                }
            }
        },
        error: function (e) {
            alert("生成订单出现错误!");
            alert(e);
        }
    }).done(function () {
        //$('#LoginMsg').html('').hide();
    });
}

</script>
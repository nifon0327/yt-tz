<?php
include "../model/modelhead.php";
$From = $From == "" ? "cell" : $From;
$tableMenuS = 750;
ChangeWtitle("$SubCompany 出货单元");
$funFrom = "ch_shippinglist";
$nowWebPage = $funFrom . "_cell";
$Pagination = $Pagination == "" ? 1 : $Pagination;
$ShipTypeFlag = $ShipTypeFlag == "" ? 0 : $ShipTypeFlag;
$Page_Size = 200;

$Th_Col = "选项|60|序号|40|客户名称|120|楼栋|50|楼层|50|类型|50|构件名称|130|出货单元|100";
$ActioToS = "1,3";
$sumCols = "7";      //求和列,需处理
$ColsNumber = 17;

//步骤3：
include "../model/subprogram/read_model_3.php";
//echo 'CompanyId：' . $CompanyId;
//步骤4：需处理-条件选项

//项目
$SearchRows = "";
//$SearchRows = " and M.Estate='0'";
$Company_Result = mysql_query("SELECT T.CompanyId,T.Forshort FROM productdata P INNER JOIN trade_object T ON P.CompanyId = T.CompanyId INNER JOIN trade_drawing D ON P.drawingId = D.Id GROUP BY T.Id", $link_id);
if ($CompanyRow = mysql_fetch_array($Company_Result)) {
    echo "<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"$nowWebPage\")'>";
    echo "<option value='all' selected>选择客户项目</option>";
    do {
        $thisCompanyId = $CompanyRow["CompanyId"];
        $thisForshort = $CompanyRow["Forshort"];
        $CompanyId = $CompanyId == "" ? $thisCompanyId : $CompanyId;
        if ($CompanyId == $thisCompanyId) {
            echo "<option value='$thisCompanyId' selected>$thisForshort</option>";
            $SearchRows .= " AND  P.CompanyId='$thisCompanyId' ";
        } else {
            echo "<option value='$thisCompanyId'>$thisForshort</option>";
        }
    } while ($CompanyRow = mysql_fetch_array($Company_Result));
    echo "</select>&nbsp;";
}
//楼栋
$BuildingResult = mysql_query("SELECT D.BuildingNo FROM productdata P INNER JOIN trade_object T ON P.CompanyId = T.CompanyId INNER JOIN trade_drawing D ON P.drawingId = D.Id WHERE 1 $SearchRows GROUP BY D.BuildingNo", $link_id);
if ($BuildingRow = mysql_fetch_array($BuildingResult)) {
    echo "<select name='Building' id='Building' onchange='RefreshPage(\"$nowWebPage\")'>";
    echo "<option value='all' selected>选择楼栋</option>";
    do {
        $thisBuilding = $BuildingRow["BuildingNo"];
        $Building = $Building == "" ? $thisBuilding : $Building;
        if ($Building == $thisBuilding) {
            echo "<option value='$thisBuilding' selected>$thisBuilding #</option>";
            $SearchRows .= " and D.BuildingNo='$thisBuilding' ";
        } else {
            echo "<option value='$thisBuilding'>$thisBuilding #</option>";
        }
    } while ($BuildingRow = mysql_fetch_array($BuildingResult));
    echo "</select>&nbsp;";
}

//楼层
$FloorResult = mysql_query("SELECT D.FloorNo FROM productdata P INNER JOIN trade_object T ON P.CompanyId = T.CompanyId INNER JOIN trade_drawing D ON P.drawingId = D.Id WHERE 1 $SearchRows GROUP BY D.FloorNo ORDER BY D.FloorNo+0", $link_id);
if ($FloorRow = mysql_fetch_array($FloorResult)) {
    echo "<select name='Floor' id='Floor' onchange='RefreshPage(\"$nowWebPage\")'>";
    echo "<option value='all' selected>选择楼栋</option>";
    do {
        $thisFloor = $FloorRow["FloorNo"];
        $Floor = $Floor == "" ? $thisFloor : $Floor;
        if ($Floor == $thisFloor) {
            echo "<option value='$thisFloor' selected>$thisFloor F</option>";
            $SearchRows .= " and D.FloorNo='$thisFloor' ";
        } else {
            echo "<option value='$thisFloor'>$thisFloor F</option>";
        }
    } while ($FloorRow = mysql_fetch_array($FloorResult));
    echo "</select>&nbsp;";
}


echo "<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	 <span class='ButtonH_25' onclick=ToExcel()>导出</span> <span class='ButtonH_25' onclick=enter()>导入</span>";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理

$i = 1;
$j = ($Page - 1) * $Page_Size + 1;
List_Title($Th_Col, "1", 0);
$mySql = "SELECT T.Id,T.CompanyId,T.Forshort,D.BuildingNo,D.FloorNo,D.CmptType,D.CmptNo,P.cName,P.Id AS PId,P.cell 
    FROM productdata P
	INNER JOIN trade_object T ON P.CompanyId = T.CompanyId
	INNER JOIN trade_drawing D ON P.drawingId = D.Id
	WHERE 1 $SearchRows ";
//echo $mySql;
$myResult = mysql_query($mySql . " $PageSTR", $link_id);
if ($myRow = mysql_fetch_array($myResult)) {
    $d1 = anmaIn("download/invoice/", $SinkOrder, $motherSTR);
    do {
        $m = 1;
        $theDefaultColor = "#FFFFFF";
        $Id = $myRow["Id"];
        $PId = $myRow["PId"];
        $CompanyId = $myRow["CompanyId"];
        $Forshort = $myRow["Forshort"];
        $BuildingNo = $myRow["BuildingNo"];
        $FloorNo = $myRow["FloorNo"];
        $CmptType = $myRow["CmptType"];
        $CmptNo = $myRow["CmptNo"];
        $cName = $myRow["cName"];
        $cell = $myRow["cell"];

        $img = "<img src='../images/edit.gif'/>";
        $ValueArray = array(
            array(0 => $Forshort),
            array(0 => $BuildingNo, 1 => "align='center'"),
            array(0 => $FloorNo, 1 => "align='center'"),
            array(0 => $CmptType, 1 => "align='center'"),
            array(0 => $CmptNo, 1 => "align='center'"),
            array(0 => $cell . $img, 1 => "align='center' id='cell$PId' onclick='upCell($PId,$i)'"),
        );

        $checkidValue = $PId . '|||';
        include "../model/subprogram/read_model_6.php";
        echo $StuffListTB;
    } while ($myRow = mysql_fetch_array($myResult));
} else {
    noRowInfo($tableWidth);
}
//步骤7：
echo '</div>';//
//List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql, $link_id);
$RecordToTal = mysql_num_rows($myResult);
pBottom($RecordToTal, $i - 1, $Page, $Pagination, $Page_Size, $timer, $Login_WebStyle, $tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script src="../plugins/layer/layer.js" type=text/javascript></script>
<script>
    // 导出
    // function ToExcel() {
    //     var choosedRow = 0;
    //     var Ids;
    //     var POrderId;
    //
    //     jQuery('input[name^="checkid"]:checkbox').each(function () {
    //         if (jQuery(this).prop('checked') == true) {
    //             choosedRow = choosedRow + 1;
    //             if (choosedRow == 1) {
    //                 Ids = jQuery(this).val().split('|')[0];
    //             } else {
    //                 Ids = Ids + "," + jQuery(this).val().split('|')[0];
    //             }
    //         }
    //     });
    //
    //     if (choosedRow == 0) {
    //         layer.confirm('该操作要求选定记录！', {
    //             btn: ['确定'] //按钮
    //             , icon: 2
    //             , offset: '150px'
    //         }, function (index) {
    //             layer.close(index);
    //         });
    //         return;
    //     }
    //     document.form1.action = "ch_shippinglist_cell_toexcel.php?Ids=" + Ids;
    //     document.form1.target = "download";
    //     document.form1.submit();
    // }

    function ToExcel() {
        var CompanyId = jQuery("#CompanyId").val();
        var Building = jQuery("#Building").val();
        var Floor = jQuery("#Floor").val();

        document.form1.action = "ch_shippinglist_cell_toexcel.php?CompanyId=" + CompanyId + "&Building=" + Building + "&Floor=" + Floor;
        document.form1.target = "download";
        document.form1.submit();
    }


    function enter() {
        layer.open({
            type: 2,
            title: false,
            closeBtn: 1,
            area: ['500px', '200px'],
            btn: false,
            fixed: false, //不固定
            // maxmin: true,
            offset: '150px',
            content: 'ch_shippinglist_cell_enter.php',
            success: function (layero) {
                layero.find('.layui-layer-btn').css('text-align', 'center')
            },
            yes: function (index) {
                // layer.close(layer.index);
                var $table = layer.getChildFrame('#NoteTable', index);
                var upload = $table.find('input[name="ExcelFile"]').val();

                if (!upload) {
                    layer.confirm('未选择上传文件！', {
                        btn: ['确定'] //按钮
                        , icon: 2
                        , offset: '150px'
                    }, function (index) {
                        layer.close(index);
                    });
                    return;
                }
                // RefreshPage("ch_shippinglist_cell")
                window.location.reload();

            },
            btn2: function (index) {//layer.alert('aaa',{title:'msg title'});  ////点击取消回调
                layer.close(index);
            },
            cancel: function(index, layero){
                // RefreshPage("ch_shippinglist_cell")
                window.location.reload();
            }

        });
    }

    function upCell(e, x) {
        var f = jQuery("#cell" + e).text();
        layer.open({
            type: 1,
            title: '修改出货单元',
            area: ['300px', '200px'],
            btn: ['确定', '取消'],
            fixed: false, //不固定
            // maxmin: true,
            offset: '150px',
            content: '<div style="text-align: center;margin-top: 25px;"><p>默认出货单元：<input type="text" id="mCell" value="' + f + '" disabled/></p>修改出货单元：<input type="text" id="cell" value=""/></div>',
            success: function (layero) {
                layero.find('.layui-layer-btn').css('text-align', 'center')
            },
            yes: function (index) {
                layer.close(layer.index);
                var mCell = jQuery("#mCell").val();
                var cell = jQuery("#cell").val();
                if (mCell == cell || cell == null) {
                    layer.confirm('出货单元未更改！', {
                        btn: ['确定'] //按钮
                        , icon: 2
                        , offset: '150px'
                    }, function (index) {
                        layer.close(index);
                    });
                    return;
                }
                var url = "ch_shippinglist_cell_save.php";
                var ajax = InitAjax();
                ajax.open("POST", url, true);
                ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                ajax.onreadystatechange = function () {
                    if (ajax.readyState == 4) {// && ajax.status ==200
                        var json = JSON.parse(ajax.responseText.trim());
                        if (json.status == "Y") {//更新成功
                            layer.confirm('修改单元成功！', {
                                btn: ['确定'] //按钮
                                , icon: 1
                                , offset: '150px'
                            }, function (index) {
                                layer.close(index);
                                jQuery("#cell" + e).text(cell);
                            })
                        } else {
                            layer.confirm('修改单元失败！', {
                                btn: ['确定'] //按钮
                                , icon: 2
                                , offset: '150px'
                            }, function (index) {
                                layer.close(index);
                            })
                        }
                    }
                };
                ajax.send("ActionId=upCell&PId=" + e + "&cell=" + cell);


            },
            btn2: function (index) {//layer.alert('aaa',{title:'msg title'});  ////点击取消回调
                layer.close(index);
            }

        });

    }
</script>
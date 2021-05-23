<?php
$checkResult = mysql_query("SELECT JobId,BranchId FROM $DataIn.staffmain WHERE Number=$Login_P_Number ORDER BY Number LIMIT 1", $link_id);
$JobId = mysql_result($checkResult, 0, "JobId");
$BranchId = mysql_result($checkResult, 0, "BranchId");

$SearchRows = " AND L.Estate>0 ";//生产分类里的ID
$WorkShopList = "";

$WorkShopResult = mysql_query("SELECT SC.WorkShopId,W.Name AS WorkShopName 
FROM  $DataIn.ck5_llsheet L 
INNER JOIN $DataIn.yw1_scsheet SC  ON SC.sPOrderId = L.sPOrderId
INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId
WHERE 1 $SearchRows GROUP BY SC.WorkShopId  ORDER BY SC.ActionId DESC", $link_id);
if ($WorkShopRow = mysql_fetch_array($WorkShopResult)) {
    $WorkShopList = "<select name='blWorkShopId' id='blWorkShopId' style = 'width:100px' onChange='ResetPage(9,5)'>";
    $i = 1;
    do {
        $theWorkShopId = $WorkShopRow["WorkShopId"];
        $theWorkShopName = $WorkShopRow["WorkShopName"];
        $blWorkShopId = $blWorkShopId == "" ? $theWorkShopId : $blWorkShopId;
        if ($blWorkShopId == $theWorkShopId) {
            $WorkShopList .= "<option value='$theWorkShopId' selected>$theWorkShopName</option>";
            $SearchRows .= " AND SC.WorkShopId='$theWorkShopId'";
        }
        else {
            $WorkShopList .= "<option value='$theWorkShopId'>$theWorkShopName</option>";
        }
        $i++;
    } while ($WorkShopRow = mysql_fetch_array($WorkShopResult));
    $WorkShopList .= "</select>";
}
else {
    $SearchRows .= " AND SC.WorkShopId='$blWorkShopId' ";
}

//echo $blWorkShopId;
// 骨架搭建|脱模入库
$arrayllType = [
    [
        'id'   => '1',
        'name' => '骨架搭建',
    ],
    [
        'id'   => '2',
        'name' => '脱模入库',
    ],
];
$llTypeList = "<select name='llTypeId' id='llTypeId' style = 'width:100px' onChange='ResetPage(9,5)'>";
foreach ($arrayllType as $type) {
    $thellTypeId = $type['id'];
    $thellTypeName = $type['name'];
    $llTypeId = $llTypeId == "" ? $thellTypeId : $llTypeId;
    if ($llTypeId == $thellTypeId) {
        $llTypeList .= "<option value='$thellTypeId' selected>$thellTypeName</option>";
    }
    else {
        $llTypeList .= "<option value='$thellTypeId'>$thellTypeName</option>";
    }
}

$llTypeList .= "</select>";

// 构件类型
$partTypeList = $SearchRows1 = '';
$partTypeList .= "<select name='bigClass' id='bigClass' onchange='document.form1.submit();'>";
$partTypeList .= "<option value='all' selected>全部构件类型</option>";
if ($bigClass == "9002" || $bigClass == '') {
    $partTypeList .= "<option value='9002' selected>钢筋半成品/桁架</option>";
    $SearchRows1 = " and T.TypeId = '9002' OR D.Spec = '桁架' ";
}
else {
    $partTypeList .= "<option value='9002' >钢筋半成品/桁架</option>";
}
if ($bigClass == "9006") {
    $partTypeList .= "<option value='9006' selected>混凝土</option>";
    $SearchRows1 = " and T.TypeId = '9006' ";
}
else {
    $partTypeList .= "<option value='9006' >混凝土</option>";
}
if ($bigClass == "9019") {
    $partTypeList .= "<option value='9019' selected>预埋</option>";
    $SearchRows1 = " and T.TypeId = '9019' AND D.Spec <> '桁架' ";
}
else {
    $partTypeList .= "<option value='9019' >预埋</option>";
}
if ($bigClass == "9017") {
    $partTypeList .= "<option value='9017' selected>构件半成品</option>";
    $SearchRows1 = " and T.TypeId = '9017' ";
}
else {
    $partTypeList .= "<option value='9017' >构件半成品</option>";
}

$partTypeList .= "</select>&nbsp;";

if ($llTypeId == 2) {
    include "item5_9_1.php"; //组装领料审核
}
else {
    include "item5_9_2.php";  //半成品领料审核
}
?>
<?php include '../basic/loading.php' ?>
</form>
</body>
<div id='divMessage' class="divMessage" style="position:absolute;width:300px;display:none;z-index:10;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #AAA;background:#CCC;padding-bottom:10px;">
  <table border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:100%;'>
    <tr>
      <td colspan="2" height="40" align="center" style="font-size:18px;color:#00F;" id='tdTitle'>确认为已领料吗?</td>
    </tr>
    <tr>
      <td colspan="2" height="25">
        <div id='divStockId' style="font-size:12px;padding-left:50px;"></div>
      </td>
    </tr>
    <tr>
      <td colspan="2" height="15"></td>
    </tr>
    <td width="95px" align="center">
      <input class='ButtonH_25' type='button' id='canelMsgBtn' value='取  消' onclick='MsgBtnClick(0)'/>
    </td>
    <td width="95px" align="center">
      <span class='ButtonH_25' id='okMsgBtn' onclick='MsgBtnClick(1)'>确  认</span></td>
    </tr>
  </table>
</div>
</html>
<script src="../model/pagefun.js"></script>
<script>
//导出领料单

function ToexportMaterial() {
    var choosedRow = 0;
    /*var Ids;*/

    var StockId;
    var sPOrderId;

    var checkboxArr = new Array();

    jQuery('input[name^="checkId"]:checkbox').each(function () {
        if (jQuery(this).prop('checked') == true) {
            /*  choosedRow=choosedRow+1;
              if (choosedRow == 1) {
                  Ids = jQuery(this).val();
              } else {
                  Ids = Ids + "," + jQuery(this).val();
              }*/


            checkboxArr[choosedRow] = this;

            choosedRow = choosedRow + 1;
            var Ids = jQuery(this).val();
            var arr = Ids.split("|");

            if (choosedRow == 1) {
                sPOrderId = arr[0];
                StockId = arr[1];
            } else {
                sPOrderId = sPOrderId + "," + arr[0];
                StockId = StockId + "," + arr[1];
            }


        }
    });

    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        return;
    }
    document.form1.action = "item5_9_2_export.php?sPOrderId=" + sPOrderId + "&StockId=" + StockId;
    document.form1.target = "download";
    document.form1.submit();
    window.setTimeout("window.location.reload()", 1000);//导出excel后刷新
}


function ToexportMaterialAll() {
    var blWorkShopId = jQuery('#blWorkShopId option:selected').val();
    var bigClass = jQuery('#bigClass option:selected').val();
    var bigClassName = jQuery('#bigClass option:selected').text();
    var llTypeId = jQuery('#llTypeId option:selected').val();
    var llTypeIdName = jQuery('#llTypeId option:selected').text();
    var khCompanyId = jQuery('#khCompanyId option:selected').val();
    var OrderPO = jQuery('#OrderPO option:selected').val();
    var creator = jQuery('#creator option:selected').val();
    var created = jQuery('#created option:selected').val();
    document.form1.action = "item5_9_2_exportAll.php?blWorkShopId=" + blWorkShopId + "&khCompanyId=" + khCompanyId + "&OrderPO=" + OrderPO + "&creator=" + creator + "&created=" + created+"&llTypeId="+llTypeId+"&bigClass="+bigClass+"&bigClassName="+bigClassName+"&llTypeIdName="+llTypeIdName;
    document.form1.target = "download";
    document.form1.submit();
    window.setTimeout("window.location.reload()", 1000);//导出excel后刷新
}

function CnameChanged(e) {
    var StuffCname = e.value;
    if (StuffCname.length >= 1) {
        e.style.color = '#000';
        document.getElementById("stuffQuery").disabled = false;
    }
    else {
        e.style.color = '#DDD';
        document.getElementById("stuffQuery").disabled = true;
    }
}

var curType;//1-领料确认 2-数据删除
var curTarget;
var curStockId;
var curStuffId;
var cursPOrderId;
var curthisQty;

function delLLdata(e, sPOrderId, StockId, StuffId, thisQty, msgStr) {

    var arrMsg = msgStr.split("|");
    msgStr = "";
    for (var i = 0; i < arrMsg.length; i++) {
        msgStr = msgStr + "<div>" + arrMsg[i] + "</div>";
    }

    var divMessage = document.getElementById("divMessage");
    divMessage.style.left = window.pageXOffset + (window.innerWidth - 300) / 2 + "px";
    divMessage.style.top = window.pageYOffset + (window.innerHeight - 150) / 2 + "px";
    curStockId = StockId;
    curStuffId = StuffId;
    cursPOrderId = sPOrderId;
    curthisQty = thisQty;
    curType = 2;
    curTarget = e;
    document.getElementById("tdTitle").innerHTML = "确认要删除该记录吗？";
    document.getElementById("divStockId").innerHTML = msgStr;
    divMessage.style.display = 'block';

    /*
      msgStr=msgStr+"\n确认要删除该记录吗？";
      if(confirm(msgStr)) {
            var url="item5_9_ajax.php?StockId="+StockId+"&sPOrderId="+sPOrderId+"&StuffId="+StuffId+"&thisQty="+thisQty+"&ActionId=41";
            var ajax=InitAjax();
            ajax.open("GET",url,true);
            ajax.onreadystatechange =function(){
             if(ajax.readyState==4){// && ajax.status ==200
             alert(ajax.responseText)
                 if(ajax.responseText=="Y"){//更新成功
                     e.innerHTML="已删除";
                     e.style.color="#FF0000";
                     e.onclick="";
                    //document.form1.submit();
                    }
                 else{
                    alert ("数据删除失败！");
                  }
                }
             }
           ajax.send(null);
           }

        */
}

function batchPass() {
    jQuery('.response').show();
    var choosedRow = 0;
    var StockId;
    var sPOrderId;

    var checkboxArr = new Array();

    jQuery('input[name^="checkId"]:checkbox').each(function () {
        if (jQuery(this).prop('checked') == true) {

            checkboxArr[choosedRow] = this;

            choosedRow = choosedRow + 1;
            var Ids = jQuery(this).val();
            var arr = Ids.split("|");

            if (choosedRow == 1) {
                sPOrderId = arr[0];
                StockId = arr[1];
            } else {
                sPOrderId = sPOrderId + "|" + arr[0];
                StockId = StockId + "|" + arr[1];
            }
        }
    });

    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        window.location.reload();
        return;
    }

    var url = "./item5_9_1_ajax.php";
    var ajax = InitAjax();
    ajax.open("POST", url, true);
    ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    ajax.onreadystatechange = function () {
        if (ajax.readyState == 4) {// && ajax.status ==200

            if (ajax.responseText.trim() == "Y") {//更新成功
                window.location.reload();
//                for (var i in checkboxArr) {
//                    checkboxArr[i].checked = false;
//                    checkboxArr[i].disabled = true;
//
//                    var index = jQuery(checkboxArr[i]).attr('id').replace('checkId', '');
//
//                    var updatetd = $("updatetd" + index);
//                    updatetd.innerHTML = "&nbsp;";
//                    updatetd.style.backgroundColor = "#339900";
//                    updatetd.onclick = "";
//                }
            }
            else {
                alert("领料单确认失败！");
                window.location.reload();
            }
        }
    };
    ajax.send("StockIds=" + StockId + "&sPOrderIds=" + sPOrderId);
}

function passLLdata(e, sPOrderId, StockId, msgStr) {//领料确认
    var arrMsg = msgStr.split("|");
    msgStr = "";
    for (var i = 0; i < arrMsg.length; i++) {
        msgStr = msgStr + "<div>" + arrMsg[i] + "</div>";
    }

    var divMessage = document.getElementById("divMessage");
    divMessage.style.left = window.pageXOffset + (window.innerWidth - 300) / 2 + "px";
    divMessage.style.top = window.pageYOffset + (window.innerHeight - 150) / 2 + "px";
    curStockId = StockId;
    cursPOrderId = sPOrderId;
    curType = 1;
    curTarget = e;
    document.getElementById("tdTitle").innerHTML = "确认为已领料吗？";
    document.getElementById("divStockId").innerHTML = msgStr;
    divMessage.style.display = 'block';

    /*
    var arrMsg=msgStr.split("|");
    msgStr="";
    for (var i=0;i<arrMsg.length;i++){
        msgStr=msgStr+arrMsg[i]+"\n";
    }
    //alert(StockId);
    msgStr=msgStr+"\n确认为已领料吗？";
    if(confirm(msgStr)) {
          var url="item5_9_ajax.php?StockId="+StockId+"&sPOrderId="+sPOrderId+"&ActionId=42";
          var ajax=InitAjax();
          ajax.open("GET",url,true);
          ajax.onreadystatechange =function(){
           if(ajax.readyState==4){// && ajax.status ==200
               if(ajax.responseText=="Y"){//更新成功
                   e.innerHTML="&nbsp;";
                   e.style.backgroundColor="#339900";
                   e.onclick="";
                  }
               else{
                  alert ("领料单确认失败！");
                }
              }
           }
         ajax.send(null);
       }
       */
}

function MsgBtnClick(index) {
    var divMessage = document.getElementById("divMessage");
    divMessage.style.display = 'none';

    //确认
    if (index == 1) {
        if (curType == 1) {
            var url = "item5_9_ajax.php?StockId=" + curStockId + "&sPOrderId=" + cursPOrderId + "&ActionId=42";
            var ajax = InitAjax();
            ajax.open("GET", url, true);
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {// && ajax.status ==200
                    if (ajax.responseText == "Y") {//更新成功
                        curTarget.innerHTML = "&nbsp;";
                        curTarget.style.backgroundColor = "#339900";
                        curTarget.onclick = "";
                    }
                    else {
                        alert("领料单确认失败！");
                    }
                }
            }
            ajax.send(null);
        } else if (curType == 2) {
            var url = "item5_9_ajax.php?StockId=" + curStockId + "&sPOrderId=" + cursPOrderId + "&StuffId=" + curStuffId + "&thisQty=" + curthisQty + "&ActionId=41";
            var ajax = InitAjax();
            ajax.open("GET", url, true);
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {// && ajax.status ==200
                    alert(ajax.responseText)
                    if (ajax.responseText == "Y") {//更新成功
                        curTarget.innerHTML = "已删除";
                        curTarget.style.color = "#FF0000";
                        curTarget.onclick = "";
                        //document.form1.submit();
                    }
                    else {
                        alert("数据删除失败！");
                    }
                }
            }
            ajax.send(null);
        }

    }
}

//全选 反选
var showmenuFlag = 0 //new
var showIpad = 1;
function showmenuie5(event) {
    event.preventDefault();

    if (showIpad == 0) showmenuFlag = 2;
    else showmenuFlag = 1; //new

    var menu = $("ie5menu");
    var Color = $("ColorSide");

    menu.style.display = "block";
    menu.style.visibility = "visible";

    var rightedge = table.offsetWidth - event.clientX;
    var bottomedge = table.offsetHeight - event.clientY;

    if (rightedge < menu.offsetWidth) {
        menu.style.left = window.scrollX + event.clientX - menu.offsetWidth + "px";
    }
    else {
        menu.style.left = window.scrollX + event.clientX + "px";
    }
    if (bottomedge < menu.offsetHeight) {
        menu.style.top = window.scrollY + event.clientY - menu.offsetHeight + "px";
    }
    else {
        menu.style.top = window.scrollY + event.clientY + "px";
    }
    Color.style.height = menu.offsetHeight + "px";
    return false;
}

function hidemenuie5() {
    var menu = $("ie5menu");
    menu.style.display = "none";
    menu.style.visibility = "hidden";
    showmenuFlag = 0; //new
}

function $(objName) {

    if (document.getElementById) {
        return document.getElementById(objName);
    }
    else if (document.layers) {
        return eval("document.layers['" + objName + "']");
    }
    else {
        return eval('document.all.' + objName);
    }
}

function myover(obj) {
    obj.className = "itemshovor";
}

function myout(obj) {
    obj.className = "menuitems";
}

var table = document.getElementById("menudiv592");
table.oncontextmenu = showmenuie5;

table.onclick = function () {  //new
    if (showmenuFlag == 1) {
        hidemenuie5();
    }

};

function All_elects() {
    jQuery('input[name^="checkId"]:checkbox').each(function () {

        if (jQuery(this).attr("disabled") == "disabled") {
        } else {
            if (this.checked == false) {
                this.checked = true;
            }
        }
    });
    hidemenuie5();
}
function Instead_elects() {
    jQuery('input[name^="checkId"]:checkbox').each(function () {

        if (jQuery(this).attr("disabled") == "disabled") {
        } else {
            if (this.checked == false) {
                this.checked = true;
            } else {
                this.checked = false;
            }
        }
    });
    hidemenuie5();
}
</script>
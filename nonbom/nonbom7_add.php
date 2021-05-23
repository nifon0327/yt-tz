<?php
//ewen 2013-03-04 OK
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增入库记录");//需处理
$nowWebPage = $funFrom . "_add";
$toWebPage = $funFrom . "_save";
$_SESSION["nowWebPage"] = $nowWebPage;
$Parameter = "fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth = 1100;
$tableMenuS = 700;
$CustomFun = "<span onclick='SearchRecord()' $onClickCSS>加入需求单</span>&nbsp;";
$CheckFormURL = "thisPage";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php echo $tableWidth ?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr valign="bottom">
    <td height="25" colspan="4" align="center" class="A0011"><span class='redB'>入库主单信息</span></td>
  </tr>
  <tr height="30">
    <td colspan="2" align="right" class="A0010">入库日期</td>
    <td colspan="2" class="A0001">&nbsp;<input name="rkDate" type="text" id="rkDate" style="width: 250px;" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})" datatype='Require' value="<?php echo date("Y-m-d H:m:s"); ?>" msg="没有填写或格式错误" readonly="readonly"/>
    </td>
  </tr>

  <tr bgcolor='<?php echo $Title_bgcolor ?>'>
    <td width="10" height="25" class="A0010" bgcolor="#FFFFFF">&nbsp;</td>
    <td align="right" width="70">供 应 商</td>
    <td>&nbsp;<select name="CompanyId" id="CompanyId" onchange="javascript:document.form1.submit();" style="width: 250px;">
            <?php
            //供应商:有采购且未收完货
            $GYS_Sql = "SELECT M.CompanyId,B.Forshort
			 FROM $DataIn.nonbom6_cgsheet A 
			 LEFT JOIN $DataIn.nonbom6_cgmain M ON A.Mid=M.Id  
			LEFT JOIN $DataPublic.nonbom3_retailermain B ON B.CompanyId=M.CompanyId 
			WHERE A.rkSign>0 AND A.Mid>0 GROUP BY M.CompanyId ORDER BY B.Forshort";
            $GYS_Result = mysql_query($GYS_Sql);
            while ($GYS_Myrow = mysql_fetch_array($GYS_Result)) {
                $ProviderTemp = $GYS_Myrow["CompanyId"];
                $CompanyId = $CompanyId == "" ? $ProviderTemp : $CompanyId;
                $Forshort = $GYS_Myrow["Forshort"];
                if ($ProviderTemp == $CompanyId) {
                    echo "<option value='$ProviderTemp' selected>$Forshort</option>";
                    $SearchRows = " AND B.CompanyId='$CompanyId'";
                }
                else {
                    echo "<option value='$ProviderTemp'>$Forshort</option>";
                }
            }
            ?>
      </select></td>
    <td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  <tr>
    <td width="10" class="A0010" height="25">&nbsp;</td>
    <td align="right">采&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;购</td>
    <td>&nbsp;<select name="BuyerId" id="BuyerId" onchange="javascript:document.form1.submit();" style="width: 250px;">
            <?php
            //供应商:有采购且未收完货
            $checkSql = "SELECT B.BuyerId,C.Name 
			FROM $DataIn.nonbom6_cgsheet A 
			LEFT JOIN $DataIn.nonbom6_cgmain B ON B.Id=A.Mid
			LEFT JOIN $DataPublic.staffmain C ON C.Number=B.BuyerId
			WHERE A.rkSign>0 AND A.Mid>0 $SearchRows GROUP BY B.BuyerId ORDER BY C.Name";
            $checkResult = mysql_query($checkSql);
            while ($checkRow = mysql_fetch_array($checkResult)) {
                $BuyerIdTemp = $checkRow["BuyerId"];
                $NameTemp = $checkRow["Name"];
                if ($BuyerIdTemp == $BuyerId) {
                    echo "<option value='$BuyerIdTemp' selected>$NameTemp</option>";
                }
                else {
                    echo "<option value='$BuyerIdTemp'>$NameTemp</option>";
                }
            }
            ?>
      </select></td>
    <td align="center" class="A0001">&nbsp;</td>
  </tr>
  <tr>
    <td width="10" height="30" class="A0010">&nbsp;</td>
    <td align="right">入库单号</td>
    <td>&nbsp;
      <input name="BillNumber" type="text" class="INPUT0100" id="BillNumber" style="width:250px;" value=""/>
    </td>
    <td width="10" class="A0001">&nbsp;</td>
  </tr>
  <tr>
    <td width="10" height="30" class="A0010">&nbsp;</td>
    <td align="right">入库凭证</td>
    <td>&nbsp;<input name="Bill" type="file" id="Bill" style="width: 380px;" Accept="pdf" Msg="文件格式不对,请重选"></td>
    <td width="10" class="A0001">&nbsp;</td>
  </tr>
  <tr>
    <td width="10" height="30" class="A0010">&nbsp;</td>
    <td align="right" valign="top">入库备注</td>
    <td>&nbsp;<textarea name="Remark" style="width:500px;" class="INPUT0100" id="Remark"></textarea>
    </td>
    <td width="10" class="A0001">&nbsp;</td>
  </tr>

  <tr>
    <td width="10" height="30" class="A0010">&nbsp;</td>
    <td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <span class='redB'>2.入库明细资料</span><input name="TempValue" type="hidden" id="TempValue"><input name='AddIds' type='hidden' id="AddIds"><input name="TempFixed" type="hidden" id="TempFixed">
    </td>
    <td width="10" class="A0001">&nbsp;</td>
  </tr>
</table>
<table border="0" width="<?php echo $tableWidth ?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr bgcolor='<?php echo $Title_bgcolor ?>'>
    <td width="30" class="A0010" bgcolor="#FFFFFF" height="25">&nbsp;</td>
    <td class="A1111" width="40" align="center">操作</td>
    <td class="A1101" width="40" align="center">序号</td>
    <td class="A1101" width="50" align="center">流水号</td>
    <td class="A1101" width="100" align="center">非bom配件编号</td>
    <td class="A1101" width="450" align="center">非bom配件名称</td>
    <td class="A1101" width="100" align="center">采购数量</td>
    <td class="A1101" width="100" align="center">未收数量</td>
    <td class="A1101" width="100" align="center">当前入库</td>
    <td width="30" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  <tr>
    <td width="30" class="A0010" height="180">&nbsp;</td>
    <td colspan="8" align="center" class="A0111">
      <div style="width:1040;height:100%;overflow-x:hidden;overflow-y:scroll">
        <table width='1040' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTable">
        </table>
      </div>
    </td>
    <td width="30" class="A0001">&nbsp;</td>
  </tr>
  <tr>
    <td width="30" class="A0010" height="40">&nbsp;</td>
    <td colspan="8"><span class='redB'>3.固定资产配件入库资料输入</span></td>
    <td width="30" class="A0001">&nbsp;</td>
  </tr>
</table>

<table border="0" width="<?php echo $tableWidth ?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr bgcolor='<?php echo $Title_bgcolor ?>'>
    <td width="30" class="A0010" bgcolor="#FFFFFF" height="25">&nbsp;</td>
    <td class="A1111" width="40" align="center">序号</td>
    <td class="A1101" width="80" align="center">配件编号</td>
    <td class="A1101" width="310" align="center">非bom配件名称</td>
    <td class="A1101" width="60" align="center">本次入库</td>
    <td class="A1101" width="30" align="center">序号</td>
    <td class="A1101" width="140" align="center">资产编号</td>
    <td class="A1101" width="100" align="center">入库地点</td>
    <td class="A1101" width="280" align="center">上传图片</td>
    <td width="30" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  <tr>
    <td width="30" class="A0010" height="180">&nbsp;</td>
    <td colspan="8" align="center" class="A0111" id="ShowInfo">
    </td>
    <td width="30" class="A0001">&nbsp;</td>
  </tr>
</table>
<?php
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script src="../model/showModalDialog.js"></script>
<script>
//GetDataInfo("0");
function GetDataInfo() {
    var obj = document.getElementsByName("IndepotQTY[]");
    var PropertySign = document.getElementsByName("PropertySign[]");
    var ShowInnerHTML = "<div style='width:1040px;height:100%;overflow-x:hidden;overflow-y:scroll'><table width='100%' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' id='ShowTable'>";
    var k = 0;
    tempK = 0;
    for (i = 0; i < obj.length; i++) {
        if (PropertySign[i].value == 1) {
            k = i + 1;
            var GoodsId = ListTable.rows[i].cells[3].innerHTML;
            var GoodsName = ListTable.rows[i].cells[4].innerHTML;
            var rkQty = obj[i].value;
            ShowInnerHTML += "<tr><td class='A0101' width='38' height='25' align='center' rowspan='" + rkQty + "'>" + k + "</td><td class='A0101' width='80' align='center' rowspan='" + rkQty + "'>" + GoodsId + "</td><td class='A0101' width='310' rowspan='" + rkQty + "' >" + GoodsName + "</td><td class='A0101' width='60' align='center' rowspan='" + rkQty + "'>" + rkQty + "</td>";
            for (j = 0; j < rkQty; j++) {
                tempK = j + 1;
                ShowInnerHTML += "<td class='A0101' width='30' align='center' >" + tempK + "<input type='hidden' name='tempGoodsId[]' id='tempGoodsId' value='" + GoodsId + "'></td><td class='A0101' width='140' align='center' ><input  type='text' name='GoodsNum[]' id='GoodsNum' size='16'></td><td class='A0101' width='100' align='center'><select name='CkId[]' id='CkId' style='width: 80px;' ><option value=''selected>请选择</option>";
                <?PHP
                $mySql = "SELECT Id,Name,Remark FROM $DataPublic.nonbom0_ck  WHERE Estate=1 AND TypeId IN (0,1)  order by  Remark";
                $result = mysql_query($mySql, $link_id);
                if ($myrow = mysql_fetch_array($result)) {
                    do {
                        $FloorId = $myrow["Id"];
                        $FloorRemark = $myrow["Remark"];
                        $FloorName = $myrow["Name"];
                        $echoInfo .= "<option value='$FloorId' >$FloorName</option>";
                    } while ($myrow = mysql_fetch_array($result));
                }
                ?>
                ShowInnerHTML = ShowInnerHTML + "<?PHP echo $echoInfo; ?>" + "</select></td><td class='A0101' width='275' ><input name='Picture[]' type='file' id='Picture[]'></td></tr>";
            }
            k++;
        }
    }
    ShowInnerHTML += "</table></div>";
    document.getElementById("ShowInfo").innerHTML = ShowInnerHTML;

    var defaultCkId = document.getElementsByName("defaultCkId[]"); //默认入库地点
    var CkId = document.getElementsByName("CkId[]");
    var n = 0;
    Lasttempn = 0;
    while (n < obj.length) {
        if (PropertySign[n].value == 1) {
            var tempn = obj[n].value;
            if (n == 0) tempm = n;
            else  tempm += parseInt(Lasttempn);
            for (m = tempm; m < parseInt(tempn) + parseInt(tempm); m++) {
                if (defaultCkId[n].value > 0) CkId[m].value = defaultCkId[n].value;
            }
        }
        Lasttempn = tempn;
        n = n + 1;
    }
}
function CheckForm() {
    var Message = ""
    if (ListTable.rows.length < 1) {
        Message = "没有设置入库的数据!";
    }
    /*
      var BillNumber=document.form1.BillNumber.value;
      if(BillNumber==""){
          Message="没有输入凭证单号.";
          }
      */
    var TempBuyerId = document.getElementById("BuyerId").value;
    if (TempBuyerId == "") {
        Message = "没有选择采购.";
    }
    if (Message != "") {
        alert(Message);
        return false;
    }
    else {
        var StockValues = "";
        //读取加入的数据
        var obj = document.getElementsByName("IndepotQTY[]");
        //alert(obj.length);
        for (i = 0; i < obj.length; i++) {
            if (StockValues == "") {
                StockValues = ListTable.rows[i].cells[2].innerHTML + "!" + ListTable.rows[i].cells[3].innerHTML + "!" + obj[i].value;
            }
            else {
                StockValues = StockValues + "|" + ListTable.rows[i].cells[2].innerHTML + "!" + ListTable.rows[i].cells[3].innerHTML + "!" + obj[i].value;
            }
        }
        //***********************************************************************固定资产信息保存
        var endSign = 0;
        // var ShowValues="";
        ShowTable = document.getElementById("ShowTable");
        if (ShowTable.rows.length > 0) {
            var GoodsIdobj = document.getElementsByName("tempGoodsId[]");
            var GoodsNumobj = document.getElementsByName("GoodsNum[]");
            var CkIdobj = document.getElementsByName("CkId[]");
            for (k = 0; k < GoodsIdobj.length; k++) {
                if (GoodsNumobj[k].value == "") {
                    endSign = 1;
                    break;
                }
                if (CkIdobj[k].value == "") {
                    endSign = 2;
                    break;
                }
            }
        }
        if (endSign == 1) {
            alert("此配件为固定资产，未填写完固定资产编号!");
            return false;
        }
        if (endSign == 2) {
            alert("请选择入库地点!");
            return false;
        }
        //************************************************
        document.form1.AddIds.value = StockValues;
        //  document.form1.TempFixed.value=ShowValues;
        document.form1.action = "nonbom7_save.php";
        document.form1.submit();
    }
}

function toTempValue(textValue) {
    document.form1.TempValue.value = textValue;
}
function Indepot(thisE, SumQty) {
    var oldValue = document.form1.TempValue.value;
    var thisValue = thisE.value;
    var CheckSTR = fucCheckNUM(thisValue, "Price");
    if (CheckSTR == 0) {
        alert("不是规范的数字！");
        thisE.value = oldValue;
        return false;
    }
    else {
        if ((thisValue > SumQty) || thisValue == 0) {
            alert("不在允许值的范围！");
            thisE.value = oldValue;
            return false;
        }
    }
    GetDataInfo();
}
//删除指定行
function deleteRow(rowIndex) {
    ListTable.deleteRow(rowIndex);
    ShowSequence(ListTable);
    GetDataInfo();
}

function ShowSequence(TableTemp) {
    for (i = 0; i < TableTemp.rows.length; i++) {
        var j = i + 1
        TableTemp.rows[i].cells[1].innerText = j;
    }
}
function SearchRecord() {
    var Jid = document.getElementById('CompanyId').value;
    var Bid = document.getElementById('BuyerId').value;
    var num = Math.random();
    wd = window.open("nonbom7_s1.php?r=" + num + "&Jid=" + Jid + "&Bid=" + Bid + "&tSearchPage=nonbom7&fSearchPage=nonbom7&SearchNum=2&Action=2", "BackStockId", "height=500,width=1000,scrollbars=yes,resizable=yes");

    if (wd)
        window.wd.focus();//判断窗口是否打开,如果打开,窗口前置
    winTimer = window.setInterval("wisclosed()", 500);
}
function wisclosed() {
    if (wd.closed) {
        BackStockId = window.returnVaule;//子窗体返回值
        if (BackStockId) {
            var Rowstemp = BackStockId.split("``");
            var Rowslength = Rowstemp.length;
            for (var i = 0; i < Rowslength; i++) {
                var Message = "";
                var FieldArray = Rowstemp[i].split("^^");
                //过滤相同的产品订单ID号
                for (var j = 0; j < ListTable.rows.length; j++) {
                    var StockIdtemp = ListTable.rows[j].cells[2].innerText;//隐藏ID号存于操作列
                    if (FieldArray[0] == StockIdtemp) {//如果流水号存在
                        Message = "需求流水号: " + FieldArray[0] + "的资料已在列表!跳过继续！";
                        break;
                    }
                }
                if (Message == "") {//$ValueSTR="$Id^^$GoodsId^^$GoodsName^^$Qty^^$Unreceive";
                    oTR = ListTable.insertRow(ListTable.rows.length);
                    tmpNum = oTR.rowIndex + 1;
                    //第一列:操作
                    oTD = oTR.insertCell(0);
                    oTD.innerHTML = "<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>";
                    oTD.data = "" + FieldArray[0] + "";
                    oTD.onmousedown = function () {
                        window.event.cancelBubble = true;
                    };
                    oTD.className = "A0101";
                    oTD.align = "center";
                    oTD.width = "40";
                    oTD.height = "20";

                    //第二列:序号
                    oTD = oTR.insertCell(1);
                    oTD.innerHTML = "" + tmpNum + "";
                    oTD.className = "A0101";
                    oTD.align = "center";
                    oTD.width = "40";

                    //三、流水号
                    oTD = oTR.insertCell(2);
                    oTD.innerHTML = "" + FieldArray[0] + "";
                    oTD.className = "A0101";
                    oTD.align = "center";
                    oTD.width = "50";

                    //四：配件ID
                    oTD = oTR.insertCell(3);
                    oTD.innerHTML = "" + FieldArray[1] + "";
                    oTD.className = "A0101";
                    oTD.align = "center";
                    oTD.width = "100";

                    //五:配件名称
                    oTD = oTR.insertCell(4);
                    oTD.innerHTML = "" + FieldArray[2] + "";
                    oTD.className = "A0101";
                    oTD.width = "450";

                    //六：采购数量
                    oTD = oTR.insertCell(5);
                    oTD.innerHTML = "" + FieldArray[3] + "";
                    oTD.className = "A0101";
                    oTD.align = "center";
                    oTD.width = "100";

                    //第九列:未收数量
                    oTD = oTR.insertCell(6);
                    oTD.innerHTML = "" + FieldArray[4] + "";
                    oTD.className = "A0101";
                    oTD.align = "center";
                    oTD.width = "100";

                    //第十列:本次入库
                    oTD = oTR.insertCell(7);
                    oTD.innerHTML = "<input type='text' name='IndepotQTY[]' id='IndepotQTY' style='width:80;' class='I0000L' value='" + FieldArray[4] + "' onblur='Indepot(this," + FieldArray[4] + ")' onfocus='toTempValue(this.value)'><input type='hidden' name='PropertySign[]' id='PropertySign' value='" + FieldArray[5] + "'><input type='hidden' name='defaultCkId[]' id='defaultCkId' value='" + FieldArray[6] + "'>";
                    oTD.className = "A0100";
                    oTD.width = "100";
                    GetDataInfo();
                }
                else {
                    alert(Message);
                }//if(Message=="")
            }//for(var i=0;i<Rowslength;i++)
        }//if (BackStockId)
        else {
            // alert("没有选取数据!");
            window.location.reload();
            return true;
        }
        window.clearInterval(winTimer);
    }
}


//附带不用修改浏览器安全配置的javascript代码，兼容ie， firefox全系列

function getPath(obj) {
    if (obj) {
        if (window.navigator.userAgent.indexOf("MSIE") >= 1) {
            obj.select();
            return document.selection.createRange().text;
        }

        else if (window.navigator.userAgent.indexOf("Firefox") >= 1) {
            if (obj.files) {
                return obj.files.item(0).getAsDataURL();
            }
            return obj.value;
        }
        return obj.value;
    }
}
//参数obj为input file对象
</script>

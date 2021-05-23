//窗口打开方式修改为兼容性的模态框 by ckt 2017-12-23
function searchStuffId(Action) {
    var SafariReturnValue = document.getElementById('SafariReturnValue');
    if (!arguments[1]) {
        var num = Math.random();
        SafariReturnValue.value = "";
        SafariReturnValue.callback = 'searchStuffId("",true)';
        var url = "/admin/stuffdata_s1.php?r=" + num + "&tSearchPage=stuffdata&fSearchPage=semifinishedbom&SearchNum=1&Action=" + Action;
        openFrame(url, 980, 650);//url需为绝对路径
        return false;
    }
    if (SafariReturnValue.value) {
        var FieldArray = SafariReturnValue.value.split("^^");
        SafariReturnValue.value = "";
        SafariReturnValue.callback = "";
        document.getElementById('mStuffId').value = FieldArray[0];
        document.getElementById('mStuffIdName').value = "(" + FieldArray[0] + ")" + FieldArray[1];
    }
}

//窗口打开方式修改为兼容性的模态框 by ckt 2017-12-23
function CPandsViewStuffId(Action) {
    var SafariReturnValue = document.getElementById('SafariReturnValue');
    if (!arguments[1]) {
        var mStuffId = document.getElementById('mStuffId').value;
        if (mStuffId == "" || mStuffId == null) {
            alert("请先行选择半成品配件");
            return false;
        }
        var num = Math.random();
        SafariReturnValue.value = "";
        SafariReturnValue.callback = 'CPandsViewStuffId("",true)';
        var url = "/admin/stuffdata_s1.php?r=" + num + "&tSearchPage=stuffdata&fSearchPage=semifinishedbom&SearchNum=2&mStuffId=" + mStuffId + "&Action=" + Action;
        openFrame(url, 980, 650);//url需为绝对路径
        return false;
    }
    if (SafariReturnValue.value) {
        var Rows = SafariReturnValue.value.split("``");//分拆记录
        SafariReturnValue.value = "";
        SafariReturnValue.callback = "";
        var Rowslength = Rows.length;//数组长度即领料记录数

        if (document.getElementById("TempMaxNumber")) {  ////给add by zx 2011-05-05 firfox and  safari不能用javascript生成的元素
            var TempMaxNumber = document.getElementById("TempMaxNumber");
            TempMaxNumber.value = TempMaxNumber.value * 1 + Rowslength * 1;
        }

        var mStuffId = document.getElementById('mStuffId').value;
        for (var i = 0; i < Rowslength; i++) {
            var Message = "";
            var FieldTemp = Rows[i];		//拆分后的记录
            var FieldArray = FieldTemp.split("^^");//分拆记录中的字段
            //alert(FieldTemp);
            //过滤相同的配件ID号
            for (var j = 0; j < ListTable.rows.length; j++) {
                var SIdtemp = ListTable.rows[j].cells[3].innerText;
                if (FieldArray[1] == SIdtemp || FieldArray[1] == mStuffId) {//如果流水号存在
                    Message = "配件: " + FieldArray[2] + " 已存在!跳过继续！";
                    break;
                }
            }
            if (Message == "") {
                oTR = ListTable.insertRow(ListTable.rows.length);

                //表格行数
                tmpNumQty = oTR.rowIndex;
                tmpNum = oTR.rowIndex + 1;

                //第1列:隐藏的配件ID
                oTD = oTR.insertCell(0);
                oTD.innerHTML = "<a href='#' onclick='deleteRow(this.parentNode)' title='删除当前行'>×</a>&nbsp;&nbsp;<a href='#' onclick='upMove(this.parentNode)' title='当前行上移'>∧</a>&nbsp;&nbsp;<a href='#' onclick='downMove(this.parentNode)' title='当前行下移'>∨</a>";
                oTD.className = "A0101";
                oTD.align = "center";
                oTD.width = "70";
                oTD.height = "20";
                //第2列:序号
                oTD = oTR.insertCell(1);
                oTD.innerHTML = "" + tmpNum + "";
                oTD.className = "A0101";
                oTD.align = "center";
                oTD.width = "50";
                //第3列:类别
                oTD = oTR.insertCell(2);
                oTD.innerHTML = "" + FieldArray[0] + "";
                oTD.className = "A0101";
                oTD.width = "90";
                //第4列:配件ID
                oTD = oTR.insertCell(3);
                oTD.innerHTML = "" + FieldArray[1] + "";
                oTD.className = "A0101";
                oTD.width = "50";
                //第5列:配件名称
                oTD = oTR.insertCell(4);
                oTD.innerHTML = "" + FieldArray[2] + "";
                oTD.className = "A0101";
                oTD.width = "310";
                //第6列:对应数量
                var cutQtys = calQtys(FieldArray[2]);

                oTD = oTR.insertCell(5);
                oTD.innerHTML = "<input name='Qty[]' type='text' id='Qty" + tmpNumQty + "' size='8' class='noLine' value='" + cutQtys + "' onchange='checkNum(this)' onfocus='toTempValue(this.value)'><input name='Fb[]' type='hidden' id='Fb" + tmpNumQty + "' value='" + FieldArray[6] + "'><input name='sPrice[]' type='hidden' id='sPrice" + tmpNumQty + "' value='" + FieldArray[5] + "'><input name='CostPrice[]' type='hidden' id='CostPrice" + tmpNumQty + "' value='" + FieldArray[7] + "'>";
                oTD.className = "A0101";
                oTD.align = "center";
                oTD.width = "70";
                //第7列:采购
                oTD = oTR.insertCell(6);
                oTD.innerHTML = "" + FieldArray[3] + "&nbsp;";
                oTD.className = "A0101";
                oTD.align = "center";
                oTD.width = "70";
                //第8列:供应商
                oTD = oTR.insertCell(7);
                oTD.innerHTML = "" + FieldArray[4] + "&nbsp;";
                oTD.className = "A0101";
                oTD.width = "119";
                //form1.hfield.value=tmpNum;
            }
            else {
                alert(Message);
            }
        }//end for
        GrossMargin();
        return true;
    }
    else {
        alert("没有选到配件！");
        return false;
    }
}

function checkInput() {
    //检查对应数量是否正确
    var Message = "";
    if (document.form1.mStuffId.value == "") {
        alert("没有指定加工配件！");
        return false;
    }

    var DataSTR = "";
    var Qty = document.getElementsByName('Qty[]');

    for (var i = 0; i < Qty.length; i++) {

        var thisType = getinnerText(ListTable.rows[i].cells[2]);
        var Type = thisType == "刀模" ? 2 : 1;
        var thisData = getinnerText(ListTable.rows[i].cells[3]);
        thisData = thisData + "^" + Qty[i].value + "^" + Type;
        if (DataSTR == "") {
            DataSTR = thisData;
        }
        else {
            DataSTR = DataSTR + "|" + thisData;
        }
    }

    if (DataSTR != "") {
        document.form1.SIdList.value = DataSTR;
        document.form1.action = "semifinishedbom_updated.php";
        document.form1.submit();
    }
    else {
        alert("没有加入任何配件！请先加入配件！");
        return false;
    }
    // CheckForm();
}

//窗口打开方式修改为兼容性的模态框 by ckt 2017-12-23
function addbpRate(e, tmpNumQty) {
//读取产品资料
    var SafariReturnValue = document.getElementById('SafariReturnValue');
    if(!arguments[2]){
        var num=Math.random();
        SafariReturnValue.value = "";
        SafariReturnValue.callback = 'addbpRate("","",true)';
        var url = "/admin/standbyrate_s1.php?r=" + num + "&tSearchPage=standbyrate&fSearchPage=pands&SearchNum=1&Action=1";
        openFrame(url, 780, 450);//url需为绝对路径
        return false;
    }
    if (SafariReturnValue.value) {
        var CL = SafariReturnValue.value.split("^^");
        SafariReturnValue.value = "";
        SafariReturnValue.callback = "";
        var bpRateArray = "bpRate" + tmpNumQty;
        var bpRate = document.getElementById(bpRateArray);
        //alert(CL[0]);
        bpRate.value = CL[0];//记录ID
        e.value = CL[1];
    }
}

//毛利计算
function GrossMargin() {
    var buyAmountRMB = 0;
    var buyCostAmountRMB = 0;
    var taxtCbThis = 0; //
    var cbThis = 0;
    //计算配件总价
    Q = document.getElementsByName("Qty[]");//配件对应数量数组
    F = document.getElementsByName("Fb[]");//配件货币数组
    P = document.getElementsByName("sPrice[]");//配件成本数组
    C = document.getElementsByName("CostPrice[]");

    for (var j = 0; j < ListTable.rows.length; j++) {

        var tempQty = Q[j].value;		//对应数量，以/来拆分前后两部分
        var tempFb = F[j].value * 1;	//货币ID
        var tempPrice = P[j].value * 1;	//配件含税成本
        var tempCostPrice = C[j].value * 1;
        var QtyArray = tempQty.split("/");

        if (QtyArray.length == 2) {
            taxtCbThis = (QtyArray[0] * tempPrice) / (QtyArray[1] * 1);
            cbThis = (QtyArray[0] * tempCostPrice) / (QtyArray[1] * 1);
        }
        else {
            taxtCbThis = QtyArray[0] * tempPrice;
            cbThis = QtyArray[0] * tempCostPrice;
        }
        buyAmountRMB += taxtCbThis;
        buyCostAmountRMB += cbThis;

    }//end for

    document.form1.taxtPrice.value = Number(buyAmountRMB).toFixed(4);
    document.form1.cbHZ.value = Number(buyCostAmountRMB).toFixed(4);
}

function checkNum(obj) {
    var oldScore = document.form1.TempValue.value;
    var TempScore = obj.value;
    var reBackSign = 0;
    var TempScore = funallTrim(TempScore);
    var ScoreArray = TempScore.split("/");
    var LengthScore = ScoreArray.length;
    if (LengthScore > 2) {
        reBackSign = 0;
    }
    else {
        if (LengthScore == 1) {
            //检查数字格式
            var NumTemp = ScoreArray[0];
            var reBackSign = fucCheckNUM(NumTemp, "Price");//1是数字，0不是数字
        }
        else {
            var NumTemp0 = ScoreArray[0];
            var reBackSign = fucCheckNUM(NumTemp0, "Price");//1是数字，0不是数字
            if (reBackSign == 1) {
                var NumTemp1 = ScoreArray[1];
                reBackSign = fucCheckNUM(NumTemp1, "Price");//1是数字，0不是数字
            }
        }
    }
    if (reBackSign == 0) {
        alert("对应数量不正确！");
        obj.value = oldScore;
        return false;
    }
    else {
        GrossMargin();
    }
}

function calQtys(oCnameStr) {
    var mCnameStr = document.getElementById('mStuffIdName').value;
    var mSizes = getSize(mCnameStr);
    if (mSizes.length == 0) return 1;

    var oSizes = getSize(oCnameStr);
    if (oSizes.length == 0) return 1;

    //var cutf=osizes[1]/gsizes[0];
    //刀数
    var cuts = Math.floor(oSizes[1] / mSizes[0]);
    //每刀下料支数
    var onecuts = Math.floor(oSizes[0] / mSizes[1] - 1);
    //每片余料下料支数
    var onemores = Math.floor((oSizes[1] - mSizes[0] * cuts) / mSizes[1] - 1);
    //var onemores=Math.floor((cutf-cuts)*gsizes[0]/gsizes[1]*2-1);
    //余料片数
    var mores = Math.floor(osizes[0] / mSizes[0]);
    //alert(cuts+"|"+onecuts+"|"+mores+"|"+onemores);
    var cutQtys = cuts * onecuts + mores * onemores;

    return cutQtys;
}

/*
	以下为默认表格数据操作方法
*/

function toTempValue(textValue) {
    document.form1.TempValue.value = textValue;
}

function downMove(tt) {
    //var nowRow=tt.parentElement.rowIndex;

    var nowRow;
    if (tt.parentElement == null || tt.parentElement == "undefined") {  //add by zx 2011-05-31 Firfox不支持 parentElement，只支持parentNode
        //alert("downMove2")
        nowRow = tt.parentNode.rowIndex;
    }
    else {
        nowRow = tt.parentElement.rowIndex;
    }

    for (i = 0; i < ListTable.rows.length; i++) {
        ListTable.rows[i].style.backgroundColor = "#ffffff";
    }
    ListTable.rows[nowRow].style.backgroundColor = "#999999";

    var nextRow = nowRow + 1;
    if (ListTable.rows[nextRow] != null) {
        //ListTable.rows[nowRow].swapNode(ListTable.rows[nextRow]);
        swapNode(ListTable.rows[nowRow], ListTable.rows[nextRow]);
        ShowSequence();
    }
}

function swapNode(node1, node2) {
    var parent = node1.parentNode;//父节点
    var t1 = node1.nextSibling;//两节点的相对位置
    var t2 = node2.nextSibling;
    if (t1) parent.insertBefore(node2, t1);
    else parent.appendChild(node2);
    if (t2) parent.insertBefore(node1, t2);
    else parent.appendChild(node1);

}

function upMove(tt) {
    //var nowRow=tt.parentElement.rowIndex;

    var nowRow;
    if (tt.parentElement == null || tt.parentElement == "undefined") {  //add by zx 2011-05-31 Firfox不支持 parentElement，只支持parentNode
        //alert("downMove2")
        nowRow = tt.parentNode.rowIndex;
    }
    else {
        nowRow = tt.parentElement.rowIndex;
    }

    for (i = 0; i < ListTable.rows.length; i++) {
        ListTable.rows[i].style.backgroundColor = "#ffffff";
    }
    ListTable.rows[nowRow].style.backgroundColor = "#999999";
    var preRow = nowRow - 1;
    if (preRow >= 0) {
        //ListTable.rows[nowRow].swapNode(ListTable.rows[preRow]);
        swapNode(ListTable.rows[nowRow], ListTable.rows[preRow]);
        ShowSequence();
    }
}

function ShowSequence() {
    for (i = 0; i < ListTable.rows.length; i++) {
        var j = i + 1;
        //ListTable.rows[i].cells[1].innerText=j;
        ListTable.rows[i].cells[1].innerHTML = j;
    }
    GrossMargin();
}

function deleteRow(tt) {
    //var rowIndex=tt.parentElement.rowIndex;
    var rowIndex;
    if (tt.parentElement == null || tt.parentElement == "undefined") {  //add by zx 2011-05-31 Firfox不支持 parentElement，只支持parentNode
        //alert("downMove2")
        rowIndex = tt.parentNode.rowIndex;
    }
    else {
        rowIndex = tt.parentElement.rowIndex;
    }

    ListTable.deleteRow(rowIndex);
    ShowSequence();
}


function getinnerText(e) {
    //若浏览器支持元素的innerText属性，则直接返回该属性 
    if (e.innerText) {
        return e.innerText;
    }
    var t = "";
    e = e.childNodes || e;
    //遍历子元素的所有子元素
    for (var i = 0; i < e.length; i++) {
        //若为文本元素，则累加到字符串t中。
        if (e[i].nodeType == 3) {
            t += e[i].nodeValue;
        }
        //否则递归遍历元素的所有子节点
        else {
            t += getText(e[i].childNodes);
        }
    }
    return t;
}

//取得尺寸
function getSize(nameStr) {
    var pos = nameStr.indexOf("mm");
    var sizes = new Array();

    if (pos > 2) {
        nameStr = nameStr.substring(0, pos);
        var sizeArr = nameStr.split("x");
        var counts = sizeArr.length;
        switch (counts) {
            case 2:
                if (fucCheckNUM(sizeArr[1], "Price") == 1) {
                    sizes[0] = getFirstSize(sizeArr[0]);
                    if (sizes[0] * 1 > 0) {
                        sizes[1] = sizeArr[1];
                    }
                }
                break;
            case 3:
                if (fucCheckNUM(sizeArr[1], "Price") == 1 && fucCheckNUM(sizeArr[2], "Price") == 1) {
                    sizes[0] = sizeArr[1];
                    sizes[1] = sizeArr[2];
                }
                break;
            default:
                if (counts > 3) {
                    if (fucCheckNUM(sizeArr[counts - 1], "Price") == 1 && fucCheckNUM(sizeArr[counts - 2], "Price") == 1) {
                        sizes[0] = sizeArr[counts - 1];
                        sizes[1] = sizeArr[counts - 2];
                    }
                }
                break;
        }
    }
    return sizes;
}

function getFirstSize(Str) {
    var lens = Str.length;
    var sPos = -1;
    for (var i = lens - 1; i > 0; i--) {
        var c = Str.substring(i, 1);
        if (fucCheckNUM(c, "Price") == 0) {
            break;
        }
        sPos = i;
    }

    if (sPos >= 0) {
        return Str.substring(sPos, lens - sPos - 1);
    }
    else {
        return 0;
    }
}


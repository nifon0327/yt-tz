<?php
//二合一已更新 业务专用
//标准$LockRemark
if ($isTotal == 1)  //专为统计2011-03-26
{
    echo "<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
    echo "<tr bgcolor='$theDefaultColor'>";
    $ColbgColor = $ColbgColor == "" ? "bgcolor='#FFFFFF'" : $ColbgColor;
    //上下左右（1表有边，0表示无），上色下色左色右色  （1表黑色，0表示白色）
    echo "<td class='B01111110' width='$Field[$m]' align='center' $ColbgColor> $ShowtotalRemark </td>";
    $m = $m + 2;
    echo "<td class='B01011110' width='$Field[$m]' align='center' >&nbsp;</td>";//$OrderSignColor为订单状态标记色
    for ($k = 0; $k < count($ValueArray); $k++) {
        $currentcount = count($ValueArray);  //add by zx 2011-03-26
        if ($ValueArray[$k][4] == "") {
            $m = $m + 2;
            $Value0 = $ValueArray[$k][0];
            //add by zx  201100326
            if ($m == ($Count - 1)) {
                $Field[$m] = "";
            }
            if ($ValueArray[$k][3] == "...") {
                $Value0 = "<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>$Value0</NOBR></DIV>";
            }


            if ($Value0 == "&nbsp;") {  //无数值时
                $ValueNext = "&nbsp;";
                if ($k < $currentcount - 1) {
                    $ValueNext = $ValueArray[$k + 1][0];
                } //下一个值
                if (($ValueNext != "&nbsp;") || ($Field[$m] == "")) {  //如果当前是空的，当前下一个不空值。或是最后一列，则要封下右
                    //echo"<td  class='A0101' width='$Field[$m]' ".$ValueArray[$k][1]." ".$ValueArray[$k][2].">".$Value0."</td>";
                    echo "<td  class='A0101' width='$Field[$m]' " . $ValueArray[$k][1] . " " . $ValueArray[$k][2] . ">" . $Value0 . "</td>";
                }
                else {
                    echo "<td  class='B01011110' width='$Field[$m]' " . $ValueArray[$k][1] . " " . $ValueArray[$k][2] . ">" . $Value0 . "</td>";
                }
            }
            else {  //有数值在时
                $ValueNext = "";
                if ($k < $currentcount - 1) {
                    $ValueNext = $ValueArray[$k + 1][0];
                } //下一个值
                if ($ValueNext == "&nbsp;") { //如果当前是不空的，当前下一为空值。则要封下 即可

                    echo "<td  class='B01011110' width='$Field[$m]' " . $ValueArray[$k][1] . " " . $ValueArray[$k][2] . ">" . $Value0 . "</td>";
                }
                else {

                    echo "<td  class='A0101' width='$Field[$m]' " . $ValueArray[$k][1] . " " . $ValueArray[$k][2] . ">" . $Value0 . "</td>";
                }
            }
        }
    }
    echo "</tr></table>";
}
else {    ////一般情况
    if ($Locks == 0) {//锁定状态:A一种是可以操作记录（分权限）；B一种是不可操作记录（不分权限）
        if ($Keys & mLOCK) {
            if ($LockRemark != "") {//财务强制锁定
                $Choose = "&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='$LockRemark' width='15' height='15'>";
            }
            else {
                $Choose = "<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/lock.png' width='15' height='15'>";
            }
        }
        else {        //A2：无权限对锁定记录操作
            $Choose = "&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='锁定操作!' width='15' height='15'>";
        }
    }
    else {
        if (($Keys & mUPDATE) || ($Keys & mDELETE) || ($Keys & mLOCK)) {//有权限
            if ($LockRemark != "") {
                $Choose = "&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='$LockRemark' width='15' height='15'>";
            }
            else {
                $Choose = "<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/unlock.png' width='15' height='15'>";
            }
        }
        else {//无权限
            $Choose = "&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='锁定操作!' width='15' height='15'>";
        }
    }
    //echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
    //$ColsNumber着色列数
    echo "<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
    echo "<tr bgcolor='$theDefaultColor'	onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber,$unColorCol);'
		onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber,$unColorCol);' 
		onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber,$unColorCol);'>";
    $ColbgColor = $ColbgColor == "" ? "bgcolor='#FFFFFF'" : $ColbgColor;
    if ($ChooseOut != "N") {
        $FieldWidth = $Field[$m];
        echo "<td class='A0111' width='$FieldWidth' align='center' $ColbgColor>$Choose&nbsp;$showPurchaseorder</td>";
        $m = $m + 2;
        if ($ClientOrder != "") {
            $j = $ClientOrder;
        }
        $FieldWidth = $Field[$m];
        echo "<td class='A0101' width='$FieldWidth' align='center' $OrderSignColor  Title='$OrderCgRemark' >$j</td>";//$OrderSignColor为订单状态标记色 $ClientOrder 原单在序号列显示
    }
    else {
        if ($myOpration != "") {
            $FieldWidth = $Field[$m];
            echo "<td class='A0111' width='$FieldWidth' align='center' $ColbgColor>$myOpration</td>";
            $m = $m + 2;
            $FieldWidth = $Field[$m];
            echo "<td class='A0101' width='$FieldWidth' align='center' $OrderSignColor  Title='$OrderCgRemark'>$j</td>";
        }
        else {
            echo "<td class='A0111' width='$Field[$m]' align='center' height='20' $OrderSignColor  Title='$OrderCgRemark'>$j</td>";
        }
    }
    for ($k = 0; $k < count($ValueArray); $k++) {
        $currentcount = count($ValueArray);
        if ($ValueArray[$k][4] == "") {
            $m = $m + 2;
            $Value0 = $ValueArray[$k][0];
            $Value0 = strlen($Value0) > 0 ? $Value0 : "&nbsp;";
            if (isSafari6() == 0) {
                if ($m == ($Count - 1)) {
                    $Field[$m] = "";
                }
            }
            if ($ValueArray[$k][3] == "...") {
                $Value0 = "<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>$Value0</NOBR></DIV>";
            }
            if ($ValueArray[$k][3] == "line") {
                $FieldWidth = $Field[$m] - 1;
                echo "<td  class='A0100' width='$FieldWidth' " . $ValueArray[$k][1] . " " . $ValueArray[$k][2] . ">" . $Value0 . "</td>";
            }
            else {
                echo "<td  class='A0101' width='$Field[$m]' " . $ValueArray[$k][1] . " " . $ValueArray[$k][2] . ">" . $Value0 . "</td>";
            }
        }
    }
    echo "</tr></table>";
    $i++;
    $j++;
}
?>
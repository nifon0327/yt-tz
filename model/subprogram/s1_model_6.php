<?php
// by  qianyunlai.com
$Keys=31;

//标准
if ($Locks == 0) {//锁定状态
    if ($Keys & mLOCK) {
        $Choose = "<input name='checkid[$i]' type='checkbox' id='checkid$i' value=\"$checkidValue\" disabled><img src='../images/lock.png' width='15' height='15'>";
    } else {
        $Choose = "&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='$LockRemark' width='15' height='15'>";
    }
} else {
    if (($Keys & mADD) || ($Keys & mLOCK)) {//有权限,来自新增页面，所以只需新增权限或锁定权限
        $Choose = "<input name='checkid[$i]' type='checkbox' id='checkid$i' value=\"$checkidValue\" disabled><img src='../images/unlock.png' width='15' height='15'>";
    } else {//无权限
        $Choose = "&nbsp;";
    }
}
echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
echo"<tr onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);'
 onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
 onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";

echo"<td class='A0111' width='$Field[$m]' align='center'>$Choose&nbsp;</td>";
$m=$m+2;
echo"<td class='A0101' width='$Field[$m]' align='center'>$j</td>";
for ($k = 0; $k < count($ValueArray); $k++) {
    $m = $m + 2;
    echo "<td  class='A0101' width='$Field[$m]' " . $ValueArray[$k][1] . " " . $ValueArray[$k][2] . ">" . $ValueArray[$k][0] . "</td>";
}
echo "</tr></table>";
$i++;
$j++;
?>
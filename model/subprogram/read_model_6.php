<?php
//二合一已更新 checkid[]
//标准$LockRemark
$Keys = 31;
if($Locks==0){//锁定状态:A一种是可以操作记录（分权限）；B一种是不可操作记录（不分权限）
    if($Keys & mLOCK){
        if($LockRemark!=""){//财务强制锁定
            $Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='$LockRemark' width='15' height='15'>";
        }
        else{
            $Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/lock.png' width='15' height='15'>";
        }
    }
    else{
        $Choose="&nbsp;&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='锁定操作!!' width='15' height='15'>";
    }
}
else{
    if(($Keys & mUPDATE)||($Keys & mDELETE)|| ($Keys & mLOCK)){//有权限
        if($LockRemark!=""){
            $Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='$LockRemark' width='15' height='15'>";
        }
        else{
            $Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue'  disabled><img src='../images/unlock.png' width='15' height='15'>";
        }
    }
    else{//无权限
        $Choose="&nbsp;&nbsp;<img src='../images/lock.png' title='锁定操作!' width='15' height='15'>";
    }
}
echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#F2F3F5'>";
//$ColsNumber着色列数
echo"<tr bgcolor='$theDefaultColor'
	onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);'
	onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
	onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
$ColbgColor=$ColbgColor==""?"bgcolor='#F2F3F5'":$ColbgColor;
if($ChooseOut!="N"){
    echo"<td class='A0111' width='$Field[$m]' height='25' align='center' $ColbgColor>$Choose&nbsp;$showPurchaseorder</td>";
    $m=$m+2;
    echo"<td class='A0101' width='$Field[$m]' height='25' align='center' $OrderSignColor title='$WarnRemark'>$j</td>";//$OrderSignColor为订单状态标记色
}
else{
    if($myOpration!=""){

        echo"<td class='A0111' width='$Field[$m]' height='25' align='center' $ColbgColor>$myOpration</td>";
        $m=$m+2;
        echo"<td class='A0101' width='$Field[$m]' height='25' align='center' $OrderSignColor  title='$WarnRemark'>$j</td>";
    }
    else{
        echo"<td class='A0111' width='$Field[$m]' align='center' height='25' $OrderSignColor title='$WarnRemark'>$j</td>";
    }
}
for($k=0;$k<count($ValueArray);$k++){
    if($ValueArray[$k][4]==""){
        $m=$m+2;
        $Value0=$Value0_Title=$ValueArray[$k][0];
        if (isSafari6()==0){
            if ($m==($Count-1))
            {
                $Field[$m]="";
            }
        }
        $Value0=strlen($Value0)<=0?"&nbsp;": $Value0;
        if($ValueArray[$k][3]=="..."){
            $Value0="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0_Title'><NOBR>$Value0</NOBR></DIV>";
        }
        if($ValueArray[$k][3]=="line"){
            echo"<td  class='A0100' width='$Field[$m]' ".$ValueArray[$k][1]." ".$ValueArray[$k][2].">".$Value0."</td>";
        }
        else{
            echo"<td  class='A0101' width='$Field[$m]' ".$ValueArray[$k][1]." ".$ValueArray[$k][2].">".$Value0."</td>";
        }
    }
}
echo"</tr></table>";
$i++;
$j++;
?>
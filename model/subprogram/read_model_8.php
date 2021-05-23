<?php
//标准$LockRemark
if($Locks==0){//锁定状态:A一种是可以操作记录（分权限）；B一种是不可操作记录（不分权限）
	if($Keys & mLOCK){
		if($LockRemark!=""){//财务强制锁定
			$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='$LockRemark' width='15' height='15'>";
			}
		else{
			$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/lock.png' width='15' height='15'>";
			}
		}
	else{		//A2：无权限对锁定记录操作
		$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='锁定操作!!' width='15' height='15'>";
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
		$Choose="&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='锁定操作!' width='15' height='15'>";
		}
	}

echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
echo"<tr bgcolor='$theDefaultColor'
	onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);'
	onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
	onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";

//echo"<tr bgcolor='$theDefaultColor'>";

$ColbgColor=$ColbgColor==""?"bgcolor='#FFFFFF'":$ColbgColor;
$startwidth = $Field[$m]-1;
if ($TotalRecords==0){
	echo"<td class='A0111' width='$startwidth' align='center' >&nbsp;</td>";
}
else{
	echo"<td class='A0011' width='$startwidth' align='center' >&nbsp;</td>";
}

$m=$m+2;
echo"<td class='A0101' width='$Field[$m]' align='center'  >$Choose</td>";//$OrderSignColor为订单状态标记色
for($k=0;$k<count($subValueArray);$k++){
		$m=$m+2;
		$Value0=$Value0_Title=$subValueArray[$k][0];
               if (isSafari6()==0){
                    if ($m==($Count-1)){
                    	$Field[$m]="";
                    }
               }
        $Value0=strlen($Value0)<=0?"&nbsp;": $Value0;
		 if($subValueArray[$k][3]=="..."){
			$Value0="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0_Title'><NOBR>$Value0</NOBR></DIV>";
			}
		     echo"<td  class='A0101' width='$Field[$m]' ".$subValueArray[$k][1]." ".$subValueArray[$k][2].">".$Value0."</td>";
	}
$endWidth =$Field[$m];
$m=$m+2;
$endWidth+=$Field[$m]+3;
if ($TotalRecords==0){
     echo"<td  class='A0101' width='$endWidth'>&nbsp;</td>";
}
else{
	echo"<td  class='A0001' width='$endWidth'>&nbsp;</td>";
}

echo"</tr></table>";
$i++;
$tempk++;
?>
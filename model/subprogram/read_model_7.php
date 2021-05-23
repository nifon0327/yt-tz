<?php
//二合一已更新 checkid[]
//标准$LockRemark
if($Locks==0){//锁定状态:A一种是可以操作记录（分权限）；B一种是不可操作记录（不分权限）
	$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue'  disabled>&nbsp;<img src='../images/lock.png' width='15' height='15'>";
    }
else{
        $Choose="&nbsp;&nbsp;<img src='../images/lock.png' title='$LockRemark' width='15' height='15'>";
    }
if($delBtnClick==1){//记录是否可删除
	$Choose.="&nbsp;&nbsp;<img src='../images/delbtn.gif' width='14' height='14' title='删除' onclick='delRecords($i,$checkidValue)'>";
        $delBtnClick="";
    }
echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
//$ColsNumber着色列数
echo"<tr bgcolor='$theDefaultColor'
	onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);'
	onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
	onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
$ColbgColor=$ColbgColor==""?"bgcolor='#FFFFFF'":$ColbgColor;
if($ChooseOut!="N"){
	echo"<td class='A0111' width='$Field[$m]' align='right' $ColbgColor>$Choose&nbsp;$showPurchaseorder</td>";
	$m=$m+2;
	echo"<td class='A0101' width='$Field[$m]' align='center' $OrderSignColor>$j</td>";//$OrderSignColor为订单状态标记色
	}
else{
	if($myOpration!=""){
		echo"<td class='A0111' width='$Field[$m]' align='center' $ColbgColor>$myOpration</td>";
		$m=$m+2;
		echo"<td class='A0101' width='$Field[$m]' align='center' $OrderSignColor>$j</td>";
		}
	else{
		echo"<td class='A0111' width='$Field[$m]' align='center' height='20' $OrderSignColor>$j</td>";
		}
	}
for($k=0;$k<count($ValueArray);$k++){
	if($ValueArray[$k][4]==""){
		$m=$m+2;
		$Value0=$ValueArray[$k][0];
		//add by zx  201100326  IE_Fox
		if ($m==($Count-1))
		{
			$Field[$m]="";
		}
		if($ValueArray[$k][3]=="..."){
			$Value0="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>$Value0</NOBR></DIV>";
			}
		echo"<td  class='A0101' width='$Field[$m]' ".$ValueArray[$k][1]." ".$ValueArray[$k][2].">".$Value0."</td>";
		}
	}
echo"</tr></table>";
$i++;
$j++;
?>

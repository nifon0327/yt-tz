
<?php
//电信-zxq 2012-08-01
$Th_Col="序号|30|请款日期|60|金额|60|货币|50|说明|250|分类|80|票据|30|状态|30|审核退回原因|250|操作|50";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$wField=$Field;
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$wField[$i];
	$tableWidth+=$wField[$i];
}
if (isSafari6()==1){
   $tableWidth=$tableWidth+ceil($Count*1.5)+1;
}

$SearchRows="";
//供应商过滤
$GysList="";
$nowInfo="当前:行政费用报销";
$funFrom="item3_8";
$addWebPage=$funFrom . "_add.php";
$updateWebPage=$funFrom . "_update.php";
	$monthResult = mysql_query("SELECT S.Date FROM $DataIn.hzqksheet S WHERE 1 and S.Operator=$Login_P_Number $SearchRows group by DATE_FORMAT(S.Date,'%Y-%m') order by S.Date DESC",$link_id);
	$SearchRows.=$Estate==""?"":" and S.Estate=$Estate";
	if($monthRow = mysql_fetch_array($monthResult)) {
		$GysList="<select name='chooseMonth' id='chooseMonth' onchange='ResetPage(0,3)'>";
		do{
			$dateValue=date("Y-m",strtotime($monthRow["Date"]));
			if($FirstValue==""){
				$FirstValue=$dateValue;}
			$dateText=date("Y年m月",strtotime($monthRow["Date"]));
			if($chooseMonth==$dateValue){
				$GysList.="<option value='$dateValue' selected>$dateText</option>";
				$PEADate=" and DATE_FORMAT(S.Date,'%Y-%m')='$dateValue'";
				}
			else{
				$GysList.="<option value='$dateValue'>$dateText</option>";
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		if($PEADate==""){
			$PEADate=" and DATE_FORMAT(S.Date,'%Y-%m')='$FirstValue'";
			}
		$GysList.="</select>&nbsp;";
		}
		$SearchRows.=$PEADate;
	//月份
	//结付状态
                $TempEstateSTR="EstateSTR".strval($Estate);
	$$TempEstateSTR="selected";
	$GysList.="<select name='Estate' id='Estate' onchange='ResetPage(1,3)'>
	<option value='' $EstateSTR>全  部</option>
	<option value='1' $EstateSTR1>未处理</option>
	<option value='2' $EstateSTR2>请款中</option>
	<option value='3' $EstateSTR3>请款通过</option>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";

//有权限
$addBtnDisabled=$SubAction==31?"":"disabled";
$GysList2="<span class='ButtonH_25' id='addBtn' onclick=\"openWinDialog(this,'$addWebPage',500,700,'center')\" $addBtnDisabled>新 增</span>";


//步骤5：
echo"<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
	<td  width='60%' height='40px' class=''>$GysList </td><td width='20%'  class=''>$GysList2</td><td width='20%'  align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr></table>";
	echo "<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr>";
	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";
$DefaultBgColor=$theDefaultColor;
$i=1;
$mySql="SELECT S.Id,S.Mid,S.Content,S.Amount,S.Bill,S.ReturnReasons,S.Date,S.Estate,S.Locks,S.Operator,T.Name AS Type,C.Symbol AS Currency
 	FROM $DataIn.hzqksheet S 
	LEFT JOIN $DataPublic.adminitype T ON S.TypeId=T.TypeId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
	WHERE 1 AND S.Operator=$Login_P_Number $SearchRows order by S.Date DESC";
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
                              $Id=$myRow["Id"];
		$Mid=$myRow["Mid"];
		$Date=$myRow["Date"];
		$Amount=$myRow["Amount"];
		$Currency=$myRow["Currency"];
		$Content=$myRow["Content"];
		$Type=$myRow["Type"];
		$ReturnReasons=$myRow["ReturnReasons"]==""?"&nbsp;":"<sapn class=\"redB\">".$myRow["ReturnReasons"]."</span>";
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/cwadminicost/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="H".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="-";
			}
		$Locks=$myRow["Locks"];
		$Estate=$myRow["Estate"];
                                $UpdateIMG="&nbsp;";$UpdateClick="&nbsp;";
		switch($Estate){
			case "1":
				$Estate="<div align='center' class='redB' title='未处理'>×</div>";
				$LockRemark="";
                                                                $selUpdateWebPage=$updateWebPage. "?Id=" . $Id;
                                                                $UpdateIMG="<img src='../images/register.png' width='30'>";$UpdateClick="onclick=\"openWinDialog(this,'$selUpdateWebPage',500,700,'center')\" ";

				break;
			case "2":
				$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
				$LockRemark="记录已经请款，强制锁定操作！修改需退回。";
				$Locks=0;
				break;
			case "3":
				$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
				$LockRemark="记录已经请款通过，强制锁定操作！修改需退回。";
				$Locks=0;
				break;
			case "0":
				$checkPay= mysql_fetch_array(mysql_query("SELECT PayDate FROM $DataIn.hzqkmain WHERE Id='$Mid' LIMIT 1",$link_id));
				$PayDate=$checkPay["PayDate"];
				$Estate="<div align='center' class='greenB' title='已结付,结付日期：$PayDate'>√</div>";
				$LockRemark="";
				$Locks=1;
				break;
			}


                                               echo"<tr><td class='A0111' align='center'  height='30'>$i</td>";
			echo"<td class='A0101'>$Date</td>";
			echo"<td class='A0101'>$Amount</td>";
			echo"<td class='A0101'>$Currency</td>";
			echo"<td class='A0101'>$Content</td>";
			echo"<td class='A0101' >$Type</td>";
			echo"<td class='A0101' align='center'>$Bill</td>";
			echo"<td class='A0101' align='center' >$Estate</td>";
			echo"<td class='A0101'>$ReturnReasons</td>";
			echo"<td class='A0101' align='center' $UpdateClick>$UpdateIMG</td>";
			echo"</tr>";
	   }while($myRow = mysql_fetch_array($myResult));
	}
else{
	echo"<tr><td colspan='10' align='center' height='30' class='A0111'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
	}
echo "</table>";
	?>
</form>
</body>
<div id='divShadow' class="divShadow" style="display:none;z-index:2;" onDblClick="closeMaskDiv()"></div>
<div id="divPageMask" class="divPageMask" style="display:none; background-color:rgba(0,0,0,0.6);"></div>
<div id='winDialog' style="position:absolute;display:none;z-index:9;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #333;background:#CCC;" onDblClick="closeWinDialog()"></div>
</html>
<script language="javascript" src="checkform.js" type="text/javascript"></script>
<script language="javascript" src="showDialog/showDialog.js" type="text/javascript"></script>
<script>
//更新送货单数据
function UpdateRkmain(Mid){
	var rkdate=document.getElementById("Date").value;
	var BillNumber=document.getElementById("BillNumber").value;
	var Remark=document.getElementById("Remark").value;
	var Message="";
	var reg = /^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2})$/;
    if (rkdate.match(reg)==null) {
		Message="请输入正确的送货日期！";
	}
	if (BillNumber.trim()==""){
		Message=Message+"请输入送货单号！";
	}
	if(Message!=""){
		alert(Message);
		return false;
	   }
	else{
	   var url="item5_2_ajax.php?Mid="+Mid+"&Date="+rkdate+"&BillNumber="+BillNumber+"&Remark="+Remark+"&ActionId=20";
	  var ajax=InitAjax();
	  ajax.open("GET",url,true);
	  ajax.onreadystatechange =function(){
		if(ajax.readyState==4){// && ajax.status ==200
			if(ajax.responseText=="Y"){//更新成功
				document.form1.submit();
				}
			}
		}
	ajax.send(null);
	}

}


</script>
<?php
//ewen 2013-03-18 OK
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
//步骤1：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
	$monthResult = mysql_query("SELECT PayDate FROM $DataIn.nonbom11_djmain WHERE 1  group by DATE_FORMAT(PayDate,'%Y-%m') order by PayDate DESC",$link_id);
		if ($monthRow = mysql_fetch_array($monthResult)) {
			$MonthSelect.="<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
			do{
				$dateValue=date("Y-m",strtotime($monthRow["PayDate"]));
				$FirstValue=$FirstValue==""?$dateValue:$FirstValue;
				$dateText=date("Y年m月",strtotime($monthRow["PayDate"]));
				if($chooseMonth==$dateValue){
					$MonthSelect.="<option value='$dateValue' selected>$dateText</option>";
					$SearchRows=" AND  DATE_FORMAT(B.PayDate,'%Y-%m')='$dateValue'";
					}
				else{
					$MonthSelect.="<option value='$dateValue'>$dateText</option>";
					}
				}while($monthRow = mysql_fetch_array($monthResult));
			$SearchRows=$SearchRows==""?"and  DATE_FORMAT(B.PayDate,'%Y-%m')='$FirstValue'":$SearchRows;
			$MonthSelect.="</select>&nbsp;";
			}
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' $EstateSTR3>未结付</option>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";
	//月份
	echo $MonthSelect;
	$SearchRows.=" AND A.Estate=0";
	}
else{
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
$TitlePre="<br>&nbsp;&nbsp;退回原因:<input type=\"text\" id=\"ReturnReasons\" name=\"ReturnReasons\" style=\"width:600\"><p>";
//步骤5：可用功能输出
include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
$mRow=1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
	B.PayDate,B.PayAmount,B.Payee,B.Receipt,B.CheckSheet,B.Remark AS PayRemark,B.Locks AS MLocks,
	A.Id,A.Mid,A.Did,A.CompanyId,A.PurchaseID,A.Amount,A.Remark,A.Date,A.Estate,A.Locks,A.Operator,
	C.Forshort,D.Symbol,E.Id AS cgMid,F.Title
 	FROM $DataIn.nonbom11_djmain B
	LEFT JOIN $DataIn.nonbom11_djsheet A ON A.Mid=B.Id
	LEFT JOIN $DataPublic.nonbom3_retailermain C ON C.CompanyId=A.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	LEFT JOIN $DataIn.nonbom6_cgmain E ON E.PurchaseID=A.PurchaseID
	LEFT JOIN $DataPublic.my2_bankinfo F ON F.Id=B.BankId
	WHERE 1 $SearchRows order by A.Id DESC,A.Date DESC";
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	do{
		$m=1;
		//结付主表数据
		$Mid=$mainRows["Mid"];
		$PayDate=$mainRows["PayDate"];
		$PayAmount=$mainRows["PayAmount"];
		$BankName=$mainRows["Title"];
		$ImgDir="download/nonbomdj/";
		$Checksheet=$mainRows["Checksheet"];
		$Payee=$mainRows["Payee"];
		$Receipt=$mainRows["Receipt"];
		include "../model/subprogram/cw0_imgview.php";
		$PayRemark=$mainRows["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$mainRows[PayRemark]' width='16' height='16'>";
		$MLocks=$mainRows["MLocks"];
		//查货款结付日期
		$Did=$mainRows["Did"];
		$dfPayDate=$mainRows["dfPayDate"]==""?"&nbsp;":$mainRows["dfPayDate"];
		if($Did==0){
			$Did="<div class='redB'>未抵付</div>";
			$LockRemark="";
			}
		else{
			$Did="<a href='nonbom6_cwview.php?d=$Did' target='_blank'><span class='greenB'>已抵付-$Did</span></a>";
			$LockRemark="记录已经抵付，锁定操作！";
			}
		//结付明细数据
		$Id=$mainRows["Id"];
		$PurchaseID=$mainRows["PurchaseID"];
		$Forshort=$mainRows["Forshort"];
		$Symbol=$mainRows["Symbol"];
		$Remark=$mainRows["Remark"]==""?"&nbsp;":$mainRows["Remark"];
		$Date=$mainRows["Date"];
		$Operator=$mainRows["Operator"];
		include "../model/subprogram/staffname.php";
		$Amount=$mainRows["Amount"];

		$cgMid=$mainRows["cgMid"];
		$cgMidSTR=anmaIn($cgMid,$SinkOrder,$motherSTR);
		$PurchaseID="<a href='nonbom6_view.php?f=$cgMidSTR' target='_blank'>$PurchaseID</a>";
		$CompanyId=$mainRows["CompanyId"];
		$CompanyId=anmaIn($CompanyId,$SinkOrder,$motherSTR);
		$Forshort="<a href='nonbom3_view.php?d=$CompanyId' target='_blank'>$Forshort</a>";
		//输出
		if($Keys & mUPDATE || $Keys & mLOCK){
			$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"$funFrom"."_upmain\",$Mid)' src='../images/edit.gif' title='行号:$mRow 编号:$Mid 更新结付单资料!' width='13' height='13'>";
			}
		else{
			$upMian="&nbsp;";
			}
		if($MLocks==0){
			$Choose="<img src='../images/lock.png' title='主记录已锁定!' width='15' height='15'>";
			}
		else{
			if($Keys & mUPDATE){
				$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$Id' disabled>";
				}
			else{
				$Choose="<img src='../images/lock.png' title='没有操作权限!' width='15' height='15'>";
				}
			}

		if($tbDefalut==0 && $midDefault==""){//首行
			//并行列
			echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
			echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";//更新
			$unitWidth=$tableWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PayDate</td>";//结付日期
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Payee</td>";		//凭证
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$Receipt</td>";		//回执
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayRemark</td>";		//结付备注
			$mRow++;
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='right'>$PayAmount</td>";		//结付总额
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$BankName</td>";		//结付银行
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			//并行宽
			//echo"<td width='$unitWidth' class='A0101'>";
			echo"<td width='' class='A0101'>";
			$midDefault=$Mid;
			}
		if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
			$m=15;
			echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
			echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
			$unitFirst=$Field[$m]-1;
			echo"<td class='A0001' width='$unitFirst' height='20' align='center' $tdBGCOLOR>$Choose</td>";//选项
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";				//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]'>$Forshort</td>";						//供应商
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]'>$PurchaseID</td>";					//采购单号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]'>$Remark</td>";							//说明
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Symbol</td>";		//货币
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='right'>$Amount</td>";		//预付金额
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Did</td>";			//抵付状态
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Operator</td>";	//请款人
			$m=$m+2;
			echo"<td width='' align='center'>$Date</td>";						//请款日期
			echo"</tr></table>";
			$i++;
			}
		else{
			//新行开始
			echo"</td></tr></table>";//结束上一个表格
			//并行列
			echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
			echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";//更新
			$unitWidth=$tableWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PayDate</td>";//结付日期
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Payee</td>";		//凭证
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$Receipt</td>";		//回执
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayRemark</td>";		//结付备注
			$mRow++;
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='right'>$PayAmount</td>";		//结付总额
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$BankName</td>";		//结付银行
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			//并行宽
			//echo"<td width='$unitWidth' class='A0101'>";
			echo"<td width='' class='A0101'>";
			$midDefault=$Mid;
			echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
			echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
			$unitFirst=$Field[$m]-1;
			echo"<td class='A0001' width='$unitFirst' height='20' align='center' $tdBGCOLOR>$Choose</td>";//选项
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";				//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]'>$Forshort</td>";						//供应商
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]'>$PurchaseID</td>";					//采购单号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]'>$Remark</td>";							//说明
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Symbol</td>";		//货币
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='right'>$Amount</td>";		//预付金额
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Did</td>";			//抵付状态
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Operator</td>";	//请款人
			$m=$m+2;
			echo"<td width='' align='center'>$Date</td>";						//请款日期
			echo"</tr></table>";
			$i++;
			}
		}while($mainRows = mysql_fetch_array($mainResult));
	echo"</tr></table>";
	}
else{
	noRowInfo($tableWidth);
	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
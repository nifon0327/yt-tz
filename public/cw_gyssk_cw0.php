<?php
//电信-zxq 2012-08-01
/*
MC、DP共享代码
*/
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$mainData="$DataIn.cw2_gysskmain";
$sheetData="$DataIn.cw2_gyssksheet";
//步骤1：
include "../model/subprogram/cw0_model1.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
$mRow=1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
	M.PayDate,M.PayAmount,M.Payee,M.Receipt,M.CheckSheet,M.Remark AS PayRemark,M.Locks AS MLocks,
	S.Id,S.Mid,S.Forshort,S.PayMonth,S.InvoiceNUM,S.InvoiceFile,S.Amount,S.Remark,S.Date,S.Estate,S.Locks,S.Operator,C.Symbol AS Currency,B.Title
 	FROM $mainData M
	LEFT JOIN $sheetData S ON S.Mid=M.Id
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
	LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
	WHERE 1 $SearchRows order by M.Id DESC,S.Date DESC";
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	$ImgDir="download/cwgyssk/";
	do{
		$m=1;
		//结付主表数据
		$Mid=$mainRows["Mid"];
		$PayDate=$mainRows["PayDate"];
		$PayAmount=$mainRows["PayAmount"];
		$Checksheet=$mainRows["Checksheet"];
		$BankName=$mainRows["Title"];
		$Payee=$mainRows["Payee"];
		$Receipt=$mainRows["Receipt"];
		include "../model/subprogram/cw0_imgview.php";
		$PayRemark=$mainRows["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' alt='$mainRows[PayRemark]' width='16' height='16'>";
		$MLocks=$mainRows["MLocks"];
		//结付明细数据
		$Id=$mainRows["Id"];
		$Date=$mainRows["Date"];
		$Operator=$mainRows["Operator"];
		include "../model/subprogram/staffname.php";
 		$Amount=$mainRows["Amount"];
		$Forshort=$mainRows["Forshort"];
		$Currency=$mainRows["Currency"];
		$PayMonth=$mainRows["PayMonth"];
		$Remark=$mainRows["Remark"];
		$InvoiceNUM=$mainRows["InvoiceNUM"];
		$InvoiceFile=$mainRows["InvoiceFile"];
		if($InvoiceFile==1){
			$InvoiceFile="S".$Id;
			$Dir=anmaIn("download/cwgyssk/",$SinkOrder,$motherSTR);
			$InvoiceFile=anmaIn($InvoiceFile,$SinkOrder,$motherSTR);
			//$InvoiceNUM="<span onClick='OpenOrLoad(\"$Dir\",\"$InvoiceFile\",7)' style='CURSOR: pointer;color:#FF6633'>$InvoiceNUM</span>";
		$InvoiceNUM="<a href=\"openorload.php?d=$Dir&f=$InvoiceFile&Type=&Action=7\" target=\"download\">$InvoiceNUM</a>";
			}
		//输出
		if($Keys & mUPDATE || $Keys & mLOCK){
			$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"cw_gyssk_upmain\",$Mid)' src='../images/edit.gif' alt='行号:$mRow 编号:$Mid 更新结付单资料!' width='13' height='13'>";
			}
		else{
			$upMian="&nbsp;";
			}
		if($MLocks==0){
			$Choose="<img src='../images/lock.png' alt='主记录已锁定!' width='15' height='15'>";
			}
		else{
			if($Keys & mUPDATE){
				$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$Id' disabled>";
				}
			else{
				$Choose="<img src='../images/lock.png' alt='没有操作权限!' width='15' height='15'>";
				}
			}

		$URL="nonbom6_relation_ajax.php";
		//$URL="test.php";
        $theParam="Id=$Id";
		$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\",\"public\");' name='showtable$i' src='../images/showtable.gif' 
		alt='显示或隐藏关联非BOM采购单.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='1800' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='1800'>&nbsp;</div><br></td></tr></table>";


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
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayRemark</td>";		//对帐单
			$mRow++;
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$Checksheet</td>";		//结付备注
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayAmount</td>";		//结付总额
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$BankName</td>";		//结付银行
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			//并行宽
			echo"<td width=' ' class='A0101'>";
			$midDefault=$Mid;
			}
		if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
			$m=17;
			echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
			echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
			$unitFirst=$Field[$m]-1;
			echo"<td class='A0001' width='$unitFirst' height='20' align='center' $tdBGCOLOR>$Choose $showPurchaseorder</td>";//选项
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";			//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Date</td>";		//请款日期
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$PayMonth</td>";		//货款月份
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Forshort</td>";	//供应商
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Currency</td>";	//货币
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Amount</td>";		//金额
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]'>$Remark</td>";	//说明
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>&nbsp;$InvoiceNUM</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'><div class='greenB'>√</div></td>";
			$m=$m+2;
			echo"<td  class='A0001' width=' ' align='center'>$Operator</td>";//请款状态
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
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayRemark</td>";		//对帐单
			$mRow++;
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$Checksheet</td>";		//结付备注
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayAmount</td>";		//结付总额
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$BankName</td>";		//结付银行
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			//并行宽
			echo"<td width=' ' class='A0101'>";
			$midDefault=$Mid;
			echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
			echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
			$unitFirst=$Field[$m]-1;
			echo"<td class='A0001' width='$unitFirst' height='20' align='center' $tdBGCOLOR>$Choose $showPurchaseorder</td>";//选项
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";			//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Date</td>";		//请款日期
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$PayMonth</td>";		//货款月份
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Forshort</td>";	//供应商
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Currency</td>";	//货币
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Amount</td>";		//金额
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]'>$Remark</td>";	//说明
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>&nbsp;$InvoiceNUM</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'><div class='greenB'>√</div></td>";
			$m=$m+2;
			echo"<td  class='A0001' width=' ' align='center'>$Operator</td>";//请款状态
			echo"</tr></table>";
			$i++;
			}
		 echo $StuffListTB;
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
<?php
//电信-EWEN
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$mainData="$DataIn.hdjbmain";
$sheetData="$DataIn.hdjbsheet";
//步骤1：
include "../model/subprogram/cw0_model1.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
$mRow=1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
	M.PayDate,M.PayAmount,M.Payee,M.Receipt,M.CheckSheet,M.Remark AS PayRemark,M.Locks AS MLocks,
	S.Id,S.Mid,S.Number,S.Month,S.xHours,S.xWage,S.fHours,S.fWage,S.Amount,S.Date,S.Estate,S.Locks,S.Operator,P.Name,J.Name AS Job,B.Name AS Branch,BK.Title
 	FROM $mainData M
	LEFT JOIN $sheetData S ON S.Mid=M.Id
	LEFT JOIN $DataPublic.staffmain P ON S.Number=P.Number
	LEFT JOIN $DataPublic.jobdata J ON J.Id=S.JobId
	LEFT JOIN $DataPublic.branchdata B ON B.Id=S.BranchId
	LEFT JOIN $DataPublic.my2_bankinfo BK ON BK.Id=M.BankId
	WHERE 1 $SearchRows order by M.Id DESC,S.Month DESC";
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	$ImgDir="download/cwjrjb/";
	do{
		$m=1;
		//结付主表数据
		$Mid=$mainRows["Mid"];
		$PayDate=$mainRows["PayDate"];
		$PayAmount=$mainRows["PayAmount"];
		$BankName=$mainRows["Title"];
		$PayRemark=$mainRows["PayRemark"];
			$Checksheet=$mainRows["Checksheet"];
			$Payee=$mainRows["Payee"];
			$Receipt=$mainRows["Receipt"];
			include "../model/subprogram/cw0_imgview.php";
		$PayRemark=$mainRows["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$mainRows[PayRemark]' width='16' height='16'>";
		$MLocks=$mainRows["MLocks"];
		//结付明细数据
		$Id=$mainRows["Id"];
		$Number=$mainRows["Number"];
		$Name=$mainRows["Name"];
		$Branch=$mainRows["Branch"];
		$Job=$mainRows["Job"];
		$Month=$mainRows["Month"];
		$xHours=$mainRows["xHours"];
		$fHours=$mainRows["fHours"];
		$xWage=$mainRows["xWage"];
		$fWage=$mainRows["fWage"];
		$Amount=sprintf("%.0f",$mainRows["Amount"]);
		$Date=$mainRows["Date"];
		//输出
		if($Keys & mUPDATE || $Keys & mLOCK){
			$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"kq_jrjb_upmain\",$Mid)' src='../images/edit.gif' title='行号:$mRow 编号:$Mid 更新结付单资料!' width='13' height='13'>";
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
			$mRow++;
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayRemark</td>";		//结付备注
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayAmount</td>";		//结付总额
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
			$m=13;
			echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
			echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
			$unitFirst=$Field[$m]-1;
			echo"<td class='A0001' width='$unitFirst' height='20' align='center' $tdBGCOLOR>$Choose</td>";//选项
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Number</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Name</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Branch</td>";//金额
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Job</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Month</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$xWage</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$xHours</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$fWage</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$fHours</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Amount</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='' align='center'><div class='greenB'>√</div></td>";//请款状态
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
			$mRow++;
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayRemark</td>";		//结付备注
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayAmount</td>";		//结付总额
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
			echo"<td class='A0001' width='$Field[$m]'><div align='center'>$i</div></td>";//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Number</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Name</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Branch</td>";//金额
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Job</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Month</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$xWage</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$xHours</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$fWage</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$fHours</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Amount</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='' align='center'><div class='greenB'>√</div></td>";//请款状态
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
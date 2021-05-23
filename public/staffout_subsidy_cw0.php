<?php
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;
$mainData="$DataIn.staff_outsubsidymain";
$sheetData="$DataIn.staff_outsubsidysheet";
include "../model/subprogram/cw0_model1.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
$mRow=1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
	M.PayDate,M.PayAmount,M.Payee,M.Receipt,M.CheckSheet,M.Remark AS PayRemark,M.Locks AS MLocks,S.Id,S.Mid,S.Content,S.Amount,S.Bill,S.Date,S.TotalRate,S.Time,S.TypeId,
   S.Operator,C.Symbol AS Currency,B.Title,S.AveAmount,S.Number ,SM.Name,BD.Name AS Branch,SM.ComeIn,D.outDate,T.Name AS TypeName,S.PaySign,D.Reason AS LeaveReason,M.cSign
 	FROM $mainData M
	LEFT JOIN $sheetData S ON S.Mid=M.Id
    LEFT JOIN $DataPublic.staffmain SM ON SM.Number=S.Number
	LEFT JOIN $DataPublic.branchdata BD ON BD.Id=SM.BranchId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
	LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
    LEFT JOIN $DataPublic.dimissiondata D ON D.Number=SM.Number 
    LEFT JOIN $DataPublic.dimissiontype T ON T.Id =D.LeaveType
   WHERE 1 $SearchRows order by M.Id DESC,S.Date DESC";
//echo $mySql;
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	$ImgDir="download/staff_subsidy/";
	do{
         $m=1;
		//结付主表数据
		$Mid=$mainRows["Mid"];
		$PayDate=$mainRows["PayDate"];
		$PayAmount=$mainRows["PayAmount"];
		$BankName=$mainRows["Title"]==""?"&nbsp;":$mainRows["Title"];
		$Checksheet=$mainRows["Checksheet"];
		$Payee=$mainRows["Payee"];
		$Receipt=$mainRows["Receipt"];
		include "../model/subprogram/cw0_imgview.php";
		$cSignFrom=$mainRows["cSign"];
		include"../model/subselect/cSign.php";
		$PayRemark=$mainRows["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$mainRows[PayRemark]' width='16' height='16'>";
		$MLocks=$mainRows["MLocks"];
		//结付明细数据
		$Id=$mainRows["Id"];
        $Number=$mainRows["Number"];
		$Date=$mainRows["Date"];
        $TypeId=$mainRows["TypeId"]==1?"离职补助":"辞退赔偿金";
        $outDate=$mainRows["outDate"];
        $TypeName=$mainRows["TypeName"];
		$LeaveReason=$mainRows["LeaveReason"]==""?"&nbsp":"<img src='../images/remark.gif' title='$mainRows[LeaveReason]' width='16' height='16'>";
        $Name=$mainRows["Name"];
        $Branch=$mainRows["Branch"];
        $AveAmount=$mainRows["AveAmount"];
        $TotalRate =$mainRows["TotalRate"];
        $Time ="第".$mainRows["Time"]."次";
        $PaySign =$mainRows["PaySign"];
        if($PaySign==1)$Time="<span class='redB'>一次性支付</span>";
        $Rate =$TotalRate."个月";
        $ComeIn=$mainRows["ComeIn"];
		 //工龄计算
		 $ComeInYM=substr($ComeIn,0,7);
		 include "subprogram/staff_model_gl.php";
		$Operator=$mainRows["Operator"];
		include "../model/subprogram/staffname.php";
 		$Amount=$mainRows["Amount"];
		$Currency=$mainRows["Currency"];
		$Content=$mainRows["Content"]==""?"&nbsp":"<img src='../images/remark.gif' title='$mainRows[Content]' width='16' height='16'>";
		$Bill=$mainRows["Bill"];
		if($Bill==1){
			$Bill=$Number.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="-";
			}
		//输出
		if($Keys & mUPDATE || $Keys & mLOCK){
			$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"staffout_subsidy_upmain\",$Mid)' src='../images/edit.gif' title='行号:$mRow 编号:$Mid 更新结付单资料!' width='13' height='13'>";
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
			echo"<td width='$Field[$m]' class='A0101' align='center'>$cSign</td>";
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			//并行宽
			//echo"<td width='$unitWidth' class='A0101'>";
			echo"<td width='' class='A0101'>";
			$midDefault=$Mid;
			}
		if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
			$m=19;
			echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
			echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
			$unitFirst=$Field[$m]-1;
			echo"<td class='A0001' width='$unitFirst' height='20' align='center' $tdBGCOLOR>$Choose</td>";//选项
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$TypeId</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Date</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Name</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Branch</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Gl_STR</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$outDate</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$TypeName</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$LeaveReason</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$AveAmount</td>";//金额
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Rate</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Time</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Amount</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Currency</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Content</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Bill</td>";
			$m=$m+2;
			echo"<td   width='' align='center'><div class='greenB'>√</div></td>";//请款状态
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
			echo"<td width='$Field[$m]' class='A0101' align='center'>$cSign</td>";
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='' class='A0101'>";
			$midDefault=$Mid;
			echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
			echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
			$unitFirst=$Field[$m]-1;
			echo"<td class='A0001' width='$unitFirst' height='20' align='center' $tdBGCOLOR>$Choose</td>";//选项
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$TypeId</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Date</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Name</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Branch</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Gl_STR</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$outDate</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$TypeName</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$LeaveReason</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$AveAmount</td>";//金额
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Rate</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Time</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Amount</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Currency</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Content</td>";
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Bill</td>";
			$m=$m+2;
			echo"<td   width='' align='center'><div class='greenB'>√</div></td>";//请款状态
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
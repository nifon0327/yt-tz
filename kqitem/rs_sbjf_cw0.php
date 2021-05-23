<?php
//电信-ZX  2012-08-01
/*
$DataIn.sbpaysheet
$DataPublic.staffmain
$DataPublic.jobdata
$DataPublic.branchdata
二合一已更新
*/
$mainData="$DataIn.sbpaymain";
$sheetData="$DataIn.sbpaysheet";
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
//步骤1：
include "../model/subprogram/cw0_model1.php";
$mySql="SELECT 
	M.PayDate,M.PayAmount,M.Payee,M.Receipt,M.Checksheet,M.Remark AS PayRemark,M.Locks AS MLocks,	
	S.Id,S.Mid,S.BranchId,S.JobId,S.Number,S.Month,S.mAmount,S.cAmount,S.Date,S.Estate,S.Locks,
	P.Name,B.Title,S.TypeId
	FROM $mainData M
	LEFT JOIN $sheetData S ON S.Mid=M.Id
	LEFT JOIN $DataPublic.staffmain P ON P.Number=S.Number
	LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
	WHERE 1 $SearchRows
	ORDER BY S.Month DESC,S.BranchId,S.JobId,P.Number";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$SUMA0=0;//总金额$PEADate
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	do{
		$m=1;
		//主结付单资料
		$Id=$mainRows["Id"];
		$Mid=$mainRows["Mid"];
		$PayDate=$mainRows["PayDate"];
		$PayAmount=$mainRows["PayAmount"];
		$BankName=$mainRows["Title"];
			$ImgDir="download/sbjf/";
			$Checksheet=$mainRows["Checksheet"];
			$Payee=$mainRows["Payee"];
			$Receipt=$mainRows["Receipt"];
			include "../model/subprogram/cw0_imgview.php";
		$PayRemark=$mainRows["PayRemark"]==""?"-":"<img src='../images/remark.gif' title='$mainRows[PayRemark]' width='16' height='16'>";
		$MLocks=$mainRows["MLocks"];

		if($Keys & mUPDATE || $Keys & mLOCK){
			$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"rs_sbjf_upmain\",$Mid)' src='../images/edit.gif' title='更新结付单资料!' width='13' height='13'>";
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
			echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
			echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";//更新
			$unitWidth=$tableWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PayDate</td>";	//结付日期
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Payee</td>";		//结付凭证
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayRemark</td>";	//结付备注
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
		//明细记录
		$Name=$mainRows["Name"];
		$BranchId=$mainRows["BranchId"];
		$JobId=$mainRows["JobId"];
		$Month=$mainRows["Month"];
		$mAmount=$mainRows["mAmount"];
		$cAmount=$mainRows["cAmount"];
		$Amount=sprintf("%.2f",$mAmount+$cAmount);
		$EstateSTR=$Estate==0?"<div align='center' class='greenB'>已付</div>":"<div align='center' class='redB'>状态错</div>";
		$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata WHERE 1 AND Id=$BranchId LIMIT 1",$link_id));
		$Branch=$B_Result["Name"];
		$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata WHERE 1 AND Id=$JobId LIMIT 1",$link_id));
		$Job=$J_Result["Name"];
       switch($mainRows["TypeId"]){
                    case 1: $TypeName="社保";break;
                    case 2: $TypeName="公积金";break;
                    case 3: $TypeName="意外险";break;
            }
		if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
			$m=13;
			echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
			echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
			$unitFirst=$Field[$m]-1;
			echo"<td class='A0001' width='$unitFirst' align='center'>$Choose</td>";//选项
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$TypeName</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Name</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Branch</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Job</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Month</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$mAmount</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$cAmount</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Amount</td>";
			$m=$m+2;
			echo"<td class='A0001' width='' align='center'>$EstateSTR</td>";
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
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PayDate</td>";	//结付日期
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Payee</td>";		//结付凭证
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayRemark</td>";	//结付备注
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
			echo"<td class='A0001' width='$unitFirst' align='center'>$Choose</td>";//选项
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$TypeName</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Name</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Branch</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Job</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Month</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$mAmount</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$cAmount</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Amount</td>";
			$m=$m+2;
			echo"<td class='A0001' width='' align='center'>$EstateSTR</td>";
			echo"</tr></table>";
			$i++;
			}
		}while ($mainRows = mysql_fetch_array($mainResult));
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

<?php
$mainData="$DataIn.cw19_studyfeemain";
$sheetData="$DataIn.cw19_studyfeesheet";
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
//步骤1：
include "../model/subprogram/cw0_model1.php";
$mySql="SELECT 
	M.PayDate,M.PayAmount,M.Payee,M.Receipt,M.Checksheet,M.Remark AS PayRemark,M.Locks AS MLocks,
	S.Id,S.Mid,S.Month,S.Amount,S.Date,S.Estate,S.Locks,S.Operator,P.Name,B.Title,S.Remark,BD.Name AS BranchName,J.Name AS JobName,S.Attached,A.ChildName,A.Sex,S.cSign  
	FROM $mainData M
	LEFT JOIN $sheetData S ON S.Mid=M.Id
   LEFT JOIN  $DataPublic.childinfo A  ON A.Id=S.cId
	LEFT JOIN $DataPublic.staffmain P ON P.Number=A.Number
	LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
    LEFT JOIN $DataPublic.branchdata BD ON BD.Id=P.BranchId
    LEFT JOIN $DataPublic.jobdata J ON J.Id=P.JobId
	WHERE 1 $SearchRows ORDER BY S.Month DESC,P.Number";
//echo $mySql;
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
		$ImgDir="download/childinfo/";
		$Checksheet=$mainRows["Checksheet"];
		$Payee=$mainRows["Payee"];
		$Receipt=$mainRows["Receipt"];
		include "../model/subprogram/cw0_imgview.php";
		$cSignFrom=$mainRows["cSign"];
		include"../model/subselect/cSign.php";
        $BranchName=$mainRows["BranchName"];
        $JobName=$mainRows["JobName"];
		$PayRemark=$mainRows["PayRemark"]==""?"-":"<img src='../images/remark.gif' title='$mainRows[PayRemark]' width='16' height='16'>";
        $Remark=$mainRows["Remark"]==""?"&nbsp;":$mainRows["Remark"];
		$MLocks=$mainRows["MLocks"];
        $ChildName=$mainRows["ChildName"];
        $Date=$mainRows["Date"];
        $Attached=$mainRows["Attached"];
			if($Attached!="" ){
				 $f1=anmaIn($Attached,$SinkOrder,$motherSTR);
				 $d1=anmaIn("download/childinfo/",$SinkOrder,$motherSTR);
				 $Attached="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";

			  }
			else $Attached="&nbsp;";
      $Operator=$mainRows["Operator"];
		include "../model/subprogram/staffname.php";

		if($Keys & mUPDATE || $Keys & mLOCK){
			$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"childstudyfee_upmain\",$Mid)' src='../images/edit.gif' title='更新结付单资料!' width='13' height='13'>";
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
		$Month=$mainRows["Month"];
		$Amount=$mainRows["Amount"];
		$EstateSTR=$Estate==0?"<div align='center' class='greenB'>已付</div>":"<div align='center' class='redB'>状态错</div>";

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
			echo"<td class='A0001' width='$Field[$m]' align='center'>$cSign</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Name</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$ChildName</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Amount</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Attached</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Remark</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$EstateSTR</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Operator</td>";
			$m=$m+2;
			echo"<td class='A0001' width='' align='center'>$Date</td>";
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
			echo"<td class='A0001' width='$Field[$m]' align='center'>$cSign</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Name</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$ChildName</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Amount</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Attached</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Remark</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$EstateSTR</td>";
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Operator</td>";
			$m=$m+2;
			echo"<td class='A0001' width='' align='center'>$Date</td>";
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

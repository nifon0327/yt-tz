<?php
/*电信---yang 20120801
$DataIn.cwdyfmain
$DataIn.cwdyfsheet
*/
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$mainData="$DataIn.cwdyfmain";
$sheetData="$DataIn.cwdyfsheet";
//步骤1：
include "../model/subprogram/cw0_model1.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
//	S.Id,S.Mid,S.ItemId,S.Description,S.Amount,S.Remark,S.Bill,S.Operator,S.Provider,S.Date,S.Locks
$mySql="SELECT 
	M.PayDate,M.PayAmount,M.Payee,M.Receipt,M.Checksheet,M.Remark AS PayRemark,M.Locks AS MLocks,
	S.Id,S.Mid,S.ItemId,K.Name as KName,S.Date,S.Amount,C.Name as CName,S.ModelDetail,S.Description,S.Remark,S.Provider,S.Bill,S.Estate,S.Locks,S.Operator,B.Title,M.cSign
 	FROM $mainData M
	LEFT JOIN $sheetData S ON S.Mid=M.Id
	LEFT JOIN $DataPublic.kftypedata K ON K.ID=S.TypeID
	LEFT JOIN $DataPublic.currencydata C ON C.ID=S.Currency
	LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
	WHERE 1 $SearchRows order by M.Id DESC,S.Date DESC";
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	do{
		$m=1;
		$Mid=$mainRows["Mid"];
		$PayDate=$mainRows["PayDate"];
		$PayAmount=$mainRows["PayAmount"];
		$BankName=$mainRows["Title"];
		$ImgDir="download/cwdyf/";
		$Checksheet=$mainRows["Checksheet"];
		$Payee=$mainRows["Payee"];
		$Receipt=$mainRows["Receipt"];
		include "../model/subprogram/cw0_imgview.php";
		$PayRemark=$mainRows["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$mainRows[PayRemark]' width='16' height='16'>";
		$MLocks=$mainRows["MLocks"];
		$cSignFrom=$mainRows["cSign"];
		include"../model/subselect/cSign.php";

		$Id=$mainRows["Id"];
		$ItemId=$mainRows["ItemId"];
		$KName=$mainRows["KName"];
		$Description=$mainRows["Description"]==""?"&nbsp":$mainRows["Description"];
		$Amount=$mainRows["Amount"];

		$CName=$mainRows["CName"];
		$ModelDetail=$mainRows["ModelDetail"]==""?"&nbsp":$mainRows["ModelDetail"];

		$Remark=$mainRows["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$mainRows[Remark]' width='16' height='16'>";
		$Provider=$mainRows["Provider"];
		$Date=$mainRows["Date"];
		$Locks=$mainRows["Locks"];
		$Operator=$mainRows["Operator"];
		$Bill=$mainRows["Bill"];
		$Dir=anmaIn("download/dyf/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="DYF".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="&nbsp;";
			}

		include "../model/subprogram/staffname.php";
		$Amount=sprintf("%.2f",$Amount);
		if($Keys & mUPDATE || $Keys & mLOCK){
			$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"prayforcost_upmain\",$Mid)' src='../images/edit.gif' title='更新结付单资料!' width='13' height='13'>";
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
			echo"<td class='A0001' width='$Field[$m]'><div align='center'>$i</div></td>";//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$ItemId</td>";//项目ID
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]'><DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Description'><NOBR>$KName</NOBR></DIV></td>";//费用分类
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Date</td>";//请款日期
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Amount</td>";//金额
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$CName</td>";//货币类型
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]'><DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Description'><NOBR>$Description</NOBR></DIV></td>";//说明
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Bill</td>";//单据
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Operator</td>";//请款人
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'><div class='greenB'>√</div></td>";//请款状态
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]'><DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Provider'><NOBR>$Provider</NOBR></DIV></td>";//供应商
			$m=$m+2;
			echo"<td class='A0001' width='' align='center'>$Remark</td>";//备注

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
			echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
			echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
			$unitFirst=$Field[$m]-1;
			echo"<td class='A0001' width='$unitFirst' height='20' align='center' $tdBGCOLOR>$Choose</td>";//选项
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]'><div align='center'>$i</div></td>";//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$ItemId</td>";//项目ID
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]'><DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Description'><NOBR>$KName</NOBR></DIV></td>";//费用分类
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Date</td>";//请款日期
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Amount</td>";//金额
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$CName</td>";//货币类型
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]'><DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Description'><NOBR>$Description</NOBR></DIV></td>";//说明
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Bill</td>";//单据
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Operator</td>";//请款人
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'><div class='greenB'>√</div></td>";//请款状态
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]'><DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Provider'><NOBR>$Provider</NOBR></DIV></td>";//供应商
			$m=$m+2;
			echo"<td class='A0001' width='' align='center'>$Remark</td>";//备注

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
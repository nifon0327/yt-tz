<?php
//电信-zxq 2012-08-01
/*
$DataIn.cw2_fkdjmain
$DataIn.cw2_fkdjsheet
$DataIn.trade_object
二合一已更新
*/
include "../model/modelhead.php";
$ColsNumber=10;					//必选参数,需处理
$tableMenuS=600;				//必选参数,功能项列出的起始位置
$From=$From==""?"nodefray":$From;		//必选参数：是否来自查询结果浏览
$funFrom="cw_fkdj";			//必选参数：功能模块
$nowWebPage=$funFrom."_nodefray";		//必选参数：功能页面
$Log_Item="预付订金";

//必选，分页默认值
ChangeWtitle($SubCompany.$Log_Item."抵付记录");
$MergeRows=7;
 $sumCols="4";
$Th_Col="更新|35|结付日期|70|结付凭证|35|结付回执|35|结付备注|35|结付总额|60|结付银行|100|选项|40|序号|35|供应商|120|预付说明|300|预付金额|60|分类|80|抵付状态|60|抵付日期|80|请款人|50|请款日期|70";

$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$mainData="$DataIn.cw2_fkdjmain";
$sheetData="$DataIn.cw2_fkdjsheet";
//步骤1：

include "../model/subprogram/read_model_3.php";
if($From!="slist"){
		$Estate=strlen($Estate)<=0?1:$Estate;
		$SelectedSTR="Selected" .$Estate ;
		$$SelectedSTR="selected";
		echo"<select name='Estate' id='Estate' onchange='document.form1.submit()'>
			<option value='1' $Selected1>未抵付订金</option><option value='0'  $Selected0>已抵付订金</option></select>&nbsp;";
			 $SearchRows=$Estate==1?" AND S.Did=0 ":"AND S.Did>0";

	  if ($Estate==0){
	    $monthResult = mysql_query("SELECT PayDate FROM $mainData WHERE 1  group by DATE_FORMAT(PayDate,'%Y-%m') order by PayDate DESC",$link_id);
		if ($monthRow = mysql_fetch_array($monthResult)) {
			$MonthSelect.="<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
			do{
				$dateValue=date("Y-m",strtotime($monthRow["PayDate"]));
				$FirstValue=$FirstValue==""?$dateValue:$FirstValue;
				$dateText=date("Y年m月",strtotime($monthRow["PayDate"]));
				if($chooseMonth==$dateValue){
					$MonthSelect.="<option value='$dateValue' selected>$dateText</option>";
					$SearchRows1.=" and  DATE_FORMAT(M.PayDate,'%Y-%m')='$dateValue' ";
					}
				else{
					$MonthSelect.="<option value='$dateValue'>$dateText</option>";
					}
				}while($monthRow = mysql_fetch_array($monthResult));
			$SearchRows.=$SearchRows1==""?" and  DATE_FORMAT(M.PayDate,'%Y-%m')='$FirstValue' ":$SearchRows1;
			$MonthSelect.="</select>&nbsp;";
			echo $MonthSelect;
			}
	  }
}
//步骤4：
include "../model/subprogram/read_model_5.php";

$i=1;
$j=($Page-1)*$Page_Size+1;
$mRow=1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
	M.PayDate,M.PayAmount,M.Payee,M.Receipt,M.CheckSheet,M.Remark AS PayRemark,M.Locks AS MLocks,
	S.Id,S.Mid,S.Did,S.TypeId,S.CompanyId,S.Amount,S.Remark,S.Date,S.Estate,S.Locks,S.Operator,P.Forshort,P.Currency,K.PayDate AS dfPayDate,B.Title,C.Symbol
 	FROM $mainData M
	LEFT JOIN $sheetData S ON S.Mid=M.Id
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
	LEFT JOIN $DataIn.cw1_fkoutmain K ON K.Id=S.Did 
	LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
	WHERE 1 $SearchRows order by S.Date DESC";
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
		$ImgDir="download/cwfkdj/";
		$Checksheet=$mainRows["Checksheet"];
		$Payee=$mainRows["Payee"];
		$Receipt=$mainRows["Receipt"];
		include "../model/subprogram/cw0_imgview.php";
		$PayRemark=$mainRows["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$mainRows[PayRemark]' width='16' height='16'>";
		$MLocks=$mainRows["MLocks"];
		//查货款结付日期
		$Did=$mainRows["Did"];
		$dfPayDate=$mainRows["dfPayDate"]==""?"&nbsp;":$mainRows["dfPayDate"];
		/*
		$dfPayDate=;
		if($Did>0){
			$CheckPayDate=mysql_fetch_array(mysql_query("SELECT PayDate FROM $DataIn.cw1_fkoutmain WHERE Id='$Did' LIMIT 1",$link_id));

		 }*/
		$Did=$Did>0?"<div class='greenB'>已抵货款</div>":"<div class='redB'>未抵付</div>";

		//结付明细数据
		$Id=$mainRows["Id"];
		$Forshort=$mainRows["Forshort"]."-".$mainRows["Symbol"];
		$Remark=$mainRows["Remark"]==""?"&nbsp;":$mainRows["Remark"];
		$Date=$mainRows["Date"];
		$Operator=$mainRows["Operator"];
		include "../model/subprogram/staffname.php";
		$Amount=$mainRows["Amount"];
		$TypeId=$mainRows["TypeId"];
		$Type=$TypeId==1?"订金":($TypeId==2?"多付平衡帐":"少付平衡帐");
		//输出
		if($Keys & mUPDATE || $Keys & mLOCK){
			$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"cw_fkdj_upmain\",$Mid)' src='../images/edit.gif' title='行号:$mRow 编号:$Mid 更新结付单资料!' width='13' height='13'>";
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
			echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]'>$Forshort</td>";	//供应商
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]'>$Remark</td>";		//说明
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='right'>$Amount</td>";		//预付金额
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]'>$Type</td>";		//分类
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Did</td>";	//状态
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$dfPayDate</td>";				//请款人
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Operator</td>";				//请款人
			$m=$m+2;
			echo"<td  class='A0001' width='' align='center'>$Date</td>";		//请款日期
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
			echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]'>$Forshort</td>";	//供应商
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]'>$Remark</td>";		//说明
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='right'>$Amount</td>";		//预付金额
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]'>$Type</td>";		//分类
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Did</td>";	//状态
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$dfPayDate</td>";				//请款人
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Operator</td>";				//请款人
			$m=$m+2;
			echo"<td  class='A0001' width='' align='center'>$Date</td>";		//请款日期
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
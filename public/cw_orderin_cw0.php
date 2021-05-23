<?php
//电信-zxq 2012-08-01
/*
$DataIn.cw6_orderinmain
$DataIn.cw6_orderinsheet
$DataIn.trade_object
$DataIn.ch1_shipmain
$DataIn.ch1_shipsheet
二合一已更新
*/
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$mainData="$DataIn.cw6_orderinmain";
$sheetData="$DataIn.cw6_orderinsheet";
//步骤1：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
	echo"<select name='cwSign' id='cwSign' onchange='document.form1.submit()'>";
	echo"<option value='1'>未收货款</option>";
	echo"<option value='0' selected>已收货款</option>";
	echo"</select>";

	$monthResult = mysql_query("SELECT PayDate FROM $mainData WHERE 1 GROUP BY DATE_FORMAT(PayDate,'%Y-%m') order by PayDate DESC",$link_id);
		if ($monthRow = mysql_fetch_array($monthResult)) {
			$MonthSelect.="<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
			do{
				$dateValue=date("Y-m",strtotime($monthRow["PayDate"]));
				$FirstValue=$FirstValue==""?$dateValue:$FirstValue;
				$dateText=date("Y年m月",strtotime($monthRow["PayDate"]));
				if($chooseMonth==$dateValue){
					$MonthSelect.="<option value='$dateValue' selected>$dateText</option>";
					$SearchRows="and  DATE_FORMAT(M.PayDate,'%Y-%m')='$dateValue'";
					}
				else{
					$MonthSelect.="<option value='$dateValue'>$dateText</option>";
					}
				}while($monthRow = mysql_fetch_array($monthResult));
			$SearchRows=$SearchRows==""?"and  DATE_FORMAT(M.PayDate,'%Y-%m')='$FirstValue'":$SearchRows;
			$MonthSelect.="</select>&nbsp;";
			}
          echo $MonthSelect;
		$pResult = mysql_query("SELECT M.CompanyId,C.Forshort FROM $mainData M LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId WHERE 1 $SearchRows GROUP BY M.CompanyId ORDER BY C.OrderBy DESC,C.CompanyId",$link_id);
		if($pRow = mysql_fetch_array($pResult)){
			echo"<select name='CompanyId' id='CompanyId' onchange='document.form1.submit()'>";
			echo"<option value='' selected>全部客户</option>";
			do{
				$Forshort=$pRow["Forshort"];
				$thisCompanyId=$pRow["CompanyId"];
				if($CompanyId==$thisCompanyId){
					echo"<option value='$thisCompanyId' selected>$Forshort </option>";
					$SearchRows.=" and M.CompanyId='$thisCompanyId'";
					}
				else{
					echo"<option value='$thisCompanyId'>$Forshort</option>";
					}
				}while($pRow = mysql_fetch_array($pResult));
			echo"</select>&nbsp;";
			}
		}
else{
	echo "<input name='cwSign' type='hidden' id='cwSign' value='$cwSign'>";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：可用功能输出
include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
$mRow=1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
	M.PayDate,M.PreAmount,M.PayAmount,M.Remark AS PayRemark,M.Handingfee,M.Locks AS MLocks,
	S.Id,S.Mid,S.Amount,S.chId,C.InvoiceNO,C.InvoiceFile,C.Date,D.Forshort,B.Title
 	FROM $sheetData S 
	LEFT JOIN $mainData M ON S.Mid=M.Id
	LEFT JOIN $DataIn.ch1_shipmain C ON C.Id=S.chId
	LEFT JOIN $DataIn.trade_object D ON D.CompanyId=M.CompanyId
	LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
	WHERE 1 $SearchRows order by M.Id DESC,M.PayDate DESC";
//echo $mySql;
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
        $d2=anmaIn("download/cwjzpz/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		//结付主表数据
		$Mid=$mainRows["Mid"];
	 	$Handingfee=$mainRows["Handingfee"];
		$Forshort=$mainRows["Forshort"];
		$PayDate=$mainRows["PayDate"];
		$BankName=$mainRows["Title"];
		$PreAmount=$mainRows["PreAmount"];
		$PayAmount=$mainRows["PayAmount"];
		$SumAmount=$PreAmount+$PayAmount;

		$PayRemark=$mainRows["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$mainRows[PayRemark]' width='16' height='16'>";
		$MLocks=$mainRows["MLocks"];
		//出货凭证
         $jzpzFile="../download/cwjzpz/Z" . $Mid.".pdf";
         if(file_exists($jzpzFile)){
                   $f2=anmaIn("Z" . $Mid.".pdf",$SinkOrder,$motherSTR);
		          $jzpzFile="<a href=\"openorload.php?d=$d2&f=$f2&Type=&Action=6\" target=\"download\">查 看</a>";
           }else{
                   $jzpzFile="&nbsp;";
           }
		//结付明细数据
		$Id=$mainRows["Id"];
		$Date=$mainRows["Date"];
		$Amount=$mainRows["Amount"];
		$InvoiceNO=$mainRows["InvoiceNO"];
		$InvoiceFile=$mainRows["InvoiceFile"];

		//出货金额计算
		$chId=$mainRows["chId"];
		$checkShipAmount=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty*S.Price*M.Sign) AS ShipAmount FROM $DataIn.ch1_shipsheet S,$DataIn.ch1_shipmain M WHERE S.YandN=1 AND M.Id='$chId' AND S.Mid=M.Id",$link_id));
		//$checkShipAmount=mysql_fetch_array(mysql_query("SELECT SUM(substr(S.Qty*S.Price*M.Sign,1,position('.' in Qty*Price)+2)) AS ShipAmount FROM $DataIn.ch1_shipsheet S,$DataIn.ch1_shipmain M WHERE S.YandN=1 AND M.Id='$chId' AND S.Mid=M.Id",$link_id));
		//echo $chId;
		$ShipAmount=$checkShipAmount["ShipAmount"];
		$ShipAmount=$ShipAmount==""?0:round($ShipAmount,2);

		if($funFrom == "cw_orderin")
		{
			$totleInSql = mysql_query("Select Sum(Amount) as totleAmount From $sheetData Where chId = '$chId'");
			//echo "Select Sum(Amount) as totleAmount From $sheetData Where chId = '$chId'";
			$totleInResult = mysql_fetch_assoc($totleInSql);
			$totleInAmount = $totleInResult["totleAmount"];
			$totleInAmount = $totleInAmount == ""?0:round($totleInAmount,2);
		}


		if($Amount==$ShipAmount || $totleInAmount== $ShipAmount){
			$Amount="<span class='greenB'>$Amount</span>";
			}
		else{
			$Amount="<span class='redB' title='$chId'>$Amount</span>";
			}

		//加密参数
		$f1=anmaIn($InvoiceNO.".pdf",$SinkOrder,$motherSTR);
		 if ($InvoiceFile==1){
             $dfname=urldecode($InvoiceNO);
	        $InvoiceFile=strlen($InvoiceNO)>20?"<a href=\"../admin/openorload.php?dfname=$dfname&Type=invoice\" target=\"download\">$InvoiceNO</a>":"<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\" >$InvoiceNO</a>";
        }
        else{
	        $InvoiceFile="&nbsp;";
        }
		//$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\">$InvoiceNO</a>";
		//输出
		if($Keys & mUPDATE || $Keys & mLOCK){
			$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"cw_orderin_upmain\",$Mid)' src='../images/edit.gif' title='行号:$mRow 编号:$Mid 更新结付单资料!' width='13' height='13'>";
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
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PayDate</td>";//收款日期
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";//客户
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$Handingfee</td>";		//手续费
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$SumAmount</td>";		//收款总额
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PreAmount</td>";		//预收金额
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayAmount</td>";		//实收金额
			$mRow++;
			$unitWidth=$unitWidth-$Field[$m];
            $m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$BankName</td>";		//结付银行
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$jzpzFile</td>";		//进帐凭证
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayRemark</td>";		//结付备注
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			//并行宽
			//echo"<td width='$unitWidth' class='A0101'>";
			echo"<td width='' class='A0101'>";
			$midDefault=$Mid;
			}
		if($midDefault!="" && $midDefault==$Mid){//同属于一个主ID，则依然输出明细表格
			$m=21;
			echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
			echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
			$unitFirst=$Field[$m]-1;
			echo"<td class='A0001' width='$unitFirst' height='20' align='center' $tdBGCOLOR>$Choose</td>";//选项
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Date</td>";//出货日期
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$InvoiceFile</td>";//
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$ShipAmount</td>";//出货金额
			$m=$m+2;
			echo"<td class='A0001' width='' align='center'>$Amount</td>";//本次收款金额
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
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PayDate</td>";//收款日期
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";//客户
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$Handingfee</td>";		//手续费
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$SumAmount</td>";		//收款总额
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PreAmount</td>";		//预收金额
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayAmount</td>";		//实收总额
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$BankName</td>";		//结付银行
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$jzpzFile</td>";		//进帐凭证
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$PayRemark</td>";		//结付备注
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;$mRow++;
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
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Date</td>";//出货日期
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$InvoiceFile</td>";//
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$ShipAmount</td>";//出货金额
			$m=$m+2;
			echo"<td class='A0001' width='' align='center'>$Amount</td>";//本次收款金额
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
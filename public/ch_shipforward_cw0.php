<?php
//电信-zxq 2012-08-01
/*
$DataIn.cw3_forward
$DataIn.ch3_forward
$DataIn.ch1_shipmain
$DataPublic.freightdata
二合一已更新
*/
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$mainData="$DataIn.cw3_forward";
$sheetData="$DataIn.ch3_forward";
//步骤1：
include "../model/subprogram/cw0_model1.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
$mRow=1;
//$SearchRows="and S.Estate='$Estate'";
List_Title($Th_Col,"1",1);
if($TypeId==1){
	$TempTable="ch1_shipmain";
	$SearchRows.=" AND S.TypeId='$TypeId'";
$mySql="SELECT 
	M.PayDate,M.PayAmount,M.Payee,M.Receipt,M.CheckSheet,M.Remark AS PayRemark,M.Locks AS MLocks,S.PayType,
	S.Id,S.Mid,S.chId,S.HoldNO,S.ForwardNO,S.BoxQty,S.mcWG,S.forwardWG,S.Volume,S.Amount,S.InvoiceDate,S.ETD,S.Remark,S.Estate,S.Locks,S.Date,
	C.Date AS ShipDate,C.InvoiceNO,C.InvoiceFile,D.Forshort ,B.Title
 	FROM $mainData M
	LEFT JOIN $sheetData S ON S.Mid=M.Id
	LEFT JOIN $DataIn.$TempTable C ON S.chId=C.Id
	LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=S.CompanyId 
	LEFT JOIN $DataIn.ch3_forward F ON F.CompanyId=S.CompanyId 
	LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
	WHERE 1 $SearchRows GROUP BY S.Id order by M.Id DESC,S.Date DESC";
	}
else {
     $TempTable="ch1_deliverymain";
	 $SearchRows.=" AND S.TypeId='$TypeId'";
     $mySql="SELECT 
	M.PayDate,M.PayAmount,M.Payee,M.Receipt,M.CheckSheet,M.Remark AS PayRemark,M.Locks AS MLocks,S.PayType,
	S.Id,S.Mid,S.chId,S.HoldNO,S.ForwardNO,S.BoxQty,S.mcWG,S.forwardWG,S.Volume,S.Amount,S.InvoiceDate,S.ETD,S.Remark,S.Estate,S.Locks,S.Date,
	C.DeliveryDate AS Date,C.DeliveryNumber AS InvoiceNO,D.Forshort ,B.Title
 	FROM $mainData M
	LEFT JOIN $sheetData S ON S.Mid=M.Id
	LEFT JOIN $DataIn.$TempTable C ON S.chId=C.Id
	LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=S.CompanyId 
	LEFT JOIN $DataIn.ch3_forward F ON F.CompanyId=S.CompanyId 
	LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
	WHERE 1 $SearchRows GROUP BY S.Id order by M.Id DESC,S.Date DESC";
      }
//if ($Login_P_Number=="10868") echo $mySql;
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	$d=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
	$d1=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
	$d2=anmaIn("download/DeliveryNumber/",$SinkOrder,$motherSTR);//提货单
	do{
		$m=1;
		//结付主表数据
		$Mid=$mainRows["Mid"];
		$PayDate=$mainRows["PayDate"];
		$ShipDate=$mainRows["ShipDate"];
		$PayAmount=$mainRows["PayAmount"];
		$BankName=$mainRows["Title"];
			$ImgDir="download/cwforward/";
			$Checksheet=$mainRows["Checksheet"];
			$Payee=$mainRows["Payee"];
			$Receipt=$mainRows["Receipt"];
			include "../model/subprogram/cw0_imgview.php";
		$PayRemark=$mainRows["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$mainRows[PayRemark]' width='16' height='16'>";
		$MLocks=$mainRows["MLocks"];

		//结付明细数据
		$Id=$mainRows["Id"];
		$Date=$mainRows["Date"];
		$InvoiceNO=$mainRows["InvoiceNO"];
		$InvoiceFile=$mainRows["InvoiceFile"];
		//加密参数
		$f=anmaIn($InvoiceNO.".pdf",$SinkOrder,$motherSTR);
		if($InvoiceFile!=""){//invoice
		    $InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\">$InvoiceNO</a>";
			 }
		else {
		    $filename="../download/DeliveryNumber/$InvoiceNO.pdf";
		    if(file_exists($filename)){
	           $InvoiceFile="<a href=\"openorload.php?d=$d2&f=$f&Type=&Action=6\" target=\"download\">$InvoiceNO</a>";}
			 else $InvoiceFile="$InvoiceNO";
			 }
		$Forshort=$mainRows["Forshort"];
		$HoldNO=$mainRows["HoldNO"]==""?"&nbsp;":$mainRows["HoldNO"];

		$ForwardNO=$mainRows["ForwardNO"];
		//提单
		$Lading="../download/expressbill/".$ForwardNO.".jpg";
		if(file_exists($Lading)){
			$f1=anmaIn($ForwardNO.".jpg",$SinkOrder,$motherSTR);
			$ForwardNO="<span onClick='OpenOrLoad(\"$d1\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ForwardNO</span>";
			//$ForwardNO="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=\" target=\"download\">$ForwardNO</a>";
			}

		$BoxQty=$mainRows["BoxQty"];
		$mcWG=$mainRows["mcWG"];
		$forwardWG=$mainRows["forwardWG"];
		$Amount=$mainRows["Amount"];
		$InvoiceDate=$mainRows["InvoiceDate"];
		$ETD=$mainRows["ETD"]==""?"&nbsp;":$mainRows["ETD"];
		$Remark=$mainRows["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$mainRows[Remark]' width='18' height='18'>";
		$Locks=$mainRows["Locks"];
		$Estate=$mainRows["Estate"];
		//输出
		if($Keys & mUPDATE || $Keys & mLOCK){
			$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"ch_shipforward_upmain\",$Mid)' src='../images/edit.gif' title='行号:$mRow 编号:$Mid 更新结付单资料!' width='13' height='13'>";
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
			echo"<td class='A0001' width='$Field[$m]' align='center'>$InvoiceDate</td>";//出货日期
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$InvoiceFile</td>";//研砼Invoice
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Forshort</td>";//Forward公司
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$HoldNO</td>";//入仓号
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$ForwardNO</td>";//Forward Invoice
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]'>&nbsp;$BoxQty</td>";//件数
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$mcWG</td>";//MC称重
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]'>&nbsp;$forwardWG</td>";//HK称重
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Amount</td>";//金额
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$ShipDate</td>";//发票日期
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$ETD</td>";//TT
			$m=$m+2;
			echo"<td  class='A0001' width='' align='center'>$Remark</td>";//备注
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
			echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$InvoiceDate</td>";//出货日期
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$InvoiceFile</td>";//研砼Invoice
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Forshort</td>";//Forward公司
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$HoldNO</td>";//入仓号
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$ForwardNO</td>";//Forward Invoice
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]'>&nbsp;$BoxQty</td>";//件数
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$mcWG</td>";//MC称重
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]'>&nbsp;$forwardWG</td>";//HK称重
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Amount</td>";//金额
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$ShipDate</td>";//发票日期
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$ETD</td>";//TT
			$m=$m+2;
			echo"<td  class='A0001' width='' align='center'>$Remark</td>";//备注
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
<?php
//电信-zxq 2012-08-01
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$mainData="$DataIn.cw1_tkoutmain";
$sheetData="$DataIn.cw1_tkoutsheet";
//步骤1：
include "../model/subprogram/cw0_model1.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
$mRow=1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
	M.PayDate,M.PayAmount,M.djAmount,M.Payee,M.Receipt,M.Checksheet,M.Remark AS PayRemark,M.Locks AS MLocks,
	S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Qty,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,
	S.CompanyId,S.BuyerId,S.Amount,S.Month,D.StuffCname,U.Name AS UnitName,H.Date as OutDate,B.Title,H.InvoiceNO,H.InvoiceFile,Count(*) AS ShipCount 
 	FROM $mainData M
	LEFT JOIN $sheetData S ON S.Mid=M.Id
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
    Left Join $DataIn.ch1_shipsheet C ON C.PorderId=S.PorderId
    Left Join $DataIn.ch1_shipmain H ON H.Id=C.Mid	
	LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
	WHERE 1 $SearchRows GROUP BY S.StockId order by M.Id DESC";
//echo "$mySql";
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	do{
		$m=1;
		//结付主表数据
		$Mid=$mainRows["Mid"];
		$PayDate=$mainRows["PayDate"];
		$djAmount=$mainRows["djAmount"];
		$PayAmount=$mainRows["PayAmount"];
		$BankName=$mainRows["Title"];
		$ImgDir="download/cwfk/";
		$Checksheet=$mainRows["Checksheet"];
		$Payee=$mainRows["Payee"];
		$Receipt=$mainRows["Receipt"];
		include "../model/subprogram/cw0_imgview.php";
		$PayRemark=$mainRows["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$mainRows[PayRemark]' width='16' height='16'>";
		$MLocks=$mainRows["MLocks"];
		//结付明细数据
		$Id=$mainRows["Id"];
		$StockId=$mainRows["StockId"];
		$POrderId=$mainRows["POrderId"];
		$StuffCname=$mainRows["StuffCname"];
		$Qty=$mainRows["Qty"];
		$Price=$mainRows["Price"];
		$UnitName=$mainRows["UnitName"]==""?"&nbsp;":$mainRows["UnitName"];
		$OrderQty=$mainRows["OrderQty"];
		$StockQty=$mainRows["StockQty"];
		$AddQty=$mainRows["AddQty"];
		$FactualQty=$mainRows["FactualQty"];
		$CompanyId=$mainRows["CompanyId"];
		$BuyerId=$mainRows["BuyerId"];
		$Amount=$mainRows["Amount"];
		$Month=$mainRows["Month"];
		$StuffId=$mainRows["StuffId"];//配件ID
		$Forshort=$mainRows["Forshort"];
		$Remark=$mainRows["Remark"]==""?"&nbsp;":$mainRows["Remark"];
		$Date=$mainRows["Date"];
		$Operator=$mainRows["Operator"];
		include "../model/subprogram/staffname.php";
		$Amount=$mainRows["Amount"];
		$TypeId=$mainRows["TypeId"];
		$Type=$TypeId==1?"订金":($TypeId==2?"多付平衡帐":"少付平衡帐");
		//输出
		if($Keys & mUPDATE || $Keys & mLOCK){
			$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"cw_tkout_upmain\",$Mid)' src='../images/edit.gif' title='行号:$mRow 编号:$Mid 更新结付单资料!' width='13' height='13'>";
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

		$OutDate=$mainRows["OutDate"]==""?"&nbsp":$mainRows["OutDate"];

		$ShipCount=$mainRows["ShipCount"];
		if ($ShipCount>1){
				//分批出货
				$InvoiceNOSTR="";
				$chResult=mysql_query("SELECT H.InvoiceNO,H.InvoiceFile FROM $DataIn.ch1_shipsheet E 
			                               LEFT JOIN $DataIn.ch1_shipmain H ON H.Id=E.Mid  
			                               WHERE E.PorderId='$POrderId' order by H.Date",$link_id);
			  while($chRow = mysql_fetch_array($chResult)){
				   $InvoiceNO=$chRow["InvoiceNO"];
	                $InvoiceFile=$chRow["InvoiceFile"];
			        $f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
			        $InvoiceNOSTR.=$InvoiceFile==0?"":"<div><a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">$InvoiceNO</a></div>";
				}
				$InvoiceNO=$InvoiceNOSTR==""?"&nbsp;":$InvoiceNOSTR;
			}
			else{
	            $InvoiceNO=$mainRows["InvoiceNO"];
	            $InvoiceFile=$mainRows["InvoiceFile"];
			    $f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
			    $InvoiceNO=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">$InvoiceNO</a>";
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
			echo"<td width='$Field[$m]' class='A0101' align='center'>$Checksheet</td>";		//对帐单
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
			$m=17;
			echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
			echo"<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
				onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
				onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
			$unitFirst=$Field[$m]-1;
			echo"<td class='A0001' width='$unitFirst' height='20' align='center' $tdBGCOLOR>$Choose</td>";//选项
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";			//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$StockId</td>";	//流水号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";					//配件名称
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='right'>$OrderQty</td>";	//订单数量
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='right'>$Qty</td>";			//采购数量
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='right'>$Price</td>";		//单价
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";		//单位
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='right'>$Amount</td>";	//金额
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$OutDate</td>";	//出货日期
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$InvoiceNO</td>";	//Invoice
			$m=$m+2;
			echo"<td  class='A0001' width='' align='center'>$Month</td>";		//请款日期
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
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='center'>$Checksheet</td>";		//对帐单
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
			echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";			//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$StockId</td>";	//流水号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";					//配件名称
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='right'>$OrderQty</td>";	//订单数量
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='right'>$Qty</td>";			//采购数量
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='right'>$Price</td>";		//单价
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";		//单位
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='right'>$Amount</td>";	//金额
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$OutDate</td>";	//出货日期
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$InvoiceNO</td>";	//Invoice
			$m=$m+2;
			echo"<td  class='A0001' width='' align='center'>$Month</td>";		//请款日期
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
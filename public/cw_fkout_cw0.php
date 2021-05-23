<?php
//电信-zxq 2012-08-01
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$mainData="$DataIn.cw1_fkoutmain";
$sheetData="$DataIn.cw1_fkoutsheet";
//步骤1：
include "../model/subprogram/cw0_model1.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
$mRow=1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
	M.PayDate,M.PayAmount,M.djAmount,M.Payee,M.Receipt,M.Checksheet,M.Remark AS PayRemark,M.Locks AS MLocks,
	S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Qty,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.InvoiceId,P.Forshort,
	S.CompanyId,S.BuyerId,S.Amount,S.Month,D.StuffCname,U.Name AS UnitName,B.Title,I.InvoiceNo,I.InvoiceFile,I.Remark AS InvoiceRemark,(G.AddQty+G.FactualQty) AS cgQty,G.Price AS cgPrice    
 	FROM $mainData M
	LEFT JOIN $sheetData S ON S.Mid=M.Id
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit 
	LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
	LEFT JOIN $DataIn.cw1_fkoutinvoice I ON I.Id=S.InvoiceId 
	LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
	WHERE 1 $SearchRows order by M.Id DESC";
/*
    Left Join $DataIn.ch1_shipsheet C ON C.PorderId=S.PorderId
    Left Join $DataIn.ch1_shipmain H ON H.Id=C.Mid	,H.Date as OutDate,
*/
//echo $mySql;
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	$InvoiceFileDir=anmaIn("download/fkinvoice/",$SinkOrder,$motherSTR);
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
		$Forshort=$mainRows["Forshort"];
		include "../model/subprogram/cw0_imgview.php";
		$PayRemark=$mainRows["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$mainRows[PayRemark]' width='16' height='16'>";
		$MLocks=$mainRows["MLocks"];
	   //供应商扣款数
	   $KKReuslt=mysql_query("SELECT SUM(Amount) AS KKAmount FROM $DataIn.cw15_gyskksheet WHERE Kid='$Mid'",$link_id);
	   $KKAmount=mysql_result($KKReuslt,0,"KKAmount");
	   $PayAmount=$PayAmount-$KKAmount;
	   $KKAmount=$KKAmount==0?"0.00":"<a href='cw_cgkk_read.php?Kid=$Mid' target='_bank'>$KKAmount</a>";

	   //供应商货款返利
	   $ReturnReuslt=mysql_query("SELECT SUM(Amount) AS ReturnAmount FROM $DataIn.cw2_hksheet WHERE Did='$Mid'",$link_id);
	   $ReturnAmount=mysql_result($ReturnReuslt,0,"ReturnAmount");
	   $PayAmount=$PayAmount-$ReturnAmount;
	   $ReturnAmount=$ReturnAmount==0?"0.00":"<a href='cw_fkhk_read.php?Did=$Mid' target='_bank'>$ReturnAmount</a>";

		//结付明细数据
		$Id=$mainRows["Id"];
		$StockId=$mainRows["StockId"];
		$POrderId=$mainRows["POrderId"];
		$OutDate="";
		$HDateTemp=mysql_query("SELECT IFNULL(H.Date,'') as OutDate From  $DataIn.ch1_shipsheet C 
							 Left Join $DataIn.ch1_shipmain H ON H.Id=C.Mid	
							 Where C.PorderId='$POrderId' order by H.Date desc 
							",$link_id);
		if($HDRows = mysql_fetch_array($HDateTemp)){
			$OutDate=$HDRows["OutDate"];
		}

		$OutDate=$OutDate==""?"&nbsp":$OutDate;

		$StuffCname=$mainRows["StuffCname"];
		$Qty=$mainRows["Qty"];

		$cgQty=$mainRows["cgQty"];
		$cgPrice=$mainRows["cgPrice"];
		$Price=$mainRows["Price"];
		$Amount=$mainRows["Amount"];

		$cgAmount=round($cgQty*$cgPrice,2);

		$qtyStyleStr = $Qty!=$cgQty ? "style='color:#FF0000;' title='请款数量($Qty)与采购数量($cgQty)不一致' ":"";
		$priceStyleStr = $cgPrice!=$Price ? "style='color:#FF0000;' title='请款单价($Price)与采购单价($cgPrice)不一致' ":"";
		$amountStyleStr= $cgAmount!=$Amount ? "style='color:#FF0000;' title='请款金额($Amount)与采购金额($cgAmount)不一致' ":"";

		$UnitName=$mainRows["UnitName"]==""?"&nbsp;":$mainRows["UnitName"];
		$OrderQty=$mainRows["OrderQty"];
		$StockQty=$mainRows["StockQty"];
		$AddQty=$mainRows["AddQty"];
		$FactualQty=$mainRows["FactualQty"];
		$CompanyId=$mainRows["CompanyId"];
		$BuyerId=$mainRows["BuyerId"];

		$Month=$mainRows["Month"];
		//收货情况
		$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$rkQty=$rkQty==""?0:$rkQty;
		$Mantissa=$Qty-$rkQty;
		if($Mantissa<$Qty){//如果尾数《采购数：黄色
			$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);
			$StockId="<a href='ck_rk_list.php?Sid=$Sid' target='_blank' title='点击查看收货记录'>$StockId</a>";
			if($Mantissa==0){//如果尾数=0：绿色
				$Mantissa="&nbsp;";
				}
			else{
				$Mantissa="<div class='yellowB'>$Mantissa</div>";
				}
			}
		else{
			$Mantissa="<div class='redB'>$Mantissa</div>";
			}
		//未补数量计算
		$StuffId=$mainRows["StuffId"];//配件ID
		$sSearch1=" AND S.StuffId='$StuffId'";
		$checkSql=mysql_query("
		SELECT (B.thQty-A.bcQty) AS wbQty
			FROM (
				SELECT IFNULL(SUM(S.Qty),0) AS thQty,'$StuffId' AS StuffId FROM $DataIn.ck2_thsheet S 
				LEFT JOIN $DataIn.ck2_thmain M ON M.Id=S.Mid 
				WHERE 1 $sSearch1
				)B
			LEFT JOIN (
				SELECT IFNULL(SUM(Qty),0) AS bcQty,'$StuffId' AS StuffId FROM $DataIn.ck3_bcsheet  S
				LEFT JOIN $DataIn.ck3_bcmain M ON M.Id=S.Mid 
				WHERE 1 $sSearch1
				) A ON A.StuffId=B.StuffId",$link_id);
		$wbQty=mysql_result($checkSql,0,"wbQty");
		if($wbQty!=0){
			$wbQty="<a href='stuffreport_result.php?Idtemp=$StuffId' target='_blank'>$wbQty</a>";
			}
		else{
			$wbQty="&nbsp;";
			}

		$Forshort=$mainRows["Forshort"];
		$Remark=$mainRows["Remark"]==""?"&nbsp;":$mainRows["Remark"];
		$Date=$mainRows["Date"];
		$Operator=($mainRows["Operator"]=="")?"&nbsp;":$mainRows["Operator"];
		include "../model/subprogram/staffname.php";
		$Amount=$mainRows["Amount"];
		$TypeId=$mainRows["TypeId"];
		$Type=$TypeId==1?"订金":($TypeId==2?"多付平衡帐":"少付平衡帐");
		//输出
		if($Keys & mUPDATE || $Keys & mLOCK){
			$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"cw_fkout_upmain\",$Mid)' src='../images/edit.gif' title='行号:$mRow 编号:$Mid 更新结付单资料!' width='13' height='13'>";
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

		//$OutDate=$mainRows["OutDate"]==""?"&nbsp":$mainRows["OutDate"];

		$InvoiceId     = $mainRows['InvoiceId'];
		$InvoiceFile   = $mainRows['InvoiceFile'];


		if ($InvoiceId>0){//&& $InvoiceFile!=''
		    $InvoiceNo=$mainRows['InvoiceNo'];

		    $InvoiceFile=anmaIn($InvoiceFile,$SinkOrder,$motherSTR);

            $InvoiceFile="<a href=\"../admin/openorload.php?d=$InvoiceFileDir&f=$InvoiceFile&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>$InvoiceNo</a>";

            if ($Old_InvoiceId!=$InvoiceId){
	            $InvoiceFile.="<img location.href=\"#\"' style='CURSOR: pointer;position:absolute;float:right;' onclick='upMainData(\"cw_fkout_upinvoice\",\"$Mid|$InvoiceId\")' src='../images/edit.gif' title='修改发票信息!' width='13' height='13'>";
	            $Old_InvoiceId = $InvoiceId;
            }

		}
		else{
			$InvoiceFile="&nbsp;";
			if($Keys & mUPDATE){
				$InvoiceFile="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"cw_fkout_upinvoice\",$Mid)' src='../images/upFile.gif' title='上传发票信息!' width='13' height='13'>";
			}
		}
		$InvoiceRemark = $mainRows["InvoiceRemark"]==""?"":"&nbsp;<img src='../images/remark.gif' title='$mainRows[InvoiceRemark]' width='16' height='16'>";

		if($tbDefalut==0 && $midDefault==""){//首行
			//并行列
			echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
			echo"<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";//更新
			$unitWidth=$tableWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";//结付日期
			$unitWidth=$unitWidth-$Field[$m];
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
			echo"<td width='$Field[$m]' class='A0101' align='right'>$djAmount</td>";		//订金总额
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='right'>$KKAmount</td>";		//供应商扣款
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='right'>$ReturnAmount</td>";		//供应商货款返利
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='right'>$PayAmount<br>$InvoiceRemark</td>";		//结付总额
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
			$m=25;
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
			echo"<td class='A0001' width='$Field[$m]' align='right' $qtyStyleStr>$Qty</td>";			//采购数量
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='right' $priceStyleStr>$Price</td>";		//单价
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";		//单位
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='right' $amountStyleStr>$Amount</td>";	//金额
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Mantissa</td>";	//未收数量
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$wbQty</td>";	//未补数量
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$OutDate</td>";	//出货日期
			$m=$m+2;
			echo"<td  class='A0001' width='' align='center'>$Month</td>";		//请款日期
			$m=$m+2;
			echo"<td  class='A0001' width='' align='center'>$InvoiceFile</td>";		//发票信息
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
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";//结付日期
			$unitWidth=$unitWidth-$Field[$m];
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
			echo"<td width='$Field[$m]' class='A0101' align='right'>$djAmount</td>";		//订金总额
			$mRow++;
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='right'>$KKAmount</td>";		//供应商扣款
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='right'>$ReturnAmount</td>";		//供应商货款返利
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='right'>$PayAmount<br>$InvoiceRemark</td>";		//结付总额
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
			echo"<td class='A0001' width='$Field[$m]' align='right' $qtyStyleStr>$Qty</td>";			//采购数量
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='right' $priceStyleStr>$Price</td>";		//单价
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";		//单位
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='right' $amountStyleStr>$Amount</td>";	//金额
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Mantissa</td>";	//未收数量
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$wbQty</td>";	//未补数量
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$OutDate</td>";	//出货日期
			$m=$m+2;
			echo"<td  class='A0001' width='' align='center'>$Month</td>";		//请款日期
			$m=$m+2;
			echo"<td  class='A0001' width='' align='center'>$InvoiceFile</td>"; //发票信息
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
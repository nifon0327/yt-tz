<?php
//更新结付方式：改为以主采购单请款，可以分批请款分批结付 ewen 2013-11-19 OK
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
//步骤1：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
	$monthResult = mysql_query("SELECT PayDate FROM $DataIn.nonbom11_qkmain GROUP BY DATE_FORMAT(PayDate,'%Y-%m') ORDER BY PayDate DESC",$link_id);
		if ($monthRow = mysql_fetch_array($monthResult)) {
			echo "<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
			do{
				$dateValue=date("Y-m",strtotime($monthRow["PayDate"]));
				$FirstValue=$FirstValue==""?$dateValue:$FirstValue;
				$dateText=date("Y年m月",strtotime($monthRow["PayDate"]));
				if($chooseMonth==$dateValue){
					echo "<option value='$dateValue' selected>$dateText</option>";
					$SearchRows="AND  DATE_FORMAT(A.PayDate,'%Y-%m')='$dateValue'";
					}
				else{
					echo "<option value='$dateValue'>$dateText</option>";
					}
				}while($monthRow = mysql_fetch_array($monthResult));
			//$SearchRows=$SearchRows==""?"and  DATE_FORMAT(M.PayDate,'%Y-%m')='$FirstValue'":$SearchRows;
			echo "</select>&nbsp;";
			}
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'><option value='3' $EstateSTR3>未结付</option><option value='0' $EstateSTR0>已结付</option></select>&nbsp;";

	$pResult = mysql_query("SELECT A.CompanyId,B.Letter,B.Forshort 
		FROM $DataIn.nonbom11_qkmain A 
		LEFT JOIN $DataPublic.nonbom3_retailermain B ON B.CompanyId=A.CompanyId WHERE 1 $SearchRows GROUP BY A.CompanyId ORDER BY B.Letter",$link_id);
		if($pRow = mysql_fetch_array($pResult)){
			echo"<select name='CompanyId' id='CompanyId' onchange='document.form1.submit()'>";
			echo"<option value='' selected>全部供应商</option>";
			do{
				$Forshort=$pRow["Forshort"];
				$thisCompanyId=$pRow["CompanyId"];
				$thisLetter=$pRow["Letter"];
				if($CompanyId==$thisCompanyId){
					echo"<option value='$thisCompanyId' selected>$thisLetter-$Forshort </option>";
					$SearchRows.=" AND A.CompanyId='$thisCompanyId'";
					}
				else{
					echo"<option value='$thisCompanyId'>$thisLetter-$Forshort</option>";
					}
				}while($pRow = mysql_fetch_array($pResult));
			echo"</select>&nbsp;";
			}
	$SearchRows.=" AND B.Estate=0";
	}
else{
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";
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
	A.PayDate,A.CompanyId,A.hkAmount,A.taxAmount,A.shipAmount,A.PayAmount,A.Payee,A.Receipt,A.Checksheet,A.Remark AS PayRemark,A.Locks AS MLocks,
	B.Id,B.Mid,B.cgMid,B.TypeId,B.hkAmount AS qk_hkAmount,B.taxAmount AS qk_taxAmount,B.shipAmount AS qk_shipAmount,B.Amount AS qk_Amount,B.Month,B.Remark,B.Estate,
	C.Forshort,
	D.Title,
	E.PurchaseID,E.Date AS cgDate,E.taxAmount AS cg_taxAmount,E.shipAmount AS cg_shipAmount,
	F.Symbol,G.Name
 	FROM $DataIn.nonbom11_qkmain A
	LEFT JOIN $DataIn.nonbom11_qksheet B ON B.Mid=A.Id
	LEFT JOIN $DataPublic.nonbom3_retailermain C ON C.CompanyId=B.CompanyId
	LEFT JOIN $DataPublic.my2_bankinfo D ON D.Id=A.BankId
	LEFT JOIN $DataIn.nonbom6_cgmain E ON E.Id=B.cgMid
	LEFT JOIN $DataPublic.currencydata F ON F.Id=C.Currency
	LEFT JOIN $DataPublic.staffmain G ON G.Number=E.BuyerId
	WHERE 1 $SearchRows order by A.Id DESC";//AND A.djAmount>0
//echo $mySql;
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	$nDir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
	$InvoicePath=anmaIn("download/nonbom_cginvoice/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		//结付主表数据
		$Mid=$mainRows["Mid"];
		$PayDate=$mainRows["PayDate"];
		$ImgDir="download/cwnonbom/";
		$Checksheet=$mainRows["Checksheet"];
		$Payee=$mainRows["Payee"];
		$Receipt=$mainRows["Receipt"];
		include "../model/subprogram/cw0_imgview.php";
		$PayRemark=$mainRows["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$mainRows[PayRemark]' width='16' height='16'>";
		$hkAmount=sprintf("%.2f",$mainRows["hkAmount"]);
		$taxAmount=sprintf("%.2f",$mainRows["taxAmount"]);
		$shipAmount=sprintf("%.2f",$mainRows["shipAmount"]);
		$PayAmount=sprintf("%.2f",$mainRows["PayAmount"]);
		$BankName=$mainRows["Title"];
		$MLocks=$mainRows["MLocks"];

		//结付明细数据
		$Id=$mainRows["Id"];
		$cgMid=$mainRows["cgMid"];
		$cgDate=$mainRows["cgDate"];
		$Name=$mainRows["Name"];
		$Forshort=$mainRows["Forshort"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		//采购数据
		$cg_taxAmount=sprintf("%.2f",$mainRows["cg_taxAmount"]);
		$cg_shipAmount=sprintf("%.2f",$mainRows["cg_shipAmount"]);
		$checkHk=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty*Price),0) AS cg_hkAmount FROM $DataIn.nonbom6_cgsheet WHERE Mid='$cgMid' ",$link_id));
		$cg_hkAmount=sprintf("%.2f",$checkHk["cg_hkAmount"]);   //货款
		$cg_allAmount=sprintf("%.2f",$cg_hkAmount+$cg_taxAmount+$cg_shipAmount); //采购总额

		//请款数据
		$qk_hkAmount=sprintf("%.2f",$mainRows["qk_hkAmount"]);
		$qk_taxAmount=sprintf("%.2f",$mainRows["qk_taxAmount"]);
		$qk_shipAmount=sprintf("%.2f",$mainRows["qk_shipAmount"]);
		$qk_allAmount=sprintf("%.2f",$mainRows["qk_Amount"]);

		$Month=$mainRows["Month"];

		$CheckRow = mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.nonbom6_invoice 
		WHERE cgMid ='$cgMid'",$link_id));
		$InvoiceFile = $CheckRow["InvoiceFile"];
		$InvoiceNo= $CheckRow["InvoiceNo"];
		$InvoiceRemark= $CheckRow["Remark"];
		$InvoiceAmount= $CheckRow["InvoiceAmount"];
		$InvoiceDate= $CheckRow["InvoiceDate"];

		$InvoiceRemark=$InvoiceRemark==""?"&nbsp;":"<img src='../images/remark.gif' title='$InvoiceRemark' width='18' height='18'>";
		if($InvoiceFile!=""){
		    $f2=anmaIn($InvoiceFile,$SinkOrder,$motherSTR);
			$InvoiceFile="<a href=\"../admin/openorload.php?d=$InvoicePath&f=$f2&Type=&Action=6\" target=\"download\" style='CURSOR: pointer; color:#FF6633'>$InvoiceNo</a>";
		}else{
			$InvoiceFile ="&nbsp;";
		}


		$PurchaseID=$mainRows["PurchaseID"];
		$cgMidSTR=anmaIn($cgMid,$SinkOrder,$motherSTR);
		$PurchaseID="<a href='nonbom6_view.php?f=$cgMidSTR' target='_blank'  title='$cgMid'>$PurchaseID</a>";
		$CompanyId=$mainRows["CompanyId"];
		//加密
		$CompanyId=anmaIn($CompanyId,$SinkOrder,$motherSTR);
		$Forshort="<a href='nonbom3_view.php?d=$CompanyId' target='_blank'>$Forshort</a>";
		//0值处理
		$hkAmount=zerotospace($hkAmount);
		$taxAmount=zerotospace($taxAmount);
		$shipAmount=zerotospace($taxAmount);
		$PayAmount=zerotospace($PayAmount);

		$cg_hkAmount=zerotospace($cg_hkAmount);
		$cg_taxAmount=zerotospace($cg_taxAmount);
		$cg_shipAmount=zerotospace($cg_shipAmount);
		$cg_allAmount=zerotospace($cg_allAmount);

		$qk_hkAmount=zerotospace($qk_hkAmount);
		$qk_taxAmount=zerotospace($qk_taxAmount);
		$qk_shipAmount=zerotospace($qk_taxAmount);
		$qk_allAmount=zerotospace($qk_allAmount);

		//输出
		if($Keys & mUPDATE || $Keys & mLOCK){
			$upMian="<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"nonbom6_cwupmain\",$Mid)' src='../images/edit.gif' title='行号:$mRow 编号:$Mid 更新结付单资料!' width='13' height='13'>";
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
			echo"<td scope='col' class='A0101' width='$Field[$m]'>$Forshort</td>";//供应商
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
			echo"<td width='$Field[$m]' class='A0101' align='right'>$hkAmount</td>";		//结付货款小计
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='right'>$taxAmount</td>";		//结付税款小计
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='right'>$shipAmount</td>";		//结付运费小计
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='right'>$PayAmount</td>";		//结付总额小计
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
			echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";					//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$cgDate</td>";			//下单日期
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Name</td>";			//采购
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$PurchaseID</td>";		//采购单号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='right'>$cg_hkAmount</td>";	//采购单货款
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='right'>$cg_taxAmount</td>";	//采购单税款
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='right'>$cg_shipAmount</td>";	//采购单运费
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='right'>$cg_allAmount</td>";	//采购总额
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='right'>$qk_hkAmount</td>";		//请款货款
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='right'>$qk_taxAmount</td>";	//请款税款
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='right'>$qk_shipAmount</td>";	//请款运费
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='right'>$qk_allAmount</td>";	//请款总额
			$m=$m+2;

			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Month</td>";			//请款月份
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$InvoiceFile</td>";	//发票
			$m=$m+2;
			echo"<td  width='' align='center'>$Remark</td>";											//请款备注
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
			echo"<td scope='col' class='A0101' width='$Field[$m]'>$Forshort</td>";//供应商
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PayDate</td>";//结付日期
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Payee</td>";	//凭证
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
			echo"<td width='$Field[$m]' class='A0101' align='right'>$hkAmount</td>";		//结付货款小计
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='right'>$taxAmount</td>";		//结付税款小计
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='right'>$shipAmount</td>";		//结付运费小计
			$unitWidth=$unitWidth-$Field[$m];
			$m=$m+2;
			echo"<td width='$Field[$m]' class='A0101' align='right'>$PayAmount</td>";		//结付总额小计
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
			echo"<td class='A0001' width='$Field[$m]' align='center'>$i</td>";					//序号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$cgDate</td>";			//下单日期
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$Name</td>";			//采购
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='center'>$PurchaseID</td>";		//采购单号
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='right'>$cg_hkAmount</td>";	//采购单货款
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='right'>$cg_taxAmount</td>";	//采购单税款
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='right'>$cg_shipAmount</td>";	//采购单运费
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='right'>$cg_allAmount</td>";	//采购总额
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='right'>$qk_hkAmount</td>";		//请款货款
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='right'>$qk_taxAmount</td>";	//请款税款
			$m=$m+2;
			echo"<td class='A0001' width='$Field[$m]' align='right'>$qk_shipAmount</td>";	//请款运费
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='right'>$qk_allAmount</td>";	//请款总额
			$m=$m+2;

			echo"<td  class='A0001' width='$Field[$m]' align='center'>$Month</td>";			//请款月份
			$m=$m+2;
			echo"<td  class='A0001' width='$Field[$m]' align='center'>$InvoiceFile</td>";	//发票
			$m=$m+2;
			echo"<td width='' align='center'>$Remark</td>";						//请款备注
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
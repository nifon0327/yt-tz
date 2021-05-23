<?php 
//更新结付方式：改为以主采购单请款，可以分批请款分批结付 ewen 2013-11-19 OK
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
include "../model/subprogram/read_model_3.php";
//过滤条件

if($From!="slist"){
	$SearchRows=" AND B.Estate='3'";
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' selected>未结付货款</option><option value='0' >已结付货款</option></select>&nbsp;";	
	//供应商
	$checkResult = mysql_query("SELECT B.CompanyId,C.Letter,C.Forshort 
							   FROM $DataIn.nonbom11_qksheet B 
							   LEFT JOIN $DataPublic.nonbom3_retailermain C ON C.CompanyId=B.CompanyId 
							   WHERE 1 $SearchRows GROUP BY B.CompanyId ORDER BY C.Letter,C.Forshort",$link_id);
	if($checkRow = mysql_fetch_array($checkResult)){
		echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
		echo"<option value='' selected>全部供应商</option>";
		do{
			$CompanyIdTemp=$checkRow["CompanyId"];
			$ForshortTemp=$checkRow["Forshort"];
			$Letter=$checkRow["Letter"];
			if($CompanyId==$CompanyIdTemp){
				echo"<option value='$CompanyIdTemp' selected>$Letter-$ForshortTemp</option>";
				$SearchRows.=" AND B.CompanyId='$CompanyIdTemp'";	
				}
			else{
				echo"<option value='$CompanyIdTemp'>$Letter-$ForshortTemp</option>";
				}
			}while ($checkRow = mysql_fetch_array($checkResult));
		echo"</select>&nbsp;";
		}
		$EstateStr = 'EstateStr' . $InvoiceEstate; 
		$$EstateStr	= " selected ";
		
		echo"<select name='InvoiceEstate' id='InvoiceEstate' onchange='document.form1.submit()'>
		         <option value='' $EstateStr>全部</option>
			     <option value='3' $EstateStr3>无发票</option>
			     <option value='1' $EstateStr1>有发票</option>
			     </select>&nbsp;";	
		switch($InvoiceEstate){
			case 1: $SearchRows.=" AND  I.Estate>0  "; break;
			case 3:  $SearchRows.=" AND   I.Estate IS NULL "; break;
		}
	}
else{
	$SearchRows.=" AND B.Estate='3'";
	}
//结付的银行
include "../model/selectbank1.php";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
$TitlePre="<br>&nbsp;&nbsp;退回或结付原因:<input type=\"text\" id=\"ReturnReasons\" name=\"ReturnReasons\" style=\"width:600\"><p>";

echo"$CencalSstr";

//步骤4：
include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
if($From=="slist"){ //来自查询
$mySql="
	SELECT B.Id,B.cgMid,B.Amount,(B.Amount*F.Rate)AS RmbAmount,B.Month,B.Estate,B.Remark,B.Locks,B.Date,B.Operator,
		DATE_FORMAT(E.Date,'%Y-%m-%d')  AS cgDate,E.PurchaseID,E.Remark AS mainRemark,E.BuyerId,
				C.Forshort,C.CompanyId,
				E.Attached,
				G.Name,I.InvoiceNo,I.InvoiceFile
				FROM $DataIn.nonbom11_qksheet B
				LEFT JOIN $DataIn.nonbom6_cgmain E ON E.Id=B.cgMid
				LEFT JOIN $DataIn.nonbom6_cgsheet A  ON A.Mid=E.Id
				LEFT JOIN $DataIn.nonbom3_retailermain C ON C.CompanyId=B.CompanyId
				LEFT JOIN $DataIn.currencydata F ON F.Id=C.Currency
				LEFT JOIN $DataIn.staffmain G ON G.Number=E.BuyerId
				LEFT JOIN $DataIn.nonbom6_invoice  I ON I.cgMid = E.Id 
				WHERE 1 $SearchRows group by B.Id,A.Mid  ORDER BY B.Month,B.Date DESC";				
}
else {  //来自查询
	$mySql="
	SELECT B.Id,B.cgMid,B.Amount,(B.Amount*F.Rate)AS RmbAmount,B.Month,B.Estate,B.Remark,B.Locks,B.Date,B.Operator,
		DATE_FORMAT(E.Date,'%Y-%m-%d')  AS cgDate,E.PurchaseID,E.Remark AS mainRemark,E.BuyerId,
				C.Forshort,C.CompanyId,
				E.Attached,
				G.Name,I.InvoiceNo,I.InvoiceFile
				FROM $DataIn.nonbom11_qksheet B
				LEFT JOIN $DataIn.nonbom6_cgmain E ON E.Id=B.cgMid
				LEFT JOIN $DataIn.nonbom6_cgsheet A  ON A.Mid=E.Id 
				LEFT JOIN $DataIn.nonbom3_retailermain C ON C.CompanyId=B.CompanyId
				LEFT JOIN $DataIn.currencydata F ON F.Id=C.Currency
				LEFT JOIN $DataIn.staffmain G ON G.Number=E.BuyerId
				LEFT JOIN $DataIn.nonbom6_invoice  I ON I.cgMid = E.Id 
				WHERE 1 $SearchRows group by B.Id,A.Mid   ORDER BY B.Month,B.Date DESC";		
}

//echo "$From <br> $mySql"	;	
//if ($Login_P_Number==10868) echo $mySql;
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	$Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
	$cgDir=anmaIn("download/nonbom_contract/",$SinkOrder,$motherSTR);
	$InvoiceDir=anmaIn("download/nonbom_cginvoice/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$LockRemark=$rkBgColor=$wsBgColor="";
		//主单信息						
		$checkidValue=$mainRows["Id"];				//请款记录
		$cgMid=$mainRows["cgMid"];					//采购单ID
		$cgDate=$mainRows["cgDate"];				//下单日期
		$Name=$mainRows["Name"];					//下单采购
		$Forshort=$mainRows["Forshort"];				//供应商
		$PurchaseID=$mainRows["PurchaseID"];	//采购单号
		$MidSTR=anmaIn($cgMid,$SinkOrder,$motherSTR);
		$PurchaseID="<a href='nonbom6_view.php?f=$MidSTR' target='_blank'>$PurchaseID</a>";
		$Attached=$mainRows["Attached"];			//采购凭证
		if($Attached==1){
			$Attached=$cgMid.".pdf";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<span onClick='OpenOrLoad(\"$cgDir\",\"$Attached\",6,\"pdf\")' style='CURSOR: pointer;color:#FF6633'>查看</span>";
			}
		else{
			$Attached="&nbsp;";
			}
		$InvoiceNo=$mainRows["InvoiceNo"];
		$InvoiceFile=$mainRows["InvoiceFile"];
		if($InvoiceFile!=""){
			$InvoiceFile=anmaIn($InvoiceFile,$SinkOrder,$motherSTR);
			$InvoiceNo="<span onClick='OpenOrLoad(\"$InvoiceDir\",\"$InvoiceFile\",6,\"pdf\")' style='CURSOR: pointer;color:#FF6633'>$InvoiceNo</span>";
			}
		else{
			$InvoiceNo="&nbsp;";
			}
		
		$checkHk=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty*Price),0) AS hkAmount FROM $DataIn.nonbom6_cgsheet WHERE Mid='$cgMid' ",$link_id));		
		$hkAmount=sprintf("%.2f",$checkHk["hkAmount"]);   		//采购货款
		$Sum_hkAmount+=$hkAmount;
		$checkHavedHk=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Amount),0) AS qk_SumAmount,IFNULL(SUM(IF(Estate=3,Amount,0)),0) AS qk_passAmount ,IFNULL(SUM(IF(Estate=0,Amount,0)),0) AS qk_jfAmount  FROM $DataIn.nonbom11_qksheet WHERE CgMid='$Mid' ",$link_id));		
		$qk_SumAmount=$checkHavedHk["qk_SumAmount"]; 	//已请款总额
		$qk_passAmount=$checkHavedHk["qk_passAmount"]==0?"&nbsp;":$checkHavedHk["qk_passAmount"];  //请款通过
		$qk_jfAmount=$checkHavedHk["qk_jfAmount"];  		//已结付
		$Month=$mainRows["Month"];									//本次请款月份
		$thisAmount=sprintf("%.2f",$mainRows["Amount"]);		//本次请款金额
		$Sum_thisAmount+=$thisAmount;
		$Locks=1;
		$hkAmount="<div class='yellowB'>$hkAmount</div>";//采购货款
		//表示请款状：部分请款－黄色；全部请款－绿色
		
		if($qk_SumAmount==$hkAmount) {  
			$thisAmount="<a href='nonbom6_qkview.php?Mid=$cgMid' target='_blank'>"."<div class='greenB'  >$thisAmount</div>"."</a>";  //连接请款记录
			}
		else{
			$thisAmount="<a href='nonbom6_qkview.php?Mid=$cgMid' target='_blank'>"."<div class='yellowB'  >$thisAmount</div>"."</a>";  //连接请款记录
			}
	/////////////
	if ($qk_SumAmount>$hkAmount){
			$Choose="<div class='redB' title='请款金额大于采购金额:$qk_SumAmount>$hkAmount'>错误</div>";  
	   	}
    else{
		$Choose="<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled>";
      }
		echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>
				<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>
				<td width='60' align='center' class='A0111'>$Choose</td>
				<td width='70' align='center' class='A0101'>$cgDate</td>
				<td width='50' align='center' class='A0101'>$Name</td>
				<td width='80' class='A0101'>$Forshort</td>
				<td width='60' align='center' class='A0101'>$PurchaseID</td>
				<td width='70' align='right' class='A0101'>$hkAmount</td>
				<td width='40' align='center' class='A0101'>$Attached</td>		
				<td width='60' align='center' class='A0101'>$Month</td>
				<td width='70' align='right' class='A0101'>$thisAmount</td>	
				<td width='80' align='right' class='A0101'>$InvoiceNo</td>			
				<td colspan='16' class='A0101'>";
				echo"<table width='100%' cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;margin-top:-1px;margin-left:-1px;margin-bottom:-1px;'>";			
		////////明细///////
//读取明细
				$checkSubSql=mysql_query("SELECT '1' AS fromType,A.Id,A.Mid,A.GoodsId,A.Qty,A.Price,(A.Qty*A.Price) AS Amount,A.rkSign,A.Estate,A.Locks,A.Date,B.GoodsName,B.Attached,B.Unit,C.Name,D.wStockQty,D.oStockQty,D.mStockQty
					FROM $DataIn.nonbom6_cgsheet A
					LEFT JOIN $DataPublic.nonbom4_goodsdata B ON B.GoodsId=A.GoodsId 
					LEFT JOIN $DataPublic.staffmain C ON C.Number=A.Operator
					LEFT JOIN $DataPublic.nonbom5_goodsstock D ON D.GoodsId=A.GoodsId	
					WHERE A.Mid='$cgMid'
					UNION ALL
					SELECT '2' AS fromType,A.Id,A.Mid,A.GoodsId,A.Qty,A.Price,(A.Qty*A.Price) AS Amount,A.rkSign,A.Estate,A.Locks,A.Date,B.GoodsName,B.Attached,B.Unit,C.Name,D.wStockQty,D.oStockQty,D.mStockQty
					FROM $DataIn.nonbom6_cgsheet A
					LEFT JOIN $DataPublic.nonbom4_goodsdata B ON B.GoodsId=A.GoodsId 
					LEFT JOIN $DataPublic.staffmain C ON C.Number=A.Operator
					LEFT JOIN $DataPublic.nonbom5_goodsstock D ON D.GoodsId=A.GoodsId	
					WHERE A.fromMid='$cgMid' AND A.GoodsId='70442'
					",$link_id);
					if($checkSubRow = mysql_fetch_array($checkSubSql)){
					$k=1;
					do{
						$rowColor="";
						$sheetId=$checkSubRow["Id"];
						$GoodsId=$checkSubRow["GoodsId"];
						$GoodsName=$checkSubRow["GoodsName"];
						$Attached=$checkSubRow["Attached"];
						
						$Qty=$checkSubRow["Qty"];
						$Price=$checkSubRow["Price"];
						$Unit=$checkSubRow["Unit"];
						$Amount=sprintf("%.2f",$checkSubRow["Amount"]);
						//入库数量
						$cgId=$checkSubRow["Id"];
						$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.nonbom7_insheet WHERE GoodsId='$GoodsId' AND cgId='$cgId'",$link_id);
						$rkQty=mysql_result($rkTemp,0,"Qty");
						$rkQty=$rkQty==""?0:del0($rkQty);
						$wsQty=$Qty-$rkQty;//欠数
						//
						if($rkQty==$Qty){
							$rkBgColor="class='greenB'";
							$rkSign="<sapn class='greenB'>已收货</span>";
							$rkQty="<a href='nonbom7_list.php?cgId=$cgId' target='_blank' style='color:#093'>$rkQty</a>";
							//更新入库标记
							if ($mainRows["rkSign"]>0){
						 		$UprkSignSql="UPDATE $DataIn.nonbom6_cgsheet SET rkSign='0' WHERE Id='$cgId' ";
			              		$UprkSignResult = mysql_query($UprkSignSql,$link_id);
								}
							}
						else{
							$rkBgColor="class='redB'";
							$wsBgColor="class='redB'";
							if($rkQty==0){
								$rkSign="<sapn class='redB'>未收货</span>";
								$rkQty="&nbsp;";
								$rkSignVal=1;
								}
							else{
								$rkSign="<sapn class='yellowB'>部分收货</span>";
								$rkQty="<a href='nonbom7_list.php?cgId=$cgId' target='_blank' style='color:#F00'>$rkQty</a>";
								$rkSignVal=2;
								}
								//更新入库标记
								if ($mainRows["rkSign"]==0){
									 $UprkSignSql="UPDATE $DataIn.nonbom6_cgsheet SET rkSign='$rkSignVal' WHERE Id='$cgId' ";
									  $UprkSignResult = mysql_query($UprkSignSql,$link_id);
								}
							}
						//
						$wsQty=$wsQty>0?"<span class='redB'>".$wsQty."</span>":($wsQty==0?"&nbsp;":"<span class='yellowB'>?".$wsQty."</span>");
						$wStockQty=zerotospace(del0($checkSubRow["wStockQty"]));
						$oStockQty=zerotospace(del0($checkSubRow["oStockQty"]));
						$mStockQty=zerotospace(del0($checkSubRow["mStockQty"]));
						$Estate=$checkSubRow["Estate"];
						switch($Estate){
							case 1:
								$Estate="<span class='greenB'>已审核</span>";
							break;
							case 4://审核退回
								$Estate="<img src='../images/warn.gif' title='$ReturnReasons' width='18' height='18'>";
							break;
							default:
								$Estate="<span class='redB'>需审核</span>";
							break;
							}
				
						$Name=$checkSubRow["Name"];
						$Date=$checkSubRow["Date"];
						$ClassStrA=$k==1?"":"class='A1000'";
						$ClassStrB=$k==1?"class='A0010'":"class='A1010'";
						if($GoodsId=='70442'){//快递
							$wStockQty=$oStockQty=$mStockQty="&nbsp;";
							}
							
						if($checkSubRow["fromType"]==2){
							$rowColor=" class='blueB' ";
							$k="★";
							//物料采购单号
							}
						else{
							//配件分析
							if($GoodsId=="70442"){//如果是快递配件，则查找关系的物料采购单
								$checkFromSql=mysql_query("SELECT A.fromMid,B.PurchaseID FROM $DataIn.nonbom6_cgsheet A LEFT JOIN $DataIn.nonbom6_cgmain B ON B.Id=A.fromMid WHERE A.Id='$sheetId' AND B.Id!=A.Mid",$link_id);
								if($checkFromRow=mysql_fetch_array($checkFromSql)){
									$fromPurchaseID=$checkFromRow["PurchaseID"];	//源采购单号
									$fromMid=$checkFromRow["fromMid"];					//源采购单ID
									$fromMid=anmaIn($fromMid,$SinkOrder,$motherSTR);
									$GoodsName.="<a href='nonbom6_view.php?f=$fromMid' target='_blank'>(采购单:$fromPurchaseID)</a>";
									}
								}
							else{
								$Price="<a href='nonbom4_history.php?GoodsId=$GoodsId' target='_blank'>$Price</a>";
								$GoodsId="<a href='nonbom4_report.php?GoodsId=$GoodsId' target='_blank'>$GoodsId</a>";
								if($Attached==1){
									$Attached=$GoodsId.".jpg";
									$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
									$GoodsName="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
									}
								}
							}
						
						echo "<tr $rowColor>
							<td width='30' align='center' hight='20' $ClassStrA>$k</td>
							<td width='50' align='center' $ClassStrB>$GoodsId</td>
							<td width='250' $ClassStrB>$GoodsName</td>
							<td width='60' align='right' $ClassStrB>$Price</td>
							<td width='60' align='right' $ClassStrB>$Qty</td>
							<td width='30' align='right' $ClassStrB>$Unit</td>
							<td width='60' align='right' $ClassStrB>$Amount</td>
							<td width='50' align='right' $ClassStrB>$rkQty</td>
							<td width='50' align='right' $ClassStrB>$wsQty</td>
							<td width='50' align='right' $ClassStrB>$wStockQty</td>
							<td width='50' align='right' $ClassStrB>$oStockQty</td>
							<td width='50' align='right' $ClassStrB>$mStockQty</td>
							<td width='40' align='right' $ClassStrB>$Estate</td>
							<td width='40' align='right' $ClassStrB>$rkSign</td>
							<td width='70' align='center' $ClassStrB>$Date</td>
							<td width='49' align='right' $ClassStrB>$Name&nbsp;</td>
							</tr>";
							$k++;
							}while($checkSubRow = mysql_fetch_array($checkSubSql));
						}
		/////////////////
		echo"</table>";//结束明细表格
		echo"</td></tr></table>";//结束明细行
		$i++;
		}while($mainRows = mysql_fetch_array($mainResult));
	//合计
	echo "<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#CCCCCC'><tr>
				<td width='60' align='center' class='A0111' height='25'>合计</td>
				<td width='70' class='A0101'>&nbsp;</td>
				<td width='50' class='A0101'>&nbsp;</td>
				<td width='80' class='A0101'>&nbsp;</td>
				<td width='60' class='A0101'>&nbsp;</td>
				<td width='70' align='right' class='A0101'>$Sum_hkAmount</td>
				<td width='40'  class='A0101'>&nbsp;</td>		
				<td width='60' class='A0101'>&nbsp;</td>
				<td width='70' align='right' class='A0101'>$Sum_thisAmount</td>
				<td width='80' class='A0101'>&nbsp;</td>
					<td width='30'  class='A0101'>&nbsp;</td>
					<td width='50' class='A0101'>&nbsp;</td>
					<td width='250' class='A0101'>&nbsp;</td>
					<td width='60' class='A0101'>&nbsp;</td>
					<td width='60' class='A0101'>&nbsp;</td>
					<td width='30' class='A0101'>&nbsp;</td>
					<td width='60' align='right' class='A0101'>$Sum_Amount</td>
					<td width='50' class='A0101'>&nbsp;</td>
					<td width='50' class='A0101'>&nbsp;</td>
					<td width='50' class='A0101'>&nbsp;</td>
					<td width='50' class='A0101'>&nbsp;</td>
					<td width='50' class='A0101'>&nbsp;</td>
					<td width='40' class='A0101'>&nbsp;</td>
					<td width='40' class='A0101'>&nbsp;</td>
					<td width='70' class='A0101'>&nbsp;</td>
					<td width='50' class='A0101'>&nbsp;</td>
		</tr></table>";
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

<script src='../model/pagefun_Sc.js' type=text/javascript></script>
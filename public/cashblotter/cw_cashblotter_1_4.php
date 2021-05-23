<?php
//4	非BOM采购货款支付
//ewen 2013-12-27 OK
$mySql="
SELECT 
	A.CompanyId,A.PayDate,A.PayAmount,A.Payee,A.Remark AS PayRemark,
	B.cgMid,B.Amount AS qk_Amount,B.Month,B.Remark,
	C.Forshort,
	D.Title,
	E.PurchaseID,E.Date AS cgDate,E.taxAmount AS cg_taxAmount,E.shipAmount AS cg_shipAmount
	,G.Name
 	FROM $DataIn.nonbom11_qkmain A
	LEFT JOIN $DataIn.nonbom11_qksheet B ON B.Mid=A.Id
	LEFT JOIN $DataPublic.nonbom3_retailermain C ON C.CompanyId=B.CompanyId
	LEFT JOIN $DataPublic.my2_bankinfo D ON D.Id=A.BankId
	LEFT JOIN $DataIn.nonbom6_cgmain E ON E.Id=B.cgMid
	LEFT JOIN $DataPublic.currencydata F ON F.Id=C.Currency
	LEFT JOIN $DataPublic.staffmain G ON G.Number=E.BuyerId
	WHERE A.Id='$Id_Remark'";
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='70' class='A1111'>供应商</td>
<td width='70' class='A1101'>结付日期</td>
<td width='30' class='A1101'>结付<br>凭证</td>
<td width='200' class='A1101'>结付备注</td>
<td width='60' class='A1101'>结付金额</td>
<td width='100' class='A1101'>结付银行</td>
<td width='40' class='A1101'>序号</td>
<td width='100' class='A1101'>下单日期</td>
<td width='60' class='A1101'>采购</td>
<td width='60' class='A1101'>采购单号</td>
<td width='80' class='A1101'>总货款</td>
<td width='60' class='A1101'>本次请款</td>
<td width='50' class='A1101'>请款月份</td>
<td width='150' class='A1101'>请款备注</td>

</tr>";
$mainResult = mysql_query($mySql." $PageSTR",$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	$nDir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
	$ImgDir="download/cwnonbom/";
	$count = mysql_num_rows($mainResult);
	if($count>1){
		$Rowspan="rowspan='$count'";
		}
	$i=1;
	do{
		$m=1;
		//1-供应商
		$Forshort=$mainRows["Forshort"];			
		$CompanyId=$mainRows["CompanyId"];
		$CompanyId=anmaIn($CompanyId,$SinkOrder,$motherSTR);		
		$Forshort="<a href='../nonbom/nonbom3_view.php?d=$CompanyId' target='_blank'>$Forshort</a>";
		//2-结付日期
		$PayDate=$mainRows["PayDate"];
		//3-凭证
		$Mid=$Id_Remark;
		$Payee=$mainRows["Payee"];
		$Receipt=$mainRows["Receipt"];
		include "../model/subprogram/cw0_imgview.php";		
		//4-结付备注
		$PayRemark=$mainRows["PayRemark"]==""?"&nbsp":$mainRows["PayRemark"];
		//-5结付金额
		$PayAmount=sprintf("%.2f",$mainRows["PayAmount"]);
		//-6结付银行
		$BankName=$mainRows["Title"];

		
		//结付明细数据
		$cgMid=$mainRows["cgMid"];
		//8-采购日期
		$cgDate=$mainRows["cgDate"];
		//9-采购员
		$Name=$mainRows["Name"];
		//10-采购单号
		$PurchaseID=$mainRows["PurchaseID"];
		$cgMidSTR=anmaIn($cgMid,$SinkOrder,$motherSTR);
		$PurchaseID="<a href='../nonbom/nonbom6_view.php?f=$cgMidSTR' target='_blank'  title='$cgMid'>$PurchaseID</a>";
		
		$cg_taxAmount=sprintf("%.2f",$mainRows["cg_taxAmount"]);
		$cg_shipAmount=sprintf("%.2f",$mainRows["cg_shipAmount"]);
		$checkHk=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty*Price),0) AS cg_hkAmount FROM $DataIn.nonbom6_cgsheet WHERE Mid='$cgMid' ",$link_id));		
		$cg_hkAmount=sprintf("%.2f",$checkHk["cg_hkAmount"]);   //货款
		//11-总货款
		$cg_allAmount=sprintf("%.2f",$cg_hkAmount+$cg_taxAmount+$cg_shipAmount);
		//12-本次请款
		$qk_Amount=sprintf("%.2f",$mainRows["qk_Amount"]);
		//13-请款月份
		$Month=$mainRows["Month"];
		//14-请款备注
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		if($i==1){//首行
			//并行列
			echo"<tr align='center'>";
			echo"<td $Rowspan class='A0111' align='left'>$Forshort</td>";		//供应商
			echo"<td $Rowspan class='A0101'>$PayDate</td>";						//结付日期
			echo"<td $Rowspan class='A0101'>$Payee</td>";							//凭证
			echo"<td $Rowspan class='A0101' align='left'>$PayRemark</td>";	//备注
			echo"<td $Rowspan class='A0101' align='right'>$PayAmount</td>";	//结付货额
			echo"<td $Rowspan class='A0101'>$BankName</td>";					//结付银行
			}
		else{
			echo"<tr align='center'>";
			}
			echo"<td class='A0101' height='20'>$i</td>";									//序号
			echo"<td class='A0101'>$cgDate</td>";											//下单日期
			echo"<td class='A0101'>$Name</td>";											//采购
			echo"<td class='A0101'>$PurchaseID</td>";									//采购单号
			echo"<td class='A0101' align='right'>$cg_hkAmount</td>";				//采购单货款
			echo"<td class='A0101' align='right'>$qk_Amount</td>";					//本次请款
			echo"<td class='A0101'>$Month</td>";											//请款月份
			echo"<td class='A0101' align='left'>$Remark</td>";							//请款备注
			echo"</tr>";
			$i++;
		}while($mainRows = mysql_fetch_array($mainResult));
	
	}
else{
	//noRowInfo($tableWidth);
	}
echo"</table>";
?>
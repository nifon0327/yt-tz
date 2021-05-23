<?php
//1 BOM供应商扣款
//ewen 2013-09-04 OK
$checkSql=mysql_query("SELECT A.Id,A.PayDate,A.PayAmount,A.djAmount,A.Payee,A.Receipt,A.Checksheet,A.Remark AS PayRemark,B.Title,C.Forshort
FROM $DataIn.cw1_fkoutmain A 
LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=A.CompanyId
WHERE A.Id='$Id_Remark'",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='70' class='A1111'>供应商</td>
<td width='70' class='A1101'>结付日期</td>
<td width='30' class='A1101'>结付<br>凭证</td>
<td width='30' class='A1101'>结付<br>备注</td>
<td width='60' class='A1101'>已付订金</td>
<td width='60' class='A1101'>已抵扣款</td>
<td width='60' class='A1101'>实付金额</td>
<td width='80' class='A1101'>结付银行</td>

<td width='30' class='A1101'>序号</td>
<td width='70' class='A1101'>采购单号</td>
<td width='100' class='A1101'>采购流水号</td>
<td width='170' class='A1101'>配件名称</td>
<td width='50' class='A1101'>单价</td>
<td width='40' class='A1101'>扣款<br>数量</td>
<td width='50' class='A1101'>扣款<br>金额</td>
<td width='200' class='A1101'>扣款原因</td>
</tr>";
if($checkRow=mysql_fetch_array($checkSql)){
	$Dir=anmaIn("download/fbdh/",$SinkOrder,$motherSTR);
	$i=1;
	//**********
	//结付主表数据
	$Mid=$checkRow["Id"];
	$PayDate=$checkRow["PayDate"];
	$djAmount=$checkRow["djAmount"];
	$PayAmount=$checkRow["PayAmount"];
	$BankName=$checkRow["Title"];
	$Forshort=$checkRow["Forshort"];
	$ImgDir="download/cwfk/";
	$Payee=$checkRow["Payee"];
	include "../model/subprogram/cw0_imgview.php";		
	$PayRemark=$checkRow["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$checkRow[PayRemark]' width='16' height='16'>";
	//供应商扣款数
	$KKReuslt=mysql_query("SELECT SUM(Amount) AS KKAmount FROM $DataIn.cw15_gyskksheet WHERE Kid='$Mid'",$link_id);
	$KKAmount=mysql_result($KKReuslt,0,"KKAmount");
	$PayAmount=$PayAmount-$KKAmount;
	$KKAmount=$KKAmount==0?"0.00":"<a href='cw_cgkk_read.php?Kid=$Mid' target='_bank'>$KKAmount</a>";

	$checkSheetSql=mysql_query("
		SELECT S.Id,S.PurchaseID,S.StockId,S.StuffId,S.Qty,S.Price,S.Amount,S.StuffName,S.Remark AS SheetRemark,D.Picture AS StuffPicture
         FROM $DataIn.cw15_gyskksheet S
         LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
		 LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
         WHERE S.Kid='$Mid' ORDER BY S.Id
		 
		",$link_id);
	if($checkSheetRow=mysql_fetch_array($checkSheetSql)){
		//计算子记录数量
		$Rowspan=mysql_num_rows($checkSheetSql);
		$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
		//输出首行前段
		echo"<tr><td scope='col' class='A0111' align='center' rowspan='$Rowspan' valign='top'>$Forshort</td>";		//供应商
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$PayDate</td>";							//结付日期
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$Payee</td>";								//凭证
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$PayRemark</td>";						//结付备注
		echo"<td rowspan='$Rowspan' class='A0101' align='right' valign='top'>$djAmount</td>";							//订金总额
		echo"<td rowspan='$Rowspan' class='A0101' align='right' valign='top'>$KKAmount</td>";							//供应商扣款
		echo"<td rowspan='$Rowspan' class='A0101' align='right' valign='top'>$PayAmount</td>";							//结付总额
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$BankName</td>";						//结付银行	
		$j=1;
		do{
			//结付明细数据
			//**********************
			$StuffId=$checkSheetRow["StuffId"];
			if($StuffId!=""){
				$PurchaseID=$checkSheetRow["PurchaseID"]==0?"&nbsp;":$checkSheetRow["PurchaseID"];
				$StockId=$checkSheetRow["StockId"]==0?"&nbsp;":$checkSheetRow["StockId"];
				$StuffCname=$checkSheetRow["StuffName"];
				$Price=$checkSheetRow["Price"];
				$Qty=$checkSheetRow["Qty"];
				$Amount=$checkSheetRow["Amount"];
            	$SheetRemark=$checkSheetRow["SheetRemark"]==""?"&nbsp;":$checkSheetRow["SheetRemark"];	
				//检查是否有图片
          		$Picture=$checkSheetRow["StuffPicture"];
		  		include "../model/subprogram/stuffimg_model.php";	//检查是否有图片	          
                  }
			//*********************
			if($j>1) echo "<tr>";
			echo"<td class='A0101' align='center' height='20'>$j</td>";			//序号
			echo"<td class='A0101' align='center'>$PurchaseID</td>";			//采购单号			
			echo"<td class='A0101' align='center'>$StockId</td>";					//流水号
			echo"<td class='A0101'>$StuffCname</td>";								//配件名称
			echo"<td  class='A0101' align='right'>$Price</td>";						//单价	
			echo"<td class='A0101' align='right'>$Qty</td>";							//需扣数量		
			echo"<td  class='A0101' align='right'>$Amount</td>";					//扣款金额		
			echo"<td  class='A0101'>$SheetRemark</td>";		//扣款原因
			echo"</tr>";
			$j++;
			}while ($checkSheetRow=mysql_fetch_array($checkSheetSql));
		}
	$i++;
	}
else{
	
	}
?>
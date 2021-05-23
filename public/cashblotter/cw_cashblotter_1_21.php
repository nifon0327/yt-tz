<?php
//21 寄样费						OK
//ewen 2013-09-04 OK
$checkSql=mysql_query("SELECT A.Id,A.PayDate,A.PayAmount,A.Payee,A.Remark AS PayRemark,B.Title
FROM $DataIn.cw10_samplemail A LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId WHERE A.Id='$Id_Remark'",$link_id);
echo"<tr align='center' bgcolor='#CCCCCC'>
<td width='70' class='A1111'>结付日期</td>
<td width='30' class='A1101'>结付<br>凭证</td>
<td width='30' class='A1101'>结付<br>备注</td>
<td width='60' class='A1101'>结付金额</td>
<td width='100' class='A1101'>结付银行</td>

<td width='40' class='A1101'>序号</td>
<td width='70' class='A1101'>寄件日期</td>
<td width='80' class='A1101'>快递公司</td>
<td width='70' class='A1101'>客户</td>
<td width='70' class='A1101'>目的地</td>
<td width='100' class='A1101'>提单号码</td>
<td width='40' class='A1101'>发票</td>
<td width='40' class='A1101'>样品<br>进度</td>
<td width='40' class='A1101'>寄送<br>进度</td>
<td width='40' class='A1101'>件数</td>
<td width='40' class='A1101'>重量</td>
<td width='40' class='A1101'>单价</td>
<td width='50' class='A1101'>金额</td>
<td width='50' class='A1101'>经手人</td>
<td width='40' class='A1101'>签收<br>日期</td>
<td width='300' class='A1101'>备注</td>
</tr>";
if($checkRow=mysql_fetch_array($checkSql)){
	$d=anmaIn("download/samplemail/",$SinkOrder,$motherSTR);//寄样图片、进度图片目录
	$d2=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
	$i=1;
	//**********
	//结付主表数据
	$Mid=$checkRow["Id"];
	$PayDate=$checkRow["PayDate"];
	$PayAmount=$checkRow["PayAmount"];
	$BankName=$checkRow["Title"];
	$ImgDir="download/samplemail/";
	$Payee=$checkRow["Payee"];
	include "../model/subprogram/cw0_imgview.php";		
	$PayRemark=$checkRow["PayRemark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$checkRow[PayRemark]' width='16' height='16'>";
	$checkSheetSql=mysql_query("SELECT 
	S.Id,S.Mid,S.DataType,S.LinkMan,S.ExpressNO,S.Pieces,S.Weight,S.Qty,S.Price,S.Amount,S.PayType,S.ServiceType,S.Description,S.Remark,S.Schedule,S.SendDate,S.ReceiveDate,S.Estate,S.Locks,S.Operator,
	P.Name AS HandledBy,D.Forshort,C.Forshort AS Client,A.Termini
 	FROM $DataIn.ch10_samplemail S
	LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=S.CompanyId 
	LEFT JOIN $DataIn.ch10_mailaddress A ON A.Id=S.LinkMan
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=A.CompanyId 
	LEFT JOIN $DataPublic.staffmain P ON P.Number=S.Operator
	WHERE S.Mid='$Mid' order by S.SendDate DESC
	
	",$link_id);
	if($checkSheetRow=mysql_fetch_array($checkSheetSql)){
		//计算子记录数量
		$Rowspan=mysql_num_rows($checkSheetSql);
		//输出首行前段
		echo"<tr><td scope='col' class='A0111' align='center' rowspan='$Rowspan' valign='top'>$PayDate</td>";	//结付日期
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$Payee</td>";							//凭证
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$PayRemark</td>";					//结付备注
		echo"<td rowspan='$Rowspan' class='A0101' align='right' valign='top'>$PayAmount</td>";						//结付总额
		echo"<td rowspan='$Rowspan' class='A0101' align='center' valign='top'>$BankName</td>";						//结付银行	
		$j=1;
		do{
			//结付明细数据
			$Id=$checkSheetRow["Id"];
			$SendDate=$checkSheetRow["SendDate"];
			$Forshort=$checkSheetRow["Forshort"];
			$Client=$checkSheetRow["Client"];
			$Termini=$checkSheetRow["Termini"];
			$ExpressNO=$checkSheetRow["ExpressNO"];
			//提单
			$Lading="../download/expressbill/".$ExpressNO.".jpg";
			if(file_exists($Lading)){
				$f1=anmaIn($ExpressNO.".jpg",$SinkOrder,$motherSTR);
				$ExpressNO="<span onClick='OpenOrLoad(\"$d2\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ExpressNO</span>";
				}
			//发票
			$Invoice="<a href='ch_invoicemodel.php?I=$Id' target='_black'>View</a>";
			//照片
			$SamplePicture="&nbsp;";
			$checkPicture=mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.ch10_samplepicture WHERE Mid='$Id'",$link_id));
			if($checkPicture["Id"]!=""){			
				$f2=anmaIn($Id,$SinkOrder,$motherSTR);
				$t=anmaIn("ch10_samplepicture",$SinkOrder,$motherSTR);
				$SamplePicture="<span onClick='OpenPhotos(\"$d\",\"$f2\",\"$t\")' style='CURSOR: pointer;color:#FF6633'>view</span>";
				}
			//进度
			$Schedule=$myRow["Schedule"]==0?"&nbsp;":$myRow["Schedule"];
			if($Schedule==1){
				$f3=anmaIn("Schedule".$Id.".jpg",$SinkOrder,$motherSTR);
				$Schedule="<span onClick='OpenOrLoad(\"$d\",\"$f3\")' style='CURSOR: pointer;color:#FF6633'>view</span>";
				}
			$Pieces=$checkSheetRow["Pieces"];
			$Weight=$checkSheetRow["Weight"];
			$Price=$checkSheetRow["Price"];
			$Amount=$checkSheetRow["Amount"];
			$HandledBy=$checkSheetRow["HandledBy"];		
			$ReceiveDate=$checkSheetRow["ReceiveDate"]==""?"&nbsp;":$checkSheetRow["ReceiveDate"];
			$Remark=$checkSheetRow["Remark"]==""?"&nbsp;":$checkSheetRow["Remark"];
			if($j>1) echo "<tr>";
			echo"<td class='A0101' align='center' height='20'>$j</td>";	
			echo"<td class='A0101' align='center'>$SendDate</td>";
			echo"<td class='A0101'>$Forshort</td>";
			echo"<td class='A0101'>$Client</td>";
			echo"<td class='A0101' >$Termini</td>";//目的地
			echo"<td class='A0101' align='center'>$ExpressNO</td>";
			echo"<td  class='A0101' align='center'>$Invoice</td>";//发票
			echo"<td  class='A0101' align='center'>$SamplePicture</td>";//样品照片
			echo"<td  class='A0101' align='center'>$Schedule</td>";//寄样进度
			echo"<td class='A0101' align='right'>$Pieces</td>";
			echo"<td class='A0101' align='right'>$Weight</td>";
			echo"<td  class='A0101' align='center'>$Price</td>";//单价
			echo"<td class='A0101' align='right'>$Amount</td>";
			echo"<td class='A0101' align='center'>$HandledBy</td>";
			echo"<td  class='A0101' align='center'>$ReceiveDate</td>";//签收日期
			echo"<td  class='A0101'>$Remark</td>";
			echo"</tr>";
			$j++;
			}while ($checkSheetRow=mysql_fetch_array($checkSheetSql));
		}
	$i++;
	}
else{
	
	}
?>
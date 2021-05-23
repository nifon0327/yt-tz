<?php 
//电信
//代码共享-EWEN 2012-08-19
//国内快递费(月结/现金)
//条件
$MonthSTR=$Month==""?"":" AND DATE_FORMAT(A.SendDate,'%Y-%m')='$Month'";
switch($DataT){
	case "Y":$DataTSTR="已结付记录"; $EstateSTR=" AND A.Estate=0"; break;
	case "W":$DataTSTR="未结付记录"; $EstateSTR=" AND A.Estate=3";break;
	case "A":$DataTSTR="全部记录"; $EstateSTR=" AND (A.Estate=0 OR A.Estate=3)";break;
	}
$ShowInfo=$ItemName." ".$Month.$DataTSTR."：".$Remark;
echo"
<div style='color: #FFF;'>$ShowInfo</div>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px;height:480px' align='center'>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td width='50' height='20' class='A1011'>序号</td>
<td width='70' class='A1001'>寄件日期</td>
<td width='60'  class='A1001'>快递公司</td>
<td width='70' class='A1001'>客户</td>
<td width='100' class='A1001'>目的地</td>
<td width='100' class='A1001'>提单号码</td>
<td width='40' class='A1001'>发票</td>
<td width='40' class='A1001'>样品<br>照片</td>
<td width='40' class='A1001'>寄送<br>进度</td>
<td  class='A1001'>备注</td>
<td width='50' class='A1001'>经手人</td>
<td width='60' class='A1001'>签收日期</td>
<td width='40' class='A1001'>件数</td>
<td width='40' class='A1001'>重量<br>(KG)</td>
<td width='40' class='A1001'>单价</td>
<td width='60' class='A1001'>金额</td>
</tr>
<tr>
<td colspan='16' height='450px'>
<div style='width:1111px;height:450px;overflow-x:hidden;overflow-y:none;margin-left:-1px'>
<table cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:1110px' align='center'>
";
//读取记录
$checkSql=mysql_query("
SELECT A.Id,A.SendDate,A.ExpressNO,A.Remark,P.Name AS HandledBy,A.ReceiveDate,A.Pieces,A.Weight,A.Price,A.Amount,B.Forshort AS Freight,C.Termini,D.Forshort AS Client
FROM $DataIn.ch10_samplemail A
LEFT JOIN $DataPublic.freightdata B ON B.CompanyId=A.CompanyId 
LEFT JOIN $DataIn.ch10_mailaddress C ON C.Id=A.LinkMan
LEFT JOIN $DataIn.trade_object D ON D.CompanyId=C.CompanyId 
LEFT JOIN $DataPublic.staffmain P ON P.Number=A.HandledBy
WHERE 1 AND A.SendDate>='2008-07-01' $MonthSTR 
$EstateSTR ORDER BY A.SendDate DESC,A.Id DESC
",$link_id);
$i=1;
$SumAmount=0;
if($checkRow=mysql_fetch_array($checkSql)){
	$d=anmaIn("download/samplemail/",$SinkOrder,$motherSTR);//寄样图片、进度图片目录
	$d2=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
	do{
		$Id=$checkRow["Id"];
		$SendDate=$checkRow["SendDate"];
		$Freight=$checkRow["Freight"];
		$Client=$checkRow["Client"];
		$Termini=$checkRow["Termini"];
		$ExpressNO=$checkRow["ExpressNO"];
		//提单
		$Lading="../download/expressbill/".$ExpressNO.".jpg";
		if(file_exists($Lading)){
			
			//add by cabbage 20141210 app用，附件路徑
			$filePath = "/download/expressbill/".$ExpressNO.".jpg";
			
			$f1=anmaIn($ExpressNO.".jpg",$SinkOrder,$motherSTR);
			$ExpressNO="<span onClick='OpenOrLoad(\"$d2\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ExpressNO</span>";
			}
		//发票
		$Invoice="<a href='../admin/ch_invoicemodel.php?I=$Id' target='_black'>View</a>";
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
		
		$Content=$checkRow["Content"]==""?$checkRow["Remark"]:$checkRow["Remark"]." / ".$checkRow["Content"];
		$HandledBy=$checkRow["HandledBy"];
		$ReceiveDate=$checkRow["ReceiveDate"];
		$Pieces=$checkRow["Pieces"];
		$Weight=$checkRow["Weight"];
		
		$Price=$checkRow["Price"];
		$AmountRMB=sprintf("%.2f",$checkRow["Amount"]);
		$SumAmount+=$AmountRMB;
		$AmountRMB=number_format($AmountRMB);
		
		//add by cabbage 20141210 app採集單月紀錄
		$detailList[$i - 1] = array(
			"Date" => $SendDate,
			"AmountRMB" => $AmountRMB,
			"Freight" => $Freight,
			"Content" => $Content,
			"Operator" => $HandledBy
		);
		if (strlen($filePath) > 0) {
			$detailList[$i - 1]["FileLink"] = $filePath;
		}
		
		echo"
			<tr bgcolor='#ECEAED' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
			<td width='50' height='20' class='A0111' align='center'>$i</td>
			<td width='70'  class='A0101'>$SendDate</td>
			<td width='60' class='A0101'>$Freight</td>
			<td width='70' class='A0101'>$Client</td>
			<td width='100' class='A0101'>$Termini</td>
			<td width='100'  class='A0101'>$ExpressNO</td>
			<td width='40' class='A0101' align='center'>$Invoice</td>
			<td width='40' class='A0101' align='center'>$SamplePicture</td>
			<td width='40' class='A0101' align='center'>$Schedule</td>
			<td class='A0101'>$Content</td>
			<td width='50'  class='A0101'>$HandledBy</td>
			<td width='60' class='A0101'>$ReceiveDate</td>
			<td width='40' class='A0101' align='right'>$Pieces</td>
			<td width='40' class='A0101' align='center'>$Weight</td>
			<td width='40' class='A0101' align='center'>$Price</td>
			<td width='60' class='A0100' align='right'>¥ $AmountRMB</td>
			</tr>";
		$i++;
		}while($checkRow=mysql_fetch_array($checkSql));
	}
for($j=$i;$j<27;$j++){//补空行
	echo"
	<tr bgcolor='#ECEAED' align='center' onmouseover=\"this.style.background='#F6C'; \" onmouseout =\"this.style.background='#ECEAED'; \">
		<td width='50' height='20' class='A0111'>$j</td>
		<td width='70'  class='A0101'>&nbsp;</td>
		<td width='60' class='A0101'>&nbsp;</td>
		<td width='70' class='A0101' align='center'>&nbsp;</td>
		<td width='100' class='A0101' align='center'>&nbsp;</td>
		<td width='100'  class='A0101'>&nbsp;</td>
		<td width='40' class='A0101'>&nbsp;</td>
		<td width='40' class='A0101' align='center'>&nbsp;</td>
		<td width='40' class='A0101' align='center'>&nbsp;</td>
		<td class='A0101'>&nbsp;</td>
		<td width='50'  class='A0101'>&nbsp;</td>
		<td width='60'  class='A0101'>&nbsp;</td>
		<td width='40' class='A0101'>&nbsp;</td>
		<td width='40' class='A0101' align='center'>&nbsp;</td>
		<td width='40' class='A0101' align='center'>&nbsp;</td>
		<td width='60' class='A0100' align='right'>&nbsp;</td>
	</tr>";
	}
$SumAmount=number_format(sprintf("%.0f",$SumAmount));
echo"</table>
</div>
</td>
</tr>
<tr bgcolor='#36C' align='center' style='Color:#FFF'>
<td height='20' class='A0111' colspan='2'>合计</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0100'>&nbsp;</td>
<td  class='A0101' align='right'>¥$SumAmount</td>
</tr>
</table>
";
?>
<?php 
//寄样费用 OK
include "../../basic/chksession.php" ;
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$tableWidth=1000;
$TempArray=explode("|",$TempId);
$MonthTemp=$TempArray[0];
		$Th_Col="选项|40|序号|30|寄件日期|70|快递公司|60|客户|70|目的地|120|提单号码|100|发票|40|样品<br>照片|40|寄送<br>进度|40|件数|40|重量<br>(KG)|40|单价|40|金额|50|经手人|50|签收日期|70|状态|40|备注|40";
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='70' align='center'>寄件日期</td>
		<td width='60' align='center'>快递公司</td>
		<td width='70' align='center'>客户</td>
		<td width='120' align='center'>目的地</td>
		<td width='100' align='center'>提单号码</td>
		<td width='40' align='center'>发票</td>
		<td width='40' align='center'>样品<br>照片</td>
		<td width='40' align='center'>寄送<br>进度</td>
		<td width='40' align='center'>件数</td>
		<td width='40' align='center'>单价</td>
		<td width='40' align='center'>重量<br>(KG)</td>
		<td width='50' align='center'>金额</td>
		<td width='50' align='center'>经手人</td>
		<td width='70' align='center'>签收日期</td>
		<td width='40' align='center'>状态</td>
		<td width='40' align='center'>票据</td>
	</tr></table>";
$SearchRows=" AND S.Estate='3' AND DATE_FORMAT(S.SendDate,'%Y-%m')='$MonthTemp'";

$mySql="SELECT 
S.Id,S.Mid,S.DataType,S.CompanyId,S.LinkMan,S.ExpressNO,S.Pieces,S.Weight,S.Qty,S.Price,S.Amount,
S.PayType,S.ServiceType,S.Description,S.Remark,S.Schedule,S.SendDate,S.ReceiveDate,S.Estate,S.Locks,S.Operator
,P.Name AS HandledBy,C.Forshort AS Client,D.Forshort AS Freight,
M.Termini 
FROM $DataIn.ch10_samplemail S
LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=S.CompanyId 
LEFT JOIN $DataIn.ch10_mailaddress M ON M.Id=S.LinkMan
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.staffmain P ON P.Number=S.HandledBy
WHERE 1 $SearchRows
ORDER BY S.SendDate DESC,S.Id DESC";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/samplemail/",$SinkOrder,$motherSTR);//寄样图片、进度图片目录
	$d2=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$SendDate=$myRow["SendDate"];
		$Freight=$myRow["Freight"];
		$Client=$myRow["Client"];
		$Termini=$myRow["Termini"];
		$ExpressNO=$myRow["ExpressNO"];
		//提单
		$Lading="../download/expressbill/".$ExpressNO.".jpg";
		if(file_exists($Lading)){
			$f1=anmaIn($ExpressNO.".jpg",$SinkOrder,$motherSTR);
			$ExpressNO="<span onClick='OpenOrLoad(\"$d2\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ExpressNO</span>";
			}
		//发票
		$Invoice="<a href='../public/ch_invoicemodel.php?I=$Id' target='_black'>View</a>";
		//照片
		$SamplePicture="&nbsp;";
		$checkPicture=mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.ch10_samplepicture WHERE Mid='$Id'",$link_id));
		if($checkPicture["Id"]!=""){			
			$f2=anmaIn($Id,$SinkOrder,$motherSTR);
			$t=anmaIn("ch10_samplepicture",$SinkOrder,$motherSTR);
			$SamplePicture="<span onClick='OpenPhotos(\"$d\",\"$f2\",\"$t\",\"public\")' style='CURSOR: pointer;color:#FF6633'>view</span>";
			}
		//进度
		$Schedule=$myRow["Schedule"]==0?"&nbsp;":$myRow["Schedule"];
		if($Schedule==1){
			$f3=anmaIn("Schedule".$Id.".jpg",$SinkOrder,$motherSTR);
			$Schedule="<span onClick='OpenOrLoad(\"$d\",\"$f3\")' style='CURSOR: pointer;color:#FF6633'>view</span>";
			}
		$Pieces=$myRow["Pieces"];
		$Weight=$myRow["Weight"];
		$Price=$myRow["Price"];
		$Amount=$myRow["Amount"];
		$HandledBy=$myRow["HandledBy"];		
		$ReceiveDate=$myRow["ReceiveDate"]==""?"&nbsp;":$myRow["ReceiveDate"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myrow[Remark]' width='18' height='18'>";
		$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";

         	echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='70' align='center'>$SendDate</td>
				<td width='60' align='center'>$Freight</td>
				<td width='70' align='center'>$Client</td>
				<td width='120' align='center'>$Termini</td>
                <td width='100' align='center'> $ExpressNO</td>
				<td width='40' align='center'>$Invoice</td>
				<td width='40' align='center'>$SamplePicture</td>
				<td width='40' align='center' >$Schedule</td>
				<td width='40' align='center'>$Pieces</td>
				<td width='40' align='center' >$Weight</td>
				<td width='40' align='center'>$Price</td>
				<td width='50' align='center' >$Amount</td>
				<td width='50' align='center'>$HandledBy</td>
				<td width='70' align='center' >$ReceiveDate</td>
				<td width='40' align='center'>$Estate</td>
				<td width='40' align='center' >$Remark</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>
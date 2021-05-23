<?php   
//扣供应商货款OK
include "../../basic/chksession.php" ;
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//参数拆分
$tableWidth=900;
$TempArray=explode("|",$TempId);
$MonthTemp=$TempArray[0];
echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr bgcolor='#99FF99'>
		<td width='30' height='20' align='center'>序号</td>
		<td width='100' align='center'>扣款单号</td>
		<td width='60' align='center'>凭证</td>
		<td width='70' align='center'>日期</td>
		<td width='120' align='center'>供应商</td>
		<td width='40' align='center'>备注</td>
		<td width='70' align='center'>扣款金额</td>
		<td width='40' align='center'>货币</td>
		<td width='250' align='center'>扣款原因</td>
		<td width='40' align='center'>扣款<br>状态</td>
	</tr></table>";
$SearchRows="  AND M.Estate=0 AND S.Kid=0 AND DATE_FORMAT(M.Date,'%Y-%m')='$MonthTemp'";

$mySql="SELECT S.Id,S.PurchaseID,M.Picture,S.Amount,
         S.Mid,M.BillNumber,M.Date,M.TotalAmount,M.BillFile,M.Remark,M.Estate,M.Operator,P.Forshort,S.Remark AS SheetRemark,S.Kid,C.Symbol
         FROM $DataIn.cw15_gyskksheet S
		 LEFT JOIN $DataIn.cw15_gyskkmain M ON M.Id=S.Mid
         LEFT JOIN $DataIn.trade_object  P ON P.CompanyId=M.CompanyId
		 LEFT JOIN $DataPublic.currencydata  C ON C.Id=P.Currency
         WHERE 1 $SearchRows ORDER BY M.Date";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
$i=1;
if($myRow = mysql_fetch_array($myResult)){
      $dc=anmaIn("download/cgkkbill/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		//主单信息
		$Mid=$myRow["Mid"];
		$Date=$myRow["Date"];
		$BillFile=$myRow["BillFile"];
		$BillNumber=$myRow["BillNumber"];
		$FileName=$BillNumber.".pdf";
		$fc=anmaIn($FileName,$SinkOrder,$motherSTR);
		if($BillFile==1){
		 $BillNumber="<a href=\"../admin/openorload.php?d=$dc&f=$fc&Type=&Action=6\" target=\"download\">$BillNumber</a>";  
		     }
        $PictureView=$myRow["Picture"];
		if($PictureView==1){
		    $PictureFileName=$myRow["BillNumber"].".jpg";
	        $fd=anmaIn($PictureFileName,$SinkOrder,$motherSTR);
		    $PictureView="<a href=\"../admin/openorload.php?d=$dc&f=$fd&Type=&Action=6\" target=\"download\">view</a>";  
		     }
       else $PictureView="";
		$Remark=$myRow["Remark"];
		$Remark=$Remark==""?"&nbsp;":"<img src='../images/remark.gif' title='$Remark' width='18' height='18'>";
		$TotalAmount=$myRow["TotalAmount"];
		$Forshort=$myRow["Forshort"];
		$Locks=$myRow["Locks"];
		$Estate=$myRow["Estate"];
		if($Estate==1){
		     $Estate="<div class='redB' title='未审核'>×</div>";
			 $LockRemark="";
			 }
		else {
		     $Estate="<div class='greenB' title='已审核'>√</div>";
			 }

		    $Kid=$myRow["Kid"];
			if($Kid==0)$KidEstate="<div class='redB' title='未扣款'>×</div>";
			else $KidEstate="<div class='greenB' title='已扣款'>√</div>";
			$Symbol=$myRow["Symbol"];

			$Amount=$myRow["Amount"];
			$PurchaseID=$myRow["PurchaseID"]==0?"&nbsp;":$myRow["PurchaseID"];
            $SheetRemark=$myRow["SheetRemark"]==""?"&nbsp;":$myRow["SheetRemark"];
         	echo"<table id='$TableId' width='$tableWidth' cellspacing='1' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
			<tr bgcolor='#FFFFFF'>
				<td width='30' height='20' align='center'>$i</td>
				<td width='100' align='center'>$BillNumber</td>
				<td width='60' align='center'>$PictureView</td>
		        <td width='70' align='center'>$Date</td>
				<td width='120' align='center'>$Forshort</td>
				<td width='40' align='center'>$Remark</td>
                <td width='70' align='right'> $Amount</td>
				<td width='40' align='center'>$Symbol</td>
				<td width='250' >$SheetRemark</td>
				<td width='40' align='center' >$KidEstate</td>
			</tr></table>";
		$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
?>
<?php
defined('IN_COMMON') || include '../basic/common.php';
$RowsHight=8;			//表格行高
$InvoiceHeadFontSize=8;	//头文字字体大小
$TableFontSize=8;		//表格字体大小
$Commoditycode="";
$FromFunPos="CH";
//公司信息//
$mySql1="SELECT S.Id,S.getmoneyNO, S.Amount, C.Symbol AS Currency, S.PayDate, S.Remark,S.Operator
 	FROM $DataIn.cw4_otherinsheet S 
	LEFT JOIN $DataPublic.currencydata C ON C.Id=S.Currency
	WHERE 1 AND S.Id=$Id";
//echo $mySql1;
$mainResult = mysql_query($mySql1,$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$getmoneyNO=$mainRows["getmoneyNO"];
	$Remark=$mainRows["Remark"];
	$PayDate=$mainRows["PayDate"];
	$Operator=$mainRows["Operator"];
	include "../model/subprogram/staffname.php";
	$Amount=$mainRows["Amount"];
	$CurrencyName=$mainRows["Currency"];
	}

$CheckMySql=mysql_query("SELECT * FROM $DataIn.my1_companyinfo WHERE Type='S' AND cSign=7",$link_id);
if($CheckMyRow=mysql_fetch_array($CheckMySql)){
      $S_Company=$CheckMyRow["Company"];
	  $S_Forshort=$CheckMyRow["Forshort"];
	  $S_Tel =$CheckMyRow["Tel"];
	  $S_Address =$CheckMyRow["Address"];
	  $S_Fax=$CheckMyRow["Fax"];
	  $S_WebSite=$CheckMyRow["WebSite"];
	  $S_Email=$CheckMyRow["Email"];
	  $S_ZIP=$CheckMyRow["ZIP"];
   }


$mySql2="SELECT I.Amount,I.Remark,I.Date,T.Name AS TypeName
FROM $DataIn.cw4_otherin I
LEFT JOIN $DataPublic.cw4_otherintype T ON T.Id=I.TypeId
WHERE  I.Mid=$Id";
$i=1;
$sheetResult = mysql_query($mySql2,$link_id);
if($sheetRows = mysql_fetch_array($sheetResult)){
	do{
		$Amount=$sheetRows["Amount"];
		$Date=$sheetRows["Date"];
		$SheetRemark=$sheetRows["Remark"];
		$TypeName=$sheetRows["TypeName"];
		$TotalAmount+=$Amount;
		$eurTableNo="eurTableNo".strval($i);   //每一条记录都是一个表格
		$$eurTableNo="<table  border=1 ><tr>
		<td width=10 valign=middle align=center height=$RowsHight>$i</td>
		<td width=20 valign=middle align=center >$Date</td>
		<td width=120 valign=middle align=left>$SheetRemark</td>
		<td width=27 valign=middle align=center >$TypeName</td>
		<td width=18 valign=middle align=right >$Amount</td>
		</tr></table>";
		$i++;
		}while ($sheetRows = mysql_fetch_array($sheetResult));
	}
$Counts=$i;

//总计
$eurTableNo="eurTableNo".strval($Counts);
$$eurTableNo="<table  border=1 ><tr bgcolor='#CCCCCC'>
             <td width=177 valign=middle align=left height=$RowsHight style=bold>Total</td>
			 <td width=18 valign=middle align=right style=bold>$TotalAmount</td>
             </tr><tr>
  		      <td colspan=6  height=45  align='left' valign='top'>&nbsp;<br>备注:</td>
  		      </tr></table>";
$RemarkTableNo="<table border=0><tr><td width='120' valign='top'>$Remark</td></tr></table>";

//输出Invoice
$filename="../download/otherin/$getmoneyNO.pdf";
if(file_exists($filename)){unlink($filename);}
include "cw_otherin_topdfmodel.php";

//附加图片处理
$chechPicture=mysql_query("SELECT Id AS Pid,Bill FROM $DataIn.cw4_otherin WHERE Mid='$Id' ORDER BY Id",$link_id);
if($PrictureRow=mysql_fetch_array($chechPicture)){
	do{
	   $Bill=$PrictureRow["Bill"];
	   $Pid=$PrictureRow["Pid"];
		if($Bill==1){
			    $Picture="O".$Pid.".jpg";
				$pdf->AddPage();
				$this_Photo="../download/otherin/".$Picture;
				$pdf->Image($this_Photo,10,10,190,60,"JPG");
           }
		}while($PrictureRow=mysql_fetch_array($chechPicture));
	}

$pdf->Output("$filename","F");
if($ActionId==26){
$Log.="收款单号为 $BillNumber 重置完毕!<br>";}
else{$Log.="收款单号为 $BillNumber 生成成功!<br>";}
?>

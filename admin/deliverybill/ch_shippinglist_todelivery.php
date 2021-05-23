<?php
defined('IN_COMMON') || include '../../basic/common.php';

$RowsHight=5;			//表格行高
$InvoiceHeadFontSize=8;	//头文字字体大小
$TableFontSize=8;		//表格字体大小
$FromFunPos="CH";
$Today=date("Y-m-d");
include "../model/subprogram/mycompany_info.php";
$upResult = mysql_query("SELECT M.CompanyId,M.InvoiceNO,C.Forshort
FROM $DataIn.ch1_shipmain M,$DataIn.trade_object C WHERE M.Id='$Id' AND M.CompanyId=C.CompanyId LIMIT 1",$link_id);
if($upData = mysql_fetch_array($upResult)){
	$CompanyId=$upData["CompanyId"];
	$InvoiceNO=$upData["InvoiceNO"];
	$Forshort=$upData["Forshort"];
	}
$j=1;
for($i=0;$i<$TempId-1;$i++){
		    $eurTableNo="eurTableNo".strval($i);   //每一条记录都是一个表格
	        $$eurTableNo="<table  border=1 ><tr>
		    <td width=8 valign=middle align=center height=$RowsHight rowspan='2'>$j</td>
		    <td width=20 valign=middle rowspan='2'>$OrderPO[$i]</td>
		    <td width=30 valign=middle rowspan='2'>$eCode[$i]</td>
		    <td width=60 valign=middle>$cName[$i]</td>
		    <td width=12 valign=middle align=right>$OrderQty[$i]</td>
		    <td width=65 valign=middle>$Content[$i]</td>
		    </tr>
           <tr><td  class='A0101'  height=$RowsHight>$StuffCname[$i]</td>
                   <td  class='A0101'  height=$RowsHight align=right>$StuffQty[$i]</td> 
                   <td  class='A0101'  height=$RowsHight>$StuffContent[$i]</td>
            </tr>
            </table>";
     $j++;
	}

$Counts=$i;  //记录总条数

$filename="../download/songbill/".$InvoiceNO.".pdf";
if(file_exists($filename)){unlink($filename);}
include "bill_topdf.php";
$pdf->Output("$filename","F");

?>
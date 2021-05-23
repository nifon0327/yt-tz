<?php   
//电信-EWEN
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";

header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
ChangeWtitle("业务订单拆分记录");
echo "<link rel='stylesheet' href='../cjgl/lightgreen/read_line.css'>";
echo "<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
echo "<center>";

$i=1;$sumQty=0;
$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);
$sheetResult=mysql_query("SELECT S.OrderPO,S.POrderId,S.Qty,P.cName,P.eCode,M.OrderDate,M.Operator,S.Estate ,P.ProductId 
    FROM $DataIn.yw1_ordersheet S 
    LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
    LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
    WHERE 1 and S.POrderId=$Sid LIMIT 1",$link_id);
if ($sheet_Row = mysql_fetch_array($sheetResult)){
   $OrderPO=$sheet_Row["OrderPO"]; 
   $POrderId=$sheet_Row["POrderId"];
   $OrderDate=$sheet_Row["OrderDate"]; 
$ProductId=$sheet_Row["ProductId"]; 
   $Operator=$sheet_Row["Operator"];
   include "../model/subprogram/staffname.php";
   $Qty=$sheet_Row["Qty"];
   $cName=$sheet_Row["cName"];
   $eCode=$sheet_Row["eCode"];
   $sumQty+=$Qty;
   $Estate=$sheet_Row["Estate"];
   $chDate="&nbsp;";
   $InvoiceNO="&nbsp;";
   if ($Estate==0) {
      $chResult=mysql_fetch_array(mysql_query("SELECT M.InvoiceNO,M.Date,M.InvoiceFile FROM  $DataIn.ch1_shipsheet S 
               LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
               WHERE S.POrderId=$POrderId",$link_id));
       $InvoiceNO=$chResult["InvoiceNO"];
       $chDate=$chResult["Date"];
       $InvoiceFile=$chResult["InvoiceFile"];
       $f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
       $InvoiceNO=$InvoiceFile==0?$InvoiceNO:"<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">$InvoiceNO</a>";
       $tdbgcolor="bgColor='#339900'";
      }
   else{
       $tdbgcolor="";
   }
 /* echo "<table width='640'  cellpadding='0' cellspacing='0'>
        <tr>
          <td colspan=2 height='30'><b>订单PO:</b>$OrderPO</td>
          <td colspan=3 ><b>产品名称:</b>$cName</td> 
       </tr>"; */
  echo "<table width='640'  cellpadding='0' cellspacing='0'>
        <tr> 
          <td colspan=6 height='30'  align='center' style='font-size:20px;'><b>订单拆分记录</b></td>
       </tr>
       <tr> <td colspan=6 height='20'><b>PO:</b>$OrderPO</td></tr>
       <tr> <td colspan=6 height='20'><b>产品名称:</b>$cName</td> </tr>
       <tr> <td colspan=6 height='20'><b>产品代码:</b>$eCode</td> </tr>";
  
  echo "<tr>
              <td  width='40'  align='center' class='A1111' height='30' ><b>序号</b></td>
	      <td  width='150' align='center' class='A1101'><b>订单流水号</b></td>
              <td  width='80' align='center' class='A1101'><b>数量</b></td>
              <td  width='120' align='center' class='A1101'><b>拆分日期</b></td>
              <td  width='80' align='center' class='A1101'><b>出货日期</b></td>
              <td  width='90' align='center' class='A1101'><b>Invoice</b></td>
              <td  width='80' align='center' class='A1101'><b>操作员</b></td>
        </tr>"; 
   echo  "<tr>
                 <td align='center' height='30' class='A0111' $tdbgcolor>$i</td>
	         <td class='A0101'>$Sid(原单)</td>
                 <td class='A0101' align='center'>$Qty</td>
                 <td class='A0101'>$OrderDate(下单日期)</td>
                 <td class='A0101' align='center'>$chDate</td>
                 <td class='A0101' align='center'>$InvoiceNO</td>
                 <td class='A0101' align='center'>$Operator</td>
         </tr>";

$dataResult=mysql_query("SELECT M.Date,M.Operator,S.POrderId,S.Qty,S.Estate  
FROM $DataIn.yw1_ordersplit M
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OPOrderId=S.POrderId 
WHERE 1 and M.SPOrderId=$Sid  AND S.Id>0 ORDER BY M.Date",$link_id);
if ($data_Row = mysql_fetch_array($dataResult)){
   do{
      $i++;
      $Date=$data_Row["Date"];
      $POrderId=$data_Row["POrderId"];
      $Qty=$data_Row["Qty"];
      $sumQty+=$Qty;
      $Operator=$data_Row["Operator"];
      include "../model/subprogram/staffname.php";
    
       $Estate=$data_Row["Estate"];
       $chDate="&nbsp;";
       $InvoiceNO="&nbsp;";
       if ($Estate==0) {
       $chResult=mysql_fetch_array(mysql_query("SELECT M.InvoiceNO,M.Date,M.InvoiceFile FROM  $DataIn.ch1_shipsheet S 
               LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
               WHERE S.POrderId='$POrderId'",$link_id));
       $InvoiceNO=$chResult["InvoiceNO"];
       $chDate=$chResult["Date"];
       $InvoiceFile=$chResult["InvoiceFile"];
       $f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
       $InvoiceNO=$InvoiceFile==0?$InvoiceNO:"<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">$InvoiceNO</a>";
       $tdbgcolor="bgColor='#339900'";
      }
   else{
       $tdbgcolor="";
   }
       
      echo  "<tr>
                 <td align='center' height='30' class='A0111' $tdbgcolor>$i</td>
	         <td class='A0101'>$POrderId</td>
                 <td class='A0101' align='center'>$Qty</td>
                 <td class='A0101'>$Date</td>
                <td class='A0101' align='center'>$chDate</td>
                 <td class='A0101' align='center'>$InvoiceNO</td>
                 <td class='A0101' align='center'>$Operator</td>
         </tr>";
   }while($data_Row = mysql_fetch_array($dataResult));  
   echo  "<tr>
                 <td colspan=2 height='30' align='center' class='A0111'><b>订单数量合计</b></td>
                 <td class='A0101' align='center'>$sumQty</td> 
	         <td colspan=4 class='A0101'>&nbsp;</td>
                
         </tr></table>";
   }
 
}
?>

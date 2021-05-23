<?php   
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";

header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
ChangeWtitle("Order-split Records");
echo "<link rel='stylesheet' href='../cjgl/lightgreen/read_line.css'>";
echo "<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
echo "<center>";

$i=1;$sumQty=0;
$d1=anmaIn("../download/invoice/",$SinkOrder,$motherSTR);
$sheetResult=mysql_query("SELECT S.OrderPO,S.POrderId,S.Qty,P.cName,P.eCode,M.OrderDate,M.Operator,S.Estate  
    FROM $DataIn.yw1_ordersheet S 
    LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
    LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
    WHERE 1 and S.POrderId=$Sid LIMIT 1",$link_id);
if ($sheet_Row = mysql_fetch_array($sheetResult)){
   $OrderPO=$sheet_Row["OrderPO"]; 
   $POrderId=$sheet_Row["POrderId"];
   $OrderDate=$sheet_Row["OrderDate"]; 
   $Operator=$sheet_Row["Operator"];
   include "../admin/subprogram/staffname.php";
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
       $InvoiceNO=$InvoiceFile==0?$InvoiceNO:"<a href=\"../admin/openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">$InvoiceNO</a>";
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
          <td colspan=6 height='30'  align='center' style='font-size:20px;'><b>Order-split Records</b></td>
       </tr>
       <tr> <td colspan=6 height='20'><b>PO:</b>$OrderPO</td></tr>
       <tr> <td colspan=6 height='20'><b>ProductName:</b>$cName</td> </tr>
       <tr> <td colspan=6 height='20'><b>ProductCode:</b>$eCode</td> </tr>";
  
  echo "<tr>
              <td  width='40'  align='center' class='A1111' height='30' ><b>NO</b></td>
              <td  width='150' align='center' class='A1101'><b>Split Date</b></td>
			  <td  width='100' align='center' class='A1101'><b>Shipped Qty</b></td>
              <td  width='150' align='center' class='A1101'><b>Shipped Date</b></td>
              <td  width='100' align='center' class='A1101'><b>Invoice</b></td>
   
        </tr>"; 
   echo  "<tr>
                 <td align='center' height='30' class='A0111' $tdbgcolor>$i</td>
                 <td class='A0101' align='center'>$OrderDate</td>
				 <td class='A0101' align='center'>$Qty</td>
                 <td class='A0101' align='center'>$chDate</td>
                 <td class='A0101' align='center'>$InvoiceNO</td>
         </tr>";

$dataResult=mysql_query("SELECT M.Date,M.Operator,S.POrderId,S.Qty,S.Estate  
FROM $DataIn.yw1_ordersplit M
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OPOrderId=S.POrderId 
WHERE 1 and M.SPOrderId=$Sid ORDER BY M.Date",$link_id);
if ($data_Row = mysql_fetch_array($dataResult)){
   do{
      $i++;
      $Date=$data_Row["Date"];
      $POrderId=$data_Row["POrderId"];
      $Qty=$data_Row["Qty"];
      $sumQty+=$Qty;
      $Operator=$data_Row["Operator"];
      include "../admin/subprogram/staffname.php";
    
       $Estate=$data_Row["Estate"];
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
       $InvoiceNO=$InvoiceFile==0?$InvoiceNO:"<a href=\"../admin/openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">$InvoiceNO</a>";
       $tdbgcolor="bgColor='#339900'";
      }
   else{
       $tdbgcolor="";
   }
       
      echo  "<tr>
                 <td align='center' height='30' class='A0111' $tdbgcolor>$i</td>
                 <td class='A0101' align='center'>$Date</td>
				 <td class='A0101' align='center'>$Qty</td>
                 <td class='A0101' align='center'>$chDate</td>
                 <td class='A0101' align='center'>$InvoiceNO</td>
         </tr>";
   }while($data_Row = mysql_fetch_array($dataResult));  
   echo  "<tr>
                 <td colspan=2 height='30' align='center' class='A0111'><b>Total(Qty)</b></td>
                 <td class='A0101' align='center'>$sumQty</td> 
	             <td colspan=2 class='A0101'>&nbsp;</td>
                
         </tr></table>";
   }
 
}
?>

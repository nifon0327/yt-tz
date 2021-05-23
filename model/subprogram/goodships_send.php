<?php
include "../../basic/chksession.php" ;
include "../../basic/parameter.inc";
include "../modelfunction.php";

header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
echo "<link rel='stylesheet' href='../../cjgl/lightgreen/read_line.css'>";
echo "<SCRIPT src='../pagefun.js' type=text/javascript></script>";
echo "<center>";
$today=date("Y-m-d");
$CheckMySql=mysql_query("SELECT * FROM $DataIn.my1_companyinfo where Type='S' AND cSign=7",$link_id);
if($CheckMyRow=mysql_fetch_array($CheckMySql)){
           $Company=$CheckMyRow["Company"];
           $Tel=$CheckMyRow["Tel"];
           $Fax=$CheckMyRow["Fax"];
           $WebSite=$CheckMyRow["WebSite"];
	 }
 echo "<table width='640' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' >";
echo "<tr><td height='30'    align='center'  style='Font-size:18px;'>$CompanyNameStr</td>
<tr>
               <td height='40'  align='center' style='Font-size:20px;Font-weight:bold;'>送货单</td>
           </tr>
<tr><td height='25' >地址:上海市宝安区西乡镇前进二路宝田工业区48栋 518102</td> </tr>
<tr><td height='25'  >电话: $Tel &nbsp;&nbsp; &nbsp;&nbsp;传真:  $Fax &nbsp;&nbsp; &nbsp;&nbsp;网址: $WebSite &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;日期: $today</td> </tr>
<tr><td height='25' >送货单号: $InvoiceNO</td> </tr>
<tr><td height='25' >送货地址: 大阪京科技(上海)有限公司</td> </tr>
<tr><td height='25' >&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;上海市宝安区龙华镇牛栏前村原26号厂房5楼</td> </tr>";
echo "</table>";

$sListResult = mysql_query("
SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,S.Qty,S.Price,S.Type,S.YandN,P.Weight AS Weight,M.Date,E.Leadtime,P.TestStandard,P.MainWeight,N.OrderDate AS OrderDate ,P.ProductId,N.ClientOrder
	FROM $DataIn.ch1_shipsheet S 
    LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
    LEFT JOIN $DataIn.yw1_ordermain N ON N.OrderNumber=O.OrderNumber
    LEFT JOIN $DataIn.yw3_pisheet E ON E.oId=O.Id 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId WHERE S.Mid='$ShipId' AND S.Type='1'
UNION ALL
	SELECT S.Id,S.POrderId,O.SampPO AS OrderPO,O.SampName AS cName,O.Description AS eCode,S.Qty,S.Price,S.Type,S.YandN,O.Weight AS Weight,M.Date,'' AS Leadtime,'' AS TestStandard,'' AS MainWeight,O.Date AS OrderDate  ,'' AS ProductId,'' AS ClientOrder
	FROM $DataIn.ch1_shipsheet S 
    LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId
	WHERE S.Mid='$ShipId' AND S.Type='2'
UNION ALL
	SELECT S.Id,S.POrderId,'' AS OrderPO,O.Description AS cName,O.Description AS eCode,S.Qty,S.Price,S.Type,S.YandN,'0' AS Weight ,M.Date,'' AS Leadtime,'' AS TestStandard,'' AS MainWeight,O.Date AS OrderDate,'' AS ProductId,'' AS ClientOrder
	FROM $DataIn.ch1_shipsheet S 
    LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
	LEFT JOIN $DataIn.ch6_creditnote O ON O.Number=S.POrderId
	WHERE S.Mid='$ShipId' AND S.Type='3'
",$link_id);
$i=1;
$sumQty=0;
$sumAmount=0;
 echo "<table width='640' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' >
            <tr><td  width='40'  align='center' class='A1111' height='30' ><b>序号</b></td>
	          <td  width='80' align='center' class='A1101'><b>PO#</b></td>
              <td  width='120' align='center' class='A1101'><b>产品代码</b></td>
              <td  width='220' align='center' class='A1101'><b>中文名称</b></td>
               <td  width='60' align='center' class='A1101'><b>数量</b></td>
               <td  width='60' align='center' class='A1101'><b>单价</b></td>
               <td  width='60' align='center' class='A1101'><b>金额</b></td>
            </tr>";
if ($StockRows = mysql_fetch_array($sListResult)) {
	do{
               $OrderPO=$StockRows["OrderPO"]==""?"&nbsp;":$StockRows["OrderPO"];
		       $POrderId=$StockRows["POrderId"];
               $Stock_Result=mysql_fetch_array(mysql_query("SELECT A.StuffCname,S.OrderQty
		              FROM $DataIn.cg1_stocksheet S
		              LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
                      LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=A.TypeId 
                     WHERE  1 AND A.StuffCname like '%+%'   AND  S.POrderId='$POrderId'  limit 1",$link_id));
               $StuffCnameArray= explode("+",$Stock_Result["StuffCname"]);
                $StuffCname=$StuffCnameArray[1];
               $QrderQty=$Stock_Result["OrderQty"];
		       $cName=$StockRows["cName"];
		       $eCode=$StockRows["eCode"];
               $Qty=$StockRows["Qty"];
		       $Price=$StockRows["Price"];
		        $Amount=sprintf("%.2f",$Qty*$Price);
		        $sumQty=$sumQty+$Qty;
		        $sumAmount=sprintf("%.2f",$sumAmount+$Amount);
             echo "<tr>
                        <td align='center' class='A0111' rowspan='2'>$i</td>
	                    <td class='A0101' rowspan='2'>$OrderPO</td>
                        <td class='A0101'  rowspan='2'>$eCode</td>
                        <td class='A0101'  height='30' >$cName</td>
                        <td class='A0101' align='center'>$Qty</td>
                        <td class='A0101' align='center'  rowspan='2'>$Price</td>
                        <td class='A0101' align='center' rowspan='2'>$Amount</td>
                     </tr>";
                 echo "<tr>
                        <td  class='A0101'  height='30'>$StuffCname</td>
                        <td class='A0101' align='center'>$QrderQty</td>
                      </tr>";

		     $i++;
 		   }while ($StockRows = mysql_fetch_array($sListResult));
     echo "<tr>
                   <td align='center' height='30' class='A0111' colspan='4'>总   计:</td>
                   <td class='A0101' align='center'>$sumQty</td>
                  <td class='A0101'>&nbsp;</td>
                  <td class='A0101' align='center'>$sumAmount</td>
                 </tr>";
        echo "<tr><td align='center' height='30'  colspan='7'>&nbsp;</td></tr>";
       echo "</table>";

 echo "<table width='640' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' >
             <tr><td align='left' height='30' width='320' style='Font-size:14px;Font-weight:bold;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;收货人:</td><td align='center' width='320' style='Font-size:14px;Font-weight:bold;'>送货人:刘明洪</td>
             </tr></table>";

	   }
?>
</center>
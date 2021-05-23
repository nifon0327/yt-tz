<?php   
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$subTableWidth=1050;
switch($ActionId){
  case 99:
          if($CkAdress!=""){
                $CheckAdressResult=mysql_fetch_array(mysql_query("SELECT   Id FROM $DataIn.product_ckadress WHERE ProductId=$ProductId",$link_id));
                 $CheckId=$CheckAdressResult["Id"];
                 if($CheckId>0){
                             $UpdateSql="UPDATE $DataIn.product_ckadress  SET  Adress='$CkAdress' WHERE ProductId=$ProductId";
                            $UpdateResult=@mysql_query($UpdateSql);
                            if($UpdateResult)echo "Y";
                  }
               else{
                             $InSql="INSERT INTO  $DataIn.product_ckadress(`Id`, `ProductId`, `Adress`)VALUES(NULL,'$ProductId','$CkAdress')  ";
                            $InResult=@mysql_query($InSql);
                            if($InResult)echo "Y";
                  }
            }
           else{
                             $DelSql="DELETE  FROM  $DataIn.product_ckadress  WHERE ProductId=$ProductId";
                            $DelResult=@mysql_query($DelSql);
                            if($DelResult)echo "Y";
                     }
         break;
default:
echo"<table id='$TableId'  cellspacing='1' border='1' align='center'>
	<tr bgcolor='#CCCCCC'>
	<td width='30' >序号</td>
	<td width='80' align='center'>出货流水号</td>
	<td width='100' align='center'>Invoice名称</td>
	<td width='80' align='center'>Invoice</td>
	<td width='150' align='center'>备注</td>
	<td width='80' align='center'>出货日期</td>
	<td width='80' align='center'>操作员</td>
	</tr>";
$i=1;
$sListResult = mysql_query("SELECT M.Id,M.Number,M.InvoiceNO,M.InvoiceFile,M.Date,M.Remark,M.ShipType,M.Ship,M.Operator
FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.ch1_shipsheet H ON H.Mid=M.Id 
LEFT JOIN $DataIn.ch1_shipout O ON O.ShipId=M.Id
LEFT JOIN $DataIn.productdata P ON P.ProductId=H.ProductId  
WHERE  1 AND O.Id IS NOT NULL AND  P.ProductId =$ProductId GROUP BY M.Id ",$link_id);
	$d1=anmaIn("download/invoice/",$SinkOrder,$motherSTR);		
	if($myRow = mysql_fetch_array($sListResult)) {
		do{
		   $Id=$myRow["Id"];
           $Number=$myRow["Number"];
		   $InvoiceNO=$myRow["InvoiceNO"]; 
		   $Date=$myRow["Date"];
           $Remark=$myRow["Remark"];
           $InvoiceFile=$myRow["InvoiceFile"];
		   $Operator=$myRow["Operator"];
		   include "../model/subprogram/staffname.php";
		   $f1=anmaIn($InvoiceNO,$SinkOrder,$motherSTR);
		   $InvoiceFile=$InvoiceFile==0?"&nbsp;":"<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=7\" target=\"download\">查看</a>";
			echo"<tr bgcolor='$theDefaultColor'>";
			//echo"<td  align='center' height='20'>$showPurchaseorder</td>";
			echo"<td  align='center'>$i</td>";
			echo"<td align='center'>$Number</td>";
			echo"<td align='center'>$InvoiceNO</td>";
			echo"<td align='center'>$InvoiceFile</td>";
			echo"<td >$Remark</td>";
            echo"<td align='center'>$Date</td>";
			echo"<td align='center'>$Operator</td>";
			echo"</tr>";
			$i++;
			}while ($myRow = mysql_fetch_array($sListResult));			
		}
       break;
}
?>
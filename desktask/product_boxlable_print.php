
<?php   
//步骤1 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
$checkRow=mysql_fetch_array(mysql_query("SELECT cName,eCode,Code FROM $DataIn.productdata WHERE ProductId='$p' ORDER BY Id LIMIT 1",$link_id));
$cName=$checkRow["cName"];
$eCode=$checkRow["eCode"];
$Code=$checkRow["Code"];

$checkCompRow=mysql_fetch_array(mysql_query("SELECT M.CompanyId FROM $DataIn.yw1_ordersheet S,$DataIn.yw1_ordermain M  WHERE M.OrderNumber=S.OrderNumber AND S.POrderId='$POId'",$link_id));
$CompanyId=$checkCompRow["CompanyId"];
if ($Code!=""){
   $arrCode=explode("|",$Code);
   $Code=$arrCode[1];
   $Codelen=strlen($Code);
   $Code4=$Code;
   if($Codelen>4){
	  $Code4=substr($Code,-4,4); 
   }
}
   
if ($CompanyId=='1063'){  //infinity
/*
   if ($Code!=""){
       $arrCode=explode("|",$Code);
       $Code=$arrCode[1];
   } 
   */
?>
<table border="0" cellpadding="0" cellspacing="0" style='width:200px;TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
    	<td style="height:25px"><?php    echo $eCode?></td>
	</tr>
        <tr>
      <td style="height:14px">(<?php    echo $cName?>)</td>
      </tr>
	<tr>
      <td style="height:25px">barcode:<?php    echo $Code?></td>
    </tr>
	<tr>
      <td style="height:4px"></td>
    </tr>
	<tr>
    	<td style="height:25px"><?php    echo $eCode?></td>
	</tr>
        <tr>
      <td style="height:14px">(<?php    echo $cName?>)</td>
      </tr>
	<tr>
      <td style="height:25px">barcode:<?php    echo $Code?></td>
    </tr>
</table>
<?php    } else{ ?>
<table border="0" cellpadding="0" cellspacing="0" style='width:200px;TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
    	<td style="height:25px"><?php    echo $cName?></td>
	</tr>
        <tr>
      <td style="height:14px"><?php    echo "PO:" . $PO. "&nbsp;&nbsp; 条码：$Code4 "?></td>
      </tr>
	<tr>
      <td style="height:25px"><?php    echo $eCode?></td>
    </tr>
	<tr>
      <td style="height:4px"></td>
    </tr>
	<tr>
    	<td style="height:25px"><?php    echo $cName?></td>
	</tr>
         <tr>
      <td style="height:14px"><?php    echo "PO:" . $PO. "&nbsp;&nbsp; 条码：$Code4 "?></td>
      </tr>
	<tr>
      <td style="height:25px"><?php    echo $eCode?></td>
    </tr>
</table>
<?php    } ?>

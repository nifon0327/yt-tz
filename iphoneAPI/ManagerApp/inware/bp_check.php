<?php 
//送货备品检查 ;同一张单相同配件的备品 
//传入参数:$StuffId/$Mid

$bp_Estate=$FromPageName=="sh"?1:2;
$bpRemark="";
$bpResult=mysql_query("SELECT S.Qty,S.StockId,S.SendSign  FROM $DataIn.gys_shsheet S WHERE  S.Mid='$Mid' AND S.StuffId='$StuffId' AND S.Estate='$bp_Estate'  AND S.SendSign=2",$link_id);
 if($bpRow = mysql_fetch_array($bpResult)) {
       $bpQty=number_format($bpRow["Qty"]);
        $sameResult=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Nums FROM $DataIn.gys_shsheet S WHERE  S.Mid='$Mid' AND S.StuffId='$StuffId' AND S.Estate>0  AND S.SendSign=0 ",$link_id));
	   $Nums=$sameResult["Nums"];
	   $bpRemark=$bpQty . "pcs备品($Nums);";
 }

 ?>
